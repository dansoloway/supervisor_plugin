<div class="ajax-search-component">
        <form id="ajax-search-form">
            <div class="search-input">
                <label for="search-text">חפש</label>
                <input type="text" id="search-text" name="search-text" placeholder="חפש...">
            </div>

            <div class="taxonomy-filters">
                <div class="taxonomy-filter">
                    <label for="qa_themes">נושאים</label>
                    <select id="qa_themes" name="qa_themes">
                        <option value="">בחר נושא</option>
                        <?php
                        $themes = get_terms([
                            'taxonomy' => 'qa_themes',
                            'hide_empty' => false,
                        ]);
                        foreach ($themes as $theme) {
                            echo '<option value="' . esc_attr($theme->slug) . '">' . esc_html($theme->name) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="taxonomy-filter">
                    <label for="qa_tags">טגים</label>
                    <select id="qa_tags" name="qa_tags">
                        <option value="">בחר טג</option>
                        <?php
                        $tags = get_terms([
                            'taxonomy' => 'qa_tags',
                            'hide_empty' => false,
                        ]);
                        foreach ($tags as $tag) {
                            echo '<option value="' . esc_attr($tag->slug) . '">' . esc_html($tag->name) . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>

            <button type="button" id="search-submit">חפש</button>
        </form>
        <div class="ajax-search-results"></div>

    </div>