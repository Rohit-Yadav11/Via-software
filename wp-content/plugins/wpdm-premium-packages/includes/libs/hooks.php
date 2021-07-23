<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action('init', 'wpdmpp_load_payment_methods');
add_action('init', 'wpdmpp_remove_cart_item');
add_action('init', 'wpdmpp_get_purchased_items');

add_action('wp_loaded', 'wpdmpp_load_saved_cart');

add_filter('admin_head', 'wpdmpp_head');

add_action('wp', 'wpdmpp_download_order_note_attachment');

add_filter("wpdm_email_template_tags", "wpdmpp_email_template_tags");
add_filter("wpdm_email_templates", "wpdmpp_email_templates");

if (is_admin()) {
    add_action('wp_ajax_assign_user_2order', 'wpdmpp_assign_user_2order');
    add_action('wp_ajax_RecalculateSales', 'wpdmpp_recalculate_sales');
    add_action('publish_post', 'wpdmpp_notify_product_accepted');
}

if (!is_admin()) {
    //add to cart using form submit
    add_action('init', 'wpdmpp_add_to_cart');
    //add to cart from url call
    add_action('init', 'wpdmpp_add_to_cart_ucb');

    add_action('init', 'wpdmpp_withdraw_request');

    add_filter('wp_head', 'wpdmpp_head');
    add_action('init', 'wpdmpp_update_cart');
    add_action('init', 'wpdmpp_delete_product');
}

add_action('wpdm_onstart_download', 'wpdmpp_validate_download');


add_action("wp_ajax_nopriv_update_guest_billing", "wpdmpp_update_guest_billing");
add_action("wp_ajax_wpdmpp_delete_frontend_order", "wpdmpp_delete_frontend_order");
