<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

global $wpdb;
$order->items = unserialize($order->items);
$oitems = $wpdb->get_results("select * from {$wpdb->prefix}ahm_order_items where oid='{$order->order_id}'");
    $role = '';
    $currency = maybe_unserialize($order->currency);
    $currency_sign = is_array($currency) && isset($currency['sign'])?$currency['sign']:'$';
    if($order->uid > 0){
        $user = new WP_User( $order->uid );
        $role = $user->roles[0];
    }
    $tax = $orderObj->wpdmpp_calculate_tax($order->order_id);
    $settings = maybe_unserialize(get_option('_wpdmpp_settings'));
    $total_coupon = wpdmpp_get_all_coupon(unserialize($order->cart_data));

    $sbilling =  array
    (
        'first_name' => '',
        'last_name' => '',
        'company' => '',
        'address_1' => '',
        'address_2' => '',
        'city' => '',
        'postcode' => '',
        'country' => '',
        'state' => '',
        'email' => '',
        'order_email' => '',
        'phone' => ''
    );
    $billing = unserialize($order->billing_info);
    $billing = shortcode_atts($sbilling, $billing);


$renews = $wpdb->get_results("select * from {$wpdb->prefix}ahm_order_renews where order_id='{$order->order_id}'");

?>
<?php ob_start(); ?>

<table width="100%" cellspacing="0" class="table">
    <thead>
    <tr><th align="left"><?php _e("Item Name","wpdm-premium-packages");?></th>
        <th align="left"><?php _e("Unit Price","wpdm-premium-packages");?></th>
        <th align="left"><?php _e("Quantity","wpdm-premium-packages");?></th>
        <th align="left"><?php _e("Role Discount","wpdm-premium-packages");?></th>
        <th align="left"><?php _e("Coupon Code","wpdm-premium-packages");?></th>
        <th align="left"><?php _e("Coupon Discount","wpdm-premium-packages");?></th>
        <th align="right" class="text-right" style="width: 100px"><?php _e("Total","wpdm-premium-packages");?></th>
    </tr>
    </thead>
    <?php
    //$cart_data = unserialize($order->cart_data);
    $cart_data = \WPDMPP\Libs\Order::GetOrderItems($order->order_id);

    $currency = maybe_unserialize($order->currency);
    $currency_sign          = is_array($currency) && isset($currency['sign']) ? $currency['sign'] : '$';

    $payment_method = str_replace("WPDM_", "", $order->payment_method);

    if(is_array($cart_data) && !empty($cart_data)):
        $coupon_discount = 0;
        $role_discount = 0;
        $shipping = 0;
        $order_total = 0;
        foreach ($cart_data as $pid => $item):

            $currency_sign_before   = wpdmpp_currency_sign_position() == 'before' ? $currency_sign : '';
            $currency_sign_after    = wpdmpp_currency_sign_position() == 'after' ? $currency_sign : '';

            $license = isset($item['license'])?maybe_unserialize($item['license']):null;

            if($license)
                $license = isset($license['info'], $license['info']['name'])?'<span class="ttip color-purple" title="'.esc_html($license['info']['description']).'">'.sprintf(__("%s License","wpdm-premium-packages"), $license['info']['name']).'</span>':'';



                if(!isset($item['coupon_amount']) || $item['coupon_amount'] == "") {
                    $item['coupon_amount'] = 0.00;
                }

                if(!isset($item['discount_amount']) || $item['discount_amount'] == "") {
                    $item['discount_amount'] = 0.00;
                }

                if(!isset($item['prices']) || $item['prices'] == "") {
                    $item['prices'] = 0.00;
                }

                $title = get_the_title($item['pid']);
                $title = $title?$title:'&mdash; The item is not available anymore &mdash;';
                $coupon_discount += $item['coupon_discount'];
                $role_discount += $item['role_discount'];
                $order_total += (($item['price'] + $item['prices']) * (int)$item['quantity']) - $item['coupon_discount'] - $item['role_discount'];
                $item['variations'] = maybe_unserialize($item['variations']);

                $vari = array(); //isset($item['variations']) && !empty($item['variations']) ? implode(', ', $item['variations']) : '';
                $vari_cost = 0;
                if(is_array($item['variations'])){
                    foreach ($item['variations'] as $_variation){
                        if(is_array($_variation))
                            $vari[] = $_variation['name'].": +".$currency_sign.$_variation['price'];
                        else
                            $vari[] = $_variation;
                        $vari_cost += (double)$_variation['price'];
                    }
                }
                $vari = implode(", ", $vari);
                if(!isset($item['post_title'])) $item['post_title'] = '---';
                $item['price'] = (double)$item['price'];
                //echo "<pre>";print_r($item['quantity']);

                ?>
                <tr>
                    <td><?php echo $title; ?> <a href="<?php echo get_permalink($item['pid']); ?>" target="_blank"><i class="fa fa-external-link-alt"></i></a><div><?php echo $vari; ?></div>
                        <div>
                            <?php if ((int)get_post_meta($item['pid'], '__wpdm_enable_license_key', true) === 1) { ?>
                            [ <a class="color-success" id="<?php echo "lic_{$item['pid']}_{$order->order_id}_btn"; ?>" onclick="return getkey('<?php echo $item['pid']; ?>','<?php echo $order->order_id; ?>', '#'+this.id);"  data-placement="top" data-toggle="popover" href="#"><i class="fa fa-key color-success"></i></a> ]
                            <?php } ?>
                            <?php echo $license; ?>
                        </div>
                    </td>
                    <td><?php echo wpdmpp_price_format($item['price'], true, true); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td><?php echo wpdmpp_price_format($item['role_discount'],true, true); ?></td>
                    <td><?php echo isset($item['coupon'])?$item['coupon']:''; ?></td>
                    <td><?php echo wpdmpp_price_format($item['coupon_discount'],true, true); ?></td>
                    <td class="text-right"><?php echo wpdmpp_price_format((($item['price'] + $item['prices'] + $vari_cost) * $item['quantity']) - $item['role_discount'] - $item['coupon_discount'], true, true); ?></td>
                </tr>
            <?php

        endforeach;
    endif;
    ?>
    <tr>
        <td colspan="6" class="text-right"><?php _e('Cart Coupon Discount', 'wpdm-premium-packages'); ?></td>
        <td class="text-right">-<?php echo wpdmpp_price_format($order->coupon_discount, true, true); ?></td>
    </tr>
    <tr>
        <td colspan="6" class="text-right"><?php _e('Tax', 'wpdm-premium-packages'); ?></td>
        <td class="text-right"><?php echo wpdmpp_price_format($order->tax, true, true); ?></td>
    </tr>
    <tr id="refundrow" <?php if((int)$order->refund == 0) echo "style='display:none;'"; ?>>
        <td colspan="6" class="text-right"><?php _e('Refund', 'wpdm-premium-packages'); ?></td>
        <td class="text-right" id="refundamount">-<?php echo wpdmpp_price_format($order->refund, true, true); ?></td>
    </tr>
    <tr>
        <td colspan="6" class="text-right"><?php _e('Total', 'wpdm-premium-packages'); ?></td>
        <td class="text-right"><strong id="totalamount"  class="order_total"><?php echo wpdmpp_price_format($order->total, true, true); ?></strong></td>
    </tr>
</table>
<?php $content = ob_get_clean(); ?>


    <div class="w3eden admin-orders">
        <div class="panel panel-default" id="wpdm-wrapper-panel">
            <div class="panel-heading">
                <b><i class="fa fa-bars color-purple"></i> &nbsp; <?php _e("View Order","wpdm-premium-packages");?></b>
                <span id="lng" class="color-red" style="margin-left: 20px;display: none"><i class="fas fa-sun fa-spin"></i> <?php _e('Please Wait...', 'wpdm-premium-packages'); ?></span>
            </div>
            <div class="panel-body">
                <br/><br/><br/>

        <div class="well" style="background-image: none">

           <div class="row">
               <div class="col-lg-4">
                   <b><span id="oslabel"><?php _e("Order Status:", "wpdm-premium-packages"); ?></span>
                       <select id="osv" name="order_status" title="<?php _e("Select Order Status", "wpdm-premium-packages"); ?>" class="form-control wpdm-custom-select ttip" style="width: 150px;display: inline" >
                           <option value="Pending"><?php _e("Order Status:", "wpdm-premium-packages"); ?></option>
                           <option <?php if ($order->order_status == 'Pending') echo 'selected="selected"'; ?> value="Pending">Pending</option>
                           <option <?php if ($order->order_status == 'Processing') echo 'selected="selected"'; ?> value="Processing">Processing</option>
                           <option <?php if ($order->order_status == 'Completed') echo 'selected="selected"'; ?> value="Completed">Completed</option>
                           <option <?php if ($order->order_status == 'Expired') echo 'selected="selected"'; ?> value="Expired">Expired</option>
                           <option <?php if ($order->order_status == 'Cancelled') echo 'selected="selected"'; ?> value="Cancelled">Cancelled</option>
                           <option value="Renew" class="text-success text-renew">Renew Order</option>
                       </select>
                   </b>   <input type="button" id="update_os" class="btn btn-default" value="Update">
               </div>
               <div class="col-lg-4">
                   <b><span id="pslabel"><?php _e("Payment Status:", "wpdm-premium-packages"); ?></span>
                       <select id="psv" title="<?php _e("Select Payment Status", "wpdm-premium-packages"); ?>" class="wpdm-custom-select form-control ttip" name="payment_status"  style="width: 150px;display: inline" >
                           <option value="Pending"><?php _e("Payment Status:", "wpdm-premium-packages"); ?></option>
                           <option <?php if ($order->payment_status == 'Pending') echo 'selected="selected"'; ?> value="Pending">Pending</option>
                           <option <?php if ($order->payment_status == 'Processing') echo 'selected="selected"'; ?> value="Processing">Processing</option>
                           <option <?php if ($order->payment_status == 'Completed') echo 'selected="selected"'; ?> value="Completed">Completed</option>
                           <option <?php if ($order->payment_status == 'Bonus') echo 'selected="selected"'; ?> value="Bonus">Bonus</option>
                           <option <?php if ($order->payment_status == 'Gifted') echo 'selected="selected"'; ?> value="Gifted">Gifted</option>
                           <option <?php if ($order->payment_status == 'Cancelled') echo 'selected="selected"'; ?> value="Cancelled">Cancelled</option>
                           <option <?php if ($order->payment_status == 'Disputed') echo 'selected="selected"'; ?> value="Disputed">Disputed</option>
                           <option <?php if ($order->payment_status == 'Refunded') echo 'selected="selected"'; ?> value="Refunded">Refunded</option>
                       </select>
                   </b>
                   <input id="update_ps" type="button" class="btn btn-default" value="Update">
               </div>
               <div class="col-lg-4 text-right">
                   <button id="dlh" type="button" class="btn btn-info"><?php _e('Download History', 'wpdm-premium-packages') ?></button>
                   <button id="dlh" type="button" class="btn btn-secondary" onclick="window.open('?id=<?php echo wpdm_query_var('id'); ?>&wpdminvoice=1','Invoice','height=720, width = 750, toolbar=0'); return false;"><?php _e('Invoice', 'wpdm-premium-packages') ?></button>
               </div>
           </div>
        </div>
        <div id="msg" style="border-radius: 3px;display: none;" class="alert alert-success"><?php _e("Message", "wpdm-premium-packages"); ?></div>
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading"><?php _e("Order ID", "wpdm-premium-packages");  ?></div>
                <div class="panel-body">
                    <span class="lead"><strong><?php echo apply_filters("wpdmpp_admin_order_details_order_id", $order->order_id, $payment_method); ?></strong> <?php if($order->trans_id) { echo  "<span title='".sprintf(__("%s transaction ID", "wpdm-premium-packages"), $payment_method )."' style='font-size: 9pt' class='text-muted ttip'>( ". apply_filters("wpdmpp_admin_order_details_trans_id", $order->trans_id, $payment_method) . " )</span>"; } ?></span>
                </div>
            </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading"><?php _e("Order Date", "wpdm-premium-packages"); ?></div>
                    <div class="panel-body">
                        <span class="lead"><?php echo date("M d, Y h:i a", $order->date); ?></span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="pull-right">
                            <div class="w3eden" style="margin-top: -2px">
                                <a href="#" class="auto-renew-order" data-order="<?php echo $order->order_id; ?>">
                                        <span class="fa-stack renew-<?php echo $order->auto_renew==0?'cancelled':'active'; ?>">
                                            <i class="fa fa-circle-thin fa-stack-2x"></i>
                                            <i class="fa <?php echo $order->auto_renew==1?'fa-check':'fa-times'; ?> fa-stack-1x"></i>
                                        </span>
                                </a>
                            </div>
                        </div>
                        <?php $order->auto_renew==1?_e("Auto-Renew Date", "wpdm-premium-packages"):_e("Expiry Date", "wpdm-premium-packages"); ?>
                    </div>
                    <div class="panel-body">
                        <span class="lead"><?php echo date("M d, Y h:i a", $order->expire_date); ?></span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading"><?php _e("Order Total", "wpdm-premium-packages"); ?></div>
                    <div class="panel-body">

                        <div class="dropdown pull-right" style="margin-top: -1px">
                            <a href="#" id="dLabel" class="ttip" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"  title="<?php _e("Change Payment Method", "wpdm-premium-packages"); ?>">
                                <span class="fa-stack">
                                    <i class="fa fa-circle-thin fa-stack-2x" style="color: var(--color-info)"></i>
                                    <i id="editpm" class="fas fa-pen fa-stack-1x" style="color: var(--color-info)"></i>
                                </span>
                            </a>

                            <div class="dropdown-menu panel panel-default" aria-labelledby="dLabel" style="padding: 0;width: 230px;">
                                <div class="panel-heading"><?php _e("Change Payment Method:", "wpdm-premium-packages"); ?></div>
                                <div class="panel-body-np" style="height: 200px;overflow: auto;">
                                <?php
                                $payment_methods = WPDMPP()->active_payment_gateways();
                                foreach ($payment_methods as $payment_method){
                                    $payment_method_class = $payment_method;
                                    $payment_method = str_replace("WPDM_", "", $payment_method);
                                ?>
                                <a href="#" class="list-item changepm" data-pm="<?php echo $payment_method_class; ?>"><?php echo $payment_method; ?></a>
                                <?php } ?>
                                </div>
                            </div>
                        </div>

                        <span class="lead color-green"><strong class="order_total"><?php echo wpdmpp_price_format($order->total, true, true); ?></strong></span> <span class="text-muted">via <span id="pmname"><?php echo str_ireplace("wpdm_", "", $order->payment_method); ?></span></span>
                    </div>
                </div>
            </div>


    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading"><?php _e("Order Summary", "wpdm-premium-packages"); ?></div>
        <table class="table">
            <tr><td><?php _e("Total Coupon Discount:", "wpdm-premium-packages"); ?></td><td><?php echo wpdmpp_price_format($coupon_discount + $order->coupon_discount,true, true); ?></td></tr>
            <tr><td><?php _e("Role Discount:", "wpdm-premium-packages"); ?></td><td><?php echo wpdmpp_price_format($role_discount,true, true); ?></td></tr>
            <?php
            if (count($tax) > 0) {
                foreach ($tax as $taxrow) {
                    ?>
                    <tr><td><?php echo $taxrow['label']; ?></td><td><?php echo wpdmpp_price_format($taxrow['rates'],true, true); ?></td></tr>
                <?php
                }
            }

            $ret = '';
            $ret = apply_filters('wpdmpp_admin_order_details',$ret,$order->order_id);
            if($ret != '') echo $ret;
            ?>


        </table>
            </div>
    </div>
    <div class="col-lg-5 col-md-6 col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading"><?php _e("Customer Info", "wpdm-premium-packages"); ?></div>
    <?php if($order->uid>0){ ?>
        <table class="table" id="cintable">
            <tbody>
            <tr><td><?php _e("Customer Name:", "wpdm-premium-packages"); ?></td><td><a href='user-edit.php?user_id=<?php echo $user->ID; ?>'><?php echo $user->display_name; ?></a></td></tr>
            <tr><td><?php _e("Customer Email:", "wpdm-premium-packages"); ?></td><td><button type="button" class="btn btn-xs btn-warning pull-right" data-toggle="modal" data-target="#changecustomer"><?php _e('Change', 'wpdm-premium-packages') ?></button><a href='mailto:<?php echo $user->user_email; ?>'><?php echo $user->user_email; ?></a></td></tr>
            </tbody>
        </table>

        <div class="modal fade" tabindex="-1" role="dialog" id="changecustomer">
            <div class="modal-dialog" role="document" style="width: 350px">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title"><?php echo __("Change Customer", "wpdm-premium-packages"); ?></h4>
                    </div>
                    <div class="modal-body">
                       <input type="text" placeholder="<?php echo __("Username or Email", "wpdm-premium-packages"); ?>" class="form-control input-lg" id="changec" />
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="save_customer_change" class="btn btn-primary"><?php echo __("Change", "wpdm-premium-packages"); ?></button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

    <?php } else { ?><b></b>
        <table class="table">

            <tbody>

            <tr><td><?php _e("Customer Name:", "wpdm-premium-packages"); ?></td><td><?php echo $billing['first_name'].' '.$billing['last_name']; ?></td></tr>
            <tr><td><?php _e("Customer Email:", "wpdm-premium-packages"); ?></td><td><a href="mailto:<?php echo $billing['order_email']; ?>"><?php echo $billing['order_email']; ?></a></td></tr>
            </tbody>
        </table>
    <table class="table">
        <thead>
        <tr><th align="left"><?php echo __("This order is not associated with any registered user", "wpdm-premium-packages"); ?></th></tr>
        </thead>
        <tr><td align="left" id="ausre" ><div class="input-group"><input placeholder="Username or Email" type="text" class="form-control" id="ausr"><span class="input-group-btn"><input type="button" id="ausra" class="btn btn-primary" value="<?php echo __("Assign User", "wpdm-premium-packages"); ?>"></span></div></td></tr>
     </table>
    <?php } ?>
            </div>
    </div>

            <div class="col-lg-4 col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading"><?php _e("IP Information", "wpdm-premium-packages"); ?></div>
                    <table class="table">
                        <tr><td><?php _e("IP Address:", "wpdm-premium-packages"); ?></td><td><?php echo $order->IP; ?></td></tr>
                        <tr><td><?php _e("Location:", "wpdm-premium-packages"); ?></td><td><div id="iploc">
                                    <script>
                                        jQuery(function($){
                                            $.getJSON("https://ipapi.co/<?php echo $order->IP; ?>/json/", function(data) {
                                                var table_body = "";
                                                console.log(data);
                                                if(data.error!== true && data.reserved !== true){
                                                 table_body += data.city+", ";
                                                 table_body += data.region+", ";
                                                 table_body += data.country;
                                                $("#iploc").html(table_body);
                                                } else {
                                                    $("#iploc").html('Private');
                                                }
                                            });
                                        });
                                    </script>
                        </div></td></tr>
                        <?php
                        if (count($tax) > 0) {
                            foreach ($tax as $taxrow) {
                                ?>
                                <tr><td><?php echo $taxrow['label']; ?></td><td><?php echo $currency_sign . $taxrow['rates']; ?></td></tr>
                            <?php
                            }
                        }

                        $ret = '';
                        $ret = apply_filters('wpdmpp_admin_order_details',$ret,$order->order_id);
                        if($ret != '') echo $ret;
                        ?>
                    </table>
                </div>
            </div>
            <div style="clear: both"></div>
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">

                    <?php _e("Order Items", "wpdm-premium-packages"); ?></div>
                <?php echo $content; ?>

                <div class="panel-footer text-right bg-white">
                    <button class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#refundmodal"><?php _e("Refund", "wpdm-premium-packages"); ?></button>
                </div>


            </div>




        </div>
        </div>




        <?php
        include(dirname(__FILE__) . '/renew-invoices.php');
        echo "<div class='well' style='font-weight: 700;font-size: 12pt'>".__("Order Notes", "wpdm-premium-packages")."</div>";
        include(dirname(__FILE__) . '/order-notes.php');
        ?>
    </div>
</div>

        <div class="modal fade" id="refundmodal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <form method="post" id="refundform">
                        <input type="hidden" name="action" value="wpdmpp_async_request" />
                        <input type="hidden" name="execute" value="addRefund" />
                        <input type="hidden" name="order_id" value="<?php echo wpdm_query_var('id'); ?>" />
                        <div class="modal-header">
                            <strong><?php _e("Refund", "wpdm-premium-packages"); ?></strong>
                        </div>
                        <div class="modal-header text-center" style="background: #fafafa">
                            <h4 style="padding: 0;margin: 0;"><?php _e("Order Total", "wpdm-premium-packages"); ?>: <span  class="order_total"><?php echo wpdmpp_price_format($order->total); ?></span></h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <strong style="margin-bottom: 10px;display: block"><?php _e("Refund Amount", "wpdm-premium-packages"); ?>:</strong>
                                <input type="text" class="form-control input-lg" name="refund" />
                            </div>
                            <div class="form-group">
                                <strong style="margin-bottom: 10px;display: block"><?php _e("Reason For Refund", "wpdm-premium-packages"); ?>:</strong>
                                <textarea type="text" class="form-control" name="reason"></textarea>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <button type="submit" class="btn btn-block btn-primary btn-lg"><?php _e("Apply Refund", "wpdm-premium-packages"); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</div>



<script>
    jQuery(function($){

        <?php
        $style = array(
            'Pending'=>'btn-warning',
            'Expired'=>'btn-danger',
            'Processing' => 'btn-info',
            'Completed'=>'btn-success',
            'Bonus' => 'btn-success',
            'Gifted' => 'btn-success',
            'Cancelled' => 'btn-danger',
            'Disputed' => 'btn-danger',
            'Refunded' => 'btn-danger'
        );
        $oid = sanitize_text_field($_GET['id']);
        ?>
        //$('select#osv').selectpicker({style: '<?php echo isset($style[$order->order_status])?$style[$order->order_status]:'btn-default'; ?>'});
        //$('select#psv').selectpicker({style: '<?php echo $style[$order->payment_status]; ?>'});

        $('#refundform').on('submit', function(e){
            e.preventDefault();
            WPDM.blockUI('#refundform');
            $(this).ajaxSubmit({
                url: ajaxurl,
                success: function(response){
                    $('#refundrow').show();
                    $('#refundamount').html(response.amount)
                    $('.order_total').html(response.total)
                    WPDM.notify(response.msg, "success", "top-right", 7000);
                    WPDM.unblockUI('#refundform');
                    $('#refundform').trigger('reset');
                    $('#refundmodal').modal('hide');
                }
            });
        });


        $('#update_os').click(function(){
            $('#lng').fadeIn();
            $.post(ajaxurl,{action:'wpdmpp_async_request',execute:'updateOS',order_id:'<?php echo $oid; ?>',status:$('#osv').val()},function(res){
                WPDM.notify(res, "success", "top-right", 7000);
                $('#lng').fadeOut();

            });
        });


        $('.changepm').click(function(e){
            e.preventDefault();
            $('#editpm').removeClass('fas fa-pen').addClass('far fa-sun fa-spin');
            $.post(ajaxurl,{action:'wpdmpp_async_request',execute:'updatePM',order_id:'<?php echo $oid; ?>',pm:$(this).data('pm')},function(res){
                $('#pmname').html(res.pmname);
                WPDM.notify(res.msg, 'success', 'top-right', 7000);
                $('#lng').fadeOut();
                $('#editpm').removeClass('far fa-sun fa-spin').addClass('fas fa-pen');

            });
        });


        $('#update_ps').click(function(){
            $('#lng').fadeIn();
            $.post(ajaxurl,{action:'wpdmpp_async_request',execute:'updatePS',order_id:'<?php echo $oid; ?>',status:$('#psv').val()},function(res){
                WPDM.notify(res, "success", "top-right", 7000);
                $('#lng').fadeOut();
            });
        });

        $('#save_customer_change').on('click', function(){
            WPDM.blockUI('#changecustomer .modal-content');
            $.post(ajaxurl,{action:'assign_user_2order', order:'<?php echo $oid; ?>', assignuser:$('#changec').val(), __nonce:'<?php echo wp_create_nonce(NONCE_KEY);?>'},function(res){
                $('#cintable').html("<tbody><tr><td>"+res+"</td></tr></tbpdy>");
                WPDM.unblockUI('#changecustomer .modal-content');
                $('#changecustomer').modal('hide');
            });
        });

        var ruf = $('#ausre').html();
        $('body').on('click', '#ausre .alert', function(){
            $('#ausre').html(ruf);
        });
        $('body').on('click', '#ausra', function(){
            var ausr = $('#ausr').val();
            $('#ausre').html("<div class='alert alert-primary' style='padding:7px 15px;border-radius:2px;margin:0'><i class='fa fa-spin fa-refresh'></i> <?php _e('Please Wait...','wpdm-premium-packages'); ?></div>");
            $.post(ajaxurl, {action: 'assign_user_2order', order: '<?php echo $oid; ?>', assignuser: ausr, __nonce:'<?php echo wp_create_nonce(NONCE_KEY);?>'}, function(res){
                $('#ausre').html(res);
            });
        });


        $('#dlh').on('click', function () {
            __bootModal("Download History", "<div id='dlhh'><i class='far fa-sun fa-spin'></i> Loading...</div>", 400);
            $('#dlhh').load(ajaxurl, {action: 'wpdmpp_download_hostory', oid: '<?php echo wpdm_query_var('id', 'txt'); ?>', __dlhnonce: '<?php echo wp_create_nonce(NONCE_KEY); ?>'});
        });

        $('.auto-renew-order').on('click', function (e) {
            e.preventDefault();
            $this = $(this);
            $(this).find('.fa').removeClass('fa-check').removeClass('fa-times').addClass('fa-sun fa-spin');
            $.get(ajaxurl, {orderid: $(this).data('order'), action: 'wpdmpp_toggle_auto_renew', '__arnonce': '<?php echo wp_create_nonce(NONCE_KEY); ?>'}, function (res) {
                if(res.renew != undefined){
                    if(res.renew == 0) {
                        $this.find('.fa-stack').removeClass('renew-active').addClass('renew-cancelled');
                        $this.find('.fa').removeClass('fa-sun fa-spin').addClass('fa-times');
                    }
                    else {
                        $this.find('.fa').removeClass('fa-sun fa-spin').addClass('fa-check');
                        $this.find('.fa-stack').removeClass('renew-cancelled').addClass('renew-active');
                    }
                }
            });
        });

        $('.ttip').tooltip();



    });
</script>
<style>
    .chzn-search input{ display: none; }.chzn-results{ padding-top: 5px !important; }
    .btn-group.bootstrap-select .btn{ border-radius: 3px !important; }
    a:focus{ outline: none !important; }
    .panel-heading{ font-weight: bold; }
    .text-renew *{ font-weight: 800; color: #1e9460; }
    .w3eden .dropdown-menu > li{ margin-bottom: 0; }
    .w3eden .dropdown-menu > li > a{ padding: 5px 20px; }
    a.list-item{ display: block; padding: 0 20px; line-height: 40px; color: #666666; text-decoration: none; font-size: 11px; }
    a.list-item:hover { text-decoration: none; }
    a.list-item:not(:last-child) { border-bottom: 1px solid #dddddd; }
</style>
