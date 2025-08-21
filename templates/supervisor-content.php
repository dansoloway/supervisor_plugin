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

    <div class="supervisor-content supervisor-single-column-content">
        <div class="content-main">
            <?php echo the_content();  ?>
        </div>
    </div>

</div>

<?php
get_footer();
?>