<?php
/**
 * Template for billing info dialog guest orders page
 *
 * This template can be overridden by copying it to yourtheme/download-manager/partials/guest-order-billing-info.php.
 *
 * @version     1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="modal fade" tabindex="-1" role="dialog" id="billing-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="billing-info-form" method="post">
                <div class="modal-header">
                    <h4 class="modal-title"><strong><?php _e("Edit Billing Info", "wpdm-premium-packages"); ?></strong></h4>
                </div>
                <div class="modal-body">

                    <?php
                    $billing = $sbilling = array
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
                        'phone' => '',
                        'taxid' => ''
                    );
                    $sbilling = unserialize($order->billing_info);
                    $billing = shortcode_atts($billing, $sbilling);
                    ?>
                    <!-- full-name input-->
                    <div class="form-group">
                        <div class="controls row">
                            <div class="col-md-6">
                                <label class="control-label"><?php echo __("First Name", "wpdm-premium-packages"); ?>
                                    <span class="required" title="<?php _e('Required', 'wpdm-premium-packages'); ?>">*</span></label>
                                <input id="f-name" value="<?php echo $billing['first_name']; ?>" name="billing[first_name]" required="required" type="text"
                                       placeholder="<?php echo __("First Name", "wpdm-premium-packages"); ?>" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="control-label"><?php echo __("Last Name", "wpdm-premium-packages"); ?>
                                    <span class="required" title="<?php _e('Required', 'wpdm-premium-packages'); ?>">*</span></label>
                                <input id="l-name" value="<?php echo $billing['last_name']; ?>" name="billing[last_name]" type="text" required="required"
                                       placeholder="<?php echo __("Last Name", "wpdm-premium-packages"); ?>" class="form-control">
                            </div>
                        </div>
                    </div>
                    <!-- company name input-->
                    <div class="form-group">
                        <label class="control-label"><?php echo __("Company Name", "wpdm-premium-packages"); ?></label>
                        <div class="controls">
                            <input id="address-line1" value="<?php echo $billing['company']; ?>" name="billing[company]" type="text" placeholder="<?php echo __("Company (optional)", "wpdm-premium-packages"); ?>" class="form-control">
                        </div>
                    </div>
                    <!-- address-line1 input-->
                    <div class="form-group">
                        <label class="control-label"><?php echo __("Address Line 1", "wpdm-premium-packages"); ?> <span class="required" title="<?php _e('Required', 'wpdm-premium-packages'); ?>">*</span></label>
                        <div class="controls">
                            <input id="address-line1" name="billing[address_1]" value="<?php echo $billing['address_1']; ?>" type="text" required="required"
                                   placeholder="<?php echo __("address line 1", "wpdm-premium-packages"); ?>" class="form-control">

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
                            <!-- postal-code input-->
                            <div class="col-md-6">
                                <label class="control-label"><?php echo __("Postcode/Zip", "wpdm-premium-packages"); ?>
                                    <span class="required" title="<?php _e('Required', 'wpdm-premium-packages'); ?>">*</span></label>
                                <div class="controls">
                                    <input id="postal-code" name="billing[postcode]" value="<?php echo $billing['postcode']; ?>" type="text"
                                           required="required" placeholder="<?php echo __("Postcode/Zip", "wpdm-premium-packages"); ?>"
                                           class="form-control">
                                    <p class="help-block"></p>
                                </div>
                            </div>
                            <!-- city input-->
                            <div class="col-md-6">
                                <label class="control-label"><?php echo __("Town/City", "wpdm-premium-packages"); ?> <span class="required" title="<?php _e('Required', 'wpdm-premium-packages'); ?>">*</span></label>
                                <div class="controls">
                                    <input id="city" value="<?php echo $billing['city']; ?>" name="billing[city]" type="text" required="required"
                                           placeholder="<?php echo __("Town/City", "wpdm-premium-packages"); ?>" class="form-control">
                                    <p class="help-block"></p>
                                </div>
                            </div>
                            <!-- region input-->
                            <div class="col-md-6">
                                <label class="control-label"><?php echo __("State/County", "wpdm-premium-packages"); ?>
                                    <span class="required" title="<?php _e('Required', 'wpdm-premium-packages'); ?>">*</span></label>
                                <div class="controls">
                                    <input id="region" name="billing[state]" value="<?php echo $billing['state']; ?>" type="text"
                                           placeholder="<?php echo __("State/County", "wpdm-premium-packages"); ?>"
                                           class="form-control">
                                    <p class="help-block"></p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="control-label"><?php echo __("Country", "wpdm-premium-packages"); ?>
                                    <span class="required" title="<?php _e('Required', 'wpdm-premium-packages'); ?>">*</span></label>
                                <div class="controls">
                                    <?php
                                    $allowed_countries = get_wpdmpp_option('allow_country');
                                    $all_countries = wpdmpp_countries();
                                    ?>
                                    <select id="country" name="billing[country]" required="required" class="form-control" data-live-search="true"
                                            x-moz-errormessage="<?php echo __("Please Select Your Country", "wpdm-premium-packages"); ?>">
                                        <option value=""><?php echo __("--Select Country--", "wpdm-premium-packages"); ?></option>
                                        <?php
                                        foreach ($allowed_countries as $country_code) {

                                            if($billing['country'] == $country_code) {
                                                $selected = ' selected="selected"';}
                                            else {
                                                $selected = "";
                                            }
                                            ?>
                                            <option value="<?php echo $country_code; ?>" <?php echo $selected; ?>><?php echo $all_countries[$country_code]; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- country select -->
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label" for="billing_phone"><?php _e("Phone", "wpdm-premium-packages"); ?></label>
                                <input type="text" value="<?php if (isset($billing['phone'])) echo $billing['phone']; ?>"
                                       data-placeholder="<?php _e("Phone", "wpdm-premium-packages"); ?>" id="billing_phone" name="billing[phone]"
                                       class="input-text required  form-control">
                                <span class="error help-block"></span>
                            </div>
                            <div class="col-md-6">
                                <label class="control-label" for="billing_tin"><?php _e("Tax ID #", "wpdm-premium-packages"); ?></label>
                                <input type="text" value="<?php if (isset($billing['taxid'])) echo $billing['taxid']; ?>"
                                       data-placeholder="<?php _e("Tax ID", "wpdm-premium-packages"); ?>" id="billing_tin" name="billing[taxid]"
                                       class="input-text required  form-control">
                                <span class="error help-block"></span>
                            </div>

                            <div class="col-md-6">
                                <label><?php echo __("Email Address", "wpdm-premium-packages"); ?> <span class="required" title="<?php _e('Required', 'wpdm-premium-packages'); ?>">*</span></label>
                                <input type="email" value="<?php echo $billing['order_email']; ?>" required="required" class="form-control" name="billing[order_email]" id="email_m" placeholder="<?php echo __("Email Address", "wpdm-premium-packages"); ?>">
                            </div>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e("Close", "wpdm-premium-packages"); ?></button>
                    <button type="submit" class="btn btn-primary"><?php _e("Save Changes", "wpdm-premium-packages"); ?></button>
                    <span id="bists" class="pull-left" style="display: none"></span>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    jQuery(function($){
        $('#billing-info-form').submit(function(){
            $('#bists').html('<i class="fa fa-spin fa-refresh"></i> Saving...').fadeIn();
            $(this).ajaxSubmit({
                url: '<?php echo admin_url('admin-ajax.php?action=update_guest_billing'); ?>',
                success: function(res){
                    $('#bists').html('<i class="fa fa-check-circle"></i> '+res);
                    $('#billing-modal').modal('hide');
                }
            });
            return false;
        });
    });
</script>