<?php
/**
 * Template Name: Supervisor Contact
 */
get_header('supervisor');
?>

<div class="supervisor-home supervisor-contact-page">
    <!-- Navigation Menu -->
    <?php
        $nav_path = plugin_dir_path(__FILE__) . '../inc/navigation.php';
        if (file_exists($nav_path)) {
            require_once $nav_path;
        }
    ?>

    <!-- Standardized Page Container -->
    <div class="supervisor-page-container">
        
        <!-- Main Content Area -->
        <div class="supervisor-content-wrapper supervisor-two-column">
        <!-- Right Column: Contact Info -->
        <div class="contact-info-column">
            <h2 class="contact-info-title">ליצירת קשר:</h2>
            <?php 
            $contact_name  = get_option('supervisor_contact_name', '');
            $contact_email = get_option('supervisor_contact_email', '');
            if ($contact_email) {
                echo '<p class="contact-email">';
                if ($contact_name) {
                    echo esc_html($contact_name) . ', ';
                }
                echo '<a href="mailto:' . esc_attr($contact_email) . '">' . esc_html($contact_email) . '</a></p>';
            } elseif ($contact_name) {
                echo '<p class="contact-email">' . esc_html($contact_name) . '</p>';
            } else {
                echo '<p class="contact-email">לא הוגדרה כתובת אימייל.<br><small>נא להגדיר בהגדרות המערכת.</small></p>';
            }
            ?>
        </div>
        
        <!-- Left Column: Contact Form -->
        <div class="contact-form-container">
        <!-- <h3 style="color:black;text-align:right;">צור קשר</h3> -->

            <?php 
            if (function_exists('supervisor_display_contact_form')) {
                supervisor_display_contact_form(); 
            } else {
                echo '<p>טופס יצירת קשר בטעינה...</p>';
            }
            ?>
        </div>
        

        </div> <!-- End supervisor-content-wrapper -->

    </div> <!-- End supervisor-page-container -->
</div>

<?php get_footer(); ?>
