<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if( ! class_exists( 'WPDMPPAdminMenus' ) ):

    class WPDMPPAdminMenus{

        function __construct()
        {
            if ( is_admin() ) {
                add_action('admin_menu', array($this, 'wpdmpp_menu'));
            }

        }

        /**
         * Menu for the Premium Package
         */
        function wpdmpp_menu()
        {
            add_submenu_page('edit.php?post_type=wpdmpro', __('Orders', "wpdm-premium-packages"), __('Orders', "wpdm-premium-packages"), WPDM_MENU_ACCESS_CAP, 'orders', array( $this, 'wpdmpp_orders' ) );
            add_submenu_page('edit.php?post_type=wpdmpro', __('License Manager', "wpdm-premium-packages"), __('License Manager', "wpdm-premium-packages"), WPDM_MENU_ACCESS_CAP, 'pp-license', array( $this, 'wpdmpp_license' ) );
            add_submenu_page('edit.php?post_type=wpdmpro', __('Coupon Codes', "wpdm-premium-packages"), __('Coupon Codes', "wpdm-premium-packages"), WPDM_MENU_ACCESS_CAP, 'pp-coupon-codes', array( $this, 'wpdmpp_all_coupons' ) );
            add_submenu_page('edit.php?post_type=wpdmpro', __('Customers', "wpdm-premium-packages"), __('Customers', "wpdm-premium-packages"), WPDM_MENU_ACCESS_CAP, 'customers', array( $this, 'wpdmpp_customers' ) );
            add_submenu_page('edit.php?post_type=wpdmpro', __('Payouts', "wpdm-premium-packages"), __('Payouts', "wpdm-premium-packages"), WPDM_MENU_ACCESS_CAP, 'payouts', array( $this, 'wpdmpp_all_payouts' ) );
        }

        /**
         * All Orders list
         */
        function wpdmpp_orders()
        {
            if(!current_user_can(WPDM_MENU_ACCESS_CAP)) return;

            $orderObj = new \WPDMPP\Libs\Order();
            global $wpdb;
            $l = 15;
            $currency_sign = wpdmpp_currency_sign();
            $p = isset($_GET['paged']) && (int)$_GET['paged'] > 0 ? $_GET['paged'] : 1;
            $s = ($p - 1) * $l;
            $order_id = isset($_GET['id']) && is_array($_GET['id'])?wpdm_sanitize_array($_GET['id']):sanitize_text_field(wpdm_query_var('id'));

            if (isset($_GET['task']) && $_GET['task'] == 'vieworder') {
                $order = $orderObj->getOrder($order_id);
                include('templates/view-order.php');
            } else if (isset($_GET['task']) && $_GET['task'] == 'createorder') {
                include('templates/create-order.php');
            } else {
                if (isset($_GET['task']) && $_GET['task'] == 'delete_order') {
                    $order_id = sanitize_text_field($_GET['id']);
                    $ret = $wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->prefix}ahm_orders WHERE order_id = %s", $order_id ));

                    if ($ret) {
                        $ret = $wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->prefix}ahm_order_items WHERE oid = %s", $order_id ));
                        if ($ret) $msg = __("Order ($order_id) is deleted successfully", "wpdm-premium-packages");
                    }

                } else if (isset($_GET['delete_confirm']) && $_GET['delete_confirm'] == 1) {
                    $order_ids = $_GET['id'];
                    if (!empty($order_ids) && is_array($order_ids)) {
                        $msg = "Selected order are deleted";
                        foreach ($order_ids as $key => $order_id) {
                            $order_id = sanitize_text_field($order_id);
                            $ret = $wpdb->query(
                                $wpdb->prepare("DELETE FROM {$wpdb->prefix}ahm_orders WHERE order_id = %s", $order_id));
                            if ($ret) {
                                $ret = $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}ahm_order_items WHERE oid = %s",$order_id ));
                                //if ($ret) $msg[0] = "Selected order are deleted";
                            }
                        }
                    }
                } else if (isset($_GET['delete_all_by_payment_sts']) && $_GET['delete_all_by_payment_sts'] != "") {
                    $payment_status = sanitize_text_field($_GET['delete_all_by_payment_sts']);

                    $order_ids = $wpdb->get_results(
                        "SELECT order_id
								FROM {$wpdb->prefix}ahm_orders
								WHERE payment_status = '$payment_status'"
                        , ARRAY_A);
                    if ($order_ids) {
                        foreach ($order_ids as $row) {
                            $order_id = $row['order_id'];
                            $ret = $wpdb->query(
                                $wpdb->prepare(
                                    "DELETE FROM {$wpdb->prefix}ahm_orders
							 WHERE order_id = %s",
                                    $order_id
                                )
                            );
                            if ($ret) {

                                $ret = $wpdb->query(
                                    $wpdb->prepare(
                                        "DELETE FROM {$wpdb->prefix}ahm_order_items
								 WHERE oid = %s",
                                        $order_id
                                    )
                                );

                                if ($ret) $msg = "All orders with payment status <b>{$payment_status}</b> are deleted";
                            }
                        }
                    }
                }


                if (isset($_REQUEST['oid']) && $_REQUEST['oid'])
                    $qry[] = "order_id='".sanitize_text_field($_REQUEST['oid'])."'";
                if (isset($_REQUEST['customer']) && $_REQUEST['customer']!=''){
                    $customer = esc_sql($_REQUEST['customer']);
                    if(is_email($customer)) $customer = email_exists($customer);
                    $qry[] = "uid='{$customer}'";
                }
                if(wpdm_query_var('ost') != 'Expiring') {
                    if (isset($_REQUEST['ost']) && $_REQUEST['ost'])
                        $qry[] = "order_status='" . sanitize_text_field($_REQUEST['ost']) . "'";
                    if (isset($_REQUEST['pst']) && $_REQUEST['pst'])
                        $qry[] = "payment_status='" . sanitize_text_field($_REQUEST['pst']) . "'";

                    if (isset($_REQUEST['sdate'], $_REQUEST['edate']) && ($_REQUEST['sdate'] != '' || $_REQUEST['edate'] != '')) {
                        $_REQUEST['edate'] = $_REQUEST['edate'] ? $_REQUEST['edate'] : $_REQUEST['sdate'];
                        $_REQUEST['sdate'] = $_REQUEST['sdate'] ? $_REQUEST['sdate'] : $_REQUEST['edate'];
                        $sdate = strtotime($_REQUEST['sdate']);
                        $edate = strtotime($_REQUEST['edate']);
                        $qry[] = "(`date` >=$sdate and `date` <=$edate)";
                    }
                } else {
                    $qry[] = "order_status='Completed'";
                    $sdate = wpdm_query_var('sdate') != ''? strtotime(wpdm_query_var('sdate')):time();
                    $edate = wpdm_query_var('edate') != ''? strtotime(wpdm_query_var('edate')):strtotime("+7 days");
                    $qry[] = "(`expire_date` >=$sdate and `expire_date` <=$edate)";

                }

                if (isset($qry))
                    $qry = "where " . implode(" and ", $qry);
                else $qry = "";

                if(wpdm_query_var('orderby') != ''){
                    $orderby = sanitize_text_field(wpdm_query_var('orderby'));
                    $_order = wpdm_query_var('order') == 'asc'?'asc':'desc';
                    $qry = $qry . " order by $orderby $_order";
                } else
                    $qry = "$qry order by `date` desc";

                $t = $orderObj->totalOrders($qry);
                $orders = $orderObj->GetAllOrders($qry, $s, $l);
                include('templates/orders.php');
            }
        }

        function wpdmpp_license()
        {
            global $wpdb;
            $l = 30;
            $p = isset($_GET['paged']) ? $_GET['paged'] : 1;
            $s = ($p - 1) * $l;

            if (isset($_GET['task']) && $_GET['task'] == 'NewLicense') {
                include('templates/new-license.php');
            } else if (isset($_GET['task']) && $_GET['task'] == 'editlicense') {
                $lid = intval($_GET['id']);
                if(isset($_POST['do']) && $_POST['do'] == 'updatelicense' && current_user_can('manage_options')){
                    $license = sanitize_text_field($_POST['license']);
                    if(trim($license['domain']) != ''){
                        $license['domain'] = explode("\n", $license['domain']);
                        $license['domain'] = maybe_serialize($license['domain']);
                    }
                    $license['activation_date'] = strtotime($license['activation_date']);
                    $wpdb->update("{$wpdb->prefix}ahm_licenses", $license, array('id' => sanitize_text_field( $_POST['lid'] ) ) );
                }
                $license = $wpdb->get_row("select * from {$wpdb->prefix}ahm_licenses where id='{$lid}'");
                include('templates/edit-license.php');
            } else {

                if (isset($_GET['task']) && $_GET['task'] == 'delete_selected') {

                    if(current_user_can('manage_options')){
                        $ids = implode(",", $_REQUEST['id']);
                        $ids = esc_sql($ids);
                        $wpdb->query("delete from {$wpdb->prefix}ahm_licenses where id IN ($ids)");
                    }
                }

                $qry = array();
                if (isset($_REQUEST['licenseno']) && $_REQUEST['licenseno'] != '')
                    $qry[] = "licenseno='".esc_sql($_REQUEST['licenseno'])."'";
                if (isset($_REQUEST['oid']) && $_REQUEST['oid'] != '')
                    $qry[] = "oid='".esc_sql($_REQUEST['oid'])."'";
                if (isset($_REQUEST['link']) && $_REQUEST['link'] != '')
                    $qry[] = "domain LIKE '%".sanitize_text_field($_REQUEST['link'])."%'";
                if (count($qry) > 0)
                    $qry = "and " . implode(" and ", $qry);
                else $qry = "";

                $t = $wpdb->get_var("select count(*) from {$wpdb->prefix}ahm_licenses where 1 $qry");
                $licenses = $wpdb->get_results("select l.*,f.post_title as productname from {$wpdb->prefix}ahm_licenses l,{$wpdb->prefix}posts f where l.pid=f.ID $qry order by id desc limit $s, $l");

                include("templates/manage-license.php");
            }
        }

        function wpdmpp_all_coupons(){
            switch (wpdm_query_var('task')){
                case 'new_coupon':

                    include "templates/new-coupon.php";
                    break;
                case 'edit_coupon':
                    $coupon = \WPDMPP\Libs\CouponCodes::get(wpdm_query_var('ID'));

                    include "templates/new-coupon.php";
                    break;
                default:
                    include "templates/coupon-codes.php";
                    break;
            }
        }

        /**
         * payouts section
         */
        function wpdmpp_all_payouts()
        {
            include "templates/payouts.php";
        }

        function wpdmpp_customers()
        {
            $tabs['profile'] = ['name' => esc_attr__( 'Profile', 'wpdm-premium-packages' ), 'callback' => [$this, 'customer_profile']];
            $tabs = apply_filters("wpdmpp_customer_profile_admin_tab_content", $tabs);
            $tab =  wpdm_query_var('view');
            if(isset($tabs[$tab])) {
                include __DIR__.'/templates/customer-profile.php';
            }
            else
                include __DIR__."/templates/customers.php";
        }

        function customer_profile()
        {
            include __DIR__.'/templates/customer-purchases.php';
        }

    }

endif;

new WPDMPPAdminMenus();
