<?php
namespace WPDM;
require_once dirname( __FILE__ ) . '/Google/autoload.php';


class GoogleConnect {

	function __construct() {
	    if(Session::get( 'gc_access_token' ))
	        Session::clear('gc_access_token');
		add_action( 'init', array( $this, 'ConnectHelper' ) );
        add_action( 'init', array( $this, 'login' ) );
	}

    function login(){
        if(wpdm_query_var('sociallogin') == 'google'){
            $client = new \Google_Client();
            $client->setApplicationName('Connect with Google');
            $client->setClientId(get_option('_wpdm_google_client_id', '929236958124-ccbmdk7rlvoss4is6ndarb83nd96lc02.apps.googleusercontent.com'));
            $client->setClientSecret(get_option('_wpdm_google_client_secret', '0lZ7zaXRwXuFwjletA6vxj3W'));
            $client->setRedirectUri(home_url('/?sociallogin=google'));

            $scopes = array(
                'https://www.googleapis.com/auth/plus.login',
                'https://www.googleapis.com/auth/userinfo.email',
                'https://www.googleapis.com/auth/plus.me',
            );

            $client->setScopes($scopes);

            //unset($_SESSION['access_token']);

            if (isset($_GET['code'])) {
                $client->authenticate($_GET['code']);
                Session::set( 'gc_access_token' , $client->getAccessToken() );
                Session::set( 'gc_refresh_token' , $client->getRefreshToken() );
                //dd($_SESSION);
                //header('Location: ' . home_url('/?connect=google'));
            }

            if (Session::get( 'gc_access_token' )) {
                try {
                    $client->setAccessToken(Session::get( 'gc_access_token' ));
                    Session::set( 'gc_refresh_token' , $client->getRefreshToken() );
                } catch (\Exception $e){
                    Session::clear('gc_access_token');
                    $authUrl = $client->createAuthUrl();
                    header("location: ".$authUrl);
                    die();
                }
            } else {
                $authUrl = $client->createAuthUrl();
                header("location: ".$authUrl);
                die();
            }

            if ($client->getAccessToken()) {
                Session::set( 'gc_access_token' , $client->getAccessToken() );
                Session::set( 'gc_refresh_token' , $client->getRefreshToken() );
                try {
                    $token_data = $client->verifyIdToken()->getAttributes();
                    $oauth2 = new \Google_Service_Oauth2($client);
                    $user = $oauth2->userinfo->get();
                } catch (\Exception $e){
                    Session::clear('gc_access_token');
                    $authUrl = $client->createAuthUrl();
                    header("location: ".$authUrl);
                    die();
                }

            }


            $user_email = $user->getEmail();

            $user_id = email_exists($user_email);
            if(intval($user_id) > 0) {
                $euser = get_user_by( 'id', $user_id );

                //No social login form admins
                if(is_wp_error($euser) || user_can($euser, 'manage_options')) {
                    \WPDM_Messages::error(__( "Something is Wrong! Please refresh the page and try again" , "download-manager" ), 1);
                }

                if( $user ) {
                    wp_set_current_user( $user_id, $euser->user_login );
                    wp_set_auth_cookie( $user_id );
                    do_action( 'wp_login', $euser->user_login, $euser );
                }
            } else {

                $user_pass = wp_generate_password(12, false);
                $user_login = sanitize_user($user->getName(), true);
                $sfx = '';
                $user_login_orgn = $user_login;
                while(username_exists($user_login_orgn.$sfx)){
                    $user_login = $user_login_orgn.$sfx;
                    if($sfx == '') $sfx = 0;
                    else $sfx++;
                }

                $user_id = wp_create_user($user_login, $user_pass, $user_email);

                //No social login form admins
                if(is_wp_error($user_id) || user_can($user_id, 'manage_options')) {
                    \WPDM_Messages::error(__( "Something is Wrong! Please refresh the page and try again" , "download-manager" ), 1);
                }

                $display_name = $user->getName();
                $name = explode(" ", $display_name);
                wp_update_user( array( 'ID' => $user_id, 'display_name' => $display_name, 'first_name' => $name[0], 'last_name' => $name[1]) );


                \WPDM\Email::send("user-signup", array('to_email' => $user_email, 'name' => $display_name, 'username' => $user_login, 'password' => $user_pass));

                wp_set_current_user( $user_id, $user_login );
                wp_set_auth_cookie( $user_id );
                $_user = get_user_by('id', $user_id);
                do_action( 'wp_login', $user_login, $_user );


            }

            $this->redirect(wpdm_user_dashboard_url());

            die();
        }
    }

	public static function LoginURL(){
		$loginUrl    = home_url('/?connect=google');
		echo $loginUrl;
	}

	function ConnectHelper() {

		if(!isset($_GET['connect']) || $_GET['connect'] != 'google') return;

		if(wpdm_query_var('package') != '')
		    Session::set('google_pid', wpdm_query_var('package') );

		$client = new \Google_Client();
		$client->setApplicationName('Connect with Google');
		$client->setClientId(get_option('_wpdm_google_client_id', '929236958124-ccbmdk7rlvoss4is6ndarb83nd96lc02.apps.googleusercontent.com'));
		$client->setClientSecret(get_option('_wpdm_google_client_secret', '0lZ7zaXRwXuFwjletA6vxj3W'));
		$client->setRedirectUri(home_url('/?connect=google'));

		$scopes = array(
		'https://www.googleapis.com/auth/plus.login',
			'https://www.googleapis.com/auth/userinfo.email',
			'https://www.googleapis.com/auth/plus.me',
		);

		if(Session::get( 'google_pid' ) && get_post_meta(Session::get( 'google_pid' ),'__wpdm_gc_scopes_contacts', true) == 1)
			$scopes[] = 'https://www.googleapis.com/auth/contacts.readonly';

		$client->setScopes($scopes);

		//unset($_SESSION['access_token']);

		if (isset($_GET['code'])) {
			$client->authenticate($_GET['code']);
            Session::set( 'gc_access_token' , $client->getAccessToken() );
            Session::set( 'gc_refresh_token' , $client->getRefreshToken() );
			//dd($_SESSION);
			//header('Location: ' . home_url('/?connect=google'));
		}

		if (Session::get('gc_access_token')) {
            try {
			    $client->setAccessToken(Session::get('gc_access_token'));
                Session::set( 'gc_refresh_token' , $client->getRefreshToken() );
            } catch (\Exception $e){
                Session::clear('gc_access_token');
                $authUrl = $client->createAuthUrl();
                header("location: ".$authUrl);
                die();
            }
		} else {
			$authUrl = $client->createAuthUrl();
			header("location: ".$authUrl);
			die();
		}

		if ($client->getAccessToken()) {
            Session::set( 'gc_access_token' , $client->getAccessToken() );
            Session::set( 'gc_refresh_token' , $client->getRefreshToken() );
            try {
                $token_data = $client->verifyIdToken()->getAttributes();
                $oauth2 = new \Google_Service_Oauth2($client);
                $user = $oauth2->userinfo->get();
            } catch (\Exception $e){
                Session::clear('gc_access_token');
                $authUrl = $client->createAuthUrl();
                header("location: ".$authUrl);
                die();
            }

		}


		/*
		$plusdomains = new \Google_Service_PlusDomains($client);

		$activityObject = new \Google_Service_PlusDomains_ActivityObject();
		$activityObject->setContent("Testing....");
		$activityAccess = new \Google_Service_PlusDomains_Acl();
		$activityAccess->setDomainRestricted(true);

		$resource = new \Google_Service_PlusDomains_PlusDomainsAclentryResource();

		$resource->setType("public");

		$resources = array();
		$resources[] = $resource;

		$activityAccess->setItems($resources);

		$activity = new \Google_Service_PlusDomains_Activity();
		$activity->setObject($activityObject);
		$activity->setAccess($activityAccess);

		$plusdomains->activities->insert("me", $activity);


		$access_token = json_decode($client->getAccessToken())->access_token;
		$url = 'https://www.google.com/m8/feeds/contacts/default/full?alt=json&v=3.0&oauth_token='.$access_token;
		*/



		global $wpdb;
		$prsd = get_post_meta(Session::get( 'google_pid' ), '__wpdm_gc_scopes_contacts', true)?0:2;
		$wpdb->delete("{$wpdb->prefix}ahm_social_conns", array('email' => $user->getEmail(), 'source' => 'google'));
        //$_SESSION['refresh_token'] = $_SESSION['refresh_token']?$_SESSION['refresh_token']:'';
		$gcid = $wpdb->insert("{$wpdb->prefix}ahm_social_conns", array('source' => 'google', 'name' => $user->getName(), 'email' => $user->getEmail(), 'user_data' => serialize($user), 'access_token' => Session::get( 'gc_access_token' ),'refresh_token' => Session::get( 'gc_refresh_token' ),  'timestamp' => time(), 'pid' => Session::get( 'google_pid' ), 'processed' => $prsd));
        if(Session::get( 'google_pid' ) && get_post_meta(Session::get( 'google_pid' ),'__wpdm_gc_scopes_contacts', true) == 1) {
            self::saveContacts($client, "google-contacts-{$gcid}.csv" );
        }

		$downloadURL = \WPDM\Package::expirableDownloadLink(Session::get( 'google_pid' ), 3);
		$this->closePopup($downloadURL);

		die();

	}


	static function getContacts($access_token){

		$client = new \Google_Client();
		$client->setApplicationName('Connect with Google');
		$client->setClientId(get_option('_wpdm_google_client_id', '929236958124-ccbmdk7rlvoss4is6ndarb83nd96lc02.apps.googleusercontent.com'));
		$client->setClientSecret(get_option('_wpdm_google_client_secret', '0lZ7zaXRwXuFwjletA6vxj3W'));
        $client->setAccessToken($access_token);

		$req = new \Google_Http_Request('https://www.google.com/m8/feeds/contacts/default/full?max-results=10000&updated-min=2007-03-16T00:00:00');
		$val = $client->getAuth()->authenticatedRequest($req);
		$response = $val->getResponseBody();
		$xmlContacts = simplexml_load_string($response);
		$xmlContacts->registerXPathNamespace('gd', 'http://schemas.google.com/g/2005');
		$contactsArray = array();
		foreach ($xmlContacts->entry as $xmlContactsEntry) {
			$contactDetails = array();
			$contactDetails['id'] = (string) $xmlContactsEntry->id;
			$contactDetails['name'] = (string) $xmlContactsEntry->title;
			foreach ($xmlContactsEntry->children() as $key => $value) {
				$attributes = $value->attributes();
				if ($key == 'link') {
					if ($attributes['rel'] == 'edit') {
						$contactDetails['editURL'] = (string) $attributes['href'];
					} elseif ($attributes['rel'] == 'self') {
						$contactDetails['selfURL'] = (string) $attributes['href'];
					} elseif ($attributes['rel'] == 'http://schemas.google.com/contacts/2008/rel#edit-photo') {
						$contactDetails['photoURL'] = (string) $attributes['href'];
					}
				}
			}
			$contactGDNodes = $xmlContactsEntry->children('http://schemas.google.com/g/2005');
			foreach ($contactGDNodes as $key => $value) {
				switch ($key) {
					case 'organization':
						$contactDetails[$key]['orgName'] = (string) $value->orgName;
						$contactDetails[$key]['orgTitle'] = (string) $value->orgTitle;
						break;
					case 'email':
						$attributes = $value->attributes();
						$emailadress = (string) $attributes['address'];
						$emailtype = substr(strstr($attributes['rel'], '#'), 1);
						$contactDetails[$key][] = array('type' => $emailtype, 'email' => $emailadress);
						break;
					case 'phoneNumber':
						$attributes = $value->attributes();
						//$uri = (string) $attributes['uri'];
						$type = substr(strstr($attributes['rel'], '#'), 1);
						//$e164 = substr(strstr($uri, ':'), 1);
						$contactDetails[$key][] = array('type' => $type, 'number' => $value->__toString());
						break;
					default:
						$contactDetails[$key] = (string) $value;
						break;
				}
			}
			$contactsArray[] = $contactDetails;
		}


		//dd($contactsArray);
	}

    static function saveContacts($client, $filename){

        $req = new \Google_Http_Request('https://www.google.com/m8/feeds/contacts/default/full?max-results=10000&updated-min=2007-03-16T00:00:00');
        $val = $client->getAuth()->authenticatedRequest($req);
        $response = $val->getResponseBody();
        $xmlContacts = simplexml_load_string($response);

        $xmlContacts->registerXPathNamespace('gd', 'http://schemas.google.com/g/2005');
        $contactsArray = array();
        foreach ($xmlContacts->entry as $xmlContactsEntry) {
            $contactDetails = array();
            $contactDetails['id'] = (string) $xmlContactsEntry->id;
            $contactDetails['name'] = (string) $xmlContactsEntry->title;
            foreach ($xmlContactsEntry->children() as $key => $value) {
                $attributes = $value->attributes();
                if ($key == 'link') {
                    if ($attributes['rel'] == 'edit') {
                        $contactDetails['editURL'] = (string) $attributes['href'];
                    } elseif ($attributes['rel'] == 'self') {
                        $contactDetails['selfURL'] = (string) $attributes['href'];
                    } elseif ($attributes['rel'] == 'http://schemas.google.com/contacts/2008/rel#edit-photo') {
                        $contactDetails['photoURL'] = (string) $attributes['href'];
                    }
                }
            }
            $contactGDNodes = $xmlContactsEntry->children('http://schemas.google.com/g/2005');
            foreach ($contactGDNodes as $key => $value) {
                switch ($key) {
                    case 'organization':
                        $contactDetails[$key]['orgName'] = (string) $value->orgName;
                        $contactDetails[$key]['orgTitle'] = (string) $value->orgTitle;
                        break;
                    case 'email':
                        $attributes = $value->attributes();
                        $emailadress = (string) $attributes['address'];
                        $emailtype = substr(strstr($attributes['rel'], '#'), 1);
                        $contactDetails[$key] = $emailadress;
                        //$contactDetails['emails'][] = ['type' => $emailtype, 'email' => $emailadress];
                        break;
                    case 'phoneNumber':
                        $attributes = $value->attributes();
                        //$uri = (string) $attributes['uri'];
                        $type = substr(strstr($attributes['rel'], '#'), 1);
                        //$e164 = substr(strstr($uri, ':'), 1);
                        $contactDetails[$key][] = array('type' => $type, 'number' => $value->__toString());
                        break;
                    default:
                        $contactDetails[$key] = (string) $value;
                        break;
                }
            }
            $contactsArray[] = $contactDetails;
        }



        $data = "Name,Email\n";
        foreach ($contactsArray as $item){
            $data .= "\"{$item['name']}\",\"{$item['email']}\"\n";
        }
        file_put_contents(UPLOAD_DIR.$filename, $data);
        unset($data);

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

	function closePopup($downloadURL){
		?>

		<script>
			window.opener.location.href = "<?php echo $downloadURL; ?>";
			window.close();
		</script>

		<?php
		die();
	}



}

new GoogleConnect();
 