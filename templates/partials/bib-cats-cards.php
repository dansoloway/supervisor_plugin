<?php
/**
 * Category cards grid inner (נושאי מפתח). Variables before include:
 *
 * @var array<int, WP_Term>|WP_Term[] $bib_cats_categories
 * @var string                         $bib_cats_empty_message
 */
if (! defined('ABSPATH')) {
    exit;
}

$bib_cats_categories      = isset($bib_cats_categories) ? $bib_cats_categories : [];
$bib_cats_empty_message   = isset($bib_cats_empty_message) ? (string) $bib_cats_empty_message : '';

if (! empty($bib_cats_categories)) :
    foreach ($bib_cats_categories as $category) :
        $archive_url = get_term_link($category);
        if (is_wp_error($archive_url)) {
            continue;
        }
        $icon = get_term_fa_icon($category->term_id, 'fas fa-folder');
        ?>
        <a href="<?php echo esc_url($archive_url); ?>" class="category-card">
            <div class="category-icon" aria-hidden="true">
                <i class="<?php echo esc_attr($icon); ?>"></i>
            </div>
            <h2 class="category-title"><?php echo esc_html($category->name); ?></h2>
            <div class="category-arrow" aria-hidden="true">
                <svg class="category-arrow-svg" width="32" height="10" viewBox="0 0 32 10" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false">
                    <path class="category-arrow-path" d="M30 5H6M12 1L6 5l6 4" stroke="currentColor" stroke-width="1.15" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </a>
        <?php
    endforeach;
else :
    ?>
    <p class="no-categories"><?php echo esc_html($bib_cats_empty_message); ?></p>
    <?php
endif;
