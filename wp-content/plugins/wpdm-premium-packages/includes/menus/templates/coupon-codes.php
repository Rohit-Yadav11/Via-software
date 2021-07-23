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

if(wpdm_query_var('code') != '')
    $cond[] = "code like '%".wpdm_query_var('code')."%'";

if(wpdm_query_var('description') != '')
    $cond[] = "description like '%".wpdm_query_var('description')."%'";

if(wpdm_query_var('product') != '')
    $cond[] = "product = '".wpdm_query_var('product', array('validate' => 'num'))."'";

if(count($cond) > 0)
    $cond = "where ".implode(" or ", $cond); else $cond = '';

$sql            = "select * from {$wpdb->prefix}ahm_coupons {$cond} ORDER BY ID DESC  limit $start, $limit";
$coupon_codes   = $wpdb->get_results($sql);
$total_codes    = $wpdb->get_var("select count(*) from {$wpdb->prefix}ahm_coupons where 1 {$cond}");
?>
<div class="w3eden payout-entries">
    <div class="panel panel-default" id="wpdm-wrapper-panel">
        <div class="panel-heading">
            <b><i class="fas fa-ticket-alt color-purple"></i> &nbsp; <?php _e("Coupon Codes","wpdm-premium-packages");?></b>
            <div class="pull-right">
                <a href="edit.php?post_type=wpdmpro&page=pp-coupon-codes&task=new_coupon" class="btn btn-sm btn-primary"><i class="fas fa-plus-circle"></i> <?php _e("Add New","wpdm-premium-packages");?></a>
                <a href="#" class="btn btn-sm btn-info src-coupon"><i class="fas fa-search"></i> <?php _e("Search","wpdm-premium-packages");?></a>
                <a href="#" class="btn btn-sm btn-default" id="delsel"><i class="fas fa-trash"></i> <?php _e("Delete Selected","wpdm-premium-packages");?></a>
            </div>
        </div>
        <div class="panel-body" style="padding-top: 60px">

            <form method="get" action="edit.php">
                <input type="hidden" name="post_type" value="wpdmpro">
                <input type="hidden" name="page" value="pp-coupon-codes">
                <input type="hidden" name="task" value="search_coupon">
                <div class="panel panel-default" id="src-coupon" style="display:none;">
                    <div class="panel-body">
                        <div class="col-md-3">
                            <input type="text" placeholder="<?php _e("Coupon Code:","wpdm-premium-packages");?>" class="form-control" name="code" value="<?php echo stripslashes(wpdm_query_var('code')); ?>">
                        </div>
                        <div class="col-md-3">
                            <input type="text" placeholder="<?php _e("Product ID:","wpdm-premium-packages");?>" class="form-control" name="product" value="<?php echo stripslashes(wpdm_query_var('product')); ?>">
                        </div>
                        <div class="col-md-4">
                            <input type="text" placeholder="<?php _e("Description:","wpdm-premium-packages");?>" class="form-control" name="description" value="<?php echo stripslashes(wpdm_query_var('description')); ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-default btn-block action"><i class="fas fa-search fa-green"></i> <?php _e("Search Coupon","wpdm-premium-packages");?></button>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <b><?php printf(__('%d coupon code(s) found','wpdm-premium-packages'), $total_codes); ?></b>
                    </div>
                </div>
            </form>
            <div class="panel panel-default">
            <table class="table table-striped table-wpdmpp">
                <thead>
                <tr>
                    <th style="width: 50px"><input type="checkbox" id="allc"></th>
                    <th><?php _e("Coupon Code","wpdm-premium-packages");?></th>
                    <th><?php _e("Discount","wpdm-premium-packages");?></th>
                    <th><?php _e("Type","wpdm-premium-packages");?></th>
                    <th><?php _e("Product","wpdm-premium-packages");?></th>
                    <th><?php _e("Expire Date","wpdm-premium-packages");?></th>
                    <th><?php _e("Usage / Limit","wpdm-premium-packages");?></th>
                    <th><?php _e("Spend Limit (min/max)","wpdm-premium-packages");?></th>
                    <th style="width: 180px"><?php _e("Action","wpdm-premium-packages");?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($coupon_codes as $coupon_code){
                    $product = get_post($coupon_code->product); ?>
                    <tr id="cr-<?php echo $coupon_code->ID; ?>">
                        <td><input type="checkbox" class="allc" value="<?php echo $coupon_code->ID; ?>" name="id[]"></td>
                        <td><strong <?php if($coupon_code->expire_date > 0 && $coupon_code->expire_date < time()) echo 'class="expired-coupon color-red ttip" title="Expired Coupon"'; ?>><?php echo $coupon_code->code; ?></strong></td>
                        <td><?php echo $coupon_code->discount; ?></td>
                        <td><?php echo $coupon_code->type == 'percent'?'%':wpdmpp_currency_sign(); ?></td>
                        <td><?php echo $coupon_code->product > 0? "<a href=''>".get_the_title($coupon_code->product)."</a>":'<span class="color-purple">Global Coupon</span>'; ?></td>
                        <td><?php echo $coupon_code->expire_date > 0 ? date(get_option('date_format')." h:i a", $coupon_code->expire_date) : __('Never', "wpdm-premium-packages"); ?></td>
                        <td><?php echo (int)$coupon_code->used; ?> / <?php echo $coupon_code->usage_limit > 0 ? $coupon_code->usage_limit:'∞'; ?></td>
                        <td><?php echo $coupon_code->min_order_amount; ?> / <?php echo $coupon_code->max_order_amount == 0 ? 'No Limit' : $coupon_code->max_order_amount; ?></td>
                        <td>
                            <a href="edit.php?post_type=wpdmpro&page=pp-coupon-codes&task=edit_coupon&ID=<?php echo $coupon_code->ID; ?>" class="btn btn-sm btn-info"><i class="fas fa-pencil-alt"></i> <?php _e('Edit','wpdm-premium-packages'); ?></a>
                            <a href="#" rel="<?php echo $coupon_code->ID; ?>" class="btn btn-sm btn-danger btn-delcoup"><i class="fas fa-trash-alt"></i> <?php _e('Delete','wpdm-premium-packages'); ?></a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            </div>
            <div class="text-center">
                <?php
                $total = $wpdb->get_var("select count(*) from {$wpdb->prefix}ahm_coupons");
                echo wpdm_paginate_links($total, $limit, $page, 'paged');
                ?>
            </div>
        </div>
    </div>
</div>

<style>
    .expired-coupon{
        cursor: default;
    }
</style>
<script>
    jQuery(function ($) {
        $('.ttip').tooltip({placement: 'right'});
        $('.src-coupon').click(function (e) {
            e.preventDefault();
            $('#src-coupon').slideToggle();
        });
        $('.btn-delcoup').on('click', function (e) {
            e.preventDefault();
            var row = $('#cr-'+this.rel);
            var cpid = this.rel;
            wpdm_boot_popup("Deleting a Coupon", "Are you sure?", [
                {
                    class: 'btn btn-danger',
                    label: 'Yes, Delete!',
                    callback: function () {
                        this.find(".modal-body").html('<p><i class="fas fa-sun fa-spin"></i> Deleting...</p>');
                        var modal = this;
                        $.get(ajaxurl + '?action=wpdmpp_delete_coupon&ID=' + cpid, function () {
                            row.slideUp();
                            modal.modal('hide');
                        });
                    }
                },
                 {
                    class: 'btn btn-default',
                    label: 'No, Later.',
                    callback: function () {
                        this.modal('hide');
                    }

                 }
            ]);
            //if(!confirm('Are you sure?')) return false;

        });
        $('#allc').on('click', function () {
            if($(this).is(":checked"))
                $('.allc').attr('checked', 'checked');
            else
                $('.allc').removeAttr('checked');
        });
        $('#delsel').on('click', function (e) {
            e.preventDefault();
            if(!confirm('Are you sure?')) return false;
            $('.allc').each(function () {
                if($(this).is(":checked")) delete_cc($(this).val());

            });
        });

        function delete_cc(id) {
            var row = $('#cr-'+id);
            $('#cr-'+id).addClass('color-red');
            $.get(ajaxurl+'?action=wpdmpp_delete_coupon&ID='+id, function () {
                row.slideUp();
            })
        }
    })
</script>
