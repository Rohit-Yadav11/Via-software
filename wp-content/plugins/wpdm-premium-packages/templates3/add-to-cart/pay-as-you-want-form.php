<?php
/**
 * Add To Cart form for pacakges with "Pas as you want" feature.
 *
 * This template can be overridden by copying it to yourtheme/download-manager/add-to-cart/pay-as-you-want-form.php.
 *
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $current_user;

do_action("wpdmpp_before_add_to_cart_form"); ?>

    <form method="post" action="" name="cart_form" class="wpdm_cart_form wpdm_cart_form_<?php echo $product_id; ?>" id="wpdm_cart_form_<?php echo $product_id; ?>">
        <input type="hidden" name="addtocart" value="<?php echo $product_id; ?>">
        <input type="hidden" name="files" id="files_<?php echo $product_id; ?>" class="files_<?php echo $product_id; ?>" value="">
        <div data-curr="<?php echo $currency_sign; ?>" id="total-price-<?php echo $product_id; ?>"></div>

        <?php do_action( 'wpdmpp_before_add_to_cart_button', $product_id ); ?>
        <div class="input-group input-group-asyoupay">
            <div class="input-group-addon text-muted">
                <?php echo apply_filters("wpdmpp_as_you_pay_label", __('Name Your Price:', 'wpdm-premium-packages'), $product_id); ?>
            </div>
            <input name="iwantopay" type="number" placeholder="<?php echo wpdmpp_product_price($product_id); ?>" step="0.01" min="<?php echo wpdmpp_product_price($product_id); ?>" value="<?php echo wpdmpp_product_price($product_id); ?>" class="form-control iwanttopay text-center">
        </div>
        <div class="min-price-note">
            <?php echo apply_filters("wpdmpp_min_price_note", sprintf(__('Minimum Price: %s', 'wpdm-premium-packages'), wpdmpp_product_price($product_id)), $product_id); ?>
        </div>

        <?php echo wpdmpp_product_gigs_options_html($product_id); ?>

        <div class="add-to-cart-button">
            <?php echo wpdmpp_add_to_cart_button($product_id); ?>
        </div>

        <?php do_action('wpdmpp_after_add_to_cart_button', $product_id); ?>

    </form>

<?php do_action('wpdmpp_after_add_to_cart_form', $product_id); ?>
