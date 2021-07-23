<?php
/**
 * User: shahnuralam
 * Date: 11/9/15
 * Time: 7:44 PM
 */

namespace WPDM\admin\menus;


use WPDM\libs\FileSystem;

class Stats
{
    function __construct()
    {
        add_action('admin_menu', array($this, 'menu'));
        add_action('admin_init', array($this, 'export'));
    }

    function menu()
    {
        add_submenu_page('edit.php?post_type=wpdmpro', __( "Stats &lsaquo; Download Manager" , "download-manager" ), __("Stats" , "download-manager" ), WPDM_MENU_ACCESS_CAP, 'wpdm-stats', array($this, 'UI'));
    }

    function UI()
    {
        include(WPDM_BASE_DIR."admin/tpls/stats.php");
    }

    function export(){
        if(wpdm_query_var('page') == 'wpdm-stats' && wpdm_query_var('task') == 'export'){
            global $wpdb;
            //$data = $wpdb->get_results("select s.*, p.post_title as file from {$wpdb->prefix}ahm_download_stats s, {$wpdb->prefix}posts p where p.ID = s.pid order by id DESC");
            $total = $wpdb->get_var("select count(*) as total from {$wpdb->prefix}ahm_download_stats");
            FileSystem::downloadHeaders("download-stats.csv");
            echo "Package ID,Package Name,User ID,User Name,User Email,Order ID,Date,Timestamp\r\n";
            $pages = $total / 20;
            if($pages > (int)$pages) $pages++;
            for($i = 0; $i <= $pages; $i++) {
                $start = $i * 20;
                $data = $wpdb->get_results("select * from {$wpdb->prefix}ahm_download_stats order by id DESC limit $start, 20");
                foreach ($data as $d) {
                    $package_name = get_the_title($d->pid);
                    $package_name = addslashes($package_name);
                    if ($d->uid > 0) {
                        $u = get_user_by('ID', $d->uid);
                        echo "{$d->pid},\"{$package_name}\",{$d->uid},\"{$u->display_name}\",\"{$u->user_email}\",{$d->oid},{$d->year}-{$d->month}-{$d->day},{$d->timestamp}\r\n";
                    } else
                        echo "{$d->pid},\"{$package_name}\",-,\"-\",\"-\",{$d->oid},{$d->year}-{$d->month}-{$d->day},{$d->timestamp}\r\n";
                }
                ob_flush();
            }
            die();
        }
    }


}
