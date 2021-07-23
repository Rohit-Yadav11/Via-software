<?php
/**
 * Form to connect unaasigned order to an user account
 *
 * This template can be overridden by copying it to yourtheme/download-manager/partials/resolve-order.php.
 *
 * @version     1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class='panel panel-default' style='margin: 10px 0'>
    <div class='panel-heading'><?php _e('If you do not see your order:', 'wpdm-premium-packages'); ?></div>
    <div class='panel-body'>
        <form id='resolveorder' method='post'>
            <input type='hidden' name='action' value='resolveorder'/>
            <div class='input-group input-group-lg'>
                <input type='text' name='orderid' value='' placeholder='<?php _e('Enter Your Order/Invoice ID Here', 'wpdm-premium-packages'); ?>' class='form-control' style='border-right: 0'>

                <div class="input-group-btn"><button class='btn btn-primary' type='submit'><?php _e('Resolve', 'wpdm-premium-packages'); ?></button></div>

            </div>
        </form>
        <div id='w8o' class='text-danger' style='height: 40px;line-height: 40px;display: none;cursor: pointer'>
            <i class='fa fa-spinner fa-spin'></i> <?php _e('Please Wait...', 'wpdm-premium-packages'); ?>
        </div>
    </div>
</div>
<script>
    jQuery(function ($) {
        $('#resolveorder').submit(function () {
            $('#resolveorder').slideUp();
            $('#w8o').html("<i class='fa fa-spinner fa-spin' ></i> <?php _e('Tracking Order...', 'wpdm-premium-packages'); ?>").slideDown();
            $(this).ajaxSubmit({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                success: function (res) {
                    if (res == 'ok') {
                        $('#w8o').html('<span class="text-success"><i class="fa fa-check"></i> <?php _e('Order is linked with your account successfully.', 'wpdm-premium-packages'); ?></span>');
                        location.href = location.href;
                    }
                    else
                        $('#w8o').html(res);
                }
            });
            return false;
        });
        $('#w8o').click(function () {
            jQuery(this).slideUp();
            $('#resolveorder').slideDown();
        });
    });
</script>
