<?php
global $post, $current_user;
if (!defined('ABSPATH')) die();

if (isset($pid))
    $post = get_post($pid);
else {
    $post = new stdClass();
    $post->ID = 0;
    $post->post_title = '';
    $post->post_content = '';
}

if (isset($hide)) $hide = explode(',', $hide);
else $hide = array();
$can_edit = $post->ID === 0 || (array_intersect($current_user->roles, get_option('__wpdm_front_end_admin', array()))) || $post->post_author == get_current_user_id() ? 1 : 0;
if ($can_edit) {
    ?>
    <div class="w3eden">
        <link rel="stylesheet" type="text/css"
              href="<?php echo plugins_url('/download-manager/assets/css/chosen.css'); ?>"/>
        <style>
            .cat-card ul,
            .cat-card label,
            .cat-card li {
                padding: 0;
                margin: 0;
                font-size: 9pt;
            }

            .cat-card ul {
                margin-left: 20px;
            }

            .cat-card > ul {
                padding-top: 10px;
            }

            #wpdm-pf .card {
                margin-bottom: 15px;
            }
        </style>
        <div class="wpdm-front">
            <?php if ($post->ID > 0) { ?>
                <form id="wpdm-pf" action="" method="post">
                    <?php wp_nonce_field(NONCE_KEY, '__wpdmepnonce'); ?>
                    <?php do_action("wpdm_before_new_package_form", $post->ID); ?>
                    <div class="row">

                        <div class="col-md-8">


                            <input type="hidden" id="act" name="act"
                                   value="<?php echo $task == 'edit-package' ? '_ep_wpdm' : '_ap_wpdm'; ?>"/>

                            <input type="hidden" name="id" id="id" value="<?php echo isset($pid) ? $pid : 0; ?>"/>
                            <div class="form-group">
                                <input id="title" class="form-control form-control-lg" placeholder="Enter title here"
                                       type="text"
                                       value="<?php echo isset($post->post_title) ? $post->post_title : ''; ?>"
                                       name="pack[post_title]"/>
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
                            do_action("wpdm_frontend_add_package_left");
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
                    <?php do_action("wpdm_after_new_package_form", $post->ID); ?>
                </form>
            <?php } else { ?>

                <form id="wpdm-pf" action="" method="post">
                    <?php do_action("wpdm_before_new_package_form", 0); ?>
                    <input type="hidden" id="act" name="act" value="_ap_wpdm"/>
                    <input type="hidden" name="id" id="id" value="0"/>
                    <?php wp_nonce_field(NONCE_KEY, '__wpdmepnonce'); ?>

                    <div class="card">
                        <div class="card-header"><?php _e("Create New Package", "download-manager"); ?></div>
                        <div class="card-body">
                            <div class="form-group">
                                <input id="title" class="form-control form-control-lg" required="required"
                                       placeholder="Enter title here" type="text"
                                       value="<?php echo isset($post->post_title) ? $post->post_title : ''; ?>"
                                       name="pack[post_title]"/>
                            </div>
                            <input type="hidden" value="auto-draft" name="status">
                            <button type="submit" accesskey="p" tabindex="5" id="publish" class="btn btn-primary"
                                    name="publish"><i class="fas fa-hdd" id="psp"></i>
                                &nbsp;<?php echo __("Continue...", "download-manager"); ?></button>

                        </div>
                    </div>
                    <?php do_action("wpdm_after_new_package_form", 0); ?>
                </form>

            <?php } ?>
        </div>


        <script type="text/javascript"
                src="<?php echo plugins_url('/download-manager/assets/js/chosen.jquery.min.js'); ?>"></script>
        <script type="text/javascript">

            var ps = "", pss = "", allps = "";

            jQuery(function ($) {

                $('.w3eden select').chosen();
                $('span.infoicon').css({
                    color: 'transparent',
                    width: '16px',
                    height: '16px',
                    cursor: 'pointer'
                }).tooltip({placement: 'right', html: true});
                $('span.infoicon').tooltip({placement: 'right'});
                $('.nopro').click(function () {
                    if (this.checked) $('.wpdmlock').removeAttr('checked');
                });

                $('.wpdmlock').click(function () {

                    if (this.checked) {
                        $('#' + $(this).attr('rel')).slideDown();
                        $('.nopro').removeAttr('checked');
                    } else {
                        $('#' + $(this).attr('rel')).slideUp();
                    }
                });

                // jQuery( "#pdate" ).datepicker({dateFormat:'yy-mm-dd'});
                // jQuery( "#udate" ).datepicker({dateFormat:'yy-mm-dd'});

                jQuery('#wpdm-pf').submit(function () {
                    try {
                        var editor = tinymce.get('post_content');
                        editor.save();
                    } catch (e) {
                    }
                    if (jQuery('#title').val() === '') return false;
                    jQuery('#psp').removeClass('fa-hdd').addClass('fa-sun fa-spin');
                    jQuery('#publish').attr('disabled', 'disabled');
                    jQuery('#wpdm-pf').ajaxSubmit({
                        //dataType: 'json',
                        beforeSubmit: function () {
                            jQuery('#sving').fadeIn();
                        },
                        success: function (res) {
                            jQuery('#sving').fadeOut();
                            jQuery('#nxt').slideDown();
                            jQuery(".card-file.card-danger").remove();
                            if (res.result == '_ap_wpdm') {
                                <?php
                                $edit_url = $burl . $sap . 'adb_page=edit-package/%d/';
                                if (isset($params['flaturl']) && $params['flaturl'] == 1)
                                    $edit_url = $burl . '/edit-package/%d/'; ?>
                                var edit_url = '<?php echo $edit_url; ?>';
                                edit_url = edit_url.replace('%d', res.id);
                                location.href = edit_url;
                                jQuery('#wpdm-pf').prepend('<div class="alert alert-success" style="width:300px;" data-title="<?php _e("Package Created Successfully!", "download-manager"); ?>" data-dismiss="alert"><?php _e("Opening Edit Window ...", "download-manager"); ?></div>');
                            } else {
                                jQuery('#psp').removeClass('fa-sun fa-spin').addClass('fa-hdd');
                                jQuery('#publish').removeAttr('disabled');
                                jQuery('#wpdm-pf').prepend('<div class="alert alert-success" data-title="<?php _e("DONE!", "download-manager"); ?>" data-dismiss="alert"><?php _e("Package Updated Successfully", "download-manager"); ?></div>');
                            }
                        }


                    });
                    return false;
                });


                allps = jQuery('#pps_z').val();
                if (allps == undefined) allps = '';
                jQuery('#ps').val(allps.replace(/\]\[/g, "\n").replace(/[\]|\[]+/g, ''));
                shuffle = function () {
                    var sl = 'abcdefghijklmnopqrstuvwxyz';
                    var cl = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    var nm = '0123456789';
                    var sc = '~!@#$%^&*()_';
                    ps = "";
                    pss = "";
                    if (jQuery('#ls').attr('checked') == 'checked') ps = sl;
                    if (jQuery('#lc').attr('checked') == 'checked') ps += cl;
                    if (jQuery('#nm').attr('checked') == 'checked') ps += nm;
                    if (jQuery('#sc').attr('checked') == 'checked') ps += sc;
                    var i = 0;
                    while (i <= ps.length) {
                        $max = ps.length - 1;
                        $num = Math.floor(Math.random() * $max);
                        $temp = ps.substr($num, 1);
                        pss += $temp;
                        i++;
                    }

                    jQuery('#ps').val(pss);


                };
                jQuery('#gps').click(shuffle);

                jQuery('body').on('click', '#gpsc', function () {
                    var allps = "";
                    shuffle();
                    for (k = 0; k < jQuery('#pcnt').val(); k++) {
                        allps += "[" + randomPassword(pss, jQuery('#ncp').val()) + "]";

                    }
                    vallps = allps.replace(/\]\[/g, "\n").replace(/[\]|\[]+/g, '');
                    jQuery('#ps').val(vallps);

                });

                jQuery('body').on('click', '#pins', function () {
                    var aps;
                    aps = jQuery('#ps').val();
                    aps = aps.replace(/\n/g, "][");
                    allps = "[" + aps + "]";
                    jQuery(jQuery(this).data('target')).val(allps);
                    tb_remove();
                });


            });

            jQuery('#upload-main-preview').click(function () {
                tb_show('', '<?php echo admin_url('media-upload.php?type=image&TB_iframe=1&width=640&height=551'); ?>');
                window.send_to_editor = function (html) {
                    var imgurl = jQuery('img', html).attr('src');
                    jQuery('#img').html("<img src='" + imgurl + "' style='max-width:100%'/><input type='hidden' name='file[preview]' value='" + imgurl + "' >");
                    tb_remove();
                };
                return false;
            });


            function randomPassword(chars, size) {

                //var size = 10;
                if (parseInt(size) == Number.NaN || size == "") size = 8;
                var i = 1;
                var ret = "";
                while (i <= size) {
                    $max = chars.length - 1;
                    $num = Math.floor(Math.random() * $max);
                    $temp = chars.substr($num, 1);
                    ret += $temp;
                    i++;
                }
                return ret;
            }


        </script>

    </div>
<?php } else { ?>
    <div class="w3eden">
        <div class="alert alert-danger">
            <?php echo __("You are not authorized to edit this package!", "download-manager") ?>
        </div>
    </div>
<?php } ?>
