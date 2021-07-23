<?php
namespace WPDMPP\Libs\PaymentMethods;

use WPDMPP\Libs\Order;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if(!class_exists('Cheque')){
class Cheque extends \WPDMPP\Libs\CommonVars{

    var $GatewayUrl = '';
    var $GatewayName = 'Pay with Cheque';
    var $ReturnUrl;
    var $CancelUrl;
    var $Enabled;
    var $Currency;
    var $ClientEmail;
    var $order_id;
    var $buyer_email;


    function __construct($Mode = 0){
        global $current_user;

        $this->GatewayUrl = home_url('/?wpdmpp_cheque_payment=1');

        $this->Enabled = get_wpdmpp_option('Cheque/enabled');
        $opu = !is_user_logged_in() && get_wpdmpp_option('guest_download') == 1 && wpdmpp_guest_order_page() != ''?wpdmpp_guest_order_page():wpdmpp_orders_page();
        $this->orderStatus = get_wpdmpp_option('Cheque/order_status', 'Completed');
        $this->ReturnUrl = get_wpdmpp_option('Cheque/return_url', $opu);
        $this->CancelUrl = get_wpdmpp_option('Cheque/cancel_url', home_url('/'));
        $this->NotifyUrl = home_url('?action=wpdmpp-payment-notification&class=Cheque');
        $this->Currency =  wpdmpp_currency_code();
        if(is_user_logged_in()){
            $this->ClientEmail = $current_user->user_email;
        }

    }


    function ConfigOptions(){



        if($this->Enabled)$enabled='checked="checked"';
        else $enabled = "";
        $options = array(
            'order_status' => array(
                'label' => __("Order Status:", "wpdm-premium-packages"),
                'type' => 'select',
                'options' => array('Completed' => 'Completed', 'Pending' => 'Pending', 'Processing' => 'Processing'),
                'selected' => $this->orderStatus
            ),
            'return_url' => array(
                'label' => __("Return Url:", "wpdm-premium-packages"),
                'type' => 'text',
                'placeholder' => '',
                'value' => $this->ReturnUrl
            ),
        );
        return $options;
    }

    function ShowPaymentForm($AutoSubmit = 0){
        global $wpdm_asset;
        Order::update(array('order_status' => $this->orderStatus, 'payment_status' => 'Completed'), $this->InvoiceNo);
        do_action("wpdm_after_checkout",$this->InvoiceNo);
        return "<div class='alert alert-progress'>".$wpdm_asset['spinner']." Redirecting...</div><script>location.href='{$this->ReturnUrl}';</script>";
    }


    function VerifyPayment() {

         return true;

   }


}
}
?>
