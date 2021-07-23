<?php
/**
 * User: shahnuralam
 * Date: 5/6/17
 * Time: 11:42 PM
 */
if(!defined('ABSPATH')) die('!');
$orders = new \WPDMPP\Libs\Order();
$latest_orders = $orders->GetAllOrders("where order_status='Completed' and payment_status='Completed'", 0, 10);
?>
<div class="w3eden">
<div class="list-group" style="margin-bottom: 0">
    <?php foreach ($latest_orders as $order){ $order->billing_info = maybe_unserialize($order->billing_info); ?>
    <a href="edit.php?post_type=wpdmpro&page=orders&task=vieworder&id=<?php echo $order->order_id; ?>" class="list-group-item">
        <div class="media">
            <div class="pull-right">
                <div class="badge">
                <?php echo wpdmpp_currency_sign().number_format($order->total,2, '.',','); ?>
                </div>
            </div>
            <div class="media-body">
                <strong><?php _e('Order#','wpdm-premium-packages'); ?> <?php echo $order->order_id; ?></strong><br/>
                <small><?php echo date(get_option('date_format')." ".get_option('time_format'), $order->date) ?> | <?php $u = get_user_by('id',$order->uid); echo is_object($u) && !is_wp_error($u)?$u->user_email:$order->billing_info['order_email']; ?></small>
            </div>
        </div>
    </a>
    <?php } ?>
</div>
</div>