<?php
/**
 *  Empty Cart Template
 *
 * This template can be overridden by copying it to yourtheme/download-manager/checkout-cart/cart-empty.php.
 *
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} ?>

<?php do_action( 'wpdmpp_empty_cart_before' ); ?>

<div class="w3eden">
    <div class='card card-default'>
        <div class='card-body text-danger'>
            <?php echo apply_filters( "wpdmpp_empty_cart_text", __( "No item is added in the cart yet.", "wpdm-premium-packages" ) ); ?>
        </div>
        <div class='card-footer text-right'>
            <a class='btn btn-sm btn-primary' href='<?php echo $settings['continue_shopping_url']; ?>'>
                <?php echo apply_filters( "wpdmpp_cart_continue_shopping_button_label", __( "Continue Shopping", "wpdm-premium-packages" ) ); ?> <i class='fa fa-arrow-right'></i>
            </a>
        </div>
    </div>
</div>

<?php do_action( 'wpdmpp_empty_cart_after' ); ?>
