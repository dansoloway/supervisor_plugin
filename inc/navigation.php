<?php
/**
 * Supervisor Navigation Component
 * This file contains the main navigation menu for the supervisor plugin
 */

// Define the navigation menu structure
$supervisor_menu = [
    [
        'title' => 'בית',
        'url' => get_the_permalink(SUPERVISOR_HOME),
        'is_active' => is_page(SUPERVISOR_HOME),
        'has_dropdown' => false,
        'submenu' => [],
        'is_home_icon' => true
    ],
    [
        'title' => 'אודות',
        'url' => get_the_permalink(SUPERVISOR_ABOUT),
        'is_active' => is_page(SUPERVISOR_ABOUT),
        'has_dropdown' => false,
        'submenu' => []
    ],
    [
        'title' => 'תחומי פעילות',
        'url' => get_the_permalink(SUPERVISOR_ACTIVITIES),
        'is_active' => is_page(SUPERVISOR_ACTIVITIES),
        'has_dropdown' => false,
        'submenu' => []
    ],
    [
        'title' => 'מפת הידע',
        'url' => '#', // Will be handled by JavaScript
        'is_active' => is_page(SUPERVISOR_BIB_CATS) || is_page(SUPERVISOR_KNOWLEDGE_MAP) || is_tax('qa_tags'),
        'has_dropdown' => true,
        'submenu' => [
            [
                'title' => 'מהי מפת הידע',
                'url' => get_the_permalink(SUPERVISOR_KNOWLEDGE_MAP),
                'is_active' => is_page(SUPERVISOR_KNOWLEDGE_MAP)
            ],
            [
                'title' => 'נושאי מפתח',
                'url' => get_the_permalink(SUPERVISOR_BIB_CATS),
                'is_active' => is_page(SUPERVISOR_BIB_CATS)
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
    
    if (!empty($item['is_home_icon'])) {
        $classes[] = 'nav-item-home';
    }
    
    $class_string = implode(' ', $classes);
    
    // All items are now anchor tags for consistency
    $style = $item['has_dropdown'] ? ' style="border-radius: 0 !important;"' : '';
    $aria_label = !empty($item['is_home_icon']) ? ' aria-label="בית"' : '';
    echo '<a href="' . esc_url($item['url']) . '" class="' . esc_attr($class_string) . '"' . $style . $aria_label . '>';
    
    if (!empty($item['is_home_icon'])) {
        $house_icon_url = plugin_dir_url(dirname(__FILE__)) . 'assets/img/house2.svg';
        echo '<span class="nav-icon nav-icon-home" aria-hidden="true">';
        echo '<img src="' . esc_url($house_icon_url) . '" alt="" width="24" height="24" class="nav-icon-home-img">';
        echo '</span>';
    } else {
        echo '<span class="nav-text">' . esc_html($item['title']) . '</span>';
    }
    
    // Add dropdown icon if needed
    if ($item['has_dropdown']) {
        echo '<svg class="dropdown-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">';
        echo '<path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>';
        echo '</svg>';
    }
    
    echo '</a>';
    
    // Add dropdown menu as sibling element
    if ($item['has_dropdown'] && !empty($item['submenu'])) {
        echo '<div class="dropdown-menu" data-parent="' . esc_attr($item['title']) . '">';
        foreach ($item['submenu'] as $submenu_item) {
            $submenu_classes = ['submenu-item'];
            if ($submenu_item['is_active']) {
                $submenu_classes[] = 'active';
            }
            
            echo '<a href="' . esc_url($submenu_item['url']) . '" class="' . implode(' ', $submenu_classes) . '" style="border-radius: 0 !important; border: none !important; border-bottom: none !important; border-top: none !important; border-left: none !important; border-right: none !important; font-family: var(--sv-font-secondary) !important; font-weight: 500 !important;">';
            echo esc_html($submenu_item['title']);
            echo '</a>';
        }
        echo '</div>';
    }
}
?>

<div class="nav-wrapper">
    <!-- Desktop Navigation Menu -->
    <nav class="site-nav supervisor_header_links desktop-menu" aria-label="ראשי">
        <?php
        foreach ($supervisor_menu as $menu_item) {
            render_nav_item($menu_item);
        }
        ?>
    </nav>

    <!-- Mobile Navigation Menu -->
    <button class="mobile-menu-toggle" aria-label="תפריט" aria-expanded="false">
        <span class="hamburger-icon">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </span>
    </button>

    <nav class="site-nav supervisor_header_links mobile-menu" aria-label="ראשי">
        <!-- Close button at top of mobile menu -->
        <button class="mobile-menu-close" aria-label="סגור תפריט" aria-expanded="false">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        
        <?php
        foreach ($supervisor_menu as $menu_item) {
            render_nav_item($menu_item);
        }
        ?>
    </nav>
</div>
