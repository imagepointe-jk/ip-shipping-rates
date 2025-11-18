<?php
//using namespace so we don't have to prefix every single function
//without namespace, functions are defined globally, which can cause unexpected name collisions
namespace IPShippingRates\Settings;

if (!defined('ABSPATH')) exit;


use IPShippingRates\Constants;
use function ImagePointe\Utils\Fields\text_field;
use function ImagePointe\Utils\Fields\checkbox;
use function ImagePointe\Utils\Fields\number_field;

function settings_init()
{
    $main_section_name = Constants::SLUG . '_main_settings_section';
    $ups_section_name = Constants::SLUG . '_ups_settings_section';

    //if no settings for this plugin exist in the wp_options table, create a row for them
    if (!get_option(Constants::WP_OPTION_NAME)) {
        add_option(Constants::WP_OPTION_NAME);
    }

    //MAIN SETTINGS=========================
    add_settings_section(
        id: $main_section_name,
        title: 'Main Settings',
        callback: null,
        page: Constants::SLUG
    );
    add_settings_section(
        id: $ups_section_name,
        title: 'Enabled UPS Services',
        callback: null,
        page: Constants::SLUG
    );

    add_settings_field(
        id: Constants::SLUG . '_proxy_api_url',
        title: 'Proxy API URL',
        callback: __NAMESPACE__ . '\\proxy_api_url_field',
        page: Constants::SLUG,
        section: $main_section_name,
    );
    add_settings_field(
        id: Constants::SLUG . '_universal_price_adjustment',
        title: 'Universal Price Adjustment (%)',
        callback: __NAMESPACE__ . '\\universal_price_adjustment_field',
        page: Constants::SLUG,
        section: $main_section_name,
    );
    add_settings_field(
        id: Constants::SLUG . '_ups_account_number',
        title: 'UPS Account Number (optional)', //required to show negotiated rates; otherwise, retail rates will be shown instead
        callback: __NAMESPACE__ . '\\ups_account_number_field',
        page: Constants::SLUG,
        section: $main_section_name,
    );

    //UPS SERVICES===========================
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
    add_settings_field(
        id: Constants::SLUG . '_ups_surepost_canada_enabled',
        title: 'UPS SurePost (Canada)',
        callback: __NAMESPACE__ . '\\ups_surepost_canada_checkbox',
        page: Constants::SLUG,
        section: $ups_section_name,
    );
    add_settings_field(
        id: Constants::SLUG . '_ups_worldwide_expedited_enabled',
        title: 'UPS Worldwide Expedited',
        callback: __NAMESPACE__ . '\\ups_worldwide_expedited_checkbox',
        page: Constants::SLUG,
        section: $ups_section_name,
    );
    add_settings_field(
        id: Constants::SLUG . '_ups_worldwide_express_plus_enabled',
        title: 'UPS Worldwide Express Plus',
        callback: __NAMESPACE__ . '\\ups_worldwide_express_plus_checkbox',
        page: Constants::SLUG,
        section: $ups_section_name,
    );
    add_settings_field(
        id: Constants::SLUG . '_ups_worldwide_saver_enabled',
        title: 'UPS Worldwide Saver',
        callback: __NAMESPACE__ . '\\ups_worldwide_saver_checkbox',
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

function ups_account_number_field()
{
    text_field(
        db_setting_name: 'ups_account_number',
        db_option_name: Constants::WP_OPTION_NAME
    );
}

function universal_price_adjustment_field()
{
    number_field(
        db_setting_name: 'universal_price_adjustment',
        db_option_name: Constants::WP_OPTION_NAME,
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

function ups_surepost_canada_checkbox()
{
    checkbox(
        db_setting_name: 'ups_surepost_canada_enabled',
        db_option_name: Constants::WP_OPTION_NAME,
        label: ''
    );
}

function ups_worldwide_expedited_checkbox()
{
    checkbox(
        db_setting_name: 'ups_worldwide_expedited_enabled',
        db_option_name: Constants::WP_OPTION_NAME,
        label: ''
    );
}

function ups_worldwide_express_plus_checkbox()
{
    checkbox(
        db_setting_name: 'ups_worldwide_express_plus_enabled',
        db_option_name: Constants::WP_OPTION_NAME,
        label: ''
    );
}

function ups_worldwide_saver_checkbox()
{
    checkbox(
        db_setting_name: 'ups_worldwide_saver_enabled',
        db_option_name: Constants::WP_OPTION_NAME,
        label: ''
    );
}
