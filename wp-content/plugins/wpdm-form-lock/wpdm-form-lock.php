<?php

/*
  Plugin Name: WPDM - Form Lock
  Plugin URI: https://www.wpdownloadmanager.com/download/wpdm-form-lock/
  Description: Form Lock Add-on for WordPress Download Manager Pro, Supports Live Forms, Gravity Forms, WPForms, Formidable Forms & Contact Form 7.
  Author: WordPress Download Manager
  Version: 1.7.7
  Author URI: https://www.wpdownloadmanager.com/
 */


class WPDM_FormLock {

    public $package_id = 0;
    public $show_form_everytime = 1;

    function __construct(){

        $this->show_form_everytime = (int)get_option('__wpdm_show_form_lock', 1);

        add_action('wpdm_download_lock_option', array($this,'lock_settings'));
        add_action('wpdm_download_lock', array($this,'download_lock'), 10, 2);
        add_action('wpdm_check_lock', array($this,'check_download_lock'), 10, 2);

        //Live Forms
        add_filter('liveform_submitform_thankyou_message', array($this,'show_download_button'));
        add_action( 'liveforms_form_settings', array($this, 'package_select'));
        add_filter( 'wpdm_liveforms_html', array($this, 'liveform_html'), 10, 3);
        add_action( 'wpdm_filecart_liveforms_html', array($this, 'liveform_filecart_html'), 10, 2);
        add_filter('liveform_submitform_thankyou_message', array($this,'show_download_button_fc'));


        //Gravity Forms
        add_filter( 'wpdm_gravityforms_html', array($this, 'gravityforms_html'), 10, 3);
        add_filter( 'gform_confirmation', array($this, 'after_submit_gravityform'), 10, 4);
        add_filter( 'gform_pre_render', array($this, 'gform_pre_render'));

        //Contact Form 7
        add_filter( 'wpdm_contactform7_html', array($this, 'contactform7_html'), 10, 3);
        add_action( 'wpdm_filecart_contactform7_html', array($this, 'contactform7_filecart_html'), 10, 2);
        //add_filter( 'wpcf7_display_message', array($this,'show_download_button'));
        add_filter( 'wpcf7_ajax_json_echo', array($this,'show_download_button_cf7'), 999999, 2);
        add_filter( 'wpcf7_ajax_json_echo', array($this,'show_download_button_fc_cf7'), 999999, 2);
        add_filter("wpcf7_form_response_output", [$this, 'wpcf7_form_response_output'], 10, 5);

        //Ninja Forms -- not supported anymore
        /*
        add_filter( 'wpdm_form_lock_dropdown', array($this, 'ninja_forms_dropdown'));
        add_filter( 'wpdm_ninjaforms_html', array($this, 'ninjaforms_html'), 10, 3);
        add_filter( 'nf_success_msg', array($this, 'show_download_button'), 10, 2 );
        */

        //Formidable Forms
        add_filter( 'wpdm_form_lock_dropdown', array($this, 'formidable_dropdown'));
        add_filter( 'wpdm_file_cart_form_dropdown', array($this, 'formidable_filecart_dropdown'));
        add_filter( 'wpdm_formidable_html', array($this, 'formidable_html'), 10, 3);
        add_action( 'wpdm_filecart_formidable_html', array($this, 'formidable_filecart_html'), 10, 2);
        add_filter( 'frm_main_feedback', array($this, 'show_download_button'), 10, 3);
        add_filter( 'frm_main_feedback', array($this, 'show_download_button_fc'), 10, 3);

        //WPForm Forms
        add_filter( 'wpdm_form_lock_dropdown', array($this, 'wpforms_dropdown'));
        add_filter( 'wpdm_file_cart_form_dropdown', array($this, 'wpforms_filecart_dropdown'));
        add_filter( 'wpdm_wpforms_html', array($this, 'wpforms_html'), 10, 3);
        add_action( 'wpdm_filecart_wpforms_html', array($this, 'wpforms_filecart_html'), 10, 2);
        add_filter( 'wpforms_frontend_output_success', array($this, 'show_download_button_wpforms'), 99999);
        add_filter( 'wpforms_frontend_output_success', array($this, 'show_download_button_fc_wpforms'), 99999);
        add_filter( 'wpforms_ajax_submit_success_response', array($this, 'push_download_button_wpforms'), 99999, 3);


        //Settings
        add_action('basic_settings_section', array($this, 'global_settings'));


    }

    function global_settings(){
        include dirname(__FILE__).'/tpls/global-settings.php';
    }

    function download_lock($extralocks, $file)
    {


        if (get_post_meta($file['ID'], '__wpdm_form_lock', true) != 1 || get_post_meta($file['ID'], '__wpdm_form_id', true) == '') return $extralocks;
        $form_info = get_post_meta($file['ID'], '__wpdm_form_id', true);
        $form_info = explode("|", $form_info);
        $formplugin = $form_info[0];
        $formid = isset($form_info[1])?$form_info[1]:0;
        $formhtml = apply_filters("wpdm_".$formplugin."_html", "", $formid, $file['ID']);
        if ( ! is_array( $extralocks ) ) $extralocks = array();
        if(!isset($extralocks['html'])) $extralocks['html'] = '';
        $extralocks['html'] .= $formhtml;
        $extralocks['lock'] = 'locked';
        $this->package_id = $file['ID'];
        return $extralocks;
    }

    function liveform_html($formhtml, $formid, $pid){
        if(!class_exists('liveforms')) return 'LiveForms plugin is not active!';
        if(!\WPDM\Session::get( 'wpdm_form_lock_'.$formid ) || $this->show_form_everytime === 1) {
            $liveform = LiveForms::getInstance();
            $title = get_the_title($formid);
            $formhtml = "<div class='panel panel-default card'><div class='panel-heading card-header'>{$title}</div><div class='panel-body card-body'>" . $liveform->view_showform(array('form_id' => $formid)) . "</div></div>";
            $formhtml = str_replace("</form>", "<input type='hidden' name='after_submit_wpdm' value='{$pid}' /></form>", $formhtml);
        } else {
            $download_url = \WPDM\Package::expirableDownloadLink($pid);
            $link_label = get_post_meta($pid, '__wpdm_link_label', true);
            $link_label = trim($link_label) != ''?$link_label:__('Download', 'wpdmpro');
            $formhtml = "<div class='alert alert-success' data-title='DONE'><div style='margin-bottom: 5px;text-align: center'>Your download link is ready now:</div><a href='{$download_url}' class='btn btn-lg btn-success btn-block'>{$link_label}</a></div>";
        }
        return $formhtml;
    }

    function liveform_filecart_html($formid){
        if(!class_exists('liveforms')) { echo 'LiveForms plugin is not active!'; return; }
        $liveform = LiveForms::getInstance();
        $title = get_the_title($formid);
        $formhtml = "<div class='panel panel-default card'><div class='panel-heading card-header'>{$title}</div><div class='panel-body card-body'>".$liveform->view_showform(array('form_id' => $formid))."</div></div>";
        $formhtml = str_replace("</form>", "<input type='hidden' name='wpdm_download_filecart' value='1' /></form>", $formhtml);
        echo $formhtml;
    }

    function package_select($form_data){
        if(!defined('WPDM_Version') || version_compare(WPDM_Version, '4.0.0', '>')) return;
        ?>
        <div class="form-group">

            <label form="download">Download After Submit: </label>
            <select class="form-control" name="contact[download]" id="download">
                <?php
                global $post;
                $args = array( 'numberposts' => -1,'post_type' => 'wpdmpro');
                $posts = get_posts($args);
                foreach( $posts as $post ) : setup_postdata($post); ?>
                    <option value="<? echo $post->ID; ?>" <?php isset($form_data['download']) && selected($form_data['download'], $post->ID); ?> ><?php the_title(); ?></option>
                <?php endforeach; ?>
            </select>

        </div>
        <?php
    }

    function gravityforms_html($formhtml, $formid, $pid){
        if(!\WPDM\Session::get( 'wpdm_form_lock_'.$formid ) || $this->show_form_everytime === 1){
            $form = GFAPI::get_form($formid);
            $formhtml = "<div class='panel panel-default card'><div class='panel-heading card-header'>{$form['title']}</div><div class='panel-body card-body'>".do_shortcode('[gravityform id="'.$formid.'" title="false" description="true"]')."</div></div>";
            $formhtml = str_replace("[wpdm_package_id]", $pid, $formhtml);
        } else {
            $download_url = \WPDM\Package::expirableDownloadLink($pid);
            $link_label = get_post_meta($pid, '__wpdm_link_label', true);
            $link_label = trim($link_label) != ''?$link_label:__('Download', 'wpdmpro');
            $formhtml = "<div class='alert alert-success' data-title='DONE'><div style='margin-bottom: 5px;text-align: center'>Your download link is ready now:</div><a href='{$download_url}' class='btn btn-lg btn-success btn-block'>{$link_label}</a></div>";
        }
        return $formhtml;
    }

    function contactform7_html($formhtml, $formid, $pid){
        $title = get_the_title($formid);
        if(!\WPDM\Session::get( 'wpdm_form_lock_'.$formid ) || $this->show_form_everytime === 1){
            $formhtml = "<div class='panel panel-default card'><div class='panel-heading card-header'>{$title}</div><div class='panel-body card-body'>" . do_shortcode('[contact-form-7 id="' . $formid . '"]') . "</div></div>";
            $formhtml = str_replace("</form>", "<input type='hidden' name='after_submit_wpdm' value='{$pid}' /></form>", $formhtml);
        } else {
            $download_url = \WPDM\Package::expirableDownloadLink($pid);
            $link_label = get_post_meta($pid, '__wpdm_link_label', true);
            $link_label = trim($link_label) != ''?$link_label:__('Download', 'wpdmpro');
            $formhtml = "<div class='alert alert-success' data-title='DONE'><div style='margin-bottom: 5px;text-align: center'>Your download link is ready now:</div><a href='{$download_url}' class='btn btn-lg btn-success btn-block'>{$link_label}</a></div>";
        }
        return $formhtml;
    }

    function contactform7_filecart_html($formid){
        $title = get_the_title($formid);
        $formhtml = "<div class='panel panel-default card'><div class='panel-heading card-header'>{$title}</div><div class='panel-body card-body'>".do_shortcode('[contact-form-7 id="'.$formid.'"]')."</div></div>";
        $formhtml = str_replace("</form>", "<input type='hidden' name='wpdm_download_filecart' value='1' /></form>", $formhtml);
        echo $formhtml;
    }

    function ninjaforms_html($formhtml, $formid, $pid){
        $data = Ninja_Forms()->form( $formid )->get_all_settings();
        $formhtml = "<div class='panel panel-default card'><div class='panel-heading card-header'>{$data['form_title']}</div><div class='panel-body card-body'>".do_shortcode('[ninja_forms id="'.$formid.'"]')."</div></div>";
        $formhtml = str_replace("</form>", "<input type='hidden' name='after_submit_wpdm' value='{$pid}' /></form>", $formhtml);
        return $formhtml;
    }

    function formidable_html($formhtml, $formid, $pid){
        global $wpdb;
        $formid = (int)$formid;
        if(!\WPDM\Session::get( 'wpdm_form_lock_'.$formid ) || $this->show_form_everytime === 1) {
            $formname = $wpdb->get_var("select name from {$wpdb->prefix}frm_forms where id='{$formid}'");
            $formhtml = "<div class='panel panel-default card'><div class='panel-heading card-header'>{$formname}</div><div class='panel-body card-body'>" . do_shortcode('[formidable id="' . $formid . '"]') . "</div></div>";
            $formhtml = str_replace("</form>", "<input type='hidden' name='after_submit_wpdm' value='{$pid}' /></form>", $formhtml);
        } else {
            $download_url = \WPDM\Package::expirableDownloadLink($pid);
            $link_label = get_post_meta($pid, '__wpdm_link_label', true);
            $link_label = trim($link_label) != ''?$link_label:__('Download', 'wpdmpro');
            $formhtml = "<div class='alert alert-success' data-title='DONE'><div style='margin-bottom: 5px;text-align: center'>Your download link is ready now:</div><a href='{$download_url}' class='btn btn-lg btn-success btn-block'>{$link_label}</a></div>";
        }
        return $formhtml;
    }

    function formidable_filecart_html($formid){
        global $wpdb;
        $formid = (int)$formid;
        $formname = $wpdb->get_var("select name from {$wpdb->prefix}frm_forms where id='{$formid}'");
        $formhtml = "<div class='panel panel-default card'><div class='panel-heading card-header'>{$formname}</div><div class='panel-body card-body'>".do_shortcode('[formidable id="'.$formid.'"]')."</div></div>";
        $formhtml = str_replace("</form>", "<input type='hidden' name='wpdm_download_filecart' value='1' /></form>", $formhtml);
        echo $formhtml;
    }

    function wpforms_html($formhtml, $formid, $pid){
        global $wpdb;
        $formid = (int)$formid;
        if(!\WPDM\Session::get( 'wpdm_form_lock_'.$formid ) || $this->show_form_everytime === 1) {
            $form = get_post($formid);
            $formhtml = "<div class='panel panel-default card'><div class='panel-heading card-header'>{$form->post_title}</div><div class='panel-body card-body'>" . do_shortcode('[wpforms id="' . $formid . '"]') . "</div></div>";
            $formhtml = str_replace("</form>", "<input type='hidden' name='after_submit_wpdm' value='{$pid}' /></form>", $formhtml);
        } else {
            $download_url = \WPDM\Package::expirableDownloadLink($pid);
            $link_label = get_post_meta($pid, '__wpdm_link_label', true);
            $link_label = trim($link_label) != ''?$link_label:__('Download', 'wpdmpro');
            $formhtml = "<div class='alert alert-success' data-title='DONE'><div style='margin-bottom: 5px;text-align: center'>Your download link is ready now:</div><a href='{$download_url}' class='btn btn-lg btn-success btn-block'>{$link_label}</a></div>";
        }
        return $formhtml;
    }

    function wpforms_filecart_html($formid){
        global $wpdb;
        $formid = (int)$formid;
        $form = get_post($formid);
        $formhtml = "<div class='panel panel-default card'><div class='panel-heading card-header'>{$form->post_title}</div><div class='panel-body card-body'>".do_shortcode('[wpforms id="'.$formid.'"]')."</div></div>";
        $formhtml = str_replace("</form>", "<input type='hidden' name='wpdm_download_filecart' value='1' /></form>", $formhtml);
        echo $formhtml;
    }




    function check_download_lock($lock, $id)
    {
        if (get_post_meta($id, '__wpdm_form_lock', true) == '1') $lock = 'locked';
        return $lock;
    }

    function add_hidden_field($formid){
        global $post;
        $pid = 0;
        $form_data = get_post_meta($formid, 'form_data', true);
        $form_data = maybe_unserialize($form_data);
        if(isset($form_data['download'])) $this->package_id = $form_data['download'];
        if(is_singular('wpdmpro')) $this->package_id = get_the_ID();
        if($this->package_id > 0)
            echo "<input type='hidden' name='after_submit_wpdm' value='{$this->package_id}' />";
    }

    function email_link($email, $file, $post = array()){

        //do something before sending download link
        do_action("wpdm_before_email_download_link", $_POST, $file);
        $templates = \WPDM\Email::templates();


        if(isset($post['subject']))  { $post['_subject'] = $post['subject']; unset($post['subject']); }
        if(isset($post['to_email']))  { $post['_to_email'] = $post['to_email']; unset($post['to_email']); }
        if(isset($post['sitename']))  { $post['_sitename'] = $post['sitename']; unset($post['sitename']); }
        if(isset($post['message']))  { $post['_message'] = $post['message']; unset($post['message']); }
        $params = array('to_email' => $email[0], 'package_name' => $file['post_title'], 'download_url' => $file['download_url']);
        $params = $params + $post;
        if(isset($templates['email-lock']))
            \WPDM\Email::send("email-lock", $params);
        else {
            $sitename = get_bloginfo('name');
            $message = "Thanks for Subscribing to {$sitename}<br/>Please click on following link to start download:<br/><b><a style=\"display: block;text-align: center\" class=\"button\" href=\"{$file['download_url']}\">Download</a></b><br/><br/><br/>Best Regards,<br/>Support Team<br/><b>{$sitename}</b>";
            $params = array(
                'subject' => "[$sitename] Your Download is Ready",
                'to_email' => $email[0],
                'sitename' => $sitename,
                'message' => $message
            );

            $params = $params + $post;
            \WPDM\Email::send("default", $params);
        }
    }

    function show_download_button($message, $status = null, $extra = null){
        if(!isset($_POST['after_submit_wpdm'])) return $message;

        if(isset($_POST['after_submit_wpdm'])) {
            $file = array('ID' => $_POST['after_submit_wpdm']);
            $download_url = \WPDM\Package::expirableDownloadLink((int)$_POST['after_submit_wpdm'], 3);
            $link_label = get_post_meta($_POST['after_submit_wpdm'], '__wpdm_link_label', true);
            $link_label = $link_label ? $link_label : 'Download';
            $email_link = get_post_meta($_POST['after_submit_wpdm'],'__wpdm_form_lock_email_downlad_link', true);
            if(!$email_link)
                $message .= "<hr style='margin: 10px 0'/><a class='btn btn-success' href='{$download_url}'><i class='fa fa-downlaod'></i> " . $link_label . "</a>";
            else {
                if (method_exists('\WPDM\Package', 'expirableDownloadPage'))
                    $file['download_url'] = \WPDM\Package::expirableDownloadPage(wpdm_query_var('after_submit_wpdm', 'int'), 3, 604800);
                else
                    $file['download_url'] = \WPDM\Package::expirableDownloadLink(wpdm_query_var('after_submit_wpdm', 'int'), 3, 604800);
                $file['post_title'] = get_the_title($file['ID']);
                $this->findEmail($_POST, $emails);
                $this->email_link($emails, $file);
            }
            if(isset($_POST['wpforms'], $_POST['wpforms']['id']))
                \WPDM\Session::set('wpdm_form_lock_'.$_POST['wpforms']['id'], 'unlocked');
            do_action("wpdm_form_lock_submit_form", $_POST);
        }
        return "<div style='padding: 10px;'>".$message."</div>";
    }

    function show_download_button_fc($message, $status = null, $extra = null){
        if(!isset($_POST['wpdm_download_filecart'])) return $message;
        $key = uniqid();
        if(isset($_POST['wpdm_download_filecart'])) {

            \WPDM\Session::set('_wpdm_unlocked_filecart', 1);
            $download_link = '<button class="btn btn-primary" id="download-file-cart"><i class="fa fa-download"></i> &nbsp; Download</button>';
            $email_link = get_option('__wpdm_file_cart_email_downlad_link', 0);
            if(!$email_link)
                $message = "<hr style='margin: 0 0 10px 0'/>$download_link";
            else {
                $this->findEmail($_POST, $emails);
                WPDM_FileCart::EmailFileCart($emails, \WPDM\Session::get('file_cart_data'));
                $message = "<div class='w3eden'><div class='alert alert-success'>Download link sent to your email!</div></div>";
            }

            do_action("wpdm_form_lock_submit_form", $_POST);
        }
        return "<div style='padding: 10px;'>".$message."</div>";
    }

    function show_download_button_wpforms($form_data){
        $vars = array();
        foreach ($_POST['wpforms']['fields'] as $id => $val){
            $vars[$id] = is_array($val) ? implode(" ", $val) : $val;
        }
        if(!isset($_POST['after_submit_wpdm'])) return;
        if(isset($_POST['after_submit_wpdm'])) {
            $file = array('ID' => (int)$_POST['after_submit_wpdm']);
            $download_url = \WPDM\Package::expirableDownloadLink((int)$_POST['after_submit_wpdm'], 3);
            $link_label = get_post_meta($_POST['after_submit_wpdm'], '__wpdm_link_label', true);
            $link_label = $link_label ? $link_label : 'Download';
            $email_link = get_post_meta($_POST['after_submit_wpdm'],'__wpdm_form_lock_email_downlad_link', true);
            if(!$email_link)
                $message = "<hr style='margin: 0 0 10px 0'/><a class='btn btn-success btn-lg' href='{$download_url}'><i class='fa fa-downlaod'></i> " . $link_label . "</a>";
            else {
                if (method_exists('\WPDM\Package', 'expirableDownloadPage'))
                    $file['download_url'] = \WPDM\Package::expirableDownloadPage(wpdm_query_var('after_submit_wpdm', 'int'), 3, 604800);
                else
                    $file['download_url'] = \WPDM\Package::expirableDownloadLink(wpdm_query_var('after_submit_wpdm', 'int'), 3, 604800);
                $file['post_title'] = get_the_title($file['ID']);
                $this->findEmail($_POST, $emails);
                $this->email_link($emails, $file, $vars);
                $message = "<div class='w3eden'><div class='alert alert-success'>Download link sent to your email!</div></div>";
            }

            \WPDM\Session::set('wpdm_form_lock_'.$_POST['wpforms']['id'], 'unlocked');
            do_action("wpdm_form_lock_submit_form", $_POST);
        }
        echo "<div style='padding: 10px 0;'>".$message."</div>";
    }

    function show_download_button_fc_wpforms($form_data){
        if(!isset($_POST['wpdm_download_filecart'])) return;
        $key = uniqid();
        if(isset($_POST['wpdm_download_filecart'])) {

            \WPDM\Session::set('_wpdm_unlocked_filecart', 1);
            $download_link = '<button class="btn btn-primary" id="download-file-cart"><i class="fa fa-download"></i> &nbsp; Download</button>';
            $email_link = get_option('__wpdm_file_cart_email_downlad_link', 0);
            if(!$email_link)
                $message = "<hr style='margin: 0 0 10px 0'/>$download_link";
            else {
                $this->findEmail($_POST, $emails);
                WPDM_FileCart::EmailFileCart($emails, \WPDM\Session::get('file_cart_data'));
                $message = "<div class='w3eden'><div class='alert alert-success'>Download link sent to your email!</div></div>";
            }

            do_action("wpdm_form_lock_submit_form", $_POST);
        }
        echo "<div style='padding: 10px 0;'>".$message."</div>";
    }

    /**
     * When WPForm ajax submission is enabled
     * @param $confirm
     * @param $formid
     * @param $formdata
     * @return mixed
     */
    function push_download_button_wpforms($confirm, $formid, $formdata){
        $vars = array();
        foreach ($_POST['wpforms']['fields'] as $id => $val){
            $vars[$id] = is_array($val) ? implode(" ", $val) : $val;
        }
        if(!isset($_POST['after_submit_wpdm'])) return $confirm;
        if(isset($_POST['after_submit_wpdm'])) {
            $file = array('ID' => (int)$_POST['after_submit_wpdm']);
            $download_url = \WPDM\Package::expirableDownloadLink((int)$_POST['after_submit_wpdm'], 3);
            $link_label = get_post_meta($_POST['after_submit_wpdm'], '__wpdm_link_label', true);
            $link_label = $link_label ? $link_label : 'Download';
            $email_link = get_post_meta($_POST['after_submit_wpdm'],'__wpdm_form_lock_email_downlad_link', true);
            if(!$email_link)
                $message = "<hr style='margin: 0 0 20px 0'/><a class='btn btn-success btn-lg btn-block' href='{$download_url}'><i class='fa fa-downlaod'></i> " . $link_label . "</a>";
            else {
                if (method_exists('\WPDM\Package', 'expirableDownloadPage'))
                    $file['download_url'] = \WPDM\Package::expirableDownloadPage(wpdm_query_var('after_submit_wpdm', 'int'), 3, 604800);
                else
                    $file['download_url'] = \WPDM\Package::expirableDownloadLink(wpdm_query_var('after_submit_wpdm', 'int'), 3, 604800);
                $file['post_title'] = get_the_title($file['ID']);
                $this->findEmail($_POST, $emails);
                $this->email_link($emails, $file, $vars);
                $message = "<div class='w3eden'><div class='alert alert-success'>Download link sent to your email!</div></div>";
            }

            \WPDM\Session::set('wpdm_form_lock_'.$_POST['wpforms']['id'], 'unlocked');
            do_action("wpdm_form_lock_submit_form", $_POST);
        }
        $confirm['confirmation'] .= $message;
        return $confirm;
    }


    function show_download_button_cf7($items, $result){

        if(isset($items['status']) && $items['status'] == 'mail_sent' && isset($_REQUEST['after_submit_wpdm'])){
            //$key = uniqid();
            //update_post_meta($_REQUEST['after_submit_wpdm'], "__wpdmkey_".$key, 3);
            //wpdmdd($_REQUEST);
            $file = array('ID' => $_REQUEST['after_submit_wpdm']);
            $download_url = \WPDM\Package::expirableDownloadLink((int)$_POST['after_submit_wpdm'], get_option('__wpdm_private_link_usage_limit', 3), get_option('__wpdm_private_link_expiration_period', 3)*get_option('__wpdm_private_link_expiration_period_unit', 60));
            $link_label = get_post_meta($_REQUEST['after_submit_wpdm'], '__wpdm_link_label', true);
            $link_label = $link_label ? $link_label : 'Download';
            $email_link = get_post_meta($_REQUEST['after_submit_wpdm'],'__wpdm_form_lock_email_downlad_link', true);
            $items['email_link'] = $email_link;
            if(!$email_link)
                $items['message'] .= "<hr style='margin: 10px 0'/><a class='btn btn-success' href='{$download_url}'><i class='fa fa-downlaod'></i> " . $link_label . "</a>";
            else {
                if (method_exists('\WPDM\Package', 'expirableDownloadPage'))
                    $file['download_url'] = \WPDM\Package::expirableDownloadPage(wpdm_query_var('after_submit_wpdm', 'int'), 3, 604800);
                else
                    $file['download_url'] = \WPDM\Package::expirableDownloadLink(wpdm_query_var('after_submit_wpdm', 'int'), 3, 604800);
                $file['post_title'] = get_the_title($file['ID']);
                $this->findEmail($_REQUEST, $emails);
                $this->email_link($emails, $file, $_REQUEST);
            }

            \WPDM\Session::set('wpdm_form_lock_'.wpdm_query_var('_wpcf7'), 'unlocked');
            do_action("wpdm_form_lock_submit_form", $_REQUEST);

        }
        return $items;

    }

    function wpcf7_form_response_output($output, $class, $content, $_this, $status){
        global $form_lock;
        if(!isset($_REQUEST['after_submit_wpdm'])) return $output;
        $file = array('ID' => $_REQUEST['after_submit_wpdm']);
        $download_url = \WPDM\Package::expirableDownloadLink((int)$_POST['after_submit_wpdm'], get_option('__wpdm_private_link_usage_limit', 3), get_option('__wpdm_private_link_expiration_period', 3)*get_option('__wpdm_private_link_expiration_period_unit', 60));
        $link_label = get_post_meta($_REQUEST['after_submit_wpdm'], '__wpdm_link_label', true);
        $link_label = $link_label ? $link_label : 'Download';
        $email_link = get_post_meta($_REQUEST['after_submit_wpdm'],'__wpdm_form_lock_email_downlad_link', true);

        if(!$email_link && isset($_REQUEST['after_submit_wpdm']))
            $output .= "<hr style='margin: 10px 0'/><a class='btn btn-success' href='{$download_url}'><i class='fa fa-downlaod'></i> " . $link_label . "</a>";
        else {
            if (method_exists('\WPDM\Package', 'expirableDownloadPage'))
                $file['download_url'] = \WPDM\Package::expirableDownloadPage(wpdm_query_var('after_submit_wpdm', 'int'), 3, 604800);
            else
                $file['download_url'] = \WPDM\Package::expirableDownloadLink(wpdm_query_var('after_submit_wpdm', 'int'), 3, 604800);
            $file['post_title'] = get_the_title($file['ID']);
            $emails = [];
            $this->findEmail($_REQUEST, $emails);
            $this->email_link($emails, $file, $_REQUEST);
        }

        \WPDM\Session::set('wpdm_form_lock_'.wpdm_query_var('_wpcf7'), 'unlocked');
        do_action("wpdm_form_lock_submit_form", $_REQUEST);
        return $output;
    }

    function show_download_button_fc_cf7($items, $result){

        if(isset($items['status']) && $items['status'] == 'mail_sent' && isset($_POST['wpdm_download_filecart'])){
            $key = uniqid();
            \WPDM\Session::set('_wpdm_unlocked_filecart', 1);
            if(isset($_POST['wpdm_download_filecart'])) {
                \WPDM\Session::set('_wpdm_unlocked_filecart', 1);
                $download_link = '<button class="btn btn-primary" id="download-file-cart"><i class="fa fa-download"></i> &nbsp; Download</button>';
                $email_link = get_option('__wpdm_file_cart_email_downlad_link', 0);
                if(!$email_link)
                    $items['message'] .= "<hr style='margin: 0 0 10px 0'/>$download_link";
                else {
                    $this->findEmail($_POST, $emails);
                    WPDM_FileCart::EmailFileCart($emails, \WPDM\Session::get('file_cart_data'));
                    $items['message'] .= "<div class='w3eden'><div class='alert alert-success'>Download link sent to your email!</div></div>";
                }
                do_action("wpdm_form_lock_submit_form", $_POST);
            }
        }

        return $items;

    }

    function findEmail($data, &$emails){
        foreach($data as $val) {
            if (is_array($val))
                $this->findEmail($val, $emails);
            else if(is_email($val))
                $emails[] = $val;
        }

    }

    function gform_pre_render($form){
        foreach ( $form['fields'] as &$field ) {
            if ( trim($field->defaultValue) == '[wpdm_package_id]' ) {
                if(get_option('__wpdm_gf_'.$form['id'].'_fieldid', 0) != $field->id)
                update_option('__wpdm_gf_'.$form['id'].'_fieldid', $field->id);
                return $form;
            }
        }

        return $form;
    }

    function after_submit_gravityform($message, $form, $entry, $ajax){
        $key = uniqid();
        $field_id = get_option('__wpdm_gf_'.$form['id'].'_fieldid');
        $field_name = 'input_'.$field_id;
        if(!isset($_POST[$field_name])) return $message;
        $pid = $_POST[$field_name];
        if(isset($_POST[$field_name])) {
            $file = array('ID' => $pid);
            $download_url = \WPDM\Package::expirableDownloadLink($pid, 3);

            if (method_exists('\WPDM\Package', 'expirableDownloadPage'))
                $download_page = \WPDM\Package::expirableDownloadPage($pid, 3, 604800);
            else
                $download_page = \WPDM\Package::expirableDownloadLink($pid, 3, 604800);

            $link_label = get_post_meta($pid, '__wpdm_link_label', true);
            $link_label = $link_label ? $link_label : __( "Download", "download-manager" );

            $email_link = get_post_meta($pid,'__wpdm_form_lock_email_downlad_link', true);
            if(!$email_link)
                $message .= "<hr style='margin: 10px 0'/><a class='btn btn-success' href='{$download_url}'><i class='fa fa-downlaod'></i> " . $link_label . "</a>";
            else {
                $file['download_url'] = $download_page;
                $file['post_title'] = get_the_title($file['ID']);
                $this->findEmail($_POST, $emails);
                $this->email_link($emails, $file);
            }

            \WPDM\Session::set('wpdm_form_lock_'.$form['id'], 'unlocked');
        }
        return $message;
    }


    function lock_settings($post = null)
    {
        $id = is_object($post)?$post->ID:null;
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        $formplugin = array();

        ?>
        <div class="panel panel-default card">
        <div class="panel-heading card-header"><label><input type="checkbox" rel="form_lock" class="wpdmlock" name="file[form_lock]" <?php if (get_post_meta($id, '__wpdm_form_lock', true) == '1') echo "checked=checked"; ?> value="1"><span class="checkx"><i class="fas fa-check-double"></i></span> <?php echo __('Enable Form Lock', 'wpdmpro'); ?></label></div>
        <div id="form_lock" class="formlock fwpdmlock panel-body card-body"  <?php if (get_post_meta($id, '__wpdm_form_lock', true) != '1') echo "style='display:none'"; ?> >


            <div class="form-group">
            Select From: <br/>
            <select id="fl" class="chzn-select" name="file[form_id]" style="min-width: 250px;width: 300px;">
                <?php if(is_plugin_active( 'liveforms/liveforms.php' )){ ?>
                <optgroup label="Live Forms">
                <?php
                $forms = get_posts('post_type=form&posts_per_page=1000');

                foreach ($forms as $form) {

                    // foreach($res as $row){
                    ?>

                    <option value="liveforms|<?php echo $form->ID; ?>" <?php if (get_post_meta(get_the_ID(), '__wpdm_form_id', true) == 'liveforms|'.$form->ID) echo "selected=selected"; ?> ><?php echo $form->post_title; ?></option>


                <?php

                }

                ?>
                </optgroup>
                <?php  } ?>
                <?php if(is_plugin_active( 'gravityforms/gravityforms.php' )){ ?>
                    <optgroup label="Gravity Forms">
                        <?php
                        $forms = GFAPI::get_forms();

                        foreach ($forms as $form) {
                            ?>

                            <option value="gravityforms|<?php echo $form['id']; ?>" <?php if (get_post_meta(get_the_ID(), '__wpdm_form_id', true) == 'gravityforms|'.$form['id']) echo "selected=selected"; ?> ><?php echo $form['title']; ?></option>


                            <?php

                        }

                        ?>
                    </optgroup>
                <?php  } ?>
                <?php if(is_plugin_active( 'contact-form-7/wp-contact-form-7.php' )){ ?>
                    <optgroup label="Contact Form 7">
                        <?php
                        $forms = get_posts('post_type=wpcf7_contact_form&posts_per_page=1000');

                        foreach ($forms as $form) {
                            ?>

                            <option value="contactform7|<?php echo $form->ID; ?>" <?php if (get_post_meta(get_the_ID(), '__wpdm_form_id', true) == 'contactform7|'.$form->ID) echo "selected=selected"; ?> ><?php echo $form->post_title; ?></option>


                            <?php

                        }

                        ?>
                    </optgroup>
                <?php  } ?>
                <?php do_action('wpdm_form_lock_dropdown'); ?>
            </select>
            </div>
            <div class="form-group">
            <label><input type="hidden" value="0" name="file[form_lock_email_downlad_link]" /><input type="checkbox" name="file[form_lock_email_downlad_link]" value="1" <?php checked(1, get_post_meta(get_the_ID(),'__wpdm_form_lock_email_downlad_link', true)); ?> /> Email Download Link</label>
            </div>
            <style>#fl_chosen{ width: 300px !important; }</style>


        </div>
        </div>

    <?php
    }

    function ninja_forms_dropdown(){
        if(!is_plugin_active( 'ninja-forms/ninja-forms.php' )) return;
        ?>

        <optgroup label="Ninja Forms">

            <?php
            $forms = Ninja_Forms()->forms()->get_all();

            foreach ($forms as $form_id) {
                $form = Ninja_Forms()->form( $form_id )->get_all_settings();
                ?>

                <option value="ninjaforms|<?php echo $form_id; ?>" <?php if (get_post_meta(get_the_ID(), '__wpdm_form_id', true) == 'ninjaforms|'.$form_id) echo "selected=selected"; ?> ><?php echo $form['form_title']; ?></option>


                <?php

            }

            ?>
        </optgroup>

        <?php
    }

    function formidable_dropdown(){
        global $wpdb;
        if(!is_plugin_active( 'formidable/formidable.php' )) return;
        ?>

        <optgroup label="Formidable Forms">

            <?php
            $forms = $wpdb->get_results("select * from {$wpdb->prefix}frm_forms where is_template=0");
            foreach ($forms as $form) {
                ?>

                <option value="formidable|<?php echo $form->id; ?>" <?php if (get_post_meta(get_the_ID(), '__wpdm_form_id', true) == 'formidable|'.$form->id) echo "selected=selected"; ?> ><?php echo $form->name; ?></option>


                <?php

            }

            ?>
        </optgroup>

        <?php
    }

    function formidable_filecart_dropdown(){
        global $wpdb;
        if(!is_plugin_active( 'formidable/formidable.php' )) return;
        ?>

        <optgroup label="Formidable Forms">

            <?php
            $forms = $wpdb->get_results("select * from {$wpdb->prefix}frm_forms where is_template=0");
            foreach ($forms as $form) {
                ?>

                <option value="formidable|<?php echo $form->id; ?>" <?php if (get_option( '__wpdm_file_cart_form' ) == 'formidable|'.$form->id) echo "selected=selected"; ?> ><?php echo $form->name; ?></option>


                <?php

            }

            ?>
        </optgroup>

        <?php
    }

    function wpforms_dropdown(){
        global $wpdb;
        if(!is_plugin_active( 'wpforms-lite/wpforms.php' ) && !is_plugin_active( 'wpforms/wpforms.php' )) return;
        ?>

        <optgroup label="WPForms Forms">

            <?php
            $forms = get_posts(array('post_type' => 'wpforms', 'post_status' => 'publish'));

            foreach ($forms as $form) {
                ?>

                <option value="wpforms|<?php echo $form->ID; ?>" <?php if (get_post_meta(get_the_ID(), '__wpdm_form_id', true) == 'wpforms|'.$form->ID) echo "selected=selected"; ?> ><?php echo $form->post_title; ?></option>


                <?php

            }

            ?>
        </optgroup>

        <?php
    }

    function wpforms_filecart_dropdown(){
        global $wpdb;
        if(!is_plugin_active( 'wpforms-lite/wpforms.php' ) && !is_plugin_active( 'wpforms/wpforms.php' )) return;
        ?>

        <optgroup label="WPForms Forms">

            <?php
            $forms = get_posts(array('post_type' => 'wpforms', 'post_status' => 'publish'));

            foreach ($forms as $form) {
                ?>

                <option value="wpforms|<?php echo $form->ID; ?>" <?php if (get_option('__wpdm_file_cart_form') == 'wpforms|'.$form->ID) echo "selected=selected"; ?> ><?php echo $form->post_title; ?></option>


                <?php

            }

            ?>
        </optgroup>

        <?php
    }




}



new WPDM_FormLock();

