<?php
/**
 * Link to the guest order page.
 *
 * This template is active only when you set Guest Order page in Premium Package >> Basic Settings >> Frontend Settings panel.
 *
 * This template can be overridden by copying it to yourtheme/download-manager/partials/guest_order_page_link.php.
 *
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
$guest_order_page_link = wpdmpp_guest_order_page(); ?>

<div class='panel panel-default'>
    <div class='panel-body'>
        <?php echo apply_filters( "wpdmpp_guest_order_page_link_description", __( "Don't have an account yet? Need to download quickly?", "wpdm-premium-packages" ) ); ?>
    </div>
    <div class='panel-footer'>
        <a class='btn btn-block btn-info' href='<?php echo $guest_order_page_link; ?>'>
            <?php echo apply_filters( "wpdmpp_guest_order_page_link_label", __( "Go to Guest Download Page", "wpdm-premium-packages" ) ); ?>
        </a>
    </div>
</div>