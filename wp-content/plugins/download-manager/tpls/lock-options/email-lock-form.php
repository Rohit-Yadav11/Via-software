<?php
/**
 * Author: shahnuralam
 * Date: 2018-12-28
 * Time: 11:58
 */
if (!defined('ABSPATH')) die();

$already_verified = \WPDM\Session::get('__wpdm_email_lock_verified');
$idl = isset($package['email_lock_idl']) ? (int)$package['email_lock_idl'] : 0;
if(is_array($already_verified) && isset($already_verified['email']) && $idl === 1){
    ?>
    <a class="btn btn-success btn-lg btn-block" href="<?php echo \WPDM\Package::expirableDownloadLink($package['ID'], 3); ?>&__wpdm_els=1"><?php echo $package['link_label']; ?></a>
    <?php
} else {

    $section_id = "email_lock_{$unqid}{$package['ID']}";
    $form_id = "wpdmdlf_{$field_id}";
    $custom_form_field = maybe_unserialize(get_post_meta($package['ID'], '__wpdm_custom_form_field', true));
    $show_name = ( is_array($custom_form_field) && isset($custom_form_field['name']) && (int)$custom_form_field['name'] === 1 );
    $custom_form_fields =  '';
    $custom_form_fields = apply_filters('wpdm_render_custom_form_fields', $custom_form_fields, $package['ID']);

?>

<div id="<?php echo $section_id;?>" class="<?php echo $section_id;?>">
<form id="<?php echo $form_id; ?>" class="<?php echo $form_id; ?>" method=post style="font-weight:normal;font-size:12px;padding:0px;margin:0px">
    <div class="card card-default">
        <div class="card-header bg-light">
            <?php echo $section_title; ?>
        </div>
        <div class="card-body">

             <?php echo $intro; ?>

            <input type=hidden name="__wpdm_ID" value="<?php echo $package['ID']; ?>" />
            <input type=hidden name="dataType" value="json" />
            <input type=hidden name="execute" value="wpdm_getlink" />
            <input type=hidden name="verify" value="email" />
            <input type=hidden name="action" value="wpdm_email_lock" />

            <?php
            if($show_name){ ?>
                <div class="form-group">
                    <div class="input-wrapper text-input-wrapper">
                        <label form="log"><?php echo __( "Your Name", "download-manager" ) ?>:</label>
                        <input type="text" name="name" id="name" required="required" placeholder="<?php _e( "Your Full Name", "download-manager" ) ?>" class="form-control email-lock-name">
                    </div>
                </div>
            <?php }
            echo $custom_form_fields; ?>

            <div class="form-group">
                <div class="input-wrapper text-input-wrapper">
                    <label form="log"><?php echo apply_filters("email_lock_email_field_label",__( "Email" , "download-manager" ), $package['ID']); ?>:</label>
                    <input type="email" required="required"  title="<?php echo __( "Enter a valid email address" , "download-manager" ) ?>" class="form-control group-item email-lock-mail" placeholder="<?php _e("Email Address", "download-manager"); ; ?>" size="20" id="email_<?php echo $field_id; ?>" name="email" />
                </div>
            </div>
            <button id="wpdm_submit_<?php echo $field_id; ?>" class="wpdm_submit btn btn-primary btn-block btn-lg group-item"  type=submit><?php echo $form_button_label; ?></button>
        </div>
    </div>
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
                url: '<?php echo admin_url('/admin-ajax.php') ?>',
                success:function(res){

                    WPDM.unblockUI('.<?php echo $section_id; ?>');

                    if( res.downloadurl ) {
                        //if(window.self !== window.top)
                        //    window.parent.location.href = res.downloadurl;
                        //else
                        //    location.href = res.downloadurl;
                        var html =
                            WPDM.overlay(".<?php echo $section_id; ?>", "<div class='card'><div class='card-body text-center'>"+res.msg+"</div><div class='card-footer'><a target=_blank style=\'margin-top:5px;color:#fff !important\' class=\'btn btn-success btn-lg btn-block\' href=\'"+res.downloadurl+"\'><?php echo $button_label; ?></a></div></div>");
                    } else {
                        var msg = "<div class='media' style='font-size: 10pt;line-height: 18px;font-weight: 400'><div class='mr-3'><i class='fa fa-check-double' style='font-size: 32px'></i></div><div class='media-body'><strong style='font-size: 11pt;font-weight: 600;'>Success!</strong><br/>"+res.msg+"</div></div>"
                        WPDM.notify(msg, res.type);
                    }

                }});

            return false;
        });
    });

</script>

<?php
}
