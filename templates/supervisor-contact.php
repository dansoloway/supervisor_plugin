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
        
        <!-- Page Title -->
        <div class="page-header">
            <h1 class="page-title">יצירת קשר</h1>
        </div>
        
        <!-- Right Column: Contact Info -->
        <div class="contact-info-column">
            <h2 class="contact-info-title">ליצירת קשר:</h2>
            <?php 
            $contact_email = get_option('supervisor_contact_email', '');
            if ($contact_email) {
                echo '<p class="contact-email"><a href="mailto:' . esc_attr($contact_email) . '">' . esc_html($contact_email) . '</a></p>';
            } else {
                echo '<p class="contact-email">לא הוגדרה כתובת אימייל.<br><small>נא להגדיר בהגדרות המערכת.</small></p>';
            }
            ?>
        </div>
        
        <!-- Left Column: Contact Form -->
        <div class="contact-form-container">
            <?php 
            if (function_exists('supervisor_display_contact_form')) {
                supervisor_display_contact_form(); 
            } else {
                echo '<p>טופס יצירת קשר בטעינה...</p>';
                echo '<p>אנא צרו קשר בכתובת: <a href="mailto:TalLen@jdc.org">TalLen@jdc.org</a></p>';
            }
            ?>
        </div>
        

        </div> <!-- End supervisor-content-wrapper -->

    </div> <!-- End supervisor-page-container -->
</div>

<?php get_footer(); ?>
