<?php
/**
 * Supervisor Navigation Component
 * This file contains the main navigation menu for the supervisor plugin
 */
?>

<div class="supervisor_header_links">
    <a href="<?php echo get_the_permalink(SUPERVISOR_ABOUT); ?>" class="<?php echo is_page(SUPERVISOR_ABOUT) ? 'active' : ''; ?>">אודות</a>
    <a href="<?php echo get_the_permalink(SUPERVISOR_INTRO_TEXT); ?>" class="<?php echo is_page(SUPERVISOR_INTRO_TEXT) ? 'active' : ''; ?>">תחומי פעילות</a>
    <a href="<?php echo get_the_permalink(SUPERVISOR_BIB_CATS); ?>" class="<?php echo is_page(SUPERVISOR_BIB_CATS) ? 'active' : ''; ?>">מפת ידע</a>
    <a href="<?php echo get_the_permalink(SUPERVISOR_UPDATES); ?>" class="<?php echo is_page(SUPERVISOR_UPDATES) ? 'active' : ''; ?>">עדכונים</a>
    <a href="<?php echo get_the_permalink(SUPERVISOR_CONTACT); ?>" class="<?php echo is_page(SUPERVISOR_CONTACT) ? 'active' : ''; ?>">צור קשר</a>
</div>
