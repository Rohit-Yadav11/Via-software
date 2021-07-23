<?php
namespace WPDMPP\Libs;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class CustomColumns {

    function __construct(){
        add_filter('manage_posts_columns', array($this, 'ColumnsTH'));
        add_filter('manage_posts_custom_column', array($this, 'ColumnsTD'), 10, 2);
        add_filter('request', array($this, 'OrderBy'));
        add_filter( 'manage_edit-wpdmpro_sortable_columns', array($this, 'Sortable'));
    }

    function ColumnsTH($defaults) {
        if(get_post_type() != 'wpdmpro' || !current_user_can(WPDM_ADMIN_CAP)) return $defaults;
        $otf['wpdmuprice'] = __('Price', 'wpdm-premium-packages');
        $otf['wpdmsaleqty'] = "<span title=".__('Sales Quantity','wpdm-premium-packages')." class='ttip'>".__('Quantity', 'wpdm-premium-packages')."</span>";
        $otf['wpdmsaleamt'] = "<span title=".__('Total Sales','wpdm-premium-packages')." class='wpdm-th-icon ttip'><i class='fas fa-dollar-sign'></i></span>";

        wpdm_array_splice_assoc( $defaults, 4, 0, $otf );
        return $defaults;
    }

    function ColumnsTD($column_name, $post_ID) {
        if(get_post_type() != 'wpdmpro' || !current_user_can(WPDM_ADMIN_CAP)) return;
        if ($column_name == 'wpdmsaleqty') {
            echo "<span id='sc-{$post_ID}'>".intval(get_post_meta($post_ID, '__wpdm_sales_count', true))."</span>";
        }

        if ($column_name == 'wpdmsaleamt') {
            echo "<a title=".__('Total Sales, Click to Recalculate', 'wpdm-premium-packages')." class='ttip recal-sa' href='#' rel='{$post_ID}'>".wpdmpp_price_format((double)get_post_meta($post_ID, '__wpdm_sales_amount', true), true, true)."</a>";
        }

        if ($column_name == 'wpdmuprice') {
            echo wpdmpp_price_range($post_ID);
            //echo wpdmpp_currency_sign().number_format((double)get_post_meta($post_ID, '__wpdm_base_price', true),2);
        }
    }

    function Sortable( $columns ) {
        if(get_post_type() != 'wpdmpro') return $columns;

        $columns['download_count'] = 'download_count';

        return $columns;
    }

    function OrderBy( $vars ) {

        if ( isset( $vars['orderby'] ) && 'download_count' == $vars['orderby'] ) {
            $vars = array_merge( $vars, array(
                'meta_key' => '__wpdm_download_count',
                'orderby' => 'meta_value_num'
            ) );
        }
        return $vars;
    }

}

new CustomColumns();
