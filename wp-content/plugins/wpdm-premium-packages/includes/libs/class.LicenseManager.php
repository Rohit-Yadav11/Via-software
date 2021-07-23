<?php
namespace WPDMPP\Libs;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if( ! class_exists( 'LicenseManager' ) ):

    class LicenseManager{

        function __construct()
        {
            add_action( 'init', array( $this, 'getlicensekey' ) );
            add_action( 'init', array( $this, 'add_new_license' ) );
            add_action( 'init', array( $this, 'update_license' ) );
            add_action( 'init', array( $this, 'validate_license_key' ) );
            add_action( 'wp_ajax_wpdm_unlock_license', array( $this, 'unlock_license_key' ) );
            add_action( 'wp_ajax_wpdmpp_remove_domain', array( $this, 'remove_domain' ) );
        }

        public static function generate_licensekey(){
            $licenseno = strtoupper(substr(uniqid(rand()), 3, 5) . '-' . substr(uniqid(rand()), 3, 5) . '-' . substr(uniqid(rand()), 3, 5) . '-' . substr(uniqid(rand()), 3, 5));
            return $licenseno;
        }

        function unlock_license_key(){
            if(current_user_can(WPDMPP_ADMIN_CAP) && isset($_REQUEST['unlock_license']) && isset( $_POST['__suc'] ) && wp_verify_nonce( $_POST['__suc'], NONCE_KEY ) ){
                global $wpdb;
                $wpdb->update("{$wpdb->prefix}ahm_licenses", array('domain' => ''), array('id' => (int)$_REQUEST['unlock_license']));
                ob_clean();
                die('ok');
            } else die('Error!');
        }

        function remove_domain(){
            if(is_user_logged_in()){
                global $wpdb;
                $licno = wpdm_query_var('license');
                $lic = $wpdb->get_row("select * from {$wpdb->prefix}ahm_licenses where licenseno='$licno'");
                $order = new Order($lic->oid);

                //Neither a customer not an admin
                if((int)$order->uid !== (int)get_current_user_id() && !current_user_can(WPDM_ADMIN_CAP)) wp_send_json(array('success' => false, 'message' => __( "Unauthorized access", "wpdm-premium-packages" )));

                $domains = maybe_unserialize($lic->domain);
                $domain = wpdm_query_var('domain');
                $index = array_search($domain, $domains);
                if($index !== false) {
                    unset($domains[$index]);
                    foreach ($domains as $domain){
                        $_domains[] = $domain;
                    }
                    $domains = count($_domains) > 0 ? serialize($_domains) : '';
                    $wpdb->update("{$wpdb->prefix}ahm_licenses", array('domain' => $domains), array('id' => $lic->id));
                }
                ob_clean();
                wp_send_json(array('success' => true, 'message' => __( "Domain is removed", "wpdm-premium-packages" )));
            } else
                wp_send_json(array('success' => false, 'message' => __( "Unauthorized access", "wpdm-premium-packages" )));
        }


        function validate_license_key(){
            global $wpdb;
            //print_r($_REQUEST);die('ok');
            if(wpdm_query_var('wpdmLicense') === 'validate') {

                $licenseKey = wpdm_query_var('licenseKey');
                $domain = wpdm_query_var('domain');

                if(!$licenseKey || !$domain) wp_send_json(array('status' => 'INVALID', 'error' => 'REQUEST_INVALID', 'download_url' => '', 'request' => $_REQUEST));

                $productId = wpdm_query_var('productId');
                $license = $wpdb->get_row("select * from {$wpdb->prefix}ahm_licenses where licenseno = '$licenseKey'");
                //wp_send_json($license);
                $activation_date = $license->activation_date;
                $activation_date = (int)$activation_date > 0 ? $activation_date : time();
                if ($license) {
                    $domains = maybe_unserialize($license->domain);
                    if (!is_array($domains)) $domains = array();
                    if ($license->oid !== '') {
                        $order = $wpdb->get_row("select * from {$wpdb->prefix}ahm_orders where order_id='$license->oid'");

                        if (!$order || ($order->order_id != '' && !in_array($order->order_status, array('Completed', 'Expired', 'Gifted')))) {

                            wp_send_json(array('status' => 'INVALID', 'error' => 'ORDER_ISSUE', 'download_url' => ''));

                        }
                    }

                    //if($license->status == 0) $validity = array('status' => 'INACTIVE', 'error' => 'NOT_ACTIVE');

                    $productCode = esc_attr(get_post_meta($license->pid, '__wpdm_product_code', true));

                    //if ($productCode !== '' && $productId !== $productCode) wp_send_json(array('status' => 'INVALID', 'error' => 'INVALID_PRODUCT'));

                    if (count($domains) >= $license->domain_limit && $license->domain_limit > 0 && !in_array($domain, $domains)) $validity = array('status' => 'INVALID', 'error' => 'USAGE_LIMIT_REACHED');
                    else if ((count($domains) < $license->domain_limit || $license->domain_limit == 0) && !in_array($domain, $domains)) {
                        $domains[] = $domain;
                        $wpdb->update("{$wpdb->prefix}ahm_licenses", array('domain' => serialize($domains), 'activation_date' => $activation_date), array('id' => $license->id));

                        $validity = array('status' => 'VALID', 'expire_date' => $license->expire_date, 'activation_date' => $license->activation_date, 'order_status' => $order->order_status, 'order_id' => $order->order_id);
                    } else if (in_array($domain, $domains)) {
                        $status = ($license->expire_date > time() || $license->expire_date == 0) ? 'VALID' : 'EXPIRED';
                        $validity = array('status' => $status, 'expire_date' => $license->expire_date, 'activation_date' => $license->activation_date, 'order_status' => $order->order_status, 'order_id' => $order->order_id);
                    } else {
                        $validity = array('status' => 'INVALID', 'error' => 'USAGE_LIMIT_REACHED');
                    }
                } else {
                    $validity = array('status' => 'INVALID', 'error' => 'LICENSE_KEY_NOT_FOUND');
                }
                if ($validity['status'] === 'VALID') {
                    $download_url = '';
                    if (is_object($order)) {
                        if ($order->order_status === 'Completed') {
                            $files = get_post_meta($license->pid, '__wpdm_files', true);
                            if(is_array($files)) {
                                foreach ($files as $index => $file) {
                                    $download_url = \WPDMPP\WPDMPremiumPackage::customerDownloadURL($license->pid, $license->oid, array('domain' => $domain)) . "&ind={$index}";  //home_url("/?wpdmdl={$license->pid}&oid={$license->oid}&ind=" . $index);
                                    break;
                                }
                            }
                        }
                        $validity['download_url'] = $download_url;
                        $validity['expire'] = $order->expire_date;
                    }
                }
                wp_send_json($validity);
            }
        }

        function add_new_license(){
            if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'addlicense' && current_user_can(WPDMPP_ADMIN_CAP) && isset( $_POST['__suc'] ) && wp_verify_nonce( $_POST['__suc'], NONCE_KEY ) ){
                global $wpdb;
                $license = $_REQUEST['license'];
                if(trim($license['domain']) != '') {
                    $license['domain'] = str_replace("\r", "", esc_attr($license['domain']));
                    $license['domain'] = explode("\n", $license['domain']);
                    $license['domain'] = array_unique($license['domain']);
                    $license['domain'] = serialize($license['domain']);
                }
                $license['activation_date'] = strtotime($license['activation_date']);
                if($license['expire_date'] != '')
                    $license['expire_date'] = strtotime($license['expire_date']);
                else
                    $license['expire_date'] = 0;
                $wpdb->insert("{$wpdb->prefix}ahm_licenses", $license);
                header("location: edit.php?post_type=wpdmpro&page=pp-license");
                die();
            }
        }

        function update_license(){
            if(isset($_REQUEST['do']) && $_REQUEST['do'] == 'updatelicense' && current_user_can(WPDMPP_ADMIN_CAP) && isset( $_POST['__suc'] ) && wp_verify_nonce( $_POST['__suc'], NONCE_KEY ) ){
                global $wpdb;
                $license = $_REQUEST['license'];
                if(trim($license['domain']) != '') {
                    $license['domain'] = str_replace("\r", "", sanitize_textarea_field($license['domain']));
                    $license['domain'] = explode("\n", $license['domain']);
                    $license['domain'] = array_unique($license['domain']);
                    $license['domain'] = serialize($license['domain']);
                }
                $license['activation_date'] = strtotime($license['activation_date']);
                if($license['expire_date'] != '')
                    $license['expire_date'] = strtotime($license['expire_date']);
                else
                    $license['expire_date'] = 0;
                $wpdb->update("{$wpdb->prefix}ahm_licenses", $license, array('id' => (int)$_REQUEST['id']));
                header("location: edit.php?post_type=wpdmpro&page=pp-license");
                die();
            }
        }

        function getlicensekey()
        {
            if (!isset($_REQUEST['execute']) || $_REQUEST['execute'] != 'getlicensekey' || !is_user_logged_in()) return;
            global $wpdb, $current_user;
            $oid = sanitize_text_field($_REQUEST['orderid']);
            $pid = intval($_REQUEST['fileid']);
            $order = new \WPDMPP\Libs\Order();
            $odata = $order->GetOrder($oid);
            $items = unserialize($odata->items);

            if (in_array($pid, $items) && $odata->order_status == 'Completed' &&  ( $current_user->ID == $odata->uid || current_user_can(WPDM_ADMIN_CAP) )) {
                $licenseinfo = $wpdb->get_row("select * from {$wpdb->prefix}ahm_licenses where oid='{$oid}' and pid='{$pid}'");
                if (!$licenseinfo) {
                    $_license = $wpdb->get_var("select license from {$wpdb->prefix}ahm_order_items where oid='{$oid}' and pid='{$pid}'");
                    $_license = maybe_unserialize($_license);
                    $limit = 1;
                    if(isset($_license, $_license['info'], $_license['info']['use']))
                        $limit = $_license['info']['use'];

                    $licenseno = self::generate_licensekey();

                    $wpdb->insert("{$wpdb->prefix}ahm_licenses", array('licenseno' => $licenseno, 'domain_limit' => $limit, 'status' => 1, 'oid' => $oid, 'pid' => $pid));

                    wp_send_json(array('key' => $licenseno, 'domains' => array()));
                    //die($licenseno);
                } else {
                    $domains = maybe_unserialize($licenseinfo->domain);
                    $domains = is_array($domains)?$domains:array();
                    wp_send_json(array('key' => $licenseinfo->licenseno, 'domains' => $domains));
                    //die($licenseno);
                }

            } else die('error!');
        }

        function wpdm_pp_add_domain()
        {
            if (!$_POST || !$_GET['id']) return;
            global $current_user, $wpdb;

            $order = new \WPDMPP\Libs\Order();
            $item = (int)$_GET['item'];
            $oid = sanitize_text_field($_GET['id']);
            $ord = $order->GetOrder($oid);
            $cart_data = unserialize($ord->cart_data);
            $mxd = $cart_data[$item] ? $cart_data[$item] : 1;
            if ($ord->uid != $current_user->ID || $_POST['domain'] == '' || !$current_user->ID || $ord->uid == '') return false;

            $lic = $wpdb->get_row("select * from {$wpdb->prefix}ahm_licenses where oid='$oid' and pid='$item'");

            $domain = is_array(unserialize($lic->domain)) ? unserialize($lic->domain) : array($lic->domain);
            $licenseno = self::generate_licensekey();
            if (count($domain) == 1 && $domain[0] == '') $domain = array();

            if (count($domain) < $mxd) {
                $domain[] = str_replace(array("http://", "https://", "www."), "", strtolower(sanitize_text_field($_POST['domain'])));
                $domain = array_unique($domain);

                if ($lic->id > 0)
                    $wpdb->update("{$wpdb->prefix}ahm_licenses", array('domain' => serialize($domain)), array('oid' => $oid, 'pid' => $item));
                else
                    $wpdb->insert("{$wpdb->prefix}ahm_licenses", array('domain' => serialize($domain), 'licenseno' => $licenseno, 'oid' => $oid, 'pid' => $item));
            }

            header("location: {$_SERVER['HTTP_REFERER']}");
            die();
        }



    }

endif;

new LicenseManager();
