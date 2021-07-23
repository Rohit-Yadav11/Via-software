<?php
/**
 * Template for [wpdm-pp-guest-orders] shortcode
 *
 * This template can be overridden by copying it to yourtheme/download-manager/wpdm-pp-guest-orders.php.
 *
 * @version     1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="w3eden">

    <?php do_action( 'wpdmpp_guest_orders_before' ); ?>

    <?php include wpdm_tpl_path('partials/guest-order-search-form.php', WPDMPP_TPL_DIR); ?>

    <?php include wpdm_tpl_path('partials/guest-order-details.php', WPDMPP_TPL_DIR); ?>

    <?php include wpdm_tpl_path('partials/guest-order-billing-info.php', WPDMPP_TPL_DIR); ?>

    <?php do_action( 'wpdmpp_guest_orders_after' ); ?>

</div>