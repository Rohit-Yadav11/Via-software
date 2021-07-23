<?php
/**
 * User: shahjada
 * Date: 2019-03-21
 * Time: 13:14
 */

namespace WPDMPP\Libs;


class CronJobs
{
    function __construct()
    {

        add_filter( 'cron_schedules', array($this, 'interval') );

        if ( ! wp_next_scheduled( 'wpdmpp_cron' ) ) {
            wp_schedule_event( time() + 3600, 'six_hourly', 'wpdmpp_cron' );
        }

        $this->schedule();


    }

    function interval( $schedules ) {
        $schedules['six_hourly'] = array(
            'interval' => 21600, //6 hours
            'display'  => esc_html__( 'Every 6 hours' ),
        );

        return $schedules;
    }

    function schedule(){
        add_action( 'wpdmpp_cron', array($this, 'notifyToRenew') );
    }

    function notifyToRenew(){
        global $wpdb, $wpdmpp_settings;
        if(!isset($wpdmpp_settings['order_expiry_alert']) || (int)$wpdmpp_settings['order_expiry_alert'] !== 1) return;
        $date = date("Y-m-d", strtotime("+8 days"));
        $stime = strtotime($date." 00:00");
        $etime = strtotime($date." 23:59");
        $orders = $wpdb->get_results("select * from {$wpdb->wpdmpp_orders} where expire_date >= $stime and expire_date <= $etime");

        $ndate = date("Y_m");
        $renew_notifs = get_option("__wpdmpp_order_renewal_notifs_{$ndate}", array());
        $renew_notifs = maybe_unserialize($renew_notifs);
        $mailed = 0;
        $total = 0;
        $totalm = 0;
        $msg = __( "Order Expiration and Subscription reminder email sent for the following orders:", "wpdm-premium-packages" )."<br/>";
        $msg .= "<table style='width:100%' class='email' cellspacing='0'>";
        foreach ($orders as $order){
            if(!isset($renew_notifs[$order->order_id."_".$order->expire_date])) {
                if ((int)$order->auto_renew === 1)
                    $total += (double)$order->total;
                else
                    $totalm += (double)$order->total;
                if($order->payment_method !== 'WPDM_2Checkout') {
                    $order->billing_info = maybe_unserialize($order->billing_info);
                    $order->currency    = maybe_unserialize($order->currency);
                    $csign              = isset($order->currency['sign']) ? $order->currency['sign'] : '$';
                    $user = get_user_by('id', $order->uid);
                    $sitename = get_bloginfo('name');
                    $exp_date = date(get_option('date_format'), $order->expire_date - 82800);
                    $order_url = wpdmpp_orders_page('id=' . $order->order_id);
                    $params = array('subject' => "[$sitename] Automatic Order Renewal", 'to_email' => $user->user_email, 'expire_date' => $exp_date, 'orderid' => $order->order_id, 'order_url' => $order_url);
                    $items = \WPDMPP\Libs\Order::GetOrderItems($order->order_id);
                    $allitems = "<table  class='email' style='width: 100%;border: 0;margin-top: 15px' cellpadding='0' cellspacing='0'><tr><th>Product Name</th><th>License</th><th style='width:80px;text-align:right'>Price</th></tr>";
                    foreach ($items as $item) {
                        $product = get_post($item['pid']);
                        $license = maybe_unserialize($item['license']);
                        $license = is_array($license) && isset($license['info'], $license['info']['name']) ? $license['info']['name'] : " &mdash; ";
                        $price = $csign . number_format($item['price'], 2);
                        $_item = "<tr><td><a href='" . get_permalink($product->ID) . "'>{$product->post_title}</a></td><td>{$license}</td><td align='right' style='width:80px;text-align:right'>{$price}</td></tr>";
                        $product_by_seller[$product->post_author][] = $_item;
                        $allitems .= $_item;
                    }
                    $allitems .= "</table>";
                    $params['items'] = $params['order_items'] = $allitems;

                    if ($order->auto_renew == 1) {
                        \WPDM\Email::send('subscription-reminder', $params);
                    } else {
                        \WPDM\Email::send('order-expire', $params);
                    }
                    $mailed++;
                    $admin_view_order = admin_url("edit.php?post_type=wpdmpro&page=orders&task=vieworder&id={$order->order_id}");
                    $renew_notifs[$order->order_id . "_" . $order->expire_date] = 1;
                } else
                    Order::update(array('auto_renew' => 1), $order->order_id);
                $msg .= "<tr style='border-bottom: #ebf0f5'><td><a href='{$admin_view_order}'>{$order->order_id}</a></td><td align='right' style='text-align:right'>{$csign}{$order->total}</td></tr>";
            } else {
                Order::update(array('auto_renew' => 1), $order->order_id);
            }
        }

        if($mailed > 0){
            // Notify admin
            $msg .= "<tr style='background: #ebf0f5'><th>".__("Total Auto-renewal Amount", "wpdm-premium-packages").": </th><th align='right' style='text-align:right'>". wpdmpp_currency_sign().number_format($total, 2)."</th></tr>";
            $msg .= "<tr><th>".__("Total Manual-renewal Amount", "wpdm-premium-packages").": </th><th align='right' style='text-align:right'>". wpdmpp_currency_sign().number_format($totalm)."</th></tr>";
            $msg .= "<tr style='background: #ebf0f5'><th>".__("Renewal Date", "wpdm-premium-packages").": </th><th align='right' style='text-align:right'>". $date."</th></tr></table>";
            $params = array('subject' => sprintf(__("[%s] Order Expiration and Subscription reminder sent.", "wpdm-premium-packages"), $sitename), 'to_email' => get_option('admin_email'), 'message' => $msg);
            \WPDM\Email::send('default', $params);
        }

        update_option("__wpdmpp_order_renewal_notifs_{$ndate}", $renew_notifs);

    }
}

new CronJobs();