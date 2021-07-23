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
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class='panel panel-default panel-empty-cart'>
                <div class='panel-body text-center' style="padding: 50px;letter-spacing: 1px">

                    <div>
                        <svg style="width: 64px;margin-bottom: 20px;" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><defs><style>.cls-1{fill:none;stroke:#4a8eff;stroke-linejoin:round;stroke-width:1px;}</style></defs><title/><g data-name="168-Shopping Bag" id="_168-Shopping_Bag"><circle class="cls-1" cx="19" cy="21" r="5"/><line class="cls-1" x1="22" x2="29" y1="24" y2="31"/><polyline class="cls-1" points="15 31 3 31 3 7 29 7 29 15"/><path class="cls-1" d="M10,12V2a1,1,0,0,1,1-1h5"/><path class="cls-1" d="M22,12V2a1,1,0,0,0-1-1H16"/><line class="cls-1" x1="7" x2="13" y1="12" y2="12"/><line class="cls-1" x1="19" x2="25" y1="12" y2="12"/></g></svg>
                    </div>

                    <h2 class="text-muted">&mdash; <?php _e( "Cart is Empty", "wpdm-premium-packages" ) ?> &mdash;</h2>
                    <div class="lead text-muted"><?php echo apply_filters( "wpdmpp_empty_cart_text", __( "No item is added in the cart yet.", "wpdm-premium-packages" ) ); ?></div>
                    <a class='btn btn-primary' href='<?php echo $settings['continue_shopping_url']; ?>'>
                        <?php echo apply_filters( "wpdmpp_cart_continue_shopping_button_label", __( "Explore Products", "wpdm-premium-packages" ) ); ?> <i class='fas fa-arrow-right'></i>
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    .panel-empty-cart{
        font-family: var(--fetfont);
        margin: 50px 0;
    }
    .panel-empty-cart h2{
        font-size: 16pt;
        font-weight: 700;
        margin: 0 0 5px 0 !important;
    }
    .panel-empty-cart .lead{
        font-size: 12pt;
    }
    .panel-empty-cart .panel-footer{

    }
</style>

<?php do_action( 'wpdmpp_empty_cart_after' ); ?>
