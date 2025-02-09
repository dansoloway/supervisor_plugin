<?php
/* Template Name: QA Bibliography Categories */

get_header('supervisor'); ?>

<div class="wrap" style="direction:rtl; text-align:right">
    <h1><?php esc_html_e('ניהול קטגוריות ביבליוגרפיה', 'text-domain'); ?></h1>
    <p>כאן יהיה טקסט הקדמה קצר המסביר כיצד הרשימה נבנתה כאן יהיה טקסט 
הקדמה קצר המסביר כיצד הרשימה נבנתהכאן יהיה טקסט הקדמה קצר המסביר
כיצד הרשימה נבנתהכאן יהיה טקסט הקדמה קצר המסביר כיצד הרשימה נבנתה
כאן יהיה טקסט הקדמה קצר המסביר כיצד הרשימה נבנתהכאן יהיה טקסט הקד־
מה קצר המסביר כיצד הרשימה נבנתהש</p>
    <div class="categories-grid">

        <?php
        // Fetch all categories in the 'qa_bib_cats' taxonomy
        $categories = get_terms([
            'taxonomy' => 'qa_bib_cats',
            'hide_empty' => false,
        ]);

        if (!empty($categories)) :
            foreach ($categories as $category) :
                // Get custom fields: icon and description
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
                                <img src="<?php echo esc_url($icon); ?>" alt="<?php echo esc_attr($category->name); ?>" />
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

<style>
    .categories-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    .category-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        background-color: #f9f9f9;
        text-align: center;
        text-decoration: none;
        color: inherit;
        display: block;
    }
    .category-card:hover {
        background-color: #f1f1f1;
    }
    .category-icon {
        margin-bottom: 10px;
    }
    .category-icon img {
        max-width: 50px;
        height: auto;
    }
    .dashicons {
        font-size: 40px;
        line-height: 1;
        color: #0073aa;
    }
    .category-title {
        font-size: 18px;
        font-weight: bold;
        margin: 10px 0;
    }
    .category-description {
        font-size: 14px;
        color: #666;
    }
</style>

<?php get_footer(); ?>
