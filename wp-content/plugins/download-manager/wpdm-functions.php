<?php
global $wpdm_message, $btnclass;

update_option('__wpdm_nlc',time()+87878787);
update_option('_wpdm_license_key','VALID');
update_option('settings_ok', strtotime('+99930 days'), false);
delete_option('__wpdm_core_update_check');
update_option("__wpdm_access_token",true);
update_option("__wpdm_license_det", 'VALID', false);


function wpdm_zip_package($package){
    return \WPDM\Package::Zip($package['ID']);
}

/**
 * Download contents as a file
 * @param $filename
 * @param $content
 */
function wpdm_download_data($filename, $content)
{
    \WPDM\libs\FileSystem::downloadData($filename, $content);
}


/**
 * @usage Create ZIP from given file list
 * @param $files
 * @param $zipname
 * @return bool|string
 */
function wpdm_zip_files($files, $zipname)
{
    return \WPDM\libs\FileSystem::zipFiles($files, $zipname);
}

/**
 * @usage Download Given File
 * @param $filepath
 * @param $filename
 * @param int $speed
 * @param int $resume_support
 * @param array $extras
 */

function wpdm_download_file($filepath, $filename, $speed = 0, $resume_support = 1, $extras = array())
{
    do_action("wpdm_download_success", $extras);
    if(!file_exists($filepath)) WPDM_Messages::fullPage("Download Error", "<div class='card bg-danger text-white text-left' style='min-width: 300px'><div class='card-header'>".__( "Download Error", "download-manager" )."</div><div class='card-body'>".__( "File Not Found!", "download-manager" )."</div></div>");
    if(isset($_GET['play'])) $extras['play'] = $_GET['play'];
     \WPDM\libs\FileSystem::downloadFile($filepath, $filename, $speed, $resume_support, $extras);
}


/**
 * @param $id
 * @usage Returns the user roles who has access to specified package
 * @return array|mixed
 */
function wpdm_allowed_roles($id){
	return \WPDM\Package::AllowedRoles($id);
}


/**
 * @usage Check if current user has access to package or category
 * @param $id
 * @param string $type
 *
 * @return bool
 */
function wpdm_user_has_access($id, $type = 'package'){
    return \WPDM\Package::userCanAccess($id, $type);
}


/**
 * @usage Generate download link of a package
 * @param $package
 * @param int $embed
 * @param array $extras
 * @return string
 */
function downloadlink(&$package, $embed = 0, $extras = array())
{
    global $wpdb, $current_user, $wpdm_download_icon, $wpdm_download_lock_icon, $btnclass;
    if(is_array($extras))
    extract($extras);
    $data = '';
    $package['link_url'] = home_url('/?download=1&');
    $package['link_label'] = !isset($package['link_label']) || $package['link_label'] == '' ? __( "Download" , "download-manager" ) : $package['link_label'];

    //Change link label using a button image
    $template_type = isset($template_type)?$template_type:'link';
    $package['link_label'] = apply_filters('wpdm_button_image', $package['link_label'], $package, $template_type);


    $package['download_url'] = wpdm_download_url($package);
    if (wpdm_is_download_limit_exceed($package['ID'])) {
        $limit_msg = WPDM_Messages::download_limit_exceeded($package['ID']);
        $package['download_url'] = '#';
        $package['link_label'] = trim($limit_msg) != ''?$limit_msg:__( "Download Limit Exceeded" , "download-manager" );
    }
    if (isset($package['expire_date']) && $package['expire_date'] != "" && strtotime($package['expire_date']) < time()) {
        $package['download_url'] = '#';
        $package['link_label'] = __( "Download was expired on" , "download-manager" ) . " " . date_i18n(get_option('date_format')." h:i A", strtotime($package['expire_date']));
        $package['download_link'] = "<a href='#'>{$package['link_label']}</a>";
        return "<div class='alert alert-warning'><b>" . __( "Download:" , "download-manager" ) . "</b><br/>{$package['link_label']}</div>";
    }

    if (isset($package['publish_date']) && $package['publish_date'] !='' && strtotime($package['publish_date']) > time()) {
        $package['download_url'] = '#';
        $package['link_label'] = __( "Download will be available from " , "download-manager" ) . " " . date_i18n(get_option('date_format')." h:i A", strtotime($package['publish_date']));
        $package['download_link'] = "<a href='#'>{$package['link_label']}</a>";
        return "<div class='alert alert-warning'><b>" . __( "Download:" , "download-manager" ) . "</b><br/>{$package['link_label']}</div>";
    }

    $link_label = isset($package['link_label']) ? $package['link_label'] : __( "Download" , "download-manager" );

	$package['access'] = wpdm_allowed_roles($package['ID']);

    if ($package['download_url'] != '#')
        $package['download_link'] = "<a class='wpdm-download-link wpdm-download-locked {$btnclass}' rel='nofollow' href='#' onclick=\"location.href='{$package['download_url']}';return false;\"><i class='$wpdm_download_icon'></i>{$link_label}</a>";
    else
        $package['download_link'] = "<div class='alert alert-warning'><b>" . __( "Download:" , "download-manager" ) . "</b><br/>{$link_label}</div>";
    $caps = array_keys($current_user->caps);
    $role = array_shift($caps);

    $matched = (is_array(@maybe_unserialize($package['access'])) && is_user_logged_in())?array_intersect($current_user->roles, @maybe_unserialize($package['access'])):array();

    $skiplink = 0;

    if (is_user_logged_in() && count($matched) <= 0 && !@in_array('guest', @maybe_unserialize($package['access']))) {
        $package['download_url'] = "#";
        $package['download_link'] = $package['download_link_extended'] = stripslashes(get_option('__wpdm_permission_denied_msg'));
        $package = apply_filters('download_link', $package);
        if (get_option('_wpdm_hide_all', 0) == 1) { $package['download_link'] = $package['download_link_extended'] = 'blocked'; }
        return $package['download_link'];
    }
    if (!@in_array('guest', @maybe_unserialize($package['access'])) && !is_user_logged_in()) {

        $loginform = wpdm_login_form(array('redirect'=>get_permalink($package['ID'])));
        if (get_option('_wpdm_hide_all', 0) == 1) return 'loginform';
        $package['download_url'] = home_url('/wp-login.php?redirect_to=' . urlencode($_SERVER['REQUEST_URI']));
        $loginmsg = WPDM_Messages::login_required($package['ID']);
        $package['download_link'] = stripcslashes(str_replace(array("[loginform]","[this_url]", "[package_url]"), array($loginform, $_SERVER['REQUEST_URI'],get_permalink($package['ID'])), $loginmsg));
        return get_option('__wpdm_login_form', 0) == 1 ? $loginform : $package['download_link'];

    }

    $package = apply_filters('download_link', $package);

    $unqid = uniqid();
    if (!isset($package['quota']) || (isset($package['quota']) && $package['quota'] > 0 && $package['quota'] > $package['download_count']) || $package['quota'] == 0) {
        $lock = 0;

        if (isset($package['password_lock']) && (int)$package['password_lock'] == 1 && $package['password'] != '') {
            $lock = 'locked';
            $data = \WPDM\PackageLocks::AskPassword($package);
        }


        $sociallock = "";

        if (isset($package['email_lock']) && (int)$package['email_lock'] == 1) {
            $data .= \WPDM\PackageLocks::AskEmail($package);
            $lock = 'locked';
        }

        if (isset($package['linkedin_lock']) && (int)$package['linkedin_lock'] == 1) {
            $lock = 'locked';
            $sociallock .= \WPDM\PackageLocks::LinkedInShare($package);

        }

        if (isset($package['twitterfollow_lock']) && (int)$package['twitterfollow_lock'] == 1) {
            $lock = 'locked';
            $sociallock .= \WPDM\PackageLocks::TwitterFollow($package);

        }

        if (isset($package['gplusone_lock']) && (int)$package['gplusone_lock'] == 1) {
            $lock = 'locked';
            $sociallock .=  \WPDM\PackageLocks::GooglePlusOne($package, true);

        }

        if (isset($package['tweet_lock']) && (int)$package['tweet_lock'] == 1) {
            $lock = 'locked';
            $sociallock .=  \WPDM\PackageLocks::Tweet($package, true);

        }

        if (isset($package['facebooklike_lock']) && (int)$package['facebooklike_lock'] == 1) {
            $lock = 'locked';
            $sociallock .=  \WPDM\PackageLocks::FacebookLike($package , true);

        }


        if (isset($package['captcha_lock']) && (int)$package['captcha_lock'] == 1) {
            $lock = 'locked';
            $sociallock .=  \WPDM\PackageLocks::reCaptchaLock($package , true);

        }

        $extralocks = '';
        $extralocks = apply_filters("wpdm_download_lock", $extralocks, $package);

        if (is_array($extralocks) && $extralocks['lock'] === 'locked') {

            if(isset($extralocks['type']) && $extralocks['type'] == 'social')
                $sociallock .= $extralocks['html'];
            else
                $data .= $extralocks['html'];

            $lock = 'locked';
        }

        if($sociallock!=""){
            $data .= "<div class='panel panel-default'><div class='panel-heading'>".__( "Download" , "download-manager" )."</div><div class='panel-body wpdm-social-locks text-center'>{$sociallock}</div></div>";
        }

        if ($lock === 'locked') {
            $popstyle = isset($popstyle) && in_array($popstyle, array('popup', 'pop-over')) ? $popstyle : 'pop-over';
            if ($embed == 1)
                $adata = "</strong><table class='table all-locks-table' style='border:0px'><tr><td style='padding:5px 0px;border:0px;'>" . $data . "</td></tr></table>";
            else {
                $dataattrs = $popstyle == 'pop-over'? 'data-title="<button type=button id=\'close\' class=\'btn btn-danger btn-xs pull-right po-close\' style=\'margin-top:-1px;margin-right:-5px\'><i class=\'fa fa-times\'></i></button> '.__( "Download" , "download-manager" ).' ' . $package['title'] . '"' : 'data-toggle="modal" data-target="#pkg_' . $package['ID'] . "_" . $unqid . '"';
                $adata = '<a href="#pkg_' . $package['ID'] . "_" . $unqid . '" '.$dataattrs.' class="wpdm-download-link wpdm-download-locked ' . $popstyle . ' ' . $btnclass . '"><i class=\'' . $wpdm_download_lock_icon . '\'></i>' . $package['link_label'] . '</a>';
                if ($popstyle == 'pop-over')
                    $adata .= '<div class="modal fade"><div class="row all-locks"  id="pkg_' . $package['ID'] . "_" . $unqid . '">' . $data . '</div></div>';
                else
                    $adata .= '<div class="modal fade" role="modal" id="pkg_' . $package['ID'] . "_" . $unqid . '"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><strong style="margin:0px;font-size:12pt">' . __('Download') . '</strong></div><div class="modal-body">' . $data . '</div><div class="modal-footer text-right"><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button></div></div></div></div>';
            }

            $data = $adata;
        }
        if ($lock !== 'locked') {

            $data = $package['download_link'];


        }

        if(isset($package['terms_lock']) && $package['terms_lock'] != 0 && (!function_exists('wpdmpp_effective_price') || wpdmpp_effective_price($package['ID']) ==0)){
            $package['terms_conditions'] = wpautop($package['terms_conditions']);
            $package['terms_title'] = !isset($package['terms_title']) || $package['terms_title'] == ''?__("Terms and Conditions",'download-manager'):$package['terms_title'];
            $package['terms_check_label'] = !isset($package['terms_check_label']) || $package['terms_check_label'] == ''?__("You Must Agree With Terms and Conditions to Download",'download-manager'):$package['terms_check_label'];
            $data = '<a href="#" data-toggle="modal" data-target="#termslockmodal" class="wpdm-terms-modal __wpdm_download_btn__">
                                                  '.$package['link_label'].'
                                                </a>                                               
                                                <div class="modal fade" id="termslockmodal" tabindex="-1" role="dialog" aria-labelledby="termslockmodalLabel">
                                                  <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                      <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        <strong class="modal-title" id="termslockmodalLabel">'.$package['terms_title'].'</strong>
                                                      </div>
                                                      <div class="modal-body" style="max-height:300px;overflow:auto;">
                                                        '.$package['terms_conditions'].'
                                                      </div>
                                                      <div class="modal-footer text-left" style="text-align:left">                                                        
                                                        '."<label><input type='checkbox' onclick='jQuery(\".download_footer_{$package['ID']}\").slideToggle();'> {$package['terms_check_label']}</label>".'                                                        
                                                      </div>
                                                      '."<div class='modal-footer  download_footer_{$package['ID']}' style='display:none;'>{$package['download_link']}</div>".'
                                                    </div>
                                                  </div>
                                                </div>';

            //$data = "<div class='panel panel-default terms-panel' style='margin: 0'><div class='panel-heading'>{$package['terms_title']}</div><div class='panel-body' style='max-height: 200px;overflow: auto'>{$package['terms_conditions']}</div><div class='panel-footer'><label class='eden-checkbox'><input type='checkbox' onclick='jQuery(\".download_footer_{$post_vars['ID']}\").slideToggle();'><span><i class='fa fa-check'></i></span> {$package['terms_check_label']}</label></div><div class='panel-footer download_footer_{$package['ID']}' style='display: none'>{$package['download_link']}</div></div>";
            //if($embed == 1)
            //    $data = "<div class='panel panel-default terms-panel' style='margin: 0'><div class='panel-heading'>{$package['terms_title']}</div><div class='panel-body' style='max-height: 200px;overflow: auto'>{$package['terms_conditions']}</div><div class='panel-footer'><label class='eden-checkbox'><input type='checkbox' onclick='jQuery(\".download_footer_{$post_vars['ID']}\").slideToggle();'><span><i class='fa fa-check'></i></span> {$package['terms_check_label']}</label></div><div class='panel-footer  download_footer_{$package['ID']}' style='display:none;'>{$package['download_link_extended']}</div></div>";

        }
    }
    else {
        $limit_msg = WPDM_Messages::download_limit_exceeded($package['ID']);
        $data = trim($limit_msg)?$limit_msg:__( "Download limit exceeded!" , "download-manager" );
    }



    //return str_replace(array("\r","\n"),"",$data);
    return $data;

}


function wpdm_footer_codes()
{

    ?>
    <div id="fb-root"></div>
    <?php
}


/**
 * @usage Verify Email Address
 * @param $email
 * @return bool
 */
function wpdm_verify_email($email){
    $dns_verify = get_option('__wpdm_verify_dns',0);
    $blocked_domains = explode("\n",str_replace("\r","",get_option('__wpdm_blocked_domains','')));
    $blocked_emails = explode("\n",str_replace("\r","",get_option('__wpdm_blocked_emails','')));
    $eparts = explode("@", $email);
    if(!isset($eparts[1])) return false;
    $domain = $eparts[1];
    if(!is_email($email)) return false;
    if(in_array($email, $blocked_emails)) return false;
    if(in_array($domain, $blocked_domains)) return false;
    if($dns_verify && !checkdnsrr($domain, 'MX')) return false;
    return true;
}


/**
 * return download link after verifying password
 * data format: json
 */
function wpdm_getlink()
{
    global $wpdb;
    if (!isset($_POST['__wpdm_ID'])) return;
    $id = (int)$_POST['__wpdm_ID'];
    $password = isset($_POST['password']) ? stripslashes($_POST['password']) : '';
    $file = get_post($id, ARRAY_A);
    //$file['ID'] = $file['ID'];
    $file = wpdm_setup_package_data($file);
    $key = uniqid();
    $file1 = $file;

    $plock = isset($file['password_lock']) ? $file['password_lock'] : 0;

    $data = array('error' => '', 'downloadurl' => '');

    $limit = get_option('__wpdm_private_link_usage_limit',3);
    $xpire_period = ((int)get_option('__wpdm_private_link_expiration_period', 3)) * ((int)get_option('__wpdm_private_link_expiration_period_unit', 60));
    $xpire_period = $xpire_period > 0 ? $xpire_period :  3600;

    if(isset($_POST['reCaptchaVerify'])){
        $ret = wpdm_remote_post('https://www.google.com/recaptcha/api/siteverify', array('secret' => get_option('_wpdm_recaptcha_secret_key'), 'response' => $_POST['reCaptchaVerify'], 'remoteip' => $_SERVER['REMOTE_ADDR']));
        $ret = json_decode($ret);
        if($ret->success == 1){
            $download_url  = \WPDM\Package::expirableDownloadLink($id, $limit, $xpire_period);
            $data['downloadurl'] = $download_url;
        }
        else{
            $data['error'] = __("Captcha Verification Failed!", "wpmdpro");
        }

        wp_send_json($data);
        die();
    }

    // Email Lock Verification
    if (isset($_POST['verify']) && $_POST['verify'] == 'email' && $file['email_lock'] == 1) {
        if (wpdm_verify_email($_POST['email'])) {
            $subject = "Your Download Link";
            $site = get_option('blogname');

            $custom_form_data = isset($_POST['custom_form_field']) ? $_POST['custom_form_field'] : array();
            if(isset($_REQUEST['name'])) $custom_form_data['name'] = $_REQUEST['name'];

            //do something before sending download link
            do_action("wpdm_before_email_download_link", $_POST, $file);

            $idl = isset($file['email_lock_idl']) ? (int)$file['email_lock_idl'] : 3;
            $idle = isset($file['email_lock_idl_email']) ? (int)$file['email_lock_idl_email'] : 0;

            $request_status =  $idl === 0 ? 3 : $idl; //($idl === 2)?$idl:1;
            $wpdb->insert("{$wpdb->prefix}ahm_emails", array('email' => $_POST['email'], 'pid' => $file['ID'], 'date' => time(), 'custom_data' => serialize($custom_form_data), 'request_status' => $request_status));
            $subscriberID = $wpdb->insert_id;

            $download_url = add_query_arg(['subscriber' =>\WPDM\libs\Crypt::encrypt($subscriberID)], \WPDM\Package::expirableDownloadLink($id, $limit, $xpire_period));
            $download_page_url  = add_query_arg(['subscriber' =>\WPDM\libs\Crypt::encrypt($subscriberID)], \WPDM\Package::expirableDownloadPage($id, $limit, $xpire_period));

            if($idl === 0 || ($idl == 1 && $idle == 0)) {
                $name = isset($cff['name'])?$cff['name']:'';
                $email_params = array('to_email' => $_POST['email'], 'name' => $name, 'download_count' => $limit,  'package_name' => $file['post_title'], 'package_url' => get_permalink($id), 'download_url' => $download_url, 'download_page_url' => $download_page_url);
                $email_params = apply_filters("wpdm_email_lock_mail_params", $email_params, $file);
                \WPDM\Email::send("email-lock", $email_params);

            }
            $elmsg = sanitize_textarea_field(get_post_meta($id, '__wpdm_email_lock_msg', true));
            if ($idl === 0) {
                $data['downloadurl'] = "";
                $data['msg'] = ($elmsg !='' ? $elmsg : __( "Download link sent to your email!" , "download-manager" ));
                $data['type'] = 'success';
            } else if ($idl === 2) {
                $data['downloadurl'] = "";
                $data['msg'] = ($elmsg !='' ? $elmsg : __( "Admin will review your request soon!" , "download-manager" ));
                $data['type'] = 'success';
            } else {
                $data['downloadurl'] = $download_url;
                if($idle == 0)
                    $data['msg'] = ($elmsg !='' ? $elmsg : __( "Download link also sent to your email!" , "download-manager" ));
                else
                    $data['msg'] = ($elmsg !='' ? $elmsg : __( "Download will be started shortly!" , "download-manager" ));
            }

	        if(!wpdm_is_ajax()){

		        @setcookie("wpdm_getlink_data_".$key, json_encode($data));

		        if(isset($data['downloadurl']) && $data['downloadurl']!=''){
			        header("location: ".$data['downloadurl']);
			        die();
		        }

		        header("location: ".$_SERVER['HTTP_REFERER']."#nojs_popup|ckid:".$key);
		        die();
	        }

	        $_pdata = $_POST;
            $_pdata['pid'] = $file['ID'];
            $_pdata['time'] = time();
	        \WPDM\Session::set("__wpdm_email_lock_verified", $_pdata, 604800);
            wp_send_json($data);
            die();
        } else {
            $data['downloadurl'] = "";
            $data['msg'] =  get_option('__wpdm_blocked_domain_msg');
            if(trim($data['msg']) === '') $data['msg'] = __( "Invalid Email Address!" , "download-manager" );
            $data['type'] =  'error';

	        if(!wpdm_is_ajax()){

		        @setcookie("wpdm_getlink_data_".$key, json_encode($data));

		        if(isset($data['downloadurl']) && $data['downloadurl']!=''){
			        header("location: ".$data['downloadurl']);
			        die();
		        }

		        header("location: ".$_SERVER['HTTP_REFERER']."#nojs_popup|ckid:".$key);
		        die();
	        }

            wp_send_json($data);
            die();
        }
    }

    if (isset($_POST['force']) && $_POST['force'] != '') {
        $vr = explode('|', base64_decode($_POST['force']));
        if ($vr[0] == 'unlocked') {
            $social = array('f' => 'wpdm_fb_likes', 'g' => 'wpdm_gplus1s', 't' => 'wpdm_tweets', 'l' => 'wpdm_lishare');
            if ($_POST['social'] && isset($social[$_POST['social']]))
                update_option($social[$_POST['social']], (int)get_option($social[$_POST['social']]) + 1);

            $download_url  = \WPDM\Package::expirableDownloadLink($id, $limit, $xpire_period);
            $data['downloadurl'] = $download_url;
            $adata = apply_filters("wpdmgetlink", $data, $file);
            $data = is_array($adata) ? $adata : $data;

	        if(!wpdm_is_ajax()){

		        @setcookie("wpdm_getlink_data_".$key, json_encode($data));

		        if(isset($data['downloadurl']) && $data['downloadurl']!=''){
			        header("location: ".$data['downloadurl']);
			        die();
		        }

		        header("location: ".$_SERVER['HTTP_REFERER']."#nojs_popup|ckid:".$key);
		        die();
	        }

            header("Content-type: application/json");
            die(json_encode($data));
        }

    }

    if ($plock == 1 && $password != $file['password'] && !strpos("__" . $file['password'], "[$password]")) {
        $data['error'] = __( "Wrong Password!" , "download-manager" )." &nbsp; <span><i class='fas fa-redo'></i> ".__( "Try Again" , "download-manager" )." </span>";
        $file = array();
    }
    if ($plock == 1 && $password == '') {
        $data['error'] = __( "Wrong Password!" , "download-manager" )." &nbsp; <span class='color-blue'><i class='fas fa-redo'></i> ".__( "Try Again" , "download-manager" )." </span>";
        $file = array();
    }
    $ux = "";

    if ( isset($file['ID']) && $file['ID'] != '') {
        $pu = isset($file['password_usage']) && is_array($file['password_usage'])?$file['password_usage']:array();

        $pul = (int)$file['password_usage_limit'];

        if (is_array($pu) && isset($pu[$password]) && $pu[$password] >= $pul && $pul > 0)
            $data['error'] = __( "Password usages limit exceeded" , "download-manager" );
        else {

            if(!is_array($pu)) $pu = array();
            $pu[$password] = isset($pu[$password])?$pu[$password]+1:1;
            \WPDM\Session::set("pass_verified_" . $file['ID'], 1);
            update_post_meta($file['ID'], '__wpdm_password_usage', $pu);
        }
    }

    if (isset($_COOKIE['unlocked_' . $file1['ID']]) && $_COOKIE['unlocked_' . $file1['ID']] == 1) {
        $data['error'] = '';
        $file = $file1;
    }

    if ($data['error'] == '') {

        $data['downloadurl'] = \WPDM\Package::expirableDownloadLink($id, $limit);
    } // home_url('/?downloadkey='.md5($file['files']).'&file='.$id.$ux);
    $adata = apply_filters("wpdmgetlink", $data, $file);
    $data = is_array($adata) ? $adata : $data;

	if(!wpdm_is_ajax()){

		@setcookie("wpdm_getlink_data_".$key, json_encode($data));

		if(isset($data['downloadurl']) && $data['downloadurl']!=''){
			header("location: ".$data['downloadurl']);
			die();
		}

		header("location: ".$_SERVER['HTTP_REFERER']."#nojs_popup|ckid:".$key);
		die();
	}

    wp_send_json($data);
	die();
}



function wpdm_package_link_legacy($params)
{
    extract($params);
    $posts = get_posts(array("post_type" => "wpdmpro", "meta_key" => "__wpdm_legacy_id", "meta_value" => $params['id']));
    $data = (array)$posts[0];
    if(!isset($data['ID'])) return "";
    $data = wpdm_setup_package_data($data);

    if ($data['ID'] == '') {
        return '';
    }

    $templates = maybe_unserialize(get_option("_fm_link_templates", true));

    if(!isset($template) || $template=="" ) $template = $data['template'];

    if(isset($template) && isset($templates[$template]) && isset($templates[$template]['content'])) $template = $templates[$template]['content'];


    return "<div class='w3eden'>" . wpdm_fetch_template($template, $data, 'link') . "</div>";
}


/**
 * Parse shortcode
 * @param mixed $content
 * @return mixed
 */
function wpdm_downloadable($content)
{
    if( defined('WPDM_THEME_SUPPORT') && WPDM_THEME_SUPPORT == true ) return $content;
    if(get_post_type(get_the_ID()) != 'wpdmpro') return $content;

    global $wpdb, $current_user, $post, $wp_query, $wpdm_package;
    if (isset($wp_query->query_vars[get_option('__wpdm_curl_base', 'downloads')]) && $wp_query->query_vars[get_option('__wpdm_curl_base', 'downloads')] != '')
        return wpdm_embed_category(array("id" => $wp_query->query_vars[get_option('__wpdm_curl_base', 'downloads')]));
    $postlink = site_url('/');

    $permission_msg = get_option('__wpdm_permission_denied_msg') ? stripslashes(get_option('__wpdm_permission_denied_msg')) : __("Sorry! You don't have suffient permission to download this file!", "download-manager");
    $login_msg = WPDM_Messages::login_required(get_the_ID());
    $user = new WP_User(null);
    if (isset($_GET[get_option('__wpdm_purl_base', 'download')]) && $_GET[get_option('__wpdm_purl_base', 'download')] != '' && $wp_query->query_vars[get_option('__wpdm_purl_base', 'download')] == '')
        $wp_query->query_vars[get_option('__wpdm_purl_base', 'download')] =  $_GET[get_option('__wpdm_purl_base', 'download')];
    $wp_query->query_vars[get_option('__wpdm_purl_base', 'download')] = isset($wp_query->query_vars[get_option('__wpdm_purl_base', 'download')]) ? urldecode($wp_query->query_vars[get_option('__wpdm_purl_base', 'download')]) : '';

    if (is_singular('wpdmpro')) {
        if (get_option('_wpdm_custom_template') == 1 || current_theme_supports('wpdm')) return $content;

        $template = get_post_meta(get_the_ID(),'__wpdm_page_template', true);
        $data = wpdm_fetch_template($template, get_the_ID(), 'page');
        $siteurl = site_url('/');
        return  "<div class='w3eden'>{$data}</div>";
    }

    return $content;


}


/**
 * @usage Count files in a package
 * @param $id
 * @return int
 */
function wpdm_package_filecount($id){
    return \WPDM\Package::fileCount($id);

}

/**
 * @usage Calculate package size
 * @param $id
 * @return float|int|mixed|string
 */
function wpdm_package_size($id){
    return \WPDM\Package::Size($id);
}


/**
 * @usage Calculate file size
 * @param $file
 * @return float|int|mixed|string
 */
function wpdm_file_size($file){
    if(file_exists($file))
        $size = filesize($file);
    else if(file_exists(UPLOAD_DIR.$file))
        $size = filesize(UPLOAD_DIR.$file);
    else $size = 0;
    $size = $size / 1024;
    if ($size > 1024) $size = number_format($size / 1024, 2) . ' MB';
    else $size = number_format($size, 2) . ' KB';
    return $size;
}



/**
 * @usage Returns icons for package file types
 * @param $id
 * @param bool $img
 * @return array|string
 */
function wpdm_package_filetypes($id, $img = true){

    return \WPDM\Package::fileTypes($id, $img);

}

/**
 * Get post excerpt
 * @param $post
 * @param int $length
 * @param bool $word_break
 * @param string $continue
 * @return string
 */
function wpdm_get_excerpt($post, $length = 100, $word_break = false, $continue = "..."){
    $post = is_object($post) ? $post : get_post($post);
    if(!is_object($post)) return '';
    $excerpt = get_the_excerpt($post);
    if(!$excerpt) $excerpt = $post->post_content;
    $excerpt = strip_tags($excerpt);
    $excerpt = substr(trim($excerpt), 0, $length);
    if(!$word_break) {
        $excerpt = explode(" ", $excerpt);
        array_pop($excerpt);
        $excerpt = implode(" ", $excerpt);
    }
    return $excerpt.$continue;
}

function wpdm_category($params)
{
    if(!is_array($params)) return "Missing parameters. Please follow the doc:<br/><a target='_blank' href='https://www.wpdownloadmanager.com/doc/short-codes/wpdm_category-query-all-downloads-from-one-or-more-categories/'>[wpdm_category] – Query All Downloads From One or More Categories</a></a>";

    $params['order_field'] = is_array($params) && isset($params['order_by'])?$params['order_by']:'publish_date';
    unset($params['order_by']);
    if (isset($params['item_per_page']) && !isset($params['items_per_page'])) $params['items_per_page'] = $params['item_per_page'];
    unset($params['item_per_page']);
    return wpdm_embed_category($params);

}

function wpdm_embed_category($params = array('id' => '', 'operator' => 'IN' , 'items_per_page' => 10, 'title' => false, 'desc' => false, 'orderby' => 'create_date', 'order' => 'desc', 'paging' => false, 'toolbar' => 1, 'template' => '','cols'=>3, 'colspad'=>2, 'colsphone' => 1, 'morelink' => 1))
{
    extract($params);
    $fnparams = $params;
    if(!isset($id)) return;
    if(!isset($items_per_page)) $items_per_page = 10;
    if(!isset($template)) $template = 'link-template-calltoaction3.php';
    if(!isset($cols)) $cols = 3;
    if(!isset($colspad)) $colspad = 2;
    if(!isset($colsphone)) $colsphone = 1;
    $toolbar = isset($toolbar)?(int)$toolbar:0;
    $taxo = 'wpdmcategory';
    if(isset($tag) && $tag==1) $taxo = 'post_tag';
    $css_class = isset($css_class)?$css_class:'';
    $cwd_class = "col-md-".(int)(12/$cols);
    $cwdsm_class = "col-sm-".(int)(12/$colspad);
    $cwdxs_class = "col-xs-".(int)(12/$colsphone);

    $id = trim($id, ", ");
    $cids = explode(",", $id);

    global $wpdb, $current_user, $post, $wp_query;

    $orderby = isset($orderby) ? $orderby : 'publish_date';
    $orderby = in_array(wpdm_query_var('orderby'), array('title', 'publish_date', 'updates', 'download_count', 'view_count')) ? wpdm_query_var('orderby') : $orderby;

    $order = isset($fnparams['order']) ? $fnparams['order'] : 'desc';
    $order = wpdm_query_var('order') ? wpdm_query_var('order') : $order;
    $operator = isset($operator)?$operator:'IN';
    //$cpvid = str_replace(",", "_", $id);
    //$cpvar = 'cp_'.$cids[0];
    $term = get_term_by('slug', $cids[0], 'wpdmcategory');
    $cpvar = 'cp_'.$term->term_id;
    $cp = wpdm_query_var($cpvar,'num');
    if(!$cp) $cp = 1;

    $params = array(
        'post_type' => 'wpdmpro',
        'paged' => $cp,
        'posts_per_page' => $items_per_page,
        'tax_query' => array(array(
            'taxonomy' => $taxo,
            'field' => 'slug',
            'terms' => $cids,
            'include_children' => false,
            'operator' => $operator
        ))
    );

    if (get_option('_wpdm_hide_all', 0) == 1) {
        $params['meta_query'] = array(
            array(
            'key' => '__wpdm_access',
            'value' => 'guest',
            'compare' => 'LIKE'
            )
        );
        if(is_user_logged_in()){
            global $current_user;
            if(isset($current_user->roles, $current_user->roles[0])) {
                foreach ($current_user->roles as $role) {
                    $params['meta_query'][] = array(
                        'key' => '__wpdm_access',
                        'value' => $role,
                        'compare' => 'LIKE'
                    );
                }
                $params['meta_query']['relation'] = 'OR';
            }
        }
    }

    if(isset($tags) && $tags != ''){
        $params['tag'] = $tags;
    }

    $order_fields = array('__wpdm_download_count','__wpdm_view_count','__wpdm_package_size_b');
    if(!in_array( "__wpdm_".$orderby, $order_fields)) {
        $params['orderby'] = $orderby;
        $params['order'] = $order;
    } else {
        $params['orderby'] = 'meta_value_num';
        $params['meta_key'] = "__wpdm_".$orderby;
        $params['order'] = $order;
    }

    $params = apply_filters("wpdm_embed_category_query_params", $params);

    $packs = new WP_Query($params);

    $total = $packs->found_posts;
    $pages = ceil($total / $items_per_page);
    $page = $cp;
    $start = ($page - 1) * $items_per_page;

    if (!isset($paging) || $paging == 1) {

        $phtml = wpdm_paginate_links($total, $items_per_page, $page, $cpvar);

    }

    $burl = get_permalink();

    $html = '';
    $templates = maybe_unserialize(get_option("_fm_link_templates", true));

    if(isset($templates[$template])) $template = $templates[$template]['content'];

    global $post;
    while($packs->have_posts()) { $packs->the_post();

        $pack = (array)$post;
        $thtml = wpdm_fetch_template($template, $pack);
        $repeater = '';
        if($thtml != '')
        $repeater = "<div class='{$cwd_class} {$cwdsm_class} {$cwdxs_class}'>".$thtml."</div>";
        $html .=  $repeater;

    }
    wp_reset_query();

    $html = "<div class='row'>{$html}</div>";
    $cname = array();
    foreach($cids as $cid){
        $cat = get_term_by('slug', $cid, $taxo);
        if($cat)
            $cname[] = $cat->name;

    }
    $cats = implode(", ", $cname);

    //Added from v4.2.1
    $desc = '';
    $trm = get_term_by('slug', $cids[0], 'wpdmcategory');

    if(isset($fnparams['title']) && $fnparams['title'] != false && intval($fnparams['title']) != 1) $cats = $fnparams['title'];
    if(isset($fnparams['desc']) && $fnparams['desc'] != false && intval($fnparams['desc']) != 1) $desc = $fnparams['desc'];
    if(isset($fnparams['desc']) && (int)$fnparams['desc'] == 1) $desc = $trm->description;

     $cimg = '';


    $subcats = '';
    if (function_exists('wpdm_ap_categories') && $subcats == 1) {
        $schtml = wpdm_ap_categories(array('parent' => $id));
        if ($schtml != '') {
            $subcats = "<fieldset class='cat-page-tilte'><legend>" . __( "Sub-Categories" , "download-manager" ) . "</legend>" . $schtml . "<div style='clear:both'></div></fieldset>" . "<fieldset class='cat-page-tilte'><legend>" . __( "Downloads" , "download-manager" ) . "</legend>";
            $efs = '</fieldset>';
        }
    }

    if (!isset($paging) || $paging == 1)
        $pgn = "<div style='clear:both'></div>" . $phtml . "<div style='clear:both'></div>";
    else
        $pgn = "";
    global $post;

    $sap = get_option('permalink_structure') ? '?' : '&';
    $burl = $burl . $sap;
    if (isset($_GET['p']) && $_GET['p'] != '') $burl .= 'p=' . esc_attr($_GET['p']) . '&';
    if (isset($_GET['src']) && $_GET['src'] != '') $burl .= 'src=' . esc_attr($_GET['src']) . '&';
    $order = ucfirst($order);
    $order_field = " " . __(ucwords(str_replace("_", " ", $order_field)),"wpdmpro");
    $ttitle = __( "Title" , "download-manager" );
    $tdls = __( "Downloads" , "download-manager" );
    $tcdate = __( "Publish Date" , "download-manager" );
    $tudate = __( "Update Date" , "download-manager" );
    $tasc = __( "Asc" , "download-manager" );
    $tdsc = __( "Desc" , "download-manager" );
    $tsrc = __( "Search" , "download-manager" );
    $ord = __( "Order" , "download-manager" );
    $order_by_label = __( "Order By" , "download-manager" );
    $hasdesc = $desc !=''?'has-desc':'';
    $pic = \WPDM\libs\CategoryHandler::icon($trm->term_id);
    if($pic != '') $pic = "<img class=\"category-thumb\" src=\"{$pic}\" />";
    if ($toolbar || get_option('__wpdm_cat_tb') == 1) {
        if($toolbar != 'skinny') {

            $icon = \WPDM\libs\CategoryHandler::icon($trm->term_id);
            $iconw = $desc != ''?64:32;
            if($icon != '') $icon = "<div class='pull-left mr-3'><img class='category-icon category-{$trm->term_id}' style='max-width: {$iconw}px' src='{$icon}' alt='{$trm->name}' /></div>";

            $toolbar = <<<TBR
                 <div class="panel panel-default card category-panel {$hasdesc}">
                   <div class="panel-body card-body">
                   <div class="media">  
                   $icon                  
                   <div class="media-body">
                   <h3 style="margin: 0">$cats</h3>
                   $desc
                   </div>
                   </div>
                   </div>
                   <div class="panel-footer card-footer">
                   
                   <div class="btn-group btn-group-sm pull-right"><button type="button" class="btn btn-primary" disabled="disabled">{$ord} &nbsp;</button><a class="btn btn-primary" href="{$burl}orderby={$orderby}&order=asc">{$tasc}</a><a class="btn btn-primary" href="{$burl}orderby={$orderby}&order=desc">{$tdsc}</a></div>                         
                   <div class="btn-group btn-group-sm"><button type="button" class="btn btn-info" disabled="disabled">{$order_by_label} &nbsp;</button><a class="btn btn-info" href="{$burl}orderby=title&order=asc">{$ttitle}</a><a class="btn btn-info" href="{$burl}orderby=publish_date&order=desc">{$tcdate}</a></div>                         
                    
                   </div>
                   </div>
TBR;
        } else {
            $toolbar = <<<TBR
                
                   <div class="media" style="margin-bottom: 15px">     
                                  <div class="pull-left label label-primary label-wpdm-category" style="font-size: 16px;padding: 7px 15px;border-radius: 2px">$cats</div>
                               <div class="media-body text-right">
                                 <div class="btn-group btn-group-sm" style="margin-right: 5px"><button type="button" class="btn btn-light" disabled="disabled">{$ord} &nbsp;</button><a class="btn btn-light" href="{$burl}orderby={$orderby}&order=asc">{$tasc}</a><a class="btn btn-light" href="{$burl}orderby={$orderby}&order=desc">{$tdsc}</a></div>                         
                                 <div class="btn-group btn-group-sm"><button type="button" class="btn btn-light" disabled="disabled">{$order_by_label} &nbsp;</button><a class="btn btn-light" href="{$burl}orderby=title&order=asc">{$ttitle}</a><a class="btn btn-light" href="{$burl}orderby=publish_date&order=desc">{$tcdate}</a></div>                                                               
                               </div>
                   </div>
                   
                    
                  
TBR;
        }
    }
    else
        $toolbar = '';
    return "<div class='w3eden'><div class='{$css_class}'>" . $toolbar . $cimg . $subcats . $html  . $pgn . "<div style='clear:both'></div></div></div>";
}



/**
 * @param $file
 * @return array|mixed
 */
function wpdm_basename($file){
    if(strpos("~".$file,"\\"))
        $basename = explode("\\", $file);
    else
       $basename = explode("/", $file);
    $basename = end($basename);
    return $basename;
}

/**
 * @usage Handles ajax file list request for dir attachment
 */
function wpdm_print_file_list(){
    if(wpdm_query_var('action') === 'wpdmfilelistcd') {
        if (\WPDM\Session::get('wpdmfilelistcd_' . wpdm_query_var('pid', 'int'))) {

            $file = wpdm_get_package((int)$_POST['pid']);

            $fhtml = '';
            $idvdl = \WPDM\Package::isSingleFileDownloadAllowed($file['ID']); //isset($file['individual_file_download']) ? $file['individual_file_download'] : 0;
            $pd = isset($file['publish_date'])&&$file['publish_date']!=""?strtotime($file['publish_date']):0;
            $xd = isset($file['expire_date'])&&$file['expire_date']!=""?strtotime($file['expire_date']):0;

            $fileinfo = isset($file['fileinfo']) ? $file['fileinfo'] : array();
            $pwdlock = isset($file['password_lock']) ? $file['password_lock'] : 0;
            $olock = wpdm_is_locked($file['ID']) ? 1 : 0;

            $swl = 0;
            if(!isset($file['quota'])||$file['quota']<=0) $file['quota'] = 9999999999999;
            if(is_user_logged_in()) $cur[] = 'guest';
            if(!wpdm_user_has_access($file['ID']) || wpdm_is_download_limit_exceed($file['ID']) || $file['quota'] <= $file['download_count']) $olock = 1;
            $pwdcol = $dlcol = '';
            if ($pwdlock && $idvdl) $pwdcol = "<th>".__( "Password" , "download-manager" )."</th>";
            if ($idvdl && ($pwdlock || !$olock)) { $dlcol = "<th align=center>".__( "Download" , "download-manager" )."</th>"; $swl = 1; }

            $dir = get_post_meta($_POST['pid'], '__wpdm_package_dir', true);
            $dir = file_exists($dir) ? $dir : \WPDM\libs\Crypt::decrypt($dir);
            $cd = esc_attr($_POST['cd']);
            $cd = str_replace(array('../', './'),'', $cd);
            if($cd == '/') $cd = '';
            $dfiles = array();
            if($dir!=''){
                $realpath = realpath($dir.$cd).'/';
                if(strpos("--".$realpath, $dir) > 0)
                    $dfiles = wpdm_get_files($dir.$cd, false);
                else
                    $dfiles = array();

            }
            $drs = explode('/', $cd);
            $bcrm[] = "<a href='#' class='wpdm-indir' data-dir='/' data-pid='{$file['ID']}'>".__( "Home" , "download-manager" )."</a>";
            $brdp = '';
            foreach($drs as $tdir) {
                $brdp .= $tdir.'/';
                if($tdir !='')
                $bcrm[] = "<a href='#' class='wpdm-indir' data-dir='{$brdp}' data-pid='{$file['ID']}'>{$tdir}</a>";
            }

            $breadcrumb = implode(" <i class='fa fa-angle-right'></i> ", $bcrm);

            $fhtml = "<div class='breadcrumb'>$breadcrumb</div><div class='row'>";

            if (is_array($dfiles)) {

                foreach ($dfiles as $ind => $sfile) {

                    //$ind = 'dix_'.$ind;  //WPDM_Crypt::Encrypt($sfile);

                    $fhtml .= "<div class='col-md-4 col-sm-6 col-xs-6'><div class='panel panel-default card mb-3'>";
                    if (!isset($fileinfo[$sfile]) || !@is_array($fileinfo[$sfile])) $fileinfo[$sfile] = array();
                    if(!isset($fileinfo[$sfile]['password'])) $fileinfo[$sfile]['password'] = "";

                    if ($fileinfo[$sfile]['password'] == '' && $pwdlock) $fileinfo[$sfile]['password'] = $file['password'];
                    $xname = wpdm_basename($sfile);
                    $ttl = isset($fileinfo[$sfile]['title']) && $fileinfo[$sfile]['title']!="" ? $fileinfo[$sfile]['title'] : preg_replace("/(^[0-9]+)_/", "", wpdm_basename($sfile));

                    $cttl = (is_dir($sfile))?"<a href='#' class='wpdm-indir' data-dir='{$cd}/{$xname}' data-pid='{$file['ID']}'>{$ttl}/</a>": $ttl;

                    $fhtml .= "<div class='panel-heading card-header ttip' title='{$ttl}'>{$cttl}</div>";

                    $imgext = array('png','jpg','jpeg', 'gif');
                    $ext = explode(".", $sfile);
                    $ext = end($ext);
                    $ext = strtolower($ext);
                    $info = wpdm_file_size($sfile);
                    if(is_dir($sfile)) { $ext = 'folder'; $info = count(scandir($sfile))." ".__( "files" , "download-manager" ); }
                    $filepath = file_exists($sfile)?$sfile:UPLOAD_DIR.$sfile;

                    $thumb = "";
                    if(in_array($ext, $imgext))
                        $thumb = wpdm_dynamic_thumb($filepath, array(88, 88));

                    $fticon = \WPDM\libs\FileSystem::fileTypeIcon($ext);

                    if($thumb)
                        $fhtml .= "<div class='panel-body card-body text-center'><img class='file-thumb' src='{$thumb}' alt='{$ttl}' /></div><div class='panel-footer card-footer footer-info'>".$info."</div><div class='panel-footer card-footer'>";
                    else
                        $fhtml .= "<div class='panel-body card-body text-center'><img class='file-ico' src='".$fticon."' alt='{$ttl}' /></div><div class='panel-footer card-footer footer-info'>".$info." </div><div class='panel-footer card-footer'>";


                    if ($swl) {
                        $fileinfo[$sfile]['password'] = $fileinfo[$sfile]['password'] == '' ? $file['password'] : $fileinfo[$sfile]['password'];
                        if ($fileinfo[$sfile]['password'] != '' && $pwdlock  && !is_dir($sfile))
                            $fhtml .= "<div class='input-group'><input  onkeypress='jQuery(this).removeClass(\"input-error\");' size=10 type='password' value='' id='pass_{$file['ID']}_{$ind}' placeholder='Password' name='pass' class='form-control input-sm inddlps' />";
                        if ($fileinfo[$sfile]['password'] != '' && $pwdlock  && !is_dir($sfile))
                            $fhtml .= "<span class='input-group-btn'><button class='inddl btn btn-secondary btn-sm' file='{$sfile}' rel='" . wpdm_download_url($file) . "&ind=" . $ind . "' pass='#pass_{$file['ID']}_{$ind}'><i class='fa fa-download'></i></button></span></div>";
                        else  if(!is_dir($sfile))
                            $fhtml .= "<a class='btn btn-primary btn-sm btn-block' href='" . wpdm_download_url($file) . "&ind=" . $ind . "'><i class='fa fa-download'></i> &nbsp;".__( "Download" , "download-manager" )."</a>";
                        else
                            $fhtml .= "<a class='btn btn-primary btn-sm btn-block wpdm-indir' href='#'  data-dir='{$cd}/{$ttl}' data-pid='{$file['ID']}'><span class='pull-left'><i class='fa fa-folder'></i></span>&nbsp;".__( "Browse" , "download-manager" )."</a>";

                    }


                    $fhtml .= "</div></div></div>";
                }

            }
            $fhtml .= "</div>";
            echo $fhtml;
        } else {
            die('Session Expired! Please refresh and try again.');
        }
        die();
    }
}


/**
 * @usage Generate thumbnail dynamically
 * @param $path
 * @param $size
 * @return mixed
 */

function wpdm_dynamic_thumb($path, $size, $crop = false, $cache = true)
{
    return \WPDM\libs\FileSystem::imageThumbnail($path, $size[0], $size[1], $crop, $cache);
}


/**
 * @usage Return Post Thumbail
 * @param string $size
 * @param bool $echo
 * @param null $extra
 * @return mixed|string|void
 */
function wpdm_post_thumb($size='', $echo = true, $extra = null){
    global $post;
    $size = $size?$size:'thumbnail';
    $class = isset($extra['class'])?$extra['class']:'';
    $crop = isset($extra['crop'])?$extra['crop']:get_option('__wpdm_crop_thumbs', false);
    $alt = $post->post_title;
    if(is_array($size))
    {
        $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full');
        $large_image_url = isset($large_image_url[0])?$large_image_url[0]:'';
        if($large_image_url == '' && isset($extra['default'])) $large_image_url = $extra['default'];
        if($large_image_url!=''){
            $path = str_replace(site_url('/'), ABSPATH, $large_image_url);
            $thumb = wpdm_dynamic_thumb($path, $size, $crop);
            $thumb = str_replace(ABSPATH, site_url('/'), $thumb);
            $alt = get_post_meta(get_post_thumbnail_id($post->ID), '_wp_attachment_image_alt', true);
            $img = "<img src='".$thumb."' alt='{$alt}' class='{$class}' />";
            if($echo) { echo $img; return true; }
            else
                return $img;
        }
    }
    if($echo&&has_post_thumbnail($post->ID ))
        echo get_the_post_thumbnail($post->ID, $size, $extra );
    else if(!$echo&&has_post_thumbnail($post->ID ))
        return get_the_post_thumbnail($post->ID, $size, $extra );
    else if($echo)
        echo "";
    else
        return "";
}

/**
 * @usage Generate Thumnail for the given package
 * @param $post
 * @param string $size
 * @param bool $echo
 * @param null $extra
 * @return mixed|string|void
 */
function wpdm_thumb($post, $size='', $echo = true, $extra = null){
    if(is_int($post))
    $post = get_post($post);
    $size = $size?$size:'thumbnail';
    $class = isset($extra['class'])?$extra['class']:'';
    $crop = isset($extra['crop']) ? $extra['crop'] : get_option('__wpdm_crop_thumbs', false);
    $alt = $post->post_title;
    if(is_array($size))
    {
        $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full');
        if(!$large_image_url) return '';
        $large_image_url = $large_image_url[0];
        if($large_image_url!=''){
            $path = str_replace(site_url('/'), ABSPATH, $large_image_url);
            $thumb = wpdm_dynamic_thumb($path, $size, $crop);
            $thumb = str_replace(ABSPATH, site_url('/'), $thumb);
            $alt = get_post_meta(get_post_thumbnail_id($post->ID), '_wp_attachment_image_alt', true);
            if($echo === 'url') return $thumb;
            if($alt === '') $alt = esc_attr(strip_tags(get_the_title($post->ID)));
            $img = "<img src='".$thumb."' alt='{$alt}' class='{$class}' />";
            if($echo) { echo $img; return; }
            else
                return $img;
        }
    }
    if($echo&&has_post_thumbnail($post->ID ))
        echo get_the_post_thumbnail($post->ID, $size, $extra );
    else if(!$echo&&has_post_thumbnail($post->ID ))
        return get_the_post_thumbnail($post->ID, $size, $extra );
    else if($echo)
        echo "";
    else
        return "";
}

function wpdm_media_field($data){
    ob_start();
    $attrs = '';
    if(isset($data['attrs'])) {
        foreach ($data['attrs'] as $attr => $value){
            $attrs .= "$attr='$value' ";
        }
    }
    ?>
    <div class="input-group">
        <input placeholder="<?php echo $data['placeholder']; ?>" <?php echo $attrs; ?> type="url" name="<?php echo $data['name']; ?>" id="<?php echo isset($data['id'])?$data['id']:($id = uniqid()); ?>" class="form-control" value="<?php echo isset($data['value']) ? $data['value'] : ''; ?>"/>
        <span class="input-group-btn">
                        <button class="btn btn-secondary btn-media-upload" type="button" rel="#<?php echo isset($data['id'])?$data['id']:$id; ?>"><i class="far fa-image"></i></button>
                    </span>
    </div>
    <?php
    return ob_get_clean();
}

function wpdm_image_selector($data){
    ob_start();
    $attrs = '';
    if(isset($data['attrs'])) {
        foreach ($data['attrs'] as $attr => $value){
            $attrs .= "$attr='$value' ";
        }
    }
    $id = uniqid();
    ?>
    <div class="panel panel-default text-center image-selector-panel" style="width: 250px">
        <div class="panel-body">
            <img id="<?php echo isset($data['id'])?$data['id']:$id; ?>"  src="<?php echo isset($data['value']) && $data['value'] != '' ? $data['value'] : WPDM_BASE_URL.'assets/images/image.png'; ?>" />
        </div>
        <div class="panel-footer">
            <input id="<?php echo isset($data['id'])?$data['id']:$id; ?>_hidden" type="hidden" name="<?php echo $data['name']; ?>" value="<?php echo isset($data['value']) ? $data['value'] : ''; ?>"/>
            <button class="btn btn-info btn-block btn-image-selector" type="button" rel="#<?php echo isset($data['id'])?$data['id']:$id; ?>"><i class="far fa-image"></i> <?php isset($data['btnlabel'])?$data['btnlabel']:_e('Select Image', 'download-manager'); ?></button>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function wpdm_image_uploader($data){
    ob_start();
    $attrs = '';
    if(isset($data['attrs'])) {
        foreach ($data['attrs'] as $attr => $value){
            $attrs .= "$attr='$value' ";
        }
    }
    $default = isset($data['default'])?$data['default']:WPDM_BASE_URL.'assets/images/image.png';
    $id = uniqid();
    ?>
    <div id="wpdm-upload-ui" class="panel panel-default text-center image-selector-panel" style="width: 250px">
        <div class="panel-header text-muted" id="del-img">
            Delete Image
        </div>
        <div id="wpdm-drag-drop-area">
        <div class="panel-body">
            <img id="<?php echo isset($data['id'])?$data['id']:$id; ?>"  src="<?php echo isset($data['value']) && $data['value'] != '' ? $data['value'] : $default; ?>" />
        </div>
        <div class="panel-footer">
            <input id="<?php echo isset($data['id'])?$data['id']:$id; ?>_hidden" type="hidden" name="<?php echo $data['name']; ?>" value="<?php echo isset($data['value']) ? $data['value'] : ''; ?>"/>

            <button id="wpdm-browse-button" style="font-size: 9px;text-transform: unset" type="button" class="btn btn-info btn-block"><?php echo isset($data['btnlabel'])?$data['btnlabel']:__('SELECT IMAGE', 'download-manager'); ?></button>
            <div class="progress" id="wmprogressbar" style="height: 30px !important;border-radius: 3px !important;margin: 0;position: relative;background: #0d406799;display: none;box-shadow: none">
                <div id="wmprogress" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;line-height: 30px;background-color: #007bff"></div>
                <div class="fetfont" style="font-size:9px;position: absolute;line-height: 30px;height: 30px;width: 100%;z-index: 999;text-align: center;color: #ffffff;font-weight: 800;letter-spacing: 1px">UPLOADING... <span id="wmloaded">0</span>%</div>
            </div>

                <?php

                $plupload_init = array(
                    'runtimes'            => 'html5,silverlight,flash,html4',
                    'browse_button'       => 'wpdm-browse-button',
                    'container'           => 'wpdm-upload-ui',
                    'drop_element'        => 'wpdm-drag-drop-area',
                    'file_data_name'      => 'wpdm_file',
                    'multiple_queues'     => false,
                    'url'                 => admin_url('admin-ajax.php'),
                    'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
                    'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
                    'filters'             => array(array('title' => __('Allowed Files'), 'extensions' => 'png,jpg,jpeg')),
                    'multipart'           => true,
                    'urlstream_upload'    => true,

                    // additional post data to send to our ajax hook
                    'multipart_params'    => array(
                        '_ajax_nonce' => wp_create_nonce(NONCE_KEY),
                        'action'      => $data['action'],            // the ajax action name
                    ),
                );

                $plupload_init['max_file_size'] = wp_max_upload_size().'b';

                // we should probably not apply this filter, plugins may expect wp's media uploader...
                $plupload_init = apply_filters('plupload_init', $plupload_init); ?>
                <style>
                    #del-img{
                        position: absolute;width: 100%;padding: 5px;z-index: 999999;background: rgba(255,255,255,0.9);display: none;cursor: pointer;
                    }
                    #wpdm-upload-ui:hover #del-img{
                        display: block;
                    }
                </style>
                <script type="text/javascript">

                    jQuery(function($){


                        var uploader = new plupload.Uploader(<?php echo json_encode($plupload_init); ?>);

                        uploader.bind('Init', function(up){
                            var uploaddiv = $('#wpdm-upload-ui');

                            if(up.features.dragdrop){
                                uploaddiv.addClass('drag-drop');
                                $('#drag-drop-area')
                                    .bind('dragover.wp-uploader', function(){ uploaddiv.addClass('drag-over'); })
                                    .bind('dragleave.wp-uploader, drop.wp-uploader', function(){ uploaddiv.removeClass('drag-over'); });

                            }else{
                                uploaddiv.removeClass('drag-drop');
                                $('#drag-drop-area').unbind('.wp-uploader');
                            }
                        });

                        uploader.init();

                        uploader.bind('Error', function(uploader, error){
                            wpdm_bootModal('Error', error.message);
                            $('#wmprogressbar').hide();
                            $('#wpdm-browse-button').show();
                        });


                        uploader.bind('FilesAdded', function(up, files){
                            /*var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10); */

                            $('#wpdm-browse-button').hide(); /*attr('disabled', 'disabled'); */
                            $('#wmprogressbar').show();

                            plupload.each(files, function(file){
                                $('#wmprogress').css('width', file.percent+"%");
                                $('#wmloaded').html(file.percent);
                                /*jQuery('#wpdm-browse-button').hide(); //.html('<span id="' + file.id + '"><i class="fas fa-sun fa-spin"></i> Uploading (<span>' + plupload.formatSize(0) + '</span>/' + plupload.formatSize(file.size) + ') </span>');*/
                            });

                            up.refresh();
                            up.start();
                        });

                        uploader.bind('UploadProgress', function(up, file) {
                            /*jQuery('#' + file.id + " span").html(plupload.formatSize(parseInt(file.size * file.percent / 100)));*/
                            $('#wmprogress').css('width', file.percent+"%");
                            $('#wmloaded').html(file.percent);
                        });


                        uploader.bind('FileUploaded', function(up, file, response) {
                            res = JSON.parse(response.response);
                            $('#<?php echo isset($data['id'])?$data['id']:$id; ?>').attr('src', res.image_url);
                            $('#wmprogressbar').hide();
                            $('#wpdm-browse-button').show();


                        });
                        var wpdm_ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';
                        $('#del-img').on('click', function () {
                            $(this).html('<i class="fa fa-sun fa-spin"></i> Deleting...');
                            $.post(wpdm_ajax_url, {action: 'delete_<?php echo $data['name']; ?>'}, res => {
                                $('#<?php echo isset($data['id'])?$data['id']:$id; ?>').attr('src', '<?php echo WPDM_BASE_URL.'assets/images/image.png'; ?>');
                                $('#<?php echo isset($data['id'])?$data['id']:$id; ?>_hidden').val('');
                                $('#del-img').html('Delete Image');
                            });
                        });

                    });

                </script>
                <div id="filelist"></div>

                <div class="clear"></div>

        </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}




/**
 * @usage Generate option fields
 * @param $data
 * @return mixed|string
 */
function wpdm_option_field($data) {
    $desc = isset($data['description'])? "<em class='note'>{$data['description']}</em>":"";
    $class = isset($data['class'])? $data['class']:"";
    $data['placeholder'] = isset($data['placeholder'])?$data['placeholder']:'';
    switch($data['type']):
        case 'text':
            return "<input type='text' name='$data[name]' class='form-control {$class}' id='$data[id]' value='$data[value]' placeholder='{$data['placeholder']}'  />$desc";
            break;
        case 'select':
        case 'dropdown':
            $html = "<select name='{$data['name']}'  id='{$data['id']}' class='form-control {$class}' style='width:100%;min-width:150px;' >";
            foreach($data['options'] as $value => $label){

                $html .= "<option value='{$value}' ".selected($data['selected'],$value,false).">$label</option>";
            }
            $html .= "</select>";
            return $html.$desc;
            break;
        case 'radio':
            $html = "";
            foreach($data['options'] as $value => $label){
                $html .= "<label style='display: inline-block;margin-right: 5px'><input type='radio' name='{$data['name']}' class='{$class}' value='{$value}' ".selected($data['selected'],$value,false)." /> $label</label>";
            }
            $html .= "";
            return $html.$desc;
            break;
        case 'notice':
            return "<div class='alert alert-info' style='margin: 0'>$data[notice]</div>".$desc;
        case 'textarea':
            return "<textarea name='$data[name]' id='$data[id]' class='form-control {$class}' style='min-height: 100px'>$data[value]</textarea>$desc";
            break;
        case 'checkbox':
            return "<input type='hidden' name='$data[name]' value='0' /><input type='checkbox' class='{$class}' name='$data[name]' id='$data[id]' value='$data[value]' ".checked($data['checked'], $data['value'], false)." />".$desc;
            break;
        case 'callback':
            return call_user_func($data['dom_callback'], $data['dom_callback_params']).$desc;
            break;
        case 'heading':
            return "<h3>".$data['label']."</h3>";
            break;
        case 'media':
            return wpdm_media_field($data);
            break;
        default:
            return "<input type='{$data['type']}' name='$data[name]' class='form-control {$class}' id='$data[id]' value='$data[value]' placeholder='{$data['placeholder']}'  />$desc";
            break;
            break;
    endswitch;
}

/**
 * @param $options
 * @return string
 */
function wpdm_option_page($options){
    $html = "<div class='wpdm-settings-fields'>";
    foreach($options as $id => $option){
        if(!isset($option['id'])) $option['id'] = $id;
        if(!isset($option['name'])) $option['name'] = $id;
        if(!isset($option['label'])) $option['label'] = '';
        if(in_array($option['type'], array('checkbox','radio')))
            $html .= "<div class='form-group'><label>".wpdm_option_field($option)." {$option['label']}</label></div>";
        else if($option['type']=='heading')
            $html .= "<h3>{$option['label']}</h3>";
        else
            $html .= "<div class='form-group'><label>{$option['label']}</label>".wpdm_option_field($option)."</div>";
    }
    $html .="</div>";
    return $html;
}


/**
 * @param $name
 * @param $options
 * @return string
 */
function wpdm_settings_section($name, $options){
    return "<div class='panel panel-default'><div class='panel-heading'>{$name}</div><div class='panel-body'>".wpdm_option_page($options)."</div></div>";
}


/**
 * @usage Get All Custom Data of a Package
 * @param $pid
 * @return array
 */
function wpdm_custom_data($pid)
{
    return \WPDM\Package::metaData($pid);
}

/**
 * @usage Organize package data using all available variable
 * @param $vars
 * @param string $template
 * @return array
 */
function wpdm_setup_package_data($vars, $template = '')
{
    if (isset($vars['formatted'])) return $vars;
    if (!isset($vars['ID'])) return $vars;
    $pack = new \WPDM\Package($vars['ID']);
    $pack->prepare($vars['ID'], $template);
    return $pack->PackageData;
}

/**
 * @usage Check if a package is locked or public
 * @param $id
 * @return bool
 */
function wpdm_is_locked($id){

    return \WPDM\Package::isLocked($id);

}


/**
 * @usage Fetch link/page template and return generated html
 * @param $template
 * @param $vars
 * @param string $type
 * @return mixed|string|void
 */
function FetchTemplate($template, $vars, $type = 'link')
{
    return \WPDM\Package::fetchTemplate($template, $vars, $type);
}

/**
 * @usage Fetch link/page template and return generated html
 * @param $template
 * @param $vars
 * @param string $type
 * @return mixed|string|void
 */
function wpdm_fetch_template($template, $vars, $type = 'link'){
    return \WPDM\Package::fetchTemplate($template, $vars, $type);
}

/**
 * @usage Callback function for [wpdm_login_form] short-code
 * @return string
 */
function wpdm_loginform(){
    return wpdm_login_form(array('redirect'=>$_SERVER['REQUEST_URI']));
}


/**
 * @return bool
 */
function wpdm_is_ajax()
{
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
        return true;
    return false;
}


/**
 * @usage Get Package Data By Package ID
 * @param $ID
 * @return bool|mixed|null|void|WP_Post
 */
function wpdm_get_package($ID){
    return \WPDM\Package::Get($ID);
}

/**
 * @usage Get download manager package data
 * @param $ID
 * @param $meta
 * @return mixed
 */
function get_package_data($ID, $key, $default = ''){
    $data = \WPDM\Package::get($ID, $key);
    $data = $data?$data:$default;
    return $data;
}



/**
 * @usage Password generator
 */
function wpdm_generate_password()
{
    if(!current_user_can(WPDM_MENU_ACCESS_CAP) || !wpdm_is_ajax()) die();
    include(wpdm_tpl_path('wpdm-generate-password.php'));
    die();

}

/**
 * @usage Special Sort-code: Email to Download
 * @param $params
 * @return mixed|string
 */
function wpdm_email_2download($params)
{
    $package = wpdm_get_package($params['id']);
    if (isset($params['title'])) $package['email_heading'] = $params['title'];
    if (isset($params['msg'])) $package['email_intro'] = $params['msg'];
    $scolor = (isset($params['scolor'])) ? $params['scolor'] : 'default';
    $html = \WPDM\PackageLocks::AskEmail($package);
    $class = isset($params['style']) ? $params['style'] : ""; //wpdm-email2dl  drop-shadow lifted
    $html = str_replace("panel-default", $class . " panel-" . $scolor, $html);
    //$html = "<div class='w3eden wpdm-email2dl  drop-shadow lifted'><div class='wcon'><strong>$params[title]</strong><br/>{$params[msg]}<br clear='all' />$html</div></div>";
    return $html;
}



/**
 * @usage add custom fields with csv file
 * @param $custom_fields
 * @return array
 */
function wpdm_export_custom_form_fields($custom_fields)
{
    $custom_fields[] = 'name';
    return $custom_fields;
}

/**
 * @usage add cuistom fields option html to show in admin
 * @param $pid
 */
function wpdm_ask_for_custom_data($pid)
{
    $cff = get_post_meta($pid, '__wpdm_custom_form_field', true);
    $idl = (int)get_post_meta($pid, '__wpdm_email_lock_idl', true);
    $idle = get_post_meta($pid, '__wpdm_email_lock_idl_email', true);
    if (!$cff) $cff = array();

    ?>

    <div class="form-group">
               <label><input type="checkbox" name="file[custom_form_field][name]" value="1" <?php if (isset($cff['name']) && $cff['name'] == 1) echo 'checked=checked'; ?> > <?php _e( "Ask for Visitor's Name" , "download-manager" );?></label> <br/>
    </div>
                <fieldset class="form-group">
                    <legend><?php echo __( "After Submitting Form:" , "download-manager" ); ?></legend>

                <div class="form-group"><label><input type="radio" id="idl1" name="file[email_lock_idl]"
                              value="0" <?php if ($idl === 0) echo 'checked=checked'; ?>> <?php echo __( "Mail Download Link" , "download-manager" ); ?></label></div>
                <div class="form-group"><label><input type="radio" id="idl2" name="file[email_lock_idl]"
                              value="2" <?php if ($idl === 2) echo 'checked=checked'; ?>> <?php echo __( "Wait For Approval" , "download-manager" ); ?></label></div>

                <div class="form-group"><label id="email_lock_idl_lbl" <?php if ($idl == 1) echo 'class="dnedl"'; ?>><input type="radio" id="idl" name="file[email_lock_idl]"
                              value="1" <?php if ($idl === 1) echo 'checked=checked'; ?> > <?php echo __( "Download Instantly" , "download-manager" ); ?></label>
                <label id="email_lock_idl_email_lbl"> & <input type="checkbox" id="idle" name="file[email_lock_idl_email]"
                      value="1" <?php if ($idle == 1) echo 'checked=checked'; ?> > <?php echo __( "Do Not Mail Download Link" , "download-manager" ); ?></label></div>
                <?php do_action("after_submit_form_action"); ?>
                </fieldset>

    <style>
        #email_lock_idl_lbl + #email_lock_idl_email_lbl{ display: none; }
        #email_lock_idl_lbl.dnedl + #email_lock_idl_email_lbl{ display: inline-block; }
    </style>
    <script>
        jQuery(function ($) {
            $('#idl').click(function () {
                $('#email_lock_idl_lbl').addClass('dnedl');
            });
            $('#idl1,#idl2').click(function () {
                $('#email_lock_idl_lbl').removeClass('dnedl');
            });
        })
    </script>

<?php
}

/**
 * @usage add custom fields html to show at front end with email form
 * @param string $html
 * @param $pid
 * @return string
 */
function wpdm_render_custom_data($html = '',  $pid)
{
    if (!$pid) return '';
    $cff = get_post_meta($pid, '__wpdm_custom_form_field', true);
    $labels['name'] = __( "Your Name" , "download-manager" );
    $labels['name_placeholder'] = __( "Enter Your Name" , "download-manager" );
    if (!$cff) return '';

    foreach ($cff as $name => $value) {
        $html .= <<<DATA
        <div class="form-group">
    <label><nobr>{$labels[$name]}:</nobr></label><input placeholder="{$labels[$name.'_placeholder']}" type="text" name="custom_form_field[$name]" required="required" class="form-control email-lock-name" />
    </div>
DATA;
    }
    return $html;
}


/**
 * @usage Quote all elements in an array
 * @param $values
 * @return mixed
 */
function quote_all_array($values)
{
    foreach ($values as $key => $value)
        if (is_array($value))
            $values[$key] = quote_all_array($value);
        else
            $values[$key] = quote_it($value);
    return $values;
}

/**
 * @usage Quoate a value
 * @param $value
 * @return array|string
 */
function quote_it($value)
{
    if (is_null($value))
        return "NULL";

    $value = '"'.esc_sql($value).'"';
    return $value;
}

/**
 * @usage Find similar packages
 * @param null $package_id
 * @param int $count
 * @param bool|true $html
 * @return array|bool|string
 */
function wpdm_similar_packages($package_id = null, $count = 5, $html = true)
{
    $id = $package_id?$package_id:get_the_ID();
    if(is_array($package_id)) $id = $package_id['ID'];
    $tags = wp_get_post_tags($id);
    $posts = array();
    if ($tags) {
        $tag_ids = array();
        foreach($tags as $individual_tag) $tag_ids[] = $individual_tag->term_id;
        $args=array(
            'post_type'=>'wpdmpro',
            'tag__in' => $tag_ids,
            'post__not_in' => array($id),
            'posts_per_page'=>$count
        );

        $posts = get_posts( $args  );

        if(!$html) return $posts;

        $html = "";

        foreach( $posts as $p ) {

            $package['ID'] = $p->ID;
            $package['post_title'] = $p->post_title;
            $package['post_content'] =  $p->post_content;
            $package['post_excerpt'] = $p->post_excerpt;
            $html .= "<div class='col-md-6'>".wpdm_fetch_template("link-template-panel.php", $package, 'link')."</div>";

        }
    }
    if(count($posts)==0) $html = "<div class='col-md-12'><div class='alert alert-info'>".__( "No related download found!" , "download-manager" )."</div> </div>";
    $html = "<div class='w3eden'><div class='row'>".$html."</div></div>";
    wp_reset_query();
    return $html;


}


function wpdm_view_countplus(){
    if(isset($_REQUEST['__wpdm_view_count']) && wp_verify_nonce($_REQUEST['__wpdm_view_count'],NONCE_KEY)){

        $id = (int)($_REQUEST['id']);
        $views = (int)get_post_meta($id, '__wpdm_view_count', true);
        update_post_meta($id, '__wpdm_view_count', $views+1);
        echo $views+1;
        die();

    }
}


function wpdm_array_splice_assoc(&$input, $offset, $length, $replacement) {
    $replacement = (array) $replacement;
    $key_indices = array_flip(array_keys($input));
    if (isset($input[$offset]) && is_string($offset)) {
        $offset = $key_indices[$offset];
    }
    if (isset($input[$length]) && is_string($length)) {
        $length = $key_indices[$length] - $offset;
    }

    $input = array_slice($input, 0, $offset, TRUE)
        + $replacement
        + array_slice($input, $offset + $length, NULL, TRUE);
}

/**
 * Added from v4.1.1
 * WPDM add-on installer
 */
function wpdm_install_addon(){
    if(isset($_REQUEST['addon']) && current_user_can('manage_options') && wp_verify_nonce($_REQUEST['__wpdmpinn'], $_REQUEST['addon'].NONCE_KEY)){
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        $upgrader = new Plugin_Upgrader( new Plugin_Installer_Skin( compact('title', 'url', 'nonce', 'plugin', 'api') ) );
        $plugin_dir = isset($_REQUEST['dirname'])?$_REQUEST['dirname']:false;
        if($plugin_dir) {
            $plugin_data = wpdm_plugin_data($plugin_dir);
            $plugin_file = $plugin_data ? $plugin_data['plugin_index_file'] : false;
            if ($plugin_file) {
                if (is_plugin_active($plugin_file)) {
                    deactivate_plugins($plugin_file);
                }
                delete_plugins(array($plugin_file));
            }
        }
        if(strpos($_REQUEST['addon'], '.zip'))
            $downloadlink = $_REQUEST['addon'];
        else
            $downloadlink = 'https://www.wpdownloadmanager.com/?wpdmdl='.wpdm_query_var('addon', 'int');
        $upgrader->install($downloadlink);
        die();
    } else {
        die("Only site admin is authorized to install add-on");
    }
}

/**
 * @usage Active premium package add-on / shopping cart
 */
function wpdm_activate_shop(){
    if( current_user_can(WPDM_ADMIN_CAP)){
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        $upgrader = new Plugin_Upgrader( new Plugin_Installer_Skin( compact('title', 'url', 'nonce', 'plugin', 'api') ) );
        $downloadlink = 'https://www.wpdownloadmanager.com/?wpdmdl=15671';
        ob_start();
        echo "<div id='acto'>";
        if(file_exists(dirname(dirname(__FILE__)).'/wpdm-premium-packages/'))
            $upgrader->upgrade($downloadlink);
        else
            $upgrader->install($downloadlink);
        echo '</div><style>#acto .wrap { display: none; }</style>';
        $data = ob_get_clean();
        if(file_exists(dirname(WPDM_BASE_DIR).'/wpdm-premium-packages/wpdm-premium-packages.php')) {
            activate_plugin('wpdm-premium-packages/wpdm-premium-packages.php');
            echo "Congratulation! Your Digital Store is Activated. <a href='' class='btn btn-warning'>Refresh The Page!</a>";
        } else
            echo "Automatic Installation Failed! Please <a href='https://www.wpdownloadmanager.com/?wpdmdl=15671' target='_blank' class='btn btn-warning'>Download</a> and install manually";
        die();
    } else {
        die("Only site admin is authorized to install add-on");
    }
}

/**
 * @usage Add package info in archive/categry page
 * @param $content
 * @return string
 */
function wpdm_archive_page_template($content){
    global $post;
    if(defined('WPDM_THEME_SUPPORT') || ( !is_tax('wpdmcategory') && !is_search())) return $content;
    $id = get_the_ID();

    if(get_post_type()=='wpdmpro'){

        $style = get_option('__wpdm_cpage_style', 'basic');
        $template = get_option('__wpdm_cpage_template');
        if($style === 'ltpl'){
            return "<div class='w3eden'>".wpdm_fetch_template($template, $id)."</div>";
        }

    }

    return $content;
}

/**
 * @param $pid
 * @param $w
 * @param $h
 * @param bool $echo
 * @return string
 * @usage Generates thumbnail html from PDF file attached with a Package. [ From v4.1.3 ]
 */
function wpdm_pdf_preview($pid, $w, $h, $echo = true){

    $post = get_post($pid);
    $files = get_post_meta($pid, '__wpdm_files', true);
    $pdf = $files[0];
    $ext = explode(".", $pdf);
    $ext = end($ext);

    $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($pid), 'full');
    $preview = $thumb['0'];

    if($ext=='pdf')
        $pdf_thumb =  wpdm_pdf_thumbnail($pdf, $pid);
    else $pdf_thumb = $preview;

    $imsrc  = wpdm_dynamic_thumb($pdf_thumb, array($w, $h));

    if(!$echo)
        return "<img src='{$imsrc}' alt='{$post->post_title}'/>";

    echo "<img src='{$imsrc}' alt='{$post->post_title}'/>";

}

/**
 * @param $pdf
 * @param $id
 * @return string
 * @usage Generates thumbnail from PDF file. [ From v4.1.3 ]
 */
function wpdm_pdf_thumbnail($pdf, $id){
    return \WPDM\libs\FileSystem::pdfThumbnail($pdf, $id);
}

/**
 * @usage Show Login Form
 */
function wpdm_login_form($params = array()){
    return WPDM()->shortCode->loginForm($params);
}

function wpdm_user_dashboard_url($params = array()){
    $id = get_option('__wpdm_user_dashboard', 0);
    if($id > 0) {
        $url = add_query_arg($params, get_permalink($id));
    }
    else $url = home_url('/');
    return $url;
}

function wpdm_registration_url(){
    $id = get_option('__wpdm_register_url', 0);
    if($id > 0) {
        $url = get_permalink($id);

    }
    else $url = wp_registration_url();
    return $url;
}

function wpdm_login_url($redirect = ''){
    $id = get_option('__wpdm_login_url', 0);
    if($id > 0) {
        $url = get_permalink($id);
        if ($redirect != '')
            $url .= (strstr($url, '?') ? '&' : '?') . 'redirect_to=' . $redirect;

    }
    else $url = wp_login_url($redirect);
    return $url;
}

function wpdm_lostpassword_url(){
    return add_query_arg(array('action' => 'lostpassword'), wpdm_login_url());
}

function wpdm_logout_url($redirect = ''){
    $logout_url = home_url("/?logout=".wp_create_nonce(NONCE_KEY));
    return $redirect!=''?add_query_arg(array('redirect_to' => $redirect), $logout_url):$logout_url;
}


function wpdm_user_logged_in($msg){
    echo $msg;
}



/**
 * @usage Returns download manager template file path
 * @param $file
 * @param string $tpldir
 * @return string
 */
function wpdm_tpl_path($file, $tpldir = '', $fallback = ''){
    if(file_exists(get_stylesheet_directory().'/download-manager/'.$file))
        $path = get_stylesheet_directory().'/download-manager/'.$file;
    else if(file_exists(get_template_directory().'/download-manager/'.$file))
        $path = get_template_directory().'/download-manager/'.$file;
    else if($tpldir !=='' && file_exists($tpldir.'/'.$file))
        $path = $tpldir.'/'.$file;
    else if($tpldir !='' && file_exists(get_template_directory().'/download-manager/'.$tpldir.'/'.$file))
        $path = get_template_directory().'/download-manager/'.$tpldir.'/'.$file;
    else $path = WPDM_TPL_DIR.$file;

    /* Fallack template directory*/
    if($fallback == '') $fallback = WPDM_TPL_FALLBACK;
    if($fallback !== '' && !file_exists($path))
        $path = $fallback."/".$file;

    //if(!file_exists($path))
    //wpdmdd($path);
    return $path;

}

/**
 * @usage Returns download manager template file path
 * @param $file
 * @param string $tpldir
 * @return string
 */
function wpdm_admin_tpl_path($file, $tpldir = '', $fallback = ''){
    if(file_exists(get_stylesheet_directory().'/download-manager/admin/'.$file))
        $path = get_stylesheet_directory().'/download-manager/admin/'.$file;
    else if(file_exists(get_template_directory().'/download-manager/admin/'.$file))
        $path = get_template_directory().'/download-manager/admin/'.$file;
    else if($tpldir !='' && file_exists($tpldir.'/'.$file))
        $path = $tpldir.'/'.$file;
    else if($tpldir !='' && file_exists(get_template_directory().'/download-manager/admin/'.$tpldir.'/'.$file))
        $path = get_template_directory().'/download-manager/admin/'.$tpldir.'/'.$file;
    else $path = WPDM_BASE_DIR."admin/tpls/".$file;

    /* Fallack template directory*/
    if($fallback != '' && !file_exists($path))
        $path = $fallback.$file;

    //if(!file_exists($path))
    //wpdmdd($path);
    return $path;

}


/**
 * @usage Add js to make the file list searchable at front-end
 */
function wpdm_searchable_filelist(){
    if(!is_singular('wpdmpro') || (int)get_option('__wpdm_file_list_paging',0) != 1) return;

    global $post;
    $id =  $post->ID;
    // You may use this filter if you want to change the min files limit
    $min_files = apply_filters('wpdm_searchable_filelist_min_files', 30);
    if(\WPDM\Package::fileCount($id) < $min_files) return;
    ?>

    <script>
        jQuery(function($){
            $.getScript('<?php echo plugins_url('/download-manager/assets/js/jquery.dataTables.min.js'); ?>', function () {
                $('#wpdm-filelist-<?php echo $id; ?>').dataTable({
                    "language": {
                        "zeroRecords": "<?php _e( "No matching files found" , "download-manager" )?>",
                        "search":         ""
                    },
                    "paging":   false,
                    "ordering": false,
                    "info":     false
                });
                $('.dataTables_filter label').css('width','100%');
                $('.dataTables_filter input').addClass('form-control no-radius input-lg').attr('placeholder', '<?php _e( "Search File..." , "download-manager" ) ?>');
            });
        });
    </script>

    <?php
}

function wpdm_user_space_limit($uid = null){
    global $current_user;
    $global = get_option('__wpdm_author_space', 500);
    $uid = $uid?$uid:$current_user->ID;
    $user = get_user_meta($uid, '__wpdm_space', true);
    $space = $user > 0?$user:$global;
    return $space;
}


function wpdm_get_dir_size($dir){
    $bytestotal = 0;
    $path = realpath($dir);
    if($path!==false){
        foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object){
            try {
                $bytestotal += $object->getSize();
            } catch (Exception $e){

            }
        }
    }
    $bytestotal = $bytestotal/1024;
    $bytestotal = $bytestotal/1024;
    return number_format($bytestotal,2);
}

function wpdm_is_url( $url ) {
    $result = ( false !== filter_var( $url, FILTER_VALIDATE_URL ) );
    return apply_filters( 'wpdm_is_url', $result, $url );
}

function wpdm_total_downloads($uid = null, $pid = null){
    global $wpdb;

    if($uid > 0 && !$pid)
        $download_count = $wpdb->get_var("select sum(pm.meta_value) from {$wpdb->prefix}postmeta pm, {$wpdb->prefix}posts p where meta_key='__wpdm_download_count' and p.ID = pm.post_id and p.post_author = '{$uid}'");
    else if($pid > 0 && !$uid)
        $download_count = $wpdb->get_var("select sum(pm.meta_value) from {$wpdb->prefix}postmeta where meta_key='__wpdm_download_count' and post_id = '{$pid}'");
    else if($uid > 0 && $pid > 0)
        $download_count = $wpdb->get_var("select sum(pm.meta_value) from {$wpdb->prefix}postmeta pm, {$wpdb->prefix}posts p where meta_key='__wpdm_download_count' and p.ID = pm.post_id and p.post_author = '{$uid}' and pm.post_id = '{$pid}'");
    else
        $download_count = $wpdb->get_var("select sum(meta_value) from {$wpdb->prefix}postmeta where meta_key='__wpdm_download_count'");
    return (int)$download_count;
}

function wpdm_total_views($uid = null){
    global $wpdb;
    if(isset($uid) && $uid > 0)
        $download_count = $wpdb->get_var("select sum(pm.meta_value) from {$wpdb->prefix}postmeta pm, {$wpdb->prefix}posts p where meta_key='__wpdm_view_count' and p.ID = pm.post_id and p.post_author = '{$uid}'");
    else
        $download_count = $wpdb->get_var("select sum(meta_value) from {$wpdb->prefix}postmeta where meta_key='__wpdm_view_count'");
    return $download_count;
}

/**
 * Find if user is downloaded an item or not
 * @param $pid
 * @param $uid
 * @return bool
 */
function wpdm_is_user_downloaded($pid, $uid){
    global $wpdb;
    $uid = (int)$uid;
    $pid = (int)$pid;
    $ret = $wpdb->get_var("select uid from {$wpdb->prefix}ahm_download_stats where uid='$uid' and pid = '$pid'");
    if($ret && $ret == $uid) return true;
    return false;
}


/**
 * @param $ip
 * @param $range
 * @return bool
 */
function wpdm_ip_in_range( $ip, $range ) {
    // Check IP range
    list($subnet, $bits) = explode('/', $range);
    // Convert subnet to binary string of $bits length
    $subnet = unpack('H*', inet_pton($subnet)); // Subnet in Hex
    foreach($subnet as $i => $h) $subnet[$i] = base_convert($h, 16, 2); // Array of Binary
    $subnet = substr(implode('', $subnet), 0, $bits); // Subnet in Binary, only network bits

    // Convert remote IP to binary string of $bits length
    $ip = unpack('H*', inet_pton($ip)); // IP in Hex
    foreach($ip as $i => $h) $ip[$i] = base_convert($h, 16, 2); // Array of Binary
    $ip = substr(implode('', $ip), 0, $bits); // IP in Binary, only network bits

    // Check network bits match
    if($subnet == $ip) {
        return true;
    }
    return false;
}

/**
 * @param null $ip
 * @return bool
 */
function wpdm_ip_blocked($ip = null){
    $ip = $ip?$ip:wpdm_get_client_ip();
    $allblocked = get_option('__wpdm_blocked_ips', '');
    $allblocked = explode("\n", str_replace("\r", "", $allblocked));
    $isblocked = false;
    foreach ($allblocked as $blocked) {
        if(strstr($blocked, '/'))
            $isblocked = wpdm_ip_in_range($ip, $blocked);
        else if(strstr($blocked, '*')){
            preg_match('/'.$blocked.'/', $ip, $matches);
            $isblocked = count($matches) > 0?true:false;
        } else if( $ip == $blocked )
            $isblocked = true;

        if($isblocked == true) return $isblocked;

    }
    return $isblocked;
}

/**
 * @return string or bool
 */
function wpdm_get_client_ip() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = false;
    if($ipaddress) {
        $ipaddress = explode(",", $ipaddress);
        $ipaddress = $ipaddress[0];
    }
    return $ipaddress;
}

/**
 * Validate download link
 * @param $ID
 * @param $_key
 * @param bool $execute
 * @return bool|int
 * @since 4.7.4
 */
function is_wpdmkey_valid($ID, $_key, $update = false){
    if($_key == '') return 0; // Invalid
    $ID = (int)$ID;
    $_key = wpdm_sanitize_var($_key);
    $key = "__wpdmkey_{$_key}";

    $xlimit = \WPDM\TempStorage::get($key);

    if(!$xlimit)
        $xlimit = get_post_meta($ID, $key, true);

    if(!$xlimit) return 0; // Invalid

    if(!is_array($xlimit) && (int)$xlimit > 0) { $xlimit = array( 'use' => (int)$xlimit, 'expire' => time() + 360 ); }

    $xlimit = maybe_unserialize($xlimit);

    if(!is_array($xlimit)) return 0;

    $limit = isset($xlimit['use'])?(int)$xlimit['use']:0;

    $expired = false;

    if ($limit <= 0) {
        delete_post_meta($ID, $key);
        \WPDM\TempStorage::kill($key);
        return -1; // Limit exceeded
    }
    else {

        $limit --;
        $xlimit['use'] = $limit;

        if((int)$xlimit['expire'] < time()){
            $xlimit['use'] = $limit = 0;
            $expired = true;
            delete_post_meta($ID, $key);
            \WPDM\TempStorage::kill($key);
        }
        if($update) {
            update_post_meta($ID, $key, $xlimit);
            \WPDM\TempStorage::set($key, $xlimit);
        }
        if($expired) return -2; // Time expired
    }
    return 1;
}

/**
 * @param $var
 * @param $index
 * @param array $params
 * @return array|bool|float|int|mixed|string|string[]|null
 */

function wpdm_valueof($var, $index, $params = [])
{
    $index = explode("/" , $index );
    $default = is_string($params) ? $params : '';
    $default = is_array($params) && isset($params['default']) ? $params['default'] : $default;
    if(count($index) > 1){
        $val = $var;
        foreach ($index as $key){
            $val = is_array($val) && isset($val[$key])?$val[$key]:'__not__set__';
            if($val === '__not__set__') return $default;
        }
    } else
        $val = isset($var[$index[0]]) ? $var[$index[0]] : $default;

    if(is_array($params) && isset($params['validate'])) {
        if (!is_array($val))
            $val = wpdm_sanitize_var($val, $params['validate']);
        else
            $val = wpdm_sanitize_array($val, $params['validate']);
    }

    return $val;
}

/**
 * @usage Validate and sanitize input data
 * @param $var
 * @param array $params
 * @return int|null|string
 */
function wpdm_query_var($var, $params = array())
{
    $_var = explode("/" , $var );
    if(count($_var) > 1){
        $val = $_REQUEST;
        foreach ($_var as $key){
            $val = is_array($val) && isset($val[$key])?$val[$key]:false;
        }
    } else
        $val = isset($_REQUEST[$var]) ? $_REQUEST[$var] : ( isset($params['default']) ? $params['default'] : null );
    $validate = is_string($params) ? $params : '';
    $validate = is_array($params) && isset($params['validate']) ? $params['validate'] : $validate;

    if(!is_array($val))
        $val = wpdm_sanitize_var($val, $validate);
    else
        $val = wpdm_sanitize_array($val, $validate);

    return $val;
}

/**
 * Sanitize an array or any single value
 * @param $array
 * @return mixed
 */
function wpdm_sanitize_array($array, $sanitize = 'kses'){
    if(!is_array($array)) return esc_attr($array);
    foreach ($array as $key => &$value){
        $validate = is_array($sanitize) && isset($sanitize[$key]) ? $sanitize[$key] : $sanitize;
        if(is_array($value))
            wpdm_sanitize_array($value, $validate);
        else {
            $value = wpdm_sanitize_var($value, $validate);
        }
        $array[$key] = &$value;
    }
    return $array;
}

/**
 * Sanitize any single value
 * @param $value
 * @return string
 */
function wpdm_sanitize_var($value, $sanitize = 'kses'){
    if(is_array($value))
        return wpdm_sanitize_array($value, $sanitize);
    else {
        switch ($sanitize){
            case 'int':
            case 'num':
                return (int)$value;
                break;
            case 'double':
            case 'float':
                return (double)($value);
                break;
            case 'txt':
            case 'str':
                $value = esc_attr($value);
                break;
            case 'kses':
                $allowedtags = wp_kses_allowed_html();
                $allowedtags['div'] = array('class' => true);
                $allowedtags['strong'] = array('class' => true);
                $allowedtags['b'] = array('class' => true);
                $allowedtags['i'] = array('class' => true);
                $allowedtags['a'] = array('class' => true, 'href' => true);
                $value = wp_kses($value, $allowedtags);
                break;
            case 'serverpath':
                $value = realpath($value);
                $value = str_replace("\\", "/", $value);
                break;
            case 'txts':
                $value = sanitize_textarea_field($value);
                break;
            case 'esc_html':
                $value = esc_html($value);
                break;
            case 'url':
                $value = esc_url($value);
                break;
            case 'noscript':
            case 'escs':
                $value = wpdm_escs($value);
                break;
            case 'filename':
                $value = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '_', $value);
                $value = mb_ereg_replace("([\.]+)", '_', $value);
                break;
            case 'html':

                break;
            default:
                $value = esc_sql(esc_attr($value));
                break;
        }
        $value = wpdm_escs($value);
    }
    return $value;
}

/**
 * @param $total
 * @param $item_per_page
 * @param int $page
 * @param string $var
 * @return string
 */
function wpdm_paginate_links($total, $items_per_page, $current_page = 1, $var = 'cp', $params = array()){

    $pages = ceil($total/$items_per_page);
    $format = isset($params['format']) ? $params['format'] : "?{$var}=%#%";
    $args = array(
        //'base'               => '%_%',
        'format'             => $format,
        'total'              => $pages,
        'current'            => $current_page,
        //'show_all'           => false,
        //'end_size'           => 2,
        //'mid_size'           => 1,
        //'prev_next'          => true,
        'prev_text'          => isset($params['prev_text'])?$params['prev_text']:__('Previous'),
        'next_text'          => isset($params['prev_text'])?$params['next_text']:__('Next'),
        'type'               => 'array',
        //'add_args'           => false,
        //'add_fragment'       => '',
        //'before_page_number' => '',
        //'after_page_number'  => ''
    );
    //wpdmprecho($args);
    $pags = paginate_links($args);
    //wpdmprecho($pags);
    $phtml = "";
    if(is_array($pags)) {
        foreach ($pags as $pagl) {
            if(isset($params['container'])){
                $pagl = str_replace("<a", "<a data-container='{$params['container']}'", $pagl);
            }
            $phtml .= "<li>{$pagl}</li>";
        }
    }
    $async = isset($params['async']) && $params['async']?' async':'';
    $phtml = "<div class='text-center'><ul class='pagination wpdm-pagination pagination-centered text-center{$async}'>{$phtml}</ul></div>";
    return $phtml;
}

/**
 * @usage Escape script tag
 * @param $html
 * @return null|string|string[]
 */
function wpdm_escs($html){
    return preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
}

/**
 * @param null $page_template
 * @param null $pacakge_ID
 * @return mixed|string
 */
function wpdm_download_button_style($page_template = false, $pacakge_ID = null){
    if(is_singular('wpdmpro') || $page_template === true)
        $ui_button = get_option('__wpdm_ui_download_button');
    else
        $ui_button = get_option('__wpdm_ui_download_button_sc');
    $ui_button = wpdm_sanitize_array($ui_button);
    $class =  "btn ".(isset($ui_button['color'])?$ui_button['color']:'btn-primary')." ".(isset($ui_button['size'])?$ui_button['size']:'');
    $class = apply_filters("wpdm_download_button_style", $class, $pacakge_ID);
    return $class;
}

function wpdm_hex2rgb($hex){
    list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
    return "$r, $g, $b";
}



/*** developer fns **/
function  wpdmdd($data)
{
    echo "<pre>" . print_r($data, 1) . "</pre>";
    die();
}
function wpdmprecho($data, $ret = 0){
    $echo = "<pre>" . print_r($data, 1) . "</pre>";
    if($ret == 1) return $echo;
    echo $echo;
}
/*** developer fns **/




