<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

global $wpdb;
$osi = array('Pending'=>'ellipsis-h','Processing'=>'paw','Completed'=>'check','Cancelled'=>'times','Refunded'=>'retweet','Expired' => ' fas fa-exclamation-triangle','Gifted' => 'gift','Disputed'=>'gavel');

$completed = $wpdb->get_row("select sum(total) as sales, count(total) as orders from {$wpdb->prefix}ahm_orders where payment_status='Completed' or payment_status='Expired'");
$expired = $wpdb->get_row("select sum(total) as sales, count(total) as orders from {$wpdb->prefix}ahm_orders where payment_status='Expired'");
$refunded = $wpdb->get_row("select sum(total) as sales, count(total) as orders from {$wpdb->prefix}ahm_orders where payment_status='Refunded'");
$abandoned = $wpdb->get_row("select sum(total) as sales, count(total) as orders from {$wpdb->prefix}ahm_orders where payment_status='Processing'");
$renews = $wpdb->get_results("select count(*) as renew_cycle, order_id from {$wpdb->prefix}ahm_order_renews GROUP  BY order_id");
$renew_cycle = array();
foreach ($renews as $renew){
    $renew_cycle[$renew->order_id] = $renew->renew_cycle;
}
?>

<div class="w3eden admin-orders">
    <div class="panel panel-default" id="wpdm-wrapper-panel">
        <div class="panel-heading">
            <b><i class="fas fa-cart-arrow-down color-purple"></i> &nbsp; <?php _e("Orders","wpdm-premium-packages");?></b>
            <div class="pull-right">
                <?php do_action("wpdmpp_orders_action_buttons"); ?>
                <a href="edit.php?post_type=wpdmpro&page=orders&task=createorder" id="addorder" class="btn btn-success btn-sm wpdm-whatsapp"><i class="fas fa-plus-circle"></i> <?php _e('Create Order', 'wpdm-premium-packages'); ?></a>
                <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm" id="delete_selected"><i class="fas fa-trash"></i> <?php _e('Delete Selected','wpdm-premium-packages'); ?></button>
                    <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="#" class="delete-all-sts" data-status="Processing"><?php _e('Processing Orders', 'wpdm-premium-packages'); ?></a></li>
                        <li><a href="#" class="delete-all-sts" data-status="Cancelled"><?php _e('Cancelled Orders', 'wpdm-premium-packages'); ?></a></li>
                        <li><a href="#" class="delete-all-sts" data-status="Disputed"><?php _e('Disputed Orders', 'wpdm-premium-packages'); ?></a></li>
                        <li><a href="#" class="delete-all-sts" data-status="Refunded"><?php _e('Refunded Orders', 'wpdm-premium-packages'); ?></a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <br/><br/><br/>

            <?php
            if(isset($msg)):
                echo "<div class='alert alert-info alert-floating'>$msg</div>";
            endif;
            ?>

            <div class="order summery row">

                <div class="col-sm-3">
                    <div class="panel panel-default text-center">
                        <div class="panel-heading"><?php echo __( "Completed Orders", "download-manager" ); ?></div>
                        <div class="panel-body"><h3 class="color-green"><?php echo wpdmpp_price_format($completed->sales, true, true); ?> / <?php echo $completed->orders; ?></h3></div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="panel panel-default text-center">
                        <div class="panel-heading"><?php echo __( "Refunded Orders", "download-manager" ); ?></div>
                        <div class="panel-body"><h3 class="color-red"><?php echo wpdmpp_price_format($refunded->sales, true, true); ?> / <?php echo $refunded->orders; ?></h3></div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="panel panel-default text-center">
                        <div class="panel-heading"><?php echo __( "Abandoned Orders", "download-manager" ) ?></div>
                        <div class="panel-body"><h3 class="color-blue"><?php echo wpdmpp_price_format($abandoned->sales, true, true); ?> / <?php echo $abandoned->orders; ?></h3></div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="panel panel-default text-center">
                        <div class="panel-heading"><?php echo __( "Expired Orders", "download-manager" ); ?></div>
                        <div class="panel-body">

                            <h3 class="color-purple">
                                <?php echo wpdmpp_price_format($expired->sales, true, true); ?> / <?php echo $expired->orders; ?>
                                &nbsp;
                            </h3>
                    </div>
                </div>


            </div>
            <div class="clear"></div>
            <div class="col-md-12">
            <form method="get" action="" id="order-search">

                <input type="hidden" name="post_type" value="wpdmpro">
                <input type="hidden" name="page" value="orders">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-2"><select class="select-action form-control wpdm-custom-select" name="ost">
                                    <option value=""><?php _e('Order status:','wpdm-premium-packages'); ?></option>
                                    <option value="Pending" <?php if(isset($_REQUEST['ost'])) echo $_REQUEST['ost']=='Pending'?'selected=selected':''; ?>>Pending</option>
                                    <option value="Processing" <?php if(isset($_REQUEST['ost'])) echo $_REQUEST['ost']=='Processing'?'selected=selected':''; ?>>Processing</option>
                                    <option value="Completed" <?php if(isset($_REQUEST['ost'])) echo $_REQUEST['ost']=='Completed'?'selected=selected':''; ?>>Completed</option>
                                    <option value="Cancelled" <?php if(isset($_REQUEST['ost'])) echo $_REQUEST['ost']=='Cancelled'?'selected=selected':''; ?>>Cancelled</option>
                                    <option value="Expiring" <?php if(isset($_REQUEST['ost'])) echo $_REQUEST['ost']=='Expiring'?'selected=selected':''; ?>>Expiring ( On Selected Period )</option>
                                </select></div>
                            <div class="col-md-2"><select class="select-action form-control wpdm-custom-select" name="pst">
                                    <option value=""><?php _e('Payment status:','wpdm-premium-packages'); ?></option>
                                    <option value="Pending" <?php if(isset($_REQUEST['pst'])) echo $_REQUEST['pst']=='Pending'?'selected=selected':''; ?>>Pending</option>
                                    <option value="Processing" <?php if(isset($_REQUEST['pst'])) echo $_REQUEST['pst']=='Processing'?'selected=selected':''; ?>>Processing</option>
                                    <option value="Completed" <?php if(isset($_REQUEST['pst'])) echo $_REQUEST['pst']=='Completed'?'selected=selected':''; ?>>Completed</option>
                                    <option value="Bonus" <?php if(isset($_REQUEST['pst'])) echo $_REQUEST['pst']=='Bonus'?'selected=selected':''; ?>>Bonus</option>
                                    <option value="Gifted" <?php if(isset($_REQUEST['pst'])) echo $_REQUEST['pst']=='Gifted'?'selected=selected':''; ?>>Gifted</option>
                                    <option value="Cancelled" <?php if(isset($_REQUEST['pst'])) echo $_REQUEST['pst']=='Cancelled'?'selected=selected':''; ?>>Cancelled</option>
                                    <option value="Disputed" <?php if(isset($_REQUEST['pst'])) echo $_REQUEST['pst']=='Disputed'?'selected=selected':''; ?>>Disputed</option>
                                    <option value="Refunded" <?php if(isset($_REQUEST['pst'])) echo $_REQUEST['pst']=='Refunded'?'selected=selected':''; ?>>Refunded</option>
                                </select></div>
                            <div class="col-md-1"><input  class="form-control datep" type="text" placeholder="<?php _e("From Date","wpdm-premium-packages");?>" name="sdate" value="<?php if(isset($_REQUEST['sdate'])) echo esc_attr($_REQUEST['sdate']); ?>"></div>
                            <div class="col-md-1"><input  class="form-control datep" type="text" placeholder="<?php _e("To Date","wpdm-premium-packages");?>" name="edate" value="<?php if(isset($_REQUEST['edate'])) echo esc_attr($_REQUEST['edate']); ?>"></div>
                            <div class="col-md-2"><input  class="form-control" type="text" placeholder="<?php _e("Order ID","wpdm-premium-packages");?> " name="oid" value="<?php if(isset($_REQUEST['oid'])) echo esc_attr($_REQUEST['oid']); ?>"></div>
                            <div class="col-md-2"><input  class="form-control" type="text" placeholder="<?php _e("Customer ID / Email / Username","wpdm-premium-packages");?> " name="customer" value="<?php if(isset($_REQUEST['customer'])) echo esc_attr($_REQUEST['customer']); ?>"></div>
                            <div class="col-md-2"><button style="margin: 0" type="submit" class="btn btn-secondary btn-block" id="doaction" name="doaction"><i class="fas fa-search"></i> <?php _e('Search','wpdm-premium-packages'); ?></button></div>
                        </div>

                    </div>
                    <div class="panel-footer"><?php
                        // Calculate Total Sales

                        ?>
                        <span class="pull-right color-green"> <b><?php _e("Total Sales:","wpdm-premium-packages");?> <?php echo wpdmpp_price_format($completed->sales, true, true); ?></b></span>
                        <b><?php echo $completed->orders; ?> <?php _e("order(s) found","wpdm-premium-packages");?></b>
                    </div>
                </div>
            </form>
            <div class="clear"></div>
            <form method="get" action="<?php echo admin_url('/edit.php'); ?>" id="orders-form">
                <input type="hidden" name="post_type" value="wpdmpro">
                <input type="hidden" name="page" value="orders">

                <?php if(wpdm_query_var('ost') == 'Expiring'){ ?>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <ul>
                                <li><input type="checkbox" checked="checked" disabled="disabled" style="margin: 0"> Update order status to expired</li>
                                <li><input type="checkbox" checked="checked" disabled="disabled" style="margin: 0"> Send email notification to customers</li>
                            </ul>
                        </div>
                        <div class="panel-footer">
                            <a href="#" class="btn btn-primary" id="expire-orders">Execute</a>
                        </div>
                    </div>
                <?php } ?>

                <div class="panel panel-default">
                <table cellspacing="0" class="table table-striped table-wpdmpp">
                    <thead>
                    <tr>
                        <th style="width: 40px" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
                        <th style="width: 40px" class="manage-column" id="media" scope="col">
                            <div class="w3eden">
                    <span class="fa-stack ttip" title="<?php _e("Order Status","wpdm-premium-packages");?>">
                        <i class="fa fa-circle-thin fa-stack-2x"></i>
                        <i class="fas fa-cart-arrow-down fa-stack-1x"></i>
                    </span>
                            </div>
                        </th>
                        <th style="" class="manage-column" id="media" scope="col"><?php _e("Order","wpdm-premium-packages");?></th>
                        <th style="width: 40px" class="manage-column" id="media" scope="col">
                            <div class="w3eden">
                    <span class="fa-stack ttip" title="<?php _e("Payment Status","wpdm-premium-packages");?>">
                        <i class="fa fa-circle-thin fa-stack-2x"></i>
                        <i class="far fa-money-bill-alt fa-stack-1x"></i>
                    </span>
                            </div>
                        </th>
                        <th style="width: 150px" class="manage-column" id="author" scope="col"><?php _e("Total","wpdm-premium-packages");?></th>
                        <th style="" class="manage-column" id="author" scope="col"><?php _e("Customer","wpdm-premium-packages");?></th>
                        <th style="width: 200px" class="manage-column column-parent" id="parent" scope="col"><?php _e("Order Date","wpdm-premium-packages");?></th>
                        <th style="" class="manage-column" id="parent" scope="col"><?php _e("Renew Cycle","wpdm-premium-packages");?></th>
                        <th style="" class="manage-column" id="parent" scope="col"><a href="edit.php?post_type=wpdmpro&page=orders&paged=<?php echo wpdm_query_var('paged') ?>&orderby=expire_date&order=<?php echo wpdm_query_var('order') == 'asc'?'desc':'asc';  ?>"><?php _e("Expire Date","wpdm-premium-packages");?> <?php if(wpdm_query_var('order') == 'desc') echo '&#9660;'; if(wpdm_query_var('order') == 'asc') echo '&#9650;';  ?></a></th>
                        <th style="width: 40px" class="manage-column" id="parent" scope="col">
                            <div class="w3eden">
                    <span class="fa-stack ttip" title="<?php _e('Item Download Status','wpdm-premium-packages'); ?>">
                        <i class="fa fa-circle-thin fa-stack-2x"></i>
                        <i class="fa fa-download fa-stack-1x"></i>
                    </span>
                            </div>
                        </th>
                        <th style="width: 40px" class="manage-column" id="parent" scope="col">
                            <div class="w3eden">
                    <span class="fa-stack ttip" title="<?php _e('Auto Renew Status','wpdm-premium-packages'); ?>">
                        <i class="fa fa-circle-thin fa-stack-2x"></i>
                        <i class="fa fa-sync fa-stack-1x"></i>
                    </span>
                            </div>
                        </th>
                        <?php do_action("wpdmpp_orders_custom_column_th"); ?>
                    </tr>
                    </thead>

                    <tfoot>
                    <tr>
                        <th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
                        <th style="" class="manage-column" id="media" scope="col" title="<?php _e("Order Status","wpdm-premium-packages");?>">
                <span class="fa-stack">
                    <i class="fa fa-circle-thin fa-stack-2x"></i>
                    <i class="fas fa-cart-arrow-down fa-stack-1x"></i>
                </span>
                        </th>
                        <th style="" class="manage-column" id="media" scope="col"><?php _e("Order","wpdm-premium-packages");?></th>
                        <th style="width: 40px" class="manage-column" id="media" scope="col" title="<?php _e("Payment Status","wpdm-premium-packages");?>">
                <span class="fa-stack">
                    <i class="fa fa-circle-thin fa-stack-2x"></i>
                    <i class="far fa-money-bill-alt fa-stack-1x"></i>
                </span>
                        </th>
                        <th style="" class="manage-column" id="author" scope="col"><?php _e("Total","wpdm-premium-packages");?></th>
                        <th style="" class="manage-column " id="author" scope="col"><?php _e("Customer","wpdm-premium-packages");?></th>
                        <th style="" class="manage-column" id="parent" scope="col"><?php _e("Order Date","wpdm-premium-packages");?></th>
                        <th style="" class="manage-column" id="parent" scope="col"><?php _e("Renew Cycle","wpdm-premium-packages");?></th>
                        <th style="" class="manage-column" id="parent" scope="col"><?php _e("Expire Date","wpdm-premium-packages");?></th>
                        <th style="width: 40px" class="manage-column" id="parent" scope="col">
                            <div class="w3eden">
                    <span class="fa-stack ttip" title="<?php _e('Item Download Status','wpdm-premium-packages'); ?>">
                        <i class="fa fa-circle-thin fa-stack-2x"></i>
                        <i class="fa fa-download fa-stack-1x"></i>
                    </span>
                            </div>
                        </th>
                        <th style="width: 40px" class="manage-column" id="parent" scope="col">
                            <div class="w3eden">
                    <span class="fa-stack ttip" title="<?php _e('Auto Renew Status','wpdm-premium-packages'); ?>">
                        <i class="fa fa-circle-thin fa-stack-2x"></i>
                        <i class="fa fa-sync fa-stack-1x"></i>
                    </span>
                            </div>
                        </th>
                        <?php do_action("wpdmpp_orders_custom_column_th"); ?>
                    </tr>
                    </tfoot>

                    <tbody class="list:post" id="the-list">
                    <?php
                    $z = 'alternate';
                    foreach($orders as $order) {
                        //$o = new \WPDMPP\Libs\Order();
                        //echo $o->calcOrderTotal($order->order_id);
                        //echo "<pre>".print_r($order, 1);die();
                        $user_info = get_userdata($order->uid);
                        $z = $z == 'alternate' ? '' : 'alternate';
                        $currency = maybe_unserialize($order->currency);
                        $currency = is_array($currency) && isset($currency['sign'])?$currency['sign']:'$';
                        $citems = maybe_unserialize($order->cart_data);
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
                        $items = 0;
                        if(is_array($citems)){
                            foreach($citems as $ci){
                                $items += (int)$ci['quantity'];
                            }}
                        $oitems = maybe_unserialize($order->cart_data);
                        $product_name = array();
                        if(is_array($oitems)) {
                            foreach ($oitems as $oitem) {
                                $product_name[] = $oitem['post_title'];
                            }
                        }
                        $product_names = implode(", ", $product_name);
                        $_product_name = (isset($product_name[0])) ? $product_name[0] : '';
                        $order_title = $order->title ? $order->title : $_product_name;
                        if($order->expire_date == 0)
                            $order->expire_date = $order->date + (get_wpdmpp_option('order_validity_period', 365) * 86400);

                        ?>
                        <tr valign="top" class="author-self status-inherit" id="post-8">
                            <th class="check-column" scope="row"><input type="checkbox" class="cboid" value="<?php echo $order->order_id; ?>" name="id[]"></th>
                            <td class="">
                                <div class="w3eden">
                    <span title="<?php echo $order->order_status; ?>" class="fa-stack oa-<?php echo $order->order_status; ?>">
                        <i class="fa fa-circle-thin fa-stack-2x"></i>
                        <i class="fa fa-<?php echo $osi[$order->order_status]; ?> fa-stack-1x"></i>
                    </span>
                                </div>
                            </td>
                            <td class="">
                                <strong>
                                    <a title="<?php echo __( "View Order Details", "download-manager" ) ?>" href="edit.php?post_type=wpdmpro&page=orders&task=vieworder&id=<?php echo $order->order_id; ?>"><i class="fas fa-hashtag" style="color: var(--color-primary);"></i><?php echo $order->order_id; ?></a>
                                </strong><br/>
                                <small class="text-muted">
                                    <i  class="ttip far fa-list-alt" style="color: var(--color-info);" title="<?php echo $product_names; ?>"></i> <?php echo $items; ?> <?php $items > 1 ? _e("items","wpdm-premium-packages"):_e("item","wpdm-premium-packages");?>
                                    <?php if($order->trans_id !== '') { ?><span class="ttip" title="<?php echo __( "Transaction ID", "download-manager" ) ?>"><i class="fas fa-bullseye" style="color: var(--color-primary);margin-left: 5px"></i> <?php echo apply_filters("wpdmpp_admin_order_details_trans_id", $order->trans_id, $order->payment_method); ?></span><?php } ?>
                                </small>
                            </td>
                            <td class="">
                                <div class="w3eden">
                    <span title="<?php echo $order->payment_status; ?>" class="fa-stack oa-<?php echo $order->payment_status; ?>">
                        <i class="fa fa-circle-thin fa-stack-2x"></i>
                        <i class="fa fa-<?php echo $osi[$order->payment_status]; ?> fa-stack-1x"></i>
                    </span>
                                </div>
                            </td>
                            <td class=""><strong><?php echo wpdmpp_price_format($order->total,true, true); ?></strong><br/>
                                <small class="note"><?php _e('via','wpdm-premium-packages'); echo " ".$order->payment_method; ?></small>
                            </td>
                            <td class="">
                                <?php if(is_object($user_info)){ ?>
                                    <b><a href="user-edit.php?user_id=<?php echo $user_info->ID; ?>"><?php echo $user_info->display_name; ?></a></b>
                                    <a class="text-filter" title="<?php _e('All orders placed by this customer','wpdm-premium-packages'); ?>" href="edit.php?post_type=wpdmpro&page=orders&customer=<?php echo $user_info->ID; ?>"><i class="fas fa-search"></i></a><br/>
                                    <a href="mailto:<?php echo $user_info->user_email; ?>"><?php echo $user_info->user_email; ?></a>
                                <?php } else { ?>
                                    <b><?php echo $billing['first_name'].' '.$billing['last_name']; ?></b>
                                    <a class="text-filter" href="edit.php?post_type=wpdmpro&page=orders&customer=<?php echo $billing['order_email']; ?>"><i class="fas fa-search"></i></a><br/>
                                    <a href="mailto:<?php echo $billing['order_email']; ?>"><?php echo $billing['order_email']; ?></a>
                                <?php }?>
                            </td>
                            <td class=""><?php echo wp_date(get_option('date_format'). " "  .get_option('time_format'),$order->date); ?></td>
                            <td class=""><?php echo isset($renew_cycle[$order->order_id])?sprintf(__("%d time(s)", 'wpdm-premium-packages'), $renew_cycle[$order->order_id]):__('First Purchase', 'wpdm-premium-packages'); ?></td>
                            <td class=""><?php echo wp_date(get_option('date_format'). " "  .get_option('time_format'),$order->expire_date); ?></td>
                            <td style="" class="" id="parent" scope="col">
                                <div class="w3eden">
                    <span class="fa-stack download-<?php echo $order->download==0?'off':'on'; ?> ttip" title="<?php echo $order->download==0?__('New','wpdm-premium-packages'):__('Downloaded','wpdm-premium-packages'); ?>">
                        <i class="fa fa-circle-thin fa-stack-2x"></i>
                        <i class="fa fa-toggle-<?php echo $order->download==0?'off':'on'; ?> fa-stack-1x"></i>
                    </span>
                                </div>
                            </td>
                            <td style="" class="" id="parent" scope="col">
                                <div class="w3eden">
                                    <a href="#" class="auto-renew-order" data-order="<?php echo $order->order_id; ?>">
                                        <span class="fa-stack renew-<?php echo $order->auto_renew==0?'cancelled':'active'; ?>">
                                            <i class="fa fa-circle-thin fa-stack-2x"></i>
                                            <i class="fa <?php echo $order->auto_renew==1?'fa-check':'fa-times'; ?> fa-stack-1x"></i>
                                        </span>
                                    </a>
                                </div>
                            </td>
                            <?php do_action("wpdmpp_orders_custom_column_td", $order); ?>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                </div>
                <?php

                $page_links = paginate_links( array(
                    'base' => add_query_arg( 'paged', '%#%' ),
                    'format' => '',
                    'prev_text' => '&laquo;',
                    'next_text' => '&raquo;',
                    'total' => ceil($t/$l),
                    'current' => $p
                ));
                ?>

                <div id="ajax-response"></div>

                <div class="tablenav">
                    <?php
                    if ( $page_links ) {
                        ?>
                        <div class="tablenav-pages">
                            <?php
                            $paged = wpdm_query_var('paged');
                            $paged = (int)$paged > 0?$paged:1;
                            $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
                                number_format_i18n( ( $paged - 1 ) * $l + 1 ),
                                number_format_i18n( min( $paged * $l, $t ) ),
                                number_format_i18n( $t ),
                                $page_links
                            );

                            echo $page_links_text; ?>
                        </div>
                    <?php } ?>

                    <div class="alignleft actions" style="height: 35px;">
                        <input type="hidden" id="delete_confirm" name="delete_confirm" value="0" />
                    </div>


                    <br class="clear">
                </div>

            </form>
            </div>
            <br class="clear">
        </div>
    </div>
</div>

<script>
    jQuery(function($){

        $('#order-search').submit(function (e) {
            var params = $(this).serialize();

            e.preventDefault();
            WPDM.blockUI('#orders-form');
            $(this).ajaxSubmit({
                success: function (res) {
                    $('#orders-form').html($(res).find('#orders-form').html());
                    $('#order-search').html($(res).find('#order-search').html());
                    window.history.pushState({"html":res,"pageTitle":"response.pageTitle"},"", "edit.php?"+params);
                    WPDM.unblockUI('#orders-form');
                    $('.ttip').tooltip();
                }
            });
        });

        $("#delete_selected").on('click',function(){
            if( confirm("<?php _e('Are you sure you want to delete selected orders?','wpdm-premium-packages'); ?>") ){
                $("#delete_confirm").val("1");
                WPDM.blockUI('#orders-form');
                $('#orders-form').ajaxSubmit({
                    success: function (res) {
                        $('#orders-form').html($(res).find('#orders-form').html());
                        $('#order-search').html($(res).find('#order-search').html());
                        WPDM.pushNotify("Done!", "Selected items are deleted successfully");
                        WPDM.unblockUI('#orders-form');
                    }
                });
            }
            else{
                return false;
            }
        });

        $(".delete-all-sts").on('click',function(){
            var status = $(this).data('status');
            if( confirm("<?php _e('Are you sure to delete all \'#_#\' orders?','wpdm-premium-packages'); ?>".replace("#_#", status)) ){
                location.href = "edit.php?post_type=wpdmpro&page=orders&delete_all_by_payment_sts="+status;
            }
            else{
                return false;
            }
        });

        $('span.fa-stack').tooltip({placement:'bottom', padding: 10, template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'});
        $('.datep').datetimepicker({dateFormat:"yy-mm-dd", timeFormat: "HH:mm"});

        $('body').on('click', '.auto-renew-order', function (e) {
            e.preventDefault();
            $this = $(this);
            $(this).find('.fa').removeClass('fa-check').removeClass('fa-times').addClass('fa-sync fa-spin');
            $.get(ajaxurl, {orderid: $(this).data('order'), action: 'wpdmpp_toggle_auto_renew', '__arnonce': '<?php echo wp_create_nonce(NONCE_KEY); ?>'}, function (res) {
                if(res.renew != undefined){
                    if(res.renew == 0) {
                        $this.find('.fa-stack').removeClass('renew-active').addClass('renew-cancelled');
                        $this.find('.fa').removeClass('fa-sync fa-spin').addClass('fa-times');
                    }
                    else {
                        $this.find('.fa').removeClass('fa-sync fa-spin').addClass('fa-check');
                        $this.find('.fa-stack').removeClass('renew-cancelled').addClass('renew-active');
                    }
                }
            });
        });

        var __oid = [];
        $('#expire-orders').on('click', function (e) {
            e.preventDefault();
            $('#expire-orders').html("<i class='fa fa-sun fa-spin'></i>").attr('disabled', 'disabled');
            $('.cboid').each(function (i) {
                __oid[i] = $(this).val();

            });
            $.post(ajaxurl, { action: 'wpdmpp_expire_orders', oids:__oid, __oenonce: '<?php echo wp_create_nonce(NONCE_KEY); ?>'}, function (res) {
                $('#expire-orders').html("<i class=\"fas fa-check-double\"></i> Done!");
            });
        });

        $('.ttip').tooltip();
    });
</script>

