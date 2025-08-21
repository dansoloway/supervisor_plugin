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

    <div class="supervisor-content supervisor-single-column-content">
        <div class="content-main">
            <?php echo the_content();  ?>
        </div>
    </div>

</div>

<?php
get_footer();
?>