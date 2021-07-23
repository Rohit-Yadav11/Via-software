<?php
if(!defined("ABSPATH")) die("Shit happens!");
global $wpdb;
$uid = wpdm_query_var('id', 'int');
$customer = get_user_by('id', $uid);

$order          = new \WPDMPP\Libs\Order();
$orders       = $order->GetOrders($uid, true);

$purchased_items = \WPDMPP\Libs\Order::getPurchasedItems(wpdm_query_var('id', 'int'));

?>

<div class="tab-pane active">
    <div class="row">
        <div class="col-md-3">
            <div class="panel panel-default card-plain">
                <div class="panel-heading"><?=esc_attr__( 'Total Spent', 'download-manager' );?></div>
                <div class="panel-body"><h3 style="color: var(--color-success-active);margin: 0;font-size: 16pt"><?= wpdmpp_price_format(\WPDMPP\Libs\User::totalSpent($uid)); ?></h3></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="panel panel-default card-plain">
                <div class="panel-heading"><?=esc_attr__( 'Total Orders', 'download-manager' );?></div>
                <div class="panel-body"><h3 style="color: var(--color-primary);margin: 0;font-size: 16pt"><?= count($orders); ?></h3></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default card-plain">
                <div class="panel-heading"><?=esc_attr__( 'Last Login', 'download-manager' );?></div>
                <div class="panel-body"><h3 style="margin: 0;font-size: 16pt"><?= wp_date(get_option('date_format')." ".get_option('time_format'), get_user_meta($uid, '__wpdm_last_login_time', true)); ?></h3></div>
            </div>
        </div>
    </div>
    <div class="panel panel-default card-plain">
        <div class="panel-heading"><?=esc_attr__( 'Invoices', 'download-manager' );?></div>
        <table class="table table-striped">
            <thead>
            <tr>
                <th><?=esc_attr__( 'Order ID', 'download-manager' );?></th>
                <th><?=esc_attr__( 'Date', 'download-manager' );?></th>
                <th><?=esc_attr__( 'Type', 'download-manager' );?></th>
                <th><?=esc_attr__( 'Amount', 'download-manager' );?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($orders as $order) {
                $renews = $wpdb->get_results("select * from {$wpdb->prefix}ahm_order_renews where order_id='{$order->order_id}'");
                ?>
                <tr>
                    <td><i class="fa fa-shopping-bag text-primary"></i> &nbsp;<a target="_blank" href="edit.php?post_type=wpdmpro&page=orders&task=vieworder&id=<?= $order->order_id ?>"><?= $order->order_id ?></a></td>
                    <td><?= wp_date(get_option('date_format'), $order->date) ?></td>
                    <td><?=esc_attr__( 'Purchase', 'download-manager' );?></td>
                    <td><?= wpdm_valueof(maybe_unserialize($order->currency), 'sign') . wpdmpp_price_format($order->total, false) ?></td>
                </tr>
                <?php
                foreach ($renews as $renew) {
                    ?>
                    <tr>
                        <td><i class="fa fa-redo text-success"></i> &nbsp;<?= $renew->order_id ?></td>
                        <td><?= wp_date(get_option('date_format'), $renew->date) ?></td>
                        <td><?=esc_attr__( 'Renew', 'download-manager' );?></td>
                        <td><?= maybe_unserialize($order->currency)['sign'] . wpdmpp_price_format($order->total, false) ?></td>
                    </tr>

                    <?php
                }
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="panel panel-default card-plain">
        <div class="panel-heading"><?=esc_attr__( 'Purchased Items', 'download-manager' );?></div>
        <table class="table table-striped">
            <thead>
            <tr>
                <th><?=esc_attr__( 'Name', 'download-manager' );?></th>
                <th><?=esc_attr__( 'Price', 'download-manager' );?></th>
                <th style="width: 100px"><?=esc_attr__( 'Actions', 'download-manager' );?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($purchased_items as $item) {
                ?>
                <tr>
                    <td>
                        <a target="_blank" href="edit.php?post_type=wpdmpro&page=orders&task=vieworder&id=<?= $item->oid ?>"><?=$item->post_title;?></a>
                    </td>
                    <td><?= maybe_unserialize($order->currency)['sign'] . wpdmpp_price_format($item->price, false) ?></td>
                    <td>
                        <a href="<?=get_permalink($item->pid); ?>" target="_blank"><?=esc_attr__( 'View', 'download-manager' ); ?></a> |
                        <a href="post.php?action=edit&post=<?=($item->pid); ?>" target="_blank"><?=esc_attr__( 'Edit', 'download-manager' ); ?></a>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
