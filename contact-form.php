<?php
/**
 * Hebrew Contact Form for Supervisor Plugin
 */

// Contact form configuration
function supervisor_contact_form_config() {
    return [
        'recipients' => [
            'TalLen@jdc.org',
            // Add more email addresses here as needed
            // 'another@example.com',
            // 'third@example.com',
        ],
        'subject_prefix' => '[המפקחת - טופס יצירת קשר]',
        'from_email' => get_option('admin_email'),
        'from_name' => 'מערכת המפקחת',
    ];
}

// Handle contact form submission
function supervisor_handle_contact_form() {
    if (!isset($_POST['supervisor_contact_submit']) || !wp_verify_nonce($_POST['supervisor_contact_nonce'], 'supervisor_contact_form')) {
        return;
    }

    $config = supervisor_contact_form_config();
    
    // Sanitize form data
    $name = sanitize_text_field($_POST['contact_name']);
    $email = sanitize_email($_POST['contact_email']);
    $content = sanitize_textarea_field($_POST['contact_content']);
    
    // Validate required fields
    if (empty($name) || empty($email) || empty($content)) {
        wp_redirect(add_query_arg('contact_status', 'error', wp_get_referer()));
        exit;
    }
    
    if (!is_email($email)) {
        wp_redirect(add_query_arg('contact_status', 'invalid_email', wp_get_referer()));
        exit;
    }
    
    // Prepare email content
    $subject = $config['subject_prefix'] . ' - ' . $name;
    $message = "
שם: {$name}
אימייל: {$email}

תוכן הפנייה:
{$content}

---
הודעה זו נשלחה מטופס יצירת הקשר באתר המפקחת.
";
    
    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . $config['from_name'] . ' <' . $config['from_email'] . '>',
        'Reply-To: ' . $name . ' <' . $email . '>',
    ];
    
    // Send email to all recipients
    $success = true;
    foreach ($config['recipients'] as $recipient) {
        $sent = wp_mail($recipient, $subject, nl2br($message), $headers);
        if (!$sent) {
            $success = false;
        }
    }
    
    if ($success) {
        wp_redirect(add_query_arg('contact_status', 'success', wp_get_referer()));
    } else {
        wp_redirect(add_query_arg('contact_status', 'send_error', wp_get_referer()));
    }
    exit;
}
add_action('init', 'supervisor_handle_contact_form');

// Display contact form
function supervisor_display_contact_form() {
    $config = supervisor_contact_form_config();
    $status = isset($_GET['contact_status']) ? $_GET['contact_status'] : '';
    
    // Display status messages
    if ($status === 'success') {
        echo '<div class="supervisor-notice supervisor-notice-success">';
        echo '<p>תודה רבה! הודעתכם נשלחה בהצלחה ונחזור אליכם בהקדם.</p>';
        echo '</div>';
    } elseif ($status === 'error') {
        echo '<div class="supervisor-notice supervisor-notice-error">';
        echo '<p>שגיאה: יש למלא את כל השדות הנדרשים.</p>';
        echo '</div>';
    } elseif ($status === 'invalid_email') {
        echo '<div class="supervisor-notice supervisor-notice-error">';
        echo '<p>שגיאה: כתובת האימייל אינה תקינה.</p>';
        echo '</div>';
    } elseif ($status === 'send_error') {
        echo '<div class="supervisor-notice supervisor-notice-error">';
        echo '<p>שגיאה: לא ניתן לשלוח את ההודעה. אנא נסו שוב מאוחר יותר.</p>';
        echo '</div>';
    }
    
    ?>
    <div class="supervisor-contact-form">
        <p>נשמח לשמוע ממכם</p>
        
        <form method="post" action="" class="supervisor-form" dir="rtl">
            <?php wp_nonce_field('supervisor_contact_form', 'supervisor_contact_nonce'); ?>
            
            <div class="form-group">
                <label for="contact_name">שם מלא *</label>
                <input type="text" id="contact_name" name="contact_name" required 
                       value="<?php echo esc_attr(isset($_POST['contact_name']) ? $_POST['contact_name'] : ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="contact_email">כתובת אימייל *</label>
                <input type="email" id="contact_email" name="contact_email" required 
                       value="<?php echo esc_attr(isset($_POST['contact_email']) ? $_POST['contact_email'] : ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="contact_content">תוכן הפנייה *</label>
                <textarea id="contact_content" name="contact_content" rows="6" required 
                          placeholder="אנא פרטו את תוכן הפנייה שלכם..."><?php echo esc_textarea(isset($_POST['contact_content']) ? $_POST['contact_content'] : ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <button type="submit" name="supervisor_contact_submit" class="supervisor-button">
                    שליחת הודעה
                </button>
            </div>
        </form>
    </div>
    <?php
}

// Add contact form shortcode
function supervisor_contact_form_shortcode($atts) {
    ob_start();
    supervisor_display_contact_form();
    return ob_get_clean();
}
add_shortcode('supervisor_contact_form', 'supervisor_contact_form_shortcode');

// Add contact form to admin menu
function supervisor_add_contact_form_admin() {
    add_submenu_page(
        'supervisor-admin',
        'יצירת קשר',
        'יצירת קשר',
        'read', // Capability - visible to all, but editing restricted
        'supervisor-contact',
        'supervisor_contact_admin_page'
    );
}
add_action('admin_menu', 'supervisor_add_contact_form_admin', 30); // Run after main menu (priority 20)

// Contact form admin page
function supervisor_contact_admin_page() {
    // Check permissions - only admins and supervisor editors can edit
    if (!current_user_can('manage_options') && !current_user_can('edit_qa_updates') && !current_user_can('supervisor_editor')) {
        wp_die(__('אין לך הרשאות לגשת לעמוד זה.', 'text-domain'));
    }
    
    $config = supervisor_contact_form_config();
    ?>
    <div class="wrap">
        <h1>ניהול טופס יצירת קשר</h1>
        
        <div class="contact-form-info">
            <h2>הגדרות טופס יצירת קשר</h2>
            
            <h3>כתובות אימייל לקבלת הודעות:</h3>
            <ul>
                <?php foreach ($config['recipients'] as $email): ?>
                    <li><?php echo esc_html($email); ?></li>
                <?php endforeach; ?>
            </ul>
            
            <h3>שימוש בטופס:</h3>
            <p><strong>כקוד קצר:</strong> <code>[supervisor_contact_form]</code></p>
            <p><strong>כפונקציה PHP:</strong> <code>&lt;?php supervisor_display_contact_form(); ?&gt;</code></p>
            
            <h3>עריכת הגדרות:</h3>
            <p>כדי לשנות את כתובות האימייל, ערכו את הקובץ <code>contact-form.php</code> ושינוי את המערך <code>recipients</code> בפונקציה <code>supervisor_contact_form_config()</code>.</p>
            
            <h3>דוגמה להוספת כתובת אימייל:</h3>
            <pre><code>'recipients' => [
    'TalLen@jdc.org',
    'newperson@jdc.org',
    'another@example.com',
],</code></pre>
        </div>
    </div>
    <?php
}

