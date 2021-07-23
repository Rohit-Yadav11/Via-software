<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 22/5/20 08:20
 */
if(!defined("ABSPATH")) die();
?><div style="text-align: right;padding: 10px;"><a href="#" data-toggle="modal" data-target="#newtagmodal" class="btn btn-success"><i class="fa fa-plus-circle"></i> <?php _e( "Add New Tag", "download-manager" ); ?></a></div>
<table class="table table-striped" id="tagstable">
    <thead>
    <tr>
        <th><?php _e( "Tag", "download-manager" ) ?></th>
        <th><?php _e( "Value", "download-manager" ) ?></th>
        <th><?php _e( "Action", "download-manager" ) ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    $upload_dir = wp_upload_dir();
    $upload_dir = $upload_dir['basedir'];
    $tags_dir = $upload_dir.'/wpdm-custom-tags/';
    if(!file_exists($tags_dir)) mkdir($tags_dir, 0755, true);
    $custom_tags = scandir($tags_dir);
    foreach ($custom_tags as $custom_tag){
        if(strstr($custom_tag, '.tag')) {
            $content = file_get_contents($tags_dir.$custom_tag);
            $custom_tag = str_replace(".tag", "", $custom_tag);
            ?>
            <tr id="row_<?php echo $custom_tag; ?>">
                <td>[<?php echo $custom_tag; ?>]</td>
                <td><pre style="background: #ffffff;border-radius: 3px;font-size: 10px"><?php echo htmlspecialchars(stripslashes($content)); ?></pre></td>
                <td style="width: 220px">
                    <a href="#" class="btn btn-info tag-edit" data-tag="<?php echo $custom_tag; ?>"><?php _e( "Edit", "download-manager" ); ?></a>
                    <a href="#" class="btn btn-danger tag-delete" data-tag="<?php echo $custom_tag; ?>"><?php _e( "Delete", "download-manager" ); ?></a>
                </td>
            </tr>
            <?php
        }
    } ?>
    </tbody>
</table>

<div class="modal fade" id="newtagmodal" tabindex="-1" role="dialog" aria-labelledby="preview" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" id="newtagform">
            <input type="hidden" name="action" value="wpdm_save_custom_tag">
            <input type="hidden" name="__ctxnonce" value="<?php echo wp_create_nonce(NONCE_KEY); ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel"><?php _e( "New Tag" , "download-manager" ); ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" id="tag_name" name="ctag[name]" class="form-control input-lg" placeholder="<?php echo __( "Tag Name", "download-manager" ) ?>" />
                    </div>
                    <div class="form-group">
                        <textarea id="tag_value" placeholder="<?php echo __( "Tag Value", "download-manager" ) ?>" class="form-control" style="height: 100px" name="ctag[value]"></textarea>
                        <em class="note"><?php echo __( "No php code, only text, html, css and js", "download-manager" ); ?></em>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="newtagformsubmit" style="width: 180px" class="btn btn-success btn-lg"><?php echo __( "Save Tag", "download-manager" ) ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
