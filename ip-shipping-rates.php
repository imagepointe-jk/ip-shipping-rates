<?php
/*
Plugin Name: IP Shipping Rates
Description: Show live shipping rates on the checkout page. Replacement for the previous UPS plugin.
Version: 1.0.0
Contributors: jklope
Author: Josh Klope
*/

if (!defined('WPINC')) {
    die;
}

define('IP_SHIPPING_RATES_PLUGIN_DIR', plugin_dir_path(__FILE__));

include(plugin_dir_path(__FILE__) . 'includes/constants.php');
include(plugin_dir_path(__FILE__) . 'includes/utils/misc.php');
include(plugin_dir_path(__FILE__) . 'includes/utils/fields.php');
include(plugin_dir_path(__FILE__) . 'includes/settings.php');
include(plugin_dir_path(__FILE__) . 'includes/menus.php');
include(plugin_dir_path(__FILE__) . 'includes/shipping/shipping.php');
include(plugin_dir_path(__FILE__) . 'includes/shipping/checkout.php');
