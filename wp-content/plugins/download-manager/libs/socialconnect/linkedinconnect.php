<?php
namespace WPDM;

include_once __DIR__ . '/LinkedIn/LinkedIn.php';


class LinkedInConnect {

    public $linkedIn;

	function __construct() {
        if (version_compare(phpversion(), '5.4.0', '<')) return;

		add_action( 'init', array( $this, 'connectHelper' ) );
		add_action( 'init', array( $this, 'login' ) );

	}

	function login(){
        if(wpdm_query_var('sociallogin') == 'linkedin'){
            if(!isset($_GET['code'])) {

                $client = new LinkedIn(get_option('_wpdm_linkedin_client_id'), get_option('_wpdm_linkedin_client_secret'), home_url('/?sociallogin=linkedin&package='.wpdm_query_var('package', 'int')), "r_liteprofile r_emailaddress w_member_social");

                $loginUrl = $client->getAuthUrl();

                header("location: " . $loginUrl);

            } else{

                $client = new LinkedIn(get_option('_wpdm_linkedin_client_id'), get_option('_wpdm_linkedin_client_secret'), home_url('/?sociallogin=linkedin&package='.wpdm_query_var('package', 'int')), "r_liteprofile r_emailaddress w_member_social");
                $token = $client->getAccessToken(wpdm_query_var('code'));
                $user = $client->getPerson($token);

                $user_email = $user['email'];
                $user_id = email_exists($user_email);
                if(intval($user_id) > 0) {
                    $euser = get_user_by( 'ID', $user_id );

                    //No social login form admins
                    if(is_wp_error($euser) || user_can($euser, 'manage_options')) {
                        \WPDM_Messages::error(__( "Something is Wrong! Please refresh the page and try again" , "download-manager" ), 1);
                    }

                    if( $euser ) {
                        wp_set_current_user( $user_id, $euser->user_login );
                        wp_set_auth_cookie( $user_id );
                        do_action( 'wp_login', $euser->user_login, $euser );
                    }
                } else {

                    $user_pass = wp_generate_password(12, false);
                    $user_login = sanitize_user($user['first_name'].$user['last_name'], true);
                    $sfx = '';
                    $user_login_orgn = $user_login;
                    while(username_exists($user_login_orgn.$sfx)){
                        $user_login = $user_login_orgn.$sfx;
                        if($sfx == '') $sfx = 0;
                        else $sfx++;
                    }

                    $user_id = wp_create_user($user_login, $user_pass, $user_email);

                    //No social login form admins
                    if((int)$user_id < 1 || is_wp_error($user_id) || user_can($user_id, 'manage_options')) {
                        \WPDM_Messages::error(__( "Something is Wrong! Please refresh the page and try again" , "download-manager" ), 1);
                    }

                    $display_name = $user['firstName']." ".$user['lastName'];

                    if($user_id){
                        \WPDM\Email::send("user-signup", array('to_email' => $user_email, 'name' => $display_name, 'username' => $user_login, 'password' => $user_pass));
                    }

                    wp_update_user( array( 'ID' => $user_id, 'display_name' => $display_name, 'first_name' => $user['firstName'], 'last_name' => $user['lastName'] ) );

                    wp_set_current_user( $user_id, $user_login );
                    wp_set_auth_cookie( $user_id );
                    $_user = get_user_by('ID', $user_id);
                    do_action( 'wp_login', $user_login, $_user);


                }
            }
            $this->redirect(wpdm_user_dashboard_url());
            die();
        }
    }

	public static function LoginURL($pid, $direct = 0){
		if($direct == 0){
			return home_url('/?connect=linkedin&__plin=0&package=' . $pid);
		} else {

            $client = new LinkedIn(get_option('_wpdm_linkedin_client_id'), get_option('_wpdm_linkedin_client_secret'), home_url('/?connect=linkedin&package=' . $pid), "r_liteprofile r_emailaddress w_member_social");
            $loginUrl = $client->getAuthUrl();
            return $loginUrl;
		}
	}

	function connectHelper() {

		if(!isset($_GET['connect']) || $_GET['connect'] != 'linkedin') return;

		if(isset($_REQUEST['__plin']) && wpdm_query_var('__plin') == 0){
			$jquery = includes_url("/js/jquery/jquery.js");
			$pid = wpdm_query_var('package');
			$url = get_post_meta($pid,'__wpdm_linkedin_url', true);
			$msg = get_post_meta($pid,'__wpdm_linkedin_message', true);
			$href = $url ? $url : get_permalink($pid);
			$msg = trim($msg) !=''? $msg:get_the_title($pid);
			?>
			<!DOCTYPE html>
			<html style="padding: 0;margin: 0">
			<head>
				<title>LinkedIn Connect</title>
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
				<script src="<?php echo $jquery; ?>"></script>
				<link rel="stylesheet" href="<?php echo WPDM_BASE_URL.'assets/bootstrap/css/bootstrap.css'; ?>">
				<link rel="stylesheet" href="<?php echo WPDM_BASE_URL.'assets/css/front.css'; ?>">
				<link rel="stylesheet" href="<?php echo WPDM_BASE_URL.'assets/font-awesome/css/font-awesome.min.css'; ?>">
				<link href="https://fonts.googleapis.com/css?family=Slabo+27px" rel="stylesheet">
				<style>
                    body{
                        font-family: 'Slabo 27px', serif;
                        padding: 20px;margin: 0;
                        word-break: break-word;
                    }
                    @media (min-width: 800px) {
                        body{
                            padding: 50px 80px;margin: 0;
                        }
                    }
				</style>
				<script>
					jQuery(function ($) {
						var target = '<?php echo $url; ?>';
						$.ajax({
							url: "https://api.linkpreview.net",
							dataType: 'jsonp',
							data: {q: target, key: '59fa36de8df86444d8477c9764f0afff3f91ee5165019'},
							success: function (response) {
								console.log(response);
								$('#title').html(response.title);
								if(response.description.indexOf('orbidden') < 1)
									$('#description').html(response.description);
								if(response.image != undefined && response.image != ''){
									$('#ppic').html('<img style="max-width: 64px" src="'+response.image+'" alt="'+response.title+'" />')
								}
							}
						});

					});
				</script>
			</head>
			<body class="w3eden">
			<div id="fb-root"></div>

			<div class="page-info">
				<div class="panel panel-default" style="margin: 0">
					<div class="panel-body">
						<blockquote>
							<?php echo $msg; ?>
						</blockquote>
						<div class="media">
							<div id="ppic" class="pull-left"></div>
							<div class="media-body">
								<h3 id="title" style="margin-top: 0;font-size: 14pt"></h3>
								<div id="description" style="font-size: 9pt;margin-bottom: 10px">

								</div>
								<div class="color-green"><i class="fa fa-link"></i> <?php echo $url; ?></div>
							</div>
						</div>
					</div>
					<div class="panel-footer">

						 <a class="wpdm-social-lock btn wpdm-linkedin" href="<?php echo self::LoginURL($pid, 1); ?>"><i class="fa fa-share-alt"></i> Share in LinkedIn</a>

					</div>
				</div>
			</div>
			</body>
			</html>
			<?php
			die();
		}
		if(wpdm_query_var('__plin') == 1){
			header("location: ".self::LoginURL(wpdm_query_var('package'), 1));
			die();
		}

		$client = new LinkedIn(get_option('_wpdm_linkedin_client_id'), get_option('_wpdm_linkedin_client_secret'), home_url('/?connect=linkedin&package=' . wpdm_query_var('package', 'int')), "r_liteprofile r_emailaddress w_member_social");
        $token = $client->getAccessToken(wpdm_query_var('code'));
        $user = $client->getPerson($token);
		$package = get_post(wpdm_query_var('package', 'int'));
		$force = str_replace("=", "", base64_encode("unlocked|" . date("Ymdh")));
		$href = get_post_meta($package->ID,'__wpdm_linkedin_url', true);
		$msg = get_post_meta($package->ID,'__wpdm_linkedin_message', true);
        $link = $href ? $href : get_permalink($package->ID);
		$msg = trim($msg) !=''? $msg:$package->post_title;
		//wpdmprecho($msg);
        //wpdmprecho($link);
        $ret = $client->linkedInLinkPost($token, $client->getPersonID($token), $msg, "", "", $link);
        //wpdmdd($ret);

        global $wpdb;
		$wpdb->delete("{$wpdb->prefix}ahm_social_conns", array('email' => $user['email'], 'source' => 'linkedin'));
		$wpdb->insert("{$wpdb->prefix}ahm_social_conns", array('source' => 'linkedin', 'name' => $user['first_name'].' '.$user['last_name'], 'email' => $user['email'], 'user_data' => serialize($user), 'access_token' => maybe_serialize($token), 'timestamp' => time(), 'pid' => $package->ID, 'processed' => 1));

		$this->download($package->ID);

	}


	function download($pid){
		$downloadURL = \WPDM\Package::expirableDownloadLink($pid, 3);
		$this->redirect($downloadURL);
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

new LinkedInConnect();
