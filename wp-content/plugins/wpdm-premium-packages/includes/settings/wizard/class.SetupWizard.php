<?php
/**
 * Premium Packages Setup Wizard Class - Takes new users through some basic steps to setup Premium Packages Add-on.
 *
 * @version     1.0.0
 */
namespace WPDMPP\Libs;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Premium_Packages_Setup_Wizard {

    /** @var string Current Step */
    private $step   = '';
    private $steps  = array();

    public function __construct() {

        if ( current_user_can( 'manage_options' ) ) {
            add_action( 'admin_menu', array( $this, 'admin_menus' ) );
            add_action( 'admin_init', array( $this, 'setup_wizard' ) );
        }
    }

    /**
     * Add admin menus/screens.
     */
    public function admin_menus() {
        add_dashboard_page( '', '', 'manage_options', 'wpdmpp-setup', '' );
    }

    /**
     * Show the setup wizard.
     */
    public function setup_wizard() {
        if ( empty( $_GET['page'] ) || 'wpdmpp-setup' !== $_GET['page'] ) {
            return;
        }
        $default_steps = array(
            'basics' => array(
                'name'    => __( 'Basics', 'wpdm-premium-packages' ),
                'view'    => array( $this, 'wpdmpp_basic_options' ),
                'handler' => array( $this, 'wpdmpp_basic_options_save' ),
            ),
            'pages' => array(
                'name'    => __( 'Pages', 'wpdm-premium-packages' ),
                'view'    => array( $this, 'wpdmpp_pages_options' ),
                'handler' => array( $this, 'wpdmpp_pages_options_save' ),
            ),
            'payment' => array(
                'name'    => __( 'Payment', 'wpdm-premium-packages' ),
                'view'    => array( $this, 'wpdmpp_payment_options' ),
                'handler' => array( $this, 'wpdmpp_payment_options_save' ),
            ),

            'ready' => array(
                'name'    => __( 'Ready!', 'wpdm-premium-packages' ),
                'view'    => array( $this, 'wpdmpp_setup_ready' ),
                'handler' => '',
            ),
        );

        $this->steps = apply_filters( 'wpdmpp_setup_wizard_steps', $default_steps );
        $this->step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) );

        wp_enqueue_style('wpdm-bootstrap', WPDM_BASE_URL.'assets/bootstrap/css/bootstrap.css' );
        wp_enqueue_style('font-awesome', WPDM_BASE_URL . 'assets/font-awesome/css/font-awesome.min.css');
        wp_enqueue_style('chosen-css', WPDM_BASE_URL.'assets/css/chosen.css' );
        wp_enqueue_style( 'wpdmpp-wizard', WPDMPP_BASE_URL . 'includes/settings/wizard/wizard.css' );

        wp_register_script('chosen', WPDM_BASE_URL.'assets/js/chosen.jquery.min.js', array('jquery'));
        wp_register_script( 'wpdmpp-wizard', WPDMPP_BASE_URL . 'includes/settings/wizard/wizard.js', array( 'chosen' ) );

        if ( ! empty( $_POST['save_step'] ) && isset( $this->steps[ $this->step ]['handler'] ) ) {
            call_user_func( $this->steps[ $this->step ]['handler'], $this );
        }

        ob_start();
        $this->setup_wizard_header();
        $this->setup_wizard_steps();
        $this->setup_wizard_content();
        $this->setup_wizard_footer();
        exit;
    }

    /**
     * Get the URL for the next step's screen.
     * @param string step   slug (default: current step)
     * @return string       URL for next step if a next step exists.
     *                      Admin URL if it's the last step.
     *                      Empty string on failure.
     */
    public function get_next_step_link( $step = '' ) {
        if ( ! $step ) {
            $step = $this->step;
        }

        $keys = array_keys( $this->steps );
        if ( end( $keys ) === $step ) {
            return admin_url();
        }

        $step_index = array_search( $step, $keys );
        if ( false === $step_index ) {
            return '';
        }

        return add_query_arg( 'step', $keys[ $step_index + 1 ], remove_query_arg( 'activate_error' ) );
    }

    /**
     * Setup Wizard Header.
     */
    public function setup_wizard_header() {
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta name="viewport" content="width=device-width" />
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title><?php esc_html_e( 'Premium Package &rsaquo; Setup Wizard', 'wpdm-premium-packages' ); ?></title>
            <?php wp_print_scripts( 'wpdmpp-wizard' ); ?>
            <?php do_action( 'admin_print_styles' ); ?>
            <?php do_action( 'admin_head' ); ?>
        </head>
        <body class="wpdmpp-setup">
        <h1 id="wpdmpp-logo">
            <a href="https://www.wpdownloadmanager.com/download/premium-package-wordpress-digital-store-solution/">
                <img src="<?php echo WPDMPP_BASE_URL."/assets/images/download-manager-logo-v4.png";?>" alt="Premium Package" />
            </a>
        </h1>
        <?php
    }

    /**
     * Setup Wizard Footer.
     */
    public function setup_wizard_footer() {
        ?>
        <?php if ( 'basics' === $this->step ) : ?>
            <a class="wpdmpp-return-to-dashboard" href="<?php echo esc_url( admin_url() ); ?>"><?php esc_html_e( 'Not right now', 'wpdm-premium-packages' ); ?></a>
        <?php elseif ( 'ready' === $this->step ) : ?>
            <a class="wpdmpp-return-to-dashboard" href="<?php echo esc_url( admin_url() ); ?>"><?php esc_html_e( 'Return to your dashboard', 'wpdm-premium-packages' ); ?></a>
        <?php endif; ?>
        </body>
        </html>
        <?php
    }

    /**
     * Output the steps.
     */
    public function setup_wizard_steps() {
        $output_steps = $this->steps;
        ?>
        <ul class="wpdmpp-setup-steps">
            <?php foreach ( $output_steps as $step_key => $step ) : ?>
                <li class="<?php
                if ( $step_key === $this->step ) {
                    echo 'current';
                } elseif ( array_search( $this->step, array_keys( $this->steps ) ) > array_search( $step_key, array_keys( $this->steps ) ) ) {
                    echo 'completed';
                }
                ?>"><?php echo esc_html( $step['name'] ); ?></li>
            <?php endforeach; ?>
        </ul>
        <?php
    }

    /**
     * Output the content for the current step.
     */
    public function setup_wizard_content() {
        echo '<div class="wpdmpp-setup-content w3eden">';
        call_user_func( $this->steps[ $this->step ]['view'], $this );
        echo '</div>';
    }

    /**
     * Basics step.
     *
     */
    public function wpdmpp_basic_options() {

        $settings = maybe_unserialize(get_option('_wpdmpp_settings'));
        ?>
        <form method="post" class="basics-step">
            <?php wp_nonce_field( 'wpdmpp-setup' ); ?>

            <input class="sw" id="billing_address" type="checkbox" name="_wpdmpp_settings[billing_address]" <?php if (isset($settings['billing_address']) && $settings['billing_address'] == 1) echo 'checked=checked' ?> value="1">
            <label for="billing_address"><span><?php esc_html_e("Ask Billing Address When Checkout", "wpdm-premium-packages"); ?></span></label><br/>
            <p class="option-note">A customer must provide billing info ( Name, Address, Email, Phone etc. ) before checkout.</p>
            <hr />

            <input class="sw" id="guest_checkout" type="checkbox" name="_wpdmpp_settings[guest_checkout]" <?php if (isset($settings['guest_checkout']) && $settings['guest_checkout'] == 1) echo 'checked=checked' ?> value="1">
            <label for="guest_checkout"><span><?php esc_html_e("Enable Guest Checkout", "wpdm-premium-packages"); ?></span></label><br/>
            <p class="option-note">Customers can purchase the product without signing up. Just collect Email and Name during checkout.</p>
            <hr />

            <input class="sw" id="guest_download" type="checkbox" name="_wpdmpp_settings[guest_download]" <?php if (isset($settings['guest_download']) && $settings['guest_download'] == 1) echo 'checked=checked' ?> value="1">
            <label for="guest_download"><span><?php esc_html_e("Enable Guest Download", "wpdm-premium-packages"); ?></span></label><br/>
            <p class="option-note">Let customers download the purchased product without signing up. To access the download user must provide order id and order email ( the email used for checkout )</p>
            <hr />

            <input class="sw" id="wpdmpp_after_addtocart_redirect" type="checkbox" name="_wpdmpp_settings[wpdmpp_after_addtocart_redirect]" <?php if ( isset($settings['wpdmpp_after_addtocart_redirect']) &&  $settings['wpdmpp_after_addtocart_redirect'] == 1 ) echo "checked='checked'"; ?> value="1">
            <label for="wpdmpp_after_addtocart_redirect"><span><?php esc_html_e("Redirect to shopping cart after a product is added to the cart", "wpdm-premium-packages"); ?></span></label><br/>
            <p class="option-note">Redirects the user to the cart page after clicking Add To Cart button. If the option is disabled user stays on the product page and can continue shopping from there.</p>
            <hr />

            <input class="sw" id="tax_calculation" type="checkbox" name="_wpdmpp_settings[tax][enable]" <?php if (isset($settings['tax']['enable']) && $settings['tax']['enable'] == 1) echo "checked='checked'"; ?> value="1">
            <label for="tax_calculation"><span><?php esc_html_e("Enable tax calculation", "wpdm-premium-packages"); ?></span></label><br/>
            <p class="option-note">Enables taxes on purchases. After this quick setup you can add tax rates for specific countries and/or states/provinces from Premium Package tax settings page.</p>
            <hr />

            <p class="wpdmpp-setup-actions">
                <input type="submit" class="button button-primary" value="<?php esc_attr_e( "Let's go!", 'wpdm-premium-packages' ); ?>" name="save_step" />
            </p>
        </form>
        <?php
    }

    /**
     * Save Basics settings.
     */
    public function wpdmpp_basic_options_save() {
        check_admin_referer( 'wpdmpp-setup' );

        //echo '<pre>';print_r($_POST['_wpdmpp_settings']);echo '</pre>';

        if( ! get_option('_wpdmpp_settings') )
            $settings = array();
        else
            $settings = maybe_unserialize( get_option('_wpdmpp_settings') );

        if( isset( $_POST['_wpdmpp_settings']['billing_address'] ) )
            $settings['billing_address'] = sanitize_text_field($_POST['_wpdmpp_settings']['billing_address']);
        else
            unset($settings['billing_address']);

        if( isset( $_POST['_wpdmpp_settings']['guest_checkout'] ) )
            $settings['guest_checkout'] = (int)$_POST['_wpdmpp_settings']['guest_checkout'];
        else
            unset($settings['guest_checkout']);

        if( isset( $_POST['_wpdmpp_settings']['guest_download'] ) )
            $settings['guest_download'] = (int)$_POST['_wpdmpp_settings']['guest_download'];
        else
            unset($settings['guest_download']);

        if( isset( $_POST['_wpdmpp_settings']['wpdmpp_after_addtocart_redirect'] ) )
            $settings['wpdmpp_after_addtocart_redirect'] = sanitize_text_field($_POST['_wpdmpp_settings']['wpdmpp_after_addtocart_redirect']);
        else
            unset($settings['wpdmpp_after_addtocart_redirect']);

        if( isset( $_POST['_wpdmpp_settings']['tax']['enable'] ) )
            $settings['tax']['enable'] = (int)$_POST['_wpdmpp_settings']['tax']['enable'];
        else
            unset($settings['tax']['enable']);

        update_option('_wpdmpp_settings', $settings);

        wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
        exit;
    }

    /**
     * Pages step.
     *
     */
    public function wpdmpp_pages_options() {

        global $wpdb;
        $settings = maybe_unserialize(get_option('_wpdmpp_settings'));

        if ( ! $cart_page_id = $wpdb->get_var("select id from {$wpdb->prefix}posts where post_type='page' AND post_content like '%[wpdmpp_cart]%'")) {
            $cart_page_id = wp_insert_post( array('post_title' => 'Cart', 'post_content' => '[wpdmpp_cart]', 'post_type' => 'page', 'post_status' => 'publish') );
        }
        if ( ! $orders_page_id = $wpdb->get_var("select id from {$wpdb->prefix}posts where post_type='page' AND post_content like '%[wpdmpp_purchases]%'")) {
            $orders_page_id = wp_insert_post( array('post_title' => 'Purchases', 'post_content' => '[wpdmpp_purchases]', 'post_type' => 'page', 'post_status' => 'publish') );
        }
        if ( ! $guest_orders_page_id = $wpdb->get_var("select id from {$wpdb->prefix}posts where post_type='page' AND post_content like '%[wpdmpp_guest_orders]%'")) {
            $guest_orders_page_id = wp_insert_post( array('post_title' => 'Guest Orders', 'post_content' => '[wpdmpp_guest_orders]', 'post_type' => 'page', 'post_status' => 'publish') );
        }
        //print_r($settings['page_id']);
        ?>
        <form method="post" class="pages-step">
            <?php wp_nonce_field( 'wpdmpp-setup' ); ?>

            <label><?php esc_html_e("Cart Page", "wpdm-premium-packages"); ?></label><br>
            <?php
            $args = array(
                'show_option_none' => __('None Selected','wpdm-premium-packages'),
                'name' => '_wpdmpp_settings[page_id]',
                'selected' => isset( $settings['page_id'] ) && $settings['page_id'] != "" ? $settings['page_id'] : $cart_page_id
            );
            wp_dropdown_pages($args);
            ?>
            <p class="option-note-no-pad">This page will show cart items with prices, tax info, payment options and billing info. Customers checkout from this page. The <code>[wpdmpp_cart]</code> shortcode must be on this page.</p>
            <hr/>

            <label><?php esc_html_e("Orders / Purchases Page", "wpdm-premium-packages"); ?></label><br>
            <?php
            $args = array(
                'name' => '_wpdmpp_settings[orders_page_id]',
                'show_option_none' => __('None Selected','wpdm-premium-packages'),
                'selected' => isset( $settings['orders_page_id'] ) && $settings['orders_page_id'] != "" ? $settings['orders_page_id'] : $orders_page_id
            );

            wp_dropdown_pages($args);
            ?>
            <p class="option-note-no-pad">Orders page is optional if you have User Dashboard ( <code>[wpdm_user_dashboard flaturl=0]</code> ) Page, becasue User Dashboard page has a tab that lists all orders. This page lists all purchases by current ( logged in ) customer. The <code>[wpdmpp_purchases]</code> shortcode must be on this page.</p>
            <hr/>

            <?php if( isset( $settings['guest_download'] ) ): ?>
            <div class="guest-orders-page">
                <label><?php esc_html_e("Guest Order Page", "wpdm-premium-packages"); ?></label><br>
                <?php
                $args = array(
                    'name' => '_wpdmpp_settings[guest_order_page_id]',
                    'show_option_none' => __('None Selected','wpdm-premium-packages'),
                    'selected' => isset( $settings['guest_order_page_id'] ) && $settings['guest_order_page_id'] != "" ? $settings['guest_order_page_id'] : $guest_orders_page_id
                );
                wp_dropdown_pages($args);
                ?>
            </div>
            <p class="option-note-no-pad">When guest checkout and download option is enabled, the guest customer ( didn't sign up ) can download product and invoice from here. The <code>[wpdm-pp-guest-orders]</code> shortcode should be on this page.</p>
            <hr />
            <?php endif; ?>

            <label><?php esc_html_e("Continue Shopping URL", "wpdm-premium-packages"); ?></label><br/>
            <input type="text" class="form-control" name="_wpdmpp_settings[continue_shopping_url]" size="50" id="continue_shopping_url" value="<?php echo isset($settings['continue_shopping_url']) && $settings['continue_shopping_url'] != '' ? $settings['continue_shopping_url'] : home_url(); ?>"/>
            <p class="option-note-no-pad">Cart page and cart widget have a "Continue Shopping" button. That button will link to this URL.</p>
            <hr />

            <p class="wpdmpp-setup-actions">
                <input type="submit" class="button button-primary" value="<?php esc_attr_e( "Continue", 'wpdm-premium-packages' ); ?>" name="save_step" />
            </p>
        </form>
        <?php
    }

    /**
     * Save Pages settings.
     */
    public function wpdmpp_pages_options_save() {
        check_admin_referer( 'wpdmpp-setup' );

        if( ! get_option('_wpdmpp_settings') )
            $settings = array();
        else
            $settings = maybe_unserialize( get_option('_wpdmpp_settings') );

        $settings['page_id']                = isset( $_POST['_wpdmpp_settings']['page_id'] ) ? intval( $_POST['_wpdmpp_settings']['page_id'] ) : '';
        $settings['orders_page_id']         = isset( $_POST['_wpdmpp_settings']['orders_page_id'] ) ? intval( $_POST['_wpdmpp_settings']['orders_page_id'] ) : '';
        $settings['guest_order_page_id']    = isset( $_POST['_wpdmpp_settings']['guest_order_page_id'] ) ? intval( $_POST['_wpdmpp_settings']['guest_order_page_id'] ) : '';
        $settings['continue_shopping_url']  = isset( $_POST['_wpdmpp_settings']['continue_shopping_url'] ) ? esc_url( $_POST['_wpdmpp_settings']['continue_shopping_url'] ) : '';

        update_option('_wpdmpp_settings', $settings);

        wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
        exit;
    }


    /**
     * Payments step.
     *
     */
    public function wpdmpp_payment_options() {

        $settings = maybe_unserialize(get_option('_wpdmpp_settings'));
        ?>
        <form method="post" class="payment-step">
            <?php wp_nonce_field( 'wpdmpp-setup' ); ?>

            <input class="sw" id="paypal" type="checkbox" name="_wpdmpp_settings[Paypal][enabled]" <?php if (isset($settings['Paypal']['enabled']) && $settings['Paypal']['enabled'] == 1) echo 'checked=checked' ?> value="1">
            <label for="paypal"><span><?php esc_html_e("PayPal", "wpdm-premium-packages"); ?></span></label><br/>

            <div class="gateway-settings-paypal hidden <?php //if (isset($settings['Paypal']['enabled']) && $settings['Paypal']['enabled'] == 0) echo 'hidden'; ?>">

                <label><?php esc_html_e('PayPal Mode', 'wpdm-premium-packages'); ?></label>
                <select class='form-control' name="_wpdmpp_settings[Paypal][Paypal_mode]">
                    <option value="live" <?php selected( $settings['Paypal']['Paypal_mode'], 'live' ); ?>><?php esc_html_e('Live', 'wpdm-premium-packages'); ?></option>
                    <option value="sandbox" <?php selected( $settings['Paypal']['Paypal_mode'], 'sandbox' ); ?>><?php esc_html_e('Sandbox', 'wpdm-premium-packages'); ?></option>
                </select><br /><br />

                <label><?php esc_html_e("Title", "wpdm-premium-packages"); ?></label>
                <input type="text" class="form-control" name="_wpdmpp_settings[Paypal][title]" size="50"
                       value="<?php echo isset($settings['Paypal']['title']) && $settings['Paypal']['title'] != '' ? $settings['Paypal']['title'] : 'PayPal'; ?>"/><br />

                <label><?php esc_html_e("PayPal Email", "wpdm-premium-packages"); ?></label>
                <input type="email" class="form-control" name="_wpdmpp_settings[Paypal][Paypal_email]" size="50"
                       value="<?php echo isset($settings['Paypal']['Paypal_email']) && $settings['Paypal']['Paypal_email'] != '' ? $settings['Paypal']['Paypal_email'] : ''; ?>"/><br />

                <label><?php esc_html_e("Cancel URL", "wpdm-premium-packages"); ?></label>
                <input type="url" class="form-control" name="_wpdmpp_settings[Paypal][cancel_url]" size="50"
                       value="<?php echo isset($settings['Paypal']['cancel_url']) && $settings['Paypal']['cancel_url'] != '' ? $settings['Paypal']['cancel_url'] : ''; ?>"/><br />

                <label><?php esc_html_e("Return URL", "wpdm-premium-packages"); ?></label>
                <input type="text" class="form-control" name="_wpdmpp_settings[Paypal][return_url]" size="50"
                       value="<?php echo isset($settings['Paypal']['return_url']) && $settings['Paypal']['return_url'] != '' ? $settings['Paypal']['return_url'] : ''; ?>"/><br />

                <label><?php esc_html_e("Checkout Page Logo URL", "wpdm-premium-packages"); ?></label>
                <input type="url" class="form-control" name="_wpdmpp_settings[Paypal][Paypal_image_url]" size="50"
                       value="<?php echo isset($settings['Paypal']['Paypal_image_url']) && $settings['Paypal']['Paypal_image_url'] != '' ? $settings['Paypal']['Paypal_image_url'] : ''; ?>"/>
            </div>
            <hr />

            <input class="sw" id="testpay" type="checkbox" name="_wpdmpp_settings[TestPay][enabled]" <?php if (isset($settings['TestPay']['enabled']) && $settings['TestPay']['enabled'] == 1) echo 'checked=checked' ?> value="1">
            <label for="testpay"><span><?php esc_html_e("Test Pay", "wpdm-premium-packages"); ?></span></label><br/>
            <p class="option-note">Only for testing purpose.</p>
            <div class="gateway-settings-testpay hidden <?php //if (isset($settings['TestPay']['enabled']) && $settings['TestPay']['enabled'] == 0) echo 'hidden'; ?>">

                <label><?php esc_html_e("Title", "wpdm-premium-packages"); ?></label>
                <input type="text" class="form-control" name="_wpdmpp_settings[TestPay][title]" size="50"
                       value="<?php echo isset($settings['TestPay']['title']) && $settings['TestPay']['title'] != '' ? $settings['TestPay']['title'] : 'Test Pay'; ?>"/><br />

                <label><?php esc_html_e("Cancel URL", "wpdm-premium-packages"); ?></label>
                <input type="url" class="form-control" name="_wpdmpp_settings[TestPay][cancel_url]" size="50"
                       value="<?php echo isset($settings['TestPay']['cancel_url']) && $settings['TestPay']['cancel_url'] != '' ? $settings['TestPay']['cancel_url'] : ''; ?>"/><br />

                <label><?php esc_html_e("Return URL", "wpdm-premium-packages"); ?></label>
                <input type="url" class="form-control" name="_wpdmpp_settings[TestPay][return_url]" size="50"
                       value="<?php echo isset($settings['TestPay']['return_url']) && $settings['TestPay']['return_url'] != '' ? $settings['TestPay']['return_url'] : ''; ?>"/>
            </div>
            <hr />

            <input class="sw" id="chequepay" type="checkbox" name="_wpdmpp_settings[Cheque][enabled]" <?php if (isset($settings['Cheque']['enabled']) && $settings['Cheque']['enabled'] == 1) echo 'checked=checked' ?> value="1">
            <label for="chequepay"><span><?php esc_html_e("Pay with Cheque", "wpdm-premium-packages"); ?></span></label><br/>
            <p class="option-note">Collect payments from customers offline.</p>
            <div class="gateway-settings-chequepay hidden <?php //if (isset($settings['Cheque']['enabled']) && $settings['Cheque']['enabled'] == 0) echo 'hidden'; ?>">

                <label><?php esc_html_e("Title", "wpdm-premium-packages"); ?></label>
                <input type="text" class="form-control" name="_wpdmpp_settings[Cheque][title]" size="50"
                       value="<?php echo isset($settings['Cheque']['title']) && $settings['Cheque']['title'] != '' ? $settings['Cheque']['title'] : 'Pay with Cheque'; ?>"/>
            </div>
            <hr />

            <input class="sw" id="cashpay" type="checkbox" name="_wpdmpp_settings[Cash][enabled]" <?php if ( isset($settings['Cash']['enabled']) &&  $settings['Cash']['enabled'] == 1 ) echo "checked='checked'"; ?> value="1">
            <label for="cashpay"><span><?php esc_html_e("Pay with Cash", "wpdm-premium-packages"); ?></span></label><br/>
            <p class="option-note">Collect payments from customers offline.</p>
            <div class="gateway-settings-cashpay hidden <?php //if (isset($settings['Cash']['enabled']) && $settings['Cash']['enabled'] == 0) echo 'hidden'; ?>">

                <label><?php esc_html_e("Title", "wpdm-premium-packages"); ?></label>
                <input type="text" class="form-control" name="_wpdmpp_settings[Cash][title]" size="50"
                       value="<?php echo isset($settings['Cash']['title']) && $settings['Cash']['title'] != '' ? $settings['Cash']['title'] : 'Pay with Cash'; ?>"/>
            </div>
            <hr />

            <label><?php esc_html_e('Currency', 'wpdm-premium-packages'); ?></label>
            <?php \WPDMPP\Libs\Currencies::CurrencyListHTML(array('name'=>'_wpdmpp_settings[currency]', 'selected'=> (isset($settings['currency'])?$settings['currency']:''))); ?>
            <hr />

            <label><?php esc_html_e('Currency Sign Position', 'wpdm-premium-packages');
                $settings['currency_position'] = isset($settings['currency_position'])?$settings['currency_position']:'before';
            ?></label>
            <select class='form-control wpdmpp-currecy-position-dropdown' name="_wpdmpp_settings[currency_position]">
                <option value="before" <?php selected( $settings['currency_position'], 'before' ); ?>><?php esc_html_e('Before - $99', 'wpdm-premium-packages'); ?></option>
                <option value="after" <?php selected( $settings['currency_position'], 'after' ); ?>><?php esc_html_e('After - 99$', 'wpdm-premium-packages'); ?></option>
            </select>
            <hr />

            <p class="wpdmpp-setup-actions">
                <input type="submit" class="button button-primary" value="<?php esc_attr_e( "Continue", 'wpdm-premium-packages' ); ?>" name="save_step" />
            </p>
        </form>
        <?php
    }

    /**
     * Save Payments settings.
     */
    public function wpdmpp_payment_options_save() {
        check_admin_referer( 'wpdmpp-setup' );

        if( ! get_option('_wpdmpp_settings') )
            $settings = array();
        else
            $settings = maybe_unserialize( get_option('_wpdmpp_settings') );

        // PayPal
        $settings['Paypal']['enabled'] = isset( $_POST['_wpdmpp_settings']['Paypal']['enabled'] ) ? 1 : 0;
        $settings['Paypal']['title'] = isset($_POST['_wpdmpp_settings']['Paypal']['title']) && $_POST['_wpdmpp_settings']['Paypal']['title'] != '' ? sanitize_text_field ( $_POST['_wpdmpp_settings']['Paypal']['title'] ) : 'PayPal';
        $settings['Paypal']['cancel_url'] = isset($_POST['_wpdmpp_settings']['Paypal']['cancel_url']) ? esc_url( $_POST['_wpdmpp_settings']['Paypal']['cancel_url'] ) : '';
        $settings['Paypal']['return_url'] = isset($_POST['_wpdmpp_settings']['Paypal']['return_url']) ? esc_url( $_POST['_wpdmpp_settings']['Paypal']['return_url'] ) : '';
        $settings['Paypal']['Paypal_mode'] = isset($_POST['_wpdmpp_settings']['Paypal']['Paypal_mode']) ? sanitize_text_field( $_POST['_wpdmpp_settings']['Paypal']['Paypal_mode'] ) : 'sandbox';
        $settings['Paypal']['Paypal_email'] = isset($_POST['_wpdmpp_settings']['Paypal']['Paypal_email']) ? sanitize_email( $_POST['_wpdmpp_settings']['Paypal']['Paypal_email'] ) : '';
        $settings['Paypal']['Paypal_image_url'] = isset($_POST['_wpdmpp_settings']['Paypal']['Paypal_image_url']) ? esc_url( $_POST['_wpdmpp_settings']['Paypal']['Paypal_image_url'] ) : '';

        // TestPay
        $settings['TestPay']['enabled'] = isset( $_POST['_wpdmpp_settings']['TestPay']['enabled'] ) ? 1 : 0;
        $settings['TestPay']['title'] = isset($_POST['_wpdmpp_settings']['TestPay']['title']) && $_POST['_wpdmpp_settings']['TestPay']['title'] != '' ? sanitize_text_field( $_POST['_wpdmpp_settings']['TestPay']['title'] ) : 'Test Pay';
        $settings['TestPay']['cancel_url'] = isset($_POST['_wpdmpp_settings']['TestPay']['cancel_url']) ? esc_url( $_POST['_wpdmpp_settings']['TestPay']['cancel_url'] ) : '';
        $settings['TestPay']['return_url'] = isset($_POST['_wpdmpp_settings']['TestPay']['return_url']) ? esc_url( $_POST['_wpdmpp_settings']['TestPay']['return_url'] ) : '';

        // Cheque
        $settings['Cheque']['enabled'] = isset( $_POST['_wpdmpp_settings']['Cheque']['enabled'] ) ? 1 : 0;
        $settings['Cheque']['title'] = isset($_POST['_wpdmpp_settings']['Cheque']['title']) && $_POST['_wpdmpp_settings']['Cheque']['title'] != '' ? sanitize_text_field( $_POST['_wpdmpp_settings']['Cheque']['title'] ) : 'Pay with Cheque';

        // Cash
        $settings['Cash']['enabled'] = isset( $_POST['_wpdmpp_settings']['Cash']['enabled'] ) ? 1 : 0;
        $settings['Cash']['title'] = isset($_POST['_wpdmpp_settings']['Cash']['title']) && $_POST['_wpdmpp_settings']['Cash']['title'] != '' ? sanitize_text_field( $_POST['_wpdmpp_settings']['Cash']['title'] ) : 'Pay with Cash';

        //echo '<pre>';print_r( $_POST['_wpdmpp_settings']);echo '</pre>';
        //echo '<pre>';print_r( $settings);echo '</pre>';
        //die();

        update_option('_wpdmpp_settings', $settings);

        wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
        exit;
    }

    /**
     * Final step.
     */
    public function wpdmpp_setup_ready() {
        // We've made it! Don't prompt the user to run the wizard again.
        update_option('wpdmpp_setp_wizard_notice', 'hide');
        ?>
        <div class="setup-complete">
            <h1><?php esc_html_e( "🎉 You're ready to start selling downloads!", 'wpdm-premium-packages' ); ?></h1>
            <div class="well">
                <div class="media">
                    <div class="pull-right">
                        <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=wpdmpro' ) ); ?>" class="btn btn-info">
                            <?php esc_html_e( 'Add a download', 'wpdm-premium-packages' ); ?>
                        </a>
                    </div>
                    <div class="media-body">
                        <h3 class="cta-heading"><?php esc_html_e( 'Create your first product', 'wpdm-premium-packages' ); ?></h3>
                        <?php esc_html_e( "You're ready to add your first download.", 'wpdm-premium-packages' ); ?>
                    </div>
                </div>
            </div>

            <div class="well">
                <div class="media">
                    <div class="pull-right">
                        <a href="<?php echo esc_url( "https://www.wpdownloadmanager.com/docsfor/premium-package/" ); ?>" class="btn btn-info">
                            <?php esc_html_e( 'Documentation', 'wpdm-premium-packages' ); ?>
                        </a>
                    </div>
                    <div class="media-body">
                        <h3 class="cta-heading"><?php esc_html_e( 'Want to learn more?', 'wpdm-premium-packages' ); ?></h3>
                        <?php esc_html_e( "Read Premium Package documentation.", 'wpdm-premium-packages' ); ?>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-6">
                    <div class="well">
                        <div class="media">
                            <div class="media-body">
                                <h3 class="cta-heading"><?php esc_html_e( 'Verse', 'wpdm-premium-packages' ); ?></h3>
                                <p><i class="fa fa-gift"></i> <?php esc_html_e( "A Free Digital Shop WordPress Theme for Premium Package.", 'wpdm-premium-packages' ); ?></p>
                                <a href="<?php echo esc_url( "https://www.wpdownloadmanager.com/download/verse-wordpress-theme-for-digital-shop/" ); ?>" class="btn btn-success btn-block">
                                    <?php esc_html_e( 'Get it now!', 'wpdm-premium-packages' ); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="well">
                        <div class="media">
                            <div class="media-body">
                                <h3 class="cta-heading"><?php esc_html_e( 'Explore Free Add-ons', 'wpdm-premium-packages' ); ?></h3>
                                <p><?php esc_html_e( "There are lots of free add-ons to extend Premium Package.", 'wpdm-premium-packages' ); ?></p>
                                <a href="<?php echo esc_url( "https://www.wpdownloadmanager.com/downloads/free-add-ons/" ); ?>" class="btn btn-success btn-block">
                                    <?php esc_html_e( 'Free Add-ons!', 'wpdm-premium-packages' ); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <?php
    }
}

new Premium_Packages_Setup_Wizard();
