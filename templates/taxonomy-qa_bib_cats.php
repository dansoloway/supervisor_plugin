<?php
/* Template for QA Bibliography Categories */
get_header('supervisor');

// Get current taxonomy term
$term = get_queried_object();

error_log('Loading taxonomy-qa_bib_cats.php template');
?>

<div class="supervisor-home">
    <h1><?php echo esc_html($term->name); ?></h1>
    <p><?php echo esc_html(term_description($term->term_id, 'qa_bib_cats')); ?></p>

    <div class="bib-items-grid">
        <?php
        // Query the qa_bib_items posts in this category
        $args = [
            'post_type' => 'qa_bib_items',
            'tax_query' => [
                [
                    'taxonomy' => 'qa_bib_cats',
                    'field' => 'term_id',
                    'terms' => $term->term_id,
                ],
            ],
            'posts_per_page' => -1, // Show all
        ];
        $query = new WP_Query($args);

        if ($query->have_posts()) :
            while ($query->have_posts()) : $query->the_post();
                ?>
                <div class="bib-item">
                    <h2 class="bib-title"><?php the_title(); ?></h2>
                    <p class="bib-excerpt"><?php the_excerpt(); ?></p>

                    <?php
                    // Get the ACF field "original_link"
                    $original_link = get_field('orignial_link');
                    if ($original_link) :
                    ?>
                        <p class="bib-original-link">
                            <strong>למקור: </strong> 
                            <a href="<?php echo esc_url($original_link); ?>" target="_blank" rel="noopener noreferrer">
                                <?php echo esc_html($original_link); ?>
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endwhile;
            wp_reset_postdata();
        else :
            ?>
            <p><?php esc_html_e('אין פריטים', 'text-domain'); ?></p>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>