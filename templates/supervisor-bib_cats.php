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
            // Fetch all categories in the 'qa_bib_cats' taxonomy
            $categories = get_terms([
                'taxonomy' => 'qa_bib_cats',
                'hide_empty' => false,
            ]);

            if (!empty($categories)) :
                foreach ($categories as $category) :
                    $icon = get_term_meta($category->term_id, 'qa_bib_icon', true);
                    $description = get_term_meta($category->term_id, 'qa_bib_description', true);
                    $category_link = get_term_link($category);
                    ?>
                    <a href="<?php echo esc_url($category_link); ?>" class="category-card">
                        <div class="category-icon">
                            <?php if (!empty($icon)) : ?>
                                <?php if (strpos($icon, 'dashicons-') !== false) : ?>
                                    <span class="dashicons <?php echo esc_attr($icon); ?>"></span>
                                <?php else : ?>
                                    <img src="<?php echo esc_url($icon); ?>" alt="<?php echo esc_attr($category->name); ?>" />
                                <?php endif; ?>
                            <?php else : ?>
                                <i class="fas fa-folder"></i>
                            <?php endif; ?>
                        </div>
                        <h2 class="category-title"><?php echo esc_html($category->name); ?></h2>
                        <p class="category-description"><?php echo esc_html($description); ?></p>
                    </a>
                <?php endforeach;
            else : ?>
                <p class="no-categories"><?php esc_html_e('No categories found.', 'text-domain'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>