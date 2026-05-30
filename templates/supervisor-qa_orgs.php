<?php
/**
 * Template Name: Supervisor Organizations
 *
 * ACF (qa_orgs):
 * - `qa_org_acronym` (optional text): English acronym; fallback post title.
 * - `qa_country_code` (select): registered by plugin from flag-icons country.json; optional legacy `qa_country` text still used as flag fallback.
 */
get_header('supervisor');
?>

<div class="supervisor-home supervisor-qa_orgs">
    <!-- Navigation Menu -->
    <?php
        $nav_path = PLUGIN_ROOT . 'inc/navigation.php';
        if (file_exists($nav_path)) {
            require_once $nav_path;
        }
    ?>

    <!-- Standardized Page Container -->
    <div class="supervisor-page-container">

        <!-- Main Content -->
        <div>

        <!-- Column 1: Organizations Grid -->
        <div class="categories-container">
            <h1 class="page-title">גופי פיקוח בעולם</h1>
            <p>
            ארגוני הפיקוח האמונים על השירותים החברתיים הם לעיתים גופים הפועלים מתוך הממשלה (בדומה לאלו במשרד הרווחה, במשרד הבריאות ובמשרד החינוך בישראל), ולעיתים הם פועלים כיחידות עצמאיות חוץ-ממשלתית בעבור משרד ממשלתי.
            </p>

            <div class="org-card-grid">
    <?php
    $args = [
        'post_type'      => 'qa_orgs',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ];
    $query = new WP_Query($args);

    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post();
            $acf_fields = get_fields(get_the_ID());
            $organization_link = get_permalink();
            $flag_meta           = supervisor_org_resolve_flag_meta($acf_fields ?: []);
            $country_flag_url    = $flag_meta['url'];
            $country_display_for_alt = $flag_meta['name'] !== ''
                ? $flag_meta['name']
                : ($acf_fields['qa_country'] ?? '');

            $acronym_raw = isset($acf_fields['qa_org_acronym']) ? trim((string) $acf_fields['qa_org_acronym']) : '';
            $acronym     = $acronym_raw !== '' ? $acronym_raw : get_the_title();
            $tagline     = isset($acf_fields['qa_subtitle']) ? trim((string) $acf_fields['qa_subtitle']) : '';

            $terms = get_the_terms(get_the_ID(), 'qa_themes');
            $has_themes = $terms && ! is_wp_error($terms) && count($terms) > 0;
            $themes_text = $has_themes ? implode(', ', wp_list_pluck($terms, 'name')) : '';
            ?>

            <a href="<?php echo esc_url($organization_link); ?>" class="org-card">
                <div class="org-card__main">
                    <div class="org-card__headline">
                        <?php if ($country_flag_url) : ?>
                            <div class="org-card__flag-well" aria-hidden="true">
                                <img
                                    src="<?php echo esc_url($country_flag_url); ?>"
                                    alt=""
                                    class="org-card__flag"
                                    width="40"
                                    height="28"
                                    loading="lazy"
                                    decoding="async"
                                />
                            </div>
                        <?php endif; ?>
                        <div class="org-card__en">
                            <h2 class="org-card__acronym"><?php echo esc_html($acronym); ?></h2>
                            <?php if ($tagline !== '') : ?>
                                <p class="org-card__tagline"><?php echo esc_html($tagline); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="org-card__footer">
                    <span class="org-card__arrow" aria-hidden="true">
                        <svg class="org-card__arrow-svg" width="32" height="10" viewBox="0 0 32 10" fill="none" xmlns="http://www.w3.org/2000/svg" focusable="false">
                            <path d="M30 5H6M12 1L6 5l6 4" stroke="currentColor" stroke-width="1.15" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <?php if ($has_themes) : ?>
                        <span class="org-card__themes" dir="rtl"><?php echo esc_html($themes_text); ?></span>
                    <?php endif; ?>
                </div>
            </a>

        <?php endwhile;
        wp_reset_postdata();
    else : ?>
        <p><?php esc_html_e('No organizations found.', 'text-domain'); ?></p>
    <?php endif; ?>
</div>
        </div>

        </div> <!-- End supervisor-content-wrapper -->

    </div> <!-- End supervisor-page-container -->
</div>
<?php get_footer(); ?>
