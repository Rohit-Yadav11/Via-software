<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

global $wpdb;
global $current_user;
$sql = "select * from {$wpdb->prefix}ahm_withdraws where status<>1 order by date desc";
$payouts = $wpdb->get_results($sql);
//($payout->amount-($payout->amount*($comission[$userrole]/100)))
?>
<table cellspacing="0" class="table table-striped">
    <thead>
    <tr>
        <th><?php _e("Name","wpdm-premium-packages");?></th>
        <th><?php _e("PayPal Account","wpdm-premium-packages");?></th>
        <th><?php _e("Amount","wpdm-premium-packages");?></th>
        <th><?php _e("Status","wpdm-premium-packages");?></th>
        <th style="width: 100px;"><?php _e("Action","wpdm-premium-packages");?></th>
    </tr>
    </thead>
    <tbody>
    <?php

    foreach($payouts as $payout){
        $userrole = get_userdata($payout->uid)->roles[0];
        $pa = get_user_meta($payout->uid,'payment_account',true);
        $_pa = get_user_meta($payout->uid, '__wpdm_public_profile', true);
        $_pa = isset($_pa['paypal'])?$_pa['paypal']:'';
        $pa = $_pa!= ''?$_pa:$pa;
        $payform = '
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" id="payPalForm" name="payPalForm" >
    <input type="hidden" name="item_number" value="product">
    <input type="hidden" name="cmd" value="_xclick">
    <input type="hidden" name="item_name" value="Payout from '.get_bloginfo('name').'">
    <input type="hidden" name="no_note" value="1">
    <input type="hidden" name="business" value="'.$pa.'">
    <input type="hidden" name="currency_code" value="'.wpdmpp_currency_code().'">
    <input type="hidden" name="return" value="'.home_url().'/">
    <input type="hidden" name="notify_url" value="'.home_url().'/?action=withdraw_paypal_notification">
    <input name="item_name" type="hidden" id="item_name"  value="">
    <input name="amount" type="hidden" id="amount" value="'.$payout->amount.'">
    <input type="hidden" name="custom" value="'.$payout->id.'" >
    <button name="sub" class="btn btn-info btn-sm" type="submit" id="sub">Pay Now</button>
    </form>';

        $pstatus = "";

        if( $payout->status == 0 ) { $pstatus = "Pending"; $pstatusa = "Paid"; }


        $currency_sign = wpdmpp_currency_sign();
        $paypal = get_user_meta($payout->uid, 'payment_account', true);
        echo "<tr><td><a href='user-edit.php?user_id={$payout->uid}' >".get_userdata($payout->uid)->display_name."</a></td><td>{$paypal}</td><td >{$currency_sign}{$payout->amount}</td><td >{$pstatus}</td><td >{$payform} </td></tr>";
    }
    ?>
    </tbody>
</table>
