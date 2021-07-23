<?php
/**
 * User: shahnuralam
 * Date: 01/11/18
 * Time: 7:08 PM
 * From v4.7.9
 * Last Updated: 10/11/2018
 */

namespace WPDM;

use WPDM\libs\Crypt;

class Session
{
    static $data;
    static $deviceID;
    static $store;

    function __construct()
    {

        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $deviceID = md5(wpdm_get_client_ip() . $agent);
        self::$deviceID = $deviceID;

        self::$store = get_option('__wpdm_tmp_storage', 'file');
        /*if(self::$store === 'cookie'){
            update_option('__wpdm_tmp_storage', 'file');
            self::$store = 'file';
        }*/

        if (self::$store === 'file') {
            if (file_exists(WPDM_CACHE_DIR . "/session-{$deviceID}.txt")) {
                $data = file_get_contents(WPDM_CACHE_DIR . "/session-{$deviceID}.txt");
                $data = Crypt::decrypt($data, true);
                if (!is_array($data)) $data = array();
            } else {
                $data = array();
            }

            self::$data = $data;

            register_shutdown_function(array($this, 'saveSession'));
        }
    }

    static function deviceID($deviceID)
    {
        self::$deviceID = $deviceID;
    }

    static function set($name, $value, $expire = 1800)
    {
        global $wpdb;
        //if(self::$store === 'cookie') setcookie($name, Crypt::encrypt($value), time() + $expire, '/');
        if (self::$store === 'file') self::$data[$name] = array('value' => $value, 'expire' => time() + $expire);
        if (self::$store === 'db') $wpdb->insert("{$wpdb->prefix}ahm_sessions", array('deviceID' => self::$deviceID, 'name' => $name, 'value' => maybe_serialize($value), 'expire' => time() + $expire));
    }

    static function get($name)
    {
        /*if(self::$store === 'cookie') {
            $value = isset($_COOKIE[$name]) ? $_COOKIE[$name] : '';
            $value = Crypt::decrypt($value);
        }*/
        if (self::$store === 'file') {
            if (!isset(self::$data[$name])) return null;
            $_value = self::$data[$name];
            if (count($_value) == 0) return null;
            extract($_value);
            if (isset($expire) && $expire < time()) {
                unset(self::$data[$name]);
                $value = null;
            }
        }
        if (self::$store === 'db') {
            global $wpdb;
            $deviceID = self::$deviceID;
            $value = $wpdb->get_var("select `value` from {$wpdb->prefix}ahm_sessions where deviceID = '{$deviceID}' and `name` = '{$name}'");
        }
        return maybe_unserialize($value);

    }

    static function clear($name = '')
    {
        global $wpdb;
        if ($name == '') {
            if (self::$store === 'file') self::$data = array();
            if (self::$store === 'db') $wpdb->delete("{$wpdb->prefix}ahm_sessions", array('deviceID' => self::$deviceID));
        } else {
            //if(self::$store === 'cookie') setcookie($name, null, '/', time() - 3600);
            if (self::$store === 'file' && isset(self::$data[$name])) unset(self::$data[$name]);
            if (self::$store === 'db') $wpdb->delete("{$wpdb->prefix}ahm_sessions", array('deviceID' => self::$deviceID, 'name' => $name));
        }
    }

    static function show()
    {
        wpdmprecho(self::$data);
    }

    static function saveSession()
    {
        if (self::$store === 'file' && is_array(self::$data) && count(self::$data) > 0) {
            $data = Crypt::encrypt(self::$data);
            if (!file_exists(WPDM_CACHE_DIR))
                @mkdir(WPDM_CACHE_DIR, 0755, true);
            file_put_contents(WPDM_CACHE_DIR . 'session-' . self::$deviceID . '.txt', $data);
        }

    }

}

new Session();

