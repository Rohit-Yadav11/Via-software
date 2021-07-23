<?php

namespace WPDM;

global $gp1c, $tbc;


class PackageLocks
{

    public function __construct(){
        global $post;
        //if(has_shortcode($post->post_content, "[wpdm_package]"))
        add_action('wp_enqueue_scripts', array($this, 'Enqueue'));
    }

    function Enqueue(){
       // wp_enqueue_script('wpdm-fb', 'http://connect.facebook.net/en_US/all.js?ver=3.1.3#xfbml=1');
    }

    public static function linkedInShare($package)
    {

        return "<button class='wpdm-social-lock btn wpdm-linkedin' data-url='".SocialConnect::LinkedinAuthUrl($package['ID'])."'><i class='fab fa-linkedin-in'></i> ".__( "Share", "download-manager" )."</button>";


    }

    public static function googlePlusShare($package){

        $tmpid = "gps_".uniqid();
        $var = md5('li_visitor.' . $_SERVER['REMOTE_ADDR'] . '.' . $tmpid . '.' . md5(get_permalink($package['ID'])));
        $req = home_url('/?pid=' . $package['ID'] . '&var=' . $var);
        $home = home_url('/');
        $force = str_replace("=", "", base64_encode("unlocked|" . date("Ymdh")));
        $href = $package['google_plus_share'];
        $href = $href ? $href : get_permalink($package['ID']);
        $msg = ""; //isset($package['linkedin_message']) && $package['linkedin_message'] !=''? $package['linkedin_message']:$package['post_title'];
        $msg .= " ".$href;
        ob_start();

        ?>

        <!-- Place this tag in your head or just before your close body tag. -->
        <script src="https://apis.google.com/js/platform.js" async defer></script>
        <div id="lin_<?php echo $tmpid; ?>"></div>
        <div id="wpdm_dlbtn_<?php echo $tmpid; ?>"></div>
        <!-- Place this tag where you want the share button to render. -->
        <div class="g-plus" data-href="<?php echo $href; ?>" data-action="share" data-onendinteraction="download_file_<?php echo $tmpid; ?>"></div>

        <script>
            function download_file_<?php echo $tmpid; ?>(data) {
                if(data.type != 'confirm') return;
                console.log(data);
                var ctz = new Date().getMilliseconds();
                jQuery.post("<?php echo $home; ?>?__wpdmnocache="+ctz,{id:<?php echo $package['ID']; ?>,dataType:'json',execute:'wpdm_getlink',force:'<?php echo $force; ?>',social:'l',action:'wpdm_ajax_call'},function(res){
                    if(res.downloadurl!=""&&res.downloadurl!=undefined) {
                        jQuery('#wpdmslb-googleshare-<?php echo $package['ID']; ?>').addClass('wpdm-social-lock-unlocked').html('<a href="'+res.downloadurl+'" class="wpdm-download-button btn btn-secondary btn-block">Download</a>');
                        window.open(res.downloadurl);
                    } else {
                        jQuery("#lin_<?php echo $tmpid; ?>").html(""+res.error);
                    }
                }, "json");
            }
        </script>

        <?php
        $data = ob_get_clean();
        return $data;
    }

    public static function googlePlusOne($package, $buttononly = false)
    {
        global $gp1c;

        return "<button class='wpdm-social-lock btn wpdm-google-plus' data-url='".SocialConnect::GooglePlusUrl($package['ID'])."'><i class='fab fa-google-plus-g'></i> Connect</button>";

    }

    public static function twitterFollow($package){

        return "<button class='wpdm-social-lock btn wpdm-twitter' data-url='".SocialConnect::TwitterAuthUrl($package['ID'], 'follow')."'><i class='fab fa-twitter'></i> Follow</button>";

        ob_start();
        $tmpid = "tf_".uniqid();
        $home = home_url('/');
        $twitter_handle = $package['twitter_handle'];
        $force = str_replace("=", "", base64_encode("unlocked|" . date("Ymdh")));
        ?>
        <div class="wpdm-social-lock-box wpdmslb-twitterfollow" id="wpdmslb-twitterfollow-<?php echo $package['ID']; ?>">
            <div class="placehold wpdmtwitter"><i class="fab fa-twitter"></i></div>
        <div id="lin_<?php echo $tmpid; ?>"></div>
        <div id="wpdm_dlbtn_<?php echo $tmpid; ?>"></div>
        <a href="https://twitter.com/<?php echo $twitter_handle; ?>" class="twitter-follow-button" id="follow-me-<?php echo $package['ID']; ?>" data-pid="<?php echo $package['ID']; ?>" data-show-count="false">Follow @<?php echo $twitter_handle; ?></a>
        <script src="//platform.twitter.com/widgets.js" type="text/javascript"></script>
        <script type="text/javascript">
        if(typeof twttr != 'undefined') {
            twttr.events.bind('follow', function (event) {
                console.log(event);
                var followed_user_id = event.data.user_id;
                var followed_screen_name = event.data.screen_name;
                var pid = localStorage.getItem('tfid');
                var ctz = new Date().getMilliseconds();
                jQuery.post("<?php echo $home; ?>?__wpdmnocache=" + ctz, {
                    id: pid,
                    dataType: 'json',
                    execute: 'wpdm_getlink',
                    force: '<?php echo $force; ?>',
                    social: 'l',
                    action: 'wpdm_ajax_call'
                }, function (res) {
                    if (res.downloadurl != "" && res.downloadurl != undefined) {
                        jQuery('#wpdmslb-twitterfollow-' + pid).addClass('wpdm-social-lock-unlocked').html('<a href="' + res.downloadurl + '" class="wpdm-download-button btn btn-secondary btn-block">Download</a>');
                        window.open(res.downloadurl);
                    } else {
                        jQuery("#lin_<?php echo $tmpid; ?>").html("" + res.error);
                    }
                }, "json");

            });

            twttr.events.bind(
                'click',
                function (ev) {
                    var pid = jQuery('#'+ev.target.id).parent().attr('id').replace('wpdmslb-twitterfollow-', '');
                    localStorage.setItem('tfid', pid);

                }
            );
        }

        </script>
        </div>
        <?php

        $data = ob_get_clean();
        return $data;
    }

    public static function askPassword($package){
        ob_start();
        $unqid = uniqid();
        $field_id = $unqid.'_'.$package['ID'];
        include wpdm_tpl_path("lock-options/password-lock.php", WPDM_TPL_DIR, WPDM_TPL_FALLBACK);
        $data = ob_get_clean();
        return $data;
    }

    public static  function askEmail($package)
    {

        $data = '<div class="alert alert-danger">Email Lock Is Not Enabled for This Download!</div>';
        if (isset($package['email_lock']) && $package['email_lock'] == '1') {

            $lock = 'locked';
            $unqid = uniqid();
            $package['email_lock_title'] = !isset($package['email_lock_title']) || $package['email_lock_title'] === '' ? get_post_meta($package['ID'], '__wpdm_email_lock_title', true) : $package['email_lock_title'];
            $section_title = $package['email_lock_title'] != ''? $package['email_lock_title']:__( "Subscribe To Download" , "download-manager" );
            $button_label = isset($package['button_label']) ? $package['button_label'] : __( "Download" , "download-manager" );
            $form_button_label = __( "Submit" , "download-manager" );
            $form_button_label = apply_filters("wpdm_email_lock_form_button_label", $form_button_label, $package);
            $intro = isset($package['email_intro']) ? "<p>" . $package['email_intro'] . "</p>" : '';
            $field_id = $unqid.'_'.$package['ID'];

            ob_start();
            include wpdm_tpl_path("lock-options/email-lock-form.php", WPDM_TPL_DIR, WPDM_TPL_FALLBACK);
            $data = ob_get_clean();

        }
        return apply_filters("wpdm_email_lock_html", $data);
    }

    public static function tweet($package){
        return "<button class='wpdm-social-lock btn wpdm-twitter' data-url='".SocialConnect::TwitterAuthUrl($package['ID'])."'><i class='fab fa-twitter'></i> Tweet</button>";
    }

    public static function facebookLike($package, $buttononly = false)
    {


        return "<button class='wpdm-social-lock btn wpdm-facebook' data-url='".SocialConnect::FacebookLikeUrl($package['ID'])."'><i class='fab fa-facebook-f'></i> ".__( "Like", "download-manager" )."</button>";

        $url = $package['facebook_like'];
        $url = $url ? $url : get_permalink();
        $dlabel =  __( "Download" , "download-manager" );
        $force = str_replace("=", "", base64_encode("unlocked|" . date("Ymdh")));
        //return '<div class="fb-like" data-href="'.$url.'#'.$package['ID'].'" data-send="false" data-width="300" data-show-faces="false" data-font="arial"></div>';
        $unlockurl = home_url("/?id={$package['ID']}&execute=wpdm_getlink&force={$force}&social=f");
        $btitle = isset($package['facebook_heading']) ? $package['facebook_heading'] : __( "Like on FB to Download" , "download-manager" );
        $intro = isset($package['facebook_intro']) ? "<p>" . $package['facebook_intro'] . "</p>" : '';

        if($buttononly==true){
            return '<div id="wpdmslb-facebooklike-'.$package['ID'].'" class="wpdm-social-lock-box wpdmslb-facebooklike">' .'

    <div class="placehold wpdmfacebook"><i class="fa fa-thumbs-up"></i></div>
  <div class="labell">
     <div style="display:none" id="' . strtolower(str_replace(array("://", "/", "."), "", $url)) . '" >' . $package['ID'] . '</div>
     <script>
     var ctz = new Date().getMilliseconds();
            var siteurl = "' . home_url('/?__wpdmnocache=') . '"+ctz,force="' . $force . '", appid="' . get_option('_wpdm_facebook_app_id', 0) . '";
            window.fbAsyncInit = function() {
                 console.log(FB);
                FB.Event.subscribe(\'edge.create\', function(href) {
                    console.log("FB Like");
                    console.log(href);
                    var id = href.replace(/[^0-9a-zA-Z-]/g,"");
                    id = id.toLowerCase();
                      var pkgid = jQuery(\'#\'+id).html();

                      jQuery.post(siteurl,{id:pkgid,dataType:\'json\',execute:\'wpdm_getlink\',force:force,social:\'f\',action:\'wpdm_ajax_call\'},function(res){
                                            if(res.downloadurl!=\'\'&&res.downloadurl!=\'undefined\'&&res!=\'undefined\') {
                                            window.open(res.downloadurl);
                                            jQuery(\'#wpdmslb-facebooklike-\'+pkgid).addClass(\'wpdm-social-lock-unlocked\').html(\'<a href="\'+res.downloadurl+\'" class="wpdm-download-button btn btn-secondary btn-block">'.$dlabel.'</a>\');
                                            } else {
                                                jQuery(\'#msg_\'+pkgid).html(\'\'+res.error);
                                            }
                                    });
                      return false;
                });
            };

            (function(d, s, id) {
             if(typeof FB != "undefined") return;
              var js, fjs = d.getElementsByTagName(s)[0];
              if (d.getElementById(id)) return;
              js = d.createElement(s); js.id = id; /* js.async = true; */
              js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=' . get_option('_wpdm_facebook_app_id', 0) . '";
              fjs.parentNode.insertBefore(js, fjs);
            }(document, \'script\', \'facebook-jssdk\'));
     </script>
     <div class="fb-like" data-href="' . $url . '" data-send="false" data-width="100" data-show-faces="false" data-layout="button_count" data-font="arial"></div>

     <style>.fb_edge_widget_with_comment{ max-height:20px !important; overflow:hidden !important;}</style>
     </div>
     </div>

     ';
        }

        return '
            <div class="panel panel-default">
            <div class="panel-heading">
    ' . $btitle . '
  </div>
  <div class="panel-body">

' . $intro . '<br/>
     <div id="fb-root"></div>
     <div style="display:none" id="' . str_replace(array("://", "/", "."), "", $url) . '" >' . $package['ID'] . '</div>
     <script>
            var siteurl = "' . home_url('/') . '",force="' . $force . '", appid="' . get_option('_wpdm_facebook_app_id', 0) . '";
            window.fbAsyncInit = function() {
               
                FB.Event.subscribe(\'edge.create\', function(href) {
                    var id = href.replace(/[^0-9a-z-]/g,"");
                      var pkgid = jQuery(\'#\'+id).html();

                      jQuery.post(siteurl,{id:pkgid,dataType:\'json\',execute:\'wpdm_getlink\',force:force,social:\'f\',action:\'wpdm_ajax_call\'},function(res){
                                            if(res.downloadurl!=\'\'&&res.downloadurl!=\'undefined\'&&res!=\'undefined\') {
                                            window.open(res.downloadurl);
                                            jQuery(\'#pkg_\'+pkgid).html(\'<a style="color:#000" href="\'+res.downloadurl+\'">'.$dlabel.'</a>\');                                             
                                            } else {
                                                jQuery(\'#msg_\'+pkgid).html(\'\'+res.error);
                                            }
                                    });
                      return false;
                });
            };

            (function(d, s, id) {
              var js, fjs = d.getElementsByTagName(s)[0];
              if (d.getElementById(id)) return;
              js = d.createElement(s); js.id = id; /* js.async = true; */
              js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=' . get_option('_wpdm_facebook_app_id', 0) . '";
              fjs.parentNode.insertBefore(js, fjs);
            }(document, \'script\', \'facebook-jssdk\'));
     </script>
     <div class="fb-like" data-href="' . $url . '" data-send="false" data-width="100" data-show-faces="false" data-layout="button_count" data-font="arial"></div>

     <style>.fb_edge_widget_with_comment{ max-height:20px !important; overflow:hidden !important;}</style>
     </div>

</div>
     ';

    }

    public static function reCaptchaLock($package, $buttononly = false){
        ob_start();
        $force = str_replace("=", "", base64_encode("unlocked|" . date("Ymdh")));
        include wpdm_tpl_path("lock-options/recaptcha-lock.php", WPDM_TPL_DIR, WPDM_TPL_FALLBACK);
        return ob_get_clean();
    }



}
