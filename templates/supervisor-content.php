<?php
/* Template Name: Supervisor Content */
get_header('supervisor'); ?>

<div class="supervisor-home">
    <!-- Navigation Menu -->
    <nav class="site-nav supervisor_header_links" aria-label="ראשי">
        <?php
            $nav_path = PLUGIN_ROOT . 'inc/navigation.php';
            if (file_exists($nav_path)) {
                ob_start();
                require_once $nav_path;
                $nav_content = ob_get_clean();
                // Extract just the links from the navigation file
                preg_match_all('/<a[^>]*>.*?<\/a>/s', $nav_content, $matches);
                if (!empty($matches[0])) {
                    echo implode('', $matches[0]);
                }
            }
        ?>
    </nav>

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