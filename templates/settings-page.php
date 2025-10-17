<div class="my-test-class">
    <h1><?php

        use IPShippingRates\Constants;

        echo esc_html(get_admin_page_title()); ?></h1>

    <form method="post" action="options.php">
        <!-- settings_fields renders some hidden fields NECESSARY for functionality and security (not the actual fields we registered) -->
        <?php settings_fields(Constants::WP_OPTION_NAME); ?>
        <!-- do_settings_sections renders the sections we registered and all fields inside of each -->
        <?php do_settings_sections(Constants::SLUG); ?>
        <?php submit_button(); ?>
    </form>
</div>