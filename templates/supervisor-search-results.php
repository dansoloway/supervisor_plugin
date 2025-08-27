<?php
/* Template Name: Supervisor Search Results */
get_header('supervisor');

// Get search parameters - use plugin-specific parameter
$search_term = $_GET['supervisor_search'] ?? '';
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

// Debug logging
error_log('Supervisor Search Debug - Search term: ' . $search_term);
error_log('Supervisor Search Debug - GET params: ' . print_r($_GET, true));

// Build comprehensive search query
$args = [
    'post_type' => ['qa_updates', 'qa_orgs', 'qa_bib_items'], // Fixed: qa_bib_items instead of qa_bibs
    'posts_per_page' => 20,
    'paged' => $paged,
    'orderby' => 'date',
    'order' => 'DESC',
];

// Enhanced search query that includes custom fields and taxonomies
if (!empty($search_term)) {
    // First, get posts that match the search term in title/content
    $title_content_posts = get_posts([
        'post_type' => ['qa_updates', 'qa_orgs', 'qa_bib_items'],
        'posts_per_page' => -1,
        's' => $search_term,
        'fields' => 'ids'
    ]);
    
    // Get posts that have matching taxonomy terms - FIXED LOGIC
    $taxonomy_posts = [];
    
    // Get all terms that contain the search term
    $matching_themes = get_terms([
        'taxonomy' => 'qa_themes',
        'name__like' => $search_term,
        'hide_empty' => false,
        'fields' => 'ids'
    ]);
    
    $matching_tags = get_terms([
        'taxonomy' => 'qa_tags',
        'name__like' => $search_term,
        'hide_empty' => false,
        'fields' => 'ids'
    ]);
    
    $matching_bib_cats = get_terms([
        'taxonomy' => 'qa_tags',
        'name__like' => $search_term,
        'hide_empty' => false,
        'fields' => 'ids'
    ]);
    
    // Only search for posts if we found matching terms
    if (!empty($matching_themes) || !empty($matching_tags) || !empty($matching_bib_cats)) {
        $tax_query = [];
        
        if (!empty($matching_themes)) {
            $tax_query[] = [
                'taxonomy' => 'qa_themes',
                'field' => 'term_id',
                'terms' => $matching_themes
            ];
        }
        
        if (!empty($matching_tags)) {
            $tax_query[] = [
                'taxonomy' => 'qa_tags',
                'field' => 'term_id',
                'terms' => $matching_tags
            ];
        }
        
        if (!empty($matching_bib_cats)) {
            $tax_query[] = [
                'taxonomy' => 'qa_tags',
                'field' => 'term_id',
                'terms' => $matching_bib_cats
            ];
        }
        
        if (!empty($tax_query)) {
            $tax_query['relation'] = 'OR';
            
            $taxonomy_posts = get_posts([
                'post_type' => ['qa_updates', 'qa_orgs', 'qa_bib_items'],
                'posts_per_page' => -1,
                'tax_query' => $tax_query,
                'fields' => 'ids'
            ]);
        }
    }
    
    // Get posts that have matching custom fields
    $meta_posts = get_posts([
        'post_type' => ['qa_updates', 'qa_orgs', 'qa_bib_items'],
        'posts_per_page' => -1,
        'meta_query' => [
            'relation' => 'OR',
            [
                'key' => 'qa_updates_link',
                'value' => $search_term,
                'compare' => 'LIKE'
            ],
            [
                'key' => 'qa_updates_date',
                'value' => $search_term,
                'compare' => 'LIKE'
            ],
            [
                'key' => 'qa_orgs_link',
                'value' => $search_term,
                'compare' => 'LIKE'
            ],
            [
                'key' => 'orignial_link',
                'value' => $search_term,
                'compare' => 'LIKE'
            ]
        ],
        'fields' => 'ids'
    ]);
    
    // Combine all post IDs and remove duplicates
    $all_post_ids = array_unique(array_merge($title_content_posts, $taxonomy_posts, $meta_posts));
    
    if (!empty($all_post_ids)) {
        $args['post__in'] = $all_post_ids;
        $args['orderby'] = 'post__in'; // Maintain the order we want
    } else {
        // If no results found, return empty query
        $args['post__in'] = [0]; // This will return no results
    }
}

$search_query = new WP_Query($args);
$total_results = $search_query->found_posts;
?>

<div class="supervisor-home">
    <!-- Navigation Menu -->
    <?php
        $nav_path = PLUGIN_ROOT . 'inc/navigation.php';
        if (file_exists($nav_path)) {
            require_once $nav_path;
        }
    ?>

    <!-- New Search Section 
    <div class="new-search-section">
        <div class="new-search-container">
            <h2>חיפוש נוסף</h2>
            <p class="new-search-description">חפש מידע נוסף במערכת בקרת האיכות</p>
            <form class="supervisor-search" role="search" action="<?php echo home_url('/supervisor-search/'); ?>">
                <input type="search" name="supervisor_search" placeholder="הקלד מילות חיפוש..." class="supervisor-search-bar" aria-label="שדה חיפוש נוסף">
                <button class="search-button" aria-label="חיפוש">
                    <svg class="search-icon" width="20" height="20" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="13" cy="13" r="9" stroke="white" stroke-width="4"></circle>
                        <line x1="19" y1="19" x2="28" y2="28" stroke="white" stroke-width="4"></line>
                    </svg>
                </button>
            </form>
        </div>
    </div>
    -->

    <!-- Search Results Header -->
    <div class="search-results-header">
        <h1>תוצאות חיפוש</h1>
        <?php if (!empty($search_term)): ?>
            <p class="search-query">חיפוש עבור: <strong><?php echo esc_html($search_term); ?></strong></p>
        <?php endif; ?>
        <p class="results-count">נמצאו <?php echo $total_results; ?> תוצאות</p>
    </div>

    <!-- Search Results Content -->
    <div class="search-results-content">
        <?php if ($search_query->have_posts()): ?>
            <div class="search-results-list">
                <?php
                $result_index = 0;
                while ($search_query->have_posts()):
                    $search_query->the_post();
                    $post_id = get_the_ID();
                    $post_type = get_post_type();
                    $accordion_id = 'search-result-' . $result_index;
                    $result_index++;
                    
                    // Get post type specific data
                    switch ($post_type) {
                        case 'qa_updates':
                            $themes = get_the_terms($post_id, 'qa_themes');
                            $tags = get_the_terms($post_id, 'qa_tags');
                            $link = get_field('qa_updates_link');
                            $raw_date = get_field('qa_updates_date');
                            if ($raw_date) {
                                $formatted_date = date_i18n('F Y', strtotime($raw_date));
                            } else {
                                // Fallback to post date if ACF field is empty
                                $formatted_date = get_the_date('F Y');
                            }
                            break;
                            
                        case 'qa_bib_items': // Fixed: correct post type name
                            $link = get_field('orignial_link'); // Fixed: correct field name with typo
                            $formatted_date = '';
                            break;
                            
                        case 'qa_orgs':
                            $link = get_field('qa_orgs_link');
                            $formatted_date = '';
                            break;
                    }
                    ?>

                    <?php if ($post_type === 'qa_bib_items'): ?>
                        <!-- Bibliography Item - Content visible directly -->
                        <div class="qa-update-item">
                            <div class="light-green-bkg">
                                <div class="qa-update-title">
                                    <div class="title-date-container">
                                        <h3><?php echo get_the_title(); ?></h3>
                                        <span class="update-date"><?php echo esc_html($formatted_date); ?></span>
                                    </div>
                                </div>
                            </div>
                            <!-- Bibliography Content - Always visible -->
                            <div class="accordion-content" style="display: block;">
                                <p><?php echo get_the_content(); ?></p>
                                <?php if ($link): ?>
                                    <p><strong>לקישור:</strong> <a href="<?php echo esc_url($link); ?>" target="_blank" class="source-link"><?php echo esc_url($link); ?></a></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Other post types - Accordion behavior -->
                        <div class="qa-update-item">
                            <!-- Accordion Header -->
                            <div class="light-green-bkg accordion-header" data-accordion="<?php echo esc_attr($accordion_id); ?>">
                                <div class="qa-update-title">
                                    <div class="title-date-container">
                                        <h3><?php echo get_the_title(); ?></h3>
                                        <span class="update-date"><?php echo esc_html($formatted_date); ?></span>
                                    </div>
                                    <span class="accordion-icon" id="icon-<?php echo esc_attr($accordion_id); ?>">⌄</span>
                                </div>
                            </div>

                            <!-- Accordion Content -->
                            <div class="accordion-content" id="accordion-<?php echo esc_attr($accordion_id); ?>" style="display: none;">
                                <?php if ($post_type === 'qa_updates'): ?>
                                    <!-- Updates Content -->
                                    <p><?php echo get_the_content(); ?></p>
                                    <div class="taxonomy-boxes">
                                        <?php if ($tags): ?>
                                            <p><strong>נושאי מפתח:</strong> 
                                                <?php foreach ($tags as $tag): ?>
                                                    <span class="taxonomy-term">
                                                        <i class="<?php echo get_term_fa_icon($tag->term_id, 'fas fa-tag'); ?>"></i>
                                                        <?php echo esc_html($tag->name); ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php if ($themes): ?>
                                            <p><strong>תחומים:</strong> 
                                                <?php foreach ($themes as $theme): ?>
                                                    <span class="taxonomy-term">
                                                        <i class="<?php echo get_term_fa_icon($theme->term_id, 'fas fa-folder'); ?>"></i>
                                                        <?php echo esc_html($theme->name); ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php 
                                        // Get bibliography categories for this post
                                        $bib_cats = get_the_terms($post_id, 'qa_tags');
                                        if ($bib_cats): ?>
                                            <p><strong>נושאי מפתח:</strong> 
                                                <?php foreach ($bib_cats as $bib_cat): ?>
                                                    <span class="taxonomy-term">
                                                        <i class="<?php echo get_term_fa_icon($bib_cat->term_id, 'fas fa-book'); ?>"></i>
                                                        <?php echo esc_html($bib_cat->name); ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php if ($link): ?>
                                            <p><strong>לקישור:</strong> <a href="<?php echo esc_url($link); ?>" target="_blank" class="source-link"><?php echo esc_url($link); ?></a></p>
                                        <?php endif; ?>
                                    </div>
                                <?php elseif ($post_type === 'qa_orgs'): ?>
                                    <!-- Organizations Content -->
                                    <p><?php echo get_the_content(); ?></p>
                                    <?php if ($link): ?>
                                        <p><strong>לקישור:</strong> <a href="<?php echo esc_url($link); ?>" target="_blank" class="source-link"><?php echo esc_url($link); ?></a></p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <?php
            $total_pages = $search_query->max_num_pages;
            if ($total_pages > 1):
            ?>
                <div class="pagination">
                    <?php
                    $pagination_links = paginate_links([
                        'total' => $total_pages,
                        'current' => $paged,
                        'prev_next' => false,
                        'type' => 'array',
                    ]);
                    
                    if ($pagination_links):
                        foreach ($pagination_links as $link):
                            echo '<span style="display: inline-block; margin-right: 8px;">' . $link . '</span>';
                        endforeach;
                    endif;
                    ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- No Results -->
            <div class="no-results">
                <p>לא נמצאו תוצאות עבור החיפוש שלך.</p>
                <p>נסה לשנות את מילות החיפוש או לחזור לדף החיפוש.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        function initializeAccordions() {
            const accordions = document.querySelectorAll('.accordion-header');

            accordions.forEach(header => {
                header.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const accordionId = this.getAttribute('data-accordion');
                    if (!accordionId) return;
                    
                    const content = document.getElementById('accordion-' + accordionId);
                    const icon = document.getElementById('icon-' + accordionId);
                    
                    if (!content || !icon) return;

                    // Close all other accordions first
                    const allAccordions = document.querySelectorAll('.accordion-header');
                    allAccordions.forEach(otherHeader => {
                        if (otherHeader !== this) {
                            const otherAccordionId = otherHeader.getAttribute('data-accordion');
                            if (otherAccordionId) {
                                const otherContent = document.getElementById('accordion-' + otherAccordionId);
                                const otherIcon = document.getElementById('icon-' + otherAccordionId);
                                
                                if (otherContent && otherIcon) {
                                    otherContent.style.display = 'none';
                                    otherIcon.innerHTML = '⌄';
                                }
                            }
                        }
                    });

                    // Toggle the clicked accordion
                    if (content.style.display === 'none' || content.style.display === '') {
                        content.style.display = 'block';
                        icon.innerHTML = '⌃';
                    } else {
                        content.style.display = 'none';
                        icon.innerHTML = '⌄';
                    }
                });
            });
        }

        // Initialize accordions on page load
        initializeAccordions();
    });
</script>

<?php
wp_reset_postdata();
get_footer();
?>
