<div class="ajax-search-component">
    <form id="ajax-search-form">
        <div class="search-input">
            <label for="search-text">חפש</label>
            <input type="text" id="search-text" name="search-text" placeholder="חפש...">
        </div>

        <div class="taxonomy-filters">
            <div class="taxonomy-filter">
                <div class="filter-title">
                    <label>נושאים</label>
                </div>
                <div class="checkbox-list">
                    <?php
                    $themes = get_terms([
                        'taxonomy' => 'qa_themes',
                        'hide_empty' => false,
                    ]);
                    foreach ($themes as $theme) {
                        // Skip terms with empty slugs
                        if (empty(trim($theme->slug))) {
                            continue;
                        }
                        echo '<label class="checkbox-label">';
                        echo '<input type="checkbox" name="qa_themes[]" value="' . esc_attr($theme->slug) . '">';
                        echo esc_html($theme->name);
                        echo '</label>';
                    }
                    ?>
                </div>
            </div>

            <div class="taxonomy-filter">
                <div class="filter-title">
                    <label>טגים</label>
                </div>
                
                <div class="checkbox-list">
                    <?php
                    $tags = get_terms([
                        'taxonomy' => 'qa_tags',
                        'hide_empty' => false,
                    ]);
                    foreach ($tags as $tag) {
                        // Skip terms with empty slugs
                        if (empty(trim($tag->slug))) {
                            continue;
                        }
                        echo '<label class="checkbox-label">';
                        echo '<input type="checkbox" name="qa_tags[]" value="' . esc_attr($tag->slug) . '">';
                        echo esc_html($tag->name);
                        echo '</label>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <button type="button" id="search-submit">חפש</button>
    </form>
    <div class="ajax-search-results"></div>
</div>