<?php
namespace WPDMPP\Libs;
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Payment{

    var $Processor;

    function __construct(){

    }

    function InitiateProcessor($MethodID){
        //$MethodClass = $MethodID;
        $MethodClass = 'WPDMPP\Libs\PaymentMethods\\'.$MethodID;
        if(!class_exists($MethodClass)) die('<span class="label label-danger">Payment method is not active!</span>');
        $this->Processor = new $MethodClass();
    }

    function ProcessPayment(){

    }

    function ListMethods() {
         global $wpdb;
         $methods = $wpdb->get_results("select * from {$wpdb->prefix}ahm_payment_methods where enabled='1'",ARRAY_A);
         return $methods;
    }

    function CountMethods(){
         global $wpdb;
         return $wpdb->get_var("select count(*) from {$wpdb->prefix}ahm_payment_methods where enabled='1'");
    }

    function PaymentMethodDropDown(){
        $methods = $this->ListMethods();
        $html = "";
        if(count($methods) > 1){
            foreach($methods as $method){
                $html .= "<option value='{$method['class_name']}'>{$method['title']}</option>\r\n";
            }
        }
        return $html;
    }

    /**
     * Return credit card type if number is valid
     * @return string
     * @param $number string
     **/
    public static function cardType($number)
    {
        $number=preg_replace('/[^\d]/','',$number);
        if (preg_match('/^3[47][0-9]{13}$/',$number))
        {
            return 'AMEX';
        }
        elseif (preg_match('/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/',$number))
        {
            return 'DINERS';
        }
        elseif (preg_match('/^6(?:011|5[0-9][0-9])[0-9]{12}$/',$number))
        {
            return 'DISCOVER';
        }
        elseif (preg_match('/^(?:2131|1800|35\d{3})\d{11}$/',$number))
        {
            return 'JCB';
        }
        elseif (preg_match('/^5[1-5][0-9]{14}$/',$number))
        {
            return 'MASTERCARD';
        }
        elseif (preg_match('/^4[0-9]{12}(?:[0-9]{3})?$/',$number))
        {
            return 'VISA';
        }
        else
        {
            return 'Unknown';
        }
    }


    static function GateWaySettings($gateway){
        if(!is_object($gateway)) $gateway = new $gateway();
        $options  = $gateway->ConfigOptions();

        // Retrieve Payment Gateway classname withount namespace
        $gateway_class = new \ReflectionClass($gateway);
        $class_shortname = $gateway_class->getShortName();

        $enabled['Paypal_enable'] = array(
            'name'          =>      '_wpdmpp_settings['.$class_shortname.'][enabled]',
            'id'            =>      'enable_'.$class_shortname,
            'label'         =>      __("Active","wpdm-premium-packages"),
            'type'          =>      'checkbox',
            'value'         =>      1,
            'checked'       =>      get_wpdmpp_option($class_shortname.'/enabled',0)
        );

        $enabled['GatewayTitle'] = array(
            'name'          =>      '_wpdmpp_settings['.$class_shortname.'][title]',
            'id'            =>      'title_'.$class_shortname,
            'label'         =>      __("Title","wpdm-premium-packages"),
            'type'          =>      'text',
            'value'         =>      get_wpdmpp_option($class_shortname.'/title', $gateway->GatewayName)
        );


        $options = array_merge($enabled, $options);
        foreach($options as $id => $option){
            if(!isset($option['id']))
                $option['name'] = '_wpdmpp_settings['.$class_shortname.']['.$id.']';
            if(!isset($option['id']))
                $option['id'] = $id;
            $options[$id] = $option;
        }

        $connection_tester = method_exists($gateway, 'testConnection') ? $gateway->testConnection() : '';

        return wpdm_option_page($options).$connection_tester."<script>jQuery(function($){ $('#enable_".$class_shortname."').on('click', function(){ if(this.checked) $('#pmstatus_".$class_shortname."').html('<span class=\"color-green\">Active <i class=\"far fa-check-square\"></i></span>'); else $('#pmstatus_".$class_shortname."').html('<span class=\"color-red\">Inactive  <i class=\"far fa-minus-square\"></i></span>');  }); });</script>";
    }

    function getMonthOptions(){
        return
            '<option value="01">January</option>\r\n'.
            '<option value="02">February</option>\r\n'.
            '<option value="03">March</option>\r\n'.
            '<option value="04">April</option>\r\n'.
            '<option value="05">May</option>\r\n'.
            '<option value="06">June</option>\r\n'.
            '<option value="07">July</option>\r\n'.
            '<option value="08">August</option>\r\n'.
            '<option value="09">September</option>\r\n'.
            '<option value="10">October</option>\r\n'.
            '<option value="11">November</option>\r\n'.
            '<option value="12">December</option>\r\n';
    }

    function getYearOptions(){
        $start = date("Y");
        $fin = $start + 25;
        $options = "";
        for($i=$start; $i<$fin; $i++){
            $options .='<option value="'.$i.'>'.$i.'</option>\r\n';
        }
        return $options;
    }


}


class CommonVars{
    var $Currency = 'USD';
    var $OrderTitle;
    var $Amount;
    var $InvoiceNo;
    var $OrderID;
    var $Settings;
    var $VerificationError;
}
