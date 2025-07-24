<?php
/* Template Name: Supervisor Content */
get_header('supervisor'); ?>

<div class="supervisor-home">
    <!-- Text Search Bar -->
    <?php
        $search_bar_path = PLUGIN_ROOT . 'inc/top_search_bar.php';
        

        if (file_exists($search_bar_path)) {
            require_once $search_bar_path;
        } else {
            error_log('Search bar file not found: ' . $search_bar_path);
        }
    ?>

    <div class="supervisor-content supervisor-two-column-content">

        <?php
            $sidebar_path =  PLUGIN_ROOT . 'inc/sidebar.php';

            if (file_exists($sidebar_path)) {
                require_once $sidebar_path;
            } else {
                error_log('Sidebar file not found: ' . $sidebar_path);
            }
        ?>

        <div>
            <?php echo the_content();  ?>
        </div>

        <!-- Vertical Buttons -->
         
      

    </div>

</div>

<?php
get_footer();
?>