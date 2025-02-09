<?php
/* Template Name: Supervisor Content */
get_header('supervisor'); ?>

<div class="supervisor-home">
ASD
    <!-- Text Search Bar -->
    <div class="supervisor-search">
        <input type="text" placeholder="חפש..." class="supervisor-search-bar" />
    </div>

    <div class="supervisor-content">

        <div>
            <?php echo the_content();  ?>
        </div>

        <!-- Vertical Buttons -->
        <div class="vertical-buttons">
            <button class="vertical-button" id="button-1">לחצן 1</button>
            <button class="vertical-button" id="button-2">לחצן 2</button>
            <button class="vertical-button" id="button-3">לחצן 3</button>
        </div>

    </div>

</div>

<?php
get_footer();
?>