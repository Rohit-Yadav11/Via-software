<?php

namespace WPDM\libs;

use WPDM\libs\FileSystem;
use WPDM\Package;
use WPDM\AuthorDashboard;
use WPDM\Session;
use WPDM\Template;
use WPDM\TempStorage;
use WPDM_Messages;

class Apply
{

    function __construct()
    {

        add_filter('wpdm_custom_data', array($this, 'SR_CheckPackageAccess'), 10, 2);

        //add_action('save_post', array( $this, 'SavePackage' ));
        add_action('publish_wpdmpro', array($this, 'customPings'));

        add_action('after_switch_theme', array($this, 'flashRules'));

        $this->adminActions();
        $this->frontendActions();

    }

    function frontendActions()
    {

        add_action("wp_ajax_nopriv_updatePassword", array($this, 'updatePassword'));
        add_action("wp_ajax_nopriv_resetPassword", array($this, 'resetPassword'));
        add_action("wp_ajax_nopriv_showLockOptions", array($this, 'showLockOptions'));
        add_action("wp_ajax_showLockOptions", array($this, 'showLockOptions'));
        add_action("wp_ajax_wpdm_addtofav", array($this, 'addToFav'));

        add_action('wp_ajax_wpdm_verify_file_pass', array($this, 'checkFilePassword'));
        add_action('wp_ajax_nopriv_wpdm_verify_file_pass', array($this, 'checkFilePassword'));

        add_action("wp_ajax_wpdm_email_lock", [$this, 'verifyEmailLock']);
        add_action("wp_ajax_nopriv_wpdm_email_lock", [$this, 'verifyEmailLock']);

        add_filter("login_url", array($this, 'loginURL'), 999999, 3);
        add_filter("logout_url", array($this, 'logoutURL'), 999999, 2);

        add_filter("init", array($this, 'loginURLRedirect'));
        add_action('wp', array($this, 'wpdmIframe'));
        add_action("wp", array($this, 'shortcodeIframe'), 0);
        add_action("wp", array($this, 'customDownloadLinkPage'), 0);

        add_filter("template_include", array($this, 'interimLogin'), 9999);

        if (is_admin()) return;

        //add_action("pre_get_posts", array($this, 'archivePage'), 0);

        //add_action("init", array($this, 'playMedia'), 8);

        add_filter("template_include", array($this, 'wpdmproTemplates'), 9999);

        add_action("init", array($this, 'triggerDownload'), 9);
        add_action('init', array($this, 'addWriteRules'), 0);
        add_action('init', array($this, 'login'));
        add_action('init', array($this, 'register'));
        add_action('init', array($this, 'logout'));

        add_filter('the_content', array($this, 'validateLoginPage'));
        add_filter('widget_text', 'do_shortcode');

        add_action('query_vars', array($this, 'dashboardPageVars'), 1);
        add_action('request', array($this, 'rssFeed'));
        add_filter('pre_get_posts', array($this, 'queryTag'));

        add_action('init', array($this, 'updateProfile'));
        add_filter('ajax_query_attachments_args', array($this, 'usersMediaQuery'));


        add_action('wp_head', array($this, 'addGenerator'), 9999);
        add_filter('post_comments_feed_link', array($this, 'removeCommentFeed'));

        add_filter('the_excerpt_embed', array($this, 'oEmbed'));
        add_action('registration_errors', array($this, 'verifyEmail'), 10, 3);
        add_action('authenticate', array($this, 'verifyLoginEmail'), 999999, 3);

        //add_filter( 'get_avatar_url', array($this, 'shopLogo'), 10, 3 );
        add_filter('get_avatar_data', array($this, 'shopLogo'), 10, 3);

        add_action('wp_head', array($this, 'wpHead'), 999999);

    }

    function adminActions()
    {
        if (!is_admin()) return;
        add_action('save_post', array($this, 'dashboardPages'));
        //add_action('admin_init', array($this, 'sfbAccess'));
        add_action('wp_ajax_clear_cache', array($this, 'clearCache'));
        add_action('wp_ajax_clear_stats', array($this, 'clearStats'));
        add_action('wp_ajax_generate_tdl', array($this, 'generateTDL'));
        add_action('admin_head', array($this, 'uiColors'));

    }

    function authorPage($wp_query)
    {
        if ((int)$wp_query->is_author === 1 && ($ppid = WPDM()->setting->author_profile('int')) > 0 && $ppid === $wp_query->query_vars['page_id']) {
            wpdmdd($ppid);
            unset($wp_query->query['post_type']);
            $pagename = get_pagename($ppid);
            $wp_query->query = array('page' => '', 'pagename' => $pagename);
            $wp_query->set('author_name', null);
            $wp_query->set('pagename', $pagename);
            $wp_query->is_archive = false;
            $wp_query->is_post_type_archive = false;
            $wp_query->queried_object_id = $ppid;
            $wp_query->queried_object = get_post($ppid);
        }
        //wpdmdd($wp_query);
        return $wp_query;
    }

    function SR_CheckPackageAccess($data, $id)
    {
        global $current_user;
        $skiplocks = maybe_unserialize(get_option('__wpdm_skip_locks', array()));
        if (is_user_logged_in()) {
            foreach ($skiplocks as $lock) {
                unset($data[$lock . "_lock"]); // = 0;
            }
        }

        return $data;
    }

    function docStream()
    {
        if (strstr($_SERVER['REQUEST_URI'], 'wpdm-doc-preview')) {
            preg_match("/wpdm\-doc\-preview\/([0-9]+)/", $_SERVER['REQUEST_URI'], $mat);
            $file_id = $mat[1];
            $files = Package::getFiles($file_id);
            if (count($files) == 0) die('No file found!');
            $sfile = '';
            foreach ($files as $i => $sfile) {
                $ifile = $sfile;
                $sfile = explode(".", $sfile);
                $fext = end($sfile);
                if (in_array(end($sfile), array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'))) {
                    $sfile = $ifile;
                    break;
                }
            }
            if ($sfile == '') die('No supported document found!');
            if (file_exists(UPLOAD_DIR . $sfile)) $sfile = UPLOAD_DIR . $sfile;
            if (!file_exists($sfile)) die('No supported document found!');

            if (strstr($sfile, '://')) header("location: {$sfile}");
            else
                FileSystem::downloadFile($sfile, basename($sfile));
            die();
        }
    }

    function addWriteRules()
    {
        global $wp_rewrite;
        $udb_page_id = get_option('__wpdm_user_dashboard', 0);
        if ($udb_page_id) {
            $page_name = get_post_field("post_name", $udb_page_id);
            add_rewrite_rule('^' . $page_name . '/(.+)/?', 'index.php?page_id=' . $udb_page_id . '&udb_page=$matches[1]', 'top');
            //dd($wp_rewrite);
        }
        $adb_page_id = get_option('__wpdm_author_dashboard', 0);

        if ($adb_page_id) {
            $page_name = get_post_field("post_name", $adb_page_id);
            add_rewrite_rule('^' . $page_name . '/(.+)/?', 'index.php?page_id=' . $adb_page_id . '&adb_page=$matches[1]', 'top');
        }

        $ap_page_id = get_option('__wpdm_author_profile', 0);

        if ($ap_page_id) {
            $page_name = get_post_field("post_name", $ap_page_id);
            add_rewrite_rule('^' . $page_name . '/(.+)/?$', 'index.php?pagename=' . $page_name . '&profile=$matches[1]', 'top');
        }

        //wpdmdd($wp_rewrite);
        //add_rewrite_rule('^wpdmdl/([0-9]+)/?', 'index.php?wpdmdl=$matches[1]', 'top');
        //add_rewrite_rule('^wpdmdl/([0-9]+)/ind/([^\/]+)/?', 'index.php?wpdmdl=$matches[1]&ind=$matches[2]', 'top');
        //if(is_404()) dd('404');
        //$wp_rewrite->flush_rules();
        //dd($wp_rewrite);
    }

    function flashRules()
    {
        $this->addWriteRules();
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }

    function wpdmproTemplates($template){
        $_template = basename($template);
        $style_global = get_option('__wpdm_cpage_style', 'basic');
        $style = get_term_meta(get_queried_object_id(), '__wpdm_style', true);
        $style = in_array($style, ['basic', 'ltpl']) ? $style : $style_global;
        if($style === 'ltpl' && (is_tax('wpdmcategory') || is_post_type_archive('wpdmpro'))){
            $template = Template::locate("taxonomy-wpdmcategory.php", WPDM_TPL_FALLBACK, WPDM_TPL_FALLBACK);
        }
        /*if($_template !== 'single-wpdmpro.php' && is_singular('wpdmpro')){
            $template = Template::locate("single-wpdmpro.php", WPDM_TPL_FALLBACK, WPDM_TPL_FALLBACK);
        }*/
        return $template;
    }

    function dashboardPages($post_id)
    {
        if (wp_is_post_revision($post_id)) return;
        if (get_post_type($post_id) !== 'page') return;
        $page_id = get_option('__wpdm_user_dashboard', 0);
        $post = get_post($post_id);
        $flush = 0;

        //If no dashboard page is selected ( $page_id === 0 )
        // And current page is dashboard shorotcode
        if ((int)$page_id === 0 && has_shortcode($post->post_content, "wpdm_user_dashboard")) {
            update_option('__wpdm_user_dashboard', $post_id);
            $flush = 1;
        }

        if (has_shortcode($post->post_content, "wpdm_package_form")) {
            update_option('__wpdm_package_form', $post_id);
        }

        $page_id = get_option('__wpdm_author_dashboard', 0);

        if ((int)$page_id === 0 && has_shortcode($post->post_content, "wpdm_frontend")) {
            update_option('__wpdm_author_dashboard', $post_id);
            $flush = 1;
        }

        $page_id = get_option('__wpdm_author_profile', 0);

        if ((int)$page_id === 0 && has_shortcode($post->post_content, "wpdm_user_profile")) {
            update_option('__wpdm_author_profile', $post_id);
            $flush = 1;
        }

        if ($flush == 1) {
            $this->addWriteRules();
            global $wp_rewrite;
            $wp_rewrite->flush_rules();
        }

    }

    function dashboardPageVars($vars)
    {
        array_push($vars, 'udb_page', 'adb_page', 'page_id', 'wpdmdl', 'ind', 'profile', 'wpdm_asset_key');
        return $vars;
    }

    function login()
    {

        global $wp_query, $post, $wpdb;
        if (!isset($_POST['wpdm_login'])) return;

        $shortcode_params = Crypt::decrypt(wpdm_query_var('__phash'), true);

        $login_try = (int)Session::get('login_try');
        $login_try++;
        Session::set('login_try', $login_try);

        if ($login_try > 30) wp_die("Slow Down!");

        if(!isset($shortcode_params['captcha']) || $shortcode_params['captcha'] ===  true) {
            $active_captcha = (int)get_option('__wpdm_recaptcha_loginform', 0) === 1 && get_option('_wpdm_recaptcha_secret_key', '') != '';
            $active_captcha = apply_filters("signin_form_captcha", $active_captcha);
            if ($active_captcha) {
                $ret = wpdm_remote_post('https://www.google.com/recaptcha/api/siteverify', array('secret' => get_option('_wpdm_recaptcha_secret_key', ''), 'response' => wpdm_query_var('__recap')));
                $ret = json_decode($ret);
                if (!$ret->success) {
                    $login_error = __("Invalid CAPTCHA!", "download-manager");
                    if (wpdm_is_ajax()) {
                        wp_send_json(array('success' => false, 'message' => 'Error: ' . $login_error));
                        die();
                    }
                    Session::set('login_error', $login_error);
                    wp_safe_redirect(wpdm_query_var('permalink', 'url'));
                    die();
                }
            }
        }

        Session::clear('login_error');
        $creds = array();
        $creds['user_login'] = isset($_POST['wpdm_login']['log']) ? $_POST['wpdm_login']['log'] : '';
        $creds['user_password'] = isset($_POST['wpdm_login']['pwd']) ? $_POST['wpdm_login']['pwd'] : '';
        $creds['remember'] = isset($_POST['rememberme']) ? $_POST['rememberme'] : false;
        $user = wp_signon($creds, false);
        if (is_wp_error($user)) {
            $login_error = $user->get_error_message();
            if (wpdm_is_ajax())
                wp_send_json(array('success' => false, 'message' => $login_error));

            Session::set('login_error', $login_error);
            wp_safe_redirect($_SERVER['HTTP_REFERER']);
            die();
        } else {
            wp_set_auth_cookie($user->ID);
            wp_set_current_user($user->ID);
            Session::set('login_try', 0);
            //do_action('wp_login', $creds['user_login'], $user);

            if (wpdm_is_ajax())
                wp_send_json(array('success' => true, 'message' => __("Success! Redirecting...", "download-manager")));

            wp_safe_redirect(wpdm_query_var('redirect_to', 'url'));
            die();
        }
    }

    /**
     * @usage Logout an user
     */

    function logout()
    {

        if (isset($_REQUEST['logout']) && wp_verify_nonce(wpdm_query_var('logout'), NONCE_KEY)) {
            wp_logout();
            wp_safe_redirect(wpdm_login_url());
            die();
        }
    }

    /**
     * @usage Register an user
     */
    function register()
    {
        global $wp_query, $wpdb;
        if (!isset($_POST['wpdm_reg'])) return;


        $shortcode_params = Crypt::decrypt(wpdm_query_var('__phash'), true);

        if (!is_array($shortcode_params)) $shortcode_params = array();

        $_verify_email = (int)get_option('__wpdm_signup_email_verify', 0);

        if (!isset($_REQUEST['__reg_nonce']) || !wp_verify_nonce($_REQUEST['__reg_nonce'], NONCE_KEY)) {
            $reg_error = apply_filters("wpdm_signup_error", __("Something is Wrong! Please refresh the page and try again", "download-manager"), $error_type = 'nonce');
            if (wpdm_is_ajax()) {
                wp_send_json(array('success' => false, 'message' => 'Error: ' . $reg_error));
                die();
            }
            Session::set('wpdm_signup_error', $reg_error, 300);
            wp_safe_redirect(wpdm_query_var('permalink', 'url'));
            die();
        }

        if(!isset($shortcode_params['captcha']) || $shortcode_params['captcha'] ===  true) {
            $active_captcha = (int)get_option('__wpdm_recaptcha_regform', 0) === 1 && get_option('_wpdm_recaptcha_secret_key', '') != '';
            $active_captcha = apply_filters("signup_form_captcha", $active_captcha);
            if ($active_captcha) {
                $ret = wpdm_remote_post('https://www.google.com/recaptcha/api/siteverify', array('secret' => get_option('_wpdm_recaptcha_secret_key', ''), 'response' => wpdm_query_var('__recap')));
                $ret = json_decode($ret);
                if (!$ret->success) {
                    $reg_error = apply_filters("wpdm_signup_error", __("Invalid CAPTCHA!", "download-manager"), $error_type = 'captcha');
                    if (wpdm_is_ajax()) {
                        wp_send_json(array('success' => false, 'message' => 'Error: ' . $reg_error));
                        die();
                    }
                    Session::set('wpdm_signup_error', $reg_error, 300);
                    wp_safe_redirect(wpdm_query_var('permalink', 'url'));
                    die();
                }
            }
        }

        if (!get_option('users_can_register') && isset($_POST['wpdm_reg'])) {
            $reg_error = apply_filters("wpdm_signup_error", __("Error: User registration is disabled!", "download-manager"), $error_type = 'signup_disabled');
            if (wpdm_is_ajax()) {
                wp_send_json(array('success' => false, 'message' => $reg_error));
                die();
            }
            Session::set('wpdm_signup_error', $reg_error, 300);
            wp_safe_redirect(wpdm_query_var('permalink', 'url'));
            die();
        }

        Session::set('tmp_reg_info', $_POST['wpdm_reg']);

        $first_name = wpdm_query_var('wpdm_reg/first_name', 'txt');
        $last_name = wpdm_query_var('wpdm_reg/last_name', 'txt');
        $user_login = wpdm_query_var('wpdm_reg/user_login', 'txt');
        $user_email = wpdm_query_var('wpdm_reg/user_email', 'email');
        $user_pass = wpdm_query_var('wpdm_reg/user_pass', 'html');
        $full_name = $first_name . " " . $last_name;
        $display_name = $full_name;

        $user_id = username_exists($user_login);

        //Check Username
        if ($user_login === '') {

            $reg_error = apply_filters("wpdm_signup_error", __("Username is Empty!", "download-manager"), $error_type = 'empty_username');

            if (wpdm_is_ajax()) {
                wp_send_json(array('success' => false, 'message' => $reg_error));
                die();
            }

            Session::set('wpdm_signup_error', $reg_error, 300);
            wp_safe_redirect(wpdm_query_var('permalink', 'url'));
            die();
        }

        //Check Email
        if (!is_email($user_email)) {

            $reg_error = apply_filters("wpdm_signup_error", __("Invalid email address!", "download-manager"), $error_type = 'invalid_email');

            if (wpdm_is_ajax()) {
                wp_send_json(array('success' => false, 'message' => $reg_error));
                die();
            }

            Session::set('wpdm_signup_error', $reg_error, 300);
            wp_safe_redirect(wpdm_query_var('permalink', 'url'));

        }

        //If username is available - no other user with the username
        if (!$user_id) {

            $user_id = email_exists($user_email);

            //If email is available - no other user with the email
            if (!$user_id) {

                $user_pass = $_verify_email || !isset($user_pass) || $user_pass == '' ? wp_generate_password(12, false) : $user_pass;

                if(!$_verify_email && isset($user_pass) && wpdm_query_var('comfirm_user_pass')){
                    if($user_pass !== wpdm_query_var('confirm_user_pass', 'html')){
                        $reg_error = apply_filters("wpdm_signup_error", __("Passwords not matched!", "download-manager"), $error_type = 'password_match');

                        if (wpdm_is_ajax()) {
                            wp_send_json(array('success' => false, 'message' => $reg_error));
                            die();
                        }

                        Session::set('wpdm_signup_error', $reg_error, 300);
                        wp_safe_redirect(wpdm_query_var('permalink', 'url'));
                    }
                }

                $errors = new \WP_Error();

                do_action('register_post', $user_login, $user_email, $errors);

                $errors = apply_filters('registration_errors', $errors, $user_login, $user_email);

                if ($errors->get_error_code()) {
                    if (wpdm_is_ajax()) {
                        wp_send_json(array('success' => false, 'message' => 'Error: ' . $errors->get_error_message()));
                        die();
                    }

                    Session::set('wpdm_signup_error', 'Error: ' . $errors->get_error_message(), 300);
                    wp_safe_redirect(wpdm_query_var('permalink', 'url'));
                    die();
                }

                $user_id = wp_create_user($user_login, $user_pass, $user_email);

                $user_meta = array('ID' => $user_id, 'display_name' => $display_name, 'first_name' => $first_name, 'last_name' => $last_name);

                if(!defined('WPDM_DISABLE_SIGNUP_ROLES') || WPDM_DISABLE_SIGNUP_ROLES === false) {
                    $valid_roles = get_option('__wpdm_signup_roles', array());

                    if (isset($shortcode_params['role']) && trim($shortcode_params['role']) !== '' && is_array($valid_roles) && count($valid_roles) > 0 && in_array($shortcode_params['role'], $valid_roles)) {
                        $role = $shortcode_params['role'];
                        //Block front-end signup as administrator or any role with edit_files cap
                        if($role !== 'administrator') {
                            $role = get_role($role);
                            $caps = is_object($role) && isset($role->capabilities) ? $role->capabilities : array();
                            if (!in_array('edit_files', $caps))
                                $user_meta['role'] = $role;
                        }

                    }
                }
                wp_update_user($user_meta);

                //To User
                $usparams = array('to_email' => $user_email, 'name' => $display_name, 'first_name' => $first_name, 'last_name' => $last_name, 'user_email' => $user_email, 'username' => $user_login, 'password' => $user_pass);

                foreach ($_POST['wpdm_reg'] as $key => $value) {
                    $usparams["user_" . $key] = $value;
                }

                //Filter user email params
                $usparams = apply_filters("wpdm_signup_user_mail_params", $usparams, $user_id);

                \WPDM\Email::send("user-signup", $usparams);

                //To Admin
                $ip = wpdm_get_client_ip();
                $data = array(
                    array('Name', $display_name),
                    array('Username', $user_login),
                    array('Email', $user_email),
                    array('IP', $ip)
                );
                $css = array('col' => array('background: #edf0f2 !important'), 'td' => 'border-bottom:1px solid #e6e7e8');
                $table = MailUI::table(null, $data, $css);

                $params['name'] = $display_name;
                $params['username'] = $user_login;
                $params['email'] = $user_email;
                $params['user_ip'] = $ip;
                $params['edit_user_btn'] = "<a class='button' style='display:block;margin:10px 0 0;text-decoration: none;text-align:center;' href='" . admin_url('user-edit.php?user_id=' . $user_id) . "'> " . __("Edit User", "download-manager") . " </a>";
                //Include all data from signup form
                foreach ($_POST['wpdm_reg'] as $key => $value) {
                    $params["user_" . $key] = $value;
                }

                //Filter admin email params
                $params = apply_filters("wpdm_signup_admin_mail_params", $params, $user_id);
                \WPDM\Email::send("user-signup-admin", $params);

                Session::clear('guest_order');
                Session::clear('login_error');
                Session::clear('tmp_reg_info');

                $creds['user_login'] = $user_login;
                $creds['user_password'] = $user_pass;
                $creds['remember'] = true;

                if($_verify_email)
                    $reg_success = apply_filters("wpdm_signup_success", __("Your account has been created successfully and login info sent to your mail address.", "download-manager"));
                else
                    $reg_success = apply_filters("wpdm_signup_success", __("Your account has been created successfully.", "download-manager"));

                do_action("wpdm_user_signup", $user_id, wpdm_query_var('wpdm_reg') );

                $_auto_login = isset($shortcode_params['autologin']) && ($shortcode_params['autologin'] === 'true' || (int)$shortcode_params['autologin'] === 1) ? true : false;
                if ((int)get_option('__wpdm_signup_autologin', 0) === 1|| $_auto_login) {
                    $reg_success = apply_filters("wpdm_signup_success", __("Your account has been created successfully.", "download-manager"));
                    wp_signon($creds);
                    wp_set_current_user($user_id);
                    wp_set_auth_cookie($user_id);
                }

                \WPDM\Session::set('__wpdm_signup_success', $reg_success, 300);

                if (wpdm_is_ajax()) {
                    $redirect = wpdm_query_var('reg_redirect', 'url');
                    if(!$_auto_login)
                        $redirect = add_query_arg(['signedup' => 1], $redirect);
                    wp_send_json(array('success' => true, 'redirect_to' => $redirect, 'msg' => Session::get('__wpdm_signup_success')));
                    die();
                }
                wp_safe_redirect(wpdm_query_var('reg_redirect', 'url'));
                die();
            } else {
                $reg_error = apply_filters("wpdm_signup_error", __("Email already exist!", "download-manager"), $error_type = 'invalid_email');

                if (wpdm_is_ajax()) {
                    wp_send_json(array('success' => false, 'message' => $reg_error));
                    die();
                }

                \WPDM\Session::set('wpdm_signup_error', $reg_error);
                wp_safe_redirect(wpdm_query_var('permalink', 'url'));

                die();
            }
        } else {
            $reg_error = apply_filters("wpdm_signup_error", __("Username already exists.", "download-manager"), $error_type = 'username_exists');

            if (wpdm_is_ajax()) {
                wp_send_json(array('success' => false, 'message' => $reg_error));
                die();
            }

            Session::set('wpdm_signup_error', $reg_error, 300);
            wp_safe_redirect(wpdm_query_var('permalink', 'url'));
            die();
        }

    }

    function updateProfile()
    {
        global $current_user;

        if (isset($_POST['wpdm_profile']) && is_user_logged_in() && wp_verify_nonce(wpdm_query_var('__wpdm_epnonce'), NONCE_KEY)) {

            $error = 0;

            $pfile_data['display_name'] = wpdm_sanitize_var($_POST['wpdm_profile']['display_name']);
            $pfile_data['user_email'] = sanitize_email($_POST['wpdm_profile']['user_email']);


            if ($_POST['password'] != $_POST['cpassword']) {
                Session::set('member_error', 'Password not matched');
                $error = 1;
            }
            if (!$error) {
                $pfile_data['ID'] = $current_user->ID;
                if ($_POST['password'] != '')
                    $pfile_data['user_pass'] = $_POST['password'];

                wp_update_user($pfile_data);

                update_user_meta($current_user->ID, 'payment_account', $_POST['payment_account']);
                Session::set('member_success', 'Profile data updated successfully.');
            }

            do_action("wpdm_update_profile");

            if (wpdm_is_ajax()) {
                if (ob_get_length() > 0) @ob_clean();
                if ($error == 1) {
                    $msg['type'] = 'danger';
                    $msg['title'] = 'ERROR!';
                    $msg['msg'] = Session::get('member_error');
                    Session::clear('member_error');
                    wp_send_json($msg);
                    die();
                } else {
                    $msg['type'] = 'success';
                    $msg['title'] = 'DONE!';
                    $msg['msg'] = Session::get('member_success');
                    Session::clear('member_success');
                    $msg['data'] = $pfile_data;
                    wp_send_json($msg);
                    die();
                }
            }
            header("location: " . $_SERVER['HTTP_REFERER']);
            die();
        }
    }

    function playMedia()
    {

        if (strstr("!{$_SERVER['REQUEST_URI']}", "/wpdm-media/")) {
            $media = explode("wpdm-media/", $_SERVER['REQUEST_URI']);
            $media = explode("/", $media[1]);
            list($ID, $file, $name) = $media;
            $key = wpdm_query_var('_wpdmkey');

            if (isset($_SERVER['HTTP_RANGE'])) {
                $partialContent = true;
                preg_match('/bytes=(\d+)-(\d+)?/', $_SERVER['HTTP_RANGE'], $matches);
                $offset = intval($matches[1]);
                $length = intval($matches[2]) - $offset;
            } else {
                $partialContent = false;
            }

            $keyValid = is_wpdmkey_valid($ID, $key, true);
            wpdmdd($keyValid);
            if (!$partialContent) {
                if ($key == '' || !$keyValid)
                    \WPDM_Messages::Error(stripslashes(get_option('__wpdm_permission_denied_msg')), 1);
            }
            $files = \WPDM\Package::getFiles($ID);
            $file = $files[$file];
            $file = \WPDM\libs\FileSystem::fullPath($file, $ID);
            $stream = new \WPDM\libs\StreamMedia($file);
            $stream->start();
            die();
        }
    }


    /**
     * @usage Process Download Request from lock options
     */
    function triggerDownload()
    {

        global $wpdb, $current_user, $wp_query;
        if (preg_match("/\/wpdmdl\/([\d]+)-([^\/]+)\/(.+)/", $_SERVER['REQUEST_URI'])) {
            $uri = trim($_SERVER['REQUEST_URI'], '/');
            $download_url_base = get_option('__wpdm_download_url_base', 'download');
            $uri = explode("/" . $download_url_base . "/", $uri);
            $parts = explode("/", $uri[1]);
            $parts = explode("-", $parts[0]);
            $_REQUEST['wpdmdl'] = $_GET['wpdmdl'] = (int)$parts[0];
            $wp_query->query_vars['wpdmdl'] = (int)$parts[0];
            $parts = json_decode(base64_decode($parts[1]));
            if (is_array($parts)) {
                foreach ($parts as $key => $val) {
                    $_REQUEST[$key] = $_GET[$key] = $val;
                }
            }
        }

        //Instant download link processing
        if (isset($_GET['wpdmidl'])) {
            $file = TempStorage::get("__wpdm_instant_download_" . wpdm_query_var('wpdmidl'));
            if (!$file)
                \WPDM_Messages::error(__("Download ID not found or expired", "download-manager"), 1);
            if (!file_exists($file))
                \WPDM_Messages::error(__("The file is already removed from the server!", "download-manager"), 1);

            FileSystem::downloadFile($file, wpdm_basename($file));
            die();
        }

        //Regular download processing
        if (!isset($wp_query->query_vars['wpdmdl']) && !isset($_GET['wpdmdl'])) return;


        $id = isset($_GET['wpdmdl']) ? (int)$_GET['wpdmdl'] : (int)$wp_query->query_vars['wpdmdl'];
        if ($id <= 0) return;

        //Master key validation
        $masterKey = wpdm_query_var('masterkey');
        $hasMasterKey = $masterKey !== '' ? true : false;
        $isMasterKeyValid = Package::validateMasterKey($id, $masterKey);
        $isMaster = $hasMasterKey && $isMasterKeyValid;

        //Temporary download key validation
        $key = wpdm_query_var('_wpdmkey');
        $key = $key == '' && array_key_exists('_wpdmkey', $wp_query->query_vars) ? $wp_query->query_vars['_wpdmkey'] : $key;
        $key = preg_replace("/[^_a-z|A-Z|0-9]/i", "", $key);

        $keyValid = 0;

        if ($key) {
            $keyValid = is_wpdmkey_valid($id, $key, true);

            if ((int)$keyValid !== 1) {
                \WPDM_Messages::error(__("&mdash; Invalid download link &mdash;", "download-manager"), 1);
            }
        }


        if (Package::isLocked($id) && !$keyValid && !$isMaster)
            \WPDM_Messages::error(__("&mdash; You are not allowed to download &mdash;", "download-manager"), 1);


        $package = get_post($id, ARRAY_A);
        if(!is_array($package)) \WPDM_Messages::error(__("&mdash; Package Not Found &mdash;", "download-manager"), 1);
        $package = array_merge($package, \WPDM\Package::metaData($id));

        if (isset($package['files']))
            $package['files'] = maybe_unserialize($package['files']);
        else
            $package['files'] = array();
        //$package = wpdm_setup_package_data($package);

        $package['access'] = wpdm_allowed_roles($id);


        if (is_array($package)) {

            //$cpackage = apply_filters('before_download', $package);


            if ($isMaster || $keyValid) {
                $package['access'] = array('guest');
            }


            $matched = (is_array(@maybe_unserialize($package['access'])) && is_user_logged_in()) ? array_intersect($current_user->roles, @maybe_unserialize($package['access'])) : array();


            if ((($id != '' && is_user_logged_in() && count($matched) < 1 && !@in_array('guest', $package['access'])) || (!is_user_logged_in() && !@in_array('guest', $package['access']) && $id != ''))) {
                do_action("wpdm_download_permission_denied", $id);
                wpdm_download_data("permission-denied.txt", __("You don't have permission to download this file", "download-manager"));
                die();
            } else {

                if ($package['ID'] > 0) {

                    if ((int)$package['quota'] == 0 || $package['quota'] > $package['download_count']) {
                        $package['force_download'] = wpdm_query_var('_wpdmkey');
                        include(WPDM_BASE_DIR . "wpdm-start-download.php");
                    } else
                        wpdm_download_data("stock-limit-reached.txt", __("Stock Limit Reached", "download-manager"));

                }

            }
        } else
            \WPDM_Messages::error(__("Invalid download link.", "download-manager"), 1);
    }


    /**
     * @usage Add with main RSS feed
     * @param $query
     * @return mixed
     */
    function rssFeed($query)
    {
        if (isset($query['feed']) && !isset($query['post_type']) && get_option('__wpdm_rss_feed_main', 0) == 1) {
            $query['post_type'] = array('post', 'wpdmpro');
        }
        return $query;
    }

    /**
     * @usage Schedule custom ping
     * @param $post_id
     */
    function customPings($post_id)
    {
        wp_schedule_single_event(time() + 5000, 'do_pings', array($post_id));
    }

    /**
     * @usage Allow access to server file browser for selected user roles
     */
    function sfbAccess()
    {

        global $wp_roles;
        if (!is_array($wp_roles->roles)) return;
        $roleids = array_keys($wp_roles->roles);
        $roles = get_option('_wpdm_file_browser_access', array('administrator'));
        $naroles = array_diff($roleids, $roles);
        foreach ($roles as $role) {
            $role = get_role($role);
            if (is_object($role) && !is_wp_error($role))
                $role->add_cap('access_server_browser');
        }

        foreach ($naroles as $role) {
            $role = get_role($role);
            if (is_object($role) && !is_wp_error($role)  && in_array('access_server_browser', $role->capabilities)) {
                $role->remove_cap('access_server_browser');
            }
        }

    }

    /**
     * @usage Validate individual file password
     */
    function checkFilePassword()
    {
        if (isset($_POST['actioninddlpvr'], $_POST['wpdmfileid']) && $_POST['actioninddlpvr'] != '') {
            $limit = get_option('__wpdm_private_link_usage_limit', 3);
            $fileid = intval($_POST['wpdmfileid']);
            $data = get_post_meta($_POST['wpdmfileid'], '__wpdm_fileinfo', true);
            $data = $data ? $data : array();
            $package = get_post($fileid);
            $packagemeta = wpdm_custom_data($fileid);
            $password = isset($data[$_POST['wpdmfile']]['password']) && $data[$_POST['wpdmfile']]['password'] != "" ? $data[$_POST['wpdmfile']]['password'] : $packagemeta['password'];
            $pu = isset($packagemeta['password_usage']) && is_array($packagemeta['password_usage']) ? $packagemeta['password_usage'] : array();
            if ($password == $_POST['filepass'] || strpos($password, "[" . $_POST['filepass'] . "]") !== FALSE) {
                $pul = $packagemeta['password_usage_limit'];
                if (is_array($pu) && isset($pu[$password]) && $pu[$password] >= $pul && $pul > 0) {
                    $data['error'] = __("Password usages limit exceeded", "download-manager");
                    die('|error|');
                } else {
                    if (!is_array($pu)) $pu = array();
                    $pu[$password] = isset($pu[$password]) ? $pu[$password] + 1 : 1;
                    update_post_meta($fileid, '__wpdm_password_usage', $pu);
                }

                //$id = uniqid();
                //Session::set( '__wpdm_unlocked_'.$_POST['wpdmfileid'] , 1 );
                //update_post_meta($fileid, "__wpdmkey_".$id, 8);
                $_data['error'] = '';
                $_data['downloadurl'] = \WPDM\Package::expirableDownloadLink($fileid, $limit);
                $_data['downloadurl'] .= "&ind=" . wpdm_query_var('wpdmfile');
                wp_send_json($_data);

            } else
                wp_send_json(array('error' => __("Invalid password", "download-manager"), 'downloadurl' => ''));
        }
    }

    /**
     * @usage Allow front-end users to access their own files only
     * @param $query_params
     * @return string
     */
    function usersMediaQuery($query_params)
    {
        global $current_user;

        if (current_user_can('edit_posts')) return $query_params;

        if (is_user_logged_in()) {
            $query_params['author'] = $current_user->ID;
        }
        return $query_params;
    }

    /**
     * @usage Add packages wth tag query
     * @param $query
     * @return mixed
     */
    function queryTag($query)
    {

        if ($query->is_tag() && $query->is_main_query()) {
            $post_type = get_query_var('post_type');
            if (!is_array($post_type))
                $post_type = array('post', 'page', 'wpdmpro', 'nav_menu_item');
            else
                $post_type = array_merge($post_type, array('post', 'wpdmpro', 'nav_menu_item'));
            $query->set('post_type', $post_type);
        }
        return $query;
    }

    /**
     * Empty cache dir
     */
    function clearCache()
    {
        if (!current_user_can('manage_options')) die('You are not authorized!');
        \WPDM\libs\FileSystem::deleteFiles(WPDM_CACHE_DIR, false);
        \WPDM\libs\FileSystem::deleteFiles(WPDM_CACHE_DIR . 'pdfthumbs/', false);
        die('ok');
    }

    /**
     * Delete all download hostory
     */
    function clearStats()
    {
        if (!current_user_can('manage_options')) return;
        global $wpdb;
        $wpdb->query('truncate table ' . $wpdb->prefix . 'ahm_download_stats');
        $wpdb->query("delete from {$wpdb->prefix}postmeta where meta_key='__wpdmx_user_download_count'");
        die('ok');
    }


    /**
     * @usage Add generator tag
     */
    function addGenerator()
    {
        echo '<meta name="generator" content="WordPress Download Manager ' . WPDM_Version . '" />' . "\r\n";
    }

    function oEmbed($content)
    {
        if (get_post_type(get_the_ID()) !== 'wpdmpro') return $content;
        if (function_exists('wpdmpp_effective_price') && wpdmpp_effective_price(get_the_ID()) > 0)
            $template = '<table class="table table-bordered"><tbody><tr><td colspan="2">[excerpt_200]</td></tr><tr><td>[txt=Price]</td><td>[currency][effective_price]</td></tr><tr><td>[txt=Version]</td><td>[version]</td></tr><tr><td>[txt=Total Files]</td><td>[file_count]</td></tr><tr><td>[txt=File Size]</td><td>[file_size]</td></tr><tr><td>[txt=Create Date]</td><td>[create_date]</td></tr><tr><td>[txt=Last Updated]</td><td>[update_date]</td><tr><td colspan="2" style="text-align: right;border-bottom: 0"><a class="wpdmdlbtn" href="[page_url]" target="_parent">[txt=Buy Now]</a></td></tr></tbody></table><br/><style> .wpdmdlbtn {-moz-box-shadow:inset 0px 1px 0px 0px #9acc85;-webkit-box-shadow:inset 0px 1px 0px 0px #9acc85;box-shadow:inset 0px 1px 0px 0px #9acc85;background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #74ad5a), color-stop(1, #68a54b));background:-moz-linear-gradient(top, #74ad5a 5%, #68a54b 100%);background:-webkit-linear-gradient(top, #74ad5a 5%, #68a54b 100%);background:-o-linear-gradient(top, #74ad5a 5%, #68a54b 100%);background:-ms-linear-gradient(top, #74ad5a 5%, #68a54b 100%);background:linear-gradient(to bottom, #74ad5a 5%, #68a54b 100%);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#74ad5a\', endColorstr=\'#68a54b\',GradientType=0);background-color:#74ad5a;-moz-border-radius:3px;-webkit-border-radius:3px;border-radius:3px;border:1px solid #3b6e22;display:inline-block;cursor:pointer;color:#ffffff !important; font-size:12px;font-weight:bold;padding:10px 20px;text-transform: uppercase;text-decoration:none !important;}.wpdmdlbtn:hover {background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #68a54b), color-stop(1, #74ad5a));background:-moz-linear-gradient(top, #68a54b 5%, #74ad5a 100%);background:-webkit-linear-gradient(top, #68a54b 5%, #74ad5a 100%);background:-o-linear-gradient(top, #68a54b 5%, #74ad5a 100%);background:-ms-linear-gradient(top, #68a54b 5%, #74ad5a 100%);background:linear-gradient(to bottom, #68a54b 5%, #74ad5a 100%);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#68a54b\', endColorstr=\'#74ad5a\',GradientType=0);background-color:#68a54b;}.wpdmdlbtn:active {position:relative;top:1px;} .table{width:100%;border: 1px solid #eeeeee;} .table td{ padding:10px;border-bottom:1px solid #eee;}</style>';
        else
            $template = '<table class="table table-bordered"><tbody><tr><td colspan="2">[excerpt_200]</td></tr><tr><td>[txt=Version]</td><td>[version]</td></tr><tr><td>[txt=Total Files]</td><td>[file_count]</td></tr><tr><td>[txt=File Size]</td><td>[file_size]</td></tr><tr><td>[txt=Create Date]</td><td>[create_date]</td></tr><tr><td>[txt=Last Updated]</td><td>[update_date]</td><tr><td colspan="2" style="text-align: right;border-bottom: 0"><a class="wpdmdlbtn" href="[page_url]" target="_parent">[txt=Download]</a></td></tr></tbody></table><br/><style> .wpdmdlbtn {-moz-box-shadow:inset 0px 1px 0px 0px #9acc85;-webkit-box-shadow:inset 0px 1px 0px 0px #9acc85;box-shadow:inset 0px 1px 0px 0px #9acc85;background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #74ad5a), color-stop(1, #68a54b));background:-moz-linear-gradient(top, #74ad5a 5%, #68a54b 100%);background:-webkit-linear-gradient(top, #74ad5a 5%, #68a54b 100%);background:-o-linear-gradient(top, #74ad5a 5%, #68a54b 100%);background:-ms-linear-gradient(top, #74ad5a 5%, #68a54b 100%);background:linear-gradient(to bottom, #74ad5a 5%, #68a54b 100%);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#74ad5a\', endColorstr=\'#68a54b\',GradientType=0);background-color:#74ad5a;-moz-border-radius:3px;-webkit-border-radius:3px;border-radius:3px;border:1px solid #3b6e22;display:inline-block;cursor:pointer;color:#ffffff !important; font-size:12px;font-weight:bold;padding:10px 20px;text-transform: uppercase;text-decoration:none !important;}.wpdmdlbtn:hover {background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #68a54b), color-stop(1, #74ad5a));background:-moz-linear-gradient(top, #68a54b 5%, #74ad5a 100%);background:-webkit-linear-gradient(top, #68a54b 5%, #74ad5a 100%);background:-o-linear-gradient(top, #68a54b 5%, #74ad5a 100%);background:-ms-linear-gradient(top, #68a54b 5%, #74ad5a 100%);background:linear-gradient(to bottom, #68a54b 5%, #74ad5a 100%);filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#68a54b\', endColorstr=\'#74ad5a\',GradientType=0);background-color:#68a54b;}.wpdmdlbtn:active {position:relative;top:1px;} .table{width:100%; border: 1px solid #eeeeee; } .table td{ padding:10px;border-bottom:1px solid #eee;}</style>';
        return \WPDM\Package::fetchTemplate($template, get_the_ID());
    }

    function showLockOptions()
    {
        if (!isset($_REQUEST['id'])) die('ID Missing!');
        echo \WPDM\Package::downloadLink((int)$_REQUEST['id'], 1);
        die();
    }

    function addToFav()
    {
        if (!is_user_logged_in()) die('error');
        $pid = (int)$_REQUEST['pid'];
        $ufavs = maybe_unserialize(get_post_meta($pid, '__wpdm_favs', true));
        $myfavs = maybe_unserialize(get_user_meta(get_current_user_id(), '__wpdm_favs', true));
        if (!is_array($myfavs)) $myfavs = array();
        if (!is_array($ufavs)) $ufavs = array();

        if (is_array($myfavs) && ($key = array_search($_REQUEST['pid'], $myfavs)) !== false) {
            unset($myfavs[$key]);
            if (isset($ufavs[get_current_user_id()])) unset($ufavs[get_current_user_id()]);
        } else {
            $myfavs[] = $_REQUEST['pid'];
            $ufavs[get_current_user_id()] = time();
        }
        update_user_meta(get_current_user_id(), '__wpdm_favs', $myfavs);
        update_post_meta($pid, '__wpdm_favs', $ufavs);
        die('ok');
    }


    function loginURL($login_url, $redirect, $force_reauth)
    {
        $id = get_option('__wpdm_login_url', 0);
        if ($id > 0) {
            $page = get_post($id);
            if ($page->post_status == 'publish') {
                $url = get_page_link($page);
                //$url = $page->guid;
                if ($redirect != '')
                    $url = add_query_arg(array('redirect_to' => $redirect), $url);
            } else $url = $login_url;
        } else $url = $login_url;
        return $url;
    }

    function logoutURL($logout_url, $redirect)
    {
        $logout_url = wpdm_logout_url($redirect);
        return $logout_url;
    }

    function loginURLRedirect()
    {
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'GET' && strstr($_SERVER['REQUEST_URI'], 'wp-login.php') && !wpdm_query_var('skipwpdm')) {
            $id = get_option('__wpdm_login_url', 0);
            if ($id > 0) {
                $page = get_post($id);
                if ($page->post_status == 'publish') {
                    if (is_user_logged_in()) {
                        wp_redirect(wpdm_user_dashboard_url());
                    } else {
                        wp_redirect(add_query_arg($_GET, wpdm_login_url()));
                    }
                    die();
                }
            }
        }
    }

    function resetPassword()
    {
        if (wpdm_query_var('__reset_pass')) {

            if (empty($_POST['user_login'])) {
                die('error');
            } elseif (strpos($_POST['user_login'], '@')) {
                $user_data = get_user_by('email', trim(wp_unslash($_POST['user_login'])));
                if (empty($user_data))
                    die('error');
            } else {
                $login = trim($_POST['user_login']);
                $user_data = get_user_by('login', $login);
            }
            if (Session::get('__reset_time') && time() - Session::get('__reset_time') < 60) {
                echo "toosoon";
                exit;
            }
            if (!is_object($user_data) || !isset($user_data->user_login)) die('error');
            $user_login = Crypt::encrypt($user_data->user_login);
            $user_email = $user_data->user_email;
            $key = get_password_reset_key($user_data);


            $reseturl = add_query_arg(array('action' => 'rp', 'key' => $key, 'login' => $user_login), wpdm_login_url());

            $params = array('reset_password' => $reseturl, 'to_email' => $user_email);

            \WPDM\Email::send('password-reset', $params);
            Session::set('__reset_time', time());
            echo 'ok';
            exit;

        }
    }

    function updatePassword()
    {
        if (wpdm_query_var('__update_pass')) {

            if (wp_verify_nonce(wpdm_query_var('__update_pass'), NONCE_KEY)) {
                $pass = wpdm_query_var('password','html');
                if ($pass == '') die('error');
                $user = Crypt::decrypt(wpdm_query_var('__up_user'));
                if (is_object($user) && isset($user->ID)) {

                    if(user_can($user->ID, 'manage_options'))
                        wp_send_json(array('success' => false, 'message' => apply_filters('wpdm_update_password_error', __('Password update is disabled for this user!', 'download-manager'))));

                    wp_set_current_user($user->ID, $user->user_login);
                    wp_set_auth_cookie($user->ID);
                    //do_action('wp_login', $user->user_login);
                    wp_set_password($pass, $user->ID);
                    //print_r($user);
                    wp_send_json(array('success' => true, 'message' => ''));
                } else wp_send_json(array('success' => false, 'message' => apply_filters('wpdm_update_password_error', __('Session Expired! Please try again.', 'download-manager'))));
            } else
                wp_send_json(array('success' => false, 'message' => apply_filters('wpdm_update_password_error', __('Session Expired! Please try again.', 'download-manager'))));

        }
    }

    function interimLogin($template)
    {
        if (isset($_REQUEST['interim-login']) && !isset($_POST['wpdm_login'])) {
            $template = WPDM_BASE_DIR . 'tpls/clean.php';
        }
        return $template;
    }


    function verifyEmail($errors, $sanitized_user_login, $user_email)
    {
        if (!$errors) $errors = new \WP_Error();
        if (!wpdm_verify_email($user_email)) {
            $emsg = get_option('__wpdm_blocked_domain_msg');
            if (trim($emsg) === '') $emsg = __('Your email address is blocked!', 'download-manager');
            $errors->add('blocked_email', $emsg);
        }
        return $errors;
    }

    function verifyLoginEmail($user, $user_login, $user_pass)
    {

        $user_email = null;
        if(!is_email($user_login) && !$user) {
            $_user = get_user_by('user_login', $user_login);
            if($_user)
                $user_email = $_user->user_email;
        } else if(is_email($user_login))
            $user_email = $user_login;
        else if($user && isset($user->user_email))
            $user_email = $user->user_email;

        if (is_email($user_email) && !wpdm_verify_email($user_email)) {
            $user = new \WP_Error();
            $emsg = get_option('__wpdm_blocked_domain_msg');
            if (trim($emsg) === '') $emsg = __('Your email address is blocked!', 'download-manager');
            $user->add('blocked_email', $emsg);
        }
        return $user;
    }

    function generateTDL()
    {
        if (is_user_logged_in() && AuthorDashboard::hasAccess(get_current_user_id()) && wp_verify_nonce($_REQUEST['__tdlnonce'], NONCE_KEY)) {
            $pid = (int)$_REQUEST['pid'];
            $pack = get_post($pid);
            if (current_user_can(WPDM_ADMIN_CAP) || $pack->post_author == get_current_user_id()) {
                Session::set('__wpdm_unlocked_' . $pid, 1);
                $key = uniqid();
                $expire = time() + ((int)$_REQUEST['exmisd'] * (int)$_REQUEST['expire_multiply']);
                update_post_meta($pid, "__wpdmkey_" . $key, array('use' => (int)$_REQUEST['ulimit'], 'expire' => $expire));
                $download_page_key = Crypt::encrypt(array('pid' => $pid, 'key' => $key));
                $download_page = home_url("wpdm-download/{$download_page_key}");
                $download_url = wpdm_download_url($pid, array("_wpdmkey" => $key));
                wp_send_json(array('download_url' => $download_url, 'download_page' => $download_page));
                die();
            }
        }
        wp_send_json(array('download_url' => 'Error! Unauthorized Access', 'download_page' => 'Error! Unauthorized Access'));

    }

    function wpdmIframe()
    {
        if (isset($_REQUEST['__wpdmlo'])) {
            include wpdm_tpl_path("lock-options-iframe.php");
            die();
        }
    }

    function shortcodeIframe()
    {
        if (isset($_REQUEST['__wpdmxp'])) {
            include wpdm_tpl_path("shortcode-iframe.php");
            die();
        }
    }

    function customDownloadLinkPage()
    {
        global $wp_query;
        $url = parse_url($_SERVER['REQUEST_URI']);
        if (preg_match('/wpdm\-download\/([^\/]+)/', $url['path'], $matches)) {
            $pack = Crypt::decrypt($matches[1], true);
            $package = get_post($pack['pid']);

            $validity = TempStorage::get("__wpdmkey_{$pack['key']}");
            if (!$validity)
                $validity = get_post_meta($pack['pid'], "__wpdmkey_{$pack['key']}", true);

            if (!is_array($validity)) $validity = array('expire' => 0, 'use' => 0);

            $validity['expire'] = $validity['expire'] - time();

            $mtime = '';
            if ($validity['expire'] > 0 && $validity['use'] > 0) {
                $init = $validity['expire'];
                $days = round($init / 86400);
                $hours = round($init / 3600);
                $minutes = round(($init / 60) % 60);
                $seconds = $init % 60;
                if ($days > 0)
                    $mtime .= "<b>{$days}</b> days ";
                else if ($hours > 0)
                    $mtime .= "<b>{$hours}</b> hours ";
                else if ($minutes > 0)
                    $mtime .= "<b>{$minutes}</b> mins ";
                else if ($seconds > 0)
                    $mtime .= "<b>{$seconds}</b> secs ";
                $keyvalid = true;
            } else {
                $keyvalid = false;
                $init = abs($validity['expire']);
                $days = round($init / 86400);
                $hours = round($init / 3600);
                $minutes = round(($init / 60) % 60);
                $_mtime = '';
                $seconds = $init % 60;
                if ($days > 0)
                    $_mtime .= "<b>{$days}</b> days ";
                else if ($hours > 0)
                    $_mtime .= "<b>{$hours}</b> hours ";
                else if ($minutes > 0)
                    $_mtime .= "<b>{$minutes}</b> mins ";
                else if ($seconds > 0)
                    $_mtime .= "{$seconds} secs";
                $validity['expired'] = $_mtime;
            }

            $files = Package::getFiles($package->ID, true);
            $picon = get_post_meta($package->ID, '__wpdm_icon', true);
            $validity['expire'] = $mtime;
            $download_url = add_query_arg(array('wpdmdl' => $pack['pid'], '_wpdmkey' => $pack['key']), home_url());
            include wpdm_tpl_path("download-page-clean.php");
            die();
        }
    }

    function validateLoginPage($content)
    {
        if (is_singular('page')) {
            $id = get_option('__wpdm_login_url', 0);
            if ($id > 0 && $id == get_the_ID()) {
                if (!has_shortcode($content, 'wpdm_login_form') && !has_shortcode($content, 'wpdm_user_dashboard') && !has_shortcode($content, 'wpdm_author_dashboard')) {
                    $content = WPDM()->shortCode->loginForm();
                }
            }
        }
        return $content;

    }

    function removeCommentFeed($feed)
    {
        if (get_post_type() == 'wpdmpro' && get_option('__wpdm_has_archive', false) == false)
            $feed = false;
        return $feed;
    }

    function verifyEmailLock(){
        global $wpdb;

        if (!isset($_POST['__wpdm_ID'])) return;
        $id = (int)$_POST['__wpdm_ID'];
        $key = uniqid();
        $file = get_post($id, ARRAY_A);
        $file = wpdm_setup_package_data($file);
        $file1 = $file;
        $data = array('error' => '', 'downloadurl' => '');

        if (wpdm_verify_email(wpdm_query_var('email'))) {

            $subject = "Your Download Link";
            $site = get_option('blogname');

            $limit = get_option('__wpdm_private_link_usage_limit',3);
            $xpire_period = ((int)get_option('__wpdm_private_link_expiration_period', 3)) * ((int)get_option('__wpdm_private_link_expiration_period_unit', 60));
            $xpire_period = $xpire_period > 0 ? $xpire_period :  3600;

            //When there are custom form data with the email lock form, ex: Name, Company Name, Position, etc.
            $custom_form_data = isset($_POST['custom_form_field']) ? $_POST['custom_form_field'] : array();
            if(wpdm_query_var('name') !== '') $custom_form_data = ['name' => wpdm_query_var('name')] + $custom_form_data;

            //do something before sending download link
            do_action("wpdm_before_email_download_link", $_POST, $file);

            $show_download_link_instantly = isset($file['email_lock_idl']) ? (int)$file['email_lock_idl'] : 3;
            $show_download_link_instantly_also_mail = isset($file['email_lock_idl_email']) ? (int)$file['email_lock_idl_email'] : 0;

            $request_status =  $show_download_link_instantly === 0 ? 3 : $show_download_link_instantly;
            $wpdb->insert("{$wpdb->prefix}ahm_emails", array('email' => $_POST['email'], 'pid' => $file['ID'], 'date' => time(), 'custom_data' => serialize($custom_form_data), 'request_status' => $request_status));
            $subscriberID = $wpdb->insert_id;

            $download_url = add_query_arg(['subscriber' =>\WPDM\libs\Crypt::encrypt($subscriberID)], \WPDM\Package::expirableDownloadLink($id, $limit, $xpire_period));
            $download_page_url  = add_query_arg(['subscriber' =>\WPDM\libs\Crypt::encrypt($subscriberID)], \WPDM\Package::expirableDownloadPage($id, $limit, $xpire_period));

            if($show_download_link_instantly === 0 || ($show_download_link_instantly == 1 && $show_download_link_instantly_also_mail == 0)) {
                $name = isset($cff['name'])?$cff['name']:'';
                $email_params = array('to_email' => $_POST['email'], 'name' => $name, 'download_count' => $limit,  'package_name' => $file['post_title'], 'package_url' => get_permalink($id), 'download_url' => $download_url, 'download_page_url' => $download_page_url);
                $email_params = apply_filters("wpdm_email_lock_mail_params", $email_params, $file);
                \WPDM\Email::send("email-lock", $email_params);

            }
            $elmsg = sanitize_textarea_field(get_post_meta($id, '__wpdm_email_lock_msg', true));
            if ($show_download_link_instantly === 0) {
                $data['downloadurl'] = "";
                $data['msg'] = ($elmsg !='' ? $elmsg : __( "Download link sent to your email!" , "download-manager" ));
                $data['type'] = 'success';
            } else if ($show_download_link_instantly === 2) {
                $data['downloadurl'] = "";
                $data['msg'] = ($elmsg !='' ? $elmsg : __( "Admin will review your request soon!" , "download-manager" ));
                $data['type'] = 'success';
            } else {
                $data['downloadurl'] = $download_url;
                if($show_download_link_instantly_also_mail == 0)
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


    function shopLogo1($url, $id, $args = array())
    {
        if (is_object($id)) $id = $id->user_id;
        if (is_email($id)) {
            $user = get_user_by('email', $id);
            $id = $user->ID;
        }

        $store = get_user_meta($id, '__wpdm_public_profile', true);
        return isset($store['logo']) && $store['logo'] != '' ? $store['logo'] : $url;
    }

    function shopLogo($args, $id)
    {
        if (is_object($id)) $id = $id->user_id;
        if (is_email($id)) {
            $user = get_user_by('email', $id);
            $id = $user->ID;
        }

        $store = get_user_meta($id, '__wpdm_public_profile', true);
        if (isset($store['logo']) && $store['logo'] != '')
            $args['url'] = $store['logo'];
        return $args;
    }


    function wpHead(){

        self::googleFont();

    }

    static function googleFont()
    {
        $wpdmss = maybe_unserialize(get_option('__wpdm_disable_scripts', array()));
        $uicolors = maybe_unserialize(get_option('__wpdm_ui_colors', array()));
        //$ltemplates = maybe_unserialize(get_option("_fm_link_templates", true));
        //$ptemplates = maybe_unserialize(get_option("_fm_page_templates", true));
        $font = get_option('__wpdm_google_font', 'Rubik');
        $font = $font ? $font . ',' : '';

        $css = WPDM()->packageTemplate->getStyles('link');

        ?>
        <?php if (get_option('__wpdm_google_font') !== '') { ?>
        <link href="https://fonts.googleapis.com/css?family=<?php echo get_option('__wpdm_google_font', 'Rubik'); ?>"
              rel="stylesheet">
        <style>
            .w3eden .fetfont,
            .w3eden .btn,
            .w3eden .btn.wpdm-front h3.title,
            .w3eden .wpdm-social-lock-box .IN-widget a span:last-child,
            .w3eden #xfilelist .panel-heading,
            .w3eden .wpdm-frontend-tabs a,
            .w3eden .alert:before,
            .w3eden .panel .panel-heading,
            .w3eden .discount-msg,
            .w3eden .panel.dashboard-panel h3,
            .w3eden #wpdm-dashboard-sidebar .list-group-item,
            .w3eden #package-description .wp-switch-editor,
            .w3eden .w3eden.author-dashbboard .nav.nav-tabs li a,
            .w3eden .wpdm_cart thead th,
            .w3eden #csp .list-group-item,
            .w3eden .modal-title {
                font-family: <?php echo $font; ?> -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
                text-transform: uppercase;
                font-weight: 700;
            }
            .w3eden #csp .list-group-item {
                text-transform: unset;
            }


        </style>
    <?php } ?>
        <style>
        <?php
        echo '/* WPDM Link Template Styles */';
        echo $css;
        ?>
        </style>
        <?php
        self::uiColors();

    }

    static function uiColors($override_option = true)
    {

        $wpdmss = maybe_unserialize(get_option('__wpdm_disable_scripts', array()));
        if (is_array($wpdmss) && in_array('wpdm-front', $wpdmss) && !is_admin()) return;

        $uicolors = maybe_unserialize(get_option('__wpdm_ui_colors', array()));
        $primary = isset($uicolors['primary']) ? $uicolors['primary'] : '#4a8eff';
        $secondary = isset($uicolors['secondary']) ? $uicolors['secondary'] : '#4a8eff';
        $success = isset($uicolors['success']) ? $uicolors['success'] : '#18ce0f';
        $info = isset($uicolors['info']) ? $uicolors['info'] : '#2CA8FF';
        $warning = isset($uicolors['warning']) ? $uicolors['warning'] : '#f29e0f';
        $danger = isset($uicolors['danger']) ? $uicolors['danger'] : '#ff5062';
        $font = get_option('__wpdm_google_font', 'Rubik');
        $font = $font ? "\"{$font}\"," : '';
        if (is_singular('wpdmpro'))
            $ui_button = get_option('__wpdm_ui_download_button');
        else
            $ui_button = get_option('__wpdm_ui_download_button_sc');
        $class = ".btn." . (isset($ui_button['color']) ? $ui_button['color'] : 'btn-primary') . (isset($ui_button['size']) && $ui_button['size'] != '' ? "." . $ui_button['size'] : '');

        ?>
        <style>

            :root {
                --color-primary: <?php echo $primary; ?>;
                --color-primary-rgb: <?php echo wpdm_hex2rgb($primary); ?>;
                --color-primary-hover: <?php echo isset($uicolors['primary'])?$uicolors['primary_hover']:'#4a8eff'; ?>;
                --color-primary-active: <?php echo isset($uicolors['primary'])?$uicolors['primary_active']:'#4a8eff'; ?>;
                --color-secondary: <?php echo $secondary; ?>;
                --color-secondary-rgb: <?php echo wpdm_hex2rgb($secondary); ?>;
                --color-secondary-hover: <?php echo isset($uicolors['secondary'])?$uicolors['secondary_hover']:'#4a8eff'; ?>;
                --color-secondary-active: <?php echo isset($uicolors['secondary'])?$uicolors['secondary_active']:'#4a8eff'; ?>;
                --color-success: <?php echo $success; ?>;
                --color-success-rgb: <?php echo wpdm_hex2rgb($success); ?>;
                --color-success-hover: <?php echo isset($uicolors['success_hover'])?$uicolors['success_hover']:'#4a8eff'; ?>;
                --color-success-active: <?php echo isset($uicolors['success_active'])?$uicolors['success_active']:'#4a8eff'; ?>;
                --color-info: <?php echo $info; ?>;
                --color-info-rgb: <?php echo wpdm_hex2rgb($info); ?>;
                --color-info-hover: <?php echo isset($uicolors['info_hover'])?$uicolors['info_hover']:'#2CA8FF'; ?>;
                --color-info-active: <?php echo isset($uicolors['info_active'])?$uicolors['info_active']:'#2CA8FF'; ?>;
                --color-warning: <?php echo $warning; ?>;
                --color-warning-rgb: <?php echo wpdm_hex2rgb($warning); ?>;
                --color-warning-hover: <?php echo isset($uicolors['warning_hover'])?$uicolors['warning_hover']:'orange'; ?>;
                --color-warning-active: <?php echo isset($uicolors['warning_active'])?$uicolors['warning_active']:'orange'; ?>;
                --color-danger: <?php echo $danger; ?>;
                --color-danger-rgb: <?php echo wpdm_hex2rgb($danger); ?>;
                --color-danger-hover: <?php echo isset($uicolors['danger_hover'])?$uicolors['danger_hover']:'#ff5062'; ?>;
                --color-danger-active: <?php echo isset($uicolors['danger_active'])?$uicolors['danger_active']:'#ff5062'; ?>;
                --color-green: <?php echo isset($uicolors['green'])?$uicolors['green']:'#30b570'; ?>;
                --color-blue: <?php echo isset($uicolors['blue'])?$uicolors['blue']:'#0073ff'; ?>;
                --color-purple: <?php echo isset($uicolors['purple'])?$uicolors['purple']:'#8557D3'; ?>;
                --color-red: <?php echo isset($uicolors['red'])?$uicolors['red']:'#ff5062'; ?>;
                --color-muted: rgba(69, 89, 122, 0.6);
                --wpdm-font: <?php echo $font; ?> -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
            }

            .wpdm-download-link<?php echo $class; ?> {
                border-radius: <?php echo (isset($ui_button['borderradius'])?$ui_button['borderradius']:4); ?>px;
            }


        </style>
        <?php

    }


}
