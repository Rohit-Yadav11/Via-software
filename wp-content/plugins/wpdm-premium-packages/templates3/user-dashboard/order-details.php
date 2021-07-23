<?php
/**
 * Order details in Frontend User Dashboard >> Purchases >> Order details page
 *
 * This template can be overridden by copying it to yourtheme/download-manager/user-dashboard/order-details.php.
 *
 * @version     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $wpdb, $sap, $wpdmpp_settings, $current_user;
$wpdmpp_settings['order_validity_period'] = (int)$wpdmpp_settings['order_validity_period'] > 0 ? (int)$wpdmpp_settings['order_validity_period'] : 365;

$order_notes    = '';
$orderObj          = new \WPDMPP\Libs\Order($params[2]);
$orderurl       = get_permalink(get_the_ID());
$loginurl       = home_url("/wp-login.php?redirect_to=".urlencode($orderurl));
$csign          = wpdmpp_currency_sign();
$csign_before   = wpdmpp_currency_sign_position() == 'before' ? $csign : '';
$csign_after    = wpdmpp_currency_sign_position() == 'after' ? $csign : '';
$link           = get_permalink()."?udb_page=purchases/";
$o              = $orderObj;
$order          = $orderObj->getOrder($params[2]);
$extbtns        = "";
$extbtns        = apply_filters("wpdmpp_order_details_frontend", $extbtns, $order);

//Check order status
if($order->expire_date == 0 && get_wpdmpp_option('order_validity_period', 365) > 0) {
    $expire_date    = $order->date + (get_wpdmpp_option('order_validity_period', 365)*86400);
    $orderObj->set('expire_date', $expire_date);
    if(time() > $expire_date){
        $orderObj->set('order_status', 'Expired');
        $orderObj->set('payment_status', 'Expired');
        $order->order_status = 'Expired';
        $order->payment_status = 'Expired';
    }
    $orderObj->save();
}

$date = date("Y-m-d h:i a",$order->date);
$items = maybe_unserialize($order->items);
$expire_date = $order->expire_date;


$renews = $wpdb->get_results("select * from {$wpdb->prefix}ahm_order_renews where order_id='".esc_sql($orderObj->oid)."'");

if( $order->uid == 0 ) {
    $order->uid = $current_user->ID;
    $o->update( array('uid' => $current_user->ID), $order->order_id );
}

if( $order->uid == $current_user->ID ) {

    $order->currency    = maybe_unserialize($order->currency);
    $csign              = isset($order->currency['sign']) ? $order->currency['sign'] : '$';
    $csign_before       = wpdmpp_currency_sign_position() == 'before' ? $csign : '';
    $csign_after        = wpdmpp_currency_sign_position() == 'after' ? $csign : '';
    $cart_data          = maybe_unserialize($order->cart_data);
    $items              = \WPDMPP\Libs\Order::GetOrderItems($order->order_id);

    if ( count( $items ) == 0 ) {
        foreach ( $cart_data as $pid => $noi ) {
            $newi = get_posts(array('post_type' => 'wpdmpro', 'meta_key' => '__wpdm_legacy_id', 'meta_value' => $pid));
            if(count($newi) > 0) {
                $new_cart_data[$newi[0]->ID] = array("quantity" => $noi, "variation" => "", "price" => get_post_meta($newi[0]->ID, "__wpdm_base_price", true));
                $new_order_items[] = $newi[0]->ID;
            }
        }

        \WPDMPP\Libs\Order::Update(array('cart_data' => serialize($new_cart_data), 'items' => serialize($new_order_items)), $order->order_id);
        \WPDMPP\Libs\Order::UpdateOrderItems($new_cart_data, $order->order_id);
        $items = \WPDMPP\Libs\Order::GetOrderItems($order->order_id);
    }

    $order->title = $order->title ? $order->title : sprintf(__('Order # %s', 'wpdm-premium-packages') , $order->order_id);

    $colspan = 6;
    $coupon_discount = $role_discount = 0;
    foreach ($items as $item) {
        $coupon_discount += $item['coupon_discount'];
        $role_discount += $item['role_discount'];
    }
    if($coupon_discount == 0) $colspan --;
    if($role_discount == 0) $colspan --;
    if ( $order->order_status !== 'Completed' ) $colspan --;

    ?>


    <?php
    if(\WPDM\Session::get('wpdm_global_msg_success')){
        echo "<div class='alert alert-success'>".\WPDM\Session::get('wpdm_global_msg_success')."</div>";
        \WPDM\Session::clear('wpdm_global_msg_success');
    }
    if(\WPDM\Session::get('wpdm_global_msg_error')){
        echo "<div class='alert alert-danger'>".\WPDM\Session::get('wpdm_global_msg_error')."</div>";
        \WPDM\Session::clear('wpdm_global_msg_error');
    }
    ?>

    <div class="panel panel-default panel-purchases dashboard-panel">
        <div class="panel-heading">
            <?php if ( $order->order_status == 'Completed' ) { //Show invoice button ?>
                <span class="pull-right btn-group">
                    <button id="od-fullwidth-view" class="btn btn-xs btn-primary ttip hidden-xs" title="Toggle Full-Width"><i class="fa fa-arrows-alt-h"></i></button>
                    <a class="btn btn-info btn-xs white btn-invoice" href="#"
                       onclick="window.open('?id=<?php echo $order->order_id; ?>&amp;wpdminvoice=1','Invoice','height=720, width = 750, toolbar=0'); return false;">
                        <i class="fa fa-file-text-o"></i> <?php _e('Invoice', 'wpdm-premium-packages'); ?>
                    </a>
                    <?php echo $extbtns; ?>
                </span>
                <a href="<?php echo $link; ?>" style="display:inline;with:auto;"><?php _e("All Orders","wpdm-premium-packages"); ?></a> &nbsp;<i class="fa fa-angle-double-right"></i>&nbsp;<?php echo $order->title; ?>
            <?php } else { ?>
                <span class="pull-right btn-group">
                    <button id="od-fullwidth-view" class="btn btn-xs btn-primary ttip hidden-xs" title="Toggle Full-Width"><i class="fa fa-arrows-alt-h"></i></button>
                    <?php echo $extbtns; ?>
                </span>
                <a href="<?php echo $link; ?>"><?php _e("All Orders","wpdm-premium-packages"); ?></a>&nbsp;<i class="fa fa-angle-double-right"></i>&nbsp;<?php echo $order->title; ?>
            <?php } ?>
        </div>
        <div class="panel-body1">
            <table class="table wpdm-table-clean" style="margin:0">
                <thead>
                <tr>
                    <th><?php _e("Product","wpdm-premium-packages"); ?></th>
                    <th class="hidden-xs"><?php _e("Quantity","wpdm-premium-packages"); ?></th>
                    <th class="hidden-xs"><?php _e("Unit Price","wpdm-premium-packages"); ?></th>
                    <?php if($coupon_discount > 0){ ?>
                        <th class="hidden-xs"><?php _e("Coupon Discount","wpdm-premium-packages"); ?></th>
                    <?php }
                    if($role_discount > 0){
                        ?>
                        <th class="hidden-xs"><?php _e("Role Discount","wpdm-premium-packages"); ?></th>
                    <?php } ?>
                    <th><?php _e("License","wpdm-premium-packages"); ?></th>
                    <th class='text-right' align='right'><?php _e("Total","wpdm-premium-packages"); ?></th>
                    <?php if ($order->order_status == 'Completed') { ?>
                    <th class='text-right' align='right' style="width: 100px"><?php _e("Download","wpdm-premium-packages"); ?></th>
                    <?php } ?>
                </tr>
                </thead>
                <?php
                $total = 0;

                foreach ($items as $item) {
                    $ditem = get_post($item['pid']);

                    if ( ! is_object($ditem) || get_post_type($item['pid']) != 'wpdmpro' ) {
                        $ditem              = new stdClass();
                        $ditem->ID          = 0;
                        $ditem->post_title  = "[Item Deleted]";
                    }

                    $meta           = get_post_meta($ditem->ID, 'wpdmpp_list_opts', true);
                    $price          = $item['price'] * $item['quantity'];
                    $discount_r     = $item['role_discount'];
                    $prices         = 0;
                    $variations     = "";
                    $discount       = $discount_r;
                    $_variations    = unserialize($item['variations']);

                    foreach ($_variations as $vr) {
                        $variations .= "{$vr['name']}: +{$csign_before}" . number_format(floatval($vr['price']), 2) . $csign_after;
                        $prices     += number_format(floatval($vr['price']), 2);
                    }

                    $itotal         = number_format(((($item['price'] + $prices) * $item['quantity']) - $discount - $item['coupon_discount']), 2, ".", "");
                    $total          += $itotal;
                    $download_link  = \WPDMPP\WPDMPremiumPackage::customerDownloadURL($item['pid'], $order->order_id); //home_url("/?wpdmdl={$item['pid']}&oid={$order->order_id}");
                    $licenseurl     = home_url("/?task=getlicensekey&file={$item['pid']}&oid={$order->order_id}");
                    $order_item     = "";

                    $license = maybe_unserialize($item['license']);

                    $license = isset($license['info'], $license['info']['name'])?'<span class="ttip color-purple" title="'.esc_html($license['info']['description']).'">'.sprintf(__("%s License","wpdm-premium-packages"), $license['info']['name']).'</span>':'';


                    if ($order->order_status == 'Completed') {
                        if (get_post_meta($item['pid'], '__wpdm_enable_license_key', true) == 1) {
                            $licenseg = <<<LIC
<a id="lic_{$item['pid']}_{$order->order_id}_btn" onclick="return getkey('{$item['pid']}','{$order->order_id}', '#'+this.id);" class="btn btn-primary btn-xs" data-placement="top" data-toggle="popover" href="#"><i class="fa fa-key white"></i></a>
LIC;
                        } else
                            $licenseg = "&mdash;";

                        $indf   = "";
                        $files  = maybe_unserialize(get_post_meta($item['pid'], '__wpdm_files', true));
                        $cart   = maybe_unserialize($order->cart_data);
                        $sfiles = isset($cart[$item['pid']], $cart[$item['pid']]['files'])?$cart[$item['pid']]['files']: array();
                        $sfiles = is_array($sfiles) ? array_keys($sfiles): array();
                        $cfiles = array();

                        foreach ($sfiles as $fID){
                            $cfiles[$fID] = $files[$fID];
                        }

                        if(count($cfiles) === 0) {
                            $license = wpdm_valueof($cart, "{$item['pid']}/license/id");
                            $license_pack = get_post_meta($item['pid'], "__wpdm_license_pack", true);
                            $license_pack = wpdm_valueof($license_pack, $license);
                            foreach ($license_pack as $fID) {
                                $cfiles[$fID] = $files[$fID];
                            }
                        }



                        if(count($cfiles) > 0)
                            $files = $cfiles;

                        if (count($files) > 1 && $order->order_status == 'Completed') {
                            $index = 0;

                            foreach ($files as $ind => $ff) {
                                $data = get_post_meta($ditem->ID,'__wpdm_fileinfo', true);
                                $title = isset($data[$ind], $data[$ind]['title']) ? $data[$ind]['title'] : basename($ff);
                                $index = $ind;
                                $ff = "<li class='list-group-item'>" . $title . " <a class='btn btn-xs btn-success pull-right' href=\"{$download_link}&ind={$index}\">".__("Download","download-manager")."</a></li>";
                                $indf .= "$ff";
                            }
                        }
                        $discount = number_format(floatval($discount), 2);
                        $item['price'] = number_format($item['price'], 2);
                        ?>

                        <tr class="item">
                        <td><div><strong><a target="_blank" href="<?php echo get_permalink($ditem->ID); ?>"><?php echo $ditem->post_title; ?></a></strong></div><div><?php echo $variations; ?></div><div class="color-purple"><?php echo $license; ?></div></td>
                        <td class="hidden-xs"><?php echo $item['quantity']; ?></td>
                        <td class="hidden-xs"><?php echo $csign_before.$item['price'].$csign_after; ?></td>
                        <?php if($coupon_discount > 0){ ?>
                            <td class="hidden-xs"><?php echo $csign_before.$item['coupon_discount'].$csign_after; ?></td>
                        <?php }
                        if($role_discount > 0){
                            ?>
                            <td class="hidden-xs"><?php echo $csign_before.$discount.$csign_after; ?></td>
                        <?php } ?>
                        <td id="lic_<?php echo $item['pid'].'_'.$order->order_id; ?>" ><?php echo $licenseg; ?></td>
                        <td class='text-right' align='right'><?php echo $csign_before.$itotal.$csign_after; ?></td>

                    <?php } else {

                        $discount = number_format(floatval($discount), 2);
                        $item['price'] = number_format($item['price'], 2);

                        ?>
                        <tr class="item">
                        <td><?php echo $ditem->post_title.'<br>'.$variations; ?></td>
                        <td class="hidden-xs"><?php echo $item['quantity']; ?></td>
                        <td class="hidden-xs"><?php echo $csign_before.$item['price'].$csign_after; ?></td>
                        <?php if($coupon_discount > 0){ ?>
                        <td class="hidden-xs"><?php echo $csign_before.$item['coupon_discount'].$csign_after; ?></td>
                        <?php } ?>
                        <?php if($role_discount > 0){ ?>
                        <td class="hidden-xs"><?php echo $csign_before.$discount.$csign_after; ?></td>
                        <?php } ?>
                        <td>&mdash;</td>
                        <td class='text-right' align='right'><?php echo $csign_before.$itotal.$csign_after; ?></td>
                        <?php
                    }

                    if ($order->order_status == 'Completed') {

                        $spec = "";
                        if (count($files) > 1)
                            $spec = <<<SPEC
<a class="btn btn-xs btn-success btn-group-item" href="#" data-toggle="modal" data-target="#dpop" onclick="jQuery('#dpop .modal-body').html(jQuery('#indvd-{$ditem->ID}').html());"><i class="fa fa-list"></i></a></div><div  id="indvd-{$ditem->ID}" style="display:none;"><ul class='list-group'>{$indf}</ul>
SPEC;
                        ?>

                        <td class='text-right' align='right'>
                            <div class="btn-group">
                                <a href="<?php echo $download_link; ?>" class="btn btn-xs btn-success btn-group-item"><i class="fa fa-arrow-alt-circle-down white"></i></a>
                                <?php echo $spec; ?>
                            </div>
                        </td>
                        </tr>
                    <?php } else { ?>

                        </tr>
                    <?php }

                    $order_item = apply_filters("wpdmpp_order_item", "", $item);

                    if ($order_item != '') { ?>
                        <tr><td colspan='<?php echo $colspan+1; ?>'><?php echo $order_item; ?></td></tr>
                    <?php }
                }

                $vdlink         = __("If you still want to complete this order ", "wpdm-premium-packages");
                $vdlink_expired = sprintf(__("Get continuous support and update for another %d days", "wpdm-premium-packages"), $wpdmpp_settings['order_validity_period']);
                $pnow           = __("Pay Now", "wpdm-premium-packages");
                $pnow_expired   = __("Renew Now", "wpdm-premium-packages");
                $usermeta       = maybe_unserialize(get_user_meta($current_user->ID, 'user_billing_shipping', true));

                if(is_array($usermeta)) extract($usermeta);

                $order->cart_discount   = number_format($order->cart_discount, 2, ".", "");
                $order->total           = number_format($order->total, 2, ".", "");
                $total                  = number_format($total, 2, ".", "");
                ?>

                <tr class="item hidden-xs">
                    <td colspan="<?php echo ($order->order_status == 'Completed') ? $colspan : $colspan + 1; ?>" class='text-right' align='right'><b><?php _e("Subtotal","wpdm-premium-packages"); ?></b></td>
                    <td class='text-right' align='right'><b><?php echo $csign_before.$total.$csign_after; ?></b></td>
                    <?php if ($order->order_status == 'Completed') { ?>
                    <td>&nbsp;</td>
                    <?php } ?>
                </tr>
                <tr class="item hidden-xs">
                    <td colspan="<?php echo ($order->order_status == 'Completed') ? $colspan : $colspan + 1; ?>" class='text-right' align='right'><b><?php _e("Coupon Discount","wpdm-premium-packages"); ?></b></td>
                    <td class='text-right' align='right'><b>- <?php echo $csign_before.$order->coupon_discount.$csign_after; ?></b></td>
                    <?php if ($order->order_status == 'Completed') { ?>
                        <td>&nbsp;</td>
                    <?php } ?>
                </tr>
                <tr class="item hidden-xs">
                    <td colspan="<?php echo ($order->order_status == 'Completed') ? $colspan : $colspan + 1; ?>" class='text-right' align='right'><b><?php _e("Tax","wpdm-premium-packages"); ?></b></td>
                    <td class='text-right' align='right'><b>+ <?php echo $csign_before.$order->tax.$csign_after; ?></b></td>
                    <?php if ($order->order_status == 'Completed') { ?>
                        <td>&nbsp;</td>
                    <?php } ?>
                </tr>
                <tr class="item hidden-xs">
                    <td colspan="<?php echo ($order->order_status == 'Completed') ? $colspan : $colspan + 1; ?>" class='text-right' align='right'><b><?php _e("Total","wpdm-premium-packages"); ?></b></td>
                    <td class='text-right' align='right'><b><?php echo $csign_before.$order->total.$csign_after; ?></b></td>
                    <?php if ($order->order_status == 'Completed') { ?>
                        <td>&nbsp;</td>
                    <?php } ?>
                </tr>
            </table>
        </div>
        <?php if(isset($wpdmpp_settings['auto_renew']) && $wpdmpp_settings['auto_renew'] == 1 && $order->order_status == 'Completed'){ ?>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-md-9">
                        Auto Renew <?php echo $order->auto_renew == 1?'<span id="csstatus" class="badge badge-success">Active</span>':'<span class="badge badge-danger">Inactive</span>'; ?>
                        <?php echo $order->auto_renew == 1?'Next Renew':'Expiry'; ?> Date <span class="badge badge-secondary"><?php echo date(get_option('date_format'),  $order->expire_date); ?></span>
                        Payment Method <span class="badge badge-secondary"><?php echo str_replace("WPDM_", "", $order->payment_method); ?></span>
                    </div>
                    <div class="col-md-3 text-right">
                        <?php echo $order->auto_renew == 1?'<button data-toggle="modal" data-target="#cansubModal" id="btn-acs" class="btn btn-danger btn-xs">Cancel Subscription</button>':'<button disabled="disabled" id="btn-actsub" class="btn btn-success btn-xs">Activate</button>'; ?>

                    </div>
                </div>
            </div>


            <div class="modal fade" id="cansubModal">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="width: 400px">
                        <div class="modal-header">

                            <strong class="modal-title"><?php _e('Cancel Subscription!','wpdm-premium-packages'); ?></strong>

                        </div>
                        <div class="modal-body">
                            <p>
                                If you cancel auto-renew feature, you will be blocked from support forum and plugin updates after subscription period expires ( <?php echo date(get_option('date_format'),  $order->expire_date); ?> ).<br/>
                                <strong>Are you sure?</strong>
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success btn-xs" id="btn-ka" data-dismiss="modal"><?php _e('Keep Subscription Active','wpdm-premium-packages'); ?></button>
                            <button type="button" class="btn btn-danger btn-xs" id="btn-cansub"><?php _e('Cancel Subscription','wpdm-premium-packages'); ?></button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->

        <?php } ?>
        <div class="modal fade" id="dpop">
            <div class="modal-dialog" style="margin-top:100px;">
                <div class="modal-content">
                    <div class="modal-header">

                        <strong class="modal-title"><?php _e('Download Single File','wpdm-premium-packages'); ?></strong>

                    </div>
                    <div class="modal-body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-xs" data-dismiss="modal"><?php _e('Close','wpdm-premium-packages'); ?></button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->



        <?php

        if ($order->order_status != 'Completed') {
            if ($order->order_status == 'Expired') {
                $vdlink = $vdlink_expired;
                $pnow = $pnow_expired;
            }
            $purl = home_url('/?pay_now=' . $order->order_id);
            ?>

            <div class="panel-footer" style="line-height: 30px !important;">
                <b><?php _e("Order Status", "wpdm-premium-packages"); ?> : <span class='label label-danger'><?php echo $order->order_status; ?></span></b>&nbsp;&nbsp;
                <div class='pull-right'>
                    <?php echo $vdlink; ?>
                    <div class="pull-right" style="margin-left:10px" id="proceed_<?php echo $order->order_id; ?>">
                        <a class='btn btn-success white btn-sm' onclick="return proceed2payment_<?php echo $order->order_id; ?>(this)" href="#"><b><?php echo $pnow; ?></b></a>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <script>
                function proceed2payment_<?php echo $order->order_id; ?>(ob){
                    jQuery('#proceed_<?php echo $order->order_id; ?>').html('Processing...');
                    jQuery.post('<?php echo $purl; ?>',{action:'wpdmpp_anync_exec',execute:'PayNow',order_id:'<?php echo $order->order_id; ?>'},function(res){
                        jQuery('#proceed_<?php echo $order->order_id; ?>').html(res);
                    });
                    return false;
                }
            </script>

        <?php }

        $homeurl = home_url('/');
        ?>
    </div>



    <script>


        jQuery(function($){
            var fullwidth = 0;
            $('body').on('click','#od-fullwidth-view', function(){
                fullwidth = fullwidth == 0 ? 1 : 0;
                $('#wpdm-dashboard-sidebar').toggle();
                $('#wpdm-dashboard-contents').toggleClass('col-md-9 col-md-12');
            });

            $('#btn-cansub').on('click', function () {
                var $this = $(this);
                $('#btn-ka, #btn-acs').attr('disabled','disabled');
                $this.attr('disabled','disabled').html('<i class="fa fa-spin fa-refresh"></i>');
                $.post(wpdm_ajax_url, {action: 'wpdmpp_cancel_subscription', __cansub: '<?php echo wp_create_nonce(NONCE_KEY) ?>', orderid: '<?php echo $order->order_id ?>'}, function (res) {
                    $this.html('Canceled');
                    $('#csstatus').removeClass('badge-success').addClass('badge-danger').html('Inactive');
                    $('#btn-acs').html('Canceled');
                    $('#cansubModal').modal('hide');
                });
            });
        });

        //To show license TaCs
        function viewlic(file, order_id){
            var res = " You have to accept these Terms and Conditions before using this product.";
            jQuery('#lic_'+file+'_'+order_id+'_view').html("<i class='fa fa-spin fa-spinner white'></i>");
            jQuery('#lic_'+file+'_'+order_id+'_view').popover({html: true, title: "Terms and Conditions<button class='pull-right btn btn-danger btn-xs xx' rel='#lic_"+file+"_"+order_id+"_view' id='cppo'>&times;</button>", content: res}).popover('show');
            jQuery('#lic_'+file+'_'+order_id+'_view').html("<i class='fa fa-copy white'></i>");

            jQuery('.xx').on("click",function(e) {
                jQuery(jQuery(this).attr("rel")).popover('destroy');
                return false;
            });

            return false;
        }
    </script>
    <style>.white{ color: #ffffff !important; } </style>

    <?php

    include wpdm_tpl_path('partials/renew-invoices.php', WPDMPP_TPL_DIR);

    include wpdm_tpl_path('partials/order-notes.php', WPDMPP_TPL_DIR);
} else { ?>
    <div class='alert alert-danger'><?php _e('Order does not belong to you!','wpdm-premium-packages'); ?></div>
<?php }
