<?php

use IPShippingRates\Constants;

if (!defined('ABSPATH')) exit;

use function ImagePointe\Utils\Misc\try_get_option_value;

add_action('woocommerce_shipping_init', 'ip_shipping_method_init');
function ip_shipping_method_init()
{
    class WC_Shipping_Custom_Option extends WC_Shipping_Method
    {
        public function __construct()
        {
            $this->id                 = 'custom_option'; // Unique ID
            $this->method_title       = __('Custom Option');
            $this->method_description = __('Custom shipping options.');

            $this->enabled            = 'yes';
            $this->title              = __('Custom Shipping');
        }

        public function calculate_shipping($package = array())
        {
            //Outputs the package array into WooCommerce -> Status -> Logs if necessary, using the WooCommerce Logger
            $logger = wc_get_logger();
            // $logger->info(print_r($package, true), ['source' => 'ip_shipping_debug']);

            $proxy_api_url = try_get_option_value('proxy_api_url', Constants::WP_OPTION_NAME);
            if (!$proxy_api_url) {
                $logger->warning('The proxy API URL is empty.', ['source' => 'ip_shipping_debug']);
                return;
            }

            $api_url = $proxy_api_url . '/api/shipping/ups/rate/batch';

            //$package contains the shipping info input by the user.
            $body = build_request_body($package);
            $response = wp_remote_post($api_url, array(
                'headers' => array('Content-Type' => 'application/json'),
                'body' => wp_json_encode($body),
                'timeout' => 10
            ));

            if (is_wp_error($response)) {
                $logger->warning('API request failed during wp_remote_post(): ' . $response->get_error_message(), ['source' => 'ip_shipping_debug']);
                return;
            }

            $status_code = wp_remote_retrieve_response_code($response);
            $data = json_decode(wp_remote_retrieve_body($response), true);
            if ($status_code !== 200) {
                $message = isset($data['message']) ? $data['message'] : 'No message provided';
                $logger->warning('API request failed with a status of ' . $status_code . '. Message: ' . $message, ['source' => 'ip_shipping_debug']);
                return;
            }

            $universal_price_adjustment = try_get_option_value('universal_price_adjustment', Constants::WP_OPTION_NAME);
            if (!$universal_price_adjustment) $universal_price_adjustment = 0;
            for ($i = 0; $i < count($data); $i++) {
                //pull data from item
                $item = $data[$i];
                $service = $item['service'];

                //log info if this service was not able to retrieve a rate
                if ($item['statusCode'] !== 200) {
                    $message = isset($item['message']) ? $item['message'] : 'No message provided';
                    $logger->info('Failed to retrieve a shipping rate for service "' .
                        $service['Description'] . '". Status code: ' . $item['statusCode'] . '. Message: ' . $message . '.', ['source' => 'ip_shipping_debug']);
                    continue;
                }

                $item_data = $item['data'];
                $rateResponse = $item_data['RateResponse'];
                $ratedShipment = $rateResponse['RatedShipment'];
                $totalCharges = $ratedShipment['TotalCharges'];
                $val = $totalCharges['MonetaryValue'];
                $adjusted_val = adjust_price($val, $universal_price_adjustment);

                $this->add_rate(array(
                    'id' => $this->id . $i,
                    'label' => $service['Description'],
                    'cost' => $adjusted_val,
                ));
            }
        }
    }
    add_filter('woocommerce_shipping_methods', 'ip_add_shipping_method');
    function ip_add_shipping_method($methods)
    {
        $methods['custom_option'] = 'WC_Shipping_Custom_Option';
        return $methods;
    }

    function get_ups_services()
    {
        $services = [
            array(
                'code' => '03',
                'description' => 'UPS Ground',
                'enabled' => try_get_option_value('ups_ground_enabled', Constants::WP_OPTION_NAME)
            ),
            array(
                'code' => '01',
                'description' => 'UPS Next Day Air',
                'enabled' => try_get_option_value('ups_next_day_air_enabled', Constants::WP_OPTION_NAME)
            ),
            array(
                'code' => '12',
                'description' => 'UPS 3 Day Select',
                'enabled' => try_get_option_value('ups_3_day_select_enabled', Constants::WP_OPTION_NAME)
            ),
            array(
                'code' => '02',
                'description' => 'UPS 2nd Day Air',
                'enabled' => try_get_option_value('ups_2nd_day_air_enabled', Constants::WP_OPTION_NAME)
            ),
            array(
                'code' => '11',
                'description' => 'UPS Standard',
                'enabled' => try_get_option_value('ups_standard_enabled', Constants::WP_OPTION_NAME)
            ),
            array(
                'code' => '07',
                'description' => 'UPS Worldwide Express',
                'enabled' => try_get_option_value('ups_worldwide_express_enabled', Constants::WP_OPTION_NAME)
            ),
            array(
                'code' => '93',
                'description' => 'UPS SurePost (Canada)',
                'enabled' => try_get_option_value('ups_surepost_canada_enabled', Constants::WP_OPTION_NAME)
            ),
            array(
                'code' => '08',
                'description' => 'UPS Worldwide Expedited',
                'enabled' => try_get_option_value('ups_worldwide_expedited_enabled', Constants::WP_OPTION_NAME)
            ),
            array(
                'code' => '54',
                'description' => 'UPS Worldwide Express Plus',
                'enabled' => try_get_option_value('ups_worldwide_express_plus_enabled', Constants::WP_OPTION_NAME)
            ),
            array(
                'code' => '65',
                'description' => 'UPS Worldwide Saver',
                'enabled' => try_get_option_value('ups_worldwide_saver_enabled', Constants::WP_OPTION_NAME)
            ),
        ];
        return $services;
    }

    function adjust_price($initial_val, $adjustment_val)
    {
        if ($adjustment_val <= -100) return 0;
        return round($initial_val + ($initial_val * $adjustment_val / 100), 2);
    }

    function build_request_body($package)
    {
        $destination = $package['destination'];
        $address = $destination['address'];
        $address2 = isset($destination['address2']) ? $destination['address2'] : '';
        $city = $destination['city'];
        $state = $destination['state'];
        $country = $destination['country'];
        $postcode = $destination['postcode'];
        $enabled_services = array_filter(get_ups_services(), function ($s) {
            return !!$s['enabled'];
        });

        $body = [];
        foreach ($enabled_services as $service) {
            $body[] =  array(
                'RateRequest' => array(
                    'Request' => array(
                        'RequestOption' => 'Rate'
                    ),
                    'Shipment' => array(
                        'Shipper' => array(
                            'Name' => 'Image Pointe',
                            'ShipperNumber' => 'Not Yet Set',
                            'Address' => array(
                                'AddressLine' => ['1224 La Porte Rd'],
                                'City' => 'Waterloo',
                                'StateProvinceCode' => 'IA',
                                'PostalCode' => '50702',
                                'CountryCode' => 'US'
                            ),
                        ),
                        'ShipFrom' => array(
                            'Name' => 'Image Pointe',
                            'Address' => array(
                                'AddressLine' => ['2795 Airline Circle'],
                                'City' => 'Waterloo',
                                'StateProvinceCode' => 'IA',
                                'PostalCode' => '50703',
                                'CountryCode' => 'US'
                            ),
                        ),
                        'ShipTo' => array(
                            'Name' => '',
                            'Address' => array(
                                'AddressLine' => [$address, $address2],
                                'City' => $city,
                                'StateProvinceCode' => $state,
                                'PostalCode' => $postcode,
                                'CountryCode' => $country
                            ),
                        ),
                        'NumOfPieces' => '1',
                        'Package' => generate_packages($package),
                        'Service' => array(
                            'Code' => $service['code'],
                            'Description' => $service['description']
                        )
                    )
                ),
            );
        }

        return $body;
    }

    function generate_packages($package)
    {
        $total_weight = 0;
        foreach ($package['contents'] as $item) {
            $product = $item['data'];
            $weight = floatval($product->get_weight());
            $quantity = $item['quantity'];
            $total_weight += $weight * $quantity;
        }

        $weight_per_package = 45;
        $total_packages = ceil($total_weight / $weight_per_package);
        $packages = [];
        $remaining_weight = $total_weight;
        for ($i = 0; $i < $total_packages; $i++) {
            $this_weight = $remaining_weight > $weight_per_package ? $weight_per_package : $remaining_weight;
            $packages[] = array(
                'PackagingType' => array(
                    'Code' => '02',
                    'Description' => 'Packaging'
                ),
                'PackageWeight' => array(
                    'UnitOfMeasurement' => array(
                        'Code' => 'LBS',
                        'Description' => 'Pounds'
                    ),
                    'Weight' => "$this_weight"
                )
            );
            $remaining_weight -= $this_weight;
        }
        return $packages;
    }
}
