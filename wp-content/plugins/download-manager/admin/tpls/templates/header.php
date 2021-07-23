<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 22/5/20 08:18
 */
if(!defined("ABSPATH")) die();
?>
        <div class="panel-heading">
            <b><i class="fa fa-magic color-purple"></i> &nbsp; <?php echo __( "Templates" , "download-manager" ); ?></b>
            <div class="pull-right">
                <a href="edit.php?post_type=wpdmpro&page=templates&_type=page&task=NewTemplate" class="btn btn-sm btn-secondary"><i class="fa fa-file"></i> <?php echo __( "Create Page Template" , "download-manager" ); ?></a>
                <a href="edit.php?post_type=wpdmpro&page=templates&_type=link&task=NewTemplate" class="btn btn-sm btn-secondary"><i class="fa fa-link"></i> <?php echo __( "Create Link Template" , "download-manager" ); ?></a>
                <a href="edit.php?post_type=wpdmpro&page=templates&_type=import" class="btn btn-sm btn-primary"><i class="fa fa-file-import"></i> <?php echo __( "Import Template" , "download-manager" ); ?></a>
            </div>
            <div style="clear: both"></div>
        </div>
        <ul id="tabs" class="nav nav-tabs nav-wrapper-tabs" style="padding: 60px 10px 0 10px;background: #f5f5f5">
            <li <?php if(!isset($_GET['_type'])||$_GET['_type']=='link'){ ?>class="active"<?php } ?>><a href="edit.php?post_type=wpdmpro&page=templates&_type=link" id="link"><?php _e( "Link Templates" , "download-manager" ); ?></a></li>
            <li <?php if(isset($_GET['_type'])&&$_GET['_type']=='page'){ ?>class="active"<?php } ?>><a href="edit.php?post_type=wpdmpro&page=templates&_type=page" id="page"><?php _e( "Page Templates" , "download-manager" ); ?></a></li>
            <li <?php if(isset($_GET['_type'])&&$_GET['_type']=='email'){ ?>class="active"<?php } ?>><a href="edit.php?post_type=wpdmpro&page=templates&_type=email" id="email"><?php _e( "Email Templates" , "download-manager" ); ?></a></li>
            <li <?php if(isset($_GET['_type'])&&$_GET['_type']=='custom-tags'){ ?>class="active"<?php } ?>><a href="edit.php?post_type=wpdmpro&page=templates&_type=custom-tags" id="custom-tags"><?php _e( "Custom Tags" , "download-manager" ); ?></a></li>
            <?php if(wpdm_query_var('_type') === 'import'){ ?>
                <li class="active"><a href="edit.php?post_type=wpdmpro&page=templates&_type=import" id="import"><?php _e( "Import Template" , "download-manager" ); ?></a></li>
            <?php } ?>
        </ul>
        <div class="tab-content panel-body">

