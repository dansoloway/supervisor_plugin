<?php
/* Template Name: Supervisor Organizations */
get_header('supervisor');
error_log('Loading supervisor-qa_orgs.php template');
?>

<div class="supervisor-container">
    <!-- Navigation Menu -->
    <?php
        $nav_path = PLUGIN_ROOT . 'inc/navigation.php';
        if (file_exists($nav_path)) {
            require_once $nav_path;
        }
    ?>

    <!-- Main Content -->
    <div class="supervisor-main supervisor-two-column">

        <!-- Column 2: Sidebar -->
        <?php
            require_once PLUGIN_ROOT . 'inc/sidebar.php';

            if (file_exists($sidebar_path)) {
                require_once $sidebar_path;
            } else {
                error_log('Sidebar file not found: ' . $sidebar_path);
            }
       ?>

        <!-- Column 1: Organizations Grid -->
        <div class="categories-container"> 
            <p>
                כאן יהיה טקסט הקדמה קצר המסביר כיצד הרשימה נבנתה כאן יהיה טקסט הקדמה קצר המסביר למה בחרנו את הארגונים האלה

                <div class="org-card-grid">
    <?php
    // Query organizations (qa_orgs)
    $args = [
        'post_type'      => 'qa_orgs',
        'posts_per_page' => -1, // Show all
        'orderby'        => 'title',
        'order'          => 'ASC'
    ];
    $query = new WP_Query($args);

    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post();
            $acf_fields = get_fields(get_the_id());
            $organization_link = get_permalink();
            $qa_country = $acf_fields['qa_country'] ?? '';

            // Get taxonomy terms
            $terms = get_the_terms(get_the_ID(), 'qa_themes');
            $qa_themes = $terms && !is_wp_error($terms)
                ? implode(', ', wp_list_pluck($terms, 'name'))
                : __('ללא', 'text-domain');
            ?>
            
            <a href="<?php echo esc_url($organization_link); ?>" class="org-card">
                <h2 class="dir_left org-title"><?php the_title(); ?></h2>
                <p class="dir_left org-description"><?php echo esc_html($acf_fields['qa_subtitle'] ?? ''); ?></p>

                <!-- Country -->
                <div class="org-info">
                    <i class="fa-solid fa-location-dot icon-space"></i>
                    <span><?php echo esc_html($qa_country); ?></span>
                </div>

                <!-- Themes (Taxonomy Terms) -->
                <div class="org-info">
                    <i class="fa-solid fa-pen-clip icon-space"></i>
                    <span><?php echo esc_html($qa_themes); ?></span>
                </div>
            </a>

        <?php endwhile;
        wp_reset_postdata();
    else : ?>
        <p><?php esc_html_e('No organizations found.', 'text-domain'); ?></p>
    <?php endif; ?>
</div>
        </div> 

    </div>
</div>
<?php get_footer(); ?>