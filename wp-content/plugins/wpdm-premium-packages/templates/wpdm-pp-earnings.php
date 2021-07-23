<?php
/**
 * Template for [wpdm-pp-earnings] shortocode. This shortcode generates the content of WPDM Author Dashboard ( [wpdm_frontend flaturl=0] ) >> Sales Tab.
 *
 * Reports sales and earning details of the author.
 *
 * This template can be overridden by copying it to yourtheme/download-manager/wpdm-pp-earnings.php.
 *
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $wpdb, $current_user;
$uid                = $current_user->ID;
$sql                = "select p.*,i.*, o.date from {$wpdb->prefix}ahm_orders o,
                      {$wpdb->prefix}ahm_order_items i,
                      {$wpdb->prefix}posts p
                      where p.post_author=$uid and
                            i.oid=o.order_id and
                            i.pid=p.ID and
                            i.quantity > 0 and
                            o.payment_status='Completed' order by o.date desc";

$sales              = $wpdb->get_results($sql);

$sql                = "select sum(i.price*i.quantity) from {$wpdb->prefix}ahm_orders o,
                      {$wpdb->prefix}ahm_order_items i,
                      {$wpdb->prefix}posts p
                      where p.post_author=$uid and
                            i.oid=o.order_id and
                            i.pid=p.ID and
                            i.quantity > 0 and
                            o.payment_status='Completed'";

$total_sales        = $wpdb->get_var($sql);
$commission         = wpdmpp_site_commission();
$total_commission   = $total_sales*$commission/100;
$total_earning      = $total_sales - $total_commission;
$sql                = "select sum(amount) from {$wpdb->prefix}ahm_withdraws where uid=$uid";
$total_withdraws    = $wpdb->get_var($sql);
$balance            = $total_earning-$total_withdraws;

//finding matured balance
$payout_duration    = get_option("wpdmpp_payout_duration");
$dt                 = $payout_duration*24*60*60;
$sqlm               = "select sum(i.price*i.quantity) from {$wpdb->prefix}ahm_orders o,
                      {$wpdb->prefix}ahm_order_items i,
                      {$wpdb->prefix}posts p
                      where p.post_author=$uid and
                            i.oid=o.order_id and
                            i.pid=p.ID and
                            i.quantity > 0 and
                            o.payment_status='Completed'
                            and (o.date+($dt))<".time()."";

$tempbalance        = $wpdb->get_var($sqlm);
$tempbalance        = $tempbalance - ($tempbalance*$commission/100);
$matured_balance    = $tempbalance - $total_withdraws;

//finding pending balance
$pending_balance    = $balance - $matured_balance;
?>

<div class="row">
    <div class="col-md-3 center">
        <div class="card">
            <div class="card-header"><?php _e("Sales:","wpdm-premium-packages");?></div>
            <div class="card-body lead"><?php echo  wpdmpp_price_format($total_sales,true, true); ?></div>
        </div>
    </div>
    <div class="col-md-3 center" title="After <?php echo $commission ?>% Site Commission Deducted">
        <div class="card">
            <div class="card-header"><?php _e("Earning:","wpdm-premium-packages");?></div>
            <div class="card-body lead"><?php echo  wpdmpp_price_format($total_earning,true, true); ?></div>
        </div>
    </div>
    <div class="col-md-3 center">
        <div class="card">
            <div class="card-header"><?php _e("Withdrawn:","wpdm-premium-packages");?></div>
            <div class="card-body lead" id="wa"><?php echo wpdmpp_price_format($total_withdraws,true, true); ?></div>
        </div>
    </div>
    <div class="col-md-3 center">
        <div class="card">
            <div class="card-header"><?php _e("Pending:","wpdm-premium-packages");?></div>
            <div class="card-body lead"><?php echo wpdmpp_price_format($pending_balance,true, true);?></div>
        </div>
    </div>
    <div class="col-md-12 center">
        <div class="card mt-4 mb-4">
            <div class="card-header"><?php _e("Balance:","wpdm-premium-packages");?></div>
            <div class="card-body lead">
                <span id="mb"><?php echo wpdmpp_price_format($matured_balance,true, true); ?></span>
                <form style="display: inline-table" id="wreqform" action="" method="post" class="pull-right">
                    <input type="hidden" name="withdraw" value="1">
                    <div class="input-group">
                        <input style="width: 80px" type="number" name="withdraw_amount" id="withdraw_amount" required="required"
                               value="<?php echo floor($matured_balance);?>" min="10" max="<?php echo floor($matured_balance);?>" class="form-control" id="wamt" >
                        <span class="input-group-append">
                            <button <?php if($matured_balance<=0){?>disabled="disabled" <?php } ?>  class="btn btn-primary pull-right" type="submit"
                                    id="wreqb"><?php _e("Withdraw","wpdm-premium-packages");?></button>
                        </span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<table class="table table-striped panel" id="earnings">
    <thead>
        <tr>
            <th><?php _e("Date","wpdm-premium-packages");?></th>
            <th><?php _e("Item","wpdm-premium-packages");?></th>
            <th><?php _e("Quantity","wpdm-premium-packages");?></th>
            <th><?php _e("Price","wpdm-premium-packages");?></th>
            <th><?php _e("Commission","wpdm-premium-packages");?></th>
            <th><?php _e("Earning","wpdm-premium-packages");?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($sales as $sale){ $sale->site_commission = $sale->site_commission ? $sale->site_commission : $sale->price*$commission/100; ?>
        <tr>
            <td><?php echo date("Y-m-d H:i",$sale->date); ?></td>
            <td><?php echo $sale->post_title; ?></td>
            <td><?php echo $sale->quantity; ?></td>
            <td><?php echo wpdmpp_price_format($sale->price, true, true); ?></td>
            <td><?php echo wpdmpp_price_format($sale->site_commission, true, true); ?></td>
            <td><?php echo wpdmpp_price_format($sale->price-$sale->site_commission,true, true); ?></td>
        </tr>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3"> </th>
            <th><?php echo wpdmpp_price_format($total_sales,true, true); ?></th>
            <th><?php echo wpdmpp_price_format($total_commission,true, true); ?></th>
            <th><?php echo wpdmpp_price_format($total_earning,true, true); ?></th>
        </tr>
    </tfoot>
</table>

<script>
    jQuery(function($){
        var cs = '<?php echo wpdmpp_currency_sign(); ?>', mb = <?php echo number_format($matured_balance,2); ?>, wd = <?php echo number_format($total_withdraws,2); ?>;

        $('#wreqform').submit(function(){
            $('#wreqb').attr('disabled','disabled').html("<i class='fa fa-spinner fa-spin'></i>");
            $(this).ajaxSubmit({
                success: function(res) {

                    if (res === 'denied') {
                        alert('<?php _e("Request denied. Matured balance is 0!", "wpdm-premium-packages"); ?>');
                        $('#wreqb').attr('disabled', 'disabled').html("<i class='fa fa-check-circle-o'></i>");
                    } else {
                        $('#wnotice .modal-title').html('Great!');
                        $('#wnotice .modal-body').html(res)
                        var wa = parseFloat($('#withdraw_amount').val());
                        var rb = mb - wa;
                        mb = rb;
                        wd += wa;
                        $('#mb').html(cs + rb.toFixed(2));
                        $('#wa').html(cs + wd.toFixed(2));
                        $('#wreqb').attr('disabled', 'disabled').html("<i class='fa fa-check-circle-o'></i>");
                        setTimeout(function () {
                            $('#wreqb').removeAttr('disabled').html("<?php _e("Withdraw", "wpdm-premium-packages");?>");
                        }, 4000);
                    }
                }
            });
            return false;
        });
    });
</script>
