<?php
get_header('supervisor');
?>

<div class="supervisor-home">
    <!-- Navigation Menu -->
    <?php
        $nav_path = PLUGIN_ROOT . 'inc/navigation.php';
        if (file_exists($nav_path)) {
            require_once $nav_path;
        }
    ?>

    <!-- Standardized Page Container -->
    <div class="supervisor-page-container">
        
        <div class="supervisor-content-wrapper supervisor-single-column">
            <?php while (have_posts()) : the_post(); ?>
                <h1><?php echo get_the_title(); ?></h1>
                <div><?php echo get_the_content(); ?></div>
            <?php endwhile; ?>
        
        </div> <!-- End supervisor-content-wrapper -->

    </div> <!-- End supervisor-page-container -->

</div>

<?php get_footer();