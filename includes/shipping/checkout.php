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

add_action('woocommerce_checkout_update_order_meta', 'ip_shipping_update_order_meta');
function ip_shipping_update_order_meta($order_id)
{
    $value = isset($_POST['ip_shipping_insurance']) ? 'yes' : 'no';

    $order = wc_get_order($order_id);
    $order->update_meta_data('_ip_shipping_insurance', $value);
    $order->save_meta_data();
}

add_action('woocommerce_admin_order_data_after_billing_address', function ($order) {
    $insurance = $order->get_meta('_ip_shipping_insurance');
    $insurance_str = $insurance === 'yes' ? 'Yes' : 'No';

    echo '<p><strong>Use insurance:</strong> ' . $insurance_str . '</p>';
});
