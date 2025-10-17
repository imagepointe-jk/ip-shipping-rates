<?php
if (!defined('ABSPATH')) exit;

use IPShippingRates\Constants;

function ip_shipping_rates_options_page()
{
    add_menu_page(
        Constants::PLUGIN_TITLE,
        Constants::MENU_TITLE, //text in the sidebar
        Constants::CAPABILITY, //permissions
        Constants::SLUG, //slug
        'ip_shipping_rates_settings_page_markup', //callback to render settings page
        'dashicons-admin-multisite', //dashicons found at https://developer.wordpress.org/resource/dashicons/
        100
    );
}
add_action('admin_menu', 'ip_shipping_rates_options_page');
function ip_shipping_rates_settings_page_markup()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    include(IP_SHIPPING_RATES_PLUGIN_DIR . 'templates/settings-page.php');
}
