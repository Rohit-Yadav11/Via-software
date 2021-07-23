<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

use WPDM\Session;

/**
 * @usage Shows a requested cart. Hooked to 'wp_loaded'
 */
function wpdmpp_load_saved_cart(){
    if( isset( $_REQUEST['savedcart'] ) ) {
        $cartid = sanitize_text_field($_REQUEST['savedcart']);
        $cartfile = WPDM_CACHE_DIR.'/saved-cart-'.$cartid.'.txt';
        $saved_cart_data = '';

        if(file_exists($cartfile)) $saved_cart_data = file_get_contents($cartfile);
        $saved_cart_data = \WPDM\libs\Crypt::Decrypt($saved_cart_data, true);

        $coupon_data = null;
        if(is_array($saved_cart_data) && count($saved_cart_data) > 0) {
            //wpdmdd($saved_cart_data);
            if(isset($saved_cart_data['cartitems'])){
                $coupon_data = $saved_cart_data['coupon'];
                $saved_cart_data = $saved_cart_data['cartitems'];

            }
            wpdmpp_update_cart_data($saved_cart_data);

            if($coupon_data){
                $cart_id = wpdmpp_cart_id();
                update_option($cart_id."_coupon", $coupon_data);
            }
        }

        wpdmpp_redirect(wpdmpp_cart_page());
    }
}

/**
 * @usage Shows Paymnet Options on checkout step. Hooked to 'init'
 */
function wpdmpp_load_payment_methods(){
    if( !wpdm_is_ajax() || !isset($_REQUEST['wpdmpp_load_pms'] ) ) return;
    $settings = get_option('_wpdmpp_settings');
    $guest_checkout = (isset($settings['guest_checkout']) && $settings['guest_checkout'] == 1) ? 1 : 0;
    if(!is_user_logged_in() && !$guest_checkout) die('You are not logged in!');
    $payment_html = "";
    include_once(WPDMPP_TPL_DIR . "checkout-cart/checkout.php");
    echo $payment_html;
    die();
}

/**
 * Checking product coupon whether valid or not
 *
 * @param $pid
 * @param $coupon
 * @return int
 */
function wpdmpp_check_coupon($pid, $coupon){
    return \WPDMPP\Libs\CouponCodes::validate_coupon($coupon, $pid);
}

/**
 * add to cart using form submit
 */
function wpdmpp_add_to_cart(){
    if( isset( $_REQUEST['addtocart']) && intval($_REQUEST['addtocart']) > 0  && get_post_type($_REQUEST['addtocart']) == 'wpdmpro') {
        global $wpdb, $post, $wp_query, $current_user;
        $settings = maybe_unserialize(get_option('_wpdmpp_settings'));

        $pid = (int)$_REQUEST['addtocart'];

        $pid = apply_filters("wpdmpp_add_to_cart", $pid);

        if($pid <= 0) return;
        $sales_price = 0;
        $cart_data = wpdmpp_get_cart_data();

        if(isset($cart_data[$pid])) unset($cart_data[$pid]);

        $q = isset($_REQUEST['quantity']) ? intval($_REQUEST['quantity']) : 1;
        $sfiles = isset($_REQUEST['files']) ? explode(",", $_REQUEST['files']) : array();
        $license = isset($_REQUEST['license']) ? sanitize_text_field($_REQUEST['license']) : '';
        $license_req = get_post_meta($pid, "__wpdm_enable_license", true);
        $license_prices = get_post_meta($pid, "__wpdm_license", true);
        $license_prices = maybe_unserialize($license_prices);

        $pre_licenses = wpdmpp_get_licenses();
        $files = array();
        $fileinfo = get_post_meta($pid, '__wpdm_fileinfo', true);
        $fileinfo = maybe_unserialize($fileinfo);
        $files_price = 0;

        if(count($sfiles) > 0 && $sfiles[0] != '' && is_array($fileinfo)) {
            foreach ($sfiles as $findx) {
                $files[$findx] = $fileinfo[$findx]['price'];
                if ($license_req == 1 && $license != '' && $fileinfo[$findx]['license_price'][$license] > 0) {
                    $files[$findx] = $fileinfo[$findx]['license_price'][$license];
                }
            }
        }
        if($q < 1) $q = 1;

        $base_price = wpdmpp_product_price($pid);
        if($license_req == 1 && isset($license_prices[$license]['price']) && $license_prices[$license]['price'] > 0)
            $base_price = $license_prices[$license]['price'];

        if( ! isset( $_REQUEST['variation'] ) ) {
            $_REQUEST['variation'] = "";
        }

        if((int)get_post_meta($pid, '__wpdm_pay_as_you_want', true) == 0) {

            // If product id already exist ( Product already added to cart )
            if (array_key_exists($pid, $cart_data)) {

                if (isset($cart_data[$pid]['multi']) && $cart_data[$pid]['multi'] == 1) {
                    $product_data = $cart_data[$pid]['item'];
                    $check = false;
                    foreach ($product_data as $key => $item):

                        //Check same variation exist or not
                        if (wpdmpp_array_diff($item['variation'], $_REQUEST['variation']) == true) {

                            //just incremnet qunatity value
                            $cart_data[$pid]['item'][$key]['quantity'] += $q;
                            $cart_data[$pid]['quantity'] += $q;
                            $check = true;
                            break;
                        }
                    endforeach;

                    if ($check == false) {

                        //Same variation does not exist. Add this item as new item
                        $cart_data[$pid]['item'][] = array(
                            'quantity' => $q,
                            'variation' => isset($_POST['variation']) ? wpdm_sanitize_array($_POST['variation']) : array()
                        );
                        $cart_data[$pid]['quantity'] += $q;
                    }

                    if (isset($cart_data[$pid]['files'])) {
                        $cart_data[$pid]['files'] = maybe_unserialize($cart_data[$pid]['files']);
                        $cart_data[$pid]['files'] += $files;
                    } else
                        $cart_data[$pid]['files'] = $files;
                    $files_price = array_sum($cart_data[$pid]['files']);
                    //dd($files);
                    $base_price = $files_price > 0 ? $files_price : $base_price;
                } else {

                    if (!isset($_REQUEST['variation']) || $_REQUEST['variation'] == '')
                        $_REQUEST['variation'] = array();

                    if (wpdmpp_array_diff($cart_data[$pid]['variation'], $_REQUEST['variation']) == true) {
                        //no change in variation

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
                        //change in variation
                        $old_qty = $cart_data[$pid]['quantity'];
                        $old_variation = $cart_data[$pid]['variation'];
                        $old_files = isset($cart_data[$pid]['files']) ? $cart_data[$pid]['files'] : array();
                        $coupon = isset($cart_data[$pid]['coupon']) ? $cart_data[$pid]['coupon'] : '';
                        $coupon_amount = isset($cart_data[$pid]['coupon_amount']) ? $cart_data[$pid]['coupon_amount'] : '';
                        $discount_amount = isset($cart_data[$pid]['discount_amount']) ? $cart_data[$pid]['discount_amount'] : '';
                        $prices = isset($cart_data[$pid]['prices']) ? $cart_data[$pid]['prices'] : '';
                        $variations = isset($cart_data[$pid]['variations']) ? $cart_data[$pid]['variations'] : '';
                        $new_data = array(
                            'quantity' => $q,
                            'files' => $files,
                            'variation' => isset($_POST['variation']) ? wpdm_sanitize_array($_POST['variation']) : array(),
                        );

                        $cart_data[$pid] = array();
                        $cart_data[$pid]['multi'] = 1;
                        $cart_data[$pid]['quantity'] = $q + $old_qty;
                        $cart_data[$pid]['price'] = $base_price;
                        $cart_data[$pid]['coupon'] = $coupon;
                        $cart_data[$pid]['item'][] = array(
                            'quantity' => $old_qty,
                            'variation' => $old_variation,
                            'files' => $old_files,
                        );
                        $cart_data[$pid]['item'][] = $new_data;
                    }
                }
            } else {
                // product id does not exist in cart. Add to cart as new item
                $variation = isset($_POST['variation']) ? wpdm_sanitize_array($_POST['variation']) : array();
                $files_price = array_sum($files);
                $base_price = $files_price > 0 && $files_price < $base_price ? $files_price : $base_price;
                $cart_data[$pid] = array('quantity' => $q, 'variation' => $variation, 'price' => $base_price, 'files' => $files);

            }

        } else {

            //Condition for as you want to pay
            $base_price         = isset($_REQUEST['iwantopay'])&&(double)$_REQUEST['iwantopay'] >= $base_price ?(double)$_REQUEST['iwantopay']:$base_price;
            $variation          = isset($_POST['variation']) ? wpdm_sanitize_array($_POST['variation']) : array();
            $cart_data[$pid]    = array('quantity' => $q, 'variation' => $variation, 'price' => $base_price, 'files' => array());
        }

        $lic_info = isset($pre_licenses[$license])?$pre_licenses[$license]: '';
        $license_det = array('id' => $license, 'info' => $lic_info);
        $cart_data[$pid]['license'] = $license_det;


        // Update cart data
        wpdmpp_update_cart_data($cart_data);

        // Calculate all discounts (role based, coupon codes, sales price discount )
        wpdmpp_calculate_discount();

        $settings = get_option('_wpdmpp_settings');

        /* Check if current request is AJAX  */
        if( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) {
            echo wpdmpp_cart_page();
            die();
        }

        if( $settings['wpdmpp_after_addtocart_redirect'] == 1 ) {
            header( "location: ".wpdmpp_cart_page() );
        }
        else header( "location: ".$_SERVER['HTTP_REFERER'] );
        die();
    }
}


/**
 * add to cart using form submit
 */
function wpdmpp_buynow(){
    if( isset( $_REQUEST['buynow']) && intval($_REQUEST['buynow']) > 0  && get_post_type((int)$_REQUEST['buynow']) == 'wpdmpro') {
        global $wpdb, $post, $wp_query, $current_user;
        $settings = maybe_unserialize(get_option('_wpdmpp_settings'));

        $pid = (int)$_REQUEST['buynow'];

        $pid = apply_filters("wpdmpp_buynow", $pid);

        if($pid <= 0) return;
        $sales_price = 0;
        $cart_data = wpdmpp_get_cart_data();

        $q = isset($_REQUEST['quantity']) ? intval($_REQUEST['quantity']) : 1;
        $sfiles = isset($_REQUEST['files']) ? explode(",", $_REQUEST['files']) : array();
        $license = isset($_REQUEST['license']) ? sanitize_text_field($_REQUEST['license']) : '';
        $license_req = get_post_meta($pid, "__wpdm_enable_license", true);
        $license_prices = get_post_meta($pid, "__wpdm_license", true);
        $license_prices = maybe_unserialize($license_prices);

        $pre_licenses = wpdmpp_get_licenses();
        $files = array();
        $fileinfo = get_post_meta($pid, '__wpdm_fileinfo', true);
        $fileinfo = maybe_unserialize($fileinfo);
        $files_price = 0;

        if(count($sfiles) > 0 && $sfiles[0] != '' && is_array($fileinfo)) {
            foreach ($sfiles as $findx) {
                $files[$findx] = $fileinfo[$findx]['price'];
                if ($license_req == 1 && $license != '' && $fileinfo[$findx]['license_price'][$license] > 0) {
                    $files[$findx] = $fileinfo[$findx]['license_price'][$license];
                }
            }
        }
        if($q < 1) $q = 1;

        $base_price = wpdmpp_product_price($pid);
        if($license_req == 1 && isset($license_prices[$license]['price']) && $license_prices[$license]['price'] > 0)
            $base_price = $license_prices[$license]['price'];

        if( ! isset( $_REQUEST['variation'] ) ) {
            $_REQUEST['variation'] = "";
        }

        // If product id already exist ( Product already added to cart )
        if( array_key_exists( $pid, $cart_data ) ) {
            if( isset( $cart_data[$pid]['multi'] ) && $cart_data[$pid]['multi'] == 1){
                $product_data = $cart_data[$pid]['item'];
                $check = false;
                foreach ($product_data as $key => $item):

                    //Check same variation exist or not
                    if(wpdmpp_array_diff($item['variation'], $_REQUEST['variation']) == true){

                        //just incremnet qunatity value
                        $cart_data[$pid]['item'][$key]['quantity'] += $q;
                        $cart_data[$pid]['quantity'] += $q;
                        $check = true;
                        break;
                    }
                endforeach;

                if($check == false){

                    //Same variation does not exist. Add this item as new item
                    $cart_data[$pid]['item'][] = array(
                        'quantity'=>$q,
                        'variation'=> isset($_POST['variation'])?wpdm_sanitize_array($_POST['variation']):array()
                    );
                    $cart_data[$pid]['quantity'] += $q;
                }

                if(isset($cart_data[$pid]['files'])){
                    $cart_data[$pid]['files'] = maybe_unserialize($cart_data[$pid]['files']);
                    $cart_data[$pid]['files'] += $files;
                } else
                    $cart_data[$pid]['files'] = $files;
                $files_price = array_sum($cart_data[$pid]['files']);
                //dd($files);
                $base_price = $files_price > 0 ? $files_price:$base_price;
            }
            else {

                if( ! isset( $_REQUEST['variation'] ) || $_REQUEST['variation'] == '' )
                    $_REQUEST['variation'] = array();

                if( wpdmpp_array_diff( $cart_data[$pid]['variation'], $_REQUEST['variation'] ) == true ) {
                    //no change in variation

                    if(isset($cart_data[$pid]['files'])){
                        $cart_data[$pid]['files'] = maybe_unserialize($cart_data[$pid]['files']);
                        $cart_data[$pid]['files'] += $files;
                    } else
                        $cart_data[$pid]['files'] = $files;
                    $files_price = array_sum($cart_data[$pid]['files']);
                    //$cart_data[$pid]['quantity'] += $q;
                    if(!isset($cart_data[$pid]['price']) || $cart_data[$pid]['price'] == 0) $cart_data[$pid]['price'] = $files_price;
                    else
                        $cart_data[$pid]['price'] = $cart_data[$pid]['price'] > $files_price && $files_price > 0?$files_price:$cart_data[$pid]['price'];
                }
                else {
                    //change in variation
                    $old_qty = $cart_data[$pid]['quantity'];
                    $old_variation = $cart_data[$pid]['variation'];
                    $old_files = isset($cart_data[$pid]['files'])?$cart_data[$pid]['files']:array();
                    $coupon = isset($cart_data[$pid]['coupon']) ? $cart_data[$pid]['coupon'] : '';
                    $coupon_amount = isset($cart_data[$pid]['coupon_amount']) ? $cart_data[$pid]['coupon_amount'] : '';
                    $discount_amount = isset($cart_data[$pid]['discount_amount']) ? $cart_data[$pid]['discount_amount'] : '';
                    $prices = isset($cart_data[$pid]['prices']) ? $cart_data[$pid]['prices'] : '';
                    $variations = isset($cart_data[$pid]['variations']) ? $cart_data[$pid]['variations'] : '';
                    $new_data = array(
                        'quantity'  => $q,
                        'files'  => $files,
                        'variation' => isset($_POST['variation'])?wpdm_sanitize_array($_POST['variation']):array(),
                    );

                    $cart_data[$pid] = array();
                    $cart_data[$pid]['multi'] = 1;
                    $cart_data[$pid]['quantity'] = $q+$old_qty;
                    $cart_data[$pid]['price'] = $base_price;
                    $cart_data[$pid]['coupon'] = $coupon;
                    $cart_data[$pid]['item'][] = array(
                        'quantity'  => $old_qty,
                        'variation' => $old_variation,
                        'files' => $old_files,
                    );
                    $cart_data[$pid]['item'][] = $new_data;
                }
            }
        } else {
            // product id does not exist in cart. Add to cart as new item
            $variation = isset( $_POST['variation'] ) ? wpdm_sanitize_array($_POST['variation']) : array();
            $files_price = array_sum($files);
            $base_price = $files_price > 0 && $files_price < $base_price ? $files_price:$base_price;
            $cart_data[$pid] = array( 'quantity' => $q,'variation' => $variation, 'price' => $base_price, 'files' => $files );
        }

        // Update cart data
        wpdmpp_update_cart_data($cart_data);

        // Calculate all discounts (role based, coupon codes, sales price discount )
        wpdmpp_calculate_discount();

        $settings = get_option('_wpdmpp_settings');

        /* Check if current request is AJAX  */
        if( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ) {
            echo wpdmpp_cart_page();
            die();
        }

        if( $settings['wpdmpp_after_addtocart_redirect'] == 1 ) {
            header( "location: ".wpdmpp_cart_page() );
        }
        else header( "location: ".$_SERVER['HTTP_REFERER'] );
        die();
    }
}

/**
 * add to cart from url call
 *
 * https://your-site.com/?add_to_cart=product_id&quantity=product_quantity&license=license_id&variation_group=group_id&variation_id=variation_id
 */
function wpdmpp_add_to_cart_ucb(){

    if( isset( $_GET['add_to_cart']) && intval($_GET['add_to_cart']) > 0  && get_post_type($_GET['add_to_cart']) == 'wpdmpro') {
        global $wpdb, $post, $wp_query, $current_user;
        $settings = maybe_unserialize(get_option('_wpdmpp_settings'));

        $pid = (int)$_GET['add_to_cart'];

        if( isset($_GET['variation_group']) && isset($_GET['variation_id']) )
            $_GET['variation'] = array($_GET['variation_group'] => sanitize_text_field($_GET['variation_id']));

        $pid = apply_filters("wpdmpp_add_to_cart", $pid);

        if($pid <= 0) return;
        $sales_price = 0;
        $cart_data = wpdmpp_get_cart_data();

        if(isset($cart_data[$pid])) unset($cart_data[$pid]);

        $q = isset($_GET['quantity']) ? intval($_GET['quantity']) : 1;
        $sfiles = isset($_GET['files']) ? explode(",", $_GET['files']) : array();
        $license = isset($_GET['license']) ? sanitize_text_field($_GET['license']) : '';
        $license_req = get_post_meta($pid, "__wpdm_enable_license", true);
        $license_prices = get_post_meta($pid, "__wpdm_license", true);
        $license_prices = maybe_unserialize($license_prices);

        $pre_licenses = wpdmpp_get_licenses();
        $files = array();
        $fileinfo = get_post_meta($pid, '__wpdm_fileinfo', true);
        $fileinfo = maybe_unserialize($fileinfo);
        $files_price = 0;

        if(count($sfiles) > 0 && $sfiles[0] != '' && is_array($fileinfo)) {
            foreach ($sfiles as $findx) {
                $files[$findx] = $fileinfo[$findx]['price'];
                if ($license_req == 1 && $license != '' && $fileinfo[$findx]['license_price'][$license] > 0) {
                    $files[$findx] = $fileinfo[$findx]['license_price'][$license];
                }
            }
        }
        if($q < 1) $q = 1;

        $base_price = wpdmpp_product_price($pid);
        if($license_req == 1 && isset($license_prices[$license]['price']) && $license_prices[$license]['price'] > 0)
            $base_price = $license_prices[$license]['price'];

        if( ! isset( $_GET['variation'] ) ) {
            $_GET['variation'] = "";
        }

        if((int)get_post_meta($pid, '__wpdm_pay_as_you_want', true) == 0) {

            // If product id already exist ( Product already added to cart )
            if (array_key_exists($pid, $cart_data)) {

                if (isset($cart_data[$pid]['multi']) && $cart_data[$pid]['multi'] == 1) {
                    $product_data = $cart_data[$pid]['item'];
                    $check = false;
                    foreach ($product_data as $key => $item):

                        //Check same variation exist or not
                        if (wpdmpp_array_diff($item['variation'], $_GET['variation']) == true) {

                            //just incremnet qunatity value
                            $cart_data[$pid]['item'][$key]['quantity'] += $q;
                            $cart_data[$pid]['quantity'] += $q;
                            $check = true;
                            break;
                        }
                    endforeach;

                    if ($check == false) {

                        //Same variation does not exist. Add this item as new item
                        $cart_data[$pid]['item'][] = array(
                            'quantity' => $q,
                            'variation' => isset($_GET['variation']) ? wpdm_sanitize_array($_GET['variation']) : array()
                        );
                        $cart_data[$pid]['quantity'] += $q;
                    }

                    if (isset($cart_data[$pid]['files'])) {
                        $cart_data[$pid]['files'] = maybe_unserialize($cart_data[$pid]['files']);
                        $cart_data[$pid]['files'] += $files;
                    } else
                        $cart_data[$pid]['files'] = $files;
                    $files_price = array_sum($cart_data[$pid]['files']);
                    //dd($files);
                    $base_price = $files_price > 0 ? $files_price : $base_price;
                } else {


                    if (!isset($_GET['variation']) || $_GET['variation'] == '')
                        $_GET['variation'] = array();

                    if (wpdmpp_array_diff($cart_data[$pid]['variation'], $_GET['variation']) == true) {
                        //no change in variation

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
                        //change in variation
                        $old_qty = $cart_data[$pid]['quantity'];
                        $old_variation = $cart_data[$pid]['variation'];
                        $old_files = isset($cart_data[$pid]['files']) ? $cart_data[$pid]['files'] : array();
                        $coupon = isset($cart_data[$pid]['coupon']) ? $cart_data[$pid]['coupon'] : '';
                        $coupon_amount = isset($cart_data[$pid]['coupon_amount']) ? $cart_data[$pid]['coupon_amount'] : '';
                        $discount_amount = isset($cart_data[$pid]['discount_amount']) ? $cart_data[$pid]['discount_amount'] : '';
                        $prices = isset($cart_data[$pid]['prices']) ? $cart_data[$pid]['prices'] : '';
                        $variations = isset($cart_data[$pid]['variations']) ? $cart_data[$pid]['variations'] : '';
                        $new_data = array(
                            'quantity' => $q,
                            'files' => $files,
                            'variation' => isset($_GET['variation']) ? wpdm_sanitize_array($_GET['variation']) : array(),
                        );

                        $cart_data[$pid] = array();
                        $cart_data[$pid]['multi'] = 1;
                        $cart_data[$pid]['quantity'] = $q + $old_qty;
                        $cart_data[$pid]['price'] = $base_price;
                        $cart_data[$pid]['coupon'] = $coupon;
                        $cart_data[$pid]['item'][] = array(
                            'quantity' => $old_qty,
                            'variation' => $old_variation,
                            'files' => $old_files,
                        );
                        $cart_data[$pid]['item'][] = $new_data;
                    }
                }
            } else {
                // product id does not exist in cart. Add to cart as new item
                $variation = isset($_GET['variation']) ? wpdm_sanitize_array($_GET['variation']) : array();
                $files_price = array_sum($files);
                $base_price = $files_price > 0 && $files_price < $base_price ? $files_price : $base_price;
                $cart_data[$pid] = array('quantity' => $q, 'variation' => $variation, 'price' => $base_price, 'files' => $files);
            }

        } else {

            //Condition for as you want to pay
            $base_price         = isset($_GET['iwantopay'])&&$_GET['iwantopay'] >= $base_price ?(double)$_GET['iwantopay']:$base_price;
            $variation          = isset($_GET['variation']) ? wpdm_sanitize_array($_GET['variation']) : array();
            $cart_data[$pid]    = array('quantity' => $q, 'variation' => $variation, 'price' => $base_price, 'files' => array());
        }

        // Update cart data
        wpdmpp_update_cart_data($cart_data);

        // Calculate all discounts (role based, coupon codes, sales price discount )
        wpdmpp_calculate_discount();

        $settings = get_option('_wpdmpp_settings');

        if( isset($settings['wpdmpp_after_addtocart_redirect']) && $settings['wpdmpp_after_addtocart_redirect'] == 1 ) {
            header( "location: ".wpdmpp_cart_page() );
            die();
        }
    }
}

/**
 * Remove a cart entry
 */
function wpdmpp_remove_cart_item(){

    if( !isset($_REQUEST['wpdmpp_remove_cart_item']) || $_REQUEST['wpdmpp_remove_cart_item'] <= 0 ) return;
    $cart_data = wpdmpp_get_cart_data();
    $rciid = (int)$_REQUEST['wpdmpp_remove_cart_item'];
    if( isset( $_REQUEST['item_id'] ) ){
        unset($cart_data[$rciid]['item'][(int)$_REQUEST['item_id']]);
        if( empty($cart_data[$rciid]['item']) ) {
            unset($cart_data[$rciid]);
        }
    }
    else{
        unset($cart_data[$rciid]);
    }
    wpdmpp_update_cart_data($cart_data);

    wpdmpp_calculate_discount();

    $ret['cart_subtotal'] = wpdmpp_get_cart_subtotal();
    $ret['cart_data'] = wpdmpp_get_cart_data();
    $cart_id = wpdmpp_cart_id();
    $coupon = get_option($cart_id."_coupon");
    if(isset($coupon['code']) && $coupon['code'] != ''){
        delete_option($cart_id."_coupon");
        $discount = \WPDMPP\Libs\CouponCodes::validate_coupon($coupon['code']);
        $ret['cart_coupon'] = $coupon['code'];
        $ret['cart_discount'] = $ret['cart_coupon_discount'] = $discount;
        if($discount > 0) update_option($cart_id."_coupon", array('code' => $coupon['code'], 'discount' => $discount));
    }

    $ret['cart_discount'] = wpdmpp_get_cart_discount();
    $ret['cart_total'] = wpdmpp_get_cart_total();

    $ret['cart_tax'] = number_format((double)str_replace(',', '', wpdmpp_get_cart_tax()), 2);

    die(json_encode($ret));
}

/**
 * Update Cart items
 */
function wpdmpp_update_cart(){


    if (!isset($_REQUEST['wpdmpp_update_cart']) || (isset($_REQUEST['wpdmpp_update_cart']) && $_REQUEST['wpdmpp_update_cart'] <= 0)) return;


    $data = wpdm_sanitize_array($_POST['cart_items']);
    $cart_data = wpdmpp_get_cart_data(); //get previous cart data


    foreach ( $cart_data as $pid => $cdt ){
        if( ! $pid || get_post_type($pid) != 'wpdmpro' ) {
            unset( $cart_data[$pid] );
            continue;
        }
        if(isset($data[$pid]['coupon']) && trim($data[$pid]['coupon']) != '') {
            $cart_data[$pid]['coupon'] = stripslashes($data[$pid]['coupon']);
        }
        else {
            unset($cart_data[$pid]['coupon']);
        }

        if( isset( $data[$pid]['item'] ) ) {

            foreach ($data[$pid]['item'] as $key => $val){

                if(isset($val['quantity'])) {
                    if($val['quantity'] < 1 ) $val['quantity'] = 1;
                    $cart_data[$pid]['item'][$key]['quantity'] = $val['quantity'];
                }

                if(isset($cart_data[$pid]['item'][$key]['coupon_amount'])) {
                    unset($cart_data[$pid]['item'][$key]['coupon_amount']);
                }

                if(isset($cart_data[$pid]['item'][$key]['discount_amount'])) {
                    unset($cart_data[$pid]['item'][$key]['discount_amount']);
                }
            }
        } else {

            if( isset($data[$pid]['quantity'] ) ) {
                if( $data[$pid]['quantity'] < 1 ) $data[$pid]['quantity'] = 1;
                $cart_data[$pid]['quantity'] = $data[$pid]['quantity'];
            }

            if(isset($cart_data[$pid]['coupon_amount'])) {
                unset($cart_data[$pid]['coupon_amount']);
            }
        }
    }

    wpdmpp_update_cart_data($cart_data);

    wpdmpp_calculate_discount();

    $ret['cart_subtotal'] = wpdmpp_get_cart_subtotal();
    $ret['cart_discount'] = wpdmpp_get_cart_discount();
    $ret['cart_total'] = wpdmpp_get_cart_total();
    $ret['cart_data'] = wpdmpp_get_cart_data();
    $cart_id = wpdmpp_cart_id();
    delete_option($cart_id."_coupon");
    if(wpdm_query_var('coupon_code') != ''){
        $discount = \WPDMPP\Libs\CouponCodes::validate_coupon(wpdm_query_var('coupon_code'));
        $ret['cart_coupon'] = wpdm_query_var('coupon_code');
        $ret['cart_coupon_discount'] = $discount;
        if($discount > 0) update_option($cart_id."_coupon", array('code' => wpdm_query_var('coupon_code'), 'discount' => $discount));
    }
    if( wpdm_is_ajax() ) {
        die(json_encode($ret));
    }
    header("location: ".wpdmpp_cart_page());
    die();
}

/**
 * @return bool|mixed|null|string
 */
function wpdmpp_get_cart_coupon(){
    $cart_id = wpdmpp_cart_id();
    $cart_coupon = get_option($cart_id."_coupon", null);
    if(!isset($cart_coupon['code']) || $cart_coupon['code'] == '') { delete_option($cart_id."_coupon"); $cart_coupon = null; }
    return $cart_coupon;
}

/**
 * Returns Cart ID
 * @return null|string
 */
function wpdmpp_cart_id(){
    global $current_user;
    $cart_id = null;
    if(is_user_logged_in()){
        $cart_id = $current_user->ID."_cart";
    } else {
        $cart_id = md5(wpdm_get_client_ip())."_cart";
    }

    return $cart_id;
}

function wpdmpp_clear_user_cart($uid){
    $cart_id = $uid."_cart";
    delete_option($cart_id);
    delete_option($cart_id."_coupon");
}

/**
 * Returns cart data
 * @return array|mixed
 */
function wpdmpp_get_cart_data(){

    global $current_user;

    $cart_id = wpdmpp_cart_id();

    $cart_data = maybe_unserialize(get_option($cart_id));

    //adjust cart id after user log in
    if( is_user_logged_in() && !$cart_data ){
        $cart_id = md5(wpdm_get_client_ip())."_cart";
        $cart_data = maybe_unserialize(get_option($cart_id));
        delete_option($cart_id);
        $cart_id = $current_user->ID."_cart";
        update_option($cart_id, $cart_data);
    }

    return $cart_data ? $cart_data : array();
}

/**
 * @usage Update cart data
 * @param $cart_data
 * @return bool
 */
function wpdmpp_update_cart_data($cart_data){
    global $current_user;

    $cart_id = wpdmpp_cart_id();

    $cart_data = update_option($cart_id, $cart_data);
    return $cart_data;
}

/**
 * Returns cart items
 * @return array|mixed
 */
function wpdmpp_get_cart_items(){
    global $current_user, $wpdb;
    $cart_data = wpdmpp_get_cart_data();
    return ($cart_data);
}

/**
 * Returns cart items
 * @return array|mixed
 */
function wpdmpp_is_cart_empty(){
    global $current_user, $wpdb;
    $cart_data = wpdmpp_get_cart_data();
    $cart_data = maybe_unserialize($cart_data);
    return count($cart_data) > 0?false:true;
}

/**
 * Calculate total cart discounts (rolse,sales,coupons)
 */
function wpdmpp_calculate_discount(){
    global $current_user;
    $role                   = is_user_logged_in() && isset($current_user->roles[0])? $current_user->roles[0] : 'guest';
    $discount_r             = 0;
    $cart_items             = wpdmpp_get_cart_items();
    $total                  = 0;
    $currency_sign          = wpdmpp_currency_sign();
    $currency_sign_before   = wpdmpp_currency_sign_position() == 'before' ? $currency_sign : '';
    $currency_sign_after    = wpdmpp_currency_sign_position() == 'after' ? $currency_sign : '';

    if(is_array($cart_items)){
        foreach($cart_items as $pid => $item)    {

            if(!is_array($cart_items[$pid])) $cart_items[$pid] = array();
            $cart_items[$pid]['ID'] = $pid;
            $cart_items[$pid]['post_title'] = get_the_title($pid);
            $prices = 0;
            $variations = "";
            $svariation = array();
            $lvariation = array();
            $lvariations = array();
            $lprices = array();
            //$discount = get_post_meta($pid,"__wpdm_discount",true);
            $base_price = get_post_meta($pid,"__wpdm_base_price",true);
            $sales_price = wpdmpp_sales_price($pid);
            $price_variation = get_post_meta($pid,"__wpdm_price_variation",true);
            $variation = get_post_meta($pid,"__wpdm_variation",true);

            if(is_array($variation) && count($variation)>0){
            foreach($variation as $key=>$value){
                foreach($value as $optionkey=>$optionvalue){
                    if($optionkey!="vname" && $optionkey != 'multiple'){

                        if(isset($item['multi']) && ($item['multi'] == 1)){

                            foreach ($item['item'] as $a => $b) { //different variations, $b is single variation contain variation and quantity

                                $lprices[$a] = isset($lprices[$a])?$lprices[$a]:0;
                                if(is_array($b['variation'])):
                                    foreach ($b['variation'] as $c):
                                        if($c == $optionkey) {
                                            $lprices[$a] += $optionvalue['option_price'];
                                            $lvariation[$a][] = $optionvalue['option_name'].": ".($optionvalue['option_price']>0?'+':'').$currency_sign_before.number_format(floatval($optionvalue['option_price']),2,".","").$currency_sign_after;
                                        }
                                    endforeach;
                                endif;
                            }
                        }
                        else{
                            if(isset($item['variation']))
                                foreach($item['variation'] as $var){
                                    if($var==$optionkey){
                                        $prices+=(double)$optionvalue['option_price'];
                                        $svariation[] = $optionvalue['option_name'].": ".($optionvalue['option_price']>0?'+':'').$currency_sign_before.number_format(floatval($optionvalue['option_price']),2,".","").$currency_sign_after;
                                    }
                                }
                        }
                    }
                }
            }
            }

            //if(isset($item['coupon']) && trim($item['coupon'])!='') $valid_coupon = wpdmpp_check_coupon($pid,$item['coupon']);
            //else $valid_coupon = false;

            $coupon_discount = (isset($item['coupon']) && trim($item['coupon'])!='' && $pid > 0) ? \WPDMPP\Libs\CouponCodes::validate_coupon(trim($item['coupon']), $pid) : 0;
            $role_discount = wpdmpp_role_discount($pid); //isset($discount[$role]) && $discount[$role] > 0?$discount[$role]:0;

            if(!isset($item['multi'])){
                $cart_items[$pid]['prices'] = $prices;
                $cart_items[$pid]['variations'] = $svariation;
                if($coupon_discount) {
                    $cart_items[$pid]['coupon_amount'] =  $coupon_discount;
                    $cart_items[$pid]['discount_amount'] = (((($item['price']+$prices)*$item['quantity'] ) - $coupon_discount ) * $role_discount)/100 ;

                }
                else {

                    $cart_items[$pid]['discount_amount'] = ((($item['price']+$prices)*$item['quantity'] )  * $role_discount)/100;
                }
                if(!$coupon_discount) {
                    if(isset($item['coupon']) && trim($item['coupon'])!='')
                    $cart_items[$pid]['error'] = __('Invalid or Expired Coupon Code','wpdm-premium-packages');
                }
                else {
                    unset($cart_items[$pid]['error']);
                }

            }
            elseif(isset($item['multi']) && $item['multi'] == 1) {

                foreach ($lprices as $key => $value):
                    if(!isset($cart_items[$pid]['item']) || !is_array($cart_items[$pid]['item'])) $cart_items[$pid]['item'] = array();
                    $cart_items[$pid]['item'][$key]['prices'] = $value;
                    $cart_items[$pid]['item'][$key]['variations'] = isset($lvariation[$key])?$lvariation[$key]:array();

                    if($coupon_discount) {
                        $cart_items[$pid]['item'][$key]['coupon_amount'] =   (($item['price']+$value)*$item['item'][$key]['quantity']*$valid_coupon)/100;
                        $cart_items[$pid]['item'][$key]['discount_amount'] =   (((($item['price']+$value)*$item['item'][$key]['quantity']) - $cart_items[$pid]['item'][$key]['coupon_amount'])* $discount[$role])/100 ;
                    }
                    else {
                        $discount[$role] = isset($discount[$role])?$discount[$role]:0;
                        $cart_items[$pid]['item'][$key]['discount_amount'] =   ((($item['price']+$value)*$item['item'][$key]['quantity'])* $discount[$role])/100 ;
                    }

                    if(!$coupon_discount) {
                        if(isset($item['coupon']) && trim($item['coupon'])!='')
                        $cart_items[$pid]['item'][$key]['error'] = __('Invalid or Expired Coupon Code','wpdm-premium-packages');
                    }

                endforeach;
            }
        }
        wpdmpp_update_cart_data($cart_items);
    }
}

/**
 * Return cart total excluding discounts
 * @return string
 */
function wpdmpp_get_cart_total_after_discount(){
    $cart_items = wpdmpp_get_cart_items();

    $total = 0;
    if(is_array($cart_items)){

        foreach($cart_items as $pid=>$item)    {
            if(isset($item['item'])){
                foreach ($item['item'] as $key => $val){
                    $role_discount = isset($val['discount_amount']) ? $val['discount_amount']: 0;
                    $coupon_discount = isset($val['coupon_amount']) ? $val['coupon_amount']: 0;
                    $val['prices'] = isset($val['prices']) ? $val['prices']: 0;
                    $total += (($item['price'] + $val['prices']) * $val['quantity']) - $role_discount - $coupon_discount;
                }
            }
            else {
                $role_discount = isset($item['discount_amount']) ? $item['discount_amount']: 0;
                $coupon_discount = isset($item['coupon_amount']) ? $item['coupon_amount']: 0;
                $total += (($item['price'] + $item['prices'])* $item['quantity']) - $role_discount - $coupon_discount;
            }
        }
    }

    $total = apply_filters('wpdmpp_cart_subtotal',$total);

    return number_format($total, 2, ".", "");
}

function wpdmpp_get_cart_tax(){
    return wpdmpp_calculate_tax();
}

function wpdmpp_get_cart_subtotal(){
    $cart_items = wpdmpp_get_cart_items();

    $total = 0;
    if(is_array($cart_items)){
        foreach($cart_items as $pid => $item){
            if(isset($item['item'])){
                foreach ($item['item'] as $key => $val){
                    $role_discount = isset($val['discount_amount']) ? $val['discount_amount']: 0;
                    $coupon_discount = isset($val['coupon_amount']) ? $val['coupon_amount']: 0;
                    $val['prices'] = isset($val['prices']) ? $val['prices']: 0;
                    //$total += (($item['price'] + $val['prices'] - $role_discount - $coupon_discount)*$item['quantity']);
                    $total += ( ( $item['price'] + $val['prices'] - $role_discount ) * $val['quantity'] - $coupon_discount );
                }
            }
            else {
                $role_discount = isset($item['discount_amount']) ? $item['discount_amount']: 0;
                $coupon_discount = isset($item['coupon_amount']) ? $item['coupon_amount']: 0;
                //$total += (($item['price'] + $item['prices'] - $role_discount - $coupon_discount)*$item['quantity']);
                $total += ( ( $item['price'] + $item['prices'] - $role_discount ) * $item['quantity'] - $coupon_discount );
            }
        }
    }

    $total = apply_filters('wpdmpp_cart_subtotal',$total);

    return number_format($total, 2, ".", "");
}

/**
 * Calculating discount
 * @return string
 */
function wpdmpp_get_cart_discount(){
    global $current_user;

    $role = is_user_logged_in() ? $current_user->roles[0] : 'guest';
    $cart_items = wpdmpp_get_cart_items();
    $discount_r = 0;

    foreach($cart_items as $pid => $item){
        $opt = get_post_meta($pid,'wpdmpp_list_opts',true);
        $prices = 0;
        $lprices = array();
        $discount = get_post_meta($pid,"__wpdm_discount",true);
        $base_price = get_post_meta($pid,"__wpdm_base_price",true);
        $sales_price = wpdmpp_sales_price($pid);
        $price_variation = get_post_meta($pid,"__wpdm_price_variation",true);
        $variation = get_post_meta($pid,"__wpdm_variation",true);

        if(is_array($variation) && count($variation) > 0){
        foreach($variation as $key => $value){
            foreach($value as $optionkey => $optionvalue){
                if($optionkey!="vname" && $optionkey != 'multiple'){
                    if(isset($item['variation']) && is_array($item['variation'])){
                        foreach($item['variation'] as $var){
                            if($var == $optionkey){
                                $prices += $optionvalue['option_price'];
                            }
                        }
                    }
                    elseif(isset($item['item']) && !empty ($item['item'])){

                        foreach ($item['item'] as $a => $b) { //different variations, $b is single variation contain variation and quantity
                            if($b['variation']):
                                $lprices[$a] = isset($lprices[$a])?$lprices[$a]:0;
                                foreach ($b['variation'] as $c):
                                    if($c == $optionkey) {
                                        $lprices[$a] += $optionvalue['option_price'];
                                    }
                                endforeach;
                            endif;

                        }
                    }
                }
            }
        }}

        if( ! isset( $discount[$role] ) || ! is_numeric( $discount[$role] ) ) $discount[$role] = 0;

        if(!empty($lprices)):
            foreach($lprices as $key => $val):
                $discount_r += ((($item['price']+$val)*$item['item'][$key]['quantity'])*$discount[$role])/100;
            endforeach;
        else:
            $discount_r +=  ((($item['price']+$prices)*$item['quantity']) * $discount[$role] ) / 100;
        endif;
    }

    $cart_coupon = wpdmpp_get_cart_coupon();
    $discount_r += ((is_array($cart_coupon) && isset($cart_coupon['discount'])) ? $cart_coupon['discount'] : 0 );
    return number_format( $discount_r, 2, ".", "" );
}

/**
 * Calculating subtotal by subtracting discount
 * @return string
 */
function wpdmpp_get_cart_total(){
    $coupon = wpdmpp_get_cart_coupon();
    $subTotal = wpdmpp_get_cart_subtotal();
    $total = $coupon?$subTotal - $coupon['discount']:$subTotal;
    return number_format( $total, 2, ".", "" );
}

function wpdmpp_grand_total(){
    $tax = wpdmpp_calculate_tax();
    return number_format((wpdmpp_get_cart_subtotal() + $tax - wpdmpp_get_cart_discount()),2,".","");
}

//tax calculation
function wpdmpp_calculate_tax($orderid = ''){
    $cartsubtotal = wpdmpp_get_cart_subtotal();
    $tax_total = 0;
    $order = new \WPDMPP\Libs\Order();

    //echo '<pre>';print_r($_SESSION['orderid']);echo '</pre>';

    if($orderid == '' && !Session::get('orderid')) return 0;
    if($orderid == '' && Session::get('orderid')) $orderid = Session::get('orderid');
    $order_info = $order->GetOrder($orderid);
    if(!is_object($order_info)) return 0;
    $bdata = unserialize($order_info->billing_info);
    $settings = maybe_unserialize(get_option('_wpdmpp_settings'));

    //echo '<pre>';print_r($bdata);echo '</pre>';

    if( isset( $settings['tax']['enable'] ) && $settings['tax']['enable'] == 1 && isset($bdata['country'] ) ){
        $rate = wpdmpp_tax_rate($bdata['country'], $bdata['state']);
        $tax_total = ( ( $cartsubtotal * $rate ) / 100 );
    }

    return $tax_total;
}


// Calculate Tax on Cart Sub-total
function wpdmpp_calculate_tax2(){
    $tax_total = 0;
    $cartsubtotal = wpdmpp_get_cart_subtotal();
    $cart_id = wpdmpp_cart_id();
    $coupon = get_option($cart_id."_coupon", array());
    $cartdiscount = isset($coupon['discount'])?$coupon['discount']:0;
    $cartsubtotal -= $cartdiscount;
    $order = new \WPDMPP\Libs\Order();

    $order_info = $order->GetOrder(Session::get('orderid'));

    if(get_wpdmpp_option('tax/enable') == 1){
        $rate = wpdmpp_tax_rate(sanitize_text_field(wpdm_query_var('country')), sanitize_text_field(wpdm_query_var('state')));
        $tax_total = ( ( $cartsubtotal * $rate ) / 100 );
    }

    return $tax_total;
}

//tax calculation
function wpdmpp_tax_rate($country, $state = ''){

    $settings = maybe_unserialize(get_option('_wpdmpp_settings'));
    $txrate = 0;
    if(is_array(get_wpdmpp_option('tax/tax_rate'))){
        foreach(get_wpdmpp_option('tax/tax_rate') as $key => $rate){

            if($rate['country'] == $country && ( $rate['state'] == $state || $rate['state'] == 'ALL-STATES' ) ){
                $txrate = $rate['rate'];
                break;
            }
        }
    }
    return $txrate;
}


/**
 * Clear all cart items
 */
function wpdmpp_empty_cart(){
    \WPDMPP\Libs\Cart::clear();
}

function wpdmpp_addtocart_js(){
    if( get_option( 'wpdmpp_ajaxed_addtocart', 0 ) == 0) return;
    ?>
    <script>
        jQuery(function(){
            jQuery('.wpdm-pp-add-to-cart-link').click(function(){
                if(this.href!=''){
                    var lbl;
                    var obj = jQuery(this);
                    lbl = jQuery(this).html();
                    jQuery(this).html('<i class="fa fa-sun fa-spin"></i> adding...');
                    jQuery.post(this.href,function(){
                        obj.html('added').unbind('click').click(function(){ return false; });
                    })

                }
                return false;
            });

            jQuery('.wpdm-pp-add-to-cart-form').submit(function(){

                var form = jQuery(this);
                var fid = this.id;
                form.ajaxSubmit({
                    'beforeSubmit':function(){
                        jQuery('#submit_'+fid).val('adding...').attr('disabled','disabled');
                    },
                    'success':function(res){
                        jQuery('#submit_'+fid).val('added').attr('disabled','disabled');
                    }
                });

                return false;
            });
        });
    </script>
<?php
}


function wpdmpp_update_os(){
    global $wpdb;

    if(!current_user_can(WPDMPP_MENU_ACCESS_CAP)) return;

    $order_id = sanitize_text_field( $_POST['order_id'] );
    $status = sanitize_text_field( $_POST['status'] );
    $order = new \WPDMPP\Libs\Order();
    $order->Update( array( 'order_status' => $status ), $order_id );

    $settings = maybe_unserialize(get_option('_wpdmpp_settings'));
    $siteurl = home_url("/");

    //email to customer of that order
    $userid = $wpdb->get_var("select uid from {$wpdb->prefix}mp_orders where order_id='".$order_id."'");
    $user_info = get_userdata($userid);
    $admin_email = get_bloginfo("admin_email");
    $email = array();
    $subject = "Order Status Changed";
    $message = "The order {$order_id} is changed to {$status}";
    $email['subject'] = $subject;
    $email['body'] = $message;
    $email['headers'] = 'From:  <'.$admin_email.'>' . "\r\n";
    $email = apply_filters("order_status_change_email", $email);

    //wp_mail($user_info->user_email,$email['subject'],$email['body'],$email['headers']);
    //wp_mail($admin_email,$email['subject'],$email['body'],$email['headers']);

    die(__('Order status updated',"wpdm-premium-packages"));
}

function wpdmpp_update_ps(){
    if(!current_user_can(WPDMPP_MENU_ACCESS_CAP)) return;
    $order_id = sanitize_text_field( $_POST['order_id'] );
    $status = sanitize_text_field( $_POST['status'] );
    $order = new \WPDMPP\Libs\Order();
    $order->Update(array('payment_status' => $status ), $order_id );
    die(__('Payment status updated',"wpdm-premium-packages"));
}

function wpdmpp_pay_now($post_data){
    global $wpdb,$current_user;

    $order = new \WPDMPP\Libs\Order();
    $corder = $order->GetOrder($post_data['order_id']);
    $payment = new \WPDMPP\Libs\Payment();
    if(!isset($post_data['payment_method']) || $post_data['payment_method']=='')  $post_data['payment_method'] = $corder->payment_method;
    $post_data['payment_method'] = $post_data['payment_method']?$post_data['payment_method']:'PayPal';
    $payment->InitiateProcessor($post_data['payment_method']);
    $payment->Processor->OrderTitle = 'WPMP Order# '.$corder->order_id;
    $payment->Processor->InvoiceNo = $corder->order_id;
    $payment->Processor->Custom = $corder->order_id;
    $payment->Processor->Amount = number_format($corder->total,2,".","");
    echo $payment->Processor->ShowPaymentForm(1);
}

function wpdmpp_process_order(){
    global $current_user;

    $order = new \WPDMPP\Libs\Order();

    if(preg_match("@\/payment\/([^\/]+)\/([^\/]+)@is", $_SERVER['REQUEST_URI'], $process)){
        $gateway = $process[1];
        $page = $process[2];
        $invoice = sanitize_text_field( $_POST['invoice'] );
        $invoice = array_shift(explode("_",$invoice));
        $odata = $order->GetOrder($invoice);
        if(!$odata) die('ERROR: Order Not Found!');
        $current_user = get_userdata($odata->uid);
        $uname = $current_user->display_name;
        $uid = $current_user->ID;
        $email = $current_user->user_email;

        $myorders = get_option('_wpdmpp_users_orders',true);
        if($page == 'notify'){
            if(!$uid) {
                $email = sanitize_email( $_POST['payer_email'] );
                $uname = str_replace(array("@",'.'),'',$email);
                $password = $invoice;
                $uid = wp_create_user($uname, $password, $email );
                $logininfo = "Username: $uname<br/>Password: $password<br/>";
            }

            $order->Update( array( 'order_status' => sanitize_text_field( $_POST['payment_status'] ), 'payment_status' => sanitize_text_field( $_POST['payment_status'] ), 'uid' => $uid ), $invoice );

            $sitename = get_option('blogname');

            $settings = get_option('_wpdmpp_settings');
            $logo = isset($settings['logo_url'])&&$settings['logo_url']!=""?"<img src='{$settings['logo_url']}' alt='".get_bloginfo('name')."'/>":get_bloginfo('name');


            //wp_mail( $email, "You order on ".get_option('blogname'), $message, $headers, $attachments );
            $params = array(
                'date' => date(get_option('date_format'),time()),
                'homeurl' => home_url('/'),
                'sitename' => get_bloginfo('name'),
                'order_link' => "<a href='".wpdmpp_orders_page('id='.$invoice)."'>".wpdmpp_orders_page('id='.$invoice)."</a>",
                'register_link' => "<a href='".wpdmpp_orders_page('orderid='.$invoice)."'>".wpdmpp_orders_page('orderid='.$invoice)."</a>",
                'name' => $uname,
                'orderid' => $invoice,
                'order_url' => wpdmpp_orders_page('id='.$invoice),
                'order_url_admin' => admin_url('edit.php?post_type=wpdmpro&page=orders&task=vieworder&id='.$invoice),
                'img_logo' => $logo
            );

            // to buyer
            //wp_mail($buyer_email,$email['subject'],$email['body'],$email['headers']);
            \WPDM\Email::send("sale-notification", $params);
            die("OK");
        }

        if($page == 'return' && $_POST['payment_status'] == 'Completed'){
            if(!$current_user->ID){
                $email = sanitize_email( $_POST['payer_email'] );
                $invoice = sanitize_text_field( $_POST['invoice'] );
                $uname = str_replace(array("@",'.'),'',$email);
                $password = $invoice;
                $creds = array();
                $creds['user_login'] = $uname;
                $creds['user_password'] = $password;
                $creds['remember'] = true;
                $user = wp_signon( $creds, false );
            }
            die("<script>location.href='$myorders';</script>");
        }

        die();
    }
}

/**
 * @param $data
 * @return int
 */
function wpdmpp_get_all_coupon( $data ){

    if( ! is_array($data) ) return 0;

    $total = 0;

    foreach($data as $pid => $item){
        $valid_coupon = isset($item['coupon']) ? wpdmpp_check_coupon($pid, $item['coupon']) : 0;

        if($valid_coupon != 0) {
            $total += ($item['price']*$item['quantity']*($valid_coupon/100));
        }
    }

    return $total;
}

function wpdmpp_product_price_html($product_id){

    $sales_price = wpdmpp_sales_price($product_id);
    $currency_sign = wpdmpp_currency_sign();
    $discount = get_post_meta($product_id,"__wpdm_discount",true);
    $base_price = get_post_meta($product_id,"__wpdm_base_price",true);

    $price_variation = get_post_meta($product_id,"__wpdm_price_variation",true);
    $variation = get_post_meta($product_id,"__wpdm_variation",true);

    $price_html = number_format($base_price, 2, ".", "");

    if($base_price == 0) $price_html = __('Free', 'wpdm-premium-packages');

    ob_start();
    include \WPDM\Template::locate("add-to-cart/price.php", WPDMPP_TPL_DIR);
    return ob_get_clean();

}

function wpdmpp_product_license_options_html($product_id){

    //License
    $sales_price = wpdmpp_sales_price($product_id);
    $currency_sign = wpdmpp_currency_sign();
    $pre_licenses = wpdmpp_get_licenses();
    $base_price = get_post_meta($product_id,"__wpdm_base_price",true);
    $license_req = get_post_meta($product_id, "__wpdm_enable_license", true);
    $license_infs = get_post_meta($product_id, "__wpdm_license", true);
    $license_infs = maybe_unserialize($license_infs);
    $active_lics = array();
    $prices = "";
    if($license_req == 1){
        foreach ($pre_licenses as $licid => $lic){
            if(isset($license_infs[$licid]) && $license_infs[$licid]['active'] == 1){
                $lic['price'] = isset($license_infs[$licid]['price'])?$license_infs[$licid]['price']:$base_price;
                $active_lics[$licid] = $lic;
            }
        }

        if(count($active_lics) > 1){
            $license_count  = 0;
            $prices .= '';
        }
    }
    ob_start();
    include \WPDM\Template::locate("add-to-cart/select-license.php", WPDMPP_TPL_DIR);
    return ob_get_clean();
}

function wpdmpp_product_gigs_options_html($product_id){
    return include(\WPDM\Template::locate("add-to-cart/extra-gigs.php", WPDMPP_TPL_DIR));
}

/**
 * Build and returns add to cart form
 * @param $product_id
 * @return string
 */
function wpdmpp_add_to_cart_form( $product_id , $template = ''){
    global $current_user, $wpdmpp_settings;
    $discount = get_post_meta($product_id,"__wpdm_discount",true);
    $base_price = get_post_meta($product_id,"__wpdm_base_price",true);
    $sales_price = wpdmpp_sales_price($product_id);
    $price_variation = get_post_meta($product_id,"__wpdm_price_variation",true);
    $variation = get_post_meta($product_id,"__wpdm_variation",true);

    $settings = $wpdmpp_settings;
    $currency_sign = wpdmpp_currency_sign();
    $discount = is_user_logged_in() && isset($current_user->roles[0]) && isset($discount[$current_user->roles[0]]) ? $discount[$current_user->roles[0]] : 0;
    $role = is_user_logged_in() && isset($current_user->roles[0])?$current_user->roles[0]:'';
    $base_price = (double)$base_price;
    $prices_text = apply_filters('price_text',__('Price','wpdm-premium-packages'));


    $price_html = number_format($base_price, 2, ".", "");

    if($base_price == 0) $price_html = __('Free', 'wpdm-premium-packages');

    ob_start();
    if((int)get_post_meta($product_id, '__wpdm_pay_as_you_want', true) == 1)
        include \WPDM\Template::locate("add-to-cart/pay-as-you-want-form.php", WPDMPP_TPL_DIR);
    else
        include \WPDM\Template::locate("add-to-cart/form.php", WPDMPP_TPL_DIR);
    $cart_form = ob_get_clean();
    return $cart_form;
}

/**
 * Generate Add to cart button html
 * @param $product_id
 * @return false|mixed|string|void
 */
function wpdmpp_add_to_cart_button($product_id, $show_price = false){
    global $current_user, $wpdmpp_settings;
    $add_to_cart_button_label = get_wpdmpp_option("a2cbtn_label", "<i class='fa fa-shopping-cart'></i> &nbsp;".__("Add to Cart","wpdm-premium-packages"));
    $add_to_cart_button_label = apply_filters('add_to_cart_button_label', $add_to_cart_button_label, $product_id);

    $add_to_cart_button_class = wpdm_download_button_style(false, $product_id). " btn-addtocart";
    $add_to_cart_button_class = str_replace(array('btn-success', 'btn-info', 'btn-default', 'btn-primary', 'btn-warning', 'btn-secondary'), get_wpdmpp_option('a2cbtn_color', 'btn-primary'), $add_to_cart_button_class);
    $add_to_cart_button_class = apply_filters('add_to_cart_button_class', $add_to_cart_button_class, $product_id);

    $price = $show_price ? wpdmpp_price_format(wpdmpp_effective_price($product_id)) : '';

    ob_start();
    ?>
    <button class="<?php echo $add_to_cart_button_class; ?> btn-addtocart-<?php echo $product_id; ?>"
            data-cart-redirect="<?php echo(isset($wpdmpp_settings['wpdmpp_after_addtocart_redirect']) ? 'on' : 'off'); ?>"
            type="submit" ><?php echo $add_to_cart_button_label; ?> <span
                class="price-<?php echo $product_id; ?> label label-price"><?php echo $price; ?></span></button>
    <?php
    $button_html = ob_get_clean();
    $button_html = apply_filters("wpdmpp_add_to_cart_button", $button_html, $product_id, $add_to_cart_button_label, $add_to_cart_button_class);
    return $button_html;
}


function wpdmpp_add_to_cart_html( $product_id , $template = ''){

    $form = wpdmpp_add_to_cart_form($product_id);
    if((int)get_post_meta($product_id, '__wpdm_pay_as_you_want', true) == 1)
        return $form;

    ob_start();
    include \WPDM\Template::locate("add-to-cart/price.php", WPDMPP_TPL_DIR);
    $price = ob_get_clean();
    $html = $price.$form;
    return $html;


}

/**
 * @param $post
 * @param string $btnclass
 * @return string
 */
function wpdmpp_waytocart($product, $btnclass = 'btn-info'){

    $product = (array) $product;
    $price_variation = get_post_meta($product['ID'], '__wpdm_price_variation', true);

    $pre_licenses = wpdmpp_get_licenses();

    $license_req = get_post_meta($product['ID'], "__wpdm_enable_license", true);
    $license_infs = get_post_meta($product['ID'], "__wpdm_license", true);
    $license_infs = maybe_unserialize($license_infs);
    $active_lics = array();
    $currency_sign = wpdmpp_currency_sign();
    $base_price = wpdmpp_product_price($product['ID']);
    /*
    if($license_req == 1) {
        foreach ($pre_licenses as $licid => $lic) {
            if (isset($license_infs[$licid]) && $license_infs[$licid]['active'] == 1) {
                $lic['price'] = isset($license_infs[$licid]['price']) ? $license_infs[$licid]['price'] : $base_price;
                $active_lics[$licid] = $lic;
            }
        }

        if (count($active_lics) > 1) {
            $vcount = 0;
            $license_html = '<div class="btn-group"><a href="#" class="btn '.$btnclass.'  btn-addtocart">' . __('Add to Cart', 'wpdm-premium-packages') . '</a><button type="button" class="btn '.$btnclass.'  dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu">';
            foreach ($active_lics as $licid => $lic) {
                $vari = (floatval($lic['price']) != 0) ? " ( {$currency_sign}" . number_format($lic['price'], 2, ".", "") . " )" : "";
                $license_html .= '<li><a href="#">' . " " . $lic['name'] . $vari . "</a></li>";
                $vcount++;
            }
            $license_html .= '</div>';
            return $license_html;
        }
    }
    */

    // Product is FREE
    if( ! $price_variation && wpdmpp_product_price($product['ID']) == 0 )
        return '<a href="'.get_permalink($product['ID']).'" class="btn '.$btnclass.'  btn-addtocart" ><i class="far fa-arrow-alt-circle-down"></i> '.__("Download","wpdm-premium-packages").'</a>';

    // Product is Premium
    $add_to_cart_button_label = get_wpdmpp_option("a2cbtn_label", "<i class='fas fa-shopping-basket mr-2'></i>".__("Add to Cart","wpdm-premium-packages"));
    $add_to_cart_button_label = apply_filters('add_to_cart_button_label', $add_to_cart_button_label, $product['ID']);
    if( $price_variation ) {
        $html = "<a href='" . get_permalink($product['ID']) . "' class='btn $btnclass' >" . $add_to_cart_button_label . "</a>";
    } else {
        $html = "<form method=\"post\" action=\"\" name=\"cart_form\" class=\"wpdm_cart_form\" id=\"wpdm_cart_form_{$product['ID']}\">
                    <input type=\"hidden\" name=\"addtocart\" value=\"{$product['ID']}\">";

        $html .= wpdmpp_add_to_cart_button($product['ID'], true);

        $html .= '</form>';
    }

    return $html;
}

/**
 * @param $user_login
 * @param $user
 */
function wpdmpp_clear_user_cartdata($user_login, $user = null) {
    delete_option($user->ID."_cart");
}
//add_action('wp_login', 'wpdmpp_clear_user_cartdata', 10, 2);

/**
 * @usage Finds if two arrays are same. Used in WPDMPP to check if same variation of product exist in cart or not
 * @param $a
 * @param $b
 * @return bool
 */
function wpdmpp_array_diff($a, $b){

    if( is_array( $a ) && is_array( $b ) ) {
        if( count( $a ) != count( $b ) ) {
            return false;
        }
        else {
            sort( $a );
            sort( $b );
            return $a == $b  ;
        }
    }
    else if( $a == "" && $b == "" ){
        return true;
    }
}
