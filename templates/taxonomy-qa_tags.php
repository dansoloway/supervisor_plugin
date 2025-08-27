<?php
/* Template for QA Tags */
get_header('supervisor');

// Get current taxonomy term
$term = get_queried_object();

error_log('Loading taxonomy-qa_tags.php template');
?>

<div class="supervisor-home">
    <!-- Navigation Menu -->
    <?php
        $nav_path = PLUGIN_ROOT . 'inc/navigation.php';
        if (file_exists($nav_path)) {
            require_once $nav_path;
        }
    ?>

    <!-- Main Content -->
    <div class="taxonomy-content">
        <h1 class="page-title" style="text-align: right;">
            <?php 
            // Get and display the icon for this term
            $icon = get_term_fa_icon($term->term_id, 'fas fa-folder');
            if ($icon) {
                echo '<i class="' . esc_attr($icon) . '" style="margin-left: 8px; color: #0073aa;"></i>';
            }
            echo esc_html($term->name); 
            ?>
        </h1>
        
        <p class="intro-text" style="text-align: right;">
            <?php 
            $description = get_term_meta($term->term_id, 'qa_bib_description', true);
            if ($description) {
                echo esc_html($description);
            } else {
                echo 'כאן יהיה טקסט הקדמה קצר המסביר כיצד הרשימה נבנתה';
            }
            ?>
        </p>

        <div class="bib-items-list">
            <?php
            // Query the qa_bib_items posts in this category
            $args = [
                'post_type' => 'qa_bib_items',
                'tax_query' => [
                    [
                        'taxonomy' => 'qa_tags',
                        'field' => 'term_id',
                        'terms' => $term->term_id,
                    ],
                ],
                'posts_per_page' => -1, // Show all
                'orderby' => 'menu_order',
                'order' => 'ASC',
            ];
            $query = new WP_Query($args);

            if ($query->have_posts()) :
                while ($query->have_posts()) : $query->the_post();
                    ?>
                    <div class="bib-item" data-post-id="<?php echo get_the_ID(); ?>">
                        <div class="bib-header">
                            <i class="fas fa-chevron-down bib-toggle"></i>
                            <div class="bib-reference"><?php the_title(); ?></div>
                        </div>
                        <div class="bib-content" style="display: none;">
                            <p class="bib-description">
                                כאן יהיה מלל המסביר על המקור ועל הארגון האנגלי ה CQC גיבש מדיניות פיקוח על רשויות מקומיות
                                כאן יהיה מלל המסביר על המקור ועל הארגון האנגלי ה CQC גיבש מדיניות פיקוח על רשויות מקומיות
                                כאן יהיה מלל המסביר על המקור ועל הארגון האנגלי ה CQC גיבש מדיניות פיקוח על רשויות מקומיות
                            </p>
                            <?php
                            // Get the ACF field "original_link"
                            $original_link = get_field('orignial_link');
                            if ($original_link) :
                            ?>
                                <p class="bib-original-link">
                                    <strong>למקור: </strong> 
                                    <a href="<?php echo esc_url($original_link); ?>" target="_blank" rel="noopener noreferrer">
                                        <?php echo esc_html($original_link); ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile;
                wp_reset_postdata();
            else :
                ?>
                <p class="no-items"><?php esc_html_e('אין פריטים', 'text-domain'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle bibliography items
    document.querySelectorAll('.bib-toggle').forEach(function(toggle) {
        toggle.addEventListener('click', function() {
            const item = this.closest('.bib-item');
            const content = item.querySelector('.bib-content');
            const isExpanded = content.style.display !== 'none';
            
            if (isExpanded) {
                content.style.display = 'none';
                this.classList.remove('fa-chevron-up');
                this.classList.add('fa-chevron-down');
            } else {
                content.style.display = 'block';
                this.classList.remove('fa-chevron-down');
                this.classList.add('fa-chevron-up');
            }
        });
    });
});
</script>

<?php get_footer(); ?>
