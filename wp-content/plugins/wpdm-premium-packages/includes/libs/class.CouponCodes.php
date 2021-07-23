<?php
namespace WPDMPP\Libs;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('CouponCodes')):

    class CouponCodes
    {

        function __construct()
        {
            add_action( 'init', array( $this, 'add_coupon' ) );
            add_action( 'init', array( $this, 'update_coupon' ) );
            add_action( 'wp_ajax_wpdmpp_delete_coupon', array( $this, 'delete_coupon' ) );
        }

        static function find($coupon_code){
            global $wpdb;
            $row = $wpdb->get_row("select * from {$wpdb->prefix}ahm_coupons where code='{$coupon_code}'");
            return $row;
        }

        static function get($ID){
            global $wpdb;
            $row = $wpdb->get_row("select * from {$wpdb->prefix}ahm_coupons where ID='{$ID}'");
            return $row;
        }

        function add_coupon(){
            if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'addcoupon' && current_user_can(WPDMPP_ADMIN_CAP) && isset( $_POST['__anc'] ) && wp_verify_nonce( $_POST['__anc'], NONCE_KEY ) ){
                global $wpdb;
                $coupon = wpdm_sanitize_array($_REQUEST['coupon']);

                if($coupon['expire_date'] != '')
                    $coupon['expire_date'] = strtotime($coupon['expire_date']);
                else
                    $coupon['expire_date'] = 0;

                $wpdb->insert("{$wpdb->prefix}ahm_coupons", $coupon);
                //$wpdb->show_errors();
                //$wpdb->print_error();
                header("location: edit.php?post_type=wpdmpro&page=pp-coupon-codes");
                die();
            }
        }

        function update_coupon(){
            if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'updatecoupon' && current_user_can(WPDMPP_ADMIN_CAP) && isset( $_POST['__ucc'] ) && wp_verify_nonce( $_POST['__ucc'], NONCE_KEY ) ){
                global $wpdb;
                $coupon = wpdm_sanitize_array($_REQUEST['coupon']);

                if($coupon['expire_date'] != '')
                    $coupon['expire_date'] = strtotime($coupon['expire_date']);
                else
                    $coupon['expire_date'] = 0;

                $wpdb->update("{$wpdb->prefix}ahm_coupons", $coupon, array('ID' => absint( $_REQUEST['ID'] ) ) );
                header("location: edit.php?post_type=wpdmpro&page=pp-coupon-codes");
                die();
            }
        }

        static function increase_coupon_usage_count($coupon){
            global $wpdb;
            $query = "UPDATE `{$wpdb->prefix}ahm_coupons` SET `used`= `used` + 1 WHERE `code` = '{$coupon['coupon_code']}'";
            $wpdb->query($query);
        }

        static function validate_coupon($code, $product = 0){

            $coupon = self::find($code);

            if(!$coupon) return false;
            $car_data = wpdmpp_get_cart_data();
            $amount = wpdmpp_get_cart_subtotal();
            if($coupon->expire_date > 0 && $coupon->expire_date < time()) return false;
            if($coupon->product != $product) return false;
            if($coupon->usage_limit > 0 && $coupon->usage_limit <= $coupon->used) return false;
            if($coupon->min_order_amount > 0 && $coupon->min_order_amount > $amount) return false;
            if($coupon->max_order_amount > 0 && $coupon->max_order_amount < $amount) return false;

            $total_amount = $product == 0?$amount:$car_data[$product]['price']*$car_data[$product]['quantity'];

            $discount = 0;
            if($product == 0 && $coupon->type == 'fixed')
                $discount = $coupon->discount;
            else if($product > 0 && $coupon->type == 'fixed')
                $discount = $coupon->discount * $car_data[$product]['quantity'];
            else {
                $discount = number_format($total_amount * $coupon->discount / 100, 2, ".", "");
                if($discount >  $total_amount) $discount = $total_amount;
            }
            return floatval($discount);
        }

        function delete_coupon(){
            if(current_user_can(WPDMPP_ADMIN_CAP)){
                global $wpdb;
                $wpdb->delete("{$wpdb->prefix}ahm_coupons", array('ID' => absint( $_REQUEST['ID'] ) ) );
                die("OK");
            }
            die("ERROR");
        }

    }

    new CouponCodes();

endif;
