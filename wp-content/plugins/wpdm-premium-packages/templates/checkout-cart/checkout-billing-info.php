<?php
/**
 * Template for displaying Billing Info Form in cart checkout page.
 *
 * This template can be overridden by copying it to yourtheme/download-manager/checkout-cart/checkout-billing-info.php.
 *
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<div class="card mt-3">
    <div class="card-header"><?php echo __("Billing Address", "wpdm-premium-packages"); ?></div>
    <div class="card-body">
        <!-- full-name input-->
        <div class="form-group">

            <div class="controls row">
                <div class="col-md-6">
                    <label class="control-label"><?php echo __("First Name", "wpdm-premium-packages"); ?> <span class="required" title="<?php _e('Required', 'wpdm-premium-packages'); ?>">*</span></label>
                    <input id="f-name" value="<?php echo $billing['first_name']; ?>" name="billing[first_name]" required="required" type="text" placeholder="<?php echo __("First Name", "wpdm-premium-packages"); ?>" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="control-label"><?php echo __("Last Name", "wpdm-premium-packages"); ?> <span class="required" title="<?php _e('Required', 'wpdm-premium-packages'); ?>">*</span></label>
                    <input id="l-name" value="<?php echo $billing['last_name']; ?>" name="billing[last_name]" type="text" required="required" placeholder="<?php echo __("Last Name", "wpdm-premium-packages"); ?>" class="form-control">
                </div>
            </div>
        </div>
        <!-- company name input-->
        <div class="form-group">
            <label class="control-label"><?php echo __("Company Name", "wpdm-premium-packages"); ?></label>
            <div class="controls">
                <input id="address-line1" value="<?php echo $billing['company']; ?>" name="billing[company]" type="text" placeholder="<?php echo __("(Optional)", "wpdm-premium-packages"); ?>" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label class="control-label"><?php echo __("Country", "wpdm-premium-packages"); ?> <span class="required" title="<?php _e('Required', 'wpdm-premium-packages'); ?>">*</span></label>
                    <div class="controls">
                        <?php
                        $allowed_countries = get_wpdmpp_option('allow_country');
                        $all_countries = wpdmpp_countries();
                        ?>
                        <select id="country" name="billing[country]" required="required" class="custom-select wpdm-custom-select form-control <?php echo wpdmpp_tax_active() ? 'calculate-tax' : ''; ?>" data-live-search="true" x-moz-errormessage="<?php echo __("Please Select Your Country", "wpdm-premium-packages"); ?>">
                            <option value=""><?php echo __("--Select Country--", "wpdm-premium-packages"); ?></option>
                            <?php foreach ($allowed_countries as $country_code) { ?>
                                <option value="<?php echo $country_code; ?>" <?php selected( $billing['country'], $country_code, true ); ?>><?php echo $all_countries[$country_code]; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="control-label"><?php echo __("State / Province", "wpdm-premium-packages"); ?> <span class="required" title="<?php _e('Required', 'wpdm-premium-packages'); ?>">*</span></label>
                    <div class="controls">
                        <select id="region" name="billing[state]" type="text" class="custom-select wpdm-custom-select form-control <?php echo wpdmpp_tax_active() ? 'calculate-tax' : ''; ?>"></select>
                        <input id="region-txt" style="display:none;" name="billing[state]" value="<?php echo $billing['state']; ?>" type="text" placeholder="<?php echo __("state / province / region", "wpdm-premium-packages"); ?>" class="form-control <?php echo wpdmpp_tax_active() ? 'calculate-tax' : ''; ?>">
                        <p class="help-block"></p>
                    </div>
                </div>
            </div>

        </div>
        <!-- address-line1 input-->
        <div class="form-group">
            <label class="control-label"><?php echo __("Address Line 1", "wpdm-premium-packages"); ?> <span class="required" title="<?php _e('Required', 'wpdm-premium-packages'); ?>">*</span></label>
            <div class="controls">
                <input id="address-line1" name="billing[address_1]" value="<?php echo $billing['address_1']; ?>" type="text" required="required" placeholder="<?php echo __("address line 1", "wpdm-premium-packages"); ?>" class="form-control">

            </div>
        </div>
        <!-- address-line2 input-->
        <div class="form-group">
            <label class="control-label"><?php echo __("Address Line 2", "wpdm-premium-packages"); ?></label>
            <div class="controls">
                <input id="address-line2" name="billing[address_2]" value="<?php echo $billing['address_2']; ?>" type="text" placeholder="<?php echo __("address line 2", "wpdm-premium-packages"); ?>" class="form-control">
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <!-- city input-->
                <div class="col-md-6">
                    <label class="control-label"><?php echo __("City / Town", "wpdm-premium-packages"); ?> <span class="required" title="<?php _e('Required', 'wpdm-premium-packages'); ?>">*</span></label>
                    <div class="controls">
                        <input id="city" value="<?php echo $billing['city']; ?>" name="billing[city]" type="text" required="required" placeholder="<?php echo __("city", "wpdm-premium-packages"); ?>" class="form-control">
                        <p class="help-block"></p>
                    </div>
                </div>

                <!-- postal-code input-->
                <div class="col-md-6">
                    <label class="control-label"><?php echo __("Zip / Postal Code", "wpdm-premium-packages"); ?> <span class="required" title="<?php _e('Required', 'wpdm-premium-packages'); ?>">*</span></label>
                    <div class="controls">
                        <input id="postal-code" name="billing[postcode]" value="<?php echo $billing['postcode']; ?>" type="text" required="required" placeholder="<?php echo __("zip or postal code", "wpdm-premium-packages"); ?>" class="form-control">
                        <p class="help-block"></p>
                    </div>
                </div>
            </div>
        </div>
        <!-- country select -->
        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label><?php echo __("Phone", "wpdm-premium-packages"); ?></label>
                    <input type="tel" value="<?php echo $billing['phone']; ?>" class="form-control" name="billing[phone]" id="phone_m" placeholder="<?php echo __("Valid Phone Number", "wpdm-premium-packages"); ?>">
                </div>
                <div class="col-md-6">
                    <label><?php echo __("Enter Order Notification Email", "wpdm-premium-packages"); ?> <span class="required" title="<?php _e('Required', 'wpdm-premium-packages'); ?>">*</span></label>
                    <input type="email" value="<?php echo $billing['email']; ?>" required="required" class="form-control" name="billing[order_email]" id="email_m" placeholder="<?php echo __("Enter Order Notification Email", "wpdm-premium-packages"); ?>">
                </div>
            </div>
        </div>
    </div>
</div>
