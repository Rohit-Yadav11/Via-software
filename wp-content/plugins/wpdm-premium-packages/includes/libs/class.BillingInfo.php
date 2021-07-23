<?php
/**
 * Display / Edit Billing Info in WordPress's user profile page.
 *
 * @version     2.0.0
 */

namespace WPDMPP\Libs;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('\WPDMPP\Libs\BillingInfo')):

    class BillingInfo
    {

        function __construct()
        {
            add_action('show_user_profile', array($this, 'addFields'));
            add_action('edit_user_profile', array($this, 'addFields'));
            add_action('personal_options_update', array($this, 'save'));
            add_action('edit_user_profile_update', array($this, 'save'));
            add_action('wpdm_edit_profile_form', array($this, 'addFieldsFrontend'));
            add_action('wpdm_update_profile', array($this, 'saveFrontend'));

        }

        static function get($user_ID)
        {
            $billing = maybe_unserialize(get_user_meta($user_ID, 'user_billing_shipping', true));
            $billing_defaults =
                [
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
                    'phone' => '',
                    'taxid' => ''
                ];
            $billing = wpdm_valueof($billing, 'billing', ['default' => $billing_defaults]);
            $user = get_user_by('id', $user_ID);
            if (!$user) return $billing_defaults;
            $billing_defaults['first_name'] = $user->first_name;
            $billing_defaults['last_name'] = $user->last_name;
            $billing_defaults['email'] = $user->user_email;
            if (!is_array($billing)) $billing = [];
            foreach ($billing_defaults as $field => $value) {
                $billing[$field] = wpdm_valueof($billing, $field) == '' ? $billing_defaults[$field] : $billing[$field];
            }
            return $billing;
        }

        function addFieldsFrontend()
        {
            global $current_user;
            $billing = $this::get($current_user->ID);
            $store = maybe_unserialize(get_user_meta(get_current_user_id(), '__wpdm_store', true));
            include wpdm_tpl_path('user-dashboard/billing-info.php', WPDMPP_TPL_DIR);
        }

        function addFields($user)
        {
            global $wpdmpp_settings;
            $billing = $this::get($user->ID);
            ob_start();
            echo "<div class='w3eden' style='width: 800px;max-width: 100%'>";
            include wpdm_tpl_path('user-dashboard/billing-info.php', WPDMPP_BASE_DIR.'templates3');
            echo "</div>";
            $data = ob_get_clean();
            echo apply_filters('wpdmpp_add_billing_info_fields', $data, $user);
        }

        function save($user_id)
        {
            if (current_user_can('edit_user', $user_id)) {
                $codata = wpdm_sanitize_array($_POST['checkout']);
                update_user_meta($user_id, 'user_billing_shipping', $codata);
                if (current_user_can(WPDMPP_ADMIN_CAP)) {
                    if (isset($_REQUEST['wpdmpp_customer']))
                        User::addCustomer($user_id);
                    else
                        User::removeCustomer($user_id);
                }
            }
        }

        function saveFrontend()
        {
            $codata = wpdm_sanitize_array($_POST['checkout']);
            $user_id = get_current_user_id();
            if($user_id) {
                update_user_meta($user_id, 'user_billing_shipping', $codata);
            }
        }

    }

endif;

new BillingInfo();
