<?php

if(!defined("ABSPATH")) die();
if (!is_user_logged_in()) die();

?><div class="w3eden author-dashbboard">
    <?php

        $menu_url = get_permalink(get_the_ID()).$sap.'adb_page=%s';

        if(isset($params['flaturl']) && $params['flaturl'] == 1)
            $menu_url = rtrim(get_permalink(get_the_ID()), '/').'/%s/';

        $store = get_user_meta(get_current_user_id(), '__wpdm_public_profile', true);


        do_action("wpdm_author_dashboard_page_before");


    ?>
<div class="row">
    <div id="wpdm-dashboard-sidebar" class="col-md-3">
    <?php if(isset($store['logo']) && $store['logo'] != ''){ ?>
        <div id="logo-block">
        <img style="margin-bottom: 10px;border-radius: 4px" class="thumbnail shop-logo m-0 p-0" id="shop-logo" src="<?php echo $store['logo']; ?>"/>
        </div>
    <?php } ?>

    <div id="tabs" class="list-group" style="margin: 0;padding: 0">
        <?php foreach ($tabs as $tid => $tab): ?>
            <a class="adp-item <?php if ($task == $tid) { ?>active<?php } ?>" href="<?php echo $tid != ''?sprintf("$menu_url", $tid):get_permalink(get_the_ID()); ?>"><i class="<?php echo isset($default_icons[$tid]) ? $default_icons[$tid] : 'fa fa-bars'; ?> mr-3"></i><?php echo $tab['label']; ?></a>
        <?php endforeach; ?>

        <a class="adp-item <?php if ($task == 'edit-profile') { ?>active<?php } ?>" href="<?php echo sprintf("$menu_url", "edit-profile"); ?>"><i class="fa fa-user-edit color-purple mr-3"></i><?php _e( "Edit Profile" , "download-manager" ); ?></a>

        <a class="adp-item <?php if ($task == 'settings') { ?>active<?php } ?>" href="<?php echo sprintf("$menu_url", "settings"); ?>"><i class="fa fa-tools color-info mr-3"></i><?php _e( "Settings" , "download-manager" ); ?></a>
        <a class="adp-item" href="<?php echo wpdm_logout_url(); ?>"><i class="fas fa-sign-out-alt color-danger mr-3"></i><?php _e( "Logout" , "download-manager" ); ?></a>
    </div>

    </div>
    <div id="wpdm-dashboard-content" class="col-md-9">

<?php

    do_action("wpdm_author_dashboard_content_before");

    if ($task == 'add-new' || $task == 'edit-package')
        include(wpdm_tpl_path('wpdm-add-new-file-front.php'));
    else if ($task == 'edit-profile')
        include(wpdm_tpl_path('wpdm-edit-user-profile.php'));
    else if ($task == 'settings')
       echo do_shortcode("[wpdm_author_settings]");
    else if ($task != '' && isset($tabs[$task]['callback']) && $tabs[$task]['callback'] != '')
        call_user_func($tabs[$task]['callback']);
    else if ($task != '' && isset($tabs[$task]['shortcode']) && $tabs[$task]['shortcode'] != '')
        echo do_shortcode($tabs[$task]['shortcode']);
    //else
        //include(wpdm_tpl_path('list-packages-table.php'));

    do_action("wpdm_author_dashboard_content_after");
?>

    </div>
    </div>

    <script>jQuery(function($){ $("#tabs > li > a").click(function(){ location.href=this.href; });  });</script>

</div>

