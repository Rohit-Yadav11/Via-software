<?php

namespace WPDM\admin\menus;


use Kunnu\Dropbox\Models\TemporaryLink;
use WPDM\libs\FileSystem;
use WPDM\Package;
use WPDM\TempStorage;

class ImportExport
{

    function __construct()
    {
        add_action("wp_ajax_wpdm_upload_csv_file", array($this, 'uploadCSV'));
        add_action("wp_ajax_wpdm_import_csv_file", array($this, 'import'));
        add_action("wp_ajax_wpdm_export_packages", array($this, 'export'));
        add_action("admin_menu", array($this, 'menu'));
        add_action("wp_ajax_wpdm_dimport", array($this, 'importDirFile'));

    }

    function menu()
    {
        add_submenu_page('edit.php?post_type=wpdmpro', __( "Import/Export &lsaquo; Download Manager" , "download-manager" ), __( "Import & Export" , "download-manager" ), WPDM_MENU_ACCESS_CAP, 'importable-files', array($this, 'UI'));
    }

    public static function UI(){

        if (isset($_POST['wpdm_importdir'])) update_option('wpdm_importdir', $_POST['wpdm_importdir']);
        $root = current_user_can('manage_options') ? get_option('_wpdm_file_browser_root', ABSPATH) : UPLOAD_DIR;
        $path = $root.get_option('wpdm_importdir', false);
        $scan = @scandir($path);
        $k = 0;
        if ($scan) {
            foreach ($scan as $v) {
                if ($v == '.' or $v == '..' or @is_dir(get_option('wpdm_importdir') . $v)) continue;

                $fileinfo[$k]['file'] = get_option('wpdm_importdir') . $v;
                $fileinfo[$k]['name'] = $v;
                $k++;
            }
        }

        include(WPDM_BASE_DIR . 'admin/tpls/import.php');


    }


    function uploadCSV(){
        if (!wp_verify_nonce(wpdm_query_var('_csv_nonce'), NONCE_KEY) || !current_user_can(WPDM_ADMIN_CAP)) die('-1');
        $source_file = $_FILES['csv_file']['tmp_name'];
        $csv_file = WPDM_CACHE_DIR.'csv-import-'.uniqid().'.csv';
        move_uploaded_file($source_file, $csv_file);
        TempStorage::set("csv_import_file", $csv_file);
        TempStorage::set("csv_current_row", 0);
        $fp = fopen($csv_file,"r");
        $entries = -1;
        if($fp){
            while(!feof($fp)){
                $content = fgetcsv($fp);
                if($content)    $entries++;
            }
        }
        fclose($fp);
        TempStorage::set("csv_total_rows", $entries);
        wp_send_json(array('csv' => $csv_file, 'entries' => $entries));
    }


    /**
     * @usage Import CSV File
     */
    function import()
    {
        global $wpdb;

        if (!wp_verify_nonce(wpdm_query_var('_csvimport_nonce'), NONCE_KEY) || !current_user_can(WPDM_ADMIN_CAP)) die('-1');

        if (! ini_get("auto_detect_line_endings")) {
            ini_set("auto_detect_line_endings", '1');
        }

        wpdm_check_license();
        $max_line_length = 10000;
        $source_file = $_FILES['csv']['tmp_name'];
        $csv_file = TempStorage::get('csv_import_file');
        $current_row = (int)TempStorage::get('csv_current_row');
        $total_rows = (int)TempStorage::get('csv_total_rows');

        //TempStorage::set("csv_current_row", 0);
        $import_limit = 10;
        $imported = 0;
        $lineno = 0;
        $fpointer = fopen($csv_file, "r");
        if($fpointer !== FALSE){
            while (($row = fgetcsv($fpointer, 1000, ",")) !== FALSE) {
                $lineno++;

                if($lineno == 1){
                    $columns = $row;
                    continue;
                }

                //Skip already imported data
                if($lineno <= $current_row + 1) continue;

                $current_row++;
                $imported++;

                //Import row
                while (count($row) < count($columns)) {
                    array_push($row, NULL);
                }

                if(count($row) !== count($columns)){
                    continue;
                }

                //$values = quote_all_array($row);
                $data_row = array_combine($columns, $row);
                $this->importRow($data_row);

                //Terminate current process after limit reached
                if($imported >= $import_limit){
                    break;
                }


            }
            fclose($fpointer);
        }

        TempStorage::set('csv_current_row', $current_row);
        $progress = ($current_row/$total_rows)*100;
        $continue = $total_rows <= $current_row ? false : true;

        wp_send_json(array('continue' => $continue, 'progress' => (int)$progress, 'imported' => $current_row));

        die();

    }

    /**
     * Parse & Import a CSV row
     * @param $csv_row
     */
    function importRow($csv_row){
        global $wpdb;
        if (isset($csv_row['url_key']))
            unset($csv_row['url_key']);

        $csv_row['category'] = isset($csv_row['category']) ? explode(',', $csv_row['category']) : array();
        $csv_row['create_date'] = isset($csv_row['create_date']) ?  wp_date("Y-m-d H:i:s", strtotime($csv_row['create_date'])) : wp_date("Y-m-d H:i:s",time());
        $csv_row['update_date'] = isset($csv_row['update_date']) ? wp_date("Y-m-d H:i:s", strtotime($csv_row['update_date'])) : wp_date("Y-m-d H:i:s",time());
        $access = explode(",", $csv_row['access']);
        $csv_row['access'] = isset($csv_row['access']) && $csv_row['access'] != '' ? $access : ( !isset($csv_row['ID']) ? array('guest') : null);

        if($csv_row['access'] === null) unset($csv_row['access']);

        $postdata = array();

        if(isset($csv_row['title']))
            $postdata['post_title'] = utf8_encode(htmlentities($csv_row['title']));

        if(isset($csv_row['description']))
            $postdata['post_content'] = utf8_encode(htmlentities($csv_row['description']));

        if(!isset($csv_row['ID'])){
            $postdata['post_date'] = $csv_row['create_date'];
            $postdata['post_modified'] = $csv_row['update_date'];
            $postdata['filter'] = false;
        }

        if(isset($csv_row['ID']) && (int)$csv_row['ID'] > 0){
            $postdata['ID'] = (int)$csv_row['ID'];
            unset($csv_row['ID']);
        }

        $postdata['post_type'] = 'wpdmpro';
        if(!isset($postdata['ID']) && !isset($postdata['post_status']))
            $postdata['post_status'] = 'publish';

        $post_id = wp_insert_post($postdata);
        if(isset($postdata['ID']))
            $post_id = $postdata['ID'];

        $wpdb->update($wpdb->posts, array('post_modified' => $csv_row['update_date']), array('ID' => $post_id));
        unset($csv_row['update_date']);

        foreach($csv_row['category'] as $index => $term){
            if((int)$term > 0) $term = (int)$term;
            if(term_exists($term, 'wpdmcategory')){
                $eterm = term_exists($term, 'wpdmcategory');
                $csv_row['category'][$index] = $eterm['term_id'];
            }
            else {
                $tinf =  wp_insert_term($term, 'wpdmcategory');
                if(is_array($tinf) && isset($tinf['term_id']))
                    $csv_row['category'][$index] = $tinf['term_id'];

            }
        }

        if(count($csv_row['category']) > 0)
            $ret = wp_set_post_terms($post_id, $csv_row['category'], 'wpdmcategory' );

        unset($csv_row['category']);

        if (isset($csv_row['title']))
            unset($csv_row['title']);
        if (isset($csv_row['description']))
            unset($csv_row['description']);
        if (isset($csv_row['create_date']))
            unset($csv_row['create_date']);

        if(isset($csv_row['additional_previews']) && $csv_row['additional_previews']!='') {
            $csv_row['additional_previews'] = explode(",", $csv_row['additional_previews']);
        }

        if(isset($csv_row['preview']) && $csv_row['preview']!='') {
            $mime_type = '';
            $wp_filetype = wp_check_filetype(basename($csv_row['preview']), null);
            if (isset($wp_filetype['type']) && $wp_filetype['type'])
                $mime_type = $wp_filetype['type'];
            unset($wp_filetype);
            $attachment = array(
                'post_mime_type' => $mime_type,
                'post_parent' => $post_id,
                'post_title' => basename($csv_row['preview']),
                'post_status' => 'inherit'
            );
            $attachment_id = wp_insert_attachment($attachment, $csv_row['preview'], $post_id);
            unset($attachment);

            if (!is_wp_error($attachment_id)) {
                $attachment_data = wp_generate_attachment_metadata($attachment_id, $csv_row['preview']);
                wp_update_attachment_metadata($attachment_id, $attachment_data);
                unset($attachment_data);
                set_post_thumbnail($post_id, $attachment_id);
            }
            unset($csv_row['preview']);
        }

        //Process custom fields with email lock option
        if(isset($csv_row['custom_form_field'])){
            $value = $csv_row['custom_form_field'];
            $value = explode(",", $value);
            $varr = array();
            foreach ($value as $v){
                $v = explode("=", $v);
                $varr[$v[0]] = isset($v[1])?$v[1]:'';
            }
            $csv_row['custom_form_field'] = $varr;
        }

        //Handle files and file infos
        if(isset($csv_row['files']) && $csv_row['files'] !== '') {
            $files = maybe_unserialize($csv_row['files']);
            $csv_row['files'] = is_array($files) ? $files : explode(',', $csv_row['files']);
        }
        if(isset($csv_row['files']) && $csv_row['files'] === '')
            unset($csv_row['files']);

        $files = isset($csv_row['files']) ? $csv_row['files'] : maybe_unserialize(get_post_meta($post_id, '__wpdm_files', true));


        if(isset($csv_row['fileinfo']) && is_array(maybe_unserialize($csv_row['fileinfo']))){
            $csv_row['fileinfo'] = maybe_unserialize($csv_row['fileinfo']);
            if(isset($csv_row['fileinfo'][0]) && is_array(maybe_unserialize($csv_row['fileinfo'][0])))
                $csv_row['fileinfo'] = maybe_unserialize($csv_row['fileinfo'][0]);
        } else {
            $file_titles = (isset($csv_row['file_titles'])) ? explode(",", $csv_row['file_titles']) : array();
            $file_passwords = isset($csv_row['file_passwords']) ? explode(",", $csv_row['file_passwords']) : array();
            $file_prices = isset($csv_row['file_prices']) ? explode(",", $csv_row['file_prices']) : array();
            if (count($file_titles) > 0) {
                foreach ($files as $index => $file) {
                    $file_title = is_array($file_titles) && count($file_titles) > 0 ? array_shift($file_titles) : '';
                    $csv_row['fileinfo'][$index]['title'] = $file_title; //array('title' => $file_title, 'password' => $file_passwords[$index], 'price' => $file_prices[$index]);
                }
            }
            if (count($file_passwords) > 0) {
                foreach ($files as $index => $file) {
                    $file_password = is_array($file_passwords) && count($file_passwords) > 0 ? array_shift($file_passwords) : '';
                    $csv_row['fileinfo'][$index]['password'] = $file_password;
                }
            }
            if (count($file_prices) > 0) {
                foreach ($files as $index => $file) {
                    $file_price = is_array($file_prices) && count($file_prices) > 0 ? array_shift($file_prices) : '';
                    $csv_row['fileinfo'][$index]['price'] = $file_price;
                }
            }
        }

        //Update all meta data
        foreach ($csv_row as $meta_key => $value) {
            $value = apply_filters("wpdm_csv_import/{$meta_key}", $value, $post_id);
            update_post_meta($post_id, "__wpdm_".$meta_key, wpdm_sanitize_var($value));
        }

        wp_set_post_tags( $post_id, $csv_row['tags'], true );
        if(!isset($csv_row['masterkey']))
            update_post_meta( $post_id, '__wpdm_masterkey', uniqid() );

        do_action('after_import_package', $post_id, $csv_row);
    }

    /**
     * Import files from a given directory path
     */
    function importDirFile()
    {
        global $wpdb;
        if(!current_user_can(WPDM_ADMIN_CAP)) die('Error!');
        //array_shift($flds);
        $fileinf = array();
        $file_id = uniqid();
        //$files = array($file_id => get_option('wpdm_importdir') . $_POST['fname']);
        $fileinf['access'] = $_POST['access'];
        if (isset($_POST['password']) && $_POST['password'] != '') {
            $fileinf['password_lock'] = 1;
            $fileinf['password'] = $_POST['password'];

        }
        $fileinf['files'] = [ $file_id => wpdm_query_var('filepath', 'serverpath') ];
        $post_id = wp_insert_post(array(
            'post_title' => esc_attr($_POST['title']),
            'post_content' => esc_attr($_POST['description']),
            'post_type' => 'wpdmpro',
            'post_status' => 'publish'
        ));
        wp_set_post_terms($post_id, $_POST['category'], 'wpdmcategory');
        foreach ($fileinf as $meta_key => $value) {
            update_post_meta($post_id, "__wpdm_" . $meta_key, $value);
        }



        do_action('after_import_package', $post_id, $fileinf);
        //@unlink(dirname(__FILE__).'/imports/'.$_POST['fname']);
        die('Done!');
    }

    function export(){
        if (!wp_verify_nonce(wpdm_query_var('_csvexport_nonce'), NONCE_KEY) || !current_user_can(WPDM_ADMIN_CAP)) die('-1');

        $package_data_core = array(
            'ID'                    => '',
            'post_title'            => '',
            //'post_content'          => '',
            //'post_excerpt'          => '',
            'post_status'           => 'publish',
            'comment_status'        => 'open',
            'post_name'             => '',
            'post_type'             => 'wpdmpro',
            'post_author'           => get_current_user_id(),
            'ping_status'           => get_option('default_ping_status'),
            'post_parent'           => 0,
            'post_date'             => '',
            'post_modified'         => '',
            'comment_count'         => 0
        );

        $package_data_meta = array(
            'files'                         => array(),
            'fileinfo'                      => array(),
            'file_titles'                   => array(),
            'file_passwords'                => array(),
            'file_prices'                   => array(),
            'package_dir'                   => '',
            'link_label'                    => __( "Download" , "download-manager" ),
            'download_count'                => 0,
            'view_count'                    => 0,
            'version'                       => '1.0.0',
            'stock'                         => 0,
            'package_size'                  => 0,
            'package_size_b'                => 0,
            'access'                        => '',
            'individual_file_download'      =>  -1,
            'cache_zip'                     =>  -1,
            'template'                      => 'link-template-panel.php',
            'page_template'                 => 'page-template-1col-flat.php',
            'password_lock'                 => '0',
            'facebook_lock'                 => '0',
            'gplusone_lock'                 => '0',
            'linkedin_lock'                 => '0',
            'linkedin_message'              => '',
            'linkedin_url'                  => '',
            'tweet_lock'                    => '0',
            'tweet_message'                 => '',
            'email_lock'                    => '0',
            'email_lock_title'              => '',
            'email_lock_idl'                => '',
            'email_lock_msg'                => '',
            'terms_title'                   => '',
            'terms_conditions'              => '',
            'terms_check_label'             => '',
            'icon'                          => '',
            'import_id'                     => 0,
            'password_usage_limit'          => 0,
            'gc_scopes_contacts'            => 0,
            'twitter_handle'                => '',
            'facebook_like'                 => '',
            'base_price'                    => '',
            'sales_price'                   => '',
            'sales_price_expire'            => '',
            'pay_as_you_want'               => '',
            'download_limit_per_user'       => '',
            'discount'                      => array(),
        );

        $items_per_page = 10;
        if(wpdm_query_var('_key') == ''){
            $key = uniqid();
            $export_file = WPDM_CACHE_DIR."wpdm-export-{$key}.".wpdm_query_var('format');
            $export['file'] = $export_file;
            $export['start'] = 0;
            $packs = wp_count_posts('wpdmpro');
            $packs = (array)$packs;
            $export['total'] = array_sum($packs);


        } else {
            $key = wpdm_query_var('_key');
            $export = TempStorage::get("export_{$key}");
            $export_file = $export['file'];
        }

        $packs = get_posts(array('post_type' => 'wpdmpro','orderby' => 'ID', 'order' => 'DESC' , 'posts_per_page' => $items_per_page, 'offset' => $export['start']));
        $pack = (array)$packs[0];

        if(strstr($export_file, ".csv")) {
            $file = fopen($export_file, 'a');
            //Add headers
            if ((int)$export['start'] === 0) {
                $heads = array_merge(array_keys($package_data_core), array_keys($package_data_meta));
                $heads = implode(",", $heads);
                fputs($file, $heads . "\r\n");
            }

            foreach ($packs as $package) {
                $csv_row = $this->csvRow($package, $package_data_core, $package_data_meta);
                fputs($file, $csv_row . "\r\n");
            }
            fclose($file);
        }

        if(strstr($export_file, ".json")) {
            $file = fopen($export_file, 'a');
            //Add headers
            if ((int)$export['start'] === 0) {
                fputs($file,  "[\r\n");
            }

            foreach ($packs as $package) {
                $array_pack = $this->arrayPack($package, $package_data_core, $package_data_meta);
                fputs($file, json_encode($array_pack) . ",\r\n");
            }
            fclose($file);
        }

        if(strstr($export_file, ".xml")) {
            $file = fopen($export_file, 'a');
            //Add headers
            if ((int)$export['start'] === 0) {
                fputs($file,  "<?xml version=\"1.0\"?>\r\n<root>\r\n<package>");
            }

            foreach ($packs as $package) {
                $array_pack = $this->arrayPack($package, $package_data_core, $package_data_meta);
                $xml = $this->arrayToXML($array_pack);
                fputs($file, $xml . "</package>\r\n<package>");
            }
            fclose($file);
        }

        $continue = true;
        $exported = $export['start'] + $items_per_page;
        $export['start'] = $exported;
        $progress = ($exported/$export['total'])*100;


        TempStorage::set("export_{$key}", $export);

        $response = array('key' => $key, 'continue' => $continue,'entries' => $export['total'], 'progress' => (int)$progress, 'exported' => $exported, 'file' => $export_file);

        if($exported >= $export['total']) {
            $progress = 100;
            $exported = $export['total'];
            $response['continue'] = false;
            if(strstr($export_file, ".json")) {
                $file = fopen($export_file, 'a');
                fputs($file, "{}]");
                fclose($file);
            }
            if(strstr($export_file, ".xml")) {
                $file = fopen($export_file, 'a');
                fputs($file, "</package>\r\n</root>");
                fclose($file);
            }
            $response['exportfile'] = FileSystem::instantDownloadURL($export_file);
            TempStorage::kill("export_{$key}");
        }

        wp_send_json($response);
    }

    function csvRow($package, $core_columns, $meta_columns){
        $package = (array)$package;
        $meta = get_post_meta($package['ID']);

        foreach ($core_columns as $key => &$value){
            $value = $package[$key];
        }

        //Update default meta values from database
        foreach($meta_columns as $meta_key => &$meta_value){
            if($meta_key === 'file_titles'){
                $fileinfo = maybe_unserialize($meta['__wpdm_fileinfo'][0]);

                $titles = array();
                foreach ($fileinfo as $fileid => $info){
                    $titles[] = ($info['title']);
                }
                $meta_value = implode(",", $titles);
            }
            else if($meta_key === 'file_passwords'){
                $fileinfo = maybe_unserialize($meta['__wpdm_fileinfo'][0]);
                $passwords = array();
                foreach ($fileinfo as $fileid => $info){
                    $passwords[] = $info['password'];
                }
                $passwords = array_unique($passwords);
                $meta_value = implode(",", $passwords);
            }
            else if($meta_key === 'file_prices'){
                $fileinfo = maybe_unserialize($meta['__wpdm_fileinfo'][0]);
                $prices = array();
                foreach ($fileinfo as $fileid => $info){
                    $prices[] = ( isset($info['price']) ? $info['price'] : 0 );
                }
                $prices = array_unique($prices);
                $meta_value = implode(",", $prices);
            }
            else {
                $meta_key = "__wpdm_{$meta_key}";
                if (array_key_exists($meta_key, $meta) && $meta_key !== '__wpdm_fileinfo') {
                    $meta_value = maybe_unserialize($meta[$meta_key][0]);
                    if (is_array($meta_value)) $meta_value = implode(",", $meta_value);
                }
            }
        }

        $all_data = $core_columns + $meta_columns;
        $all_data = quote_all_array($all_data);
        return implode(",", $all_data);

    }

    function arrayPack($package, $core_columns, $meta_columns){
        $package = (array)$package;
        $meta = get_post_meta($package['ID']);
        unset($meta_columns['file_titles'], $meta_columns['file_passwords'], $meta_columns['file_prices']);
        foreach ($core_columns as $key => &$value){
            $value = $package[$key];
        }

        foreach($meta_columns as $meta_key => &$meta_value){
            $meta_key = "__wpdm_{$meta_key}";
            if (array_key_exists($meta_key, $meta)) {
                $meta_value = maybe_unserialize($meta[$meta_key][0]);
            }
        }
        $all_data = $core_columns + $meta_columns;
        return $all_data;

    }

    function arrayToXML($array){
        $xml = '';
        foreach ($array as $key => $value){
            if(preg_match('/^\d/', $key) === 1) $key = "wpdmxt_{$key}";
            $key = str_replace(":", "_cx_", $key);
            $xml .= "<{$key}>";
            $xml .= is_array($value) ? "\r\n". $this->arrayToXML($value) ."\r\n" : '<![CDATA['.$value.']]>';
            $xml .= "</{$key}>\r\n";
        }
        return $xml;
    }

}

