<?php
/**
 * Template for Supervisor About Page
 * Dedicated template; accordion functionality to be added.
 */
get_header('supervisor');
?>

<div class="supervisor-home supervisor-about-page">
    <!-- Navigation Menu -->
    <?php
        $nav_path = plugin_dir_path(__FILE__) . '../inc/navigation.php';
        if (file_exists($nav_path)) {
            require_once $nav_path;
        }
    ?>

    <!-- Standardized Page Container -->
    <div class="supervisor-page-container">

        <div class="supervisor-content-wrapper supervisor-single-column">
            <div class="content-main about-content">
                <?php
                while (have_posts()) : the_post();
                    the_title('<h1 class="page-title">', '</h1>');
                    echo apply_filters('the_content', get_the_content());
                endwhile;
                ?>
            </div>
        </div>

    </div> <!-- End supervisor-page-container -->

</div>

<?php get_footer(); ?>
