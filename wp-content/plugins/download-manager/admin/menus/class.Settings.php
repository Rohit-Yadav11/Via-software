<?php

namespace WPDM\admin\menus;


use WPDM\Installer;
use WPDM\Session;

class Settings
{

    function __construct()
    {
        add_action('admin_init', array($this, 'initiateSettings'));
        add_action('admin_init', array($this, 'saveSettingsAuth'), 1);
        add_action('wp_ajax_wpdm_settings', array($this, 'loadSettingsPage'));
        add_action('admin_menu', array($this, 'Menu'));
    }

    function Menu(){
        add_submenu_page('edit.php?post_type=wpdmpro', __( "Settings &lsaquo; Download Manager" , "download-manager" ), __( "Settings" , "download-manager" ), WPDM_ADMIN_CAP, 'settings', array($this, 'UI'));
    }

    function saveSettingsAuth(){
        if(wpdm_query_var('task') === 'wdm_save_settings' && !current_user_can('manage_options')) die(__( "You are not allowed to change settings!", "download-manager" ));
    }

    function loadSettingsPage()
    {
        global $stabs;
        //$stabs['plugin-update']['callback'] = array($this, 'pluginUpdate');
        if (current_user_can(WPDM_MENU_ACCESS_CAP)) {
            $section = wpdm_query_var('section');
            if(isset($stabs[$section], $stabs[$section]['callback']))
                call_user_func($stabs[$section]['callback']);
            else "<div class='panel panel-danger'><div class='panel-body color-red'><i class='fa fa-exclamation-triangle'></i> ".__( "Something is wrong!", "download-manager" )."</div></div>";
        }
        die();
    }

    function UI(){
        include(WPDM_BASE_DIR . 'admin/tpls/settings.php');
    }

    /**
     * @param $tabid
     * @param $tabtitle
     * @param $callback
     * @param string $icon
     * @return array
     */
    public static function createMenu($tabid, $tabtitle, $callback, $icon = 'fa fa-cog')
    {
        return array('id' => $tabid, 'icon'=>$icon, 'link' => 'edit.php?post_type=wpdmpro&page=settings&tab=' . $tabid, 'title' => $tabtitle, 'callback' => $callback);
    }


    /**
     * @usage Initiate Settings Tabs
     */
    function initiateSettings()
    {
        global $stabs;
        $tabs = array();
        $tabs['basic'] = array('id' => 'basic','icon'=>'fa fa-cog', 'link' => 'edit.php?post_type=wpdmpro&page=settings', 'title' => 'Basic', 'callback' => array($this, 'Basic'));
        $tabs['wpdmui'] = array('id' => 'wpdmui','icon'=>'fas fa-fill-drip', 'link' => 'edit.php?post_type=wpdmpro&page=settings', 'title' => 'User Interface', 'callback' => array($this, 'userInterface'));
        $tabs['frontend'] = array('id' => 'frontend','icon'=>'fa fa-desktop', 'link' => 'edit.php?post_type=wpdmpro&page=settings&tab=frontend', 'title' => 'Frontend Access', 'callback' => array($this, 'Frontend'));
        $tabs['social-connects'] = array('id' => 'social-connects','icon'=>'fab fa-twitter', 'link' => 'edit.php?post_type=wpdmpro&page=settings&tab=social-connects', 'title' => 'Social Settings', 'callback' => array($this, 'socialConnects'));

        // Add buddypress settings menu when buddypress plugin is active
        if (function_exists('bp_is_active')) {
            $tabs['buddypress'] = array('id' => 'buddypress','icon'=>'fa fa-users', 'link' => 'edit.php?post_type=wpdmpro&page=settings&tab=buddypress', 'title' => 'BuddyPress', 'callback' => array($this, 'Buddypress'));
        }

        if(defined('WPDM_CLOUD_STORAGE')){
            $tabs['cloud-storage'] = array('id' => 'cloud-storage','icon'=>'fa fa-cloud',  'link' => 'edit.php?post_type=wpdmpro&page=settings&tab=cloud-storage', 'title' => 'Cloud Storage', 'callback' => array($this, 'cloudStorage'));
        }

        if(!$stabs) $stabs = array();


        $stabs = $tabs + $stabs;

        $stabs = apply_filters("add_wpdm_settings_tab", $stabs);

        $stabs['plugin-update'] = array('id' => 'plugin-update','icon'=>'fa fa-sync',  'link' => 'edit.php?post_type=wpdmpro&page=settings&tab=plugin-update', 'title' => 'Updates', 'callback' => array($this, 'pluginUpdate'));
        $stabs['license'] = array('id' => 'license','icon'=>'fa fa-key',  'link' => 'edit.php?post_type=wpdmpro&page=settings&tab=license', 'title' => 'License', 'callback' => array($this, 'License'));
        $stabs['privacy'] = array('id' => 'privacy','icon'=>'fas fa-user-shield',  'link' => 'edit.php?post_type=wpdmpro&page=settings&tab=privacy', 'title' => 'Privacy', 'callback' => array($this, 'privacy'));

    }


    /**
     * @usage  Admin Settings Tab Helper
     * @param string $sel
     */
    public static function renderMenu($sel = '')
    {
        global $stabs;

        foreach ($stabs as $tab) {
            if ($sel == $tab['id'])
                echo "<li class='active'><a id='{$tab['id']}' href='{$tab['link']}'><i class='{$tab['icon']}' style='margin-right: 6px'></i>{$tab['title']}</a></li>";
            else
                echo "<li class=''><a id='{$tab['id']}' href='{$tab['link']}'><i class='{$tab['icon']}' style='margin-right: 6px'></i>{$tab['title']}</a></li>";
            //if (isset($tab['func']) && function_exists($tab['func'])) {
            //    add_action('wp_ajax_' . $tab['func'], $tab['func']);
            //}
        }
    }

    function Basic(){

        if (isset($_POST['task']) && $_POST['task'] == 'wdm_save_settings') {

            if(!current_user_can('manage_options')) die(__( "You are not allowed to change settings!", "download-manager" ));

            if(!wp_verify_nonce($_POST['__wpdms_nonce'], NONCE_KEY)) die(__('Security token is expired! Refresh the page and try again.', 'download-manager'));

            if ($_POST['__wpdm_curl_base'] == '') $_POST['__wpdm_curl_base'] = 'wpdm-category';
            if ($_POST['__wpdm_purl_base'] == '') $_POST['__wpdm_purl_base'] = 'wpdm-package';
            if ($_POST['__wpdm_curl_base'] == $_POST['__wpdm_purl_base']) $_POST['__wpdm_curl_base'] .= 's';
            foreach ($_POST as $optn => $optv) {
                if(strpos("__".$optn, '_wpdm_')) {
                    $optv = wpdm_sanitize_array($optv);
                    update_option($optn, $optv);
                }
            }

            WPDM()->apply->sfbAccess();

            if (!isset($_POST['__wpdm_skip_locks'])) delete_option('__wpdm_skip_locks');
            if (!isset($_POST['__wpdm_login_form'])) delete_option('__wpdm_login_form');
            if (!isset($_POST['__wpdm_cat_desc'])) delete_option('__wpdm_cat_desc');
            if (!isset($_POST['__wpdm_cat_img'])) delete_option('__wpdm_cat_img');
            if (!isset($_POST['__wpdm_cat_tb'])) delete_option('__wpdm_cat_tb');
            flush_rewrite_rules();
            global $wp_rewrite, $WPDM;
            $WPDM->registerPostTypeTaxonomy();
            $wp_rewrite->flush_rules();
            die('Settings Saved Successfully');
        }

        if(Installer::dbUpdateRequired()){
            Installer::updateDB();
        }

        include(WPDM_BASE_DIR.'admin/tpls/settings/basic.php');

    }

    function userInterface(){

        if (isset($_POST['task']) && $_POST['task'] == 'wdm_save_settings' && current_user_can(WPDM_ADMIN_CAP)) {

            if(!wp_verify_nonce($_POST['__wpdms_nonce'], NONCE_KEY)) die(__('Security token is expired! Refresh the page and try again.', 'download-manager'));

            foreach ($_POST as $optn => $optv) {
                if(strpos("__".$optn, '_wpdm_')) {
                    $optv = wpdm_sanitize_array($optv);
                    //echo $optn."=".$optv."<br/>";
                    update_option($optn, $optv);
                }
            }

            die(__( "Settings Saved Successfully", "download-manager" ));
        }
        include(WPDM_BASE_DIR.'admin/tpls/settings/user-interface.php');

    }


    function Frontend(){
        if(isset($_POST['section']) && $_POST['section']=='frontend' && isset($_POST['task']) && $_POST['task']=='wdm_save_settings' && current_user_can(WPDM_ADMIN_CAP)){

            if(!wp_verify_nonce($_POST['__wpdms_nonce'], NONCE_KEY)) die(__('Security token is expired! Refresh the page and try again.', 'download-manager'));

            foreach($_POST as $k => $v){
                if(strpos("__".$k, '_wpdm_')){
                    $v = wpdm_sanitize_array($v);
                    update_option($k, $v, false);
                }
            }



            global $wp_roles;

            $roleids = array_keys($wp_roles->roles);
            $roles = maybe_unserialize(get_option('__wpdm_front_end_access',array()));
            $naroles = array_diff($roleids, $roles);

            foreach($roles as $role) {
                $role = get_role($role);
                if(is_object($role))
                    $role->add_cap('upload_files');
            }

            foreach($naroles as $role) {
                $role = get_role($role);
                if(!isset($role->capabilities['edit_posts']) || $role->capabilities['edit_posts']!=1)
                    $role->remove_cap('upload_files');
            }

            $refresh = 0;

            $page_id = $_POST['__wpdm_user_dashboard'];
            if($page_id != '') {
                $page_name = get_post_field("post_name", $page_id);
                add_rewrite_rule('^' . $page_name . '/(.+)/?', 'index.php?page_id=' . $page_id . '&udb_page=$matches[1]', 'top');
                $refresh = 1;
            }

            $page_id = $_POST['__wpdm_author_dashboard'];
            if($page_id != '') {
                $page_name = get_post_field("post_name", $page_id);
                add_rewrite_rule('^' . $page_name . '/(.+)/?', 'index.php?page_id=' . $page_id . '&adb_page=$matches[1]', 'top');
                $refresh = 1;
            }

            $page_id = $_POST['__wpdm_author_profile'];
            if((int)$page_id > 0) {
                $page_name = get_post_field("post_name", $page_id);
                add_rewrite_rule('^' . $page_name . '/(.+)/?$', 'index.php?pagename=' . $page_name . '&profile=$matches[1]', 'top');
                $refresh = 1;
            }

            if($refresh == 1){
                global $wp_rewrite;
                $wp_rewrite->flush_rules(true);
            }

            die('Settings Saved Successfully!');
        }
        include(WPDM_BASE_DIR."admin/tpls/settings/frontend.php");
    }

    function socialConnects(){
        if(isset($_POST['section']) && $_POST['section']=='social-connects' && isset($_POST['task']) && $_POST['task']=='wdm_save_settings' && current_user_can(WPDM_ADMIN_CAP)){

            if(!wp_verify_nonce($_POST['__wpdms_nonce'], NONCE_KEY)) die(__('Security token is expired! Refresh the page and try again.', 'download-manager'));

            foreach($_POST as $k => $v){
                if(strpos("__".$k, '_wpdm_')){
                    update_option($k, wpdm_sanitize_array($v), false);
                }
            }
            die('Settings Saved Successfully!');
        }
        include(WPDM_BASE_DIR . "admin/tpls/settings/social-connects.php");
    }

    function Buddypress(){
        if(isset($_POST['section']) && $_POST['section']=='buddypress' && isset($_POST['task']) && $_POST['task']=='wdm_save_settings' && current_user_can(WPDM_ADMIN_CAP)){

            if(!wp_verify_nonce($_POST['__wpdms_nonce'], NONCE_KEY)) die(__('Security token is expired! Refresh the page and try again.', 'download-manager'));

            foreach($_POST as $k => $v){
                if(strpos("__".$k, '_wpdm_')){
                    update_option($k, wpdm_sanitize_array($v), false);
                }
            }
            die('Settings Saved Successfully!');
        }
        include(WPDM_BASE_DIR . "admin/tpls/settings/buddypress.php");
    }

    function cloudStorage(){
        if(isset($_POST['section']) && $_POST['section']=='cloud-storage' && isset($_POST['task']) && $_POST['task']=='wdm_save_settings' && current_user_can(WPDM_ADMIN_CAP)){

            if(!wp_verify_nonce($_POST['__wpdms_nonce'], NONCE_KEY)) die(__('Security token is expired! Refresh the page and try again.', 'download-manager'));

            foreach($_POST as $k => $v){
                if(strpos("__".$k, '_wpdm_')){
                    update_option($k, wpdm_sanitize_array($v), false);
                }
            }
            die('Settings Saved Successfully!');
        }
        include(WPDM_BASE_DIR . "admin/tpls/settings/cloud-storage.php");
    }

    function pluginUpdate(){
        if(isset($_REQUEST['logout']) && $_REQUEST['logout'] == 1){
            delete_option('__wpdm_suname');
            delete_option('__wpdm_supass');
            delete_option('__wpdm_purchased_items');
            delete_option('__wpdm_freeaddons');
            delete_option('__wpdm_core_update_check');
            delete_option('__wpdm_access_token');
            Session::clear('__wpdm_download_url');
            die('<script>location.href="edit.php?post_type=wpdmpro&page=settings&tab=plugin-update";</script>Refreshing...');
        }

        if(isset($_POST['__wpdm_suname']) && $_POST['__wpdm_suname'] != ''){

            if(!wp_verify_nonce($_POST['__wpdms_nonce'], NONCE_KEY)) die(__('Security token is expired! Refresh the page and try again.', 'download-manager'));

            update_option('__wpdm_suname',$_POST['__wpdm_suname'], false);
            update_option('__wpdm_supass',$_POST['__wpdm_supass'], false);
            delete_option('__wpdm_purchased_items');
            delete_option('__wpdm_freeaddons');
            delete_option('__wpdm_core_update_check');
            delete_option('__wpdm_access_token');
            Session::clear('__wpdm_download_url');
            $access_token = wpdm_access_token();
            if($access_token != '') {
                $purchased_items = wpdm_remote_get('https://www.wpdownloadmanager.com/?wpdmppaction=getpurchaseditems&wpdm_access_token=' . $access_token);
                $ret = json_decode($purchased_items);
                update_option('__wpdm_purchased_items', $purchased_items, false);
                die('<script>location.href=location.href;</script>Login successful. Refreshing...');
            }  else{
                die('Error: Invalid Login!');
            }

        }
        if(isset($_POST['__wpdm_suname'], $_POST['__wpdm_supass']) && empty($_POST['__wpdm_suname'])){
            die('Error: Must enter a valid username!');
        }

        if(get_option('__wpdm_suname') != '') {
            $purchased_items = get_option('__wpdm_purchased_items', false);
            if(!$purchased_items || wpdm_query_var('newpurchase') != '' ) {
                $purchased_items = wpdm_remote_get('https://www.wpdownloadmanager.com/?wpdmppaction=getpurchaseditems&wpdm_access_token=' . wpdm_access_token());
                update_option('__wpdm_purchased_items', $purchased_items, false);
                delete_option('wpdm_latest');
                delete_option('wpdm_latest_check');
            }
            $purchased_items = json_decode($purchased_items);
            //wpdmdd($purchased_items);
            if (isset($purchased_items->error)){ delete_option('__wpdm_suname');  delete_option('__wpdm_purchased_items'); }
            if (isset($purchased_items->error)) $purchased_items->error = str_replace("[redirect]", admin_url("edit.php?post_type=wpdmpro&page=settings&tab=plugin-update"), $purchased_items->error);
        }
        if(get_option('__wpdm_freeaddons') == '' || wpdm_query_var('newpurchase') != '') {
            $freeaddons = wpdm_remote_get('https://www.wpdownloadmanager.com/?wpdm_api_req=getPackageList&cat_id=1148');
            update_option('__wpdm_freeaddons', $freeaddons, false);
        }
        $freeaddons = json_decode(get_option('__wpdm_freeaddons'));
        include(WPDM_BASE_DIR . 'admin/tpls/settings/addon-update.php');
    }

    function License()
    {
        if (isset($_POST['task']) && $_POST['task'] == 'wdm_save_settings') {

            if (is_valid_license_key()) {
                delete_option('__wpdm_core_update_check');
                Session::clear('__wpdm_download_url');
                update_option('_wpdm_license_key', esc_attr($_POST['_wpdm_license_key']), false);
                die('Congratulation! Your <b>Download Manager</b> copy registered successfully!');
            } else {
                delete_option('_wpdm_license_key');
                die('Invalid License Key!');
            }
        }
        ?>
        <div class="panel panel-default">

            <div class="panel-heading"><b>License Key&nbsp;</b></div>
            <div class="panel-body"><input type="text" placeholder="Enter License Key" class="form-control input-lg" value="<?php echo get_option('_wpdm_license_key'); ?>"
                                           name="_wpdm_license_key"/></div>

        </div>
        <?php
    }

    function Privacy(){
        if (wpdm_query_var('task') == 'wdm_save_settings' && wpdm_query_var('section') == 'privacy') {
            foreach ($_POST as $key => $value){
                if(strstr($key, '_wpdm_')){
                    $value = wpdm_sanitize_array($value);
                    update_option($key, $value, false);
                }
            }
            _e("Privacy Settings Saved Successfully", "download-manager");
            die();
        }
        include(WPDM_BASE_DIR . 'admin/tpls/settings/privacy.php');
    }


}
