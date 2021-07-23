<?php
/**
 * Cart Template
 *
 * This template can be overridden by copying it to yourtheme/download-manager/checkout-cart/cart.php.
 *
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$settings               = get_option('_wpdmpp_settings');
$currency_sign          = wpdmpp_currency_sign();
$currency_sign_before   = wpdmpp_currency_sign_position() == 'before' ? $currency_sign : '';
$currency_sign_after    = wpdmpp_currency_sign_position() == 'after' ? $currency_sign : '';
$guest_checkout         = ( isset($settings['guest_checkout']) && $settings['guest_checkout'] == 1 ) ? 1 : 0;
$login_required         = ! is_user_logged_in() && $guest_checkout == 0 ? true : false;
$wpdm_template          = new \WPDM\Template();

if ( is_array( $cart_data ) && count( $cart_data ) > 0 ) { ?>

    <div class="w3eden">

        <?php do_action( 'wpdmpp_before_cart' ); ?>

        <!-- Cart Form -->
        <div id="wpdmpp-cart-form">
        <?php include wpdm_tpl_path('checkout-cart/cart-form.php', WPDMPP_TPL_DIR, WPDMPP_TPL_FALLBACK); ?>
        </div>
        <!-- Cart Form end-->

        <?php do_action( 'wpdmpp_after_cart' ); ?>

        <!-- Saved cart -->
        <?php include wpdm_tpl_path('checkout-cart/cart-save.php', WPDMPP_TPL_DIR, WPDMPP_TPL_FALLBACK); ?>
        <!-- Saved cart end -->

        <!-- Cart Checkout-->
        <div id="wpdm-checkout">
            <?php
            if($login_required){
                include wpdm_tpl_path('checkout-cart/checkout-login-register.php', WPDMPP_TPL_DIR, WPDMPP_TPL_FALLBACK);
            } else {
                include wpdm_tpl_path('checkout-cart/checkout.php', WPDMPP_TPL_DIR, WPDMPP_TPL_FALLBACK);
            }
            ?>
        </div>
        <!-- Cart Checkout end-->

        <?php do_action( 'wpdmpp_after_checkout_form' ); ?>

    </div>

    <?php

} else {
    // Cart is empty
    include wpdm_tpl_path('checkout-cart/cart-empty.php', WPDMPP_TPL_DIR, WPDMPP_TPL_FALLBACK);
}
