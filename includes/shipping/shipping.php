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
            $this->method_description = __('Custom shipping options (eco, fast, local).');

            $this->enabled            = 'yes';
            $this->title              = __('Custom Shipping');

            //$this->init();
        }

        // function init()
        // {
        //     // Settings
        //     $this->init_form_fields();
        //     $this->init_settings();

        //     // Save settings
        //     add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
        // }

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
            $services = get_ups_services();

            //$package contains the shipping info input by the user.
            $body = build_request_body($package);
            $response = wp_remote_post($api_url, array(
                'headers' => array('Content-Type' => 'application/json'),
                'body' => wp_json_encode($body),
                'timeout' => 10
            ));

            if (is_wp_error($response)) {
                $logger->info('API request failed: ' . $response->get_error_message(), ['source' => 'ip_shipping_debug']);
                return;
            }

            $status_code = wp_remote_retrieve_response_code($response);
            if ($status_code !== 200) {
                $logger->info('API request failed: Status ' . $status_code, ['source' => 'ip_shipping_debug']);
                return;
            }

            $data = json_decode(wp_remote_retrieve_body($response), true);
            for ($i = 0; $i < count($data); $i++) {
                $item = $data[$i];
                if ($item['statusCode'] !== 200) {
                    continue;
                }
                $item_data = $item['data'];
                $rateResponse = $item_data['RateResponse'];
                $ratedShipment = $rateResponse['RatedShipment'];
                $service = $ratedShipment['Service'];
                $totalCharges = $ratedShipment['TotalCharges'];
                $val = $totalCharges['MonetaryValue'];

                $matching_service = null;
                foreach ($services as $s) {
                    if ($s['code'] === $service['Code']) {
                        $matching_service = $s;
                        break;
                    }
                }
                unset($s);

                $this->add_rate(array(
                    'id' => $this->id . $i,
                    'label' => $matching_service ? $matching_service['description'] : 'Unknown Shipping Option',
                    'cost' => $val,
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
                'description' => 'UPS Ground'
            ),
            array(
                'code' => '01',
                'description' => 'UPS Next Day Air'
            ),
            array(
                'code' => '12',
                'description' => 'UPS 3 Day Select'
            ),
            array(
                'code' => '02',
                'description' => 'UPS 2nd Day Air'
            ),
        ];
        return $services;
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
        $services = get_ups_services();

        $body = [];
        foreach ($services as $service) {
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
                        'Package' => array(
                            'PackagingType' => array(
                                'Code' => '02',
                                'Description' => 'Packaging'
                            ),
                            'PackageWeight' => array(
                                'UnitOfMeasurement' => array(
                                    'Code' => 'LBS',
                                    'Description' => 'Pounds'
                                ),
                                'Weight' => '0.02'
                            )
                        ),
                        'PaymentDetails' => array(
                            'ShipmentCharge' => array(
                                'Type' => '01',
                                'BillShipper' => array(
                                    'AccountNumber' => 'Not Yet Set'
                                )
                            )
                        ),
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
}
