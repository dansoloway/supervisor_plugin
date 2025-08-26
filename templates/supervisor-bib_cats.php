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

    <!-- Main Content -->
    <div class="bib-cats-content">
        <h1 class="page-title">קטגוריות ביבליוגרפיה</h1>
        
        <p class="intro-text">
            כאן יהיה טקסט הקדמה קצר המסביר כיצד הרשימה נבנתה כאן יהיה טקסט הקדמה קצר המסביר כיצד הרשימה נבנתה כאן יהיה טקסט הקדמה קצר המסביר כיצד הרשימה נבנתה כאן יהיה טקסט הקדמה קצר המסביר כיצד הרשימה נבנתה כאן יהיה טקסט הקדמה קצר המסביר כיצד הרשימה נבנתה.
        </p>

        <div class="categories-grid">
            <?php
            // Array of random Font Awesome icons
            $random_icons = [
                'fas fa-book',
                'fas fa-graduation-cap',
                'fas fa-microscope',
                'fas fa-flask',
                'fas fa-chart-line',
                'fas fa-lightbulb',
                'fas fa-cogs',
                'fas fa-rocket',
                'fas fa-palette',
                'fas fa-music',
                'fas fa-camera',
                'fas fa-code',
                'fas fa-leaf',
                'fas fa-heart',
                'fas fa-star',
                'fas fa-gem',
                'fas fa-diamond',
                'fas fa-crown',
                'fas fa-trophy',
                'fas fa-medal'
            ];
            
            // Fetch all categories in the 'qa_bib_cats' taxonomy
            $categories = get_terms([
                'taxonomy' => 'qa_bib_cats',
                'hide_empty' => false,
            ]);

            if (!empty($categories)) :
                foreach ($categories as $index => $category) :
                    $description = get_term_meta($category->term_id, 'qa_bib_description', true);
                    // Link to search page with category as search term
                    $search_url = home_url('/supervisor-search/') . '?supervisor_search=' . urlencode($category->name);
                    // Use random icon from array, cycling through if more categories than icons
                    $icon = $random_icons[$index % count($random_icons)];
                    ?>
                    <a href="<?php echo esc_url($search_url); ?>" class="category-card">
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
    </div>
</div>

<?php get_footer(); ?>