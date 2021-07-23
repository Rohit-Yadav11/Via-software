<?php
/**
 * Plugin Name:  Premium Packages - Sell Digital Products Securely
 * Plugin URI: https://www.wpdownloadmanager.com/download/premium-package-complete-digital-store-solution/
 * Description: Complete solution for selling digital products securely and easily
 * Version: 4.7.2
 * Author: WordPress Download Manager
 * Text Domain: wpdm-premium-packages
 * Author URI: https://www.wpdownloadmanager.com/
 */
namespace WPDMPP;

use WPDM\Email;
use WPDM\libs\Crypt;
use WPDM\libs\FileSystem;
use WPDM\Package;
use WPDM\Session;
use WPDMPP\Libs\BillingInfo;
use WPDMPP\Libs\Cart;
use WPDMPP\Libs\Order;
use WPDMPP\Libs\User;

if (!defined('ABSPATH')) {
	exit;
}

global $wpdmpp, $wpdmpp_settings;

if (!class_exists('WPDMPremiumPackage')):
	/**
	 * @class WPDMPremiumPackage
	 */

	define('WPDMPP_Version', '4.7.1');
	define('WPDMPP_BASE_DIR', dirname(__FILE__).'/');
	define('WPDMPP_BASE_URL', plugins_url('wpdm-premium-packages/'));

	if(!defined('WPDMPP_MENU_ACCESS_CAP'))
	    define('WPDMPP_MENU_ACCESS_CAP', 'manage_categories');
	if(!defined('WPDMPP_ADMIN_CAP'))
	    define('WPDMPP_ADMIN_CAP', 'manage_categories');

    if(!defined('WPDMPP_TPL_FALLBACK'))
        define('WPDMPP_TPL_FALLBACK', dirname(__FILE__) . '/templates/');

    if(!defined('WPDMPP_TPL_DIR')) {
        if((int)get_option('__wpdm_bsversion', '') === 3 || is_admin())
            define('WPDMPP_TPL_DIR', dirname(__FILE__) . '/templates3/');
        else
            define('WPDMPP_TPL_DIR', dirname(__FILE__) . '/templates/');
    }

	class WPDMPremiumPackage
	{

	    public $cart;

		function __construct()
		{
			global $wpdmpp_settings, $payment_methods;
			$wpdmpp_settings = maybe_unserialize(get_option('_wpdmpp_settings'));
			$payment_methods = array( 'TestPay', 'Paypal', 'Cash', 'Cheque' );

			$this->init();
			$this->init_hooks();
		}

		private function init()
		{
			global $sap;

			if ( function_exists( 'get_option' ) ) {
			    $sap = ( get_option( 'permalink_structure' ) != '') ? '?' : '&';
			}

			$this->include_files();

		}

		private function init_hooks()
		{

		    register_activation_hook( __FILE__, array( '\WPDMPP\Libs\Installer', 'init' ) );

            add_action( 'wp', array( $this, 'download' ), 1);

            add_action( 'wp_login', [ new Cart(), 'onUserLogin' ], 10, 2);

		    add_action( 'wpdm-package-form-left', array( $this, 'wpdmpp_meta_box_pricing' ) );
			add_filter( 'wpdm_package_settings_tabs', array( $this, 'wpdmpp_meta_boxes' ) );
            add_filter( 'add_wpdm_settings_tab', array( $this, 'settings_tab' ) );
            add_filter( 'wpdm_privacy_settings_panel', array( $this, 'privacy_settings' ) );

            add_action( 'wpdm_template_editor_menu', array( $this, 'template_editor_menu' ));
			//add_action( 'wpdm_template_tag_row', array( $this, 'template_tag_row' ));

			add_action( 'init', function(){
			    $this->dbTables();
			    $this->wpdmpp_languages();
			    $this->invoice();
			    $this->wpdmpp_process_guest_order();
			    $this->paynow();
			    $this->payment_notification();
			    $this->comeplete_buynow_action();
			    $this->wpdmpp_ajax_payfront();
			    $this->anync_execute();
			    $this->wpdmpp_update_profile();
			    $this->freeDownload();

            });

			/*
			add_action( 'init', array( $this, 'wpdmpp_languages' ) );
			add_action( 'init', array( $this, 'invoice' ) );
			add_action( 'init', array( $this, 'wpdmpp_process_guest_order' ) );
			//add_action( 'init', array( $this, 'wpdmpp_download' ), 1);
			add_action( 'init', array( $this, 'paynow' ) );
			add_action( 'init', array( $this, 'payment_notification' ) );
			add_action( 'init', array( $this, 'wpdmpp_ajax_payfront' ) );
			add_action( 'init', array( $this, 'anync_execute' ) );
			add_action( 'init', array( $this, 'wpdmpp_update_profile' ) );
			add_action( 'init', array( $this, 'freeDownload' ) );
			*/

			add_action( 'wpdm_login_form', array( $this, 'wpdmpp_invoice_field' ) );
            add_action( 'wpdm_register_form', array( $this, 'wpdmpp_invoice_field' ) );
            add_action( 'wp_login', array( $this, 'wpdmpp_associate_invoice' ), 10, 2 );
            add_action( 'user_register', array( $this, 'wpdmpp_associate_invoice_signup' ), 10, 1 );

            add_action( 'wp_ajax_resolveorder', array( $this, 'wpdmpp_resolveorder' ) );

            add_action( 'wp_ajax_set_payment_method_for_order', array( $this, 'set_payment_method' ) );
            add_action( 'wp_ajax_nopriv_set_payment_method_for_order', array( $this, 'set_payment_method' ) );

            add_action( 'wp_ajax_nopriv_gettax', array( $this, 'calculate_tax' ) );
            add_action( 'wp_ajax_gettax', array( $this, 'calculate_tax' ) );

            add_action( 'wp_ajax_wpdmpp_cancel_subscription', array( $this, 'cancel_subscription' ) );

            add_action( 'wp_ajax_product_sales_overview', array( $this, 'wpdmpp_meta_box_sales_overview' ) );

			add_action( 'wp_ajax_nopriv_payment_options', array( $this, 'payment_options' ) );
            add_action( 'wp_ajax_payment_options', array( $this, 'payment_options' ) );

            add_action( 'wp_ajax_wpdmpp_update_withdraw_status', array( $this, 'wpdmpp_update_withdraw_status' ) );

            add_action( 'wp_ajax_wpdmpp_expire_orders', array( $this, 'expire_orders' ) );

            add_action( 'wp_enqueue_scripts', array( $this, 'wpdmpp_enqueue_scripts' ) );

            add_action( 'admin_enqueue_scripts', array( $this, 'wpdmpp_admin_enqueue_scripts' ) );

			if ( is_admin() ) {
				add_action( 'wp_ajax_wpdmpp_save_settings', array( $this, 'wpdmpp_save_settings' ) );
				add_action( 'wp_ajax_wpdmpp_toggle_auto_renew', array( $this, 'toggle_auto_renew' ) );
				add_action( 'wp_ajax_wpdmpp_async_request', array( $this, 'wpdmpp_async_request' ) );
				add_action( 'wp_loaded', array( $this, 'wpdmpp_hide_notices' ) );
			}

			if( ! is_admin() ) {
                add_action( 'wpdm_login_form', array( $this, 'wpdmpp_guest_download_link' ) );
            }

			add_filter( 'wpdm_meta_box', array( $this, 'add_meta_boxes' ) );
			add_filter( 'wpdm_user_dashboard_menu', array( $this, 'wpdmpp_user_dashboard_menu' ) );
			add_filter( 'wpdm_frontend', array( $this, 'wpdmpp_frontend_tabs' ) );
			add_filter( 'wpdm_after_prepare_package_data', array( $this, 'fetch_template_tag' ) );
            add_filter( 'wdm_before_fetch_template', array( $this, 'fetch_template_tag' ) );
            add_filter( 'wpdm_download_link', array( $this, 'download_link' ), 10, 2 );
            add_filter( 'wpdm_check_lock', array( $this, 'wpdmpp_lock_download' ), 10, 2 );
            add_filter( 'wpdm_single_file_download_link', array( $this, 'hide_single_file_download_link' ), 10, 3 );

            //add_action( 'activated_plugin', array( $this, 'pp_save_error' ) );

            add_action( 'init', array( $this, 'connect_wizard' ) );

		}

		function dbTables(){
		    global $wpdb;
		    $wpdb->wpdmpp_orders = "{$wpdb->prefix}ahm_orders";
		    $wpdb->wpdmpp_order_items = "{$wpdb->prefix}ahm_order_items";
        }


		function connect_wizard() {
            // Setup Wizard
            if ( ! empty( $_GET['page'] ) ) {
                switch ( $_GET['page'] ) {
                    case 'wpdmpp-setup' :
                        include_once( dirname( __FILE__ ) . '/includes/settings/wizard/class.SetupWizard.php' );
                    break;
                }
            }
        }

        function wpdmpp_run_setup_wizard_notice() {

		    if( get_option('wpdmpp_setp_wizard_notice') == 'hide' ) return;
            ?>
            <div class="notice notice-info is-dismissible w3eden">
                <p class="wpdmpp-notice"><?php _e( 'Thank you for installing Premium Package! You are almost ready to start selling.', 'wpdm-premium-packages' ); ?>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpdmpp-setup' ) ); ?>" class="btn btn-sm  btn-info"><?php _e( 'Run the Setup Wizard', 'wpdm-premium-packages' ); ?></a>
                <a class="btn btn-sm btn-warning" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wpdmpp-hide-notice', 'wizard' ), 'wpdmpp_hide_notices_nonce', '_wpdmpp_notice_nonce' ) ); ?>"><?php _e( 'Skip setup', 'wpdm-premium-packages' ); ?></a></p>
            </div>
            <?php
        }

        function wpdmpp_hide_notices() {
            if ( isset( $_GET['wpdmpp-hide-notice'] ) && isset( $_GET['_wpdmpp_notice_nonce'] ) ) {
                if ( ! wp_verify_nonce( $_GET['_wpdmpp_notice_nonce'], 'wpdmpp_hide_notices_nonce' ) ) {
                    wp_die( __( 'Action failed. Please refresh the page and retry.', 'wpdm-premium-packages' ) );
                }

                if ( ! current_user_can( 'manage_options' ) ) {
                    wp_die( __( 'Cheatin&#8217; huh?', 'wpdm-premium-packages' ) );
                }

                $hide_notice = sanitize_text_field( $_GET['wpdmpp-hide-notice'] );
                if($hide_notice == 'wizard')
                    update_option('wpdmpp_setp_wizard_notice', 'hide');
            }
        }

		function pp_save_error() {
            file_put_contents( ABSPATH.'pp-errors.txt' , ob_get_contents() );
        }

		function wpdmpp_languages()
		{
			load_plugin_textdomain('wpdm-premium-packages', false, dirname(plugin_basename(__FILE__)) . '/languages/');
		}

		function include_files()
		{
            include(dirname(__FILE__) . "/includes/libs/functions.php");
            include(dirname(__FILE__) . "/includes/libs/class.Installer.php");
			include(dirname(__FILE__) . "/includes/libs/class.User.php");
			include(dirname(__FILE__) . "/includes/libs/class.LicenseManager.php");

			include(dirname(__FILE__) . "/includes/libs/class.Cart.php");
            $this->cart = new Cart();

			include(dirname(__FILE__) . "/includes/libs/class.Order.php");
			include(dirname(__FILE__) . "/includes/libs/class.Payment.php");
			include(dirname(__FILE__) . "/includes/libs/class.CustomActions.php");
			include(dirname(__FILE__) . "/includes/libs/class.CustomColumns.php");
			include(dirname(__FILE__) . "/includes/libs/class.Currencies.php");
			include(dirname(__FILE__) . "/includes/libs/class.BillingInfo.php");
			include(dirname(__FILE__) . "/includes/libs/class.DashboardWidgets.php");
			include(dirname(__FILE__) . "/includes/libs/class.CouponCodes.php");
			include(dirname(__FILE__) . "/includes/libs/class.ShortCodes.php");
			include(dirname(__FILE__) . "/includes/libs/class.CronJobs.php");
			include(dirname(__FILE__) . "/includes/libs/cart.php");
			include(dirname(__FILE__) . "/includes/libs/hooks.php");

			include(dirname(__FILE__) . "/includes/menus/class.AdminMenus.php");

			// Cart Widget
			include(dirname(__FILE__) . "/includes/widgets/widget-cart.php");

			// Integrated payment mothods
			include_once(dirname(__FILE__) . "/includes/libs/payment-methods/Cash/class.Cash.php");
			include_once(dirname(__FILE__) . "/includes/libs/payment-methods/Cheque/class.Cheque.php");
			include_once(dirname(__FILE__) . "/includes/libs/payment-methods/Paypal/class.Paypal.php");
			include_once(dirname(__FILE__) . "/includes/libs/payment-methods/TestPay/class.TestPay.php");

			include_once(dirname(__FILE__) . "/includes/controllers/class.Order.php");

		}

		function calculate_tax(){
		    $cartsubtotal = wpdmpp_get_cart_subtotal();
		    $cart_id = wpdmpp_cart_id();
		    $coupon = get_option($cart_id."_coupon", array());
		    $cartdiscount = isset($coupon['discount'])?$coupon['discount']:0;
		    $cartsubtotal -= $cartdiscount;
		    $tax_total = wpdmpp_calculate_tax2();
		    $total_including_tax = $cartsubtotal + $tax_total;
		    $currency_sign = wpdmpp_currency_sign();

		    $currency_sign_before = wpdmpp_currency_sign_position() == 'before' ? $currency_sign : '';
            $currency_sign_after = wpdmpp_currency_sign_position() == 'after' ? $currency_sign : '';

            $tax_str = $currency_sign_before . number_format((double)str_replace(',','',$tax_total),2) . $currency_sign_after;
            $total_str = $currency_sign_before . number_format((double)str_replace(',','',$total_including_tax),2) . $currency_sign_after;

            $updates = array( 'tax' => $tax_str, 'total' => $total_str , 'subtotal' => $cartsubtotal, 'dis' => $cartdiscount);


            Session::set('tax',  $tax_total);
            Session::set('subtotal',  $cartsubtotal);

            die( json_encode($updates) );
		}


		/**
		 * Metabox content for Pricing and other Premium Pckage Settings
		 */

		function wpdmpp_meta_box_sales_overview_loader(){
			 ?>
			<div id="wpdmpp-sales-overview"><div style="padding: 50px 10px;text-align: center"><i class="fas fa-sync fa-spin"></i> <?php _e('Loading....','wpdm-premium-packages'); ?></div></div>
			<script>
				jQuery(function ($) {
					$('#wpdmpp-sales-overview').load(ajaxurl, {action: 'product_sales_overview', post: <?php echo wpdm_query_var('post'); ?>});
				});
			</script>
			<?php
		}

		function wpdmpp_meta_box_sales_overview()
		{
			global $post;
			$data = Session::get( 'sales_overview_html_'.wpdm_query_var('post') );
            if($data){
                echo $data;
                die();
            }
            ob_start();
			include __DIR__.'/includes/menus/templates/product-sales-overview.php';
            $data = ob_get_clean();
            Session::set( 'sales_overview_html_'.wpdm_query_var('post') , $data );
            echo $data;
			die();
		}


		function payment_options()
		{
			global $post;
			include \WPDM\Template::locate('checkout-cart/checkout.php', dirname(__FILE__) . '/templates'.WPDM()->bsversion."/", WPDM_TPL_FALLBACK);
			die();
		}

		function add_meta_boxes($metaboxes){
			$pid = wpdm_query_var('post');
			$price = wpdmpp_effective_price($pid);
			if($price > 0){
				$wpdmpp_metaboxes['sales-overview'] =  array('title' => __('Sales Overview', "wpdm-premium-packages"), 'callback' => array($this, 'wpdmpp_meta_box_sales_overview_loader'), 'position' => 'side', 'priority' => 'core');
				$metaboxes = $wpdmpp_metaboxes + $metaboxes;
			}
			return $metaboxes;
		}

		/**
		 * Metabox content for Pricing and other Premium Pckage Settings
		 */
		function wpdmpp_meta_box_pricing()
		{
			global $post;
			include \WPDM\Template::locate('metaboxes/wpdm-pp-settings.php', WPDMPP_TPL_DIR);
		}

		/**
		 * @param $tabs
		 * @return mixed
		 * @usage Adding Premium Package Settings Metabox by applying WPDM's 'wpdm_package_settings_tabs' filter
		 */
		function wpdmpp_meta_boxes($tabs)
		{
			if(is_admin())
				$tabs['pricing'] = array('name' => __('Pricing & Discounts', "wpdm-premium-packages"), 'callback' => array( $this, 'wpdmpp_meta_box_pricing' ) );

			return $tabs;
		}



		/**
		 *  Premium Package Settings Page
		 */
		function settings()
		{
			include("includes/settings/settings.php");
		}

		function settings_tab($tabs){
			$tabs['ppsettings'] = wpdm_create_settings_tab('ppsettings', 'Premium Package', array( $this, 'settings' ), $icon = 'fa fa-shopping-cart');
			return $tabs;
		}

		/**
		 * Generate Order Invoice op request
		 */
		function invoice()
		{
			if (isset($_GET['id']) && $_GET['id'] != '' && isset($_GET['wpdminvoice'])) {
                ob_start();
			    wp_register_style('wpdm-front-bootstrap', WPDM_BASE_URL.'assets/bootstrap3/css/bootstrap.css' );
                wp_register_style('font-awesome', WPDM_BASE_URL . 'assets/font-awesome/css/font-awesome.min.css');
                wp_register_style('wpdm-front', WPDM_BASE_URL . 'assets/css/front.css');
                wp_register_style('wpdmpp-invoice', WPDMPP_BASE_URL . 'assets/css/invoice.css', array( 'wpdm-front-bootstrap', 'font-awesome', 'wpdm-front' ) );
				include \WPDM\Template::locate("wpdm-pp-invoice.php", WPDMPP_TPL_DIR);
				$data = ob_get_clean();

				$oid = sanitize_file_name($_GET['id']);

                if($_GET['wpdminvoice'] == 'pdf' && method_exists(new FileSystem(), 'html2pdf'))
                    FileSystem::html2pdf($data, "invoice-{$oid}.pdf");
                else
                    echo $data;
                die();
			}
		}


		function wpdmpp_user_dashboard_menu($menu){
			$menu = array_merge(array_splice($menu, 0, 1), array('purchases' => array('name' => __('Purchases','wpdm-premium-packages'), 'callback' => array( $this, 'wpdmpp_purchased_items' ) ) ), $menu);
			return $menu;
		}

		function wpdmpp_purchased_items($params = array()){
			global $wpdb, $current_user;
			$uid = $current_user->ID;

			//$purchased_items = $wpdb->get_results("select oi.*,o.currency, o.date as odate, o.order_status from {$wpdb->prefix}ahm_order_items oi,{$wpdb->prefix}ahm_orders o where o.order_id = oi.oid and o.uid = {$uid} and o.order_status IN ('Expired', 'Completed') order by `date` desc");

            wpdmpp_expiry_check();

			ob_start();
			if(isset($params[2]) && $params[1] == 'order') {
			    Controllers\Order::userOrderDetails($params[2]);
            }
			else {
                include_once wpdm_tpl_path('partials/resolve-order.php', WPDMPP_TPL_DIR );
                include_once wpdm_tpl_path('partials/user-orders-list.php', WPDMPP_TPL_DIR);
            }
			//else
			//    include wpdm_tpl_path('user-dashboard/purchased-items.php', WPDMPP_TPL_DIR);

			return ob_get_clean();
		}



		/**
		 * Process Guest Orders
		 */
		function wpdmpp_process_guest_order(){

		    if(wpdm_query_var('exitgo', 'int')){
		        Session::clear('guest_order');
		        wp_redirect($_SERVER['HTTP_REFERER']);
		        die('ok');
            }

			if( isset( $_POST['__wpdmpp_go'] ) ) {

			    check_ajax_referer(NONCE_KEY, '__wpdmpp_go_nonce');

				//if( ! Session::get('guest_order_init') ) { Session::set('guest_order_init', uniqid(), 18000); die('nosess'); }

				$orderid = sanitize_text_field($_POST['__wpdmpp_go']['order']);
				$orderemail = sanitize_email($_POST['__wpdmpp_go']['email']);

				$o = new \WPDMPP\Libs\Order();
				$order = $o->GetOrder($orderid);

				// No match for order id
                if( ! is_object($order) || ! isset($order->order_id) || $order->order_id != $orderid) die('noordr');

                // Found a match for order id
                $billing_info = unserialize($order->billing_info);
                $billing_email = isset($billing_info['order_email']) ? $billing_info['order_email'] : '';

				if(is_email($orderemail) && $orderemail == $billing_email && $order->uid <= 0){
					Session::set('guest_order', $orderid, 18000);
					Session::set('order_email', $billing_email, 18000);
					die('success');
				}

				// Order assigned to registered user, so no guest access, please login to access order
				if($order->uid > 0) die('nogues');

				die('noordr');
			}

		}

		function wpdmpp_frontend_tabs($tabs){
		    //$first['adb'] = array('label'=>'Dashboard','shortcode' => '[wpdmpp_seller_dashboard]');
			$tabs['sales'] = array('label'=>'Sales','shortcode' => '[wpdmpp_earnings]');

			return array('seller-dashboard' => array('label'=>'Dashboard','shortcode' => '[wpdmpp_seller_dashboard]'))+$tabs;
		}

		/**
		 * Save admin settings options
		 */
		function wpdmpp_save_settings()
		{
		    if(wp_verify_nonce(wpdm_query_var('__wpdms_nonce'), NONCE_KEY) && current_user_can(WPDMPP_ADMIN_CAP)) {
                $settings = $_POST['_wpdmpp_settings'];
                $settings = wpdm_sanitize_array($settings);
                $settings = apply_filters("wpdmpp_before_save_settings", $settings);
                update_option('_wpdmpp_settings', $settings);
                do_action("wpdmpp_after_save_settings");
                die(__('Settings Saved Successfully', "wpdm-premium-packages"));
            }
		}


		static function authorize_masterkey(){
            if(\WPDM\Package::validateMasterKey(wpdm_query_var('wpdmdl'), wpdm_query_var('masterkey')) && (int)get_wpdmpp_option('authorize_masterkey') === 1) return true;
            return false;
        }

		function download()
		{

		    if(wpdm_query_var('wpdmppd') !== '' || wpdm_query_var('wpdmppdl') !== '') {

		        $wpdmdd = wpdm_query_var('wpdmppd') !== '' ? Crypt::decrypt(wpdm_query_var('wpdmppd'), true) : wpdmppdl_decode(wpdm_query_var('wpdmppdl'));

		        if(!is_array($wpdmdd) || !isset($wpdmdd['ID'], $wpdmdd['oid'])) \WPDM_Messages::error(__( "&mdash; Invalid download link &mdash;", "wpdm-premium-packages" ), 1);

		        $package = get_post($wpdmdd['ID'], ARRAY_A);

                $PID = (int)$wpdmdd['ID']; // Product ID
                $OID = sanitize_text_field($wpdmdd['oid']); // Order ID
                $domain = isset($wpdmdd['domain']) ? sanitize_text_field($wpdmdd['domain']) : '';


                /*
                if (wpdm_query_var('preact') === 'login') {
                    $user = wp_signon(array('user_login' => wpdm_query_var('user'), 'user_password' => wpdm_query_var('pass')));
                    if (!$user->ID)
                        \WPDM_Messages::error(__( "Login failed!", "wpdm-premium-packages" ), 1);
                    else {
                        wp_set_current_user($user->ID);
                        Session::set('guest_order', $OID, 18000);
                    }
                }

                if (wpdm_query_var('wpdm_access_token') != '') {
                    $at = wpdm_query_var('wpdm_access_token');
                    if (!$at) die(json_encode(array('error' => 'Invalid Access Token!')));
                    $atx = explode("x", $at);
                    $uid = end($atx);
                    $uid = (int)$uid;
                    if (!$uid) die(json_encode(array('error' => 'Invalid Access Token!')));
                    $sat = get_user_meta($uid, '__wpdm_access_token', true);
                    if ($sat === '') die(json_encode(array('error' => 'Invalid Access Token!')));
                    if ($sat === $at)
                        wp_set_current_user($uid);
                    else
                        die(json_encode(array('error' => 'Invalid Access Token!')));
                }*/


                global $wpdb, $current_user;
                $settings = get_option('_wpdmpp_settings');

                $order = new Order();
                $odata = $order->getOrder($OID);
                $items = unserialize($odata->items);
                if($domain !== '' && $domain === wpdm_query_var('domain')){

                    if(!user_can($odata->uid, 'manage_options')) {
                        $current_user = get_user_by('id', $odata->uid);
                        wp_set_current_user($odata->uid);
                        wp_set_auth_cookie($odata->uid);
                    }
                    if(!is_user_logged_in()) $odata->uid = 0;
                    $settings['guest_download'] = 1;
                    Session::set('guest_order', $OID, 18000);

                }


                $expire_date = $odata->expire_date > 0 ? $odata->expire_date : ($odata->date + (get_wpdmpp_option('order_validity_period', 365) * 86400));

                if ($odata->uid != $current_user->ID && !Session::get('guest_order')) \WPDM_Messages::error(__("Invalid Access!", "wpdm-premium-packages"), 1);
                if ($odata->order_status == 'Expired' || time() > $expire_date) \WPDM_Messages::error(__("Sorry! Support and Update Access Period is Already Expired", "wpdm-premium-packages"), 1);

                $base_price = get_post_meta($PID, '__wpdm_base_price', true);


                $package['files'] = maybe_unserialize(get_post_meta($PID, '__wpdm_files', true));


                $cart = maybe_unserialize($odata->cart_data);

                $cfiles = array();

                if (isset($cart[$PID]['files']) && is_array($cart[$PID]['files']) && count($cart[$PID]['files']) > 0) {
                    $files = $cart[$PID]['files'];
                    $files = array_keys($files);
                    foreach ($files as $fID) {
                        $cfiles[$fID] = $package['files'][$fID];
                    }
                }

                if(count($cfiles) === 0) {
                    $all_licenses = wpdmpp_get_licenses();
                    $starter = array_keys($all_licenses)[0];
                    $_license = wpdm_valueof($cart, "{$PID}/license/id");
                    if(!$_license) $_license = $starter;
                    $license_pack = get_post_meta($PID, "__wpdm_license_pack", true);
                    $license_pack = wpdm_valueof($license_pack, $_license);
                    if(is_array($license_pack)) {
                        foreach ($license_pack as $fID) {
                            $cfiles[$fID] = $package['files'][$fID];
                        }
                    }
                }

                $package['individual_file_download'] = 1; //maybe_unserialize(get_post_meta($package['ID'], '__wpdm_individual_file_download', true));

                if ($base_price == 0 && $PID > 0) {
                    //for free items
                    $package['access'] = array( 'guest' );
                    include(WPDM_BASE_DIR . "/wpdm-start-download.php");
                }



                //Member's Download
                if (@in_array($PID, $items) && $OID != '' && is_user_logged_in() && $current_user->ID == $odata->uid && $odata->order_status == 'Completed') {
                    //for premium item

                    $order = new Order();
                    $order->Update(array('download'=>1), $OID);

                    if (count($cfiles) > 0 && !isset($cfiles[wpdm_query_var('ind')])) {
                        if (count($cfiles) > 1) {
                            $zipped = \WPDM\libs\FileSystem::zipFiles($cfiles, $package['post_title'] . " " . $odata->oid);
                            \WPDM\libs\FileSystem::downloadFile($zipped, basename($zipped));
                        } else {
                            $file = array_shift($cfiles);
                            \WPDM\libs\FileSystem::downloadFile($file, basename($file));
                        }

                        die();
                    } else {
                        Session::set('__wpdmpp_authorized_download', 1);
                        $package['access'] = array( 'guest' );
                        include(WPDM_BASE_DIR . "/wpdm-start-download.php");
                    }
                }
                //wpdmdd($odata);
                //Guest's Download
                if (@in_array($PID, $items)
                    && $OID != ''
                    && $odata->uid == 0
                    && $odata->order_status == 'Completed'
                    && isset($settings['guest_download'])
                    && Session::get('guest_order') === $OID) {
                    Session::set('__wpdmpp_authorized_download', 1);
                    $package['access'] = array( 'guest' );
                    $order = new Order();
                    $order->Update(array('download'=>1), $OID);
                    include(WPDM_BASE_DIR . "/wpdm-start-download.php");

                }

                \WPDM_Messages::error(__("&mdash; Invalid download link &mdash;", "wpdm-premium-packages"), 1);
            }



		}

		/**
		 * Create new Order
		 */
		function create_order()
		{
			global $current_user;

			//if(floatval(wpdmpp_get_cart_total()) <=0 ) return;
            $order_id = get_wpdmpp_option('order_id_prefix', 'WPDMPP').strtoupper(uniqid());

			$order = new \WPDMPP\Libs\Order();
			if (Session::get('orderid')) {
				$order_info = $order->getOrder(Session::get('orderid'));

                if ($order_info->order_id) {

                    if($order_info->order_status !== 'Completed') {
                        $data = array(
                            'cart_data' => serialize(wpdmpp_get_cart_data()),
                            'items' => serialize(array_keys(wpdmpp_get_cart_data()))
                        );
                        $order->updateOrderItems(wpdmpp_get_cart_data(), Session::get('orderid'));
                        $insertid = $order->Update($data, Session::get('orderid'));
                    } else {
                        Session::clear('orderid');
                        $cart_data = wpdmpp_get_cart_data();
                        $items = serialize(array_keys($cart_data));
                        $cart_data = serialize($cart_data);
                        $insertid = $order->newOrder($order_id, "", $items, 0, get_current_user_id(), 'Processing', 'Processing', $cart_data);
                        $order->updateOrderItems($cart_data, Session::get('orderid'));
                    }
                } else {

                    $cart_data = serialize(wpdmpp_get_cart_data());
                    $items = serialize(array_keys(wpdmpp_get_cart_data()));
                    $order->newOrder($order_id, "", $items, 0, get_current_user_id(), 'Processing', 'Processing', $cart_data);
                    $order->updateOrderItems($cart_data, Session::get('orderid'));
                }

			} else {
				$cart_data = serialize(wpdmpp_get_cart_data());
				$items = serialize(array_keys(wpdmpp_get_cart_data()));
				$insertid = $order->newOrder($order_id, '', $items, 0, get_current_user_id(), 'Processing', 'Processing', $cart_data);
				$order->updateOrderItems($cart_data, Session::get('orderid'));
			}
			return $order;
		}

        /**
         *  Set payment method for order
         */
        function set_payment_method(){
            global $current_user;

            if(wpdm_query_var('method', 'txt') != ''){
                //$order = new Order($_SESSION['orderid']);
                //$order->set('payment_method', wpdm_query_var('method', 'txt'));
                //$order->save();
                Session::set('payment_method', wpdm_query_var('method', 'txt'));
                $payment = new \WPDMPP\Libs\Payment();
                $payment->InitiateProcessor( wpdm_query_var('method', 'txt') );

                ob_start();
                $billing_required = isset($payment->Processor->billing) ? (int)$payment->Processor->billing : 0;
                $billing = array();
                if(is_user_logged_in()) {
                    $billing = BillingInfo::get(get_current_user_id());
                }

                if (get_wpdmpp_option('billing_address') == 1 || wpdmpp_tax_active() || $billing_required) {
                    // Ask Billing Address When Checkout
                    include \WPDM\Template::locate('checkout-cart/checkout-billing-info.php', dirname(__FILE__) . '/templates'.WPDM()->bsversion."/", WPDMPP_TPL_FALLBACK);
                } else {
                    // Ask only Name and Email When Checkout
                    include \WPDM\Template::locate('checkout-cart/checkout-name-email.php', dirname(__FILE__) . '/templates'.WPDM()->bsversion."/", WPDMPP_TPL_FALLBACK);
                }
                $billing_form = ob_get_clean();

                if(method_exists($payment->Processor, 'customPayButton')){
                    $cb = $payment->Processor->customPayButton();
                    if($cb != '')
                        wp_send_json(array('button' => 'custom', 'html' => $cb, 'billing_form' => $billing_form));
                }
                wp_send_json(array('button' => 'default', 'html' => '', 'billing_form' => $billing_form));
            }
        }


        /**
		 * Saving payment method info from checkout process
		 */
		function paynow()
		{
			if (isset($_REQUEST['task']) && $_REQUEST['task'] == "paynow") {

				//if(floatval(wpdmpp_get_cart_total()) <= 0 ) die('Empty Cart!');
				if(wpdmpp_is_cart_empty()) die('<div class="alert alert-danger" data-title="ERROR!">'.__('Cart is Empty!', 'wpdmp-premium-package').'</div>');
				if(!is_user_logged_in() && (!isset($_POST['billing']['order_email']) || !is_email($_POST['billing']['order_email']))) die('<div class="alert alert-danger" data-title="ERROR!">'.__('Please enter order confirmation email!', 'wpdmp-premium-package').'</div>');

				global $current_user;

				$this->create_order();

				$data = array(
					'payment_method' => sanitize_text_field( $_POST['payment_method'] ),
					'billing_info' => serialize(wpdm_sanitize_array($_POST['billing']))
				);

				$order = new \WPDMPP\Libs\Order();
				$od = $order->Update($data, Session::get('orderid'));

				//Update users billing info
				if(is_user_logged_in()){
				    $billing_info = wpdm_sanitize_array($_POST['billing']);
				    $billing_info['order_email'] = sanitize_email( $_POST['billing']['order_email'] );
				    $billing_info['email'] = sanitize_email( $_POST['billing']['order_email'] );
    				$billing_info['phone'] = '';
    				$cb = get_user_meta($current_user->ID, 'user_billing_shipping', true);
    				if(!$cb)
				    update_user_meta($current_user->ID, 'user_billing_shipping', serialize(array('billing' => $billing_info)));

				}

				$order_info = $order->GetOrder(Session::get('orderid'));
				$this->place_order();
				die();
			}
		}


		/**
		 * Placing order from checkout process
		 */
		function place_order()
		{
			//if(floatval(wpdmpp_get_cart_total()) <= 0 ) return;
            global $wpdb;
			$order = new \WPDMPP\Libs\Order();
			$order_total = $order->calcOrderTotal(Session::get('orderid'));
			$oid = Session::get('orderid');
			$tax = 0;

            $cart_data = wpdmpp_get_cart_data();
            $item = array_shift($cart_data);
            $product_name = $item['post_title'];
			$order_title = str_replace(array("{{ORDER_ID}}", "{{PRODUCT_NAME}}"), array(Session::get('orderid'), $product_name), get_wpdmpp_option('order_title'));

			if(count($order->GetOrderItems(Session::get('orderid'))) == 0){
				\WPDM_Messages::Error(__("Cart is Empty!", "wpdm-premium-packages"),0);
				die();
			}

			$subtotal = wpdmpp_get_cart_subtotal();
            if(wpdmpp_tax_active() && Session::get('tax')){
                $tax = Session::get('tax');
                $order_total = $subtotal + $tax;
            }
			$cart_id = wpdmpp_cart_id();
			$coupon = get_option($cart_id."_coupon", array('code' => '', 'discount' => 0));
			$data = array(
				'title' => $order_title,
				'subtotal' => $subtotal,
				'total' => $order_total - $coupon['discount'],
				'order_notes' => '',
				'cart_discount' => 0,
				'coupon_discount' => $coupon['discount'],
				'coupon_code' => $coupon['code'],
				'tax' => $tax
			);

			$od = $order->Update($data, Session::get('orderid'));
			//echo "<pre>";
            //print_r($data);
            //$wpdb->show_errors();
            //$wpdb->print_error();
            //die();
			do_action("wpdm_before_placing_order", Session::get('orderid'));

			// If order total is not 0 then go to payment gateway
			if ($data['total'] > 0) {
				$payment = new \WPDMPP\Libs\Payment();
				$payment->InitiateProcessor(sanitize_text_field( $_POST['payment_method'] ) );
				$payment->Processor->OrderTitle = $order_title;
				$payment->Processor->InvoiceNo = Session::get('orderid');
				$payment->Processor->Custom = Session::get('orderid');
				$payment->Processor->Amount = number_format($data['total'],2, ".", "");

				//$payment->Processor->Amount = wpdmpp_get_cart_total();
                echo $order_total;
				echo $payment->Processor->showPaymentForm(1);
				if(!isset($payment->Processor->EmptyCartOnPlaceOrder) || $payment->Processor->EmptyCartOnPlaceOrder == true)
				wpdmpp_empty_cart();
				die();
			} else {
				// if order total is 0 then empty cart and redirect to home
				\WPDMPP\Libs\Order::complete_order($oid);
				wpdmpp_empty_cart();
				wpdmpp_js_redirect(wpdmpp_orders_page('id='.$oid));
			}
		}

		function is_auto_new_active(){
            global $wpdmpp_settings;
            $wpdmpp_settings['order_validity_period'] = (int)$wpdmpp_settings['order_validity_period'] > 0 ? (int)$wpdmpp_settings['order_validity_period'] : 365;
            if (isset($wpdmpp_settings['auto_renew'], $wpdmpp_settings['order_validity_period']) && $wpdmpp_settings['auto_renew'] == 1 && $wpdmpp_settings['order_validity_period'] > 0) return true;
            return false;
        }

		/**
		 * Payment notification process/ IPN verification
		 */
		function payment_notification()
		{
			if (isset($_REQUEST['action']) && $_REQUEST['action'] == "wpdmpp-payment-notification") {

			    $payment_gateway_class = 'WPDMPP\Libs\PaymentMethods\\'.sanitize_text_field($_REQUEST['class']);
			    $payment_method = new $payment_gateway_class();

				//$payment_method = new $_REQUEST['class']();

				if ($payment_method->verifyNotification()) {
                    do_action("wpdmpp_payment_completed", $payment_method->InvoiceNo);
                    \WPDMPP\Libs\Order::complete_order($payment_method->InvoiceNo, true, $payment_method);
					do_action("wpdm_after_checkout",$payment_method->InvoiceNo);
					die('OK');
				}
				die("FAILED");
			}
		}

		/**
		 * Payment notification process/ IPN verification
		 */
		function comeplete_buynow_action()
		{
			if (wpdm_query_var('action', 'txt') === "wpdmpp-complete-buynow") {

			    $payment_gateway_class = 'WPDMPP\Libs\PaymentMethods\\'.sanitize_text_field($_REQUEST['class']);
			    $payment_method = new $payment_gateway_class();
                $payment_method->completeBuyNow();
			}
		}

		function buy_now($product_id, $license = '', $extras = array()){
		    global $wpdmpp;
		    $wpdmpp->add_to_cart($product_id);
            $wpdmpp->create_order();
            $order = new Order(Session::get('orderid'));

            wpdmpp_calculate_discount();
            $order->updateOrderItems(wpdmpp_get_cart_data(), Session::get('orderid'));
            $order_total = $order->calcOrderTotal(Session::get('orderid'));

            $tax = 0;

            foreach ($pids as $pid){
                $price = wpdmpp_effective_price($pid);
                $total += $price;
                }


            $cart_data[$product_id] = array('ID' => $product_id, 'post_title' => get_the_title($product_id), 'quantity' => 1, 'variation' => array(), 'variations' => array(), 'files' => array(), 'price' => $price, 'prices' => 0, 'discount_amount' => 0);

            $o->newOrder(wpdm_query_var('oid'), 'Custom Order', $items, $total, 0, 'Completed', 'Completed', serialize($cart_data));

            Order::updateOrderItems($cart_data, wpdm_query_var('oid'));

            $subtotal = wpdmpp_get_cart_subtotal();
            if(wpdmpp_tax_active() && Session::get('tax')){
                $tax = Session::get('tax');
                $order_total = $subtotal + $tax;
            }
            $cart_id = wpdmpp_cart_id();
            $coupon = get_option($cart_id."_coupon", array('code' => '', 'discount' => 0));

            $grand_total = $order_total - $coupon['discount'];

            $grand_total = wpdmpp_price_format($grand_total, false, false);
            if(is_user_logged_in() && $order->uid == 0)
                $order->set('uid', get_current_user_id());
            $order->set('subtotal', $subtotal);
            $order->set('cart_discount', 0);
            $order->set('payment_method', 'Paypal');
            $order->set('coupon_discount', $coupon['discount']);
            $order->set('coupon_code', $coupon['code']);
            $order->set('tax', $tax);
            $order->set('order_notes', '');
            $order->set('total', $grand_total);
            $order->save();

        }

		/**
         * Withdraw money from paypal notification
         */
        function wpdmpp_update_withdraw_status()
        {
            if (current_user_can(WPDMPP_ADMIN_CAP) && wp_verify_nonce(wpdm_query_var('__wpdmppwn_nonce'), NONCE_KEY)) {

                    global $wpdb;
                    $wpdb->update(
                        "{$wpdb->prefix}ahm_withdraws",
                        array(
                            'status' => 1
                        ),
                        array('id' => sanitize_text_field($_REQUEST['wid'])),
                        array(
                            '%d'
                        ),
                        array('%d')
                    );

                    wp_send_json(array('success' => 1));
                    die();

            }
        }



        /**
         * Payment using ajax
         */
        function wpdmpp_ajax_payfront()
        {
            if (isset($_POST['task'], $_POST['action']) && $_POST['task'] == "paymentfront" && $_POST['action'] == "wpdmpp_async_request") {
                $data['order_id'] = sanitize_text_field( $_POST['order_id'] );
                $data['payment_method'] = sanitize_text_field( $_POST['payment_method'] );
                wpdmpp_pay_now($data);
                die();
            }
        }

        /**
         * Dynamic function call using AJAX
         */
        function wpdmpp_async_request()
        {
            $CustomActions = new \WPDMPP\Libs\CustomActions();
            if (method_exists($CustomActions, $_POST['execute'])) {
                $method = sanitize_text_field($_POST['execute']);
                echo $CustomActions->$method();
                die();
            } else
            die("Function doesn't exist");
        }

        /**
         * Execute Custom Action
         */
        function anync_execute()
        {
            $CustomActions = new \WPDMPP\Libs\CustomActions();
            if(isset($_POST['action']) && $_POST['action']=='wpdmpp_anync_exec'){
                if (method_exists($CustomActions, $_POST['execute'])) {
                    $method = sanitize_text_field($_POST['execute']);
                    echo $CustomActions->$method();
                    die();
                }
            }

        }


        /**
         * Update User Profile
         */
        function wpdmpp_update_profile()
        {

            if (is_user_logged_in() && wp_verify_nonce(wpdm_query_var('__upnonce'), NONCE_KEY) && isset($_POST['profile'])) {

                $userdata = $_POST['profile'];
                $userdata['ID'] = get_current_user_id();
                if ($_POST['password'] == $_POST['cpassword']) {
                    wp_update_user($userdata);
                    $userdata['user_pass'] = $_POST['password'];
                    update_user_meta(get_current_user_id(), 'payment_account', sanitize_text_field($_POST['payment_account']));
                    update_user_meta(get_current_user_id(), 'phone', sanitize_text_field($_POST['phone']));
                    Session::set('member_success', __("Profile Updated Successfully", "wpdm-premium-packages"));

                } else {
                    $merror = Session::get('member_error');
                    $merror = is_array($merror)?$merror:array();
                    $merror[] = __("Confirm Password Not Matched. Profile Update Failed!", "wpdm-premium-packages");
                    Session::set('member_error', $merror);
                }
                update_user_meta(get_current_user_id(), 'user_billing_shipping', serialize(wpdm_sanitize_array($_POST['checkout'])));

                wpdmpp_redirect($_SERVER['HTTP_REFERER']);
                die();
            }

        }

        /**
         * Load Scripts and Styles
         * @param $hook
         */
        function wpdmpp_enqueue_scripts($hook)
        {

            $settings = get_option('_wpdmpp_settings');
            $cart_page = isset($settings['page_id']) ? $settings['page_id'] : 0;

            wp_enqueue_script('wpdm-pp-js', WPDMPP_BASE_URL.'assets/js/wpdmpp-front.js', array('jquery', 'jquery-form') );
            if(!isset($settings['disable_fron_end_css']) || (int)$settings['disable_fron_end_css'] === 0)
                wp_enqueue_style('wpdmpp-front', WPDMPP_BASE_URL.'assets/css/wpdmpp.css', 999999);

            //if((int)$cart_page === (int)get_the_ID()){
                //wp_enqueue_script('jquery-validate');
            //}

            if( get_the_ID() == $settings['orders_page_id'] || (  isset($settings['guest_order_page_id']) && get_the_ID() == $settings['guest_order_page_id'] ) ) {
                wp_enqueue_script('thickbox');
                wp_enqueue_style('thickbox');
                wp_enqueue_script('media-upload');
                wp_enqueue_media();
            }
        }

        function wpdmpp_admin_enqueue_scripts($hook)
        {
        	if( get_post_type() == 'wpdmpro' || strstr($hook, 'dmpro_page')) {
                wp_enqueue_script('jquery');
                wp_enqueue_script('jquery-form');
                wp_enqueue_script('jquery-ui-core');
                wp_enqueue_script('jquery-ui-datepicker');
                wp_enqueue_script('jquery-ui-accordion');

                wp_enqueue_style('wpdmpp-admin', WPDMPP_BASE_URL.'assets/css/wpdmpp-admin.min.css' );
                wp_enqueue_script('wpdmpp-admin-js', WPDMPP_BASE_URL.'assets/js/wpdmpp-admin.js', array('jquery'));

                // Load Download Manager Scripts
                wp_enqueue_style('wpdm-admin-bootstrap', WPDM_BASE_URL . 'assets/bootstrap3/css/bootstrap.css');
                wp_enqueue_script('wpdm-admin-bootstrap', WPDM_BASE_URL . 'assets/bootstrap3/js/bootstrap.min.js', array('jquery'));
                wp_enqueue_script('jquery-validate', WPDM_BASE_URL.'assets/js/jquery.validate.min.js', array('jquery'));
                //wp_enqueue_script('wpdm-bootstrap-select', WPDM_BASE_URL.'assets/js/bootstrap-select.min.js',  array('jquery', 'wpdm-admin-bootstrap'));
                //wp_enqueue_style('wpdm-bootstrap-select', WPDM_BASE_URL.'assets/css/bootstrap-select.min.css');
            }
        }

        /**
         * Check if a Package is premium
         * @param $pid
         * @return bool
         */
        public static function isPremium($pid){
            $price = wpdmpp_product_price($pid);
            if($price > 0) return true;
            return false;
        }

        /**
         * @usage Check if user purchased an item
         * @param $pid
         * @param int $uid
         * @return bool|string|null
         */

        public static function hasPurchased($pid, $uid = 0){
            global $current_user, $wpdb;
            if(!is_user_logged_in() && !$uid) return false;
            $uid = $uid?$uid:$current_user->ID;
            $orderid = $wpdb->get_var("select o.order_id from {$wpdb->prefix}ahm_orders o, {$wpdb->prefix}ahm_order_items oi  where uid='{$uid}' and o.order_id = oi.oid and oi.pid = {$pid} and order_status='Completed'");
            return $orderid;
        }

        /**
         * Generate Download URL
         * @param $id Package ID
         * @return string
         */
        static function customerDownloadLink($id){
            global $wpdmpp_settings;
            $downloadurl = self::customerDownloadURL($id);
            $label = get_post_meta($id, '__wpdm_link_label', true);
            $label = $label?$label:__('Download', 'wpdm-premium-packages');
            if($downloadurl)
                return "<a class='btn btn-success btn-lg' href='{$downloadurl}'>{$label}</a>";
            return isset($wpdmpp_settings['cdl_fallback']) && $wpdmpp_settings['cdl_fallback'] == '1'?wpdmpp_add_to_cart_html($id):"";
        }

        /**
         * @param $pid
         * @param null $orderid
         * @param array $extras
         * @return string
         */
        static function customerDownloadURL($pid, $orderid = null, $extras = array())
        {
            if(!$orderid)
                $orderid = self::hasPurchased($pid);
            if(!$orderid) return null;
            $params = is_array($extras) ? $extras : array();
            $params['ID'] = $pid;
            $params['oid'] = $orderid;
            if(defined('WPDMPPD_PERMALINK') && WPDMPPD_PERMALINK === true) {
                $wpdmppd = wpdmppdl_encode($params);
                return home_url("/?wpdmppdl={$wpdmppd}");
            } else {
                $wpdmppd = Crypt::encrypt($params);
                return home_url("/?wpdmppd={$wpdmppd}");
            }
        }

        function hide_single_file_download_link($link, $fileID, $package){
            if(!isset($package['ID'])) return $link;
            $effective_price = wpdmpp_effective_price($package['ID']);
            if($effective_price > 0) $link = '';
            return $link;
        }

        public static function hasFreeFile($id = null){
        	if(!$id) $id = get_the_ID();
        	$fd = maybe_unserialize(get_post_meta($id, '__wpdm_free_downloads', true));
        	if(is_array($fd) && count($fd) > 0 && $fd[0] != '') return $fd;
        	return false;
        }

        function freeDownload(){
        	if(isset($_GET['wpdmdlfree'])){
        		$id = (int)$_GET['wpdmdlfree'];
        		$freefiles = self::hasFreeFile($id);

        		if(!$freefiles) wp_die('No free file found!');
        		$pack = array('ID' => $id);
        		do_action("wpdm_onstart_download", $pack);

                if(count($freefiles) > 1) {
                    foreach ($freefiles as &$freefile){
                        $freefile = str_replace(site_url('/'), ABSPATH, $freefile);
                    }
                    $zipped = \WPDM\libs\FileSystem::zipFiles($freefiles, get_the_title($id));
                    \WPDM\libs\FileSystem::downloadFile($zipped, basename($zipped));
                } else
                    header("location: ".array_pop($freefiles));
				die();
        	}
        }

        /**
		 * @param $id
		 * @param $link_label
		 * @param string $class
		 * @return string
		 */
        static function free_download_button($id, $link_label, $class = 'btn btn-lg btn-info btn-block'){
        	return "<a href='".home_url('/?wpdmdlfree='.$id)."' class='{$class}' >".$link_label."</a>";
        }


        function download_link($link, $package){
        	$effective_price = wpdmpp_effective_price($package['ID']);
        	if ($effective_price > 0) {
        		return wpdmpp_add_to_cart_html($package['ID']);
        	}

        	return $link;
        }


        /**
		 * @param $vars
		 * @return mixed
		 */
        function fetch_template_tag($vars)
        {
            global $wpdb, $wpdmpp_settings;

            //$vars['base_price'] = get_post_meta($vars['ID'], '__wpdm_base_price', true);
            //$vars['sales_price'] = get_post_meta($vars['ID'], '__wpdm_sales_price', true);
            $effective_price = wpdmpp_effective_price($vars['ID']);
            $vars['effective_price'] = $effective_price;
            $vars['currency'] = wpdmpp_currency_sign();
            $vars['currency_code'] = wpdmpp_currency_code();
            $vars['free_download_btn'] = "";
            $vars['premium_file_list'] = \WPDM\libs\FileList::Table($vars);
            if(!isset($vars['post_author'])) {
                $product = (array)get_post($vars['ID']);
                $vars += $product;
            }
            $store = get_user_meta($vars['post_author'], '__wpdm_public_profile', true);
            $vars['author_name'] = isset($vars['author_name'])?$vars['author_name']:get_userdata($vars['post_author'])->display_name;
            $vars['store_name'] = isset($store['title'])?$store['title']:$vars['author_name'];
            $vars['store_intro'] = isset($store['intro'])?$store['intro']:'';
            $vars['store_logo'] = isset($store['logo']) && $store['logo'] != ''?"<img class='store-logo' src='{$store['logo']}' alt='{$vars['store_name']}' />":get_avatar($vars['post_author'], 512);

            if($effective_price > 0 && self::hasFreeFile($vars['ID'])){
            	$vars['free_download_btn'] = self::free_download_button($vars['ID'], $vars['link_label']);
            	$vars['free_download_url'] = home_url('/?wpdmdlfree='.$vars['ID']);
            }
            else {
            	$vars['free_download_btn'] = $vars['free_download_url'] = '';
           	}
            if ($effective_price > 0 || get_post_meta($vars['ID'], '__wpdm_pay_as_you_want', true) == 1) {
            	if(method_exists(new \WPDM\libs\FileList(), 'Premium'))
                $vars['premium_file_list'] = \WPDM\libs\FileList::Premium($vars);
                $vars['addtocart_url'] = wpdmpp_cart_page(array('addtocart' => $vars['ID']));
                $vars['addtocart_link'] = wpdmpp_waytocart($vars);
                $vars['addtocart_button'] = $vars['addtocart_link'];
                $vars['addtocart_form'] = wpdmpp_add_to_cart_html($vars['ID']);
                $vars['customer_download_link'] = $this->customerDownloadLink($vars['ID']);
                if(isset($vars['__template_type']) && $vars['__template_type'] == 'link')
                    $vars['download_link'] = $vars['addtocart_button'];
                else
                    $vars['download_link'] = $vars['addtocart_form'];
                $vars['download_link_extended'] = $vars['addtocart_form'];
                $vars['download_link_popup'] = $vars['addtocart_button'];
                $vars['price_range'] = wpdmpp_price_range($vars['ID']);
            } else {
                $vars['addtocart_url'] = $vars['download_url'];
                $vars['addtocart_link'] = $vars['download_link'];
                $vars['addtocart_form'] = $vars['download_link'];
                $vars['customer_download_link'] = $vars['download_link'];
                $vars['price_range'] =  wpdmpp_currency_sign().'0.00';
            }

            return $vars;
        }

        function template_editor_menu(){
            ?>
            <li class="dropdown">
                <a href="#" id="droppp" role="button" class="dropdown-toggle" data-toggle="dropdown"><?php _e('Premium Package','wpdm-premium-packages'); ?><b class="caret"></b></a>
                <ul class="dropdown-menu" role="menu" aria-labelledby="droppp">
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#[addtocart_url]"><?php _e('AddToCart URL','wpdm-premium-packages'); ?></a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#[addtocart_link]"><?php _e('AddToCart Link','wpdm-premium-packages'); ?></a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#[addtocart_form]"><?php _e('AddToCart Form','wpdm-premium-packages'); ?></a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#[customer_download_link]"><?php _e('Customer Download Link','wpdm-premium-packages'); ?></a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#[free_download_url]"><?php _e('Free Download Button','wpdm-premium-packages'); ?></a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#[free_download_btn]"><?php _e('Free Download URL','wpdm-premium-packages'); ?></a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#[price_range]"><?php _e('Price Range','wpdm-premium-packages'); ?></a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#[premium_file_list]"><?php _e('File List Price','wpdm-premium-packages'); ?></a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#[effective_price]"><?php _e('Effective Item Price','wpdm-premium-packages'); ?></a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#[currency_code]"><?php _e('Currency Code','wpdm-premium-packages'); ?></a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#[currency]"><?php _e('Currency Sign','wpdm-premium-packages'); ?></a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#[base_price]"><?php _e('Base Price','wpdm-premium-packages'); ?></a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#[sales_price]"><?php _e('Sales Price','wpdm-premium-packages'); ?></a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#[store_name]"><?php _e('Shop Name','wpdm-premium-packages'); ?></a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#[store_intro]"><?php _e('Shop Intro','wpdm-premium-packages'); ?></a></li>
                    <li role="presentation"><a role="menuitem" tabindex="-1" href="#[store_logo]"><?php _e('Shop Logo','wpdm-premium-packages'); ?></a></li>
                </ul>
            </li>

            <?php
        }

        function template_tag_row(){
            ?>
            <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[addtocart_url]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('AddToCart URL for a package','wpdm-premium-packages'); ?></td></tr>
            <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[addtocart_link]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('AddToCart Link for a package','wpdm-premium-packages'); ?></td></tr>
            <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[addtocart_form]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('AddToCart Form','wpdm-premium-packages'); ?></td></tr>
            <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[customer_download_link]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('Customer Download Link','wpdm-premium-packages'); ?></td></tr>
            <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[free_download_btn]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('Free Download Button','wpdm-premium-packages'); ?></td></tr>
            <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[free_download_url]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __('Free Download URL','wpdm-premium-packages'); ?></td></tr>
            <?php
        }

        /**
         * Required for guest checkout
         */
        function wpdmpp_invoice_field(){
            $oid = Session::get("orderid");
            if($oid){
                echo "<input type='hidden' name='invoice' value='".sanitize_text_field($oid)."' />";
            }
        }

        /**
         * Link Guest Order when user logging in
         * @param $user_login
         * @param $user
         */
        function wpdmpp_associate_invoice($user_login, $user){
            if(isset($_POST['invoice'])){
               $order = new \WPDMPP\Libs\Order();
               $orderdata = $order->GetOrder(sanitize_text_field($_POST['invoice']));
                if($orderdata && intval($orderdata->uid) == 0){
                    \WPDMPP\Libs\Order::Update( array('uid'=>$user->ID), sanitize_text_field( $_POST['invoice'] ) );
                }
            }
        }

        /**
         * Link Guest Order when user Signing Up
         * @param $user_id
         */
        function wpdmpp_associate_invoice_signup($user_id){
            if(isset($_POST['invoice'])){
               $order = new \WPDMPP\Libs\Order();
               $orderdata = $order->GetOrder(sanitize_text_field($_POST['invoice']));
                if($orderdata && intval($orderdata->uid) == 0){
                    \WPDMPP\Libs\Order::Update(array('uid'=>$user_id), sanitize_text_field( $_POST['invoice'] ) );
                    User::addCustomer($user_id);
                }
            }
        }

        /**
         * Resolve unassigned Order
         */
        function wpdmpp_resolveorder(){
            global $current_user;
            $order = new \WPDMPP\Libs\Order();
            $data = $order->GetOrder(sanitize_text_field($_REQUEST['orderid']));
            if(!$data) die("Order not found!");
            if($data->uid!=0) {
                if($data->uid == $current_user->ID)
                die("The order is already linked to your account!");
                else
                die("The order is already linked to an account!");
            }
            \WPDMPP\Libs\Order::Update(array('uid'=>$current_user->ID), $data->order_id);
            User::addCustomer();
            die("ok");
        }

        /**
         * Filter for locked Downloads
         * @param $lock
         * @param $id
         * @return string
         */
        function wpdmpp_lock_download($lock, $id){
            $effective_price = wpdmpp_effective_price($id);
            if( intval($effective_price) > 0 )
                $lock = 'locked';

            return $lock;
        }

        function wpdmpp_guest_download_link(){
            global $wp_query;

            if( isset( $wp_query->query_vars['udb_page'] ) && strstr($wp_query->query_vars['udb_page'], 'purchases' ) &&  wpdmpp_guest_order_page() ):
                include_once \WPDM\Template::locate("partials/guest_order_page_link.php", WPDMPP_TPL_DIR);
            endif;
        }

        function toggle_auto_renew(){
            if(isset($_REQUEST['__arnonce']) && wp_verify_nonce($_REQUEST['__arnonce'], NONCE_KEY)){
                $order = new \WPDMPP\Libs\Order(sanitize_text_field($_REQUEST['orderid']));
                $renew = $order->auto_renew == 1?0:1;
                $order->set('auto_renew', $renew);
                $order->save();
                $dt = array('renew' => $renew);
                $pm = "\WPDMPP\Libs\PaymentMethods\\".$order->payment_method;
                if(class_exists($pm) && $renew == 0) {
                    $pm = new $pm();
                    $dt['payment_method'] = $order->payment_method;
                    if(method_exists($pm, 'cancelSubscription')) {
                        $pm->cancelSubscription($order->order_id);
                        $dt['canceled'] = 1;
                    }

                }
                header("Content-type: application/json");
                echo json_encode($dt);
            } else
                echo json_encode(array('error' => 'Session Expired!'));
            die();
        }

        function cancel_subscription(){
            if(isset($_REQUEST['__cansub']) && wp_verify_nonce($_REQUEST['__cansub'], NONCE_KEY)){
                $order = new \WPDMPP\Libs\Order(sanitize_text_field($_REQUEST['orderid']));
                $renew = 0;
                $order->set('auto_renew', $renew);
                $order->save();
                $dt = array('renew' => $renew);
                $pm = "\WPDMPP\Libs\PaymentMethods\\".$order->payment_method;
                if(class_exists($pm)) {
                    $pm = new $pm();
                    $dt['payment_method'] = $order->payment_method;
                    if(method_exists($pm, 'cancelSubscription')) {
                        $pm->cancelSubscription($order->order_id);
                        $dt['canceled'] = 1;
                    }

                }
                $message     = "Subscription Canceled For Order# {$order->oid}<br/><a style='background-color:#19B999;border:none;border-radius:3px;color:#ffffff !important;display:inline-block;font-size:14px;font-weight:bold;outline:none!important;padding:5px 15px;margin:10px auto;text-decoration:none;' href='".admin_url("/edit.php?post_type=wpdmpro&page=orders&task=vieworder&id={$order->oid}")."'>View Order</a>";
                $params = array('subject' => "Subscription Canceled: Order# {$order->oid}", 'to_email' => get_option("admin_email"), 'message' => $message);
                \WPDM\Email::send('default', $params);
                header("Content-type: application/json");
                echo json_encode($dt);
                die();
            } else
                echo json_encode(array('error' => 'Session Expired!'));
            die();
        }


        function privacy_settings(){
            ?>
            <div class="panel panel-default">
                <div class="panel-heading"><?php _e('Checkout Settings', 'wpdm-premium-packages'); ?></div>
                <div class="panel-body">

                    <div class="form-group">
                        <input type="hidden" value="0" name="__wpdm_checkout_privacy" />
                        <label><input style="margin: 0 10px 0 0" type="checkbox" <?php checked(get_option('__wpdm_checkout_privacy'),1); ?> value="1" name="__wpdm_checkout_privacy"><?php _e('Must agree with privacy policy before checkout','wpdm-premium-packages'); ?></label><br/>
                        <em><?php _e('User must agree with privacy policy before they purchase any item','wpdm-premium-packages'); ?></em>
                    </div>
                    <div class="form-group">
                        <label><?php _e('Privacy policy label:','wpdm-premium-packages'); ?></label>
                        <input type="text" class="form-control" value="<?php echo get_option('__wpdm_checkout_privacy_label'); ?>" name="__wpdm_checkout_privacy_label">
                    </div>

                </div>
            </div>

            <?php
        }


        function expire_orders(){
            if(current_user_can(WPDMPP_ADMIN_CAP)){
                $oids = $_REQUEST['oids'];
                foreach ($oids as $oid){
                    Order::expireOrder($oid);
                 }
            }
            die('Done!');
        }

        function active_payment_gateways(){
            global $payment_methods;
            $settings           = maybe_unserialize(get_option('_wpdmpp_settings'));
            $payment_methods    = apply_filters('payment_method', $payment_methods);
            $payment_methods    = isset($settings['pmorders']) && count($settings['pmorders']) == count($payment_methods) ? $settings['pmorders'] : $payment_methods;
            return $payment_methods;
        }


	}

endif;

    if(defined('WPDM_Version'))
        $wpdmpp = new WPDMPremiumPackage();

else {
    class RequireWPDM{

        function __construct()
        {
            add_action( 'admin_notices', array( $this, 'check' ) );
        }

        function check() {
            $class = 'notice notice-error';
            $message = '<strong>Missing a required plugin!</strong><br/>Please install/activate <a href="'.admin_url('/plugin-install.php?tab=favorites&user=codename065').'" target="_blank"><strong>WordPress Download Manager</strong></a> to use WPDM - Premium Packages plugin';
            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), ( $message ) );
        }
    }
    new RequireWPDM();
}


