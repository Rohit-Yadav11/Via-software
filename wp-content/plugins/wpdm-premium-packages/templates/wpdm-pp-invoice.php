<?php
/**
 * Premium Package Invoice Template
 *
 * This template can be overridden by copying it to yourtheme/download-manager/wpdm-pp-invoice.php.
 *
 * @version     1.0.0
 */
if (!defined('ABSPATH')) {
    exit;
}

if ( ! is_user_logged_in() && ! \WPDM\Session::get('guest_order') ) {

    $orderid    = isset( $_GET['id'] ) ? sanitize_text_field($_GET['id']) : '';
    $orderurl   = wpdm_user_dashboard_url().'?udb_page=purchases/order/' . $orderid;

    ?><div class="require-login">Please <a href="<?php echo wp_login_url( $orderurl ); ?>"><b>Login or Register</b></a> to access this page.</div><?php
    die();
} else {

    global $wpdb, $current_user;
    $settings           = get_option('_wpdmpp_settings');
    $_ohtml             = "";
    $oid                = sanitize_text_field($_GET['id']);
    $order              = new \WPDMPP\Libs\Order();
    $oid                = is_user_logged_in() ? $oid : \WPDM\Session::get('guest_order');
    $order              = $order->GetOrder($oid);
    $order->currency    = maybe_unserialize($order->currency);
    $csign              = is_array($order->currency) && isset($order->currency['sign']) ? $order->currency['sign'] : '$';
    $csign_before       = wpdmpp_currency_sign_position() == 'before' ? $csign : '';
    $csign_after        = wpdmpp_currency_sign_position() == 'after' ? $csign : '';

    //echo '<pre>';print_r($order);echo '</pre>';die();
    $user_billing = maybe_unserialize(get_user_meta($order->uid, 'user_billing_shipping', true));
    $user_billing = isset($user_billing['billing'])?$user_billing['billing']:array();
    $billing_defaults =  array
    (
        'first_name'    => '',
        'last_name'     => '',
        'company'       => '',
        'address_1'     => '',
        'address_2'     => '',
        'city'          => '',
        'postcode'      => '',
        'country'       => '',
        'state'         => '',
        'order_email'   => '',
        'email'   => '',
        'phone'         => '',
        'taxid'         => ''
    );

    if ( (isset( $settings['billing_address'] ) && $settings['billing_address'] == 1)  || $order->uid == 0){

        // Asked billing address in checkout, Here we use order specific billing info
        // Or guest order invoice. Billing info is linked to the order

        $billing_info_from_order    = unserialize($order->billing_info);
        $billing_defaults               = shortcode_atts($billing_defaults, $user_billing);
        $billing_info               = shortcode_atts($billing_defaults, $billing_info_from_order);
    }
    else{
        // Skiped billing address in checkout, get billing address from saved user info

        $billing_info       = shortcode_atts($billing_defaults, $user_billing);;

        // Due to index mismatch in order email and saved billing email
        $billing_info['order_email'] = isset($billing_info['email'])?$billing_info['email']:'';
    }

    if($billing_info['first_name']      == '' ||
        $billing_info['last_name']      == '' ||
        $billing_info['address_1'].$billing_info['address_2']      == '' ||
        $billing_info['postcode']       == '' ||
        $billing_info['state'].$billing_info['city']          == ''
    ){
        $updatebilling = wpdm_user_dashboard_url(array('udb_page' => 'edit-profile'));
        WPDM_Messages::warning("Critical billing info is missing. Please update your billing info to generate invoice properly.<br style='margin-bottom: 10px;display: block'/><a class='btn btn-warning' target='_top' onclick=\"window.opener.location.href='$updatebilling';window.close();return false;\" href='#'>Update Billing Info</a>", 1);
    }


    $coup               = __("Coupon Discount","wpdm-premium-packages");
    $role_dis           = __("Role Discount","wpdm-premium-packages");
    $item_name_label    = __('Item Name', 'wpdm-premium-packages');
    $quantity_label     = __('Quantity', 'wpdm-premium-packages');
    $unit_price_label   = __('Unit Price', 'wpdm-premium-packages');
    $net_subtotal_label = __('Subtotal', 'wpdm-premium-packages');
    $discount_label     = __('Discount', 'wpdm-premium-packages');
    $nettotal_label     = __('Total', 'wpdm-premium-packages');
    $total_label        = __('Total', 'wpdm-premium-packages');
    $vat_label          = __('Tax', 'wpdm-premium-packages');

    $ordertotal         = number_format($order->total, 2);
    $unit_prices        = unserialize($order->unit_prices);
    $cart_discount      = number_format($order->discount, 2);
    $tax                = number_format($order->tax, 2);

    $item_table         = <<<OTH
<table class="table table-striped table-bordered" id="invoice-amount" width="100%" cellspacing="0">
<thead>
<tr id="header_row">
    <th>{$item_name_label}</th>
    <th>{$quantity_label}</th>
    <th class='item_r' style="text-align: right;">{$unit_price_label}</th>
    <th class='item_r' style="text-align: right;">{$coup}</th>
    <th class='item_r' style="text-align: right;">{$role_dis}</th>
    <th class='item_r' style="text-align: right;">{$net_subtotal_label}</th>
</tr>
</thead>
<tfoot> 
      <tr id="discount_tr">          
        <td colspan="5" class="item_r" style="text-align:right">{$discount_label}</td> 
        <td class="item_r text-right">{$csign_before}{$cart_discount}{$csign_after}</td>
      </tr> 
      <!--tr id="net_total_tr"> 
       
        <td  colspan="5" class="item_r" style="text-align:right" class="item_r">{$nettotal_label}</td> 
        <td class="item_r text-right">{$csign_before}{$ordertotal}{$csign_after}</td>
      </tr--> 
      <tr id="vat_tr"> 
        
        <td  colspan="5" class="item_r" style="text-align:right" class="item_r">{$vat_label}</td> 
        <td class="item_r text-right">{$csign_before}{$tax}{$csign_after}</td>
      </tr> 
      <tr id="total_tr"> 
         
        <td  colspan="5" class="item_r" style="text-align:right" class="total" id="total_currency">{$total_label}</td> 
        <td class="total text-right">{$csign_before}{$ordertotal}{$csign_after}</td>
      </tr> 
    </tfoot>
    <tbody>
OTH;
    $items = \WPDMPP\Libs\Order::GetOrderItems($order->order_id);
    $total = 0;
    foreach ($items as $item) {

        $ditem = get_post($item['pid']);
        if (! is_object( $ditem ) ) {
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

        foreach ( $_variations as $vr ) {
            $variations .= "{$vr['name']}: +{$csign_before}" . number_format(floatval($vr['price']), 2) . $csign_after;
            $prices     += number_format(floatval($vr['price']), 2);
        }

        $itotal         = number_format(((($item['price'] + $prices) * $item['quantity']) - $discount - $item['coupon_discount']), 2, ".", "");
        $total          += $itotal;
        $order_item     = "";
        $discount       = number_format(floatval($discount), 2);
        $item['price']  = number_format($item['price'], 2);

        $_ohtml .= <<<ITEM
                    <tr class="item">
                        <td>{$ditem->post_title} <br> {$variations}</td>
                        <td>{$item['quantity']}</td>
                        <td class="text-right">{$csign_before}{$item['price']}{$csign_after}</td>
                        <td class="text-right">{$csign_before}{$item['coupon_discount']}{$csign_after}</td>
                        <td class="text-right">{$csign_before}{$discount}{$csign_after}</td>                         
                        <td class='text-right' align='right'>{$csign_before}{$itotal}{$csign_after}</td>
ITEM;


        $order_item = apply_filters("wpdmpp_order_item", "", $item);

        if ( $order_item != '' )
            $_ohtml .= "<tr><td colspan='7'>" . $order_item . "</td></tr>";
    }

    $item_table .= $_ohtml."</tbody></table>";

    $invoice['client_info'] = <<<CINF
    <div class="vcard" id="client-details"> 
        <div class="fn">{$billing_info['first_name']} {$billing_info['last_name']}</div>
        <div class="org"><h3>{$billing_info['company']}</h3></div>
        <div class="adr">
            <div class="street-address">
            {$billing_info['address_1']}
            {$billing_info['address_2']}
            </div>
            <!-- street-address -->
            <div class="locality">{$billing_info['postcode']}, {$billing_info['city']}, {$billing_info['state']}, {$billing_info['country']}</div>
            <div id="client-email"><span class="order-email">Email: {$billing_info['order_email']}</span></div>
        </div>
        <!-- adr -->
        <div id="phone">Phone: {$billing_info['phone']}</div>
        <div id="taxid">Tax ID: {$billing_info['taxid']}</div>
    </div>
CINF;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <?php wp_print_styles('wpdmpp-invoice'); ?>
    <?php \WPDM\libs\Apply::uiColors(true); ?>
</head>
<body class="w3eden" onload="window.print();">
<div class="container-fluid">
    <br/>
    <div class="row frow">
        <div class="col-sm-<?php echo isset($_GET['renew']) ? 4 : 6; ?>">
            <div class="panel panel-default card mb-3"><div class="panel-heading card-header card-header">
                    <?php if($_GET['wpdminvoice'] != 'pdf'){ ?>
                        <button class="btn btn-primary btn-xs pull-right" id="btn-print" type="button" onclick="window.print();"><i class="fa fa-print"></i> <?php _e('Print Invoice','wpdm-premium-packages'); ?></button>
                    <?php } ?>
                    <strong><?php _e('Invoice No','wpdm-premium-packages'); ?></strong>
                </div>
                <div class="panel-body card-body card-body">
                    <h3 class="text-info invoice-no"><?php echo $order->order_id; ?></h3>
                </div>
            </div>
        </div>
        <div class="col-sm-<?php echo isset($_GET['renew']) ? 4 : 6; ?> text-right">
            <div class="panel panel-default card mb-3"><div class="panel-heading card-header card-header">
                    <strong><?php _e('Order Date','wpdm-premium-packages'); ?></strong>
                </div>
                <div class="panel-body card-body card-body">
                    <?php echo date(get_option('date_format'),$order->date); ?>
                </div>
            </div>
        </div>
        <?php if(isset($_GET['renew'])){ ?>
            <div class="col-sm-4 text-right">
                <div class="panel panel-default card mb-3"><div class="panel-heading card-header card-header">
                        <strong><?php _e('Order Renewed On','wpdm-premium-packages'); ?></strong>
                    </div>
                    <div class="panel-body card-body card-body">
                        <?php echo date(get_option('date_format'),(int)$_GET['renew']); ?>
                    </div>
                </div>
            </div>
        <?php } ?>

    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="panel panel-default info-panel card mb-3">
                <div class="panel-heading card-header card-header"><strong><?php _e('From:','wpdm-premium-packages'); ?></strong></div>
                <div class="panel-body card-body">

                    <div class="media">
                        <div class="media-left">
                            <?php if($settings['invoice_logo'] != ""){ ?>
                                <img style="width: auto; height: 50px;" class="media-object" src="<?php echo $settings['invoice_logo']; ?>">
                            <?php } ?>
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading"><?php bloginfo('sitename'); ?></h4>
                            <p><?php echo nl2br($settings['invoice_company_address']); ?></p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="panel card panel-default info-panel mb-3">
                <div class="panel-heading card-header"><strong><?php _e('To:','wpdm-premium-packages'); ?></strong></div>
                <div class="panel-body card-body">
                    <?php echo $invoice['client_info']; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <?php echo $item_table; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="panel card panel-default"><div class="panel-heading card-header">
                    <strong><?php _e('Payment Method','wpdm-premium-packages'); ?></strong>
                </div>
                <div class="panel-body card-body">
                    <?php echo $order->payment_method; ?>
                </div>
            </div>
        </div>
        <div class="col-sm-6 text-right">
            <div class="panel card panel-default">
                <div class="panel-heading card-header">
                    <strong><?php _e('Payment Status','wpdm-premium-packages'); ?></strong>
                </div>
                <div class="panel-body card-body">
                    <?php echo $order->payment_status; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
