<div class="ajax-search-component">
    <form id="ajax-search-form" dir="rtl">
        <!-- Main Search Section -->
        <div class="search-section">
            <h2 class="search-title">חיפוש חופשי בעדכונים</h2>
            <div class="search-input-container">
                <input type="text" id="search-text" name="search-text" placeholder="חיפוש" class="search-input-field">
                <button type="button" class="search-button" aria-label="חיפוש">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 21L16.514 16.506L21 21ZM19 10.5C19 15.194 15.194 19 10.5 19C5.806 19 2 15.194 2 10.5C2 5.806 5.806 2 10.5 2C15.194 2 19 5.806 19 10.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <h3 class="filter-title">סינון לפי:</h3>
            
            <div class="taxonomy-filters">
                <!-- Key Topics -->
                <div class="taxonomy-filter">
                    <div class="filter-group-title">נושאי מפתח</div>
                    <div class="filter-listbox">
                        <?php
                        $themes = get_terms([
                            'taxonomy' => 'qa_themes',
                            'hide_empty' => false,
                        ]);
                        foreach ($themes as $theme) {
                            if (empty(trim($theme->slug))) {
                                continue;
                            }
                            echo '<label class="checkbox-label">';
                            echo '<input type="checkbox" name="qa_themes[]" value="' . esc_attr($theme->slug) . '">';
                            echo '<span class="checkbox-text">' . esc_html($theme->name) . '</span>';
                            echo '</label>';
                        }
                        ?>
                    </div>
                </div>

                <!-- Areas -->
                <div class="taxonomy-filter">
                    <div class="filter-group-title">תחומים</div>
                    <div class="filter-listbox">
                        <?php
                        $tags = get_terms([
                            'taxonomy' => 'qa_tags',
                            'hide_empty' => false,
                        ]);
                        foreach ($tags as $tag) {
                            if (empty(trim($tag->slug))) {
                                continue;
                            }
                            echo '<label class="checkbox-label">';
                            echo '<input type="checkbox" name="qa_tags[]" value="' . esc_attr($tag->slug) . '">';
                            echo '<span class="checkbox-text">' . esc_html($tag->name) . '</span>';
                            echo '</label>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Button -->
        <button type="button" id="search-submit" class="filter-button">סנן</button>
    </form>
    <div class="ajax-search-results"></div>
</div>