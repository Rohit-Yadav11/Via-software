<?php
/**
 * User: shahnuralam
 * Date: 2019-01-12
 * Time: 18:39
 */

namespace WPDM\libs;


class PageTemplate {

    function __construct()
    {
        add_filter( 'template_include', array($this, 'cleanTemplate'), 99 );
    }

    function cleanTemplate( $template ) {
        global $post;
        if(!$post || !is_singular('page')) return $template;

        $new_template = wpdm_tpl_path('clean-template.php');
        $new_adb_template = wpdm_tpl_path('author-dashboard-blank.php');
        if ( $post->ID == get_option('__wpdm_login_url', 0) && !is_user_logged_in() && (int)get_option('__wpdm_clean_login', 0) === 1) {
            return $new_template;
        }

        if ( $post->ID == get_option('__wpdm_user_dashboard', 0) && !is_user_logged_in() && (int)get_option('__wpdm_clean_login', 0) === 1) {
            return $new_template;
        }

        if ( $post->ID == get_option('__wpdm_author_dashboard', 0) && !is_user_logged_in() && (int)get_option('__wpdm_clean_login', 0) === 1) {
            return $new_template;
        }

        if ( $post->ID == get_option('__wpdm_author_dashboard', 0) && is_user_logged_in() && (int)get_option('__wpdm_clean_login', 0) === 1) {
            //return $new_adb_template;
        }

        if ( $post->ID == get_option('__wpdm_register_url', 0) && (int)get_option('__wpdm_clean_signup', 0) === 1) {
            return $new_template;
        }

        return $template;
    }

}
