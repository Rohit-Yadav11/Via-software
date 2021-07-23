<?php

namespace WPDMPP\Libs\PaymentMethods;

if ( ! defined( 'ABSPATH' ) ) { exit; }

if (!class_exists('TestPay')) {

    class TestPay extends \WPDMPP\Libs\CommonVars
    {
        var $GatewayUrl = '';
        var $GatewayName = 'Test Pay';
        var $ReturnUrl;
        var $CancelUrl;
        var $Enabled;
        var $Currency;
        var $ClientEmail;
        var $order_id;
        var $buyer_email;

        function __construct($Mode = 0)
        {
            global $current_user;
            $opu = !is_user_logged_in() && get_wpdmpp_option('guest_download') == 1 && wpdmpp_guest_order_page() != ''?wpdmpp_guest_order_page():wpdmpp_orders_page();
            $this->GatewayUrl = home_url('/?wpdmpp_test_payment=1');
            $this->Enabled = get_wpdmpp_option('TestPay/enabled');
            $this->ReturnUrl = get_wpdmpp_option('TestPay/return_url', $opu);
            $this->CancelUrl = get_wpdmpp_option('TestPay/cancel_url', home_url('/'));
            $this->NotifyUrl = home_url('?action=wpdmpp-payment-notification&class=TestPay');
            $this->Currency = wpdmpp_currency_code();
            if (is_user_logged_in()) {
                $this->ClientEmail = $current_user->user_email;
            }
        }

        function ConfigOptions()
        {
            if ($this->Enabled) $enabled = 'checked="checked"'; else $enabled = "";
            $options = array(
                'cancel_url' => array(
                    'label' => __("Cancel Url:", "wpdm-premium-packages"),
                    'type' => 'text',
                    'placeholder' => '',
                    'value' => $this->CancelUrl
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

        function ShowPaymentForm($AutoSubmit = 0)
        {
            do_action("wpdmpp_payment_completed", $this->InvoiceNo);
            \WPDMPP\Libs\Order::complete_order($this->InvoiceNo);
            do_action("wpdm_after_checkout", $this->InvoiceNo);
            $opu = !is_user_logged_in() && get_wpdmpp_option('guest_download') == 1 && wpdmpp_guest_order_page() != ''?wpdmpp_guest_order_page():wpdmpp_orders_page();
            $returnURL = str_replace('{{download_page}}', $opu, $this->ReturnUrl);
            return "<div class='alert alert-progress'><i class='fas fa-sync fa-spin'></i> Redirecting...</div><script>location.href='{$returnURL}';</script>";
        }

        function VerifyPayment()
        {
            return true;
        }
    }
}
