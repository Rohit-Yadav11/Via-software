<?php
/**
 * Template for displaying active Name and Email input fields cart checkout page.
 *
 * This template can be overridden by copying it to yourtheme/download-manager/checkout-cart/checkout-name-email.php.
 *
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<div class="panel panel-default">
    <div class="panel-heading"><strong><?php echo __("Please Enter Your Name & Email", "wpdm-premium-packages"); ?></strong></div>
    <div class="panel-body">
        <!-- full-name input-->

        <div class="form-group">
            <div class="controls row">
                <div class="col-md-6">
                    <label class="control-label text-small"><?php echo __("First Name", "wpdm-premium-packages"); ?> <span class="required" title="<?php _e('Required', 'wpdm-premium-packages'); ?>">*</span></label>
                    <input id="f-name" value="<?php echo $billing['first_name']; ?>" name="billing[first_name]" required="required" type="text" placeholder="<?php echo __("First Name", "wpdm-premium-packages"); ?>" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="control-label text-small"><?php echo __("Last Name", "wpdm-premium-packages"); ?> <span class="required" title="<?php _e('Required', 'wpdm-premium-packages'); ?>">*</span></label>
                    <input id="l-name" value="<?php echo $billing['last_name']; ?>" name="billing[last_name]" type="text" required="required" placeholder="<?php echo __("Last Name", "wpdm-premium-packages"); ?>" class="form-control">
                </div>
            </div>
        </div>
        <div class="form-group mb-0">
            <label class="control-label text-small"><?php echo __("Order Notification Email", "wpdm-premium-packages"); ?> <span class="required" title="<?php _e('Required', 'wpdm-premium-packages'); ?>">*</span></label>
            <input type="email" placeholder="To receive order confirmation mail" value="<?php echo $billing['email']; ?>" required="required" class="form-control" name="billing[order_email]" id="email_m">
        </div>

    </div>
</div>
