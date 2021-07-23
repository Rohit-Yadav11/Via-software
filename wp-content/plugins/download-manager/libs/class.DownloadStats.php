<?php
/**
 * Class DownloadStats
 */

namespace WPDM\libs;

use WPDM\Session;

class DownloadStats
{

    function __construct()
    {

    }

    /**
     * @param $pid
     * @param $filename
     * @param $oid
     */
    function add($pid, $filename, $oid = null){
        global $wpdb, $current_user;

        //Handle downloads from email lock
        if(wpdm_query_var('subscriber' )){
            $subscriber = Crypt::decrypt(wpdm_query_var('subscriber' ));
            $wpdb->update("{$wpdb->prefix}ahm_emails", ['request_status' => 1], ['id' => $subscriber]);
        }

        $uid = get_current_user_id();
        $ip = (get_option('__wpdm_noip') == 0) ? wpdm_get_client_ip() : "";
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $hash = "uniq_".md5($pid.$uid.date("Y-m-d-h-i").wpdm_get_client_ip());
        if((int)Session::get($hash) === 1 || wpdm_query_var('nostat', ['validate' => 'int']) === 1) return;
        Session::set($hash, 1);
        $wpdb->insert("{$wpdb->prefix}ahm_download_stats", array('pid' => (int)$pid, 'uid' => (int)$uid, 'oid' => $oid, 'year' => date("Y"), 'month' => date("m"), 'day' => date("d"), 'timestamp' => time(), 'ip' => "$ip", 'filename' => $filename, 'agent' => $agent));
        update_post_meta($pid, '__wpdm_download_count', (int)get_post_meta($pid, '__wpdm_download_count', true) + 1);

        $udl = maybe_unserialize(get_post_meta($pid, "__wpdmx_user_download_count", true));
        if (is_user_logged_in()) {
            $index = $current_user->ID;
        } else {
            $index = str_replace(".", "_", $ip);
            if ($index == '') $index = uniqid();
        }

        $udl["{$index}"] = isset($udl["{$index}"]) && intval($udl["{$index}"]) > 0 ? (int)$udl["{$index}"] + 1 : 1;
        update_post_meta($pid, '__wpdmx_user_download_count', $udl);

        if ($ip == '') $ip = $index;
        Session::set('downloaded_' . $pid, $ip);
    }

    /**
     * @param $pid
     * @param $uid
     * @param $oid
     * @param string $filename
     */
    function newStat($pid, $uid, $oid, $filename = "")
    {
        global $wpdb, $current_user;
        return true;
        $ip = (get_option('__wpdm_noip') == 0) ? wpdm_get_client_ip() : "";
        $wpdb->insert("{$wpdb->prefix}ahm_download_stats", array('pid' => (int)$pid, 'uid' => (int)$uid, 'oid' => $oid, 'year' => date("Y"), 'month' => date("m"), 'day' => date("d"), 'timestamp' => time(), 'ip' => "$ip", 'filename' => $filename));
        update_post_meta($pid, '__wpdm_download_count', (int)get_post_meta($pid, '__wpdm_download_count', true) + 1);

        $udl = maybe_unserialize(get_post_meta($pid, "__wpdmx_user_download_count", true));
        if (is_user_logged_in()) {
            $index = $current_user->ID;
        } else {
            $index = str_replace(".", "_", $ip);
            if ($index == '') $index = uniqid();
        }
        if(!is_array($udl)) $udl = [];
        $udl["{$index}"] = isset($udl["{$index}"]) && intval($udl["{$index}"]) > 0 ? (int)$udl["{$index}"] + 1 : 1;
        update_post_meta($pid, '__wpdmx_user_download_count', $udl);

        if ($ip == '') $ip = $index;
        Session::set('downloaded_' . $pid, $ip);
    }


}
