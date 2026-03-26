<?php
/* Template Name: Supervisor Bibliography Categories */
get_header('supervisor');
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
                $filter_label  = $filter_active ? supervisor_knowledge_map_filter_banner_label($km_cat_raw) : '';
                $bib_page_url  = get_permalink();
                ?>

                <?php if ($filter_active) : ?>
                    <div class="bib-cats-map-filter-active" role="status">
                        <span class="bib-cats-map-filter-label">
                            <?php
                            echo esc_html(
                                sprintf(
                                    /* translators: %s: knowledge-map region name */
                                    __('מסנן לפי: %s', 'text-domain'),
                                    $filter_label
                                )
                            );
                            ?>
                        </span>
                        <a class="bib-cats-map-filter-clear" href="<?php echo esc_url($bib_page_url); ?>"><?php echo esc_html__('הצג את כל נושאי המפתח', 'text-domain'); ?></a>
                    </div>
                <?php endif; ?>

                <div class="categories-grid">
                    <?php
                    $term_args = [
                        'taxonomy'   => 'qa_tags',
                        'hide_empty' => false,
                    ];
                    if ($filter_active) {
                        if (count($filter_slugs) === 1) {
                            $term_args['meta_query'] = [
                                [
                                    'key'   => 'qa_knowledge_map_category',
                                    'value' => $filter_slugs[0],
                                ],
                            ];
                        } else {
                            $term_args['meta_query'] = [
                                [
                                    'key'     => 'qa_knowledge_map_category',
                                    'value'   => $filter_slugs,
                                    'compare' => 'IN',
                                ],
                            ];
                        }
                    }
                    $categories = get_terms($term_args);
                    if (is_wp_error($categories)) {
                        $categories = [];
                    }

                    if (!empty($categories)) :
                        foreach ($categories as $index => $category) :
                            $archive_url = get_term_link($category);
                            $icon        = get_term_fa_icon($category->term_id, 'fas fa-folder');
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
                        <?php endforeach;
                    else : ?>
                        <p class="no-categories">
                            <?php
                            echo $filter_active
                                ? esc_html__('לא נמצאו נושאי מפתח המשויכים לאזור זה במפה. שיוך נושאים נעשה בלוח ניהול נושאי מפתח, בשדה קטגוריית מפת הידע.', 'text-domain')
                                : esc_html__('No categories found.', 'text-domain');
                            ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <aside class="bib-cats-sidebar" aria-label="<?php esc_attr_e('סינון', 'text-domain'); ?>">
                <div class="bib-cats-filter-placeholder"></div>
            </aside>
        </div>

        </div> <!-- End supervisor-content-wrapper -->

    </div> <!-- End supervisor-page-container -->
</div>

<?php get_footer(); ?>
