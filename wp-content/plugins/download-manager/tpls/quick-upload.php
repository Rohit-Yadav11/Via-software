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

<div class="wpdm-front">
    <?php if($post->ID > 0){ ?>
    <form id="wpdm-pf" action="" method="post">
        <?php wp_nonce_field(NONCE_KEY, '__wpdmepnonce'); ?>
        <div class="row">
            <div class="col-md-8">


                <input type="hidden" id="act" name="act"
                       value="_ap_wpdm"/>

                <input type="hidden" name="id" id="id" value="0"/>
                <div class="form-group">
                    <input id="title" class="form-control form-control-lg" placeholder="Enter title here" type="text" value=""  name="pack[post_title]"/>
                </div>
                <div class="form-group">
                    <div class="card " id="package-description">
                        <div class="card-header">
                            <strong><?php _e("Description", "download-manager"); ?></strong></div>
                        <div class="card-body-desc">
                            <?php $cont = isset($post) ? $post->post_content : '';
                            wp_editor(stripslashes($cont), 'post_content', array('textarea_name' => 'pack[post_content]', 'teeny' => 1, 'media_buttons' => 0)); ?>
                        </div>
                    </div>
                </div>


                <?php require_once wpdm_tpl_path('add-new-file-front/attached-files.php'); ?>

                <?php include wpdm_tpl_path("add-new-file-front/package-settings.php");  ?>

                <?php

                do_action("wpdm-package-form-left");
                ?>


            </div>
            <div class="col-md-4">

                <?php include wpdm_tpl_path('add-new-file-front/attach-file.php', __DIR__); ?>

                <?php include wpdm_tpl_path('add-new-file-front/categories.php', __DIR__); ?>

                <?php include wpdm_tpl_path('add-new-file-front/tags.php', __DIR__); ?>

                <?php include wpdm_tpl_path('add-new-file-front/featured-image.php', __DIR__); ?>

                <?php include wpdm_tpl_path('add-new-file-front/additional-previews.php', __DIR__); ?>

                <?php include wpdm_tpl_path('add-new-file-front/author.php', __DIR__); ?>

                <?php do_action("wpdm_frontend_add_package_sidebar", (isset($pid)?$pid:0)); ?>


                <div class="card card-primary " id="form-action">
                    <div class="card-header">
                        <b>Actions</b>
                    </div>
                    <div class="card-body">

                        <label><input class="wpdm-checkbox"
                                      type="checkbox" <?php if (isset($post->post_status)) checked($post->post_status, 'draft'); ?>
                                      value="draft"
                                      name="status"> <?php _e("Save as Draft", "download-manager"); ?>
                        </label><br/><br/>


                        <button type="submit" accesskey="p" tabindex="5" id="publish"
                                class="btn btn-success btn-block btn-lg" name="publish"><i
                                    class="far fa-hdd" id="psp"></i>
                            &nbsp;<?php echo $task == 'edit-package' ? __("Update Package", "download-manager") : __("Create Package", "download-manager"); ?>
                        </button>

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
            <div class="card card-info">
                <div class="card-header"><?php _e( "Create New Package" , "download-manager" ); ?></div>
                <div class="card-body">
                    <input id="title" class="form-control form-control-lg" required="required"  placeholder="Enter title here" type="text" value="<?php echo isset($post->post_title)?$post->post_title:''; ?>" name="pack[post_title]" />
                    <input type="hidden" value="auto-draft" name="status">
                </div>
                <div class="card-footer text-right">
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
                     jQuery(".card-file.card-danger").remove();
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
