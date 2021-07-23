<?php
/**
 * Orders List Template for User Dashboard >> Purchases >> All Orders
 *
 * This template can be overridden by copying it to yourtheme/download-manager/user-dashboard/purchase-orders.php.
 *
 * @version     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} ?>

<?php
global $wpdb, $sap, $wpdmpp_settings, $current_user;
$wpdmpp_settings['order_validity_period'] = (int)$wpdmpp_settings['order_validity_period'] > 0 ? (int)$wpdmpp_settings['order_validity_period'] : 365;
$order          = new \WPDMPP\Libs\Order();
$myorders       = $order->GetOrders($current_user->ID);
$order_notes    = '';
$orderurl       = get_permalink(get_the_ID());
$loginurl       = home_url("/wp-login.php?redirect_to=".urlencode($orderurl));

if( ! isset($_GET['id']) && !isset($_GET['item'] ) ){
    $orderid        = __("Order Id","wpdm-premium-packages");
    $date_label           = __("Purchase Date","wpdm-premium-packages");
    $renew_cycle_label           = __("Recurrence","wpdm-premium-packages");
    $expire_date_label           = __("Expire Date","wpdm-premium-packages");
    $payment_status = __("Status","wpdm-premium-packages");
    $all_orders     = apply_filters("wpdmpp_all_orders_title", __("All Orders","wpdm-premium-packages"));
    $_ohtml = <<<ROW
<div class="wpdmpp-all-orders">
<h3 class="wpdmpp-all-orders-title"><i class="fas fa-layer-group text-primary"></i> $all_orders</h3>

<!-- table class="table" style="margin:0;">
<thead>
<tr>
    <th style="width:50px;"></th>
    <th>$orderid</th>
    <th>$date_label</th>
    <th>$renew_cycle_label</th>
    <th>$expire_date_label</th>
    <th>$payment_status</th>
</tr>
</thead -->
ROW;
    $ordcls = new \WPDMPP\Libs\Order();
    $renews = $wpdb->get_results("select count(*) as renew_cycle, order_id from {$wpdb->prefix}ahm_order_renews GROUP  BY order_id");
    $renew_cycle = array();
    foreach ($renews as $renew){
        $renew_cycle[$renew->order_id] = $renew->renew_cycle;
    }
    $order_validity_period = $wpdmpp_settings['order_validity_period']*86400;
    foreach($myorders as $order){
        $date = date(get_option('date_format').' '.get_option('time_format'),$order->date);
        $items = unserialize($order->items);

        $expire_date = $order->expire_date;
        if(intval($expire_date) ==0 ) {
            $expire_date = $order->date + $order_validity_period;
            $ordcls->Update(array( 'expire_date' => $expire_date ), $order->order_id);
        }

        if( time() > $expire_date && $order->order_status == 'Completed' ){
            \WPDMPP\Libs\Order::expireOrder($order->order_id);
            $order->order_status = 'Expired';
            $order->payment_status = 'Expired';
        }
        if(wpdm_query_var('udb_page')) {
            $sap = (!isset($params['flaturl']) || $params['flaturl'] == 0) ? "?udb_page=" : "";
            $zurl = get_permalink(get_the_ID()) . $sap . "purchases/order/{$order->order_id}/";
        } else {
            $zurl = add_query_arg(array('id' => $order->order_id),get_permalink(get_the_ID()));
        }
        $nonce  = wp_create_nonce("delete_order");
        $del    = $order->order_status=='Processing'?'<a href="#" data-toggle="tooltip" title="Delete Order" class="delete_order btn btn-xs btn-danger" order_id="'.$order->order_id.'" nonce="'.$nonce.'"><i class="fa fa-times"></i></a>':'<a href="#" class="btn btn-xs btn-success" disabled="disabled"><i class="fa fa-check"></i></a>';
        $expire_date_s = date(get_option('date_format'), $expire_date);
        $_renew_cycle =  isset($renew_cycle[$order->order_id])?sprintf(__("%s cycle", 'wpdm-premium-packages'), wpdmpp_ordinal(($renew_cycle[$order->order_id])+1)):__('1st cycle', 'wpdm-premium-packages');
        $auto_reenew = $order->auto_renew==0?'<i class="far fa-times-circle color-red ttip" title="Auto-Renew Inactive"></i>':'<i title="Auto-Renew Active" class="ttip far fa-check-circle color-green"></i>';
        ob_start();
        ?>
        <div class="card card-default card-purchase-order" style="margin: 10px 0px">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <?php _e('Order ID#', 'wpdm-premium-packages');?> <a href='<?php echo $zurl; ?>'><?php echo $order->order_id; ?></a>
                    </div>
                    <div class="col-md-6 text-right">
                        <?php _e('Order Status', 'wpdm-premium-packages'); ?>: <span class=" color color-<?php echo $order->order_status == 'Completed'?'success':'danger'; ?>"><?php echo $order->order_status; ?></span>
                    </div>
                </div>
            </div>

            <div class="card-footer" style="margin-top: -1px">
                <a class="pull-right btn btn-xs btn-info" href='<?php echo $zurl; ?>'><?php _e('Order Details', 'wpdm-premium-packages'); ?></a>
                <i class="fas fa-calendar"></i> <?php echo $date; ?>  &nbsp; <i class="fa fa-sync"></i> <?php echo "Auto Renew: {$auto_reenew} {$_renew_cycle}"; ?>
            </div>
        </div>
        <!--tr class="order" id="order_{$order->order_id}">
            <td>{$del}</td>
            <td><a href='{$zurl}'>{$order->order_id}</a></td>
            <td>{$date}</td>
            <td>{$auto_reenew} {$_renew_cycle}</td>
            <td>{$expire_date_s}</td>
            <td>{$order->payment_status}</td>
        </tr-->
        <?php
        $_ohtml .= ob_get_clean();
    }

    $homeurl = home_url('/');
    $_ohtml .= "</table></div>";

    $link = admin_url('admin-ajax.php');
    $_ohtml .=<<<SCRIPT
        <script type='text/javascript'>
jQuery(document).ready(function($){
   $('.delete_order').on('click',function(){
        var nonce = $(this).attr('nonce');
        var order_id = $(this).attr('order_id');
        var url = "$link";
        var th = $(this);
        jQuery('#order_'+order_id).fadeTo('0.5');
        if(confirm("Are you sure you want to delete this order ?")){
            $(this).html('<i class="fa fa-spinner fa-spin"></i>').css('outline','none');
            jQuery.ajax({
             type : "post",
             dataType : "json",
             url : url,
             data : {action: "wpdmpp_delete_frontend_order", order_id : order_id, nonce: nonce},
             success: function(response) {
            //console.log(response);
                if(response.type == "success") {
                   $('#order_'+order_id).slideUp();
                   //alert('successfull...');
                }
                else {
                   alert("Something went wrong during deleting...")
                }
             }
            }); 
        }
        return false;
   });
   
   $('.ttip').tooltip();
});
        </script>
SCRIPT;

}

$odetails   = __("Purchases","wpdm-premium-packages");
$ostatus    = __("Order Status","wpdm-premium-packages");
$prdct      = __("Product","wpdm-premium-packages");
$qnt        = __("Quantity","wpdm-premium-packages");
$unit       = __("Unit Price","wpdm-premium-packages");
$coup       = __("Coupon Discount","wpdm-premium-packages");
$role_dis   = __("Role Discount","wpdm-premium-packages");
$ttl        = __("Total","wpdm-premium-packages");
$dnl        = __("Download","wpdm-premium-packages");
$licns      = __("License","wpdm-premium-packages");
$csign =  wpdmpp_currency_sign();
$link = get_permalink();
$_order404 = "";

echo "{$_ohtml}{$order_notes}";
?>
<script>
    jQuery(function($){
        $('#resolveorder').submit( function(){
            $('#resolveorder').slideUp();
            $('#w8o').html("<i class='fa fa-spinner fa-spin' ></i> <?php _e('Tracking Order...','wpdm-premium-packages'); ?>").slideDown();
            $(this).ajaxSubmit({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                success: function(res){
                    if(res=='ok') {
                        $('#w8o').html('<span class="text-success"><i class="fa fa-check" ></i> <?php _e('Order is linked with your account successfully.','wpdm-premium-packages'); ?></span>');
                        location.href = location.href;
                    }
                    else {
                        $('#w8o').html(res);
                    }
                }
            });
            return false;
        });
        $('#w8o').click(function(){
            jQuery(this).slideUp();
            $('#resolveorder').slideDown();
        });
    });
</script>
<style>
    td{ vertical-align: middle !important;}
    .card-footer .alert{font-size: 9pt; line-height: 28px; padding: 0 10px; }
    .card-footer .btn{ border: 0 !important; margin-top: -3px;}
    .row-actions {
        padding: 2px 0 0;
        visibility: hidden;
    }
    tr:hover .row-actions{
        visibility: visible;
    }
    #__bootModal .modal-header .modal-title{
        margin: 0;
    }
    #__bootModal .modal-body{
        line-height: 30px;
        text-align: center;
    }
    #__bootModal .modal-body .form-control{
        margin-top: 10px;
        text-align: center;
    }
    #__bootModal .modal-header .close{
        display: none;
    }
</style>

