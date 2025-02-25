<?php 
require_once plugin_dir_path(dirname(__FILE__)) . 'ajax/bib_save_item_order.php';

function qa_bib_enqueue_admin_scripts($hook) {
    error_log("Current hook: " . $hook);

        wp_enqueue_script('sortable-js', 'https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js', [], null, true);
        wp_enqueue_script('qa-bib-admin-js', plugins_url('assets/js/admin.js', dirname(__FILE__)), ['jquery', 'sortable-js'], null, true);

        // Pass ajaxurl to admin.js
       // Pass the URL to the AJAX handler
       wp_localize_script('qa-bib-admin-js', 'qaBibAjax', [
        'ajaxurl' => plugins_url('ajax/bib_save_item_order.php', dirname(__FILE__)), // Correct path to AJAX file
        'nonce' => wp_create_nonce('qa_bib_nonce'),
    ]);
}
add_action('admin_enqueue_scripts', 'qa_bib_enqueue_admin_scripts');

// Add custom fields to taxonomy edit screen
function qa_bib_cats_custom_fields($term) {
    $icon = get_term_meta($term->term_id, 'qa_bib_icon', true);
    $description = get_term_meta($term->term_id, 'qa_bib_description', true);
    ?>
    <tr class="form-field">
        <th scope="row">
            <label for="qa_bib_icon"><?php esc_html_e('Category Icon', 'text-domain'); ?></label>
        </th>
        <td>
            <input type="text" name="qa_bib_icon" id="qa_bib_icon" value="<?php echo esc_attr($icon); ?>" placeholder="Enter Dashicon class or image URL">
            <p class="description"><?php esc_html_e('Use a Dashicon class (e.g., dashicons-book) or an image URL.', 'text-domain'); ?></p>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row">
            <label for="qa_bib_description"><?php esc_html_e('Category Description', 'text-domain'); ?></label>
        </th>
        <td>
            <textarea name="qa_bib_description" id="qa_bib_description" rows="3"><?php echo esc_textarea($description); ?></textarea>
            <p class="description"><?php esc_html_e('Short description for this category.', 'text-domain'); ?></p>
        </td>
    </tr>
    <?php
}
add_action('qa_bib_cats_edit_form_fields', 'qa_bib_cats_custom_fields', 10, 2);

// Save custom fields
function save_qa_bib_cats_custom_fields($term_id) {
    if (isset($_POST['qa_bib_icon'])) {
        update_term_meta($term_id, 'qa_bib_icon', sanitize_text_field($_POST['qa_bib_icon']));
    }
    if (isset($_POST['qa_bib_description'])) {
        update_term_meta($term_id, 'qa_bib_description', sanitize_textarea_field($_POST['qa_bib_description']));
    }
}
add_action('edited_qa_bib_cats', 'save_qa_bib_cats_custom_fields', 10, 2);


function qa_bib_render_admin_page() {
    ?>
    <div class="wrap" style="direction:rtl; text-align:right">
        <h1><?php esc_html_e('Bibliography Manager', 'text-domain'); ?></h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('qa_bib_admin_save', 'qa_bib_nonce'); ?>

            <h2><?php esc_html_e('Categories', 'text-domain'); ?></h2>
            <?php
            // Fetch categories (qa_bib_cats)
            $categories = get_terms([
                'taxonomy' => 'qa_bib_cats',
                'hide_empty' => false,
            ]);

            if (!empty($categories)) {
                foreach ($categories as $category) {
                    $icon = get_term_meta($category->term_id, 'qa_bib_icon', true);
                    $description = get_term_meta($category->term_id, 'qa_bib_description', true);

                    echo '<div style="margin-bottom: 20px; border: 1px solid #ccc; padding: 10px;">';
                    echo '<h3>' . esc_html($category->name) . '</h3>';

                    // Icon field
                    echo '<p><label for="category_icon_' . esc_attr($category->term_id) . '">' . esc_html__('אייקון:', 'text-domain') . '</label>';
                    echo '<input type="text" name="category_icon[' . esc_attr($category->term_id) . ']" id="category_icon_' . esc_attr($category->term_id) . '" value="' . esc_attr($icon) . '" placeholder="Dashicon or image URL"></p>';

                    // Description field
                    echo '<p><label for="category_description_' . esc_attr($category->term_id) . '">' . esc_html__('תיאור:', 'text-domain') . '</label>';
                    echo '<textarea name="category_description[' . esc_attr($category->term_id) . ']" id="category_description_' . esc_attr($category->term_id) . '" rows="3">' . esc_textarea($description) . '</textarea></p>';

                    // Items for this category
                    echo '<h4>' . esc_html__('פריטים', 'text-domain') . '</h4>';
                    $items = new WP_Query([
                        'post_type' => 'qa_bib_items',
                        'tax_query' => [
                            [
                                'taxonomy' => 'qa_bib_cats',
                                'field' => 'term_id',
                                'terms' => $category->term_id,
                            ],
                        ],
                        'orderby' => 'menu_order',
                        'order' => 'ASC',
                        'posts_per_page' => -1,
                    ]);
                    
                    // Debug: Log full query and results
                    // error_log("Running WP_Query for category: " . $category->name . " (Term ID: " . $category->term_id . ")");
                    // error_log("WP_Query Args: " . print_r($items->query, true));
                    // error_log("WP_Query Results: " . print_r($items->posts, true));
                    
                    if ($items->have_posts()) {
                        while ($items->have_posts()) {
                            $items->the_post();
                            error_log("Found item: " . get_the_title() . " (ID: " . get_the_ID() . ")");
                        }
                    } else {
                        error_log("אין פרטים בקטגוריה: " . $category->name . " (Term ID: " . $category->term_id . ")");
                    }
                    
                    // Debugging: Log query and results
                    error_log("Querying items for category: " . $category->name . " (Term ID: " . $category->term_id . ")");
                    error_log("Query Args: " . print_r([
                        'post_type' => 'qa_bib_items',
                        'taxonomy' => 'qa_bib_cats',
                        'field' => 'term_id',
                        'terms' => $category->term_id,
                    ], true));

                    echo '<ul id="sortable-' . esc_attr($category->slug) . '" class="sortable" style="list-style: none; margin: 0; padding: 0;">';
                    if ($items->have_posts()) {
                        while ($items->have_posts()) {
                            $items->the_post();
                            echo '<li data-id="' . get_the_ID() . '" class="sortable-item" style="padding: 5px; border: 1px solid #ddd; margin-bottom: 5px; background: #f9f9f9;">';
                            echo '<span class="dashicons dashicons-move"></span> ';
                            echo '<input type="text" name="items[' . get_the_ID() . '][title]" value="' . esc_attr(get_the_title()) . '" style="width: 80%;">';
                            echo '</li>';
                        }
                    } else {
                        echo '<li style="padding: 5px; color: #888;">' . esc_html__('No items found in this category.', 'text-domain') . '</li>';
                    }
                    echo '</ul>';

                    wp_reset_postdata();
                    echo '</div>';
                }
            } else {
                echo '<p>' . esc_html__('No categories found.', 'text-domain') . '</p>';
            }
            ?>

            <p>
                <button type="submit" name="save_qa_bib" class="button button-primary"><?php esc_html_e('Save Changes', 'text-domain'); ?></button>
            </p>
        </form>

        <script>
    document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".sortable").forEach(function (el) {
        new Sortable(el, {
            animation: 150,
            ghostClass: "sortable-ghost",
            onEnd: function (evt) {
                let itemOrder = [];
                evt.to.querySelectorAll(".sortable-item").forEach(function (item, index) {
                    itemOrder.push({ id: item.dataset.id, order: index + 1 });
                });

                fetch(qaBibAjax.ajaxurl, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ action: "save_item_order", items: itemOrder }),
                })
                .then(response => {
                    console.log("🚀 Fetch response received:", response);
                    return response.text();
                })
                .then(text => {
                    console.log("📩 Raw AJAX Response:", text); // Log raw response immediately

                    try {
                        let json = JSON.parse(text);
                        console.log("✅ Parsed AJAX Response:", json);
                    } catch (error) {
                        console.error("❌ Invalid JSON response:", text);
                    }
                })
                .catch(error => console.error("AJAX error:", error));
            },
        });
    });
});
        </script>
    </div>
    <?php
}

function qa_bib_admin_menu() {
    add_menu_page(
        __('ניהול פרטי ביבליוגרפיה', 'text-domain'), // Page title
        __('ניהול פרטי ביבליוגרפיה', 'text-domain'),       // Menu title
        'manage_options',                          // Capability (Admin access)
        'qa_bib_manager',                          // Slug (important for URL)
        'qa_bib_render_admin_page',                // Callback function
        'dashicons-list-view',                     // Admin menu icon
        25                                         // Position in menu
    );
}
add_action('admin_menu', 'qa_bib_admin_menu');

function qa_bib_save_admin_settings() {
    if (isset($_POST['qa_bib_nonce']) && check_admin_referer('qa_bib_admin_save', 'qa_bib_nonce')) {
        // Save Category Icons and Descriptions
        if (!empty($_POST['category_icon'])) {
            foreach ($_POST['category_icon'] as $term_id => $icon) {
                update_term_meta($term_id, 'qa_bib_icon', sanitize_text_field($icon));
            }
        }
        if (!empty($_POST['category_description'])) {
            foreach ($_POST['category_description'] as $term_id => $description) {
                update_term_meta($term_id, 'qa_bib_description', sanitize_textarea_field($description));
            }
        }

        // Save Item Titles
        if (!empty($_POST['items'])) {
            foreach ($_POST['items'] as $post_id => $data) {
                wp_update_post([
                    'ID' => intval($post_id),
                    'post_title' => sanitize_text_field($data['title']),
                ]);
            }
        }

        wp_redirect(admin_url('admin.php?page=qa_bib_manager&updated=true'));
        exit;
    }
}
add_action('admin_init', 'qa_bib_save_admin_settings');
