<?php
namespace WPDMPP\Libs;

// Exit if accessed directly
use WPDM\Email;
use WPDM\libs\MailUI;
use WPDM\Session;
use WPDMPP\WPDMPremiumPackage;

if (!defined('ABSPATH')) {
    exit;
}

    class Order{
        var $oid;
        var $ID;
        var $orderData;
        function __construct( $oid = '' ){
            if($oid) {
                $this->oid = $oid;
                $this->ID = $oid;
                $order = $this->getOrder($oid);
                $order = (array)$order;
                if(is_array($order) && count($order) > 0) {
                    foreach ($order as $key => $val) {
                        $this->$key = maybe_unserialize($val);
                        if($key != 'order_id')
                            $this->orderData[$key] = maybe_unserialize($val);
                    }
                } else {
                    $this->oid = $this->ID = null;
                }
            }
        }

        function set($key, $val){
            $this->orderData[$key] = $val;
            $this->$key = $val;
            return $this;
        }

        function save(){
           // wpdmdd($this->orderData);
            return Order::update($this->orderData, $this->oid);
        }


        function newOrder($id, $title, $items, $total, $userid, $order_status = 'Processing', $payment_status = 'Processing', $cart_data = '', $order_notes="", $payment_method = ""){
            global $wpdb, $current_user;

            $currency = array('sign' => wpdmpp_currency_sign(),'code' => wpdmpp_currency_code());
            $currency = serialize($currency);
            $auto_renew = get_wpdmpp_option('auto_renew', 0);

            $ret = $wpdb->insert("{$wpdb->prefix}ahm_orders",array('order_id'=>$id, 'title'=>$title,'date'=>time(), 'items'=> $items,'total'=> $total, 'order_status' => $order_status, 'payment_status' => $payment_status, 'cart_data' => $cart_data,'uid' => (int)$userid,'order_notes' => $order_notes,'payment_method' => $payment_method, 'download'=>0, 'IP' => $_SERVER['REMOTE_ADDR'], 'currency'=> $currency, 'auto_renew' => $auto_renew));

            //if(!$ret) { $wpdb->show_errors(); $wpdb->print_error(); echo "<div class='alert alert-info'>".wpdmpp_reactivate()."</div>"; die(); }

            $this->oid = $id;
            Session::set('orderid', $id);
            return $id;
        }

        static function update($data, $id){
            global $wpdb;
            foreach ($data as &$column){
                if(is_array($column)) $column = maybe_serialize($column);
            }
            $res = $wpdb->update("{$wpdb->prefix}ahm_orders",$data,array('order_id'=>$id));
            return $res;
        }

        public static function customerInfo($order_id){
            $_order = new \WPDMPP\Libs\Order();
            $order = $_order->getOrder($order_id);
            if($order->uid < 1) {
                $billing_info = unserialize($order->billing_info);
                $customer['name'] = isset($billing_info['first_name']) ? $billing_info['first_name'] : '';
                $customer['email'] = isset($billing_info['order_email']) ? $billing_info['order_email'] : '';
            } else {
                $user = get_user_by('id', $order->uid);
                $customer['name'] = $user->display_name;
                $customer['email'] = $user->user_email;
            }
            return $customer;
        }

        /**
         * @param $id Order ID
         * @param $data
         * @param string $type
         * @return bool
         */
        static function add_note($id, $data, $type = 'messages'){
            global $wpdb, $current_user;

            if(!is_user_logged_in()) return false;

            $order_info = $wpdb->get_row("select * from {$wpdb->prefix}ahm_orders where order_id='{$id}'");

            if($current_user->ID != $order_info->uid && !current_user_can(WPDMPP_MENU_ACCESS_CAP)) return false;

            $order_note = $order_info->order_notes;

            if(!isset($data['by'])) {
                $data['by'] = $current_user->ID == $order_info->uid?'Customer':'Seller';
            }

            $fromname = get_bloginfo('name');
            $frommail = "no-reply@".$_SERVER['HTTP_HOST'];

            $customer = get_user_by('id', $order_info->uid);

            //$customer
            // For Email
            $viewlink_customer  = "<a class='button' style='display:block;margin:0;padding:8px 0 !important;' href='".wpdmpp_orders_page('id='.$id)."'>View Order</a>";
            $viewlink_admin     = "<a class='button' style='display:block;margin:0;padding:8px 0 !important;' href='".admin_url("/edit.php?post_type=wpdmpro&page=orders&task=vieworder&id={$id}")."'>View Order</a>";
            $send_email = isset($data['email']) && $data['email'] == 0 ? 0 : 1;

            $data['note'] = wp_kses($data['note'], array('strong' => array(), 'b' => array(), 'br' => array(), 'p' => array(), 'hr' => array(), 'a' => array('href' => array(), 'title' => array())));
            $data['note'] = wpdm_escs($data['note']);

            if(isset($data['admin']) && $send_email) {

                $message = MailUI::panel("Note:", array(wpautop($data['note'])), $viewlink_admin);

                $params = array('subject' => "New Note: Order# {$id}", 'to_email' => get_option("admin_email"), 'message' => $message);
                \WPDM\Email::send("default", $params);
            }
            if(isset($data['customer']) && $send_email){

                $message = MailUI::panel("Note:", array(wpautop($data['note'])), $viewlink_customer);

                $params = array('subject' => "New Note: Order# {$id}", 'to_email' => $customer->user_email, 'message' => $message);
                \WPDM\Email::send("default", $params);
            }

            $order_note = maybe_unserialize($order_note);

            if (!is_array($order_note)) $order_note = array();

            $order_note[$type][time()] = $data;
            \WPDMPP\Libs\Order::Update(array('order_notes' => serialize($order_note)), $id);
            return true;
        }

        /**
         * @param $id
         * @param bool $email_notify
         * @param null $payment_method
         * @return bool
         */
        static function complete_order($id, $email_notify = true, $payment_method = null){

            global $wpdb;

            //echo $id;
            if(strpos($id, "renew")){
                $id = explode("_", $id);
                $id = $id[0];
            }
            $id = sanitize_text_field($id);
            $order_det      = $wpdb->get_row("select * from {$wpdb->prefix}ahm_orders where order_id='$id'");
            if(!$order_det) return false;

            wpdmpp_clear_user_cart($order_det->uid);
            wpdmpp_empty_cart();

            $billing_info   = unserialize($order_det->billing_info);
            $buyer_email    = isset($billing_info['order_email']) ? $billing_info['order_email'] : '';
            $name           = isset($billing_info['first_name'])?$billing_info['first_name']:'';
            $settings       = get_option('_wpdmpp_settings');
            $logo           = isset($settings['logo_url'])&&$settings['logo_url']!=""?"<img src='{$settings['logo_url']}' alt='".get_bloginfo('name')."'/>":get_bloginfo('name');
            $expire_date    = get_wpdmpp_option('order_validity_period', 365) > 0? strtotime("+".get_wpdmpp_option('order_validity_period', 0)." days"): 0;

            self::Update(array('order_status' => 'Completed', 'payment_status' => 'Completed', 'expire_date' => $expire_date), $id);

            if(!is_user_logged_in()){
                Session::set('guest_order', $id, 18000);
                Session::set('order_email', $buyer_email, 18000);
            }
            else
                User::addCustomer();

            //print_r( $order_det );die();

            $order_det->currency = maybe_unserialize($order_det->currency);

            if( $order_det->order_status == 'Expired' ){

                $t = time();
                $wpdb->insert("{$wpdb->prefix}ahm_order_renews", array('order_id' => $order_det->order_id, 'subscription_id' => $order_det->trans_id, 'date' => $t));

                //\WPDMPP\Libs\Order::add_note($id, array('note'=>'Order Renewed Successfully <a onclick="window.open(\'?id='.$id.'&wpdminvoice=1&renew='.$t.'\',\'Invoice\',\'height=720, width = 750, toolbar=0\'); return false;" href="#" class="btn-invoice">Get Invoice</a>.','by'=>'Customer'));

                do_action('wpdmpp_order_renewed', $id );
            } else {

                //\WPDMPP\Libs\Order::add_note($id, array('note'=>'Order Status: Completed / Payment Status: Completed / Paid with: '.$order_det->payment_method,'by'=>'Customer'));

                do_action('wpdmpp_order_completed', $id );
            }

            //check and increase coupon usage
            $coupon = array();
            $coupon['coupon_code'] = $order_det->coupon_code;
            $coupon['coupon_discount'] = (float)$order_det->coupon_discount;
            if( $coupon['coupon_discount'] > 0 ) {
                \WPDMPP\Libs\CouponCodes::increase_coupon_usage_count($coupon);
            }

            //return if email notification set to false
            if($email_notify == false) return true;

            // send email notifications
            $userid = $order_det->uid;

            if($userid && $buyer_email ==''){
                $user_info      = get_user_by('id', $userid);
                $name           = $user_info->display_name;
                $buyer_email    = $user_info->user_email;
            }

            $params = array(
                'date'              => date(get_option('date_format'),time()),
                'homeurl'           => home_url('/'),
                'sitename'          => get_bloginfo('name'),
                'order_link'        => "<a href='".wpdmpp_orders_page('id='.$id)."'>".wpdmpp_orders_page('id='.$id)."</a>",
                'register_link'     => "<a href='".wpdmpp_orders_page('orderid='.$id)."'>".wpdmpp_orders_page('orderid='.$id)."</a>",
                'name'              => $name,
                'orderid'           => $id,
                'to_email'          => $buyer_email,
                'order_url'         => wpdmpp_orders_page('id='.$id),
                'guest_order_url'   => wpdmpp_guest_order_page('id='.$id),
                'order_url_admin'   => admin_url('edit.php?post_type=wpdmpro&page=orders&task=vieworder&id='.$id),
                'img_logo'          => $logo,
                'payment_method'          => str_replace(array("Wpdm_", "WPDM_"), "", $order_det->payment_method),
                'order_total'       => $order_det->currency['sign'].number_format($order_det->total, 2)
            );





            $items      = \WPDMPP\Libs\Order::GetOrderItems($id);
            $allitems   = "<table  class='email' style='width: 100%;border: 0;' cellpadding='0' cellspacing='0'><tr><th>Product Name</th><th>License</th><th style='width:80px;text-align:right'>Price</th></tr>";
            foreach($items as $item){
                $product = get_post($item['pid']);
                $udata = get_userdata($product->post_author);
                $seller_emails[$product->post_author] = $udata->user_email;
                $license = maybe_unserialize($item['license']);
                $license = is_array($license) && isset($license['info'], $license['info']['name'])?$license['info']['name']:" &mdash; ";
                $price = $order_det->currency['sign'].wpdmpp_price_format($item['price'], false, true);
                $_item = "<tr><td><a href='".get_permalink($product->ID)."'>{$product->post_title}</a></td><td>{$license}</td><td style='width:80px;text-align:right'>{$price}</td></tr>";
                $product_by_seller[$product->post_author][] = $_item;
                $allitems .= $_item;
            }
            $allitems .= "</table>";
            $params['items'] = $allitems;

            // to buyer
            if(!$userid)
                \WPDM\Email::send("purchase-confirmation-guest", $params);
            else
                \WPDM\Email::send("purchase-confirmation", $params);



            // to admin
            $params['to_email'] = get_option('admin_email');
            \WPDM\Email::send("sale-notification", $params);

            //to sellers
            if(is_array($seller_emails)) {
                foreach ($seller_emails as $seid => $seller_email) {
                    if(get_option('admin_email') != $seller_email) {
                        $user = get_user_by('email', $seller_email);
                        $prods = implode("<br/>", $product_by_seller[$seid]);
                        $params['items'] = $prods;
                        $params['name'] = $user->display_name;
                        $params['to_email'] = $seller_email;
                        $params['to_seller'] = 1;
                        \WPDM\Email::send("sale-notification", $params);
                    }
                }
            }

        }

        static function renewOrder($id, $sub_id, $email_notify = true, $payment_method = null, $date = null){

            global $wpdb;
            $sub_id = sanitize_text_field($sub_id);
            $sql = "select * from {$wpdb->prefix}ahm_orders where order_id='$id'";
            if($sub_id != '')
                $sql .= " or trans_id='$sub_id'";
            $order_det      = $wpdb->get_row($sql);
            $billing_info   = unserialize($order_det->billing_info);
            $buyer_email    = isset($billing_info['order_email']) ? $billing_info['order_email'] : '';
            $name           = "";
            $settings       = get_option('_wpdmpp_settings');
            $logo           = isset($settings['logo_url'])&&$settings['logo_url']!=""?"<img src='{$settings['logo_url']}' alt='".get_bloginfo('name')."'/>":get_bloginfo('name');
            $expire_date    = get_wpdmpp_option('order_validity_period', 365) > 0? strtotime("+".get_wpdmpp_option('order_validity_period', 0)." days"): 0;
            if($date)
                $expire_date = $date + get_wpdmpp_option('order_validity_period', 365)*86400;
            self::Update(array('order_status' => 'Completed', 'payment_status' => 'Completed', 'auto_renew' => 1, 'expire_date' => $expire_date), $id);
            $date = $date?$date:time();
            $wpdb->insert("{$wpdb->prefix}ahm_order_renews", array('order_id' => $order_det->order_id, 'subscription_id' => $sub_id, 'date' => $date));
            do_action('wpdmpp_order_renewed', $id );


            //return if email notification set to false
            if($email_notify == false) return;

            // send email notifications
            $userid = $order_det->uid;

            if($userid && $buyer_email ==''){
                $user_info      = get_userdata($userid);
                $name           = $user_info->user_login;
                $buyer_email    = $user_info->user_email;
            }

            $params = array(
                'date'              => date(get_option('date_format'),time()),
                'homeurl'           => home_url('/'),
                'sitename'          => get_bloginfo('name'),
                'order_link'        => "<a href='".wpdmpp_orders_page('id='.$id)."'>".wpdmpp_orders_page('id='.$id)."</a>",
                'register_link'     => "<a href='".wpdmpp_orders_page('orderid='.$id)."'>".wpdmpp_orders_page('orderid='.$id)."</a>",
                'name'              => $name,
                'orderid'           => $id,
                'to_email'          => $buyer_email,
                'order_url'         => wpdmpp_orders_page('id='.$id),
                'order_url_admin'   => admin_url('edit.php?post_type=wpdmpro&page=orders&task=vieworder&id='.$id),
                'img_logo'          => $logo
            );


            // to buyer
            \WPDM\Email::send("renew-confirmation", $params);


            $items      = \WPDMPP\Libs\Order::getOrderItems($id);
            $allitems   = "";
            foreach($items as $item){
                $product = get_post($item['pid']);
                $udata = get_userdata($product->post_author);
                $seller_emails[$product->post_author] = $udata->user_email;
                $item = "<a href='".get_permalink($product->ID)."'>{$product->post_title}</a>";
                $product_by_seller[$product->post_author][] = $item;
                $allitems .= $item."<br/>";
            }

            // to admin
            /*
            $params['items'] = $allitems;
            $params['to_email'] = get_option('admin_email');
            \WPDM\Email::send("sale-notification", $params);

            //to sellers
            if(is_array($seller_emails)) {
                foreach ($seller_emails as $seid => $seller_email) {
                    if(get_option('admin_email') != $seller_email) {
                        $prods = implode("<br/>", $product_by_seller[$seid]);
                        $params['items'] = $prods;
                        $params['to_email'] = $seller_email;
                        \WPDM\Email::send("sale-notification", $params);
                    }
                }
            }
            */



        }

        /**
         * @param $id
         */
        static function expireOrder($id, $email_notify = true){

            $order = new Order($id);

            if($order->order_status == 'Expired') return;

            $order->set('order_status', 'Expired');
            $order->set('payment_status', 'Expired');
            if($order->expire_date == 0) {
                $expire_date    = $order->date + (get_wpdmpp_option('order_validity_period', 365)*86400);
                $order->set('expire_date', $expire_date);
            }
            //$_items = maybe_unserialize($order->cart_data);
            $_items      = \WPDMPP\Libs\Order::GetOrderItems($id);
            $order->save();
            $items = "<ul>";
            foreach($_items as $item){
                $product = get_post($item['pid']);
                $license = maybe_unserialize($item['license']);
                $license = is_array($license) && isset($license['info'], $license['info']['name'])?" &mdash; " . $license['info']['name']." License":'';
                $item = "<li><a href='".get_permalink($product->ID)."'>{$product->post_title}{$license}</a></li>";
                $items .= $item;
            }
            $items .= "</ul>";
            if($email_notify){
                $user = get_user_by('id', $order->uid);
                $settings  = get_option('_wpdmpp_settings');
                $settings = maybe_unserialize($settings);
                $logo           = isset($settings['logo_url'])&&$settings['logo_url']!=""?"<img src='{$settings['logo_url']}' alt='".get_bloginfo('name')."'/>":get_bloginfo('name');
                $params = array(
                    'date'              => date(get_option('date_format'),time()),
                    'expire_date'              => date(get_option('date_format'),$order->expire_date),
                    'homeurl'           => home_url('/'),
                    'sitename'          => get_bloginfo('name'),
                    'name'              => $user->display_name,
                    'orderid'           => $id,
                    'order_items'           => $items,
                    'to_email'          => $user->user_email,
                    'order_url'         => wpdmpp_orders_page('id='.$id),
                    'img_logo'          => $logo
                );
                Email::send("order-expire", $params);
            }
        }

        /**
         * @param $id
         */
        static function cancelOrder($id){
            self::update(array('order_status'=>'Cancelled','payment_status'=>'Cancelled'), $id);
        }

        /**
         * @param $cart_data
         * @param $id
         */
        static function updateOrderItems($cart_data, $id){
            global $wpdb;
            $cart_data = maybe_unserialize($cart_data);
            $o = new \WPDMPP\Libs\Order($id);
            if($o->order_status !== 'Processing') return false;
            $time = $o->date;
            $wpdb->query("delete from {$wpdb->prefix}ahm_order_items where oid='$id'");

            if(!empty($cart_data))
                foreach($cart_data as $pid=>$cdt){
                    $variation = get_post_meta($pid,"__wpdm_variation",true);
                    $vrts = array();
                    $coupon = isset($cdt['coupon']) ? $cdt['coupon'] : '';

                    if (is_array($variation)) {
                        foreach ($variation as $key => $value) {
                            foreach ($value as $optionkey => $optionvalue) {
                                if ($optionkey != "vname") {
                                    if (isset($cdt['variation']) && is_array($cdt['variation'])) {
                                        //echo "adfadf";
                                        foreach ($cdt['variation'] as $var) {
                                            //echo  $optionkey;
                                            if ($var == $optionkey) {
                                                $vrts[$optionkey] = array('name' => $optionvalue['option_name'], 'price' => $optionvalue['option_price']);

                                            }
                                        }


                                    }
                                }
                            }
                        }
                    }
                    $coupon_amount = isset($cdt['coupon_amount']) ? $cdt['coupon_amount'] : 0;
                    $role_disc = $cdt['discount_amount'];
                    $site_comm = 0;
                    $sid = get_post($pid)->post_author;
                    $cid = $o->uid;
                    $license = isset($cdt['license'])?maybe_serialize($cdt['license']):'';
                    $wpdb->insert("{$wpdb->prefix}ahm_order_items", array('oid' => $id, 'pid' => $pid, 'license' => $license, 'quantity' => $cdt['quantity'], 'price' => $cdt['price'], 'variations' => serialize($vrts), 'coupon' => $coupon, 'coupon_discount' => floatval($coupon_amount), 'role_discount' => $role_disc, 'site_commission' => $site_comm, 'date' => date("Y-m-d H:m:s", $time), 'year' => date('Y'), 'month' => date('m'), 'day' => date('d'), 'sid' => $sid, 'cid' => $cid));

                    /*
                    if(!isset($cdt['multi']) || $cdt['multi'] == 0) {
                        if (is_array($variation)) {
                            foreach ($variation as $key => $value) {
                                foreach ($value as $optionkey => $optionvalue) {
                                    if ($optionkey != "vname") {
                                        if (isset($cdt['variation']) && is_array($cdt['variation'])) {
                                            //echo "adfadf";
                                            foreach ($cdt['variation'] as $var) {
                                                //echo  $optionkey;
                                                if ($var == $optionkey) {
                                                    $vrts[$optionkey] = array('name' => $optionvalue['option_name'], 'price' => $optionvalue['option_price']);

                                                }
                                            }


                                        }
                                    }
                                }
                            }
                        }
                        $coupon_amount = isset($cdt['coupon_amount']) ? $cdt['coupon_amount'] : 0;
                        $role_disc = $cdt['discount_amount'];
                        $site_comm = 0;
                        $sid = get_post($pid)->post_author;
                        $cid = $o->uid;
                        $license = isset($cdt['license'])?maybe_serialize($cdt['license']):'';
                        $wpdb->insert("{$wpdb->prefix}ahm_order_items", array('oid' => $id, 'pid' => $pid, 'license' => $license, 'quantity' => $cdt['quantity'], 'price' => $cdt['price'], 'variations' => serialize($vrts), 'coupon' => $coupon, 'coupon_discount' => floatval($coupon_amount), 'role_discount' => $role_disc, 'site_commission' => $site_comm, 'date' => date("Y-m-d H:m:s", $time), 'year' => date('Y'), 'month' => date('m'), 'day' => date('d'), 'sid' => $sid, 'cid' => $cid));
                    } else{
                        foreach($cdt['item'] as $mcdt) {
                            $vrts = array();
                            $quantity = (int)$mcdt['quantity']>0?(int)$mcdt['quantity']:1;
                            $role_discount = isset( $mcdt['discount_amount'] ) ? $mcdt['discount_amount'] : 0;
                            $coupon_amount = isset( $mcdt['coupon_amount'] ) ? $mcdt['coupon_amount'] : 0;
                            if (is_array($variation)) {
                                foreach ($variation as $key => $value) {
                                    foreach ($value as $optionkey => $optionvalue) {
                                        if ($optionkey != "vname") {
                                            if (isset($mcdt['variation']) && is_array($mcdt['variation'])) {
                                                //echo "adfadf";
                                                foreach ($mcdt['variation'] as $var) {
                                                    //echo  $optionkey;
                                                    if ($var == $optionkey) {
                                                        $vrts[$optionkey] = array('name' => $optionvalue['option_name'], 'price' => $optionvalue['option_price']);

                                                    }
                                                }


                                            }
                                        }
                                    }
                                }
                            }
                            $coupon = isset($coupon) ? $coupon : '';
                            $coupon_amount = isset($coupon_amount) ? $coupon_amount : 0;
                            $license = isset($cdt['license'])?maybe_serialize($cdt['license']):'';
                            $wpdb->insert("{$wpdb->prefix}ahm_order_items", array('oid' => $id, 'pid' => $pid, 'license' => $license, 'quantity' => $quantity, 'price' => $cdt['price'], 'variations' => serialize($vrts), 'coupon' => $coupon, 'coupon_discount' => floatval($coupon_amount), 'role_discount' => floatval($role_discount)));
                        }
                    }
                    //*/
            }
        }

        static function getOrderItems($id){
            global $wpdb;
            $items = $wpdb->get_results("select * from {$wpdb->prefix}ahm_order_items where oid='{$id}'",ARRAY_A);
            return is_array($items)?$items:array();
        }

        function calcOrderTotal($oid){
            global $wpdb;
            global $current_user;

            $role = is_user_logged_in() && isset($current_user->roles[0]) ? $current_user->roles[0] : 'guest';
            $total = 0;
            $orderdata = $this->GetOrder($oid);
            if(!$orderdata) return 0;
            $order_items = $wpdb->get_results("select * from {$wpdb->prefix}ahm_order_items where oid='{$oid}'",ARRAY_A);;
            $discount1 = 0;

            if(is_array($order_items)){

                foreach($order_items as $item)    {
                    $prices = 0;

                    $pid = $item['pid'];
                    //$item['variation'] = isset($item['variation']) ? maybe_unserialize($item['variation']) : null;
                    $item['variations'] = isset($item['variations']) ? maybe_unserialize($item['variations']) : null;
                    //wpdmdd($item);
                    //$variation = get_post_meta($pid,'__wpdm_variation', true);
                    /*if(isset($item['variation']) && is_array($item['variation']) && is_array($variation)){
                        foreach($variation as $key=>$value){
                            foreach($value as $optionkey=>$optionvalue){
                                if($optionkey!="vname"){
                                    foreach($item['variation'] as $var){
                                        if($var==$optionkey){
                                            $prices+=(double)$optionvalue['option_price'];
                                        }
                                    }
                                }
                            }
                        }
                    }*/
                    if(is_array($item['variations'])){
                        foreach ($item['variations'] as $vari){
                            $prices += (double)$vari['price'];
                        }
                    }
                    if(isset($item['coupon']) && trim($item['coupon'])!=''){
                        $valid_coupon = wpdmpp_check_coupon($pid,$item['coupon']);
                        $item['price'] = (double)$item['price'];
                        if($valid_coupon != 0){
                            $item_total = (($item['price'] + $prices) * $item['quantity']) - $valid_coupon; //(($item['price']+$prices)*$item['quantity']*($valid_coupon/100));
                            $total += $item_total;
                        } else {
                            $item_total = (($item['price']+$prices)*$item['quantity']);
                            $total += $item_total;
                        }
                    }else {
                        $item_total = (($item['price']+$prices)*$item['quantity']);
                        $total += $item_total;

                    }

                    //calculate role discount

                    $role_discount = wpdmpp_role_discount($pid);

                    $discount1 += ( ( $item_total * $role_discount ) / 100);
                    //if($role_discount > 0)
                    //    Session::set('role_discount_'.$oid, true);

                }
            }

            $total = apply_filters('wpdmpp_cart_subtotal',$total);

            $subtotal = $total;


            $tax_summery=$this->wpdmpp_calculate_tax();

            $tax = 0;
            if(count($tax_summery)>0){
                foreach($tax_summery as $taxrow){
                    $tax += $taxrow['rates'];
                }
            }
            $total += $tax;


            $total = $total-$discount1;

            return $total;
        }

        function wpdmpp_calculate_tax($oid = null){
            $taxr = array();
            $settings = maybe_unserialize(get_option('_wpdmpp_settings'));
            $tax_summery = array();
            if(Session::get( 'orderid' )) $order_info=$this->GetOrder(Session::get( 'orderid' ));
            if($oid) $order_info = $this->GetOrder($oid);
            $bdata = unserialize($order_info->billing_info);
            $cart_items = null;
            if(Session::get( 'orderid' )) $cart_items = $this->GetOrderItems(Session::get( 'orderid' ));

            if(isset($settings['tax']['enable']) && $settings['tax']['enable']==1){
                if(is_array($cart_items)){
                    foreach($cart_items as $item){
                        $taxes = 0;
                        $tax_status = "";
                        $tax_class = "";
                        $tax_status = get_post_meta($item['pid'], '__wpdm_taxable', true);

                        $price = wpdmpp_product_price($item['pid']);

                        if($tax_status=="taxable"){

                            if($settings['tax']['tax_rate']){
                                $temp_class = "";
                                $temp_label = "";
                                $taxes = 0;
                                foreach($settings['tax']['tax_rate'] as $key=> $rate){

                                    if($rate['tax_class']==$tax_class){
                                        $taxes = 0;
                                        if(in_array($bdata['shippingin']['country'], $rate['country'])){

                                            $taxes = (($rate['rate']*$price)/100);
                                            if($rate['shipping']==1){
                                                $taxes += (($rate['rate']*$order_info->shipping_cost)/100);
                                            }
                                            //product wise tax
                                            $taxr['label'][$item['pid']][]= $rate['label'];
                                            $taxr['rate'][$item['pid']]+= $taxes;
                                            //class wise tax
                                            $tax_summery[$key]['label'] = $rate['label'];
                                            $tax_summery[$key]['rates'] += $taxes;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return $tax_summery;
        }

        function Load(){

        }

        function getOrder($id) {
            global $wpdb;
            $id = sanitize_text_field($id);
            if($id == '') return false;
            if(strpos($id, "renew")){
                $id = explode("_", $id);
                $id = $id[0];
            }
            $this->oid = $id;
            $this->ID = $id;
            $id = sanitize_text_field($id);
            return $wpdb->get_row("select * from {$wpdb->prefix}ahm_orders where order_id='$id' or trans_id='$id'");
        }

        function getOrders($user_id, $completed_only = false) {
            global $wpdb;
            $user_id = (int)$user_id;
            $os_cond = ($completed_only === true) ? " and ( order_status = 'Completed' or order_status = 'Expired' ) " : '';
            return $wpdb->get_results("select * from {$wpdb->prefix}ahm_orders where uid='$user_id' {$os_cond} order by `order_status` desc, `date` desc");
        }

        function getAllOrders($qry="",$s=0, $l=20) {
            global $wpdb;
            //wpdmdd("select * from {$wpdb->prefix}ahm_orders $qry limit $s,$l");
            return $wpdb->get_results("select * from {$wpdb->prefix}ahm_orders $qry limit $s,$l");
        }

        public static function getPurchasedItems($uid = null){
            global $wpdb, $current_user;
            if(!$uid && is_user_logged_in()) $uid = $current_user->ID;
            if(!$uid) return [];
            $purchased_items = $wpdb->get_results("select p.post_title,oi.*, o.order_status, o.date as order_date from {$wpdb->prefix}ahm_order_items oi,{$wpdb->prefix}ahm_orders o,{$wpdb->prefix}posts p where oi.pid = p.ID and o.order_id = oi.oid and o.uid = {$uid} and o.order_status IN ('Expired', 'Completed') order by `order_date` desc");
            foreach($purchased_items as &$item){
                $files = get_post_meta($item->pid, '__wpdm_files', true);
                foreach ($files as $id => $index) {
                    $item->download_url[$index] = WPDMPremiumPackage::customerDownloadURL($item->pid, $item->oid)."&ind={$id}";
                        //home_url("/?wpdmdl={$item->pid}&oid={$item->oid}&ind=" . $id);
                }
            }
            return $purchased_items;
        }

        /**
         * add to cart using form submit
         */
        static function recalculateItemPrice($cart_data){

            global $wpdb, $post, $wp_query, $current_user;

            $sales_price = 0;

            foreach ($cart_data as $pid => $item) {

                $q = 1;
                $sfiles = isset($item['files']) ? explode(",", $item['files']) : array();
                $license = isset($item['license']) ? $item['license'] : '';
                $license_req = get_post_meta($pid, "__wpdm_enable_license", true);
                $license_prices = get_post_meta($pid, "__wpdm_license", true);
                $license_prices = maybe_unserialize($license_prices);

                $pre_licenses = wpdmpp_get_licenses();
                $files = array();
                $fileinfo = get_post_meta($pid, '__wpdm_fileinfo', true);
                $fileinfo = maybe_unserialize($fileinfo);
                $files_price = 0;

                if (count($sfiles) > 0 && $sfiles[0] != '' && is_array($fileinfo)) {
                    foreach ($sfiles as $findx) {
                        $files[$findx] = $fileinfo[$findx]['price'];
                        if ($license_req == 1 && $license != '' && $fileinfo[$findx]['license_price'][$license] > 0) {
                            $files[$findx] = $fileinfo[$findx]['license_price'][$license];
                        }
                    }
                }
                if ($q < 1) $q = 1;

                $base_price = wpdmpp_product_price($pid);
                if ($license_req == 1 && isset($license_prices[$license]['price']) && $license_prices[$license]['price'] > 0)
                    $base_price = $license_prices[$license]['price'];


                if ((int)get_post_meta($pid, '__wpdm_pay_as_you_want', true) == 0) {

                    // If product id already exist ( Product already added to cart )
                    if (array_key_exists($pid, $cart_data)) {

                        if (!isset($cart_data['variation']) || $cart_data['variation'] == '')
                            $cart_data['variation'] = array();

                        if (isset($cart_data[$pid]['files'])) {
                            $cart_data[$pid]['files'] = maybe_unserialize($cart_data[$pid]['files']);
                            $cart_data[$pid]['files'] += $files;
                        } else
                            $cart_data[$pid]['files'] = $files;
                        $files_price = array_sum($cart_data[$pid]['files']);
                        //$cart_data[$pid]['quantity'] += $q;
                        if (!isset($cart_data[$pid]['price']) || $cart_data[$pid]['price'] == 0) $cart_data[$pid]['price'] = $files_price;
                        else
                            $cart_data[$pid]['price'] = $cart_data[$pid]['price'] > $files_price && $files_price > 0 ? $files_price : $cart_data[$pid]['price'];

                    } else {
                        // product id does not exist in cart. Add to cart as new item
                        $variation = isset($item['variation']) ? wpdm_sanitize_array($item['variation']) : array();
                        $files_price = array_sum($files);
                        $base_price = $files_price > 0 && $files_price < $base_price ? $files_price : $base_price;
                        $cart_data[$pid] = array('quantity' => $q, 'variation' => $variation, 'price' => $base_price, 'files' => $files);

                    }

                }

                $lic_info = isset($pre_licenses[$license]) ? $pre_licenses[$license] : '';
                $license_det = array('id' => $license, 'info' => $lic_info);
                $cart_data[$pid]['license'] = $license_det;

            }

            return $cart_data;

        }

        public static function recalculateTotal($oid){
            global $wpdb;
            $total = 0;
            $orderdata = $wpdb->get_row("select * from {$wpdb->prefix}ahm_orders where order_id='$oid' or trans_id='$oid'");
            if(current_user_can('manage_options') || $orderdata->uid === get_current_user_id()){
                $cart_items = unserialize($orderdata->cart_data);
                $discount1 = 0;

                if(is_array($cart_items)){

                    $cart_items = self::recalculateItemPrice($cart_items);

                    foreach($cart_items as $pid => $item)    {
                        $prices = 0;
                        $license = isset($item['license'])?maybe_unserialize($item['license']):null;
                        $license = is_array($license) && isset($license['id']) ? $license['id']: '';
                        $item['price'] = wpdmpp_product_price($pid, $license);
                        $variation = get_post_meta($pid,'__wpdm_variation', true);
                        if(isset($item['variation']) && is_array($item['variation']) && is_array($variation)){
                            foreach($variation as $key=>$value){
                                foreach($value as $optionkey=>$optionvalue){
                                    if($optionkey!="vname"){
                                        foreach($item['variation'] as $var){
                                            if($var==$optionkey){
                                                $prices+=(double)$optionvalue['option_price'];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if(isset($item['coupon']) && trim($item['coupon'])!=''){
                            $valid_coupon = wpdmpp_check_coupon($pid,$item['coupon']);

                            if($valid_coupon != 0){
                                $item_total = (($item['price']+$prices)*$item['quantity']) - $valid_coupon; //(($item['price']+$prices)*$item['quantity']*($valid_coupon/100));
                                $total += $item_total;
                            } else {
                                $item_total = (($item['price']+$prices)*$item['quantity']);
                                $total += $item_total;
                            }
                        }else {
                            $item_total = (($item['price']+$prices)*$item['quantity']);
                            $total += $item_total;

                        }

                        //calculate role discount

                        $role_discount = wpdmpp_role_discount($pid);

                        $discount1 += ( ( $item_total * $role_discount ) / 100);
                        //if($role_discount > 0)
                        //    Session::set('role_discount_'.$oid, true);

                    }
                }
                self::update(array('total' => $total), $oid);
            } else
                $total = $orderdata->total;

            return $total;
        }

        function totalOrders($qry=''){
            global $wpdb;
            return $wpdb->get_var("select count(*) from {$wpdb->prefix}ahm_orders $qry");
        }

        function delete($id){
            global $wpdb;
            return $wpdb->query("delete from {$wpdb->prefix}ahm_orders where order_id='$id'");
        }


}
