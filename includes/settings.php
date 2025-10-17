<?php
if (!defined('ABSPATH')) exit;

//using namespace so we don't have to prefix every single function
//without namespace, functions are defined globally, which can cause unexpected name collisions
namespace IPShippingRates\Settings;

use IPShippingRates\Constants;
use function ImagePointe\Utils\Fields\text_field;

function settings_init()
{
    $section_name = Constants::SLUG . '_settings_section';

    //if no settings for this plugin exist in the wp_options table, create a row for them
    if (!get_option(Constants::WP_OPTION_NAME)) {
        add_option(Constants::WP_OPTION_NAME);
    }

    //a section is registered in admin_init; the fields will be rendered in the template using Settings API
    //the section has to be registered to make it available to Settings API
    add_settings_section(
        // unique id for section
        id: $section_name,
        //this will be rendered as the section title
        title: 'IP Shipping Rates Settings',
        //this callback will render arbitrary markup below the section title
        //namespace must be prepended for WP to find the function in this namespace
        //double backslash because backslash itself is an escape character
        callback: null,
        //what page the section will be on
        page: Constants::SLUG
    );

    //register a field and assign it to the section we just registered
    //the field has to be registered to make it available to Settings API
    add_settings_field(
        //unique id for field
        id: Constants::SLUG . '_custom_text',
        //this will be rendered as the label of the field
        title: 'My Custom Text Field',
        //this callback will render the markup for the field itself (but not the label or the surrounding markup used for layout)
        callback: __NAMESPACE__ . '\\custom_text_field_1',
        //what page the field will be on
        page: Constants::SLUG,
        //what section the field will be in
        section: $section_name,
    );

    register_setting(Constants::WP_OPTION_NAME, Constants::WP_OPTION_NAME);
}
add_action('admin_init', __NAMESPACE__ . '\\settings_init');

function custom_text_field_1()
{
    text_field(
        db_setting_name: 'custom_text',
        db_option_name: Constants::WP_OPTION_NAME
    );
}
