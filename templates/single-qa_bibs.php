<?php
get_header();

while (have_posts()) : the_post();
    echo '<h1>' . get_the_title() . '</h1>';
    echo '<div>' . get_the_content() . '</div>';
endwhile;

get_footer();