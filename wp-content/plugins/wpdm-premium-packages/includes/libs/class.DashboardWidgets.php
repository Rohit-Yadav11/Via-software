<?php
/**
 * User: shahnuralam
 * Date: 5/6/17
 * Time: 7:58 PM
 */
namespace WPDMPP\Libs;

use WPDM\Session;


if (!defined('ABSPATH')) {
    exit;
}

class DashboardWidgets
{

    function __construct(){
        add_action('wp_dashboard_setup', array($this, 'addDashboardWidget'));
        add_action('wp_ajax_loadSalesOverview', array($this, 'loadSalesOverview'));
        add_action('wp_ajax_loadLatestOrders', array($this, 'loadLatestOrders'));
        add_action('wp_ajax_loadRecentSales', array($this, 'loadRecentSales'));
        add_action('wp_ajax_loadTopSales', array($this, 'loadTopSales'));
    }

    function salesOverview() {
        ?>
        <div id="wpdmpp-sales-overview"><div style="padding: 50px;text-align: center"><i class="fas fa-sync fa-spin"></i> <?php _e('Loading....','wpdm-premium-packages'); ?></div></div>
        <script>
            jQuery(function ($) {
                $('#wpdmpp-sales-overview').load(ajaxurl, {action: 'loadSalesOverview'});
            });
        </script>
        <?php
    }

    function loadSalesOverview() {
        $data = Session::get( 'sales_overview_html' );
        if($data){
            echo $data;
            die();
        }

        ob_start();
        include WPDMPP_TPL_DIR . 'dashboard-widgets/sales-overview.php';
        $data = ob_get_clean();
        Session::set( 'sales_overview_html' , $data );
        echo $data;
        die();
    }

    function latestOrders() {
        ?>
        <div id="wpdmpp-latestOrders"><div style="padding: 50px;text-align: center"><i class="fas fa-sync fa-spin"></i> <?php _e('Loading....','wpdm-premium-packages'); ?></div></div>
        <script>
            jQuery(function ($) {
                $('#wpdmpp-latestOrders').load(ajaxurl, {action: 'loadLatestOrders'});
            });
        </script>
        <?php
    }

    function loadLatestOrders() {
        $data = Session::get( 'latest_orders_html' );
        if($data){
            echo $data;
            die();
        }
        ob_start();
        include WPDMPP_TPL_DIR . 'dashboard-widgets/latest-orders.php';
        $data = ob_get_clean();
        Session::set( 'latest_orders_html' , $data );
        echo $data;
        die();
    }

    function recentSales() {
        ?>
        <div id="wpdmpp-recentSales"><div style="padding: 50px;text-align: center"><i class="fas fa-sync fa-spin"></i> <?php _e('Loading....','wpdm-premium-packages'); ?></div></div>
        <script>
            jQuery(function ($) {
                $('#wpdmpp-recentSales').load(ajaxurl, {action: 'loadRecentSales'});
            });
        </script>
        <?php
    }

    function loadRecentSales() {
        $data = Session::get( 'recent_sales_html' );
        if($data){
            echo $data;
            die();
        }
        ob_start();
        include WPDMPP_TPL_DIR . 'dashboard-widgets/recent-sales.php';
        $data = ob_get_clean();
        Session::set( 'recent_sales_html' , $data );
        echo $data;
        die();
    }

    function topSales() {
        ?>
        <div id="wpdmpp-topSales"><div style="padding: 50px;text-align: center"><i class="fas fa-sync fa-spin"></i> <?php _e('Loading....','wpdm-premium-packages'); ?></div></div>
        <script>
            jQuery(function ($) {
                $('#wpdmpp-topSales').load(ajaxurl, {action: 'loadTopSales'});
            });
        </script>
        <?php
    }

    function loadTopSales() {
        $data = Session::get('top_sales_html');
        if($data){
            echo $data;
            die();
        }
        ob_start();
        include WPDMPP_TPL_DIR . 'dashboard-widgets/top-sales.php';
        $data = ob_get_clean();
        Session::set( 'top_sales_html' , $data );
        echo $data;
        die();
    }


    function addDashboardWidget() {
        if(current_user_can(WPDM_ADMIN_CAP)) {
            wp_add_dashboard_widget('wpdmpp_sales_overview', __('Sales Overview', 'wpdm-premium-packages'), array($this, 'salesOverview'));
            wp_add_dashboard_widget('wpdmpp_lastest_orders', __('Latest Orders', 'wpdm-premium-packages'), array($this, 'latestOrders'));
            wp_add_dashboard_widget('wpdmpp_lastest_sales', __('Recently Sold Items', 'wpdm-premium-packages'), array($this, 'recentSales'));
            wp_add_dashboard_widget('wpdmpp_top_sales', __('Top Selling Items ( Last 90 Days )', 'wpdm-premium-packages'), array($this, 'topSales'));
        }
    }

}

new DashboardWidgets();
