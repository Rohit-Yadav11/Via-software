<?php
/**
 * Add To Cart form shown after pacakge price range
 *
 * This template can be overridden by copying it to yourtheme/download-manager/add-to-cart/form.php.
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
        <input type="hidden" data-curr="<?php echo $currency_sign; ?>" id="total-price-<?php echo $product_id; ?>" value="<?php echo wpdmpp_effective_price($product_id); ?>" />

        <?php do_action('wpdmpp_before_add_to_cart_button', $product_id); ?>
        <?php echo wpdmpp_product_license_options_html($product_id); ?>
        <?php echo wpdmpp_product_gigs_options_html($product_id); ?>
        <?php
        $role_discount = wpdmpp_role_discount($product_id);
        $role_name = wpdmpp_role_discount($product_id, true);
        if ($role_discount > 0) { ?>
            <div class="alert alert-info">
                <?php echo sprintf(__("%s %s discount will be applied in the cart", "wpdm-premium-packages"), $role_discount . '%', $role_name); ?>
            </div>
        <?php } ?>
        <span class="add-to-cart-button">
            <?php echo wpdmpp_add_to_cart_button($product_id); ?>
        </span>
        <?php do_action('wpdmpp_after_add_to_cart_button', $product_id); ?>
    </form>
<?php include wpdm_tpl_path("add-to-cart/buy-now.php", WPDMPP_TPL_DIR, WPDMPP_TPL_FALLBACK); ?>
<?php do_action('wpdmpp_after_add_to_cart_form', $product_id); ?>
