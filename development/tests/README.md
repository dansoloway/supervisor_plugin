# Supervisor Plugin Testing & Migration System

This directory contains automated testing and migration tools for the Supervisor plugin.

## Files

- `test-migration.php` - Comprehensive test suite for migration functionality
- `../run-tests.php` - Simple test runner
- `../migrate-qa_bib_cats-to-qa_tags.php` - Migration script

## How to Use

### Running Tests

#### Option 1: Browser
1. Visit `http://your-site.com/wp-content/plugins/supervisor_plugin/run-tests.php`
2. View the test results in your browser

#### Option 2: Command Line
```bash
cd /path/to/supervisor_plugin
php run-tests.php
```

### Running Migration

#### Option 1: Browser
1. Visit `http://your-site.com/wp-content/plugins/supervisor_plugin/migrate-qa_bib_cats-to-qa_tags.php`
2. Follow the migration steps

#### Option 2: Command Line
```bash
cd /path/to/supervisor_plugin
php migrate-qa_bib_cats-to-qa_tags.php
```

## Test Coverage

The test suite covers:

1. **Taxonomy Registration**
   - qa_tags exists and is properly configured
   - qa_tags is available for all post types
   - qa_bib_cats is removed

2. **Admin Functionality**
   - Bibliography admin page exists
   - Admin menu is registered
   - Custom fields hooks exist

3. **Template Functionality**
   - All required templates exist
   - Template loading functions work

4. **Search Functionality**
   - Search results template exists
   - AJAX search handler exists
   - Search form template exists

5. **Icon Functionality**
   - Icon helper functions exist
   - Icon field hooks exist

6. **AJAX Functionality**
   - Item order AJAX handler exists
   - save_item_order function exists

7. **Data Integrity**
   - qa_tags has terms
   - All post types have content

## Migration Process

The migration script performs:

1. **Term Migration** - Moves terms from qa_bib_cats to qa_tags
2. **Meta Migration** - Moves custom fields (icons, descriptions)
3. **Relationship Migration** - Updates post-term relationships
4. **Cleanup** - Prepares for old taxonomy removal
5. **Rewrite Rules** - Flushes WordPress rewrite rules

## Workflow

1. **Before Migration:**
   - Run tests to establish baseline
   - Backup your database

2. **During Migration:**
   - Update code to remove qa_bib_cats references
   - Run migration script
   - Run tests to verify functionality

3. **After Migration:**
   - Test admin interface
   - Test frontend templates
   - Verify search functionality
   - Check category cards display correctly

## Troubleshooting

### Common Issues

1. **Tests fail after migration:**
   - Check that all code references to qa_bib_cats are updated
   - Verify taxonomy registration is correct
   - Ensure templates are updated

2. **Migration script errors:**
   - Check WordPress permissions
   - Verify database connectivity
   - Review error logs

3. **Template not loading:**
   - Check file paths
   - Verify template loading logic
   - Clear any caching

### Debug Mode

To enable debug logging, add this to wp-config.php:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## Security Notes

- These scripts should only be run in development/testing environments
- Remove or protect these files in production
- Always backup your database before running migrations
