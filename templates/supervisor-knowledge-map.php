<?php
/* Template Name: Supervisor Knowledge Map */
get_header('supervisor');
?>

<div class="supervisor-home supervisor-knowledge-map">
    <!-- Navigation Menu -->
    <?php
        $nav_path = plugin_dir_path(__FILE__) . '../inc/navigation.php';
        if (file_exists($nav_path)) {
            require_once $nav_path;
        }
    ?>

    <!-- Standardized Page Container -->
    <div class="supervisor-page-container">

        <div class="supervisor-content-wrapper supervisor-single-column knowledge-map-page-inner">
        <h1 class="page-title"><?php the_title(); ?></h1>

        <div class="knowledge-map-page-stack">
            <div class="knowledge-map-main">
                <section class="knowledge-map-section knowledge-map-page-tiles" aria-label="<?php esc_attr_e('מפת הידע', 'text-domain'); ?>">
                    <div class="home-knowledge-map">
                        <div class="knowledge-map-grid">
                            <a class="km-tile km-top-left" href="<?php echo esc_url(supervisor_knowledge_map_tile_url('regulatory_welfare_state')); ?>"><span>מדינת הרווחה הרגולטורית</span></a>
                            <a class="km-tile km-top-right" href="<?php echo esc_url(supervisor_knowledge_map_tile_url('social_procurement')); ?>"><span>רכש חברתי</span></a>
                            <div class="km-middle">
                                <a class="km-tile km-mid-left" href="<?php echo esc_url(supervisor_knowledge_map_tile_url('policy')); ?>"><span>מדיניות</span></a>
                                <a class="km-tile km-mid-right" href="<?php echo esc_url(supervisor_knowledge_map_tile_url('knowledge_development')); ?>"><span>פיתוח ידע והדרכה</span></a>
                                <a class="km-tile km-bottom-left" href="<?php echo esc_url(supervisor_knowledge_map_tile_url('control')); ?>"><span>בקרה</span></a>
                                <a class="km-tile km-bottom-right" href="<?php echo esc_url(supervisor_knowledge_map_tile_url('enforcement')); ?>"><span>אכיפה</span></a>
                                <div class="km-center" aria-hidden="true"><span>גוף<br>פיקוח</span></div>
                            </div>
                            <a class="km-wide" href="<?php echo esc_url(supervisor_knowledge_map_tile_url('working_methods')); ?>"><span>שיטות עבודה</span></a>
                        </div>
                    </div>
                </section>

                <?php while (have_posts()) : the_post(); ?>
                <?php
                $show_knowledge_map_description = false;
                if ($show_knowledge_map_description) :
                    $km_body = trim((string) get_post_field('post_content', get_the_ID()));
                    if ($km_body !== '') :
                ?>
                <div class="knowledge-map-description">
                    <h2 class="knowledge-map-title">מפת הידע:</h2>
                    <div class="knowledge-map-description__content">
                        <?php the_content(); ?>
                    </div>
                </div>
                <?php
                    endif;
                endif;
                ?>
                <?php endwhile; ?>
            </div>
        </div>

        </div> <!-- End supervisor-content-wrapper -->

    </div> <!-- End supervisor-page-container -->
</div>

<?php get_footer(); ?>
