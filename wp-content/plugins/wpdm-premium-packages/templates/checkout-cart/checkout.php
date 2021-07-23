<?php
/**
 * Template for Checkout Billing Info, Customer Name and Email, and Payment Gateway Options
 *
 * This template can be overridden by copying it to yourtheme/download-manager/checkout-cart/checkout.php.
 *
 * @version     1.0.0
 */

if (!defined('ABSPATH')) die('!');

global $current_user;
$billing = $sbilling = array
(
    'first_name' => '',
    'last_name' => '',
    'company' => '',
    'address_1' => '',
    'address_2' => '',
    'city' => '',
    'postcode' => '',
    'country' => '',
    'state' => '',
    'order_email' => '',
    'phone' => ''
);

if (is_user_logged_in())
    $sbilling = maybe_unserialize(get_user_meta(get_current_user_id(), 'user_billing_shipping', true));

$sbilling = is_array($sbilling) && isset($sbilling['billing']) ? $sbilling['billing'] : array();
$billing = shortcode_atts($billing, $sbilling);

// If email and name is not available from { Edit Profile >> Billing info } get current user email and name
if ($billing['order_email'] == '' && is_user_logged_in()) $billing['order_email'] = $current_user->user_email;
if ($billing['first_name'] == '' && is_user_logged_in()) $billing['first_name'] = $current_user->display_name;

?>
<div id="select-payment-method">
    <form action="" name="payment_form" id="payment_form" method="post">


            <?php
            /*if (get_wpdmpp_option('billing_address') == 1 || wpdmpp_tax_active()) {
                // Ask Billing Address When Checkout
                include wpdm_tpl_path('checkout-cart/checkout-billing-info.php', WPDMPP_TPL_DIR, WPDMPP_TPL_FALLBACK);
            } else {
                // Ask only Name and Email When Checkout
                include wpdm_tpl_path('checkout-cart/checkout-name-email.php', WPDMPP_TPL_DIR, WPDMPP_TPL_FALLBACK);
            }*/
            ?>

            <?php
            // Show active payment methods
            include wpdm_tpl_path('checkout-cart/checkout-payment-methods.php', dirname(dirname(__FILE__))."/", WPDMPP_TPL_FALLBACK);
            ?>


        <?php /* This div with id "billing_form" is required for load billing form asynchroniously after selecting the payment method */ ?>
        <div id="billing_form"></div>

        <div class="card card-default mt-3" id="selected-payment-gateway-action">
            <?php if(get_option('__wpdm_checkout_privacy', 0) == 1){ ?>
                <div class="card-header text-left">
                    <label><input style="margin: 0 0 0 5px !important;" class="wpdm-checkbox" id="checkout-terms-agree" type="checkbox" required="required" value="1" name="__iagree"> <?php echo get_option('__wpdm_checkout_privacy_label'); ?></label>
                    ( <a href="<?php echo get_permalink(get_option('wp_page_for_privacy_policy')); ?>" target="_blank">Read <?php echo get_the_title(get_option('wp_page_for_privacy_policy')); ?></a> )
                </div>
            <?php } ?>
            <div class="card-body text-right">

                <?php if(count($payment_methods) > 0) { ?>

                    <?php if(wpdmpp_tax_active()){ ?>
                        <div class="pull-left hide cart-total-final btn color-green">
                            <?php echo apply_filters("wpdmpp_checkout_footer_tax_label", __('Total Including Tax:', 'wpdm-premium-packages') ); ?>
                            <span class="badge"></span>
                        </div>
                    <?php } ?>
                    <span id="wpdmpp-payment-button">
                        <button id="pay_btn" class="button btn <?php echo get_wpdmpp_option('cobtn_color', 'btn-success'); ?>" type="submit">
                            <?php echo apply_filters("wpdmpp_checkout_pay_button_label", get_wpdmpp_option('cobtn_label', __('Pay Now', 'wpdm-premium-packages')) ); ?>
                        </button>
                            <span id="wpdmpp-custom-payment-button"></span>

                    </span>

                <?php } else { ?>

                    <div class="alert alert-danger m-0"><?php _e('No payment option is available!', 'wpdm-premium-packages'); ?></div>

                <?php } ?>

            </div>
        </div>

    </form>
    <div id="paymentform"></div>
</div>
