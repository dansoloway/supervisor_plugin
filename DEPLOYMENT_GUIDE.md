# 🚀 Supervisor Plugin - Production Deployment Guide

This guide covers the complete process of deploying the Supervisor Plugin from staging to production, including all manual setup steps, ACF configuration, and dependencies.

## 📋 Pre-Deployment Checklist

### ✅ Git Repository
- [ ] All code committed to git repository
- [ ] Repository is accessible from production server
- [ ] No sensitive data (passwords, API keys) in repository

### ✅ Dependencies
- [ ] WordPress 5.0+ installed on production
- [ ] PHP 7.4+ (recommended 8.0+)
- [ ] Advanced Custom Fields (ACF) Pro plugin installed
- [ ] Access to WordPress admin dashboard

---

## 🔧 Step 1: Plugin Installation

### 1.1 Deploy Plugin Files
```bash
# Clone or upload plugin to production server
cd /path/to/wordpress/wp-content/plugins/
git clone [your-repository-url] supervisor_plugin
# OR upload via FTP/SFTP
```

### 1.2 Activate Plugin
1. Go to **WordPress Admin → Plugins → Installed Plugins**
2. Find "Supervisor Plugin" and click **Activate**

---

## 📄 Step 2: Page Creation

### 2.1 Create Required Pages
Create these pages in WordPress Admin → Pages → Add New:

#### Home Page
- **Title**: "המפקחת - דף הבית"
- **Slug**: `supervisor-home`
- **Template**: "Supervisor Home" (from dropdown)
- **Status**: Published

#### Activities Page  
- **Title**: "תחומי פעילות"
- **Slug**: `supervisor-activities`
- **Template**: "Supervisor Activities" (from dropdown)
- **Status**: Published

#### Knowledge Map Page
- **Title**: "מפת הידע"
- **Slug**: `supervisor-knowledge-map`
- **Template**: "Supervisor Knowledge Map" (from dropdown)
- **Status**: Published

#### Updates Page
- **Title**: "עדכונים"
- **Slug**: `supervisor-updates`
- **Template**: "Supervisor QA Updates" (from dropdown)
- **Status**: Published

#### Organizations Page
- **Title**: "ארגונים"
- **Slug**: `supervisor-orgs`
- **Template**: "Supervisor Organizations" (from dropdown)
- **Status**: Published

#### Bibliography Categories Page
- **Title**: "קטגוריות ביבליוגרפיה"
- **Slug**: `supervisor-bib-cats`
- **Template**: "Supervisor Bibliography Categories" (from dropdown)
- **Status**: Published

#### Contact Page
- **Title**: "יצירת קשר"
- **Slug**: `supervisor-contact`
- **Template**: "Supervisor Contact" (from dropdown)
- **Status**: Published

#### About Page
- **Title**: "אודות"
- **Slug**: `supervisor-about`
- **Template**: "Supervisor Content" (from dropdown)
- **Status**: Published

#### Intro Text Page
- **Title**: "טקסט פתיחה"
- **Slug**: `supervisor-intro`
- **Template**: "Supervisor Content" (from dropdown)
- **Status**: Published

### 2.2 Update Page IDs in Configuration
After creating pages, update the page IDs in `config.php`:

```php
// Get page IDs from WordPress Admin → Pages
// Edit each page and check the URL: /wp-admin/post.php?post=XXXXX&action=edit
// The number in the URL is the page ID

define('SUPERVISOR_HOME', 12345);           // Replace with actual ID
define('SUPERVISOR_ACTIVITIES', 12346);     // Replace with actual ID
define('SUPERVISOR_KNOWLEDGE_MAP', 12347);  // Replace with actual ID
define('SUPERVISOR_UPDATES', 12348);        // Replace with actual ID
define('SUPERVISOR_ORGS', 12349);           // Replace with actual ID
define('SUPERVISOR_BIB_CATS', 12350);       // Replace with actual ID
define('SUPERVISOR_CONTACT', 12351);        // Replace with actual ID
define('SUPERVISOR_ABOUT', 12352);          // Replace with actual ID
define('SUPERVISOR_INTRO_TEXT', 12353);     // Replace with actual ID
```

---

## 🎨 Step 3: ACF (Advanced Custom Fields) Setup

### 3.1 Install ACF Pro
1. Upload ACF Pro plugin files to `/wp-content/plugins/advanced-custom-fields-pro/`
2. Activate the plugin
3. Enter your ACF Pro license key in **Custom Fields → Updates**

### 3.2 Create ACF Field Groups

#### Field Group 1: Activities Page Fields
1. Go to **Custom Fields → Field Groups → Add New**
2. **Title**: "Supervisor Activities Fields"
3. **Location Rules**: 
   - Page Template is equal to Supervisor Activities
4. **Fields**:
   ```
   Field Label: "Areas of Activity"
   Field Name: "qa_areas_of_activity"
   Field Type: "Repeater"
   Sub Fields:
     - Title (Text)
       - Field Label: "Activity Title"
       - Field Name: "qa_areas_of_activity_title"
     - Icon (Text)
       - Field Label: "Font Awesome Icon Class"
       - Field Name: "qa_areas_of_activity_icon"
       - Instructions: "Enter Font Awesome icon class (e.g., fas fa-chart-line)"
     - Content (Textarea)
       - Field Label: "Activity Description"
       - Field Name: "qa_areas_of_activity_content"
   ```

#### Field Group 2: Contact Page Fields
1. Go to **Custom Fields → Field Groups → Add New**
2. **Title**: "Supervisor Contact Fields"
3. **Location Rules**: 
   - Page Template is equal to Supervisor Contact
4. **Fields**:
   ```
   Field Label: "Contact Information"
   Field Name: "qa_contact_info"
   Field Type: "Repeater"
   Sub Fields:
     - Type (Text)
       - Field Label: "Contact Type"
       - Field Name: "qa_contact_type"
     - Value (Text)
       - Field Label: "Contact Value"
       - Field Name: "qa_contact_value"
   ```

#### Field Group 3: QA Updates Fields
1. Go to **Custom Fields → Field Groups → Add New**
2. **Title**: "QA Updates Fields"
3. **Location Rules**: 
   - Post Type is equal to QA Updates
4. **Fields**:
   ```
   Field Label: "Update Date"
   Field Name: "qa_updates_date"
   Field Type: "Date Picker"
   Required: Yes
   
   Field Label: "Update Content"
   Field Name: "qa_updates_content"
   Field Type: "Wysiwyg Editor"
   Required: Yes
   
   Field Label: "Is Highlight"
   Field Name: "qa_updates_highlight"
   Field Type: "True/False"
   Default Value: No
   ```

#### Field Group 4: QA Organizations Fields
1. Go to **Custom Fields → Field Groups → Add New**
2. **Title**: "QA Organizations Fields"
3. **Location Rules**: 
   - Post Type is equal to QA Organizations
4. **Fields**:
   ```
   Field Label: "Organization Website"
   Field Name: "qa_org_website"
   Field Type: "URL"
   
   Field Label: "Contact Email"
   Field Name: "qa_org_email"
   Field Type: "Email"
   
   Field Label: "Yearly Report"
   Field Name: "qa_org_report"
   Field Type: "URL"
   
   Field Label: "Organization Type"
   Field Name: "qa_org_type"
   Field Type: "Text"
   
   Field Label: "Established Year"
   Field Name: "qa_org_established"
   Field Type: "Number"
   
   Field Label: "Number of Employees"
   Field Name: "qa_org_employees"
   Field Type: "Number"
   
   Field Label: "Geographic Focus"
   Field Name: "qa_org_geographic"
   Field Type: "Text"
   
   Field Label: "Services"
   Field Name: "qa_org_services"
   Field Type: "Textarea"
   
   Field Label: "Target Population"
   Field Name: "qa_org_target"
   Field Type: "Text"
   ```

#### Field Group 5: QA Bibliography Fields
1. Go to **Custom Fields → Field Groups → Add New**
2. **Title**: "QA Bibliography Fields"
3. **Location Rules**: 
   - Post Type is equal to QA Bibliography
4. **Fields**:
   ```
   Field Label: "Author"
   Field Name: "qa_bib_author"
   Field Type: "Text"
   
   Field Label: "Publication Year"
   Field Name: "qa_bib_year"
   Field Type: "Number"
   
   Field Label: "Journal/Publisher"
   Field Name: "qa_bib_journal"
   Field Type: "Text"
   
   Field Label: "DOI/URL"
   Field Name: "qa_bib_url"
   Field Type: "URL"
   
   Field Label: "Abstract"
   Field Name: "qa_bib_abstract"
   Field Type: "Textarea"
   ```

---

## 🎭 Step 4: Theme Integration

### 4.1 Create Supervisor Header Template
Create file: `/wp-content/themes/[your-theme]/header-supervisor.php`

```php
<!DOCTYPE html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php wp_title('|', true, 'right'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class('supervisor-body'); ?>>
    <div id="page" class="site">
        <header id="masthead" class="site-header">
            <!-- Your header content here -->
        </header>
        <div id="content" class="site-content">
```

### 4.2 Create Supervisor Footer Template
Create file: `/wp-content/themes/[your-theme]/footer-supervisor.php`

```php
        </div><!-- #content -->
        <footer id="colophon" class="site-footer">
            <!-- Your footer content here -->
        </footer>
    </div><!-- #page -->
    <?php wp_footer(); ?>
</body>
</html>
```

---

## 👤 Step 5: User Role Setup

### 5.1 Create Supervisor Editor Role
The plugin automatically creates the "Supervisor Editor" role when activated.

### 5.2 Create Supervisor Editor User
1. Go to **Users → Add New**
2. **Username**: Choose a username
3. **Email**: Enter email address
4. **Role**: Select "Supervisor Editor"
5. **Password**: Generate or set password
6. Click **Add New User**

### 5.3 Test Supervisor Editor Access
1. Log out of admin account
2. Log in with Supervisor Editor credentials
3. Verify access is limited to plugin-related content only

---

## 📧 Step 6: Email Configuration

### 6.1 Update Contact Form Recipients
Edit `/wp-content/plugins/supervisor_plugin/contact-form.php`:

```php
// Line 5: Update recipients array
$recipients = [
    'TalLen@jdc.org',
    'additional@recipient.com',  // Add more recipients as needed
];
```

### 6.2 Configure WordPress Email
Ensure WordPress can send emails:
1. **Option A**: Use SMTP plugin (recommended for production)
2. **Option B**: Configure server mail settings
3. **Option C**: Use email service (SendGrid, Mailgun, etc.)

---

## 🎨 Step 7: Assets and Styling

### 7.1 Verify CSS Files
Ensure these files are present and accessible:
- `/wp-content/plugins/supervisor_plugin/assets/css/supervisor-styles.css`
- `/wp-content/plugins/supervisor_plugin/assets/js/supervisor-scripts.js`

### 7.2 Check Image Assets
Verify these images exist:
- `/wp-content/plugins/supervisor_plugin/assets/img/knowledge_map.svg`
- Any other images referenced in templates

---

## 🗂️ Step 8: Content Setup

### 8.1 Create Sample Content

#### Create Sample QA Updates
1. Go to **QA Updates → Add New**
2. Create 3-5 sample updates with:
   - Title in Hebrew
   - Update Date (use Date Picker)
   - Content in Hebrew
   - Set one as "Highlight"

#### Create Sample Organizations
1. Go to **QA Organizations → Add New**
2. Create 2-3 sample organizations with all fields filled

#### Create Sample Bibliography Items
1. Go to **QA Bibliography → Add New**
2. Create 2-3 sample bibliography items

### 8.2 Set Up Taxonomies

#### Create Tags (QA Tags)
1. Go to **QA Updates → Tags**
2. Create relevant Hebrew tags (e.g., "עדכון", "חדש", "חשוב")

#### Create Themes (QA Themes)
1. Go to **QA Organizations → Themes**
2. Create relevant Hebrew themes (e.g., "חינוך", "בריאות", "רווחה")

---

## 🔗 Step 9: Navigation Setup

### 9.1 Update Main Navigation
Update your theme's navigation menu to include:
- דף הבית (Home) → Link to Supervisor Home page
- תחומי פעילות (Activities) → Link to Activities page
- מפת הידע (Knowledge Map) → Link to Knowledge Map page
- עדכונים (Updates) → Link to Updates page
- ארגונים (Organizations) → Link to Organizations page
- צור קשר (Contact) → Link to Contact page

### 9.2 Set Homepage
1. Go to **Settings → Reading**
2. Set "Homepage displays" to "A static page"
3. Select your Supervisor Home page as the homepage

---

## ✅ Step 10: Final Testing

### 10.1 Test All Pages
Visit each page and verify:
- [ ] Home page loads correctly
- [ ] Activities page shows content (or "no activities" message)
- [ ] Knowledge map displays properly
- [ ] Updates page shows sample content
- [ ] Organizations page lists sample organizations
- [ ] Contact form works and sends emails
- [ ] Navigation links work correctly

### 10.2 Test Admin Functionality
- [ ] Supervisor Editor can access admin
- [ ] Supervisor Editor can create/edit plugin content
- [ ] Supervisor Editor cannot access restricted areas
- [ ] ACF fields appear in edit screens
- [ ] Custom post types are accessible

### 10.3 Test Email Functionality
- [ ] Contact form sends emails to configured recipients
- [ ] Email content is properly formatted
- [ ] Hebrew text displays correctly in emails

---

## 🚨 Troubleshooting

### Common Issues:

#### Pages Show Blank/Error
- Check page IDs in `config.php` match actual WordPress page IDs
- Verify page templates are selected correctly
- Check for PHP errors in debug log

#### ACF Fields Not Showing
- Ensure ACF Pro is activated and licensed
- Check field group location rules match page templates
- Verify field names match template code

#### Contact Form Not Working
- Check email recipients in `contact-form.php`
- Verify WordPress can send emails
- Check for JavaScript errors in browser console

#### Supervisor Editor Role Issues
- Deactivate and reactivate plugin to recreate role
- Check user capabilities in database if needed
- Verify role restrictions are working

---

## 📞 Support

If you encounter issues during deployment:

1. **Check WordPress Debug Log**: `/wp-content/debug.log`
2. **Enable WordPress Debug**: Add to `wp-config.php`:
   ```php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   define('WP_DEBUG_DISPLAY', false);
   ```
3. **Check Plugin Error Logs**: Look for any custom error logging
4. **Verify File Permissions**: Ensure plugin files are readable

---

## 🎉 Post-Deployment

After successful deployment:

1. **Remove Debug Code**: Clean up any debugging code left in templates
2. **Optimize Performance**: Consider caching plugins for production
3. **Backup Strategy**: Set up regular backups of database and files
4. **Monitor**: Keep an eye on error logs and user feedback
5. **Update Documentation**: Document any customizations made during deployment

---

*This deployment guide covers the complete process of moving the Supervisor Plugin from staging to production. Follow each step carefully and test thoroughly before going live.*
