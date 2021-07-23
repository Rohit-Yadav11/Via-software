<?php
/**
 * User: shahnuralam
 * Date: 25/12/18
 * Time: 3:12 PM
 */

namespace WPDMPP\Libs;


use WPDM\Session;

class Cart
{

    private $ID;

    function __construct()
    {

    }

    /**
     * Get cart id
     * @return null|string
     */
    static function ID(){
        global $current_user;
        $cart_id = null;
        if(is_user_logged_in()){
            $cart_id = $current_user->ID."_cart";
        } else {
            $cart_id = md5(wpdm_get_client_ip())."_cart";
        }

        return $cart_id;
    }

    /**
     * Get cart items
     * @return array|mixed
     */
    static function items(){
        global $current_user;

        $cart_id = self::ID();

        $cart_data = maybe_unserialize(get_option($cart_id));

        //Transfer cart data from guest id to user id
        if( is_user_logged_in() && !$cart_data ){
            $cart_id = md5($_SERVER['REMOTE_ADDR'])."_cart";
            $cart_data = maybe_unserialize(get_option($cart_id));
            delete_option($cart_id);
            $cart_id = $current_user->ID."_cart";
            update_option($cart_id, $cart_data);
        }

        return $cart_data ? $cart_data : array();

    }

    /**
     * Add an item to cart
     * @param $product_id
     * @param string $license
     * @param array $extras
     * @return array|mixed
     */
    static function addItem($product_id, $license = '', $extras = array()){

        $cart_id = self::ID();
        $cart_data = self::items();

        if(isset($cart_data[$product_id])) unset($cart_data[$product_id]);

        $license_prices = get_post_meta($product_id, "__wpdm_license", true);
        $license_prices = maybe_unserialize($license_prices);
        $license_req = get_post_meta($product_id, "__wpdm_enable_license", true);
        $pre_licenses = wpdmpp_get_licenses();

        $files = array();
        $fileinfo = get_post_meta($product_id, '__wpdm_fileinfo', true);
        $fileinfo = maybe_unserialize($fileinfo);
        $files_price = 0;
        $sfiles = isset($extras['files']) ? $extras['files'] : array();

        $variations = isset($extras['variations']) ? $extras['variations'] : array();

        if(count($sfiles) > 0 && $sfiles[0] != '' && is_array($fileinfo)) {
            foreach ($sfiles as $findx) {
                $files[$findx] = $fileinfo[$findx]['price'];
                if ($license_req == 1 && $license != '' && $fileinfo[$findx]['license_price'][$license] > 0) {
                    $files[$findx] = $fileinfo[$findx]['license_price'][$license];
                }
            }
        }

        $base_price = wpdmpp_product_price($product_id);
        if($license_req == 1 && isset($license_prices[$license], $license_prices[$license]['price']) && $license_prices[$license]['price'] > 0)
            $base_price = $license_prices[$license]['price'];

        if((int)get_post_meta($product_id, '__wpdm_pay_as_you_want', true) == 0) {
            $files_price = array_sum($files);
            $base_price = $files_price > 0 && $files_price < $base_price ? $files_price : $base_price;
            $cart_data[$product_id] = array('quantity' => 1, 'variation' => $variations, 'price' => $base_price, 'files' => $files);

        } else {
            //Condition for as you want to pay
            $base_price         = isset($extras['iwantopay'])&&(double)$extras['iwantopay'] >= $base_price ?(double)$extras['iwantopay']:$base_price;
            $cart_data[$product_id]    = array('quantity' => 1, 'variation' => $variations, 'price' => $base_price, 'files' => array());
        }

        update_option($cart_id, $cart_data);
        return $cart_data;

    }

    function applyCoupon($code, $product_id = null){

    }

    /**
     * @param $product_id
     * @return array|mixed
     */
    function removeItem($product_id){
        $cart_id = self::ID();
        $cart_data = self::items();

        if(isset($cart_data[$product_id])) unset($cart_data[$product_id]);

        update_option($cart_id, $cart_data);
        return $cart_data;

    }

    static function coupon(){
        $cart_id = self::ID();
        $cart_coupon = get_option($cart_id."_coupon", null);
        if(!isset($cart_coupon['code']) || $cart_coupon['code'] == '') { delete_option($cart_id."_coupon"); $cart_coupon = null; }
        return $cart_coupon;
    }

    static function save(){
        $cartdata = self::items();
        $cartinfo = array('cartitems' => $cartdata, 'coupon' => self::coupon());
        $cartinfo = \WPDM\libs\Crypt::Encrypt($cartinfo);
        $id = uniqid();
        file_put_contents(WPDM_CACHE_DIR.'saved-cart-'.$id.'.txt', $cartinfo);
        Session::set('savedcartid', $id);
        return $id;
    }

    function loadSaved($saved_cart_id){


        $cartfile = WPDM_CACHE_DIR.'/saved-cart-'.$saved_cart_id.'.txt';
        $saved_cart_data = '';

        if(file_exists($cartfile)) $saved_cart_data = file_get_contents($cartfile);
        $saved_cart_data = \WPDM\libs\Crypt::decrypt($saved_cart_data, true);

        $coupon_data = null;
        if(is_array($saved_cart_data) && count($saved_cart_data) > 0) {
            //wpdmdd($saved_cart_data);
            if(isset($saved_cart_data['cartitems'])){
                $coupon_data = $saved_cart_data['coupon'];
                $saved_cart_data = $saved_cart_data['cartitems'];

            }

            $cart_id = self::ID();
            update_option($cart_id, $saved_cart_data);

            if($coupon_data){
                $cart_id = wpdmpp_cart_id();
                update_option($cart_id."_coupon", $coupon_data);
            }
        }

        return $saved_cart_data;

    }

    /**
     * Shortcode function for [wpdmpp_cart], Shows Premium Package Cart
     * @return false|string
     */
    static function render(){
        global $wpdb;
        wpdmpp_calculate_discount();
        $cart_data      = self::items();
        $login_html     = "";
        $payment_html   = "";
        $settings       = get_option('_wpdmpp_settings');
        $guest_checkout = (isset($settings['guest_checkout']) && $settings['guest_checkout'] == 1) ? 1 : 0;
        $cart_id        = wpdmpp_cart_id();
        $coupon         = get_option($cart_id."_coupon");

        if(is_array($coupon)) {
            $coupon['discount'] = \WPDMPP\Libs\CouponCodes::validate_coupon($coupon['code']);
            update_option($cart_id . "_coupon", $coupon);
        }

        $cart_subtotal          = wpdmpp_get_cart_subtotal();
        $cart_total             = wpdmpp_get_cart_total();
        $cart_tax               = wpdmpp_get_cart_tax();
        $cart_total_with_tax    = number_format($cart_total + $cart_tax, 2, '.', '');
        $cart_coupon            = wpdmpp_get_cart_coupon();
        $cart_coupon_discount   = isset($cart_coupon['discount'])?number_format($cart_coupon['discount'],2, '.', ''):0.00;

        $Template = new \WPDM\Template();
        $Template->assign('guest_checkout', $guest_checkout);
        $Template->assign('cart_data', $cart_data);
        $Template->assign('cart_subtotal', $cart_subtotal);
        $Template->assign('cart_total', $cart_total);
        $Template->assign('cart_tax', $cart_tax);
        $Template->assign('cart_total_with_tax', $cart_total_with_tax);
        $Template->assign('cart_coupon', $cart_coupon);
        $Template->assign('settings', $settings);
        $Template->assign('cart_coupon_discount', $cart_coupon_discount);

        return $Template->fetch('checkout-cart/cart.php', WPDMPP_TPL_DIR, WPDMPP_TPL_FALLBACK);
    }

    static function clear(){
        global $current_user;
        $cart_id = wpdmpp_cart_id();
        delete_option($cart_id);
        delete_option($cart_id."_coupon");
        if(Session::get('orderid')){
            Session::set('last_order', Session::get('orderid'));
            Session::clear('orderid');
            Session::clear('tax');
            Session::clear('subtotal');
        }
    }

    function onUserLogin($user_login, $user){
        $user_cart_id = $user->ID."_cart";
        $guest_cart_id = md5(wpdm_get_client_ip())."_cart";
        $cart_data =  maybe_unserialize(get_option($guest_cart_id));
        update_option($user_cart_id, $cart_data);
        delete_option($guest_cart_id);
    }

    static function clearAll(){
        global $wpdb;
        $wpdb->query("DELETE FROM `wp_options` WHERE `option_name` LIKE '%_cart'");
    }

}
