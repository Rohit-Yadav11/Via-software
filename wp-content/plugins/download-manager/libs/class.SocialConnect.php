<?php

namespace WPDM;
 


class SocialConnect {

	function __construct(){

		require_once dirname(__FILE__).'/socialconnect/facebookconnect.php';
		require_once dirname(__FILE__).'/socialconnect/googleconnect.php';
		require_once dirname(__FILE__).'/socialconnect/linkedinconnect.php';
		require_once dirname(__FILE__).'/socialconnect/twitterconnect.php';

	}

	public static function TwitterAuthUrl($pid, $action = 'tweet'){
	    return home_url("/?connect=twitter&package=".$pid.'&do='.$action);
    }
    public static function LinkedinAuthUrl($pid){
        return \WPDM\LinkedInConnect::LoginURL($pid);
    }
    public static function GooglePlusUrl($pid){
        return home_url("/?connect=google&package=".$pid);
    }
    public static function GoogleAuthUrl($pid){
        return home_url("/?connect=google&package=".$pid);
    }
    public static function FacebookLikeUrl($pid){
        return home_url("/?connect=facebook&like=1&package=".$pid);
    }
    public static function FacebookAuthUrl($pid){
        return home_url("/?connect=facebook&package=".$pid);
    }
}

new SocialConnect();