<?php
/**
 * Supervisor Navigation Component
 * This file contains the main navigation menu for the supervisor plugin
 */

// Define the navigation menu structure
$supervisor_menu = [
    [
        'title' => 'אודות',
        'url' => get_the_permalink(SUPERVISOR_ABOUT),
        'is_active' => is_page(SUPERVISOR_ABOUT),
        'has_dropdown' => false,
        'submenu' => []
    ],
    [
        'title' => 'תחומי פעילות',
        'url' => get_the_permalink(SUPERVISOR_INTRO_TEXT),
        'is_active' => is_page(SUPERVISOR_INTRO_TEXT),
        'has_dropdown' => false,
        'submenu' => []
    ],
    [
        'title' => 'מפת ידע',
        'url' => get_the_permalink(SUPERVISOR_BIB_CATS),
        'is_active' => is_page(SUPERVISOR_BIB_CATS),
        'has_dropdown' => true,
        'submenu' => [
            [
                'title' => 'מהי מפת הידע',
                'url' => get_the_permalink(SUPERVISOR_BIB_CATS),
                'is_active' => is_page(SUPERVISOR_BIB_CATS)
            ],
            [
                'title' => 'נושאי מפתח',
                'url' => get_the_permalink(SUPERVISOR_BIB_CATS),
                'is_active' => false
            ],
            [
                'title' => 'ארגוני פיקוח',
                'url' => get_the_permalink(SUPERVISOR_ORGS),
                'is_active' => is_page(SUPERVISOR_ORGS)
            ]
        ]
    ],
    [
        'title' => 'עדכונים',
        'url' => get_the_permalink(SUPERVISOR_UPDATES),
        'is_active' => is_page(SUPERVISOR_UPDATES),
        'has_dropdown' => false,
        'submenu' => []
    ],
    [
        'title' => 'צור קשר',
        'url' => get_the_permalink(SUPERVISOR_CONTACT),
        'is_active' => is_page(SUPERVISOR_CONTACT),
        'has_dropdown' => false,
        'submenu' => []
    ]
];

// Function to render navigation items
function render_nav_item($item) {
    $classes = ['nav-item'];
    
    if ($item['is_active']) {
        $classes[] = 'active';
    }
    
    if ($item['has_dropdown']) {
        $classes[] = 'dropdown';
    }
    
    $class_string = implode(' ', $classes);
    
    echo '<a href="' . esc_url($item['url']) . '" class="' . esc_attr($class_string) . '">';
    
    if ($item['has_dropdown']) {
        echo '<span class="dropdown-text">' . esc_html($item['title']) . '</span>';
        echo '<svg class="dropdown-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">';
        echo '<path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
        echo '</svg>';
        
        // Add dropdown menu
        if (!empty($item['submenu'])) {
            echo '<div class="dropdown-menu">';
            foreach ($item['submenu'] as $submenu_item) {
                $submenu_classes = ['submenu-item'];
                if ($submenu_item['is_active']) {
                    $submenu_classes[] = 'active';
                }
                
                echo '<a href="' . esc_url($submenu_item['url']) . '" class="' . implode(' ', $submenu_classes) . '">';
                echo esc_html($submenu_item['title']);
                echo '</a>';
            }
            echo '</div>';
        }
    } else {
        echo esc_html($item['title']);
    }
    
    echo '</a>';
}

// Function to render submenu (for future dropdown functionality)
function render_submenu($submenu_items) {
    if (empty($submenu_items)) {
        return;
    }
    
    echo '<div class="submenu">';
    foreach ($submenu_items as $submenu_item) {
        $submenu_classes = ['submenu-item'];
        if ($submenu_item['is_active']) {
            $submenu_classes[] = 'active';
        }
        
        echo '<a href="' . esc_url($submenu_item['url']) . '" class="' . implode(' ', $submenu_classes) . '">';
        echo esc_html($submenu_item['title']);
        echo '</a>';
    }
    echo '</div>';
}
?>

<nav class="site-nav supervisor_header_links" aria-label="ראשי">
    <?php
    foreach ($supervisor_menu as $menu_item) {
        render_nav_item($menu_item);
    }
    ?>
</nav>

<!-- Submenu container for dropdown functionality -->
<div class="submenu-container" style="display: none;">
    <?php
    foreach ($supervisor_menu as $menu_item) {
        if ($menu_item['has_dropdown'] && !empty($menu_item['submenu'])) {
            echo '<div class="submenu-wrapper" data-parent="' . esc_attr($menu_item['title']) . '">';
            render_submenu($menu_item['submenu']);
            echo '</div>';
        }
    }
    ?>
</div>
