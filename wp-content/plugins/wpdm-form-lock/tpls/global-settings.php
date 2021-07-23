<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 4/1/20 21:45
 */

if(!defined("ABSPATH")) die();
?>
<div class="panel panel-default">
    <div class="panel-heading"><?php _e('Form Lock', 'wpdm-form-lock'); ?></div>
    <div class="panel-body">

        <div class="form-group">
            <b><?php _e('When someone tries to download', 'wpdm-form-lock'); ?>: </b>
            <label> <input type="radio" checked="checked" name="__wpdm_show_form_lock" value="1" <?php checked((int)get_option('__wpdm_show_form_lock', 0), 1) ?>>  <?php _e('Show form everytime', 'wpdm-form-lock'); ?> &nbsp; </label>
            <label><input type="radio" name="__wpdm_show_form_lock" value="0" <?php checked((int)get_option('__wpdm_show_form_lock', 0), 0) ?>> <?php _e('Show form once in a session', 'wpdm-form-lock'); ?></label><br>
            <em><?php _e('When the form lock is enabled for that package', 'wpdm-form-lock'); ?></em>
        </div>

    </div>
</div>
