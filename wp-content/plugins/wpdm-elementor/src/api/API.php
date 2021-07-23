<?php

namespace WPDM\Elementor\API;



class API
{

    public static function getInstance()
    {
        static $instance;
        if (is_null($instance)) {
            $instance = new self;
        }
        return $instance;
    }

    private function __construct()
    {
        add_action('rest_api_init', array($this, 'registerAPIEndpoints'));
    }

    public function registerAPIEndpoints()
    {
        register_rest_route('wpdm-elementor/v1', '/search-packages', [
            'methods' => 'GET',
            'callback' => [$this, 'searchPackages'],
            'permission_callback' => function () {
                return true;
            }
        ]);
    }


    public function searchPackages()
    {
        global $wpdb;
        $posts_table = "{$wpdb->prefix}posts";
        $packages = [];
        $term = wpdm_query_var('term', ['validate' => 'txt', 'default' => null]);

        if ($term) {
            $result_rows = $wpdb->get_results("SELECT ID, post_title FROM $posts_table where `post_type` = 'wpdmpro' AND `post_title` LIKE  '%" . $term . "%' ");
            foreach ($result_rows as $row) {
                array_push($packages, [
                    'id' => $row->ID,
                    'text' => $row->post_title
                ]);
            }
        }
        //results key is necessary for jquery select2 
        wp_send_json(["results" => $packages]);
    }
}
