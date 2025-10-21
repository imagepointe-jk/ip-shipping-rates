<?php
//using namespace so we don't have to prefix every single function
//without namespace, functions are defined globally, which can cause unexpected name collisions
namespace IPShippingRates\Settings;

if (!defined('ABSPATH')) exit;


use IPShippingRates\Constants;
use function ImagePointe\Utils\Fields\text_field;
use function ImagePointe\Utils\Fields\checkbox;

function settings_init()
{
    $main_section_name = Constants::SLUG . '_main_settings_section';
    $ups_section_name = Constants::SLUG . '_ups_settings_section';

    //if no settings for this plugin exist in the wp_options table, create a row for them
    if (!get_option(Constants::WP_OPTION_NAME)) {
        add_option(Constants::WP_OPTION_NAME);
    }

    //a section is registered in admin_init; the fields will be rendered in the template using Settings API
    //the section has to be registered to make it available to Settings API
    add_settings_section(
        // unique id for section
        id: $main_section_name,
        //this will be rendered as the section title
        title: 'Main Settings',
        //this callback will render arbitrary markup below the section title
        //namespace must be prepended for WP to find the function in this namespace
        //double backslash because backslash itself is an escape character
        callback: null,
        //what page the section will be on
        page: Constants::SLUG
    );
    add_settings_section(
        // unique id for section
        id: $ups_section_name,
        //this will be rendered as the section title
        title: 'Enabled UPS Services',
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
        id: Constants::SLUG . '_proxy_api_url',
        //this will be rendered as the label of the field
        title: 'Proxy API URL',
        //this callback will render the markup for the field itself (but not the label or the surrounding markup used for layout)
        callback: __NAMESPACE__ . '\\proxy_api_url_field',
        //what page the field will be on
        page: Constants::SLUG,
        //what section the field will be in
        section: $main_section_name,
    );

    add_settings_field(
        id: Constants::SLUG . '_ups_ground_enabled',
        title: 'UPS Ground',
        callback: __NAMESPACE__ . '\\ups_ground_checkbox',
        page: Constants::SLUG,
        section: $ups_section_name,
    );
    add_settings_field(
        id: Constants::SLUG . '_ups_3_day_select_enabled',
        title: 'UPS 3 Day Select',
        callback: __NAMESPACE__ . '\\ups_3_day_select_checkbox',
        page: Constants::SLUG,
        section: $ups_section_name,
    );
    add_settings_field(
        id: Constants::SLUG . '_ups_2nd_day_air_enabled',
        title: 'UPS 2nd Day Air',
        callback: __NAMESPACE__ . '\\ups_2nd_day_air_checkbox',
        page: Constants::SLUG,
        section: $ups_section_name,
    );
    add_settings_field(
        id: Constants::SLUG . '_ups_next_day_air_enabled',
        title: 'UPS Next Day Air',
        callback: __NAMESPACE__ . '\\ups_next_day_air_checkbox',
        page: Constants::SLUG,
        section: $ups_section_name,
    );
    add_settings_field(
        id: Constants::SLUG . '_ups_standard_enabled',
        title: 'UPS Standard',
        callback: __NAMESPACE__ . '\\ups_standard_checkbox',
        page: Constants::SLUG,
        section: $ups_section_name,
    );
    add_settings_field(
        id: Constants::SLUG . '_ups_worldwide_express_enabled',
        title: 'UPS Worldwide Express',
        callback: __NAMESPACE__ . '\\ups_worldwide_express_checkbox',
        page: Constants::SLUG,
        section: $ups_section_name,
    );

    register_setting(Constants::WP_OPTION_NAME, Constants::WP_OPTION_NAME);
}
add_action('admin_init', __NAMESPACE__ . '\\settings_init');

function proxy_api_url_field()
{
    text_field(
        db_setting_name: 'proxy_api_url',
        db_option_name: Constants::WP_OPTION_NAME
    );
}

function ups_ground_checkbox()
{
    checkbox(
        db_setting_name: 'ups_ground_enabled',
        db_option_name: Constants::WP_OPTION_NAME,
        label: ''
    );
}

function ups_3_day_select_checkbox()
{
    checkbox(
        db_setting_name: 'ups_3_day_select_enabled',
        db_option_name: Constants::WP_OPTION_NAME,
        label: ''
    );
}

function ups_2nd_day_air_checkbox()
{
    checkbox(
        db_setting_name: 'ups_2nd_day_air_enabled',
        db_option_name: Constants::WP_OPTION_NAME,
        label: ''
    );
}

function ups_next_day_air_checkbox()
{
    checkbox(
        db_setting_name: 'ups_next_day_air_enabled',
        db_option_name: Constants::WP_OPTION_NAME,
        label: ''
    );
}

function ups_standard_checkbox()
{
    checkbox(
        db_setting_name: 'ups_standard_enabled',
        db_option_name: Constants::WP_OPTION_NAME,
        label: ''
    );
}

function ups_worldwide_express_checkbox()
{
    checkbox(
        db_setting_name: 'ups_worldwide_express_enabled',
        db_option_name: Constants::WP_OPTION_NAME,
        label: ''
    );
}
