<?php
/**
 * Supervisor Navigation Component
 * This file contains the main navigation menu for the supervisor plugin
 */
?>

<div class="supervisor_header_links">
    <a href="<?php echo get_the_permalink(SUPERVISOR_ABOUT); ?>" class="nav-item <?php echo is_page(SUPERVISOR_ABOUT) ? 'active' : ''; ?>">אודות</a>
    <a href="<?php echo get_the_permalink(SUPERVISOR_INTRO_TEXT); ?>" class="nav-item <?php echo is_page(SUPERVISOR_INTRO_TEXT) ? 'active' : ''; ?>">תחומי פעילות</a>
    <a href="<?php echo get_the_permalink(SUPERVISOR_BIB_CATS); ?>" class="nav-item dropdown <?php echo is_page(SUPERVISOR_BIB_CATS) ? 'active' : ''; ?>">
        <span class="dropdown-text">מפת ידע</span>
        <svg class="dropdown-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>
    <a href="<?php echo get_the_permalink(SUPERVISOR_UPDATES); ?>" class="nav-item <?php echo is_page(SUPERVISOR_UPDATES) ? 'active' : ''; ?>">עדכונים</a>
    <a href="<?php echo get_the_permalink(SUPERVISOR_CONTACT); ?>" class="nav-item <?php echo is_page(SUPERVISOR_CONTACT) ? 'active' : ''; ?>">צור קשר</a>
</div>
