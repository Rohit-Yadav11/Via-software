<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

global $payment_methods;
$payment_methods = apply_filters('payment_method', $payment_methods);
$xpayment_methods = $payment_methods;
$payment_methods = count(get_wpdmpp_option('pmorders', array())) == count($payment_methods) ? get_wpdmpp_option('pmorders') : $payment_methods;
$new_pgs = array_diff($xpayment_methods, $payment_methods);
if(is_array($new_pgs) && count($new_pgs) > 0){
    foreach ($new_pgs as $new_pg){
        $payment_methods[] = $new_pg;
    }
}
$settings['currency_position'] = isset($settings['currency_position']) ? $settings['currency_position'] : 'before';
?>
<div style="clear: both;margin-top:20px ;"></div>
<div class="panel panel-default">
    <div class="panel-heading"><?php _e("Payment Methods Configuration", "wpdm-premium-packages"); ?></div>
    <div id="paccordion" class="wpmppgac">
        <div class="panel-body">
            <div class="panel-group" id="wpdmpp-payment-methods" style="margin: 0">
                <?php

                //$integrated_payment_methods = array( 'TestPay', 'Paypal', 'Cash', 'Cheque' );
                //echo '<pre>';print_r($payment_methods);echo '</pre>';

                foreach ($payment_methods as $payment_method) {

                    $payment_gateway_class = 'WPDMPP\Libs\PaymentMethods\\'.$payment_method;

                    if (class_exists($payment_gateway_class)) {
                        $obj = new $payment_gateway_class();
                        $name = isset( $obj->GatewayName ) ? $obj->GatewayName : $payment_method;
                        ?>
                        <div class="panel panel-default">
                            <?php
                            echo '<div class="panel-heading"><b><i title="'.__('Drag and Drop to re-order','wpdm-premium-packages').'" class="fas fa-arrows-alt-v" style="color: #B27CD6;cursor: move"></i> &nbsp; <a data-toggle="collapse" data-parent="#wpdmpp-payment-methods" href="#'.$payment_method.'">' . ucwords($name) . '</a></b>';
                            echo '<div class="pull-right pm-status" id="pmstatus_'.$payment_method.'">';
                            if (isset($settings[$payment_method]['enabled']) && $settings[$payment_method]['enabled'] == 1)
                                echo "<span class='color-green'>" . __("Active", "wpdm-premium-packages")."  <i class='far fa-check-square'></i></span>";
                            else
                                echo '<span class="color-red">'.__("Inactive", "wpdm-premium-packages").'  <i class="far fa-minus-square"></i></span>';

                            echo '</div>';
                            echo '</div>';
                            echo '<div id="'.$payment_method.'" class="panel-collapse collapse">';
                            echo '<div class="panel-body">';
                            echo \WPDMPP\Libs\Payment::GateWaySettings($payment_gateway_class);
                            echo '</div>';
                            echo '</div>';
                            ?>
                            <input type="hidden" name="_wpdmpp_settings[pmorders][]" value="<?php echo $payment_method; ?>">
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

<div style="clear: both;margin-top:20px ;"></div>

<div class="panel panel-default" id="curr-conf-panel">
    <div class="panel-heading"><?php echo __("Currency Configuration", "wpdm-premium-packages"); ?></div>
    <div class="panel-body1">
        <table class="table table-striped" style="margin: 0;">
            <tr>
                <td style="border-top: 0"><?php _e('Currency:', 'wpdm-premium-packages'); ?></td>
                <td style="border-top: 0"><?php \WPDMPP\Libs\Currencies::CurrencyListHTML(array('name'=>'_wpdmpp_settings[currency]', 'selected'=> (isset($settings['currency'])?$settings['currency']:''))); ?></td>
            </tr>
            <tr>
                <td><?php _e('Currency sign position:', 'wpdm-premium-packages'); ?></td>
                <td>
                    <select class='form-control wpdmpp-currecy-position-dropdown' name="_wpdmpp_settings[currency_position]" style="min-width: 200px">
                        <option value="before" <?php selected( $settings['currency_position'], 'before' ); ?>><?php _e('Before - $99', 'wpdm-premium-packages'); ?></option>
                        <option value="after" <?php selected( $settings['currency_position'], 'after' ); ?>><?php _e('After - 99$', 'wpdm-premium-packages'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><?php _e('Thousand separator:', 'wpdm-premium-packages'); ?></td>
                <td>
                    <input class="form-control" type="text" style="width: 50px"  name="_wpdmpp_settings[thousand_separator]" value="<?php echo  isset($settings['thousand_separator'])?$settings['thousand_separator']:','; ?>"  />
                </td>
            </tr>
            <tr>
                <td><?php _e('Decimal separator:', 'wpdm-premium-packages'); ?></td>
                <td>
                    <input class="form-control" type="text" style="width: 50px"  name="_wpdmpp_settings[decimal_separator]" value="<?php echo  isset($settings['decimal_separator'])?$settings['decimal_separator']:'.'; ?>"  />
                </td>
            </tr>
            <tr>
                <td><?php _e('Number of decimals:', 'wpdm-premium-packages'); ?></td>
                <td>
                    <input class="form-control" type="text" style="width: 50px"  name="_wpdmpp_settings[decimal_points]" value="<?php echo  isset($settings['decimal_points'])?$settings['decimal_points']:'2'; ?>"  />
                </td>
            </tr>
        </table>
    </div>
</div>
<style>
    #curr-conf-panel .chosen-container.chosen-container-single {
        min-width: 250px;
    }
</style>
<script>
    jQuery(function($) {
        $('#wpdmpp-payment-methods').sortable();
        $('.ttip').tooltip();
        jQuery('.wpdmpp-currecy-dropdown, .wpdmpp-currecy-position-dropdown').chosen({width:'300px'});
    });
</script>
