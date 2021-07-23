<?php
/**
 * Shortcode Template for [wpdmpp_purchases]
 *
 * This template can be overridden by copying it to yourtheme/download-manager/wpdm-pp-purchases.php.
 *
 * @version     1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $wpdb, $sap, $wpdmpp_settings, $current_user;

if ( ! isset( $_GET['id'] ) && ! isset( $_GET['item'] ) ) :
    include_once wpdm_tpl_path('partials/resolve-order.php', WPDMPP_TPL_DIR );
    include_once wpdm_tpl_path('partials/user-orders-list.php', WPDMPP_TPL_DIR );
endif;

//Order Details
if ( isset( $_GET['id']) && $_GET['id'] != '' && ! isset( $_GET['item'] ) ):
    //include_once wpdm_tpl_path('partials/user-order-details.php', WPDMPP_TPL_DIR );
    \WPDMPP\Controllers\Order::userOrderDetails(wpdm_query_var('id'));
endif;
