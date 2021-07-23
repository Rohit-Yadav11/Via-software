<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 15/12/19 18:49
 */
if(!defined("ABSPATH")) die();
?>
<?php if (!in_array('images', $hide)) { ?>
<div id="additional-previews-section" class="card ">
    <div class="card-header">
        <b><?php _e("Additional Preview Images", "download-manager"); ?></b></div>
    <div class="inside">

        <?php
        wpdm_additional_preview_images($post); ?>


        <div class="clear"></div>
    </div>
</div>
<?php } ?>
