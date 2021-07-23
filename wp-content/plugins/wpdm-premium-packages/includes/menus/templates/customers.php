<?php
/**
 * Template for User Dashboard >> Downloads >> Coupon Codes submenu page
 *
 * @version     1.0.0
 */

if(!defined('ABSPATH')) die();
global $wpdb;
$limit  = 20;
$page   = wpdm_query_var('paged');
$page   = $page > 0 ? $page : 1;
$start  = ( $page - 1 ) * $limit;
$cond   = array();


$cond[] = "order_status = 'Completed' or order_status = 'Expired'";

if(wpdm_query_var('product') != '')
    $cond[] = "product = '".wpdm_query_var('product', array('validate' => 'num'))."'";

if(count($cond) > 0)
    $cond = "where ".implode(" or ", $cond); else $cond = '';

$sql                = "select uid from {$wpdb->prefix}ahm_orders {$cond} GROUP BY uid ORDER BY date DESC limit $start, $limit";
$all_customers      = $wpdb->get_results($sql);
$total_customers    = $wpdb->get_var("select count(DISTINCT uid) from {$wpdb->prefix}ahm_orders {$cond}");

?>
<div class="w3eden payout-entries">
    <div class="panel panel-default" id="wpdm-wrapper-panel">
        <div class="panel-heading">
            <b><i class="fas fa-user-graduate color-purple"></i> &nbsp; <?php _e("Customers","wpdm-premium-packages");?></b>

        </div>
        <div class="panel-body" style="padding-top: 60px">

            <div class="panel panel-default">
                <table class="table table-striped table-wpdmpp">
                    <thead>
                    <tr>
                        <th><?php _e("Name","wpdm-premium-packages");?></th>
                        <th><?php _e("Email","wpdm-premium-packages");?></th>
                        <th><?php _e("Signup Date","wpdm-premium-packages");?></th>
                        <th><?php _e("Total Spent","wpdm-premium-packages");?></th>
                        <th style="width: 180px"><?php _e("Action","wpdm-premium-packages");?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($all_customers as $customer){
                        $_customer = get_user_by('id', $customer->uid);
                        ?>
                        <tr id="cr-<?php echo $customer->uid; ?>">
                            <td><strong><?php echo is_object($_customer) ? $_customer->display_name : esc_attr__( 'User Deleted / Not Found', 'wpdm-premium-packages' ); ?></strong></td>
                            <td><?php echo is_object($_customer) ? $_customer->user_email : '&mdash;'; ?></td>
                            <td><?php echo is_object($_customer) ? wp_date(get_option('date_format')." ".get_option('time_format'), strtotime($_customer->user_registered )): '&mdash;';; ?></td>
                            <td><?php echo wpdmpp_price_format(\WPDMPP\Libs\User::totalSpent($customer->uid)); ?></td>
                            <td>
                                <?php if(is_object($_customer)){ ?>
                                <a href="user-edit.php?user_id=<?php echo $_customer->ID; ?>" class="btn btn-sm btn-info"><i class="fas fa-pencil-alt"></i> <?php _e('Edit','wpdm-premium-packages'); ?></a>
                                <?php } ?>
                                <a href="edit.php?post_type=wpdmpro&page=customers&view=profile&id=<?php echo $customer->uid; ?>" class="btn btn-sm btn-primary"><i class="fas fa-user-circle"></i> <?php _e('Profile','wpdm-premium-packages'); ?></a>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="text-center">
                <?php
                echo wpdm_paginate_links($total_customers, $limit, $page, 'paged');
                ?>
            </div>
        </div>
    </div>
</div>
