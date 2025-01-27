<?php
/* Template Name: Single QA Updates */
get_header('supervisor');

while (have_posts()) : the_post();
    echo '<h1>' . get_the_title() . '</h1>';

    // Display the date from the ACF field
    $raw_date = get_field('qa_updates_date'); // Format: YYYYMMDD
    if ($raw_date) {
        $formatted_date = date_i18n('F Y', strtotime($raw_date));
        echo '<p><strong>' . __('תאריך:', 'text-domain') . '</strong> ' . esc_html($formatted_date) . '</p>';
    }

    // Display taxonomy terms
    $themes = get_the_terms(get_the_ID(), 'qa_themes');
    $tags = get_the_terms(get_the_ID(), 'qa_tags');

    if ($themes || $tags) {
        echo '<div>';
        
        // Display נושאים (Themes)
        echo '<p><strong>' . __('נושאים:', 'text-domain') . '</strong> ';
        if ($themes && !is_wp_error($themes)) {
            $theme_names = array_map(function($theme) {
                return $theme->name; // Extract the name of each term
            }, $themes);
            echo implode(', ', array_map('esc_html', $theme_names));
        } else {
            echo __('ללא נושאים', 'text-domain');
        }
        echo '</p>';

        // Display טגים (Tags)
        echo '<p><strong>' . __('טגים:', 'text-domain') . '</strong> ';
        if ($tags && !is_wp_error($tags)) {
            $tag_names = array_map(function($tag) {
                return $tag->name; // Extract the name of each term
            }, $tags);
            echo implode(', ', array_map('esc_html', $tag_names));
        } else {
            echo __('ללא טגים', 'text-domain');
        }
        echo '</p>';

        echo '</div>';
    }

    // Display the content
    echo '<div>' . get_the_content() . '</div>';

endwhile;

get_footer();