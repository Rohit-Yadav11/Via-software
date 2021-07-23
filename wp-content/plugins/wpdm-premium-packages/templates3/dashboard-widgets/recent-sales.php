<?php
/**
 * User: shahnuralam
 * Date: 5/6/17
 * Time: 11:42 PM
 */
if(!defined('ABSPATH')) die('!');
global $wpdb;
$latest_items = $wpdb->get_results("select oi.*,o.date, o.uid from {$wpdb->prefix}ahm_order_items oi LEFT JOIN {$wpdb->prefix}ahm_orders o ON o.order_id = oi.oid and o.order_status='Completed' order by o.date desc limit 0, 10");
//echo $wpdb->last_query;
?>
<div class="w3eden">
<div class="list-group" style="margin-bottom: 0">
    <?php foreach ($latest_items as $item){
        $title = get_post($item->pid)->post_title;
        $title = $title ? $title : '<span class="text-danger">'.__('Item Deleted!','wpdm-premium-packages').'</span>';
        ?>
    <a href="edit.php?post_type=wpdmpro&page=orders&task=vieworder&id=<?php echo $item->oid; ?>" class="list-group-item">
        <div class="media">
            <div class="pull-right">
                <div class="badge">
                <?php echo wpdmpp_currency_sign().number_format($item->price,2, '.',','); ?>
                </div>
            </div>
            <div class="media-body">
                <strong><?php echo $title; ?></strong><br/>
                <small><?php echo date(get_option('date_format')." ".get_option('time_format'), $item->date) ?> | <?php _e('Order#','wpdm-premium-packages'); ?> <?php echo $item->oid; ?></small>
            </div>
        </div>
    </a>
    <?php } ?>
</div>
</div>