<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 15/12/19 18:48
 */
if(!defined("ABSPATH")) die();
?>
<div class="panel panel-default" <?php if (in_array('images', $hide)) { ?>style="display: none"<?php } ?>>
    <div class="panel-heading"><b><?php _e("Main Preview Image", "download-manager"); ?></b>
    </div>

    <div id="img"><?php if (has_post_thumbnail((isset($pid) ? $pid : 0))):
            $thumbnail_id = get_post_thumbnail_id((isset($pid) ? $pid : 0));
            $thumbnail_url = wp_get_attachment_image_src($thumbnail_id, 'full', true);
            ?>
            <img src="<?php echo $thumbnail_url[0]; ?>" alt="preview"/><input type="hidden"
                                                                              name="file[preview]"
                                                                              value="<?php echo $thumbnail_url[0]; ?>"
                                                                              id="fpvw"/>
        <?php else: ?>
            <div class="inside">
                <a href="#" id="wpdm-featured-image">&nbsp;</a>
                <input type="hidden" name="file[preview]" value="" id="fpvw"/>
                <div class="clear"></div>
            </div>
        <?php endif; ?>
    </div>
    <!-- <input type="file" name="preview" /> -->

    <?php if (has_post_thumbnail((isset($pid) ? $pid : 0))): ?>
        <div class="panel-footer" id="rmvp">
            <a href="#"
               class="text-danger"><?php _e("Remove Featured Image", "download-manager"); ?></a>
        </div>
    <?php endif; ?>
</div>
