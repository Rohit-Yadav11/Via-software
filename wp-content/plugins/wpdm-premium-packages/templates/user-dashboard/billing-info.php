<?php
/**
 * Template for User Dashboard >> Edit Profile >> Billing Info Form ( Hooked to Edit Profile using wpdm_edit_profile_form WPDM action )
 *
 * Template Partial for [wpdm-pp-edit-profile] shortocode
 *
 * This template can be overridden by copying it to yourtheme/download-manager/user-dashboard/billing-info.php.
 *
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<input type="hidden" name="__upnonce" value="<?php wp_create_nonce(NONCE_KEY); ?>"/>
<div class="card card-default dashboard-card">
    <div class="card-header"><?php _e('Billing Address', 'wpdm-premium-packages'); ?></div>
    <div class="card-body">

        <div class="row row-fluid">
            <div class="form-group col-md-6 ">
                <label class="" for="billing_first_name"><?php _e("First Name", "wpdm-premium-packages"); ?> <i class="fa fa-star text-danger ttip"
                                                                                                               title="<?php _e('Required', 'wpdm-premium-packages'); ?>"></i></label>
                <input type="text" value="<?php if (isset($billing['first_name'])) echo esc_attr($billing['first_name']); ?>"
                       placeholder="<?php _e("First Name", "wpdm-premium-packages"); ?>" id="billing_first_name" name="checkout[billing][first_name]"
                       class="form-control" required="required">

            </div>
            <div class="form-group col-md-6 ">
                <label class="" for="billing_last_name"><?php _e("Last Name", "wpdm-premium-packages"); ?> <i class="fa fa-star text-danger ttip"
                                                                                                             title="<?php _e('Required', 'wpdm-premium-packages'); ?>"></i></label>
                <input type="text" value="<?php if (isset($billing['last_name'])) echo esc_attr($billing['last_name']); ?>"
                       placeholder="<?php _e("Last Name", "wpdm-premium-packages"); ?>" id="billing_last_name" name="checkout[billing][last_name]"
                       class="form-control" required="required">

            </div>
        </div>

        <div class="row row-fluid">
            <div class="form-group col-md-6 ">
                <label class="" for="billing_company"><?php _e("Company Name", "wpdm-premium-packages"); ?></label>
                <input type="text" value="<?php if (isset($billing['company'])) echo esc_attr($billing['company']); ?>"
                       placeholder="<?php _e("Company (optional)", "wpdm-premium-packages"); ?>" id="billing_company" name="checkout[billing][company]"
                       class="input-text  form-control">
            </div>
            <div class="form-group col-md-6 ">
                <label class="" for="billing_address_1"><?php _e("Address Line 1", "wpdm-premium-packages"); ?> <i class="fa fa-star text-danger ttip"
                                                                                                                  title="<?php _e('Required', 'wpdm-premium-packages'); ?>"></i></label>
                <input type="text" value="<?php if (isset($billing['address_1'])) echo esc_attr($billing['address_1']); ?>"
                       placeholder="<?php _e("Address", "wpdm-premium-packages"); ?>" id="billing_address_1" name="checkout[billing][address_1]"
                       class="form-control" required="required">

            </div>
        </div>

        <div class="row row-fluid">
            <div class="form-group col-md-6 ">
                <label class="" for="billing_address_2"><?php _e("Address Line 2", "wpdm-premium-packages"); ?></label>
                <input type="text" value="<?php if (isset($billing['address_2'])) echo esc_attr($billing['address_2']); ?>"
                       placeholder="<?php _e("Address 2 (optional)", "wpdm-premium-packages"); ?>" id="billing_address_2"
                       name="checkout[billing][address_2]" class="input-text  form-control">
            </div>
            <div class="form-group col-md-6 ">
                <label class="" for="billing_city"><?php _e("Town/City", "wpdm-premium-packages"); ?> <i class="fa fa-star text-danger ttip"
                                                                                                        title="<?php _e('Required', 'wpdm-premium-packages'); ?>"></i></label>
                <input type="text" value="<?php if (isset($billing['city'])) echo esc_attr($billing['city']); ?>"
                       placeholder="<?php _e("Town/City", "wpdm-premium-packages"); ?>" id="billing_city" name="checkout[billing][city]"
                       class="form-control" required="required">

            </div>
        </div>

        <div class="row row-fluid">
            <div class="form-group col-md-6 ">
                <label class="" for="billing_postcode"><?php _e("Postcode/Zip", "wpdm-premium-packages"); ?> <i class="fa fa-star text-danger ttip"
                                                                                                               title="<?php _e('Required', 'wpdm-premium-packages'); ?>"></i></label>
                <input type="text" value="<?php if (isset($billing['postcode'])) echo esc_attr($billing['postcode']); ?>"
                       placeholder="<?php _e("Postcode/Zip", "wpdm-premium-packages"); ?>" id="billing_postcode" name="checkout[billing][postcode]"
                       class="form-control" required="required">

            </div>
            <div class="form-group col-md-6 ">
                <label class="" for="billing_country"><?php _e("Country", "wpdm-premium-packages"); ?> <i class="fa fa-star text-danger ttip"
                                                                                                         title="<?php _e('Required', 'wpdm-premium-packages'); ?>"></i></label>
                <?php
                $countries = wpdmpp_get_countries();
                $allowed_countries = ( isset($wpdmpp_settings['allow_country'] ) ) ? $wpdmpp_settings['allow_country'] : array();
                ?>
                <select class="required wpdm-custom-select  form-control" id="billing_country" name="checkout[billing][country]" required="required">
                    <option value=""><?php _e('--Select Country--', 'wpdm-premium-packages'); ?></option>
                    <?php
                    foreach ($countries as $country) {
                        if ( ! empty( $allowed_countries ) ) {
                            if( in_array( $country->country_code, $allowed_countries ) )
                                echo '<option value="' . $country->country_code . '"' . selected( $billing['country'], $country->country_code, false ) . '>' . $country->country_name . '</option>';
                        } else {
                            echo '<option value="' . $country->country_code . '" ' . selected( $billing['country'], $country->country_code, false ) . '>' . $country->country_name . '</option>';
                        }
                    }
                    ?>
                </select>

            </div>
        </div>

        <div class="row row-fluid">
            <div class="form-group col-md-6 ">
                <label class="" for="billing_state"><?php _e("State/County", "wpdm-premium-packages"); ?> <i class="fa fa-star text-danger ttip"
                                                                                                            title="<?php _e('Required', 'wpdm-premium-packages'); ?>"></i></label>
                <input type="text" id="billing_state" name="checkout[billing][state]" placeholder="<?php _e("State/County", "wpdm-premium-packages"); ?>"
                       value="<?php if (isset($billing['state'])) echo $billing['state']; ?>" class="form-control" required="required">

            </div>
            <div class="form-group col-md-6 ">
                <label class="" for="billing_email"><?php _e("Email Address", "wpdm-premium-packages"); ?> <i class="fa fa-star text-danger ttip"
                                                                                                             title="<?php _e('Required', 'wpdm-premium-packages'); ?>"></i></label>
                <input type="text" value="<?php if (isset($billing['email'])) echo $billing['email']; ?>"
                       placeholder="<?php _e("Email Address", "wpdm-premium-packages"); ?>" id="billing_email" name="checkout[billing][email]"
                       class="input-text required email  form-control" required="required">

            </div>
        </div>

        <div class="row row-fluid">
            <div class="form-group col-md-6 ">
                <label class="" for="billing_phone"><?php _e("Phone", "wpdm-premium-packages"); ?></label>
                <input type="text" value="<?php if (isset($billing['phone'])) echo $billing['phone']; ?>"
                       placeholder="<?php _e("Phone", "wpdm-premium-packages"); ?>" id="billing_phone" name="checkout[billing][phone]"
                       class="input-text form-control">

            </div>
            <div class="form-group col-md-6 ">
                <label class="" for="billing_tin"><?php _e("Tax ID #", "wpdm-premium-packages"); ?></label>
                <input type="text" value="<?php if (isset($billing['taxid'])) echo $billing['taxid']; ?>"
                       placeholder="<?php _e("Tax ID", "wpdm-premium-packages"); ?>" id="billing_tin" name="checkout[billing][taxid]"
                       class="form-control">

            </div>
        </div>

    </div>
</div>
