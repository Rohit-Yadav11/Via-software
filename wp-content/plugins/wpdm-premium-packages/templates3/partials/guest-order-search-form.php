<?php
/**
 * Find guest order form template
 *
 * This template can be overridden by copying it to yourtheme/download-manager/partials/guest-order-search-form.php.
 *
 * @version     1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div id="gonotice"></div>
<form method="post" id="goform">
    <input type="hidden" name="__wpdmpp_go_nonce" value="<?php echo wp_create_nonce(NONCE_KEY); ?>" />
    <div class="panel panel-default">
        <div class="panel-heading text-lg"><?php _e('Guest Order Access','wpdm-premium-packages'); ?></div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label><?php _e('Order Email:','wpdm-premium-packages'); ?></label>
                        <input type="email" required="required" id="goemail" name="__wpdmpp_go[email]" value="<?php echo \WPDM\Session::get('order_email'); ?>" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label><?php _e('Order ID:','wpdm-premium-packages'); ?></label>
                        <input type="text" required="required" id="goorder" name="__wpdmpp_go[order]" value="<?php echo \WPDM\Session::get('guest_order'); ?>" class="form-control" />
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer text-right">
            <button class="btn btn-primary btn-sm" id="goproceed">
                <?php _e('Proceed','wpdm-premium-packages'); ?> &nbsp; <i class="fa fa-chevron-right"></i>
            </button>
        </div>
    </div>
</form>
<script>
    jQuery(function($){
        var goerrors = new Array();
        goerrors['nosess'] = "<?php _e('Session was expired. Please try again','wpdm-premium-packages'); ?>";
        goerrors['noordr'] = "<?php _e('Order not found, Please re-check your info','wpdm-premium-packages'); ?>";
        goerrors['nogues'] = "<?php _e('Order is already associated with an account. Please login using that account to get access','wpdm-premium-packages'); ?>";

        $('#goform').submit(function(){
            var gop = $('#goproceed').html();
            $('#goproceed').html("<i class='fa fa-spinner fa-spin'></i>");
            $(this).ajaxSubmit({
                success: function(res){
                    if(res.match(/nosess/))  $('#gonotice').html('<div class="alert alert-danger">' + goerrors['nosess'] + '</div>');
                    else if(res.match(/noordr/))  $('#gonotice').html('<div class="alert alert-danger">' + goerrors['noordr'] + '</div>');
                    else if(res.match(/nogues/))  $('#gonotice').html('<div class="alert alert-danger">' + goerrors['nogues'] + '</div>');
                    else if(res.match(/success/)) { location.href = '<?php echo wpdmpp_guest_order_page(); ?>'; gop = "<i class='fas fa-sync fa-spin'></i>"; }
                    $('#goproceed').html(gop);
                }
            });
            return false;
        });
    });
</script>