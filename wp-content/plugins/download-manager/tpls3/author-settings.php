<?php
/**
 * User: shahnuralam
 * Date: 1/23/17
 * Time: 1:46 AM
 */
if(!defined('ABSPATH')) die('!');

?>

<form method="post" id="author-settings-form">
    <?php wp_nonce_field(NONCE_KEY, '__saveas'); ?>
    <?php do_action("wpdm_author_settings", $settings); ?>
    <hr/>
    <button type="submit" id="asfs" class="btn btn-success"><i class="far fa-hdd"></i> &nbsp;<?php _e( "Save Settings" , "download-manager" ); ?></button>
</form>

<script>
    jQuery(function ($) {
        $('#author-settings-form').submit(function (e) {
            e.preventDefault();
            var asfst = $('#asfs').html();
            $('#asfs').html("<i class='fa fa-sync fa-spin'></i> &nbsp;<?php _e( "Saving..." , "download-manager" ); ?>");
            $(this).ajaxSubmit({
                url: ajax_url+'?action=wpdm_author_settings',
                success: function (data) {
                    $('#asfs').html(asfst);
                }
            });
        });
    });
</script>

