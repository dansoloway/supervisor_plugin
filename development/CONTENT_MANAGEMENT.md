# Content Management Tools

This directory contains tools for managing and migrating content between staging and production environments.

## Tools Available

### 1. Cleanup Test Content (`cleanup-test-content.php`)

Removes all test content except for one post per custom post type (keeps the newest post).

**Access:** `/wp-admin/admin.php?page=supervisor-cleanup-test-content`

**What it does:**
- Finds all posts of type: `qa_updates`, `qa_orgs`, `qa_bib_items`
- Keeps the newest post of each type
- Permanently deletes all others

**Warning:** This action is irreversible! Make sure you have a backup before running.

---

### 2. Export Content (`export-content.php`)

Exports all plugin-related content to a JSON file for migration to production.

**Access:** `/wp-admin/admin.php?page=supervisor-export-content`

**What it exports:**
- All posts of type: `qa_updates`, `qa_orgs`, `qa_bib_items`
- All taxonomies: `qa_tags`, `qa_themes` (with all terms and meta)
- All ACF fields (including images, files, galleries, relationships)
- All media files (as base64-encoded data)
- Featured images
- Post metadata
- Term metadata (like Font Awesome icons)

**Output:** Downloads a JSON file named `supervisor-export-YYYY-MM-DD-HHMMSS.json`

**Note:** Large exports with many images may take time to generate and download.

---

### 3. Import Content (`import-content.php`)

Imports content from a JSON export file created by the export tool.

**Access:** `/wp-admin/admin.php?page=supervisor-import-content`

**Features:**
- **Duplicate Detection:** Automatically skips posts that already exist (by slug)
- **Media Handling:** Skips existing media files by filename
- **ID Mapping:** Properly handles relationships between posts, terms, and media
- **ACF Support:** Imports all ACF fields including complex types (images, galleries, relationships)

**Options:**
- Skip duplicates (recommended): Prevents overwriting existing content
- Skip existing media: Prevents re-uploading files that already exist
- Update existing: Updates existing posts instead of skipping them

**Import Process:**
1. Upload the JSON export file
2. Select import options
3. Review import results (created, skipped, errors)

---

## Migration Workflow: Staging → Production

### Step 1: Clean Up Staging (Optional)

If you have test content on staging that you don't want to migrate:

1. Go to `/wp-admin/admin.php?page=supervisor-cleanup-test-content`
2. Review the warning
3. Click "הפעל ניקוי" (Run Cleanup)
4. Review results - one post per type will remain

### Step 2: Export from Staging

1. Go to `/wp-admin/admin.php?page=supervisor-export-content`
2. Click "הורד קובץ ייצוא" (Download Export File)
3. Save the JSON file (e.g., `supervisor-export-2025-01-15-143022.json`)
4. The file contains all content, media (as base64), and metadata

### Step 3: Import to Production

1. **On the production server**, go to `/wp-admin/admin.php?page=supervisor-import-content`
2. Upload the JSON export file
3. Select options:
   - ✓ Skip duplicates (recommended)
   - ✓ Skip existing media (recommended)
   - Leave "Update existing" unchecked unless you want to overwrite
4. Click "התחל ייבוא" (Start Import)
5. Review the import results:
   - Posts created/skipped
   - Terms created/skipped
   - Media created/skipped
   - Any errors

### Step 4: Verify

After import, verify on production:
- All posts are present and correctly formatted
- All taxonomies and terms are imported
- Images and media files are displaying correctly
- ACF fields contain expected data
- Relationships between posts are maintained

---

## Important Notes

### ACF Fields

The export/import system handles these ACF field types:
- **Text/Textarea/Wysiwyg:** Direct export/import
- **Image/File:** Exports file data, imports and creates attachments
- **Gallery:** Exports all images, imports and creates gallery
- **Relationship/Post Object:** Maps old IDs to new IDs
- **Taxonomy:** Maps terms using slugs (must exist before importing posts)

### Media Files

- Media files are exported as base64-encoded data
- This makes export files larger but ensures all media is included
- On import, files are uploaded to WordPress media library
- Existing files are detected by filename and skipped

### Duplicate Detection

- **Posts:** Detected by post slug (`post_name`)
- **Terms:** Detected by term slug
- **Media:** Detected by filename

### Server Requirements

- **Export:** Requires enough memory to process all posts and encode media
- **Import:** Requires enough memory and time to upload media files
- For large sites, consider increasing PHP limits:
  ```php
  ini_set('memory_limit', '512M');
  set_time_limit(600); // 10 minutes
  ```

---

## Troubleshooting

### Export fails or times out
- Increase PHP memory limit and execution time
- Export in smaller batches (modify script to export specific post types)

### Import shows errors for media
- Check file permissions on uploads directory
- Verify base64 data is valid in export file
- Check available disk space

### Relationships not working after import
- Ensure all related posts are in the same export file
- Verify term slugs match exactly between staging and production

### ACF fields missing after import
- Ensure ACF plugin is active on production
- Verify field groups are exported/imported separately (if using ACF export)
- Check field keys match between environments

---

## File Locations

- **Export file:** Downloaded to your computer, then uploaded to production
- **Temporary import file:** Stored in PHP temp directory during upload
- **Imported media:** Stored in WordPress uploads directory (`wp-content/uploads/`)

---

## Security

All tools require:
- WordPress admin access
- `manage_options` capability
- Proper nonce verification

**Never expose these tools to public access!**

