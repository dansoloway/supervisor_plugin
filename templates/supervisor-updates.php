<?php
/* Template Name: Supervisor QA Updates */
get_header('supervisor');

// Set number of posts per page (change this value as needed)
$posts_per_page = 5; 

// Get the current pagination page
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

$args = [
    'post_type'      => 'qa_updates',
    'posts_per_page' => $posts_per_page,
    'paged'          => $paged,
    'meta_key'       => 'qa_updates_date', // Custom field for sorting
    'orderby'        => 'meta_value',      // Sort by custom field value
    'order'          => 'DESC',            // Latest dates first
];

$updates_query = new WP_Query($args);
?>

<div class="supervisor-home">

    <!-- Navigation Menu -->
    <?php
        $nav_path = PLUGIN_ROOT . 'inc/navigation.php';
        if (file_exists($nav_path)) {
            require_once $nav_path;
        }
    ?>

    <!-- Main Content Area with Two Columns -->
    <div class="supervisor-content-area">
        <!-- Left: QA Updates List -->
        <div class="qa-updates-list">
           
            <div class="search-results-container" style="display: none;"></div>
            <div class="initial-content">
                <?php
                if ($updates_query->have_posts()):
                    while ($updates_query->have_posts()):
                        $updates_query->the_post();
                        $post_id = get_the_ID();

                        // Taxonomies & Custom Field Section
                        $themes = get_the_terms($post_id, 'qa_themes');
                        $tags = get_the_terms($post_id, 'qa_tags');
                        $link = get_field('qa_updates_link');

                        $raw_date = get_field('qa_updates_date'); // ACF date field
                        $formatted_date = $raw_date ? date_i18n('F Y', strtotime($raw_date)) : '';

                        echo '<div class="qa-update-item">';

                        // Accordion Header (Clickable)
                        echo '<div class="light-green-bkg accordion-header" data-accordion="' . esc_attr($post_id) . '">';
                            echo '<div class="qa-update-title">';
                                echo '<h3>' . get_the_title() . '</h3>';
                                echo '<span class="update-date">' . esc_html($formatted_date) . '</span>';
                                echo '<span class="accordion-icon" id="icon-' . esc_attr($post_id) . '">⌄</span>';
                            echo '</div>'; // qa-update-title
                        echo '</div>'; // accordion-header

                        // Accordion Content (Hidden by Default)
                        echo '<div class="accordion-content" id="accordion-' . esc_attr($post_id) . '" style="display: none;">';
                            echo '<p>' . get_the_content() . '</p>';

                            echo '<div class="taxonomy-boxes">';
                            
                            // Tags (qa_tags)
                            if ($tags) {
                                $tag_names = array_map(fn($tag) => esc_html($tag->name), $tags);
                                echo '<p><strong>נושאי מפתח:</strong> ' . implode(', ', $tag_names) . '</p>';
                            }

                            // Themes (qa_themes)
                            if ($themes) {
                                $theme_names = array_map(fn($theme) => esc_html($theme->name), $themes);
                                echo '<p><strong>תחומים:</strong> ' . implode(', ', $theme_names) . '</p>';
                            }

                            // External Link
                            if ($link) {
                                echo '<p><strong>למקור:</strong> <a href="' . esc_url($link) . '" target="_blank">' . esc_url($link) . '</a></p>';
                            }

                            echo '</div>'; // taxonomy-boxes
                        echo '</div>'; // accordion-content

                        echo '</div>'; // qa-update-item
                    endwhile;

                   // Pagination (Numbers only, no "Next" or "Prev")
                   $total_pages = $updates_query->max_num_pages;

                   if ($total_pages > 1) {
                       $pagination_links = paginate_links([
                           'total'     => $total_pages,
                           'current'   => $paged,
                           'prev_next' => false,
                           'type'      => 'array',
                       ]);
                   
                       if ($pagination_links) {
                           echo '<div class="pagination">';
                           foreach ($pagination_links as $link) {
                               echo '<span style="display: inline-block; margin-right: 8px;">' . $link . '</span>';
                           }
                           echo '</div>';
                       }
                   }

                    wp_reset_postdata();
                else:
                    echo '<p>אין עדכונים זמינים</p>';
                endif;
                ?>
            </div>
        </div>

        <!-- Right: AJAX Search Component -->
        <?php 
        require_once(WP_PLUGIN_DIR . '/supervisor-plugin/inc/search.php');
        ?>
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
                    
                    const postId = this.getAttribute('data-accordion');
                    if (!postId) return;
                    
                    const content = document.getElementById('accordion-' + postId);
                    const icon = document.getElementById('icon-' + postId);
                    
                    if (!content || !icon) return;

                    // Close all other accordions first
                    const allAccordions = document.querySelectorAll('.accordion-header');
                    allAccordions.forEach(otherHeader => {
                        if (otherHeader !== this) {
                            const otherPostId = otherHeader.getAttribute('data-accordion');
                            if (otherPostId) {
                                const otherContent = document.getElementById('accordion-' + otherPostId);
                                const otherIcon = document.getElementById('icon-' + otherPostId);
                                
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
        
        // Re-initialize when search results are loaded
        if (typeof window !== 'undefined') {
            window.initializeAccordions = initializeAccordions;
        }
    });
</script>

<?php
get_footer();