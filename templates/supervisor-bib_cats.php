<?php
/* Template Name: Supervisor Bibliography Categories */
get_header('supervisor');
error_log('Loading supervisor-bib_cats.php template');
?>

<div class="supervisor-container">
    <!-- Main Content -->
    <div class="supervisor-main supervisor-two-column">

     <!-- Column 2: Sidebar -->
           <!-- Vertical Buttons -->
         
       <?php
       //require_once trailingslashit(dirname(__FILE__, 2)) . 'inc/sidebar.php';
       require_once PLUGIN_ROOT . 'inc/sidebar.php';

        if (file_exists($sidebar_path)) {
            require_once $sidebar_path;
        } else {
            error_log('Sidebar file not found: ' . $sidebar_path);
        }
       ?>
        
        <!-- Column 1: Categories Grid -->
        <div class="categories-container"> 
            <p>
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
                                        <!-- <span class="dashicons <?php echo esc_attr($icon); ?>"></span> -->
                                    <?php else : ?>
                                        <!-- <img src="<?php echo esc_url($icon); ?>" alt="<?php echo esc_attr($category->name); ?>" /> -->
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            <h2 class="category-title"><?php echo esc_html($category->name); ?></h2>
                            <p class="category-description"><?php echo esc_html($description); ?></p>
                        </a>
                    <?php endforeach;
                else : ?>
                    <p><?php esc_html_e('No categories found.', 'text-domain'); ?></p>
                <?php endif; ?>
            </div>
        </div> 
        
       

    </div>
</div>
<?php get_footer(); ?>