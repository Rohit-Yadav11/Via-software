<?php
namespace WPDM;

require_once dirname( __FILE__ ) . '/Twitter/autoload.php';


class TwitterConnect {

    function __construct() {
        if (version_compare(phpversion(), '5.4.0', '<')) return;
        add_action( 'init', array( $this, 'ConnectHelper' ) );
        add_action( 'init', array( $this, 'login' ) );
    }

    function login(){
        if(wpdm_query_var('sociallogin') == 'twitter'){
            if(!isset($_GET['oauth_token'])) {
                $connection = new \TwitterOAuth\TwitterOAuth(get_option('_wpdm_twitter_api_key'), get_option('_wpdm_twitter_api_secret'), get_option('_wpdm_twitter_access_token'), get_option('_wpdm_twitter_access_token_secret'));
                $request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => home_url('/?sociallogin=twitter')));
                Session::set('tw_oauth_token', $request_token['oauth_token']);
                Session::set('oauth_token_secret', $request_token['oauth_token_secret']);
                $url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
                header("location: " . $url);
                die();
            } else {

                $connection = new \TwitterOAuth\TwitterOAuth(get_option('_wpdm_twitter_api_key'), get_option('_wpdm_twitter_api_secret'), get_option('_wpdm_twitter_access_token'), get_option('_wpdm_twitter_access_token_secret'));
                $user = $connection->get("account/verify_credentials", array('include_email' => 'true'));

                $user_email = $user->id.'@twitter.com';
                $user_id = email_exists($user_email);
                if(intval($user_id) > 0) {
                    $euser = get_user_by( 'ID', $user_id );
                    if( $euser ) {
                        wp_set_current_user( $user_id, $euser->user_login );
                        wp_set_auth_cookie( $user_id );
                        do_action( 'wp_login', $euser->user_login, $euser );
                    }
                } else {

                    $user_pass = wp_generate_password(12, false);
                    $name = explode(" ", $user->name);
                    $user_login = sanitize_user($user->screen_name, true);
                    $sfx = '';
                    $user_login_orgn = $user_login;
                    while(username_exists($user_login_orgn.$sfx)){
                        $user_login = $user_login_orgn.$sfx;
                        if($sfx == '') $sfx = 0;
                        else $sfx++;
                    }

                    $user_id = wp_create_user($user_login, $user_pass, $user_email);
                    $display_name = $user->name;
                    //if($user_id){
                    //    \WPDM\Email::send("user-signup", array('to_email' => $user_email, 'name' => $display_name, 'username' => $user_login, 'password' => $user_pass));
                    //}

                    wp_update_user( array( 'ID' => $user_id, 'display_name' => $display_name ) );
                    update_user_meta($user_id, 'first_name', $name[0]);
                    update_user_meta($user_id, 'last_name', $name[1]);
                    update_user_meta($user_id, 'nickname', $display_name);

                    wp_set_current_user( $user_id, $user_login );
                    wp_set_auth_cookie( $user_id );
                    $_user = get_user_by('ID', $user_id);
                    do_action( 'wp_login', $user_login, $_user);


                }
                $this->redirect(wpdm_user_dashboard_url());
                die();
            }
        }
    }

    public static function loginURL($pid = '', $do = 'tweet'){
        $connection = new \TwitterOAuth\TwitterOAuth(get_option('_wpdm_twitter_api_key'), get_option('_wpdm_twitter_api_secret'), get_option('_wpdm_twitter_access_token'), get_option('_wpdm_twitter_access_token_secret'));
        $request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => home_url('/?connect=twitter&package='.$pid.'&do='.$do)));
        Session::set('tw_oauth_token', $request_token['oauth_token']);
        Session::set('tw_oauth_token_secret', $request_token['oauth_token_secret']);
        $url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
        return $url;
    }

    function connectHelper() {

        if(!isset($_GET['connect']) || $_GET['connect'] != 'twitter') return;

        $settings['oauth_access_token'] = get_option('_wpdm_twitter_access_token');
        $settings['oauth_access_token_secret'] = get_option('_wpdm_twitter_access_token_secret');
        $settings['consumer_key'] = get_option('_wpdm_twitter_api_key');
        $settings['consumer_secret'] = get_option('_wpdm_twitter_api_secret');

        $do = isset($_GET['do'])?$_GET['do']:'tweet';
        //print_r($_GET); die();
        if(wpdm_query_var('package', 'int') > 0 && ($do === 'tweet' && Session::get( '__twitted_'.wpdm_query_var('package', 'int') ))){
            echo "Already twitted once, starting download...";
            $this->download(wpdm_query_var('package', 'int'));
        }

        if(wpdm_query_var('package', 'int') > 0 && ($do == 'follow' && Session::get( '__followed_'.wpdm_query_var('package', 'int') ))){
            echo "Already following, starting download...";
            $this->download(wpdm_query_var('package', 'int'));
        }

        if(!isset($_GET['oauth_token'])) {
            $loginurl = TwitterConnect::loginURL(wpdm_query_var('package'), wpdm_query_var('do'));
            $try = isset($_GET['try'])?$_GET['try']+1:1;
            if($try > 2){
                $this->closePopup();
                die();
            }
            header("location: ". $loginurl."&try=".$try."&package=".(int)wpdm_query_var('package', 'int')."&do=".$_GET['do']);
            die();
        }

        $connection = new \TwitterOAuth\TwitterOAuth(get_option('_wpdm_twitter_api_key'), get_option('_wpdm_twitter_api_secret'), Session::get( 'tw_oauth_token' ), Session::get( 'tw_oauth_token_secret' ));
        $request_token = $connection->oauth('oauth/access_token', array('oauth_token' => $_GET['oauth_token'], 'oauth_verifier' => $_GET['oauth_verifier']));
        //wpdmdd($request_token);
        $oauth_token = $request_token['oauth_token'];
        $oauth_token_secret = $request_token['oauth_token_secret'];
        Session::set( '__tw_oauth_token' ,  $oauth_token);
        Session::set( '__tw_oauth_token_secret' ,  $oauth_token_secret);

        if($do == 'follow') {
            $this->follow((int)wpdm_query_var('package', 'int'));
        }
        else
            $this->tweet((int)wpdm_query_var('package', 'int'));

        $this->download((int)wpdm_query_var('package', 'int'));
        die();
    }

    function tweet($pid){
        $connection = new \TwitterOAuth\TwitterOAuth(get_option('_wpdm_twitter_api_key'), get_option('_wpdm_twitter_api_secret'), Session::get( '__tw_oauth_token' ), Session::get( '__tw_oauth_token_secret' ));
        $tweet = get_post_meta($pid, '__wpdm_tweet_message', true);
        if ($tweet == '') {
            $pack = get_post($pid);
            $tweet = $pack->post_title;
        }
        $tweet = substr($tweet, 0, 100) . " " . get_permalink($pid);
        $status = $connection->post("statuses/update", array("status" => $tweet));
        Session::set( '__twitted_'.$pid , 1 );
    }

    function follow($pid){
        $connection = new \TwitterOAuth\TwitterOAuth(get_option('_wpdm_twitter_api_key'), get_option('_wpdm_twitter_api_secret'), Session::get( '__tw_oauth_token' ), Session::get( '__tw_oauth_token_secret' ));
        $handle = get_post_meta($pid, '__wpdm_twitter_handle', true);
        $status = $connection->post("friendships/create", array("screen_name" => $handle, 'follow' => true));
        //echo "<pre>";print_r($status);die();
        Session::set( '__followed_'.$pid , 1 );
    }

    function download($pid){
        $key = uniqid();
        update_post_meta($pid, "__wpdmkey_".$key, apply_filters('wpdm_download_link_expiration_limit', 3, $pid));
        Session::set( '__wpdm_unlocked_'.$pid , 1 );
        $downloadurl = Package::expirableDownloadLink($pid);
        $this->redirect($downloadurl);
    }

    function redirect($url){
        ?>

        <script>
            window.opener.location.href = "<?php echo $url; ?>";
            document.write('You may close the window now.');
            setTimeout("window.close();", 2000);
        </script>

        <?php
        die();
    }

    function closePopup(){
        ?>

        <script>
            document.write('You may close the window now.');
            window.close();
        </script>

        <?php
        die();
    }



}

new TwitterConnect();
