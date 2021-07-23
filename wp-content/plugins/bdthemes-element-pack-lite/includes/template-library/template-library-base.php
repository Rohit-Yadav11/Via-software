<?php

namespace ElementPack\Includes\TemplateLibrary;

class ElementPack_Template_Library_Base
{
    protected $table_cat;
    protected $table_post;
    protected $table_cat_post;
    protected $charset_collate;
    protected $wpdb;

    protected $api_url = 'https://elementpack.pro/wp-json/template-manager/v1/';

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->charset_collate = $this->wpdb->get_charset_collate();
        $this->table_cat = $this->wpdb->prefix . 'ep_template_library_cat';
        $this->table_post = $this->wpdb->prefix . 'ep_template_library_post';
        $this->table_cat_post = $this->wpdb->prefix . 'ep_template_library_cat_post';
    }


    public function createTemplateTables()
    {

        $charset_collate = $this->charset_collate;
        $table_cat_name = $this->table_cat;
        $table_post_name = $this->table_post;
        $table_cat_post_name = $this->table_cat_post;


        $catsql = "CREATE TABLE IF NOT EXISTS $table_cat_name (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `term_id` mediumint(9) NOT NULL,
            `name` varchar(191) default NULL,
            `slug` varchar(191) default NULL,
            `description` text default NULL,
            `total` mediumint(9) default NULL,
            `image_url` varchar(191) default NULL,
            UNIQUE KEY id (id),
            UNIQUE (slug)
        ) $charset_collate;";

        $catPostsql = "CREATE TABLE IF NOT EXISTS $table_cat_post_name (
            `term_id` bigint(20) UNSIGNED NOT NULL,
            `demo_id` bigint(20) UNSIGNED NOT NULL
        ) $charset_collate;";

        $postsql = "CREATE TABLE IF NOT EXISTS $table_post_name (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `demo_id` bigint(20) UNSIGNED NOT NULL,
            `date` date default NULL,
            `title` varchar(191) default NULL,
            `short_desc` text default NULL,
            `is_pro` int(2) default NULL,
            `thumbnail` varchar(191) default NULL,
            `demo_url` varchar(191) default NULL,
            `json_url` varchar(191) default NULL,
            UNIQUE KEY id (id),
            UNIQUE (demo_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($catsql);
        dbDelta($postsql);
        dbDelta($catPostsql);
        $this->setTemplateDataToDB();
    }

    public function setTemplateDataToDB()
    {

        $demoData = $this->remote_get_demo_data();
        if ($demoData) {
            $this->wpdb->query('TRUNCATE ' . $this->table_cat);
            $this->wpdb->query('TRUNCATE ' . $this->table_post);
            $this->wpdb->query('TRUNCATE ' . $this->table_cat_post);

            $prefixCat = "INSERT INTO `" . $this->table_cat . "` (`term_id`, `name`, `slug`, `description`, `total`, `image_url`) VALUES ";
            $CatQueryString = [];

            $prefixPost = "INSERT INTO `" . $this->table_post . "` (`demo_id`, `date`, `title`, `short_desc`, `is_pro`, `thumbnail`, `demo_url`, `json_url`) VALUES ";
            $PostQueryString = [];

            $prefixPostCat = "INSERT INTO `" . $this->table_cat_post . "` (`term_id`, `demo_id`) VALUES ";
            $PostCatQueryString = [];

            foreach ($demoData as $demo) {
                $Catstring = [$demo['term_id'], $demo['name'], $demo['slug'],$demo['description'],$demo['total'],$demo['image_url']];
                $Catstring = '("' . implode('", "', $Catstring) . '")';
                array_push($CatQueryString, $Catstring);

                if(isset($demo['data']) && is_array($demo['data'])){
                    $postData = $demo['data'];
                    foreach($postData as $post){
                        $Poststring = [$post['demo_id'], $post['date'], $post['title'],$post['short_desc'],$post['is_pro'],$post['thumbnail'],$post['demo_url'],$post['json_url']];
                        $PostQueryString[$post['demo_id']] = '("' . implode('", "', $Poststring) . '")';

                        $PostCatstring = [$demo['term_id'], $post['demo_id']];
                        $PostCatQueryString[] = '(' . implode(',', $PostCatstring) . ')';
                    }
                }
            }

            $wpdbError = false;

            $query = $prefixCat . implode(',', $CatQueryString);
            $this->wpdb->query($query);
            if($this->wpdb->last_error){
                $wpdbError = true;
            }

            $PostQueryString = array_chunk($PostQueryString, 100, true);
            foreach ($PostQueryString as $chunk) {
                $postQuery = $prefixPost . implode(',', $chunk);
                $this->wpdb->query($postQuery);
            }

            if($this->wpdb->last_error){
                $wpdbError = true;
            }

            $PostCatQueryString = array_chunk($PostCatQueryString, 100, true);
            foreach ($PostCatQueryString as $chunk) {
                $postCatQuery = $prefixPostCat . implode(',', $chunk);
                $this->wpdb->query($postCatQuery);
            }

            if($this->wpdb->last_error){
                $wpdbError = true;
            }

            if(!$wpdbError){
                set_transient( $this->get_transient_key(), 1, DAY_IN_SECONDS * 30 );
            }

        }
    }


    public function get_transient_key(){
        return 'ep_elements_demo_import_table_data_' . BDTEP_VER;
    }

    public function checkDemoData() {


        $result = $this->wpdb->get_row("SHOW TABLES LIKE '".$this->table_cat."'", ARRAY_A);
        $tableExists = false;
        if (is_array($result)) {
            if(count($result) == 1) {
                $tableExists = true;
            }
        }

        $demoData = get_transient( $this->get_transient_key() );

        if ( ! $demoData || !$tableExists) {
            $this->createTemplateTables();
        }
    }

    /**
     * @return array|mixed
     * retrieve element pack categories from remote server with api route
     */
    public function remote_get_demo_data()
    {
        $final_url = $this->api_url . 'data/';
        $response = wp_remote_get($final_url, ['timeout' => 60, 'sslverify' => false]);
        $body = wp_remote_retrieve_body($response);
        $body = json_decode($body, true);

        return $body;
    }
}