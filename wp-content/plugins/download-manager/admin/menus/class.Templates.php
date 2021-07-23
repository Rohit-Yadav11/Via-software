<?php

namespace WPDM\admin\menus;


class Templates
{

    function __construct()
    {
        add_filter('template_include', array($this, 'livePreview'), 99 );
        add_filter('wdm_before_fetch_template', array($this, 'introduceCustomTags'), 99, 3 );
        //add_action('admin_init', array($this, 'Save'));
        add_action('wp_ajax_template_preview', array($this, 'preview'));
        add_action('wp_ajax_wpdm_delete_template', array($this, 'deleteTemplate'));
        add_action('wp_ajax_wpdm_save_template', array($this, 'save'));
        add_action('wp_ajax_wpdm_save_email_template', array($this, 'saveEmailTemplate'));
        add_action('wp_ajax_update_template_status', array($this, 'updateTemplateStatus'));
        add_action('wp_ajax_wpdm_save_email_setting', array($this, 'saveEmailSetting'));
        add_action('wp_ajax_wpdm_save_custom_tag', array($this, 'saveCustomTag'));
        add_action('wp_ajax_wpdm_edit_custom_tag', array($this, 'editCustomTag'));
        add_action('wp_ajax_wpdm_delete_custom_tag', array($this, 'deleteCustomTag'));
        add_action('wp_ajax_connect_template_server', array($this, 'templateServer'));
        add_action('wp_ajax_wpdm_import_template', array($this, 'importTemplate'));
        add_action('admin_menu', array($this, 'menu'));
    }

    function livePreview($page_template){

        if(wp_verify_nonce(wpdm_query_var('_tplnonce'), NONCE_KEY) && current_user_can(WPDM_ADMIN_CAP) && wpdm_query_var('template_preview') !== '') {
            add_filter( 'show_admin_bar', '__return_false' );
            remove_filter( 'the_content', 'wpautop' );

            $page_template = wpdm_tpl_path('clean-template.php');
            $type = wpdm_query_var('_type');
            global $post;
            if(wpdm_query_var('pid'))
                $package = get_post(wpdm_query_var('pid'), ARRAY_A);
            else {
                $package = get_posts(array('post_type' => 'wpdmpro', 'posts_per_page' => 1, 'post_status' => 'publish'));
                $package = (array)$package[0];
            }
            $template = $_REQUEST['template_preview'];
            $template = stripslashes_deep(str_replace(array("\r", "\n"), "", html_entity_decode(urldecode($template))));
            $output = wpdm_fetch_template($template, $package, $type);
            $post->post_content = "<div class='w3eden' style='max-width: 900px;margin: 0 auto !important;'>{$output}</div><style>body,html{ overflow-x: hidden;  } .w3eden { padding: 10px !important; }</style><script> jQuery(function($) {  var body = document.body, html = document.documentElement; var height = Math.max( body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight ); window.parent.wpdmifh(height); });</script>";
            include $page_template;
            die();
        }
        return $page_template;
    }

    function menu()
    {
        add_submenu_page('edit.php?post_type=wpdmpro', __( "Templates &lsaquo; Download Manager" , "download-manager" ), __( "Templates" , "download-manager" ), WPDM_MENU_ACCESS_CAP, 'templates', array($this, 'UI'));
    }

    function UI(){
        $ttype = isset($_GET['_type']) ? wpdm_query_var('_type') : 'link';

        if (isset($_GET['task']) && ($_GET['task'] == 'EditTemplate' || $_GET['task'] == 'NewTemplate'))
            \WPDM\admin\menus\Templates::Editor();
        else if (isset($_GET['task']) && $_GET['task'] == 'EditEmailTemplate')
            \WPDM\admin\menus\Templates::EmailEditor();
        else
            \WPDM\admin\menus\Templates::Show();
    }


    public static function Editor(){
        include(WPDM_BASE_DIR . "admin/tpls/templates/template-editor.php");
    }


    public static function EmailEditor(){
        include(WPDM_BASE_DIR . "admin/tpls/templates/email-template-editor.php");
    }


    public static function Show(){
        WPDM()->packageTemplate->covertAll()->covertAll('page');
        include(WPDM_BASE_DIR . "admin/tpls/templates/templates.php");
    }

    /**
     * @usage Delete link/page template
     * @since 4.7.0
     */

    function deleteTemplate(){
        if (current_user_can(WPDM_ADMIN_CAP)) {
            $ttype = wpdm_query_var('ttype');
            $tplid = wpdm_query_var('tplid');
            /*$tpldata = maybe_unserialize(get_option("_fm_{$ttype}_templates"));
            if (!is_array($tpldata)) $tpldata = array();
            unset($tpldata[$tplid]);
            update_option("_fm_{$ttype}_templates", $tpldata);*/
            WPDM()->packageTemplate->delete($tplid, $ttype);
            die('ok');
        }

    }


    /**
     * @usage Save Link/Page Templates
     */
    function save()
    {
        //if (!isset($_GET['page']) || $_GET['page'] != 'templates') return;

        $ttype = isset($_REQUEST['_type']) ? wpdm_query_var('_type') : 'link';

        if (isset($_REQUEST['task']) && $_REQUEST['task'] == 'DeleteTemplate') {
            WPDM()->packageTemplate->delete(wpdm_query_var('tplid'), $ttype);
            wp_send_json(array('success' => true));
        }

        if (isset($_POST['tpl'])) {

            $tplid = wpdm_query_var('tplid');
            if(!$tplid)
                wp_send_json(array('success' => false));
            if(wpdm_query_var('tplid_old') !== '' && isset($tpldata[wpdm_query_var('tplid_old')])){
                unset($tpldata[wpdm_query_var('tplid_old')]);
            }

            WPDM()->packageTemplate->delete(wpdm_query_var('tplid_old'), wpdm_query_var('_type'))
                ->add(wpdm_query_var('tplid'), wpdm_query_var('tpl/name'), wpdm_query_var('tpl/content', 'escs'), wpdm_query_var('tpl/css', 'escs'));

            wp_send_json(array('success' => true, 'message' => __( "Template is saved successfully!", "download-manager" )));

            die();
        }


    }

    function saveEmailTemplate(){
        if (isset($_POST['email_template'])) {
            $email_template = wpdm_query_var('email_template', array('validate' => array('subject' => '', 'message' => 'escs', 'from_name' => '', 'from_email' => '')));
            update_option("__wpdm_etpl_".wpdm_query_var('id'), $email_template);
            wp_send_json(array('success' => true, 'message' => 'Email Template Saved!'));
            //header("location: edit.php?post_type=wpdmpro&page=templates&_type=$ttype");
            die();
        }
    }

    /**
     * @usage Preview link/page template
     */
    function preview()
    {
        error_reporting(0);

        $wposts = array();

        if(!current_user_can(WPDM_ADMIN_CAP)) die(__( "Unauthorized Access!", "download-manager" ));

        $template = isset($_REQUEST['template'])?wpdm_query_var('template', 'escs'):'';
        $type = wpdm_query_var("_type");
        $css = wpdm_query_var("css","txt");


        $args=array(
            'post_type' => 'wpdmpro',
            'posts_per_page' => 1
        );

        $wposts = get_posts( $args  );
        $template = stripslashes($template);
        $template = urlencode($template);
        $tplnonce = wp_create_nonce(NONCE_KEY);
        $preview_link = home_url("/?template_preview={$template}&_type={$type}&_tplnonce={$tplnonce}");

        if(count($wposts)==0) $html = "<div class='w3eden'><div class='col-md-12'><div class='alert alert-info'>".__( "No package found! Please create at least 1 package to see template preview" , "download-manager" )."</div> </div></div>";
        else
            $html = "<a class='btn btn-link btn-block' href='{$preview_link}' target='_blank'><i class='fa fa-external-link-alt'></i> Preview in a new window</a><iframe id='templateiframe' src='{$preview_link}' style='border: 0;width: 100%;height: 200px;overflow: hidden'></iframe><script>function wpdmifh( h ){ jQuery('#templateiframe').height(h); }</script>";

        echo $html;
        die();

    }

    public static function dropdown($params, $activeOnly = false)
    {
        extract($params);
        $type = isset($type) && in_array($type, array('link', 'page', 'email')) ? esc_attr($type) : 'link';
        $tplstatus = maybe_unserialize(get_option("_fm_{$type}_template_status"));

        $xactivetpls = array();
        $activetpls = array();
        if(is_array($tplstatus)) {
            foreach ($tplstatus as $tpl => $active) {
                if (!$active)
                    $xactivetpls[] = $tpl;
                else
                    $activetpls[] = $tpl;
            }
        }

        $ttpldir = get_stylesheet_directory() . '/download-manager/' . $type . '-templates/';
        $ttpls = array();
        if(file_exists($ttpldir)) {
            $ttpls = scandir($ttpldir);
            array_shift($ttpls);
            array_shift($ttpls);
        }

        $ltpldir = WPDM_TPL_DIR . $type . '-templates/';
        $ctpls = scandir($ltpldir);
        array_shift($ctpls);
        array_shift($ctpls);

        foreach($ctpls as $ind => $tpl){
                $ctpls[$ind] = $ltpldir.$tpl;
        }

        foreach($ttpls as $tpl){
            if(!in_array($ltpldir.$tpl, $ctpls)) {
                    $ctpls[] = $ttpldir . $tpl;
            }
        }

        //$custom_templates = maybe_unserialize(get_option("_fm_{$type}_templates", array()));
        $custom_templates = WPDM()->packageTemplate->getCustomTemplates($type);
        $custom_templates = array_reverse($custom_templates);
        $name = isset($name)?$name:$type.'_template';
        $css = isset($css)?"style='$css'":'';
        $id = isset($id)?$id:uniqid();
        $default = $type == 'link'?'link-template-default.php':'page-template-default.php';
        $xdf = str_replace(".php", "", $default);
        $html = "";
        if(is_array($xactivetpls) && count($xactivetpls) > 0)
            $default = (in_array($xdf, $xactivetpls) || in_array($default, $xactivetpls)) && isset($activetpls[0])?$activetpls[0]:$default;

        $html .= "<select name='$name' id='$id' class='form-control template {$type}_template' {$css}><option value='$default'>Select ".ucfirst($type)." Template</option>";
        $data = array();
        if(is_array($custom_templates)) {
            foreach ($custom_templates as $id => $template) {
                if(!$activeOnly || ($activeOnly && (!isset($tplstatus[$id]) || $tplstatus[$id] == 1))) {
                    $data[$id] = $template['title'];
                    $eselected = isset($selected) && $selected == $id ? 'selected=selected' : '';
                    $html .= "<option value='{$id}' {$eselected}>{$template['title']}</option>";
                }
            }
        }
        foreach ($ctpls as $ctpl) {
            $ind = str_replace(".php", "", basename($ctpl));
            if(!$activeOnly || ($activeOnly && (!isset($tplstatus[$ind]) || $tplstatus[$ind] == 1))) {
                $tmpdata = file_get_contents($ctpl);
                $regx = "/WPDM.*Template[\s]*:([^\-\->]+)/";
                if (preg_match($regx, $tmpdata, $matches)) {
                    $data[basename($ctpl)] = $matches[1];
                    $eselected = isset($selected) && $selected == basename($ctpl) ? 'selected=selected' : '';

                    $html .= "<option value='" . basename($ctpl) . "' {$eselected}>{$matches[1]}</option>";
                }
            }
        }
        $html .= "</select>";

        return isset($data_type) && $data_type == 'ARRAY'? $data : $html;
    }

    function saveEmailSetting(){
        update_option('__wpdm_email_template', wpdm_query_var('__wpdm_email_template'));
        $email_settings = wpdm_query_var('__wpdm_email_setting', array('validate' => array('logo' => 'url', 'banner' => 'url', 'youtube' => 'url', 'twitter' => 'url', 'facebook' => 'url', 'footer_text' => 'txts')));
        update_option('__wpdm_email_setting', $email_settings);
        die("Done!");
    }

    function saveCustomTag(){
        if(wp_verify_nonce(wpdm_query_var('__ctxnonce'), NONCE_KEY) && current_user_can(WPDM_ADMIN_CAP)){
            $upload_dir = wp_upload_dir();
            $upload_dir = $upload_dir['basedir'];
            $tags_dir = $upload_dir.'/wpdm-custom-tags/';
            if(!file_exists($tags_dir)) mkdir($tags_dir, 0755, true);
            file_put_contents($tags_dir.wpdm_query_var('ctag/name', 'filename').'.tag', stripslashes($_REQUEST['ctag']['value']));
            wp_send_json(array('success' => true, 'name' => wpdm_query_var('ctag/name', 'filename'), 'value' => htmlspecialchars(stripslashes($_REQUEST['ctag']['value']))));
        }
    }

    function editCustomTag(){
        if(current_user_can(WPDM_ADMIN_CAP)){
            $upload_dir = wp_upload_dir();
            $upload_dir = $upload_dir['basedir'];
            $tags_dir = $upload_dir.'/wpdm-custom-tags/';
            if(!file_exists($tags_dir)) mkdir($tags_dir, 0755, true);
            $tag_value = file_get_contents($tags_dir.wpdm_query_var('tag', 'filename').'.tag');
            wp_send_json(array('success' => true, 'name' => wpdm_query_var('tag', 'filename'), 'value' => stripslashes($tag_value)));
        }
    }

    function deleteCustomTag(){
        if(current_user_can(WPDM_ADMIN_CAP)){
            $upload_dir = wp_upload_dir();
            $upload_dir = $upload_dir['basedir'];
            $tags_dir = $upload_dir.'/wpdm-custom-tags/';
            if(!file_exists($tags_dir)) mkdir($tags_dir, 0755, true);
            @unlink($tags_dir.wpdm_query_var('tag', 'filename').'.tag');
            wp_send_json(array('success' => true));
        }
    }

    function updateTemplateStatus(){
        if(!current_user_can(WPDM_ADMIN_CAP)) die('error');
        $type = wpdm_query_var('type');
        $tpldata = maybe_unserialize(get_option("_fm_{$type}_template_status"));
        $tpldata[wpdm_query_var('template')] = wpdm_query_var('status');
        update_option("_fm_{$type}_template_status", $tpldata);
        echo "OK";
        die();
    }

    function introduceCustomTags($vars, $template, $type){
        $upload_dir = wp_upload_dir();
        $upload_dir = $upload_dir['basedir'];
        $tags_dir = $upload_dir.'/wpdm-custom-tags/';
        if(!file_exists($tags_dir)) mkdir($tags_dir, 0755, true);
        $custom_tags = scandir($tags_dir);
        foreach ($custom_tags as $custom_tag) {
            if (strstr($custom_tag, '.tag')) {
                $content = file_get_contents($tags_dir . $custom_tag);
                $custom_tag = str_replace(".tag", "", $custom_tag);
                $vars[$custom_tag] = stripslashes($content);
            }
        }
        return $vars;
    }

    function templateServer(){
        $access = \WPDM\Settings::license_det();
        $access = json_decode($access);
//wpdmprecho($access);
        if($access->expire > time()){
        $templates = wpdm_remote_get("https://www.wpdownloadmanager.com/wpdm-templates/import.json");
        $templates = (array)json_decode($templates);
        ?>
        <div class="row">
            <?php foreach ($templates as $template => $info) { ?>
                <div class="col-md-3">
                    <div class="panel panel-default">
                        <div class="panel-body text-center">
                            <img src="<?php echo $info->preview; ?>" alt="Preview" />
                        </div>
                        <div class="panel-footer import-footer" style="position: relative">
                            <button data-template="<?php echo $template; ?>" data-type="<?php echo $info->type; ?>" class="btn btn-sm btn-import-template btn-secondary">Import</button>
                            <div style="width: calc(100% - 100px)" class="ellipsis">
                                <?php echo $info->name; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <?php
        } else {
            ?>
            <div class="panel panel-default" style="margin: 40px auto;width: 400px">
                <div class="panel-body text-center lead" style="padding: 40px;margin: 0">
                    Your order is already expired. Please renew your order to resume template import service!
                </div>
                <div class="panel-footer text-center">
                    <a class="btn btn-warning" target="_blank" href="https://www.wpdownloadmanager.com/user-dashboard/?udb_page=purchases/order/<?php echo $access->order_id ?>/">Renew Order Now</a>
                </div>
            </div>
            <?php
        }
        die();
    }

    function importTemplate(){
        if(!wpdm_query_var('template'))
            wp_send_json(['success' => false]);
        $template = "https://www.wpdownloadmanager.com/wpdm-templates/".wpdm_query_var('template').".xml";
        WPDM()->packageTemplate->import($template, wpdm_query_var('template_type'));
        wp_send_json(['success' => true]);
    }
}
