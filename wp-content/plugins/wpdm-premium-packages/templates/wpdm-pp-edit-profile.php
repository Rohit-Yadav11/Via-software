<?php
/**
 * Template for [wpdm-pp-edit-profile] shortocode. Includes "/templates/user-dashboard/billing-info.php" sub-template.
 *
 * This template can be overridden by copying it to yourtheme/download-manager/wpdm-pp-edit-profile.php.
 *
 * @version     1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $current_user, $wpdb;
$user               = $wpdb->get_row("select * from {$wpdb->prefix}users where ID=" . $current_user->ID);
$billing_shipping   = unserialize(get_user_meta($current_user->ID, 'user_billing_shipping', true));

if ( ! isset( $settings ) )
    $settings = array();

if ( is_array( $billing_shipping ) )
    extract( $billing_shipping );
?>
<div class="w3eden wpdm-edit-profile">
    <ul style="display: block; list-style: none;">
        <?php if (\WPDM\Session::get('member_error')) { ?>
            <li class="col-md-11">
                <div class="alert alert-warning">
                    <b><?php _e('Save Failed!', 'wpdm-premium-packages'); ?></b><br/>
                    <?php
                    echo implode('<br/>', \WPDM\Session::get('member_error'));
                    \WPDM\Session::clear('member_error');
                    ?>
                </div>
            </li>
        <?php } ?>
        <?php if (\WPDM\Session::get('member_success')) { ?>
            <li class="col-md-11">
                <div class="alert alert-success">
                    <b><?php _e('Done!', 'wpdm-premium-packages'); ?></b><br/>
                    <?php
                    echo \WPDM\Session::get('member_success');
                    \WPDM\Session::clear('member_success');
                    ?>
                </div>
            </li>
        <?php } ?>
    </ul>
    <div style="clear: both;margin-top:20px ;"></div>
    <div id="form" class="form profile-form">

        <form method="post" id="validate_form" class="wpmp-edit-profile-form" name="contact_form" action="">
            <input type="hidden" name="dact" value="update-profile"/>
            <input type="hidden" name="__upnonce" value="<?php wp_create_nonce(NONCE_KEY); ?>"/>


            <div class="panel panel-default">
                <div class="panel-heading"><?php _e('Basic Info', 'wpdm-premium-packages'); ?></div>
                <div class="panel-body">
                    <div class="row row-fluid">
                        <div class="form-group col-md-6 span6">
                            <label for="name"><?php _e('Your name:', 'wpdm-premium-packages'); ?></label>
                            <input type="text" class="required form-control col-sm-6" value="<?php echo $user->display_name; ?>" name="profile[display_name]" id="name">
                        </div>
                        <div class="form-group col-md-6 span6">
                            <label for="email"><?php _e('Your Email:', 'wpdm-premium-packages'); ?></label>
                            <input type="text" class="required form-control" value="<?php echo $user->user_email; ?>" name="profile[user_email]" id="email">
                        </div>
                    </div>

                    <div class="row row-fluid">
                        <div class="form-group col-md-6 span6">
                            <label for="phone"><?php _e('Phone Number:', 'wpdm-premium-packages'); ?></label>
                            <input type="text" class="required form-control" value="<?php echo get_user_meta($current_user->ID, 'phone', true); ?>" name="phone" id="phone">
                        </div>
                        <div class="form-group col-md-6 span6">
                            <label for="company_name"><?php _e('PayPal Account:', 'wpdm-premium-packages'); ?></label>
                            <input type="text" class="form-control" value="<?php echo get_user_meta($current_user->ID, 'payment_account', true); ?>"
                                   name="payment_account" id="payment_account"
                                   placeholder="<?php _e('Add paypal or moneybookers email here', 'wpdm-premium-packages'); ?>">
                        </div>
                    </div>

                    <div class="row row-fluid">
                        <div class="form-group col-md-6 span6">
                            <label for="new_pass"><?php _e('New Password:', 'wpdm-premium-packages'); ?></label>
                            <input placeholder="Use nothing if you don't want to change old password" type="password" value="" name="password" id="new_pass" class=" form-control">
                        </div>
                        <div class="form-group col-md-6 span6">
                            <label for="re_new_pass"><?php _e('Re-type New Password:', 'wpdm-premium-packages'); ?></label>
                            <input type="password" value="" name="cpassword" id="re_new_pass" class=" form-control">
                        </div>
                    </div>

                    <div class="row row-fluid">
                        <div class="form-group col-md-12 span12">
                            <label for="message"><?php _e('Description:', 'wpdm-premium-packages'); ?></label>
                            <textarea class="required form-control" cols="40" rows="8" name="profile[description]" id="message"><?php echo htmlspecialchars(stripslashes($current_user->description)); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <?php include wpdm_tpl_path('user-dashboard/billing-info.php', WPDMPP_TPL_DIR); ?>

            <div class="row row-fluid">
                <div class="col-md-12 span12">
                    <button type="submit" class="btn btn-large btn-primary" id="billing_btn"><i class="icon-ok icon-white"></i> <?php _e('Save Changes', 'wpdm-premium-packages'); ?></button>
                </div>
            </div>

        </form>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {

        $('span.error').css('color', 'red');

        $('#billing_btn').click(function () {
            //alert('1');
            var go = false;
            if ($.trim($("#billing_first_name").val()) == "") {
                go = true;
                $("#billing_first_name").parent().find('.error').html("<?php _e('Please Enter Your First Name', 'wpdm-premium-packages'); ?>");
            }
            else {
                $("#billing_first_name").parent().find('.error').html("");
            }

            if ($.trim($("#billing_last_name").val()) == "") {
                go = true;
                $("#billing_last_name").parent().find('.error').html("<?php _e('Please Enter Your Last Name', 'wpdm-premium-packages'); ?>");
            }
            else {
                $("#billing_last_name").parent().find('.error').html("");
            }

            if ($.trim($("#billing_address_1").val()) == "") {
                go = true;
                $("#billing_address_1").parent().find('.error').html("<?php _e('Please Enter Your Address', 'wpdm-premium-packages'); ?>");
            }
            else {
                $("#billing_address_1").parent().find('.error').html("");
            }

            if ($.trim($("#billing_city").val()) == "") {
                go = true;
                $("#billing_city").parent().find('.error').html("<?php _e('Please Enter Your City', 'wpdm-premium-packages'); ?>");
            }
            else {
                $("#billing_city").parent().find('.error').html("");
            }

            if ($.trim($("#billing_postcode").val()) == "") {
                go = true;
                $("#billing_postcode").parent().find('.error').html("<?php _e('Please Enter Your Postcode', 'wpdm-premium-packages'); ?>");
            }
            else {
                $("#billing_postcode").parent().find('.error').html("");
            }

            if ($.trim($("#billing_country").val()) == "") {
                go = true;
                $("#billing_country").parent().find('.error').html("<?php _e('Please Enter Your Country', 'wpdm-premium-packages'); ?>");
            }
            else {
                $("#billing_country").parent().find('.error').html("");
            }

            if ($.trim($("#billing_state").val()) == "") {
                go = true;
                $("#billing_state").parent().find('.error').html("<?php _e('Please Enter Your State', 'wpdm-premium-packages'); ?>");
            }
            else {
                $("#billing_state").parent().find('.error').html("");
            }

            if ($.trim($("#billing_email").val()) == "") {
                go = true;
                $("#billing_email").parent().find('.error').html("<?php _e('Please Enter Your Email Address', 'wpdm-premium-packages'); ?>");
            }
            else {
                $("#billing_email").parent().find('.error').html("");
            }

            if ($.trim($("#billing_phone").val()) == "") {
                go = true;
                $("#billing_phone").parent().find('.error').html("<?php _e('Please Enter Your Phone Number', 'wpdm-premium-packages'); ?>");
            }
            else {
                $("#billing_phone").parent().find('.error').html("");
            }


            if (go == false) return true;

            else return false;
        });

    });

</script>