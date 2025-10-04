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
        <div class="supervisor-content-wrapper supervisor-single-column">
        
        <!-- Page Title -->
        <div class="page-header">
            <h1 class="page-title">יצירת קשר</h1>
        </div>
        
        <!-- Contact Form -->
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
        
        <!-- Contact Information -->
        <div class="contact-info">
            <h2>פרטי יצירת קשר</h2>
            <div class="contact-details">
                <div class="contact-item">
                    <h3>כתובת אימייל</h3>
                    <p>TalLen@jdc.org</p>
                </div>
                
                <div class="contact-item">
                    <h3>שעות פעילות</h3>
                    <p>ימים א'-ה' | 8:00-17:00</p>
                </div>
                
                <div class="contact-item">
                    <h3>זמן תגובה</h3>
                    <p>נחזור אליכם תוך 24 שעות</p>
                </div>
            </div>
        </div>

        </div> <!-- End supervisor-content-wrapper -->

    </div> <!-- End supervisor-page-container -->
</div>

<?php get_footer(); ?>
