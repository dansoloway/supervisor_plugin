<?php
// Ensure WordPress is loaded
if (!defined('ABSPATH')) {
    exit;
}

// Register the AJAX handler (ensures it runs only for AJAX)

function save_item_order() {
    // Only allow AJAX requests
    if (!defined('DOING_AJAX') || !DOING_AJAX) {
        error_log("AJAX request failed: Not an AJAX request.");
        wp_send_json_error(['message' => 'Not an AJAX request']);
        exit;
    }

    error_log("✅ AJAX handler called: save_item_order");

    // Ensure JSON response and prevent unexpected output
    header('Content-Type: application/json; charset=utf-8');
    ob_start();

    // Read raw input
    $raw_data = file_get_contents('php://input');
    error_log("📥 Raw Input: " . $raw_data);

    // Decode JSON
    $data = json_decode($raw_data, true);
    error_log("📊 Decoded Data: " . print_r($data, true));

    // If no items, return an error
    if (empty($data['items'])) {
        error_log("❌ Error: No items provided.");
        wp_send_json_error(['message' => 'No items provided.']);
        exit;
    }

    // Process each sortable item
    foreach ($data['items'] as $item) {
        $post_id = intval($item['id']);
        $menu_order = intval($item['order']);

        error_log("🔄 Updating Post ID: $post_id to order: $menu_order");
        update_post_meta($post_id, 'menu_order', $menu_order);
    }

    // ✅ JSON success response
    $response = ['success' => true, 'message' => 'Order saved successfully.'];
    error_log("✅ Response Sent: " . json_encode($response));

    // Ensure output is properly flushed
    ob_end_clean();
    wp_send_json_success($response);
    exit;
}

// Hook into WordPress AJAX system
add_action('wp_ajax_save_item_order', 'save_item_order');