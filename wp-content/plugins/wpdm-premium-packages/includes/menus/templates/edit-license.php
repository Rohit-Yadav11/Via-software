<?php
/**
 * Edit License Form
 *
 * @version     1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $wpdb;
?>

<div class="w3eden">
    <div class="panel panel-default" id="wpdm-wrapper-panel">
        <div class="panel-heading">
            <b><i class="fa fa-id-card-o color-purple"></i> &nbsp; <?php _e('Edit License', 'wpdm-premium-packages'); ?></b>
            <div class="pull-right">
                <a href="#" id="apply" class="btn btn-sm btn-default" onclick="jQuery('#edit-license-form').submit();return false;"><i class="far fa-hdd color-green"></i> <?php _e('Update License', 'wpdm-premium-packages'); ?></a>
            </div>
        </div>
        <div class="panel-body"><br/><br/><br/>
            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <form method="post" action="" id="edit-license-form">
                            <input type="hidden" name="do" value="updatelicense">
                            <?php wp_nonce_field(NONCE_KEY, '__suc'); ?>
                            <div class="form-group">
                                <label><?php _e('License No:', 'wpdm-premium-packages'); ?></label>
                                <input id="title" class="form-control input-lg" type="text" readonly="readonly" value="<?php echo $license->licenseno; ?>">
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php _e('Order ID:', 'wpdm-premium-packages'); ?></label>
                                        <input class="form-control" type="text" readonly="readonly" value="<?php echo $license->oid; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php _e('Product ID:', 'wpdm-premium-packages'); ?></label>
                                        <input class="form-control" type="text" readonly="readonly" value="<?php echo $license->pid; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label><?php _e('Domain Limit:', 'wpdm-premium-packages'); ?></label>
                                        <input class="form-control" type="number" min="0" step="1" name="license[domain_limit]"
                                               value="<?php echo $license->domain_limit; ?>"/>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group">
                                <label>
                                    <?php _e('Domains:', 'wpdm-premium-packages'); ?>
                                    <span class="fa fa-info-circle ttip" title="One domain per line. Don't use 'http://' or 'www' only 'domain.com'"></span></label>
                                <textarea class="form-control" cols="60" rows="6"
                                          name="license[domain]"><?php echo @implode("\n", maybe_unserialize($license->domain)); ?></textarea>
                                <em><?php _e("One domain per line. Don't use 'http://' or 'www' only 'domain.com'", "wpdm-premium-packages"); ?></em>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php _e('Activation Date:', 'wpdm-premium-packages'); ?></label>
                                        <input class="form-control" id="actdate" type="text" name="license[activation_date]"
                                               value="<?php echo $license->activation_date > 0 ? date("Y-m-d h:i a", $license->activation_date) : ''; ?>"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?php _e('Expire Date:', 'wpdm-premium-packages'); ?></label>
                                        <input id="expdate" class="form-control" type="text" size="5" name="license[expire_date]"
                                               value="<?php echo $license->expire_date > 0 ? date("Y-m-d h:i a", $license->expire_date) : ''; ?>"/>
                                    </div>
                                </div>

                            </div>


                            <div class="form-group well text-right">
                                <button class="btn btn-primary btn-lg"><i class="far fa-hdd"></i> <?php _e('Update License', 'wpdm-premium-packages'); ?></button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(function () {
        jQuery('#actdate, #expdate').datetimepicker({dateFormat: "yy-mm-dd", timeFormat: "hh:mm tt"});
    });
</script>