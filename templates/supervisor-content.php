<?php
/* Template Name: Supervisor Content */
get_header('supervisor'); ?>

<div class="supervisor-home">
    <!-- Text Search Bar -->
    <div class="supervisor-search">
        <input type="text" placeholder="חפש..." class="supervisor-search-bar" />
    </div>

    <div class="supervisor-content supervisor-two-column-content">

        <div>
            <?php echo the_content();  ?>
        </div>

        <!-- Vertical Buttons -->
         
       <?php
       //require_once trailingslashit(dirname(__FILE__, 2)) . 'inc/sidebar.php';
       require_once PLUGIN_ROOT . 'inc/sidebar.php';

        if (file_exists($sidebar_path)) {
            require_once $sidebar_path;
        } else {
            error_log('Sidebar file not found: ' . $sidebar_path);
        }
       ?>

    </div>

</div>

<?php
get_footer();
?>