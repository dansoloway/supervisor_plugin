<?php
/* Template Name: Supervisor Single Story */
get_header('supervisor');
?>
<div class="supervisor-home supervisor-single-story">
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
        <div class="supervisor-content-wrapper">
        <?php
        while (have_posts()) : the_post();
        ?>
            <article class="story-single">
                <div class="story-body">
                    <?php if (has_post_thumbnail()) : ?>
                    <div class="story-featured-image">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                    <?php endif; ?>

                    <header class="story-header">
                        <h1 class="story-main-title"><?php the_title(); ?></h1>
                    </header>

                    <div class="story-content content-text">
                        <?php the_content(); ?>
                    </div>
                </div>
            </article>
        <?php endwhile; ?>
        </div>

    </div> <!-- End supervisor-page-container -->
</div>

<?php get_footer(); ?>
