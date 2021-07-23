<?php
/**
 * Widget:  WPDM - Premium Packages Mini Cart
 * Description: Floating Mini Cart for Premium Package
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class WPDMPP_MiniCart extends WP_Widget {

    function __construct() {
        parent::__construct('WPDMPP_MiniCart','Cart', array( 'description' => 'Premium Packages Mini Cart Widget' ) );
        add_action("wp", array($this, 'reload'));
    }

    function reload(){
        if(wpdm_query_var('wpdmupdatecart','int') != 1) return;
        $shopping_cart = $this->load_ajax_cart();
        echo json_encode($shopping_cart);
        die();
    }

    function widget( $args, $instance ) {
        if(is_array($args))
        extract( $args );
        $title =   isset($instance['title']) ? $instance['title'] : '';
        $panel = isset($instance['panel']) ? intval($instance['panel']) : 0;

        if($panel == 0) {
            if(!empty($before_widget)) echo $before_widget;
            if (!empty($title)) {
                echo ( !empty($before_title) ? $before_title : '') . $title . ( !empty($after_title) ? $after_title : '' );
            }
        }
        $shopping_cart = $this->load_ajax_cart();

        if( $panel == 1 ) { ?>

        <div id="wpdm-cart-panel" class="off">
            <div id="wpdm-cart-panel-trigger" class="text-primary">
                <div class="wpdm-cart-info">
                    <h3 id="wpdm-cit"><?php echo $shopping_cart['total']; ?></h3>
                    <span id="wpdm-cic"><?php echo $shopping_cart['items']; ?></span> items
                </div>
            </div>
            <?php } ?>

            <div id="mini_cart_details" class="wpdm-mini-cart w3eden"><?php echo $shopping_cart['content']; ?></div>

            <script>

                function  wpdmpp_remove_cart_item_btn(id){
                    if(!confirm('Are you sure to remove this item from your cart?')) return false;
                    jQuery('#remove_from_cart_btn_'+id).html(wpdm_asset.spinner);
                    jQuery.post('?wpdmpp_remove_cart_item='+id,function(res){
                        var obj = jQuery.parseJSON(res);
                        jQuery('#mini_cart_item_'+id).fadeOut().remove();
                        jQuery('#wpdmpp_mini_cart_subtotal').html(obj.cart_subtotal);
                    });
                    return false;
                }

                function  wpdmpp_remove_minicart_item(id){
                    if(!confirm('Are you sure?')) return false;
                    jQuery('#mini_cart_item_'+id+' *').css('color','#ccc');
                    jQuery.post('?wpdmpp_remove_cart_item='+id,function(res){
                        var obj = jQuery.parseJSON(res);
                        jQuery('#mini_cart_item_'+id).fadeOut().remove();
                        jQuery('#wpdmpp_mini_cart_subtotal').html(obj.cart_subtotal);
                    });
                    return false;
                }

                function wpdmpp_update_minicart(event) {
                    if (event.origin !== window.location.protocol+'//'+window.location.hostname)
                        return;

                    if(event.data=='cart_updated'){
                        jQuery.get('?wpdmupdatecart=1', function(res){
                            var data = jQuery.parseJSON(res);
                            console.log(data);
                            jQuery('.wpdm-mini-cart').html(data.content);
                            jQuery('#wpdm-cit').html(data.total);
                            jQuery('#wpdm-cic').html(data.items);
                        });
                    }
                }
                window.addEventListener('message', wpdmpp_update_minicart, false);
            </script>

            <?php  if($panel == 1) { ?>
        </div>
        <?php wp_enqueue_style('wpdmpp-widget-slide-cart', WPDMPP_BASE_URL.'assets/css/widget-slide-cart.css' ); ?>
        <?php }
        if( $panel == 0 && !empty($after_widget) ) {
            echo $after_widget;
        }
    }

    function update( $new_instance, $old_instance ) {
        $instance = $new_instance;

        return $instance;
    }

    function form( $instance ) {

        if ( $instance ) {
            extract($instance);
        }
        else {
        }
        $title = isset($title) ? $title : '';
        $panel = isset($panel) ? $panel : 0;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','wpdm-premium-packages'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo isset($title)?$title:""; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('panel'); ?>">
                <input class="widefat" id="<?php echo $this->get_field_id('panel'); ?>" name="<?php echo $this->get_field_name('panel'); ?>" type="checkbox" value="1" <?php echo checked($panel, 1); ?> /> <?php _e('Show as Panel','wpdm-premium-packages'); ?>
            </label>
        </p>
        <?php
    }


    /**
     * Updates MiniCart On adding new cart item
     * @return mixed
     */

    function load_ajax_cart(){
        global $wpdb;
        $cart_data = wpdmpp_get_cart_data();
        $quantity = $discount = $variation = $price = null;
        $cart_items = array();
        foreach($cart_data as $pid => $cdt){
            extract($cdt);
            if($pid){
                $cart_items[$pid] = get_post($pid,ARRAY_A);
                $cart_items[$pid]['quantity'] =  $quantity;
                $cart_items[$pid]['discount'] =  $discount;
                $cart_items[$pid]['variation'] =  $variation;
                $cart_items[$pid]['price'] = (double)$price;

                if(isset($cdt['coupon'])){
                    $valid_coupon = wpdmpp_check_coupon($pid,$coupon);
                    if($valid_coupon!=0){
                        $cart_items[$pid]['coupon'] =  $coupon;
                        $cart_items[$pid]['coupon_discount'] =  $valid_coupon;
                    }
                    else{
                        $cart_items[$pid]['error'] =  __('Coupon does not exist','wpdm-premium-packages');
                    }
                }
            }
        }
        $settings       = get_option('_wpdmpp_settings');
        $currency_sign  = wpdmpp_currency_sign();
        $total_quantity = 0;
        $cart           = "";

        if(is_array($cart_items) && count($cart_items) > 0) {
            foreach ($cart_items as $item) {
                $prices = 0;
                $variations = "";
                if (isset($item['coupon_discount'])) {
                    $discount_amount = (($item['coupon_discount'] / 100) * ($item['price'] + $prices) * $item['quantity']);
                } else {
                    $discount_amount = "";
                    $discount_style = "";
                    $discount_title = "";
                }
                if (isset($item['error'])) {
                    $coupon_style = "style='border:1px solid #ff0000;'";
                    $title = $item['error'];
                } else {
                    $coupon_style = "";
                    $title = "";
                }
                //filter for adding various message after cart item
                $cart_item_info = "";
                $cart_item_info = apply_filters("wpdmpp_cart_item_info", $cart_item_info, $item['ID']);
                $imgurl = "";
                $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($item['ID']), array(600, 300));
                $imgurl = $thumb['0'];
                $cart .= "<div class='media' id='mini_cart_item_{$item['ID']}'><a title='" . __('Delete cart item', 'wpdm-premium-packages') . "' class='wpdmpp_cart_delete_item pull-right btn btn-xs btn-danger' href='#' onclick='return wpdmpp_remove_minicart_item($item[ID])'><i class='fas fa-trash-alt'></i></a><div class='pull-left'><a href='" . get_permalink($item['ID']) . "'><img class='img-circle' src='" . wpdm_dynamic_thumb($imgurl, array(40, 40)) . "'></a></div><div class='media-body'><div class='mcwpname'>$item[post_title]</div><strong>" . $currency_sign . number_format($item['price'] + $prices) . " &times; " . $item['quantity'] . " </strong></div></div>";
                $total_quantity += $item['quantity'];
            }
            $cart .= "
        <div class='media'>
            <button class='btn btn-primary btn-sm btn-block' type='button' onclick='location.href=\"" . get_permalink($settings['page_id']) . "\"'>
            <span id='wpdmpp_mini_cart_subtotal'>" . $currency_sign . wpdmpp_get_cart_total_after_discount() . "</span> &nbsp; | &nbsp; " . __("Checkout", "wpdm-premium-packages") . "</button>
        </div>";

            $cart_['content']   = $cart;
            $cart_['items']     = $total_quantity;
            $cart_['total']     = $currency_sign . wpdmpp_get_cart_total_after_discount();
        } else {
            $cart_['content'] = "<div class='panel panel-default'><div class='panel-body text-danger'>". __('Cart is Empty !','wpdm-premium-packages')."</div><div class='panel-footer'><a href='".wpdmpp_continue_shopping_url()."' class='btn btn-sm btn-primary'>".__('Continue Shopping','wpdm-premium-packages')."</a></div></div>";
            $cart_['items'] = 0;
            $cart_['total'] = wpdmpp_currency_sign()."0.00";
        }
        return $cart_;
    }

}

//add_action( 'widgets_init',function(){ register_widget("WPDMPP_MiniCart"); } );
