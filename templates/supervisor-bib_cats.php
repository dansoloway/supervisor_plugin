<?php
/* Template Name: Supervisor Bibliography Categories */
get_header('supervisor');
$bib_cats_area_labels = [
    'policy'                => __('מדיניות', 'text-domain'),
    'control'               => __('בקרה', 'text-domain'),
    'enforcement'           => __('אכיפה', 'text-domain'),
    'knowledge_development' => __('פיתוח ידע והדרכה', 'text-domain'),
    'working_methods'       => __('שיטות עבודה', 'text-domain'),
];
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

        <div class="bib-cats-layout">
            <div class="bib-cats-main">
                <h1 class="page-title"><?php echo esc_html(get_the_title()); ?></h1>

                <?php
                while (have_posts()) :
                    the_post();
                    $has_lead = trim((string) get_post_field('post_content', get_the_ID())) !== '';
                    if ($has_lead) :
                        ?>
                        <div class="bib-cats-lead entry-content">
                            <?php the_content(); ?>
                        </div>
                        <?php
                    else :
                        ?>
                        <div class="bib-cats-lead bib-cats-lead--empty" aria-hidden="true"></div>
                        <?php
                    endif;
                endwhile;
                rewind_posts();
                ?>

                <?php
                $km_cat_raw    = isset($_GET['km_cat']) ? sanitize_key(wp_unslash($_GET['km_cat'])) : '';
                $filter_slugs  = $km_cat_raw ? supervisor_knowledge_map_resolve_to_leaf_slugs($km_cat_raw) : [];
                $filter_active = ! empty($filter_slugs);
                ?>

                <div class="categories-grid" id="bib-cats-grid">
                    <div id="bib-cats-grid-inner">
                    <?php
                    $categories = supervisor_bib_cats_get_qa_tags_terms('', $filter_active ? $filter_slugs : []);

                    $bib_cats_categories = $categories;
                    $bib_cats_empty_message = $filter_active
                        ? __('לא נמצאו נושאי מפתח המשויכים לאזור זה במפה. שיוך נושאים נעשה בלוח ניהול נושאי מפתח, בשדה קטגוריית מפת הידע.', 'text-domain')
                        : __('No categories found.', 'text-domain');
                    require plugin_dir_path(__FILE__) . 'partials/bib-cats-cards.php';
                    ?>
                    </div>
                </div>
            </div>

            <aside class="bib-cats-sidebar" aria-label="<?php esc_attr_e('סינון', 'text-domain'); ?>">
                <div class="ajax-search-component bib-cats-filter-panel" dir="rtl">
                    <form id="bib-cats-filter-form" class="bib-cats-filter-form">
                        <div class="search-section">
                            <h2 class="search-title"><?php echo esc_html__('חיפוש בנושאי המפתח', 'text-domain'); ?></h2>
                            <div class="search-input-container">
                                <input
                                    type="search"
                                    id="bib-cats-search-text"
                                    name="bib-cats-search"
                                    autocomplete="off"
                                    placeholder="<?php echo esc_attr__('חיפוש', 'text-domain'); ?>"
                                    value=""
                                    class="search-input-field"
                                >
                                <button type="button" class="search-button" id="bib-cats-search-button" aria-label="<?php echo esc_attr__('חיפוש', 'text-domain'); ?>">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path d="M21 21L16.514 16.506L21 21ZM19 10.5C19 15.194 15.194 19 10.5 19C5.806 19 2 15.194 2 10.5C2 5.806 5.806 2 10.5 2C15.194 2 19 5.806 19 10.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="filter-section">
                            <div class="filter-header">
                                <h3 class="filter-title"><?php echo esc_html__('סינון לפי:', 'text-domain'); ?></h3>
                                <button type="button" class="filter-toggle" aria-label="<?php echo esc_attr__('הצג/הסתר סינון', 'text-domain'); ?>" aria-expanded="true">
                                    <span class="filter-toggle-icon">⌃</span>
                                </button>
                            </div>
                            <div class="filter-content">
                                <div class="taxonomy-filters">
                                    <div class="filter-listbox bib-cats-km-area-list">
                                        <?php
                                        $allowed = supervisor_bib_cats_sidebar_area_slugs();
                                        $sidebar_checked = supervisor_bib_cats_sidebar_checked_areas_from_km($km_cat_raw, $filter_slugs);
                                        $checked_flip    = array_flip($sidebar_checked);
                                        foreach ($bib_cats_area_labels as $slug => $label) :
                                            if (! in_array($slug, $allowed, true)) {
                                                continue;
                                            }
                                            $checked = isset($checked_flip[$slug]);
                                            ?>
                                            <label class="checkbox-label">
                                                <input type="checkbox" name="bib_km_area[]" value="<?php echo esc_attr($slug); ?>" <?php checked($checked); ?>>
                                                <span class="checkbox-text"><?php echo esc_html($label); ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" id="bib-cats-filter-submit" class="filter-button"><?php echo esc_html__('סנן', 'text-domain'); ?></button>
                    </form>
                </div>
            </aside>
        </div>

        </div> <!-- End supervisor-content-wrapper -->

    </div> <!-- End supervisor-page-container -->
</div>

<?php get_footer(); ?>
