<?php
/* Template Name: Supervisor Bibliography Categories */
get_header('supervisor');
error_log('Loading supervisor-bib_cats.php template');
?>

<div class="supervisor-home supervisor-bib-cats">
    <!-- Navigation Menu -->
    <?php
        $nav_path = plugin_dir_path(__FILE__) . '../inc/navigation.php';
        if (file_exists($nav_path)) {
            require_once $nav_path;
        }
    ?>

    <!-- Standardized Page Container -->
    <div class="supervisor-page-container">
        
        <!-- Main Content -->
        <div class="supervisor-content-wrapper supervisor-single-column">
        <h1 class="page-title">נושאי מפתח</h1>
        
        <p class="intro-text">
            <?php 
            // Get the page description or use default text
            $page_description = get_post_meta(get_the_ID(), 'page_description', true);
            if ($page_description) {
                echo esc_html($page_description);
            } else {
                echo 'כאן יהיה טקסט הקדמה קצר המסביר כיצד הרשימה נבנתה';
            }
            ?>
        </p>

        <div class="categories-grid">
            <?php
            // Fetch all categories in the 'qa_tags' taxonomy
            $categories = get_terms([
                'taxonomy' => 'qa_tags',
                'hide_empty' => false,
            ]);

            if (!empty($categories)) :
                foreach ($categories as $index => $category) :
                    $description = get_term_meta($category->term_id, 'qa_bib_description', true);
                    // Link to taxonomy archive page for this qa_tag
                    $archive_url = get_term_link($category);
                    // Get custom Font Awesome icon for this term, or use default
                    $icon = get_term_fa_icon($category->term_id, 'fas fa-folder');
                    ?>
                    <a href="<?php echo esc_url($archive_url); ?>" class="category-card">
                        <div class="category-icon">
                            <i class="<?php echo esc_attr($icon); ?>"></i>
                        </div>
                        <h2 class="category-title"><?php echo esc_html($category->name); ?></h2>
                        <p class="category-description"><?php echo esc_html($description); ?></p>
                        <div class="category-arrow">
                            <i class="fas fa-arrow-left"></i>
                        </div>
                    </a>
                <?php endforeach;
            else : ?>
                <p class="no-categories"><?php esc_html_e('No categories found.', 'text-domain'); ?></p>
            <?php endif; ?>
        </div>
        
        </div> <!-- End supervisor-content-wrapper -->

    </div> <!-- End supervisor-page-container -->
</div>

<?php get_footer(); ?>