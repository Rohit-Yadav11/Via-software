<?php

if(!defined('ABSPATH')) die();

global $current_user;

if(isset($_GET['__wpdmedit'])){
    $pid = (int)$_GET['__wpdmedit'];
}
if(isset($pid))
$post = get_post($pid);
else {
$post = new stdClass();
$post->ID = 0;
$post->post_title = '';
$post->post_content = '';
}

if(!isset($burl)) {
    $burl = get_permalink(get_the_ID());
    $sap = strstr($burl, '?')?'&':'?';
    $edit_url = $burl.$sap.'__wpdmedit=%d';
} else {
    $edit_url = $burl . '/edit-package/%d/';
    if(get_post_meta(get_the_ID(),'__urlfix', true) == 1 || !get_option('permalink_structure'))
        $edit_url = $burl.$sap.'adb_page=edit-package/%d/';
}

$task = wpdm_query_var('task')?wpdm_query_var('task'):'add-package';
if(isset($hide)) $hide = explode(',',$hide);
else $hide = array();
?>
<div class="w3eden">
 <link rel="stylesheet" type="text/css" href="<?php echo plugins_url('/download-manager/assets/css/chosen.css'); ?>" />
<style>
    .cat-panel ul,
    .cat-panel label,
    .cat-panel li{
        padding: 0;
        margin: 0;
        font-size: 9pt;
    }
    .cat-panel ul{
        margin-left: 20px;
    }
    .cat-panel > ul{
        padding-top: 10px;
    }
</style>
<div class="wpdm-front">
    <?php if($post->ID > 0){ ?>
    <form id="wpdm-pf" action="" method="post">
        <?php wp_nonce_field(NONCE_KEY, '__wpdmepnonce'); ?>
        <div class="row">
    <div class="col-md-8">
        <input type="hidden" id="act" name="act" value="<?php echo isset($pid) && $pid > 0 ?'_ep_wpdm':'_ap_wpdm'; ?>" />
        <input type="hidden" name="id" id="id" value="<?php echo isset($pid)?$pid:0; ?>" />
<div class="form-group">
<input id="title" class="form-control input-lg"  placeholder="Enter title here" type="text" value="<?php echo isset($post->post_title)?$post->post_title:''; ?>" name="pack[post_title]" /><br/>
</div>
<div  class="form-group">
    <div class="panel panel-default" id="package-content-section">
        <div class="panel-heading"><b><?php _e( "Description" , "download-manager" ); ?></b></div>

            <?php $cont = isset($post)?$post->post_content:''; wp_editor(stripslashes($cont),'post_content',array('textarea_name'=>'pack[post_content]')); ?>

    </div>

</div>

        <div class="panel panel-default" id="attached-files-section">
            <div class="panel-heading"><b><?php _e( "Attached Files" , "download-manager" ); ?></b></div>
            <div class="panel-body">
                <?php
                require_once wpdm_tpl_path('metaboxes/attached-files.php');
                ?>
            </div>
        </div>

            <div class="panel panel-default" <?php if(in_array('package-settings', $hide)) { ?>style="display: none"<?php }?> id="package-settings-section">
                <div class="panel-heading"><b><?php _e( "Package Settings" , "download-manager" ); ?></b></div>
                <div class="panel-body">
                    <?php
                    require_once wpdm_tpl_path("metaboxes/package-settings-front.php");
                    ?>
                </div>
            </div>

        <?php

            do_action("wpdm-package-form-left");
        ?>


</div>
<div class="col-md-4">

    <div class="panel panel-default" id="package-settings-section">
        <div class="panel-heading"><b><?php _e( "Attach Files" , "download-manager" ); ?></b></div>
        <div class="panel-body">
            <?php
            require_once wpdm_tpl_path("metaboxes/attach-file.php");
            ?>
        </div>
    </div>

    <!-- div class="panel panel-default" id="package-settings-section">
        <div class="panel-heading"><b><?php _e( "Live Demo/Preview" , "download-manager" ); ?></b></div>
        <div class="panel-body" style="padding: 20px !important;">
            <input type="text" placeholder="<?php _e( "Live Preview URL" , "download-manager" ); ?>" class="form-control" name="file[demo_url]">
        </div>
    </div -->

    <div class="panel panel-default" id="package-settings-section">
        <div class="panel-heading"><b><?php _e( "Categories" , "download-manager" ); ?></b></div>
        <div class="panel-body cat-panel">
            <?php
            $term_list = wp_get_post_terms($post->ID, 'wpdmcategory', array("fields" => "all"));

            if(!function_exists('wpdm_categories_checkboxed_tree')) {
                function wpdm_categories_checkboxed_tree($parent = 0, $selected = array())
                {
                    $categories = get_terms('wpdmcategory', array('hide_empty' => 0, 'parent' => $parent));
                    $checked = "";
                    foreach ($categories as $category) {
                        if ($selected) {
                            foreach ($selected as $ptype) {
                                if ($ptype->term_id == $category->term_id) {
                                    $checked = "checked='checked'";
                                    break;
                                } else $checked = "";
                            }
                        }
                        echo '<li><label><input type="checkbox" ' . $checked . ' name="cats[]" value="' . $category->term_id . '"> ' . $category->name . ' </label>';
                        echo "<ul>";
                        wpdm_categories_checkboxed_tree($category->term_id, $selected);
                        echo "</ul>";
                        echo "</li>";
                    }
                }
            }

            echo "<ul class='ptypes'>";
            $cparent = isset($params['base_category'])?$params['base_category']:0;
            if($cparent!==0){
                $cparent = get_term_by('slug', $cparent, 'wpdmcategory');
                $cparent = $cparent->term_id;
                echo "<input type='hidden' value='{$cparent}' name='cats[]' />";
            }
            wpdm_categories_checkboxed_tree($cparent, $term_list);
            echo "</ul>";
            ?>
        </div>
    </div>

    <div class="panel panel-default" id="tags-section" <?php if(in_array('tags', $hide)) { ?>style="display: none"<?php }?>>
        <div class="panel-heading"><b><?php _e( "Tags" , "download-manager" ); ?></b></div>
        <div class="panel-body tag-panel" id="tags">
            <?php
            $tags =  wp_get_post_tags($post->ID);

            foreach ($tags as $tag){ ?>
                <div style="margin: 4px" id="tag_<?php echo $tag->term_id; ?>">
                    <div class="input-group">
                        <input type="text" class="form-control input-sm" disabled="disabled" value="<?php echo $tag->name; ?>">
                        <div class="input-group-addon">
                            <span class="input-group-text color-red" style="cursor: pointer">
                                <input type="hidden" name="tags[]" value="<?php echo $tag->name; ?>" />
                                <i class="fa fa-times-circle" onclick="$('#tag_<?php echo $tag->term_id; ?>').remove();"></i>
                            </span>

                        </div>
                    </div>
                </div>
            <?php }
            ?>
        </div>
        <div class="panel-footer">
            <div class="input-group">
                <input type="text" id="tagname" class="form-control" value="<?php echo $tag->name; ?>">
                <div class="input-group-btn">
                    <button type="button" id="addtag" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button>
                </div>
            </div>
        </div>
    </div>




    <div class="panel panel-default">
        <div class="panel-heading"><b><?php _e( "Main Preview Image" , "download-manager" ); ?></b></div>
        <div class="inside">
            <div id="img"><?php if(has_post_thumbnail((isset($pid)?$pid:0))):
                    $thumbnail_id = get_post_thumbnail_id((isset($pid)?$pid:0));
                    $thumbnail_url = wp_get_attachment_image_src($thumbnail_id,'full', true);
                    ?>
                    <p><img src="<?php  echo $thumbnail_url[0]; ?>" width="240" alt="preview" /><input type="hidden" name="file[preview]" value="<?php  echo $thumbnail_url[0]; ?>" id="fpvw" /></p>
                    <a href="#"  id="rmvp" class="text-danger">Remove Featured Image</a>
                <?php else: ?>
                    <a href="#" id="wpdm-featured-image">&nbsp;</a>
                    <input type="hidden" name="file[preview]" value="" id="fpvw" />
                <?php endif; ?>
            </div>
            <!-- <input type="file" name="preview" /> -->
            <div class="clear"></div>
        </div>
    </div>

<div class="panel panel-default">
<div class="panel-heading"><b><?php _e( "Additional Preview Images" , "download-manager" ); ?></b></div>
<div class="inside">

    <?php
    wpdm_additional_preview_images($post); ?>


 <div class="clear"></div>
</div>
</div>














<div class="panel panel-primary " id="form-action">
    <div class="panel-heading">
        <b>Actions</b>
    </div>
<div class="panel-body">

<label><input class="wpdm-checkbox" type="checkbox" <?php if(isset($post->post_status)) checked($post->post_status,'draft'); ?> value="draft" name="status"> <?php _e( "Save as Draft" , "download-manager" ); ?></label><br/><br/>


<button type="submit" accesskey="p" tabindex="5" id="publish" class="btn btn-success btn-block btn-lg" name="publish"><i class="far fa-hdd" id="psp"></i> &nbsp; <?php echo $task==isset($pid) && $pid > 0 ?__( "Update Package" , "download-manager" ):__( "Create Package" , "download-manager" ); ?></button>

</div>
</div>

</div>
</div>

</form>
    <?php } else { ?>

        <form id="wpdm-pf" action="" method="post">
            <input type="hidden" id="act" name="act" value="_ap_wpdm" />
            <?php wp_nonce_field(NONCE_KEY, '__wpdmepnonce'); ?>
            <input type="hidden" name="id" id="id" value="0" />
            <div class="panel panel-info">
                <div class="panel-heading"><h3 style="margin: 0;padding: 0;letter-spacing: 1px"><?php _e( "Create New Package" , "download-manager" ); ?></h3></div>
                <div class="panel-body">
                    <input id="title" class="form-control input-lg"  required="required" placeholder="Enter title here" type="text" value="<?php echo isset($post->post_title)?$post->post_title:''; ?>" name="pack[post_title]" />
                    <input type="hidden" value="auto-draft" name="status">
                </div>
                <div class="panel-footer text-right">
                    <button type="submit" accesskey="p" tabindex="5" id="publish" class="btn btn-success btn-sm" name="publish"><i class="fa fa-check" id="psp"></i> &nbsp;<?php echo __( "Continue..." , "download-manager" ); ?></button>
                </div>
            </div>
        </form>

    <?php } ?>

</div>


<script type="text/javascript" src="<?php echo plugins_url('/download-manager/assets/js/chosen.jquery.min.js'); ?>"></script>
      <script type="text/javascript">

      jQuery(document).ready(function() {

        jQuery('.w3eden select').chosen();
        jQuery('span.infoicon').css({color:'transparent',width:'16px',height:'16px',cursor:'pointer'}).tooltip({placement:'right',html:true});
        jQuery('span.infoicon').tooltip({placement:'right'});
        jQuery('.nopro').click(function(){
            if(this.checked) jQuery('.wpdmlock').removeAttr('checked');
        });

          $('#addtag').on('click', function () {
              var tid = Math.random().toString(36).substr(2, 16);
              var tag = $('#tagname').val();
              $('#tags').append("<div style=\"margin: 4px\" id=\"tag_"+tid+"\">\n" +
                  "                    <div class=\"input-group\">\n" +
                  "                        <input type=\"text\" class=\"form-control input-sm\" disabled=\"disabled\" value=\""+tag+"\">\n" +
                  "                        <div class=\"input-group-append\">\n" +
                  "                            <span class=\"input-group-text color-red\" style=\"cursor: pointer\">\n" +
                  "                                <input type=\"hidden\" name=\"tags[]\" value=\""+tag+"\" />\n" +
                  "                                <i class=\"fa fa-times-circle\" onclick=\"$('#tag_"+tid+"').remove();\"></i>\n" +
                  "                            </span>\n" +
                  "\n" +
                  "                        </div>\n" +
                  "                    </div>\n" +
                  "                </div>");
              $('#tagname').val('');
          });

        jQuery('.wpdmlock').click(function(){

            if(this.checked) {
            jQuery('#'+jQuery(this).attr('rel')).slideDown();
            jQuery('.nopro').removeAttr('checked');
            } else {
            jQuery('#'+jQuery(this).attr('rel')).slideUp();
            }
        });

       // jQuery( "#pdate" ).datepicker({dateFormat:'yy-mm-dd'});
       // jQuery( "#udate" ).datepicker({dateFormat:'yy-mm-dd'});

        jQuery('#wpdm-pf').submit(function(){
             try {
                var editor = tinymce.get('post_content');
                editor.save();
             }catch(e){}
             if(jQuery('#title').val() === '') return false;
             jQuery('#psp').removeClass('fa-save').addClass('fa-spinner fa-spin');
             jQuery('#publish').attr('disabled','disabled');
             jQuery('#wpdm-pf').ajaxSubmit({
                 //dataType: 'json',
                 beforeSubmit: function() { jQuery('#sving').fadeIn(); },
                 success: function(res) {  jQuery('#sving').fadeOut(); jQuery('#nxt').slideDown();
                     jQuery(".panel-file.panel-danger").remove();
                     if(res.result=='_ap_wpdm') {
                         var edit_url = '<?php echo $edit_url; ?>';
                         edit_url = edit_url.replace('%d', res.id);
                         location.href = edit_url;
                         jQuery('#wpdm-pf').prepend('<div class="alert alert-success" data-dismiss="alert"><?php _e( "Package Created Successfully. Opening Edit Window ..." , "download-manager" ); ?></div>');
                     }
                     else {
                         jQuery('#psp').removeClass('fa-spinner fa-spin').addClass('fa-save');
                         jQuery('#publish').removeAttr('disabled');
                         jQuery('#wpdm-pf').prepend('<div class="alert alert-success" data-dismiss="alert"><?php _e( "Package Updated Successfully" , "download-manager" ); ?></div>');
                     }
                 }


             });
             return false;
        });



      });

      jQuery('#upload-main-preview').click(function() {
            tb_show('', '<?php echo admin_url('media-upload.php?type=image&TB_iframe=1&width=640&height=551'); ?>');
            window.send_to_editor = function(html) {
              var imgurl = jQuery('img',"<p>"+html+"</p>").attr('src');
              jQuery('#img').html("<img src='"+imgurl+"' style='max-width:100%'/><input type='hidden' name='file[preview]' value='"+imgurl+"' >");
              tb_remove();
              }
            return false;
        });




      </script>

</div>
<?php wp_reset_query(); ?>