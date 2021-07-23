<?php
/**
 * Author: shahnuralam
 * Date: 2018-12-28
 * Time: 11:58
 */
if (!defined('ABSPATH')) die();

$section_id = "email_lock_{$unqid}{$package['ID']}";
$form_id = "wpdmdlf_{$field_id}";

$custom_form_fields =  wpdm_render_custom_data('', $package['ID']);
$custom_form_fields = apply_filters('wpdm_render_custom_form_fields', $custom_form_fields, $package['ID']);

?>

<div id="<?php echo $section_id;?>" class="<?php echo $section_id;?>">
<form id="<?php echo $form_id; ?>" class="<?php echo $form_id; ?>" method=post action="<?php echo home_url('/'); ?>" style="font-weight:normal;font-size:12px;padding:0px;margin:0px">
    <div class="panel panel-default">
        <div class="panel-heading">
            <?php echo $section_title; ?>
        </div>
        <div class="panel-body">

             <?php echo $intro; ?>

            <input type=hidden name="__wpdm_ID" value="<?php echo $package['ID']; ?>" />
            <input type=hidden name="dataType" value="json" />
            <input type=hidden name="execute" value="wpdm_getlink" />
            <input type=hidden name="verify" value="email" />
            <input type=hidden name="action" value="wpdm_ajax_call" />

            <?php echo $custom_form_fields; ?>

            <label><?php echo apply_filters("email_lock_email_field_label",__( "Email" , "download-manager" ), $package['ID']); ?>:</label>
            <input type="email" required="required"  oninvalid="this.setCustomValidity('<?php echo __( "Please enter a valid email address" , "download-manager" ) ?>')" class="form-control group-item email-lock-mail" placeholder="<?php _e("Email Address", "download-manager"); ; ?>" size="20" id="email_<?php echo $field_id; ?>" name="email" style="margin:5px 0" />



        </div><div class="panel-footer text-right"><button id="wpdm_submit_<?php echo $field_id; ?>" class="wpdm_submit btn btn-success  group-item"  type=submit><?php echo $button_label; ?></button></div></div>
</form>
</div>

<script type="text/javascript">
    jQuery(function($){
        var sname = localStorage.getItem("email_lock_name");
        var semail = localStorage.getItem("email_lock_mail");

        if(sname != "undefined")
            $(".email-lock-mail").val(semail);
        if(sname != "undefined")
            $(".email-lock-name").val(sname);

        $(".<?php echo $form_id; ?>").submit(function(){
            var paramObj = {};
            localStorage.setItem("email_lock_mail", $("#email_<?php echo $field_id; ?>").val());
            localStorage.setItem("email_lock_name", $("#<?php echo $form_id; ?> input.email-lock-name").val());
            WPDM.blockUI('.<?php echo $section_id; ?>');
            $.each($(this).serializeArray(), function(_, kv) {
                paramObj[kv.name] = kv.value;
            });
            var nocache = new Date().getMilliseconds();

            $(this).ajaxSubmit({
                url: '<?php echo home_url('/?__=');?>' + nocache,
                success:function(res){
                    WPDM.unblockUI('.<?php echo $section_id; ?>');

                    if( res.downloadurl ) {
                        //if(window.self !== window.top)
                        //    window.parent.location.href = res.downloadurl;
                        //else
                        //    location.href = res.downloadurl;
                        var html =
                            WPDM.overlay(".<?php echo $section_id; ?>", res.msg+"<a target=_blank style=\'margin-top:5px;color:#fff !important\' class=\'btn <?php echo wpdm_download_button_style(true, $package['ID']); ?> btn-block\' href=\'"+res.downloadurl+"\'><?php echo $button_label; ?></a>");
                    } else {
                        WPDM.notify(res.msg, res.type);
                    }

                }});

            return false;
        });
    });

</script>
