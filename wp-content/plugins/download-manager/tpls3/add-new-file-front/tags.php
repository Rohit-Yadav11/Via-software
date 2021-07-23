<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 15/12/19 13:26
 */
if(!defined("ABSPATH")) die();
?>
<div class="panel panel-default" id="tags-section"
     <?php if (in_array('tags', $hide)) { ?>style="display: none"<?php } ?>>
    <div class="panel-heading"><b><?php _e("Tags", "download-manager"); ?></b></div>
    <div class="panel-body tag-panel" id="tags">
        <?php
        $tags = wp_get_post_tags($post->ID);

        foreach ($tags as $tag) { ?>
            <div style="margin: 4px" id="tag_<?php echo $tag->term_id; ?>">
                <div class="input-group">
                    <input type="text" class="form-control input-sm" disabled="disabled"
                           value="<?php echo $tag->name; ?>">
                    <div class="input-group-btn">
                            <span class="input-group-text color-red" style="cursor: pointer">
                                <input type="hidden" name="tags[]" value="<?php echo $tag->name; ?>"/>
                                <i class="fa fa-times-circle"
                                   onclick="$('#tag_<?php echo $tag->term_id; ?>').remove();"></i>
                            </span>

                    </div>
                </div>
            </div>
        <?php }
        ?>
    </div>
    <div class="panel-footer">
        <div class="input-group">
            <input type="text" id="tagname" class="form-control" value="">
            <div class="input-group-btn">
                <button type="button" id="addtag" class="btn btn-primary"><i
                        class="fa fa-plus-circle"></i></button>
            </div>
        </div>
    </div>
</div>


<script>
    jQuery(function ($) {
        $('#addtag').on('click', function () {
            var tid = Math.random().toString(36).substr(2, 16);
            var tag = $('#tagname').val();
            $('#tags').append("<div style=\"margin: 4px\" id=\"tag_" + tid + "\">\n" +
                "                    <div class=\"input-group\">\n" +
                "                        <input type=\"text\" class=\"form-control input-sm\" disabled=\"disabled\" value=\"" + tag + "\">\n" +
                "                        <div class=\"input-group-btn\">\n" +
                "                            <span class=\"input-group-text color-red\" style=\"cursor: pointer\">\n" +
                "                                <input type=\"hidden\" name=\"tags[]\" value=\"" + tag + "\" />\n" +
                "                                <i class=\"fa fa-times-circle\" onclick=\"jQuery('#tag_" + tid + "').remove();\"></i>\n" +
                "                            </span>\n" +
                "\n" +
                "                        </div>\n" +
                "                    </div>\n" +
                "                </div>");
            $('#tagname').val('');
        });
    });
</script>
