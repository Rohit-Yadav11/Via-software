<?php


namespace WPDMPP\Controllers;


class Order
{
    static function userOrderDetails($order_id = null)
    {
        global $wpdb, $sap, $wpdmpp_settings, $current_user;

        $order_notes = '';
        if (!wpdm_query_var('udb_page') || !$order_id)
            $order_id = wpdm_query_var('id');
        if ($order_id) {
            $orderObj = new \WPDMPP\Libs\Order($order_id);
            $orderurl = get_permalink(get_the_ID());
            $loginurl = home_url("/wp-login.php?redirect_to=" . urlencode($orderurl));
            $csign = wpdmpp_currency_sign();
            $csign_before = wpdmpp_currency_sign_position() == 'before' ? $csign : '';
            $csign_after = wpdmpp_currency_sign_position() == 'after' ? $csign : '';
            $link = wpdm_query_var('udb_page') ? get_permalink() . "?udb_page=purchases/" : get_permalink();
            $o = $orderObj;
            $order = $orderObj->getOrder($order_id);
            $extbtns = "";
            $extbtns = apply_filters("wpdmpp_order_details_frontend", $extbtns, $order);

            //Check order status
            if ($order->expire_date == 0 && get_wpdmpp_option('order_validity_period', 365) > 0) {
                $expire_date = $order->date + (get_wpdmpp_option('order_validity_period', 365) * 86400);
                $orderObj->set('expire_date', $expire_date);
                if (time() > $expire_date) {
                    $orderObj->set('order_status', 'Expired');
                    $orderObj->set('payment_status', 'Expired');
                    $order->order_status = 'Expired';
                    $order->payment_status = 'Expired';
                }
                $orderObj->save();
            }

            $date = date("Y-m-d h:i a", $order->date);
            $items = maybe_unserialize($order->items);
            $expire_date = $order->expire_date;


            $renews = $wpdb->get_results("select * from {$wpdb->prefix}ahm_order_renews where order_id='" . esc_sql($orderObj->oid) . "'");

            if ($order->uid == 0) {
                $order->uid = $current_user->ID;
                $o->update(array('uid' => $current_user->ID), $order->order_id);
            }

            if ($order->uid == $current_user->ID) {

                $order->currency = maybe_unserialize($order->currency);
                $csign = isset($order->currency['sign']) ? $order->currency['sign'] : '$';
                $csign_before = wpdmpp_currency_sign_position() == 'before' ? $csign : '';
                $csign_after = wpdmpp_currency_sign_position() == 'after' ? $csign : '';
                $cart_data = maybe_unserialize($order->cart_data);
                $items = \WPDMPP\Libs\Order::GetOrderItems($order->order_id);

                if (is_array($items) && count($items) == 0) {
                    foreach ($cart_data as $pid => $noi) {
                        $newi = get_posts(array('post_type' => 'wpdmpro', 'meta_key' => '__wpdm_legacy_id', 'meta_value' => $pid));
                        if (is_array($newi) && count($newi) > 0) {
                            $new_cart_data[$newi[0]->ID] = array("quantity" => $noi, "variation" => "", "price" => get_post_meta($newi[0]->ID, "__wpdm_base_price", true));
                            $new_order_items[] = $newi[0]->ID;
                        }
                    }

                    \WPDMPP\Libs\Order::Update(array('cart_data' => serialize($new_cart_data), 'items' => serialize($new_order_items)), $order->order_id);
                    \WPDMPP\Libs\Order::UpdateOrderItems($new_cart_data, $order->order_id);
                    $items = \WPDMPP\Libs\Order::GetOrderItems($order->order_id);
                }

                $order->title = $order->title ? $order->title : sprintf(__('Order # %s', 'wpdm-premium-packages'), $order->order_id);

                $colspan = 6;
                $coupon_discount = $role_discount = 0;
                foreach ($items as $item) {
                    $coupon_discount += $item['coupon_discount'];
                    $role_discount += $item['role_discount'];
                }
                if ($coupon_discount == 0) $colspan--;
                if ($role_discount == 0) $colspan--;
                if ($order->order_status !== 'Completed') $colspan--;

                include wpdm_tpl_path('partials/user-order-details.php', WPDMPP_TPL_DIR);
            } else
                \WPDM_Messages::error(__('Order does not belong to you!', 'wpdm-premium-packages'));
        } else
            \WPDM_Messages::error(__('Invalid Order ID!', 'wpdm-premium-packages'));
    }
}
