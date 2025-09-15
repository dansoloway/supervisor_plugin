<?php
/* Template Name: Supervisor Content */
get_header('supervisor'); ?>

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
            <div class="content-main">
                <?php echo the_content();  ?>
            </div>
        </div>

    </div> <!-- End supervisor-page-container -->

</div>

<?php
get_footer();
?>