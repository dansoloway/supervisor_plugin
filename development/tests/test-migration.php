<?php
/**
 * Automated Testing for qa_bib_cats to qa_tags Migration
 * 
 * This file tests all functionality to ensure the migration works correctly.
 * Run this after making changes to verify everything works.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    require_once('../../../wp-load.php');
}

class SupervisorMigrationTests {
    
    private $test_results = [];
    private $errors = [];
    private $warnings = [];
    
    public function run_all_tests() {
        echo "<h1>Supervisor Plugin Migration Tests</h1>\n";
        echo "<style>
            .test-pass { color: green; }
            .test-fail { color: red; }
            .test-warning { color: orange; }
            .test-section { margin: 20px 0; padding: 10px; border: 1px solid #ccc; }
            .test-details { margin-left: 20px; }
        </style>\n";
        
        $this->test_taxonomy_registration();
        $this->test_admin_functionality();
        $this->test_template_functionality();
        $this->test_search_functionality();
        $this->test_icon_functionality();
        $this->test_ajax_functionality();
        $this->test_data_integrity();
        
        $this->print_summary();
    }
    
    private function test_taxonomy_registration() {
        echo "<div class='test-section'>\n";
        echo "<h2>1. Taxonomy Registration Tests</h2>\n";
        
        // Test qa_tags exists and is properly configured
        $this->test_step("qa_tags taxonomy exists", function() {
            $taxonomy = get_taxonomy('qa_tags');
            return $taxonomy !== false;
        });
        
        $this->test_step("qa_tags is available for qa_bib_items", function() {
            $taxonomy = get_taxonomy('qa_tags');
            return in_array('qa_bib_items', $taxonomy->object_type);
        });
        
        $this->test_step("qa_tags is available for qa_orgs", function() {
            $taxonomy = get_taxonomy('qa_tags');
            return in_array('qa_orgs', $taxonomy->object_type);
        });
        
        $this->test_step("qa_tags is available for qa_updates", function() {
            $taxonomy = get_taxonomy('qa_tags');
            return in_array('qa_updates', $taxonomy->object_type);
        });
        
        // Test qa_bib_cats is removed
        $this->test_step("qa_bib_cats taxonomy is removed", function() {
            $taxonomy = get_taxonomy('qa_bib_cats');
            return $taxonomy === false;
        });
        
        echo "</div>\n";
    }
    
    private function test_admin_functionality() {
        echo "<div class='test-section'>\n";
        echo "<h2>2. Admin Functionality Tests</h2>\n";
        
        // Test admin page exists
        $this->test_step("Bibliography admin page function exists", function() {
            return function_exists('qa_bib_render_admin_page');
        });
        
        // Test admin menu is registered
        $this->test_step("Admin menu is registered", function() {
            global $menu;
            if (!is_array($menu)) {
                return false;
            }
            $menu_slugs = array_column($menu, 2);
            return in_array('qa_bib_manager', $menu_slugs);
        });
        
        // Test custom fields for qa_tags
        $this->test_step("Custom fields hooks for qa_tags exist", function() {
            global $wp_filter;
            return isset($wp_filter['qa_tags_edit_form_fields']) || 
                   isset($wp_filter['qa_tags_add_form_fields']);
        });
        
        echo "</div>\n";
    }
    
    private function test_template_functionality() {
        echo "<div class='test-section'>\n";
        echo "<h2>3. Template Functionality Tests</h2>\n";
        
        // Test supervisor-bib_cats.php template
        $this->test_step("supervisor-bib_cats.php template exists", function() {
            return file_exists(plugin_dir_path(__FILE__) . '../templates/supervisor-bib_cats.php');
        });
        
        // Test taxonomy template exists
        $this->test_step("taxonomy-qa_tags.php template exists", function() {
            return file_exists(plugin_dir_path(__FILE__) . '../templates/taxonomy-qa_tags.php');
        });
        
        // Test template loading function
        $this->test_step("Template loading function works", function() {
            return function_exists('supervisor_load_template');
        });
        
        echo "</div>\n";
    }
    
    private function test_search_functionality() {
        echo "<div class='test-section'>\n";
        echo "<h2>4. Search Functionality Tests</h2>\n";
        
        // Test search results template
        $this->test_step("Search results template exists", function() {
            return file_exists(plugin_dir_path(__FILE__) . '../templates/supervisor-search-results.php');
        });
        
        // Test search handler
        $this->test_step("AJAX search handler exists", function() {
            return file_exists(plugin_dir_path(__FILE__) . '../ajax/search_handler.php');
        });
        
        // Test search form
        $this->test_step("Search form template exists", function() {
            return file_exists(plugin_dir_path(__FILE__) . '../inc/search.php');
        });
        
        echo "</div>\n";
    }
    
    private function test_icon_functionality() {
        echo "<div class='test-section'>\n";
        echo "<h2>5. Icon Functionality Tests</h2>\n";
        
        // Test icon helper functions
        $this->test_step("get_term_fa_icon function exists", function() {
            return function_exists('get_term_fa_icon');
        });
        
        $this->test_step("get_term_fa_icon_by_term function exists", function() {
            return function_exists('get_term_fa_icon_by_term');
        });
        
        // Test icon field hooks
        $this->test_step("Icon field hooks for qa_tags exist", function() {
            global $wp_filter;
            return isset($wp_filter['qa_tags_edit_form_fields']) || 
                   isset($wp_filter['qa_tags_add_form_fields']);
        });
        
        echo "</div>\n";
    }
    
    private function test_ajax_functionality() {
        echo "<div class='test-section'>\n";
        echo "<h2>6. AJAX Functionality Tests</h2>\n";
        
        // Test AJAX handlers
        $this->test_step("Item order AJAX handler exists", function() {
            return file_exists(plugin_dir_path(__FILE__) . '../ajax/bib_save_item_order.php');
        });
        
        $this->test_step("save_item_order function exists", function() {
            return function_exists('save_item_order');
        });
        
        echo "</div>\n";
    }
    
    private function test_data_integrity() {
        echo "<div class='test-section'>\n";
        echo "<h2>7. Data Integrity Tests</h2>\n";
        
        // Test qa_tags has terms
        $this->test_step("qa_tags has terms", function() {
            $terms = get_terms(['taxonomy' => 'qa_tags', 'hide_empty' => false]);
            return !empty($terms);
        });
        
        // Test qa_bib_items exist
        $this->test_step("qa_bib_items posts exist", function() {
            $posts = get_posts(['post_type' => 'qa_bib_items', 'numberposts' => 1]);
            return !empty($posts);
        });
        
        // Test qa_orgs exist
        $this->test_step("qa_orgs posts exist", function() {
            $posts = get_posts(['post_type' => 'qa_orgs', 'numberposts' => 1]);
            return !empty($posts);
        });
        
        // Test qa_updates exist
        $this->test_step("qa_updates posts exist", function() {
            $posts = get_posts(['post_type' => 'qa_updates', 'numberposts' => 1]);
            return !empty($posts);
        });
        
        echo "</div>\n";
    }
    
    private function test_step($description, $test_function) {
        echo "<div class='test-details'>\n";
        echo "<strong>$description:</strong> ";
        
        try {
            $result = $test_function();
            if ($result) {
                echo "<span class='test-pass'>✓ PASS</span>\n";
                $this->test_results[] = ['test' => $description, 'status' => 'pass'];
            } else {
                echo "<span class='test-fail'>✗ FAIL</span>\n";
                $this->test_results[] = ['test' => $description, 'status' => 'fail'];
                $this->errors[] = $description;
            }
        } catch (Exception $e) {
            echo "<span class='test-fail'>✗ ERROR: " . $e->getMessage() . "</span>\n";
            $this->test_results[] = ['test' => $description, 'status' => 'error'];
            $this->errors[] = $description . " - " . $e->getMessage();
        }
        
        echo "</div>\n";
    }
    
    private function print_summary() {
        echo "<div class='test-section'>\n";
        echo "<h2>Test Summary</h2>\n";
        
        $total_tests = count($this->test_results);
        $passed_tests = count(array_filter($this->test_results, function($result) {
            return $result['status'] === 'pass';
        }));
        $failed_tests = count(array_filter($this->test_results, function($result) {
            return $result['status'] === 'fail' || $result['status'] === 'error';
        }));
        
        echo "<p><strong>Total Tests:</strong> $total_tests</p>\n";
        echo "<p><strong>Passed:</strong> <span class='test-pass'>$passed_tests</span></p>\n";
        echo "<p><strong>Failed:</strong> <span class='test-fail'>$failed_tests</span></p>\n";
        
        if (!empty($this->errors)) {
            echo "<h3>Errors Found:</h3>\n";
            echo "<ul>\n";
            foreach ($this->errors as $error) {
                echo "<li class='test-fail'>$error</li>\n";
            }
            echo "</ul>\n";
        }
        
        if ($failed_tests === 0) {
            echo "<h3 class='test-pass'>🎉 All tests passed! Migration appears successful.</h3>\n";
        } else {
            echo "<h3 class='test-fail'>⚠️ Some tests failed. Please review the errors above.</h3>\n";
        }
        
        echo "</div>\n";
    }
}

// Run tests if this file is accessed directly
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $tests = new SupervisorMigrationTests();
    $tests->run_all_tests();
}
