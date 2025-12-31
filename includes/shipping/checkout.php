<?php

//this does not currently support gutenberg blocks

add_filter('woocommerce_checkout_fields', 'ip_shipping_checkout_fields');
function ip_shipping_checkout_fields($fields)
{
    $fields['billing']['ip_shipping_insurance'] = array(
        'type'        => 'checkbox',
        'label'       => 'Request insurance',
        'required'    => false,
        'class'       => array('form-row-wide'),
        'priority'    => 120,
    );

    return $fields;
}

//this hook fires when the order is being saved
add_action('woocommerce_checkout_update_order_meta', 'ip_shipping_update_order_meta');
function ip_shipping_update_order_meta($order_id)
{
    $value = isset($_POST['ip_shipping_insurance']) ? 'yes' : 'no';

    $order = wc_get_order($order_id);
    $order->update_meta_data('_ip_shipping_insurance', $value);
    $order->save_meta_data();
}

//display the insurance value in the admin area, when inspecting an order
add_action('woocommerce_admin_order_data_after_billing_address', function ($order) {
    $insurance = $order->get_meta('_ip_shipping_insurance');
    $insurance_str = $insurance === 'yes' ? 'Yes' : 'No';

    echo '<p><strong>Use insurance:</strong> ' . $insurance_str . '</p>';
});

//this hook fires whenever the user changes checkout fields
add_action('woocommerce_checkout_update_order_review', function ($post_data) {
    ip_shipping_clear_rates_cache(); //shipping rates cache must be invalidated to force recalculation and ensure prices reflect chosen insurance option

    parse_str($post_data, $data);
    $insurance_val = isset($data['ip_shipping_insurance']) ? 'yes' : 'no';

    //store the insurance value in the customer session so it can be taken into account when calculating shipping rates
    WC()->session->set('ip_shipping_insurance', $insurance_val);
});

add_action('wp_enqueue_scripts', 'ip_shipping_enqueue_checkout_script');
//this frontend script listens for changes in the insurance checkbox and signals WC to perform an AJAX update when it changes
function ip_shipping_enqueue_checkout_script()
{
    if (!is_checkout()) return;

    wp_enqueue_script(
        'ip-shipping-checkout',
        IP_SHIPPING_RATES_PLUGIN_URL . 'assets/js/checkout.js',
        [],
        '1.0.0',
        true
    );
}

//https://gist.github.com/chuckmo/9c2a57f64cf60ee0d9d3
function ip_shipping_clear_rates_cache()
{
    $packages = WC()->cart->get_shipping_packages();

    foreach ($packages as $key => $value) {
        $shipping_session = "shipping_for_package_$key";

        unset(WC()->session->$shipping_session);
    }
}
