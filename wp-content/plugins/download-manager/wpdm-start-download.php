<?php

if(!defined("ABSPATH")) die('!');

error_reporting(0);

global $current_user, $dfiles;

//Check for blocked IPs
if(wpdm_ip_blocked()) {
    $_ipblockedmsg =  __('Your IP address is blocked!', 'download-manager');
    $ipblockedmsg = get_option('__wpdm_blocked_ips_msg', '');
    $ipblockedmsg = $ipblockedmsg == ''?$_ipblockedmsg:$ipblockedmsg;
    WPDM_Messages::error($ipblockedmsg, 1);
}

//Check for blocked users by email
if(is_user_logged_in() && !wpdm_verify_email($current_user->user_email)) {
    $emsg =  get_option('__wpdm_blocked_domain_msg');
    if(trim($emsg) === '') $emsg = __('Your email address is blocked!', 'download-manager');
    WPDM_Messages::fullPage('Error!', $emsg, 'error');
}

do_action("wpdm_onstart_download", $package);

$speed = (int)get_option('__wpdm_download_speed', 10240); //in KB - default 10 MB
$speed = $speed > 0 ? $speed : 10240;
$speed = apply_filters('wpdm_download_speed', $speed);
$user = get_user_by('id', $package['post_author']);
$user_upload_dir = UPLOAD_DIR . $user->user_login . '/';

$_content_dir = str_replace('\\','/',WP_CONTENT_DIR);
$_old_up_dir = $_content_dir.'/uploads/download-manager-files/';

//Only published packages are downloadable
if(in_array($package['post_status'], array('draft','inherit','trash','pending'))) WPDM_Messages::fullPage("404", "<div class='card p-4 bg-danger text-white'>".__( "Package you are trying to download is not available!" , "download-manager" )."</div>");

$limit_msg = WPDM_Messages::download_limit_exceeded($package['ID']);

if (wpdm_is_download_limit_exceed($package['ID'])) WPDM_Messages::fullPage("Error!", $limit_msg, 'error');
$files = \WPDM\Package::getFiles($package['ID']);

$fileCount = count($files) + (isset($package['package_dir']) && $package['package_dir'] !== '' ? 1 : 0);

if($fileCount === 0){
    WPDM_Messages::fullPage(__( "No Files", "download-manager" ), \WPDM\UI::card('', __( "No file is attached with this package!", "download-manager" ), "", ['class' => 'bg-danger text-white text-left']));
}

//$idvdl = Individual file download status
$idvdl = ( \WPDM\Package::isSingleFileDownloadAllowed($package['ID']) || wpdm_query_var('oid', false) ) && isset($_GET['ind']);

$parallel_download = (int)get_option('__wpdm_parallel_download', 1);

if($parallel_download === 0 && (int)\WPDM\TempStorage::get("download.".wpdm_get_client_ip()) === 1)
    WPDM_Messages::error(get_option('__wpdm_parallel_download_msg', "Another download is in progress from your IP, please wait until finished."), 1);

if ($fileCount > 1 && !$idvdl) {
    $zipped = get_post_meta($package['ID'], "__wpdm_zipped_file", true);
    $cache_zip = get_option('__wpdm_cache_zip', 0);
    $cache_zip_po = get_post_meta($package['ID'], "__wpdm_cache_zip", true);
    //dd($cache_zip_po);
    $cache_zip = $cache_zip_po == -1 || !$cache_zip_po ? $cache_zip : $cache_zip_po;
    if ($zipped == '' || !file_exists($zipped) || $cache_zip == 0) {

        if(class_exists('ZipArchive'))
            $zip = new ZipArchive();
        else WPDM_Messages::fullPage('Error!', "<div class='card bg-danger text-white p-4'>".__( "<b>Zlib</b> is not active! Failed to initiate <b>ZipArchive</b>" , "download-manager" )."</div>", 'error');

        $package_dir = $package['package_dir'] !== '' && file_exists($package['package_dir']) ? $package['package_dir'] : \WPDM\libs\Crypt::decrypt($package['package_dir']);
        if($package_dir !== '' && file_exists($package_dir)) {
            $zipped = \WPDM\libs\FileSystem::zipDir($package_dir);
            $files['package_dir'] = $zipped;
        }

        $zipname = sanitize_file_name($package['post_title']) . '-' . $package['ID'] . '.zip';
        $zipped = UPLOAD_DIR . $zipname;
        if(file_exists($zipped)) {
            @unlink($zipped);
        }
        if ($zip->open($zipped , ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            wpdm_download_data('error.txt', 'Failed to create file. Please make "' . UPLOAD_DIR . '" dir writable..');
            die();
        }

        foreach ($files as $file) {
            $file = trim($file);
            $tmp = explode("wp-content", $file);
            if(substr_count($file, "../") > 0 || substr_count(strtolower($file), ".php") > 0) continue;
            if (file_exists(UPLOAD_DIR . $file)) {
                $fpath = UPLOAD_DIR . $file;
                $zip->addFile($fpath, wpdm_basename($file));
            }
            else if (file_exists($user_upload_dir.$file))
                $zip->addFile($user_upload_dir.$file, wpdm_basename($file));
            else if (file_exists($file))
                $zip->addFile($file, wpdm_basename($file));
            else if (file_exists(WP_CONTENT_DIR . end($tmp))) {//path fix on site move
                $tmpf = explode("wp-content", $file);
                $zip->addFile(WP_CONTENT_DIR . end($tmpf), wpdm_basename($file));
            }
            else if (file_exists($_old_up_dir . $file))
                $zip->addFile($_old_up_dir . $file);
        }

        $zip->close();
        update_post_meta($package['ID'], "__wpdm_zipped_file", $zipped);
    }

    WPDM()->downloadHistory->add($package['ID'], '', wpdm_query_var('oid'));
    wpdm_download_file($zipped, sanitize_file_name($package['post_title']) . '.zip', $speed, 1, $package);
    //@unlink($zipped);
}
else {

    if(!wpdm_query_var('ind')) {
        $package_dir = $package['package_dir'] !== '' && file_exists($package['package_dir']) ? $package['package_dir'] : \WPDM\libs\Crypt::decrypt($package['package_dir']);
        if ($package_dir !== '' && !file_exists($package_dir)) {
            WPDM_Messages::fullPage('Error!', "<div class='card bg-danger text-white p-4'>" . __("Invalid dir path.", "download-manager") . "</div>", 'error');
        }

        if ($package_dir !== '' && file_exists($package_dir)) {
            $zipped = \WPDM\libs\FileSystem::zipDir($package_dir);
            WPDM()->downloadHistory->add($package['ID'], $package_dir, wpdm_query_var('oid'));
            \WPDM\libs\FileSystem::downloadFile($zipped, basename($zipped));
            die();
        }
    }

    //Individual file or single file download section

    $indfile = '';

    if (isset($_GET['ind'])) {
        $indfile = isset($files[esc_attr($_GET['ind'])])?$files[esc_attr($_GET['ind'])]:\WPDM\libs\Crypt::Decrypt(esc_attr($_GET['ind']));

    } else if ($fileCount == 1) {
        $tmpfiles = $files;
        $indfile = array_shift($tmpfiles);
        unset($tmpfiles);
    }

    $firstfile = array_shift($files);
    $firstfile = file_exists($firstfile) ? $firstfile : UPLOAD_DIR.$firstfile;

    WPDM()->downloadHistory->add($package['ID'], $indfile ? $indfile : $firstfile, wpdm_query_var('oid'));

    //URL Download
    if ($indfile != '' && strpos($indfile, '://')) {

        if (!isset($package['url_protect']) || $package['url_protect'] == 0) {
            $indfile = wpdm_escs(htmlspecialchars_decode($indfile));
            header('location: ' . $indfile);

        } else {
            $r_filename = wpdm_basename($indfile);
            $r_filename = explode("?", $r_filename);
            $r_filename = $r_filename[0];
            wpdm_download_file($indfile, $r_filename, $speed, 1, $package);

        }

        die();
    }


    /*$tmp = explode("wp-content", $indfile);
    $tmp = end($tmp);
    if ($indfile != '' && file_exists(UPLOAD_DIR . $indfile))
        $filepath = UPLOAD_DIR . $indfile;
    else if ($indfile != '' && file_exists($user_upload_dir.$indfile))
        $filepath = $user_upload_dir.$indfile;
    else if ($indfile != '' && file_exists($indfile))
        $filepath = $indfile;
    else if ($indfile != '' && file_exists(WP_CONTENT_DIR . $tmp)) //path fix on site move
        $filepath = WP_CONTENT_DIR . $tmp;
    else if ($indfile != '' && file_exists($_old_up_dir . $indfile)) //path fix on site move
        $filepath = $_old_up_dir . $indfile;
    else {
        $filepath = $firstfile;
    }*/

    $filepath = WPDM()->fileSystem->absPath($indfile, $package['ID']);
    if(!$filepath)
        WPDM_Messages::fullPage('Error!', "<div class='card bg-danger text-white p-4'>" . __("Sorry! File now found!", "download-manager") . "</div>", 'error');
    //$plock = get_wpdm_meta($file['id'],'password_lock',true);
    //$fileinfo = get_wpdm_meta($package['id'],'fileinfo');

    $filename = wpdm_basename($filepath);
    $filename = preg_replace("/([0-9]+)[wpdm]+_/", "", $filename);

    wpdm_download_file($filepath, $filename, $speed, 1, $package);
    //@unlink($filepath);

}

\WPDM\TempStorage::kill("download.".wpdm_get_client_ip());

do_action("after_download", $package);

die();

