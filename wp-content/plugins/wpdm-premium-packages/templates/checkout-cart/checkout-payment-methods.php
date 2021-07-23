<?php
/**
 * Template for displaying active Payment Methods in cart checkout page.
 *
 * This template can be overridden by copying it to yourtheme/download-manager/checkout-cart/checkout-payment-methods.php.
 *
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $payment_methods;
?>

<div class="card" id="csp">
    <div class="card-header"><?php echo __("Select Payment Method:", "wpdm-premium-packages"); ?></div>
    <div class="list-group-flush payment-gateway-list">
    <?php
    $settings           = maybe_unserialize(get_option('_wpdmpp_settings'));
    $payment_methods    = apply_filters('payment_method', $payment_methods);
    $payment_methods    = isset($settings['pmorders']) && count($settings['pmorders']) == count($payment_methods) ? $settings['pmorders'] : $payment_methods;
    $index = 0;
    foreach ($payment_methods as $payment_method) {

        $payment_gateway_class = 'WPDMPP\Libs\PaymentMethods\\'.$payment_method;

        if ( class_exists( $payment_gateway_class ) ) {
            if ( isset( $settings[$payment_method]['enabled']) && $settings[$payment_method]['enabled'] == 1) {
                $index++;
                $obj                = new $payment_gateway_class();
                $obj->GatewayName   = isset($obj->GatewayName) ? $obj->GatewayName : $payment_method;
                $logo               = isset($obj->logo) ? $obj->logo : $obj->GatewayName;
                $name               = get_wpdmpp_option($payment_method . '/title', $obj->GatewayName);
                $name               = $name == '' ? $payment_method : $name;
                $name               = strstr($name,"://")?"<img src='$name' alt='{$obj->GatewayName}' />":$name;
                $name               = str_replace("[logo]", $logo, $name);
                $payment_method_lc = strtolower($payment_method);
                $pg_item_class = "payment-gateway-item payment-gateway-{$payment_method_lc} index-$index";
                $pg_item_class = apply_filters("wpdmpp_payment_gateway_item_class", $pg_item_class);
                $row_id = "__PM_{$payment_method}";
                // If you are editing this file, keep the radio input field name same as no, "payment_method"
                echo '<label class="list-group-item mb-0 '.$pg_item_class.'" id="'.$row_id.'"><input class="wpdm-radio mr-3" type="radio" name="payment_method" '.checked($payment_methods[0], $payment_method, false).' value="' . $payment_method . '" > <span class="grateway-name">' . $name . '</span></label>';
            }
        }
    }
    ?>
    </div>
</div>
