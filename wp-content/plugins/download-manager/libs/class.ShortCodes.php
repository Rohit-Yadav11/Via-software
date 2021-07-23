<?php

namespace WPDM;

use WPDM\libs\Crypt;

static $shortcode_count = array();

class ShortCodes
{

    function __construct()
    {


        // Total Package Count
        add_shortcode('wpdm_package_count', array($this, 'totalPackages'));

        // Total Package Count
        add_shortcode('wpdm_download_count', array($this, 'totalDownloads'));

        // Login Form
        add_shortcode('wpdm_login_form', array($this, 'loginForm'));

        // Login Modal Form
        add_shortcode('wpdm_modal_login_form', array($this, 'modalLoginFormBtn'));

        // Logout URL
        add_shortcode('wpdm_logout_url', array($this, 'logoutURL'));

        // Register form
        add_shortcode('wpdm_reg_form', array($this, 'registerForm'));

        // Edit Profile
        //add_shortcode('wpdm_edit_profile', array($this, 'EditProfile'));

        // Show all packages
        add_shortcode('wpdm_packages', array($this, 'packages'));

        // Show a package by id
        add_shortcode('wpdm_package', array($this, 'package'));

        // Show a category link
        add_shortcode('wpdm_category_link', array($this, 'categoryLink'));

        //Fetch packages from the given categories
        add_shortcode("wpdm_category", array($this, 'category'));

        // Generate direct download link
        add_shortcode('wpdm_direct_link', array($this, 'directLink'));

        // Show all packages in a responsive table
        add_shortcode('wpdm_all_packages', array($this, 'allPackages'));
        add_shortcode('wpdm-all-packages', array($this, 'allPackages'));

        //Packages by tag
        add_shortcode("wpdm_tag", array($this, 'packagesByTag'));

        //Search downloads
        add_shortcode('wpdm_search_result', array($this, 'searchResult'));

        //All Users
        add_shortcode('wpdm_authors', array($this, 'authors'));

        //User Favourites
        add_shortcode('wpdm_user_favourites', array($this, 'userFavourites'));

        //Email to download
        add_shortcode("wpdm_email_2download", array($this, 'emailToDownload'));

    }

    /**
     * @usage Short-code function for total download count
     * @param array $params
     * @return mixed
     */
    function totalDownloads($params = array())
    {
        global $wpdb;
        $download_count = $wpdb->get_var("select sum(meta_value) from {$wpdb->prefix}postmeta where meta_key='__wpdm_download_count'");
        return $download_count;
    }

    /**
     * @usage Short-code function for total package count
     * @param array $params
     * @return mixed
     */
    function totalPackages($params = array())
    {
        if (isset($params['cat'])) {
            $term = get_term_by("slug", $params['cat']);
            if (is_object($term) && isset($term->count)) return $term->count;
            return 0;
        }
        if (isset($params['author'])) {
            $user_post_count = count_user_posts($params['author'], 'wpdmpro');
            return $user_post_count;
        }
        $count_posts = wp_count_posts('wpdmpro');
        $status = isset($params['status']) ? $params['status'] : 'publish';
        if ($status == 'draft') return $count_posts->draft;
        if ($status == 'pending') return $count_posts->pending;
        return $count_posts->publish;
    }

    /**
     * @usage Short-code callback function for login form
     * @return string
     */
    function loginForm($params = array())
    {

        global $current_user;

        if (!isset($params) || !is_array($params)) $params = array();

        if (isset($params) && is_array($params))
            extract($params);
        if (!isset($redirect)) $redirect = get_permalink(get_option('__wpdm_user_dashboard'));

        if (!isset($regurl)) {
            $regurl = get_option('__wpdm_register_url');
            if ($regurl > 0)
                $regurl = get_permalink($regurl);
        }
        $log_redirect = $_SERVER['REQUEST_URI'];
        if (isset($params['redirect'])) $log_redirect = esc_url($params['redirect']);
        if (isset($_GET['redirect_to'])) $log_redirect = esc_url($_GET['redirect_to']);

        $up = parse_url($log_redirect);
        if (isset($up['host']) && $up['host'] != $_SERVER['SERVER_NAME']) $log_redirect = $_SERVER['REQUEST_URI'];

        $log_redirect = strip_tags($log_redirect);

        if (!isset($params['logo'])) $params['logo'] = get_site_icon_url();

        $__wpdm_social_login = get_option('__wpdm_social_login');
        $__wpdm_social_login = is_array($__wpdm_social_login) ? $__wpdm_social_login : array();

        ob_start();

        if (is_user_logged_in())
            $template = wpdm_tpl_path("already-logged-in.php", WPDM_TPL_DIR, WPDM_TPL_FALLBACK);
        else {
            if (wpdm_query_var('action') === 'lostpassword')
                $template = wpdm_tpl_path('lost-password-form.php');
            else if (wpdm_query_var('action') === 'rp')
                $template = wpdm_tpl_path('reset-password-form.php');
            else
                $template = wpdm_tpl_path('wpdm-login-form.php');
        }

        include($template);

        $content = ob_get_clean();
        $content = apply_filters("wpdm_login_form_html", $content);

        return $content;
    }

    /**
     * @param array $params
     * @return false|string
     */
    function modalLoginFormBtn($params = array())
    {
        if ((int)get_option('__wpdm_modal_login', 0) !== 1) return "";
        $defaults = array('class' => '', 'redirect' => '', 'label' => __('Login', 'download-manager'), 'id' => 'wpdmmodalloginbtn');
        $params = shortcode_atts($defaults, $params, 'wpdm_modal_login_form');
        $redirect = isset($params['redirect']) && $params['redirect'] !== '' ? "data-redirect='{$params['redirect']}'" : '';
        ob_start();
        ?>
        <div class="w3eden d-inline-block"><a href="#" <?php echo $redirect; ?> type="button"
                                              id="<?php echo $params['id']; ?>" class="<?php echo $params['class']; ?>"
                                              data-toggle="modal"
                                              data-target="#wpdmloginmodal"><?php echo $params['label']; ?></a></div>
        <?php
        $btncode = ob_get_clean();
        return $btncode;
    }

    /**
     * @usage Short-code callback function for logout url
     * @param $params
     * @return string|void
     */
    function logoutURL($params)
    {
        $redirect = isset($params['r']) ? $params['r'] : '';
        return wpdm_logout_url($redirect);
    }


    /**
     * @usage Edit profile
     * @return string
     */
    public function editProfile()
    {
        global $wpdb, $current_user, $wp_query;
        //wp_reset_query();
        wp_reset_postdata();
        $currentAccess = maybe_unserialize(get_option('__wpdm_front_end_access', array()));

        if (!array_intersect($currentAccess, $current_user->roles) && is_user_logged_in())
            return \WPDM_Messages::Error(wpautop(stripslashes(get_option('__wpdm_front_end_access_blocked'))), -1);


        $id = wpdm_query_var('ID');

        ob_start();
        echo "<div class='w3eden'>";
        if (is_user_logged_in()) {
            include(wpdm_tpl_path('wpdm-edit-user-profile.php'));
        } else {
            echo $this->loginForm();
        }
        echo "</div>";

        $data = ob_get_clean();
        return $data;
    }

    function registerForm($params = array())
    {

        if (!get_option('users_can_register')) return \WPDM_Messages::warning(__("User registration is disabled", "download-manager"), -1);

        if (!isset($params) || !is_array($params)) $params = array();

        ob_start();

        $_social_only = isset($params['social_only']) && ($params['social_only'] === 'true' || (int)$params['social_only'] === 1) ? true : false;
        $_verify_email = isset($params['verifyemail']) && ($params['verifyemail'] === 'true' || (int)$params['verifyemail'] === 1) ? true : false;
        $_show_captcha = !isset($params['captcha']) || ($params['captcha'] === 'true' || (int)$params['captcha'] === 1) ? true : false;
        $_auto_login = isset($params['autologin']) && ($params['autologin'] === 'true' || (int)$params['autologin'] === 1) ? true : false;


        $loginurl = wpdm_login_url();
        $reg_redirect = $loginurl;
        if (isset($params['autologin']) && (int)$params['autologin'] === 1) $reg_redirect = wpdm_user_dashboard_url();
        if (isset($params['redirect'])) $reg_redirect = esc_url($params['redirect']);
        if (isset($_GET['redirect_to'])) $reg_redirect = esc_url($_GET['redirect_to']);

        $force = uniqid();

        $up = parse_url($reg_redirect);
        if (isset($up['host']) && $up['host'] != $_SERVER['SERVER_NAME']) $reg_redirect = home_url('/');

        $reg_redirect = esc_attr(esc_url($reg_redirect));

        if (!isset($params['logo'])) $params['logo'] = get_site_icon_url();

        \WPDM\Session::set('__wpdm_reg_params', $params);

        $tmp_reg_info = \WPDM\Session::get('tmp_reg_info');

        $__wpdm_social_login = get_option('__wpdm_social_login');
        $__wpdm_social_login = is_array($__wpdm_social_login) ? $__wpdm_social_login : array();

        //Template
        include(wpdm_tpl_path('wpdm-reg-form.php'));

        $data = ob_get_clean();
        return $data;
    }

    /**
     * @param array $params
     * @return string
     */
    function packages($params = array('items_per_page' => 10, 'title' => false, 'desc' => false, 'order_by' => 'date', 'order' => 'DESC', 'paging' => false, 'page_numbers' => true, 'toolbar' => 1, 'template' => '', 'cols' => 3, 'colspad' => 2, 'colsphone' => 1, 'tags' => '', 'categories' => '', 'year' => '', 'month' => '', 's' => '', 'css_class' => 'wpdm_packages', 'scid' => '', 'async' => 1, 'tax' => '', 'terms' => ''))
    {
        global $current_user, $post;

        static $wpdm_packages = 0;

        if (isset($params['login']) && $params['login'] == 1 && !is_user_logged_in())
            return $this->loginForm($params);

        $wpdm_packages++;

        $scparams = $params;

        $defaults = array('author' => '', 'author_name' => '', 'items_per_page' => 10, 'title' => false, 'desc' => false, 'order_by' => 'date', 'order' => 'DESC', 'paging' => false, 'page_numbers' => true, 'toolbar' => 1, 'template' => 'link-template-panel', 'cols' => 3, 'colspad' => 2, 'colsphone' => 1, 'css_class' => 'wpdm_packages', 'scid' => 'wpdm_packages_' . $wpdm_packages, 'async' => 1, 'include_children' => 0);
        $params = shortcode_atts($defaults, $params, 'wpdm_packages');

        if (is_array($params))
            extract($params);

        if (!isset($items_per_page) || $items_per_page < 1) $items_per_page = 10;

        $cwd_class = "col-lg-" . (int)(12 / $cols);
        $cwdsm_class = "col-md-" . (int)(12 / $colspad);
        $cwdxs_class = "col-" . (int)(12 / $colsphone);

        if (isset($order_by) && !isset($order_field)) $order_field = $order_by;
        $order_field = isset($order_field) ? $order_field : 'date';
        $order_field = isset($_GET['orderby']) ? esc_attr($_GET['orderby']) : $order_field;
        $order = isset($order) ? $order : 'desc';
        $order = isset($_GET['order']) ? esc_attr($_GET['order']) : $order;
        $cp = wpdm_query_var('cp', 'num');
        if (!$cp) $cp = 1;

        $query = new Query();
        $query->items_per_page(wpdm_valueof($params, 'items_per_page', 10));
        $query->paged($cp);
        $query->sort($order_field, $order);

        foreach ($scparams as $key => $value) {
            if (method_exists($query, $key) && !in_array($key, ['categories'])) {
                $query->$key($value);
            }
        }

        if(wpdm_valueof($scparams, 'categories') !== '') {
            $categories = wpdm_valueof($scparams, 'categories');
            $operator = wpdm_valueof($scparams, 'operator', ['default' => 'IN']);
            $include_children = wpdm_valueof($scparams, 'include_children', ['default' => false]);
            $query->categories($categories, 'slug', $operator, $include_children);
        }

        if (wpdm_query_var('skw', 'txt') != '') {
            $query->s(wpdm_query_var('skw', 'txt'));
        }

        if(wpdm_valueof($scparams, 'tax') !== '') {
            $_terms = explode("|", wpdm_valueof($scparams, 'terms'));
            $taxos = explode("|", wpdm_valueof($scparams, 'tax'));
            foreach ($taxos as $index => $_taxo) {
                $terms = wpdm_valueof($_terms, $index);
                $terms = explode(",", $terms);
                if(count($terms) > 0) {

                    $query->taxonomy($_taxo, $terms);
                }
            }
        }

        $query->taxonomy_relation(wpdm_valueof($scparams, 'relation', ['default' => 'AND']));

        if (get_option('_wpdm_hide_all', 0) == 1) {
            $query->meta("__wpdm_access", '"guest"');

            if (is_user_logged_in()) {
                foreach ($current_user->roles as $role) {
                    $query->meta("__wpdm_access", $role);
                }
                $query->meta_relation('OR');
            }
        }

        if (isset($scparams['year']) || isset($scparams['month']) || isset($scparams['day'])) {

            if (isset($scparams['day'])) {
                $day = ($scparams['day'] == 'today') ? date('d') : $scparams['day'];
                $query->filter('day', $day);
            }

            if (isset($scparams['month'])) {
                $month = ($scparams['month'] == 'this') ? date('Ym') : $scparams['month'];
                $query->filter('m', $month);
            }

            if (isset($scparams['year'])) {
                $year = ($scparams['year'] == 'this') ? date('Y') : $scparams['year'];
                $query->filter('year', $year);
            }

            if (isset($scparams['week'])) {
                $week = ($scparams['week'] == 'this') ? date('W') : $scparams['week'];
                $query->filter('week', $week);
            }
        }
        $query->post_status('publish');
        $query->process();
        $total = $query->count;
        $packages = $query->packages();


        $pages = ceil($total / $items_per_page);
        $page = isset($_GET['cp']) ? (int)$_GET['cp'] : 1;
        $start = ($page - 1) * $items_per_page;


        $html = '';

        foreach ($packages as $pack){
            $pack = (array)$pack;
            $repeater = "<div class='{$cwd_class} {$cwdsm_class} {$cwdxs_class}'>" . \WPDM\Package::fetchTemplate(wpdm_valueof($scparams, 'template', 'link-template-default.php'), $pack) . "</div>";
            $html .= $repeater;

        }


        $html = "<div class='row'>{$html}</div>";

        $_scparams = Crypt::encrypt($scparams);
        if (!isset($paging) || intval($paging) == 1) {
            $pag_links = wpdm_paginate_links($total, $items_per_page, $page, 'cp', array( 'format' => "?sc_params={$_scparams}&cp=%#%",'container' => '#content_' . $scid, 'async' => (isset($async) && $async == 1 ? 1 : 0), 'next_text' => ' <i style="display: inline-block;width: 8px;height: 8px;border-right: 2px solid;border-top: 2px solid;transform: rotate(45deg);margin-left: -2px;margin-top: -2px;"></i> ', 'prev_text' => ' <i style="display: inline-block;width: 8px;height: 8px;border-right: 2px solid;border-bottom: 2px solid;transform: rotate(135deg);margin-left: 2px;margin-top: -2px;"></i> '));
            $pagination = "<div style='clear:both'></div>" . $pag_links . "<div style='clear:both'></div>";
        } else
            $pagination = "";

        global $post;

        $burl = get_permalink();
        $sap = get_option('permalink_structure') ? '?' : '&';
        $burl = $burl . $sap;
        if (isset($_GET['p']) && $_GET['p'] != '') $burl .= 'p=' . esc_attr($_GET['p']) . '&';
        if (isset($_GET['src']) && $_GET['src'] != '') $burl .= 'src=' . esc_attr($_GET['src']) . '&';
        $orderby = isset($_GET['orderby']) ? esc_attr($_GET['orderby']) : 'date';
        $order = ucfirst($order);

        $order_field = " " . __(ucwords(str_replace("_", " ", $order_field)), "wpdmpro");
        $ttitle = __("Title", "download-manager");
        $tdls = __("Downloads", "download-manager");
        $tcdate = __("Publish Date", "download-manager");
        $tudate = __("Update Date", "download-manager");
        $tasc = __("Asc", "download-manager");
        $tdsc = __("Desc", "download-manager");
        $tsrc = __("Search", "download-manager");
        $ord = __("Order", "download-manager");
        $order_by_label = __("Order By", "download-manager");

        $css_class = isset($scparams['css_class']) ? sanitize_text_field($scparams['css_class']) : '';
        $desc = isset($scparams['desc']) ? sanitize_text_field($scparams['desc']) : '';

        $title = isset($title) && $title != '' && $total > 0 ? "<h3>$title</h3>" : "";


        $toolbar = isset($toolbar) ? $toolbar : 0;

        ob_start();
        include Template::locate("shortcodes/packages.php", WPDM_TPL_FALLBACK);
        $content = ob_get_clean();
        return $content;
     }


    /**
     * @param array $params
     * @return array|null|\WP_Post
     * @usage Shortcode callback function for [wpdm_search_result]
     */
    function searchResult($params = array())
    {
        global $wpdb;

        if (is_array($params))
            @extract($params);
        $template = isset($template) && $template != '' ? $template : 'link-template-calltoaction3';
        $async = isset($async) ? $async : 0;
        $items_per_page = isset($items_per_page) ? $items_per_page : 0;
        update_post_meta(get_the_ID(), "__wpdm_link_template", $template);
        update_post_meta(get_the_ID(), "__wpdm_items_per_page", $items_per_page);
        $strm = wpdm_query_var('search', 'txt');
        if ($strm === '') $strm = wpdm_query_var('s', 'txt');
        $html = '';
        $cols = isset($cols) ? $cols : 1;
        $colspad = isset($colspad) ? $colspad : 1;
        $colsphone = isset($colsphone) ? $colsphone : 1;
        if (($strm == '' && isset($init) && $init == 1) || $strm != '')
            $html = $this->packages(array('items_per_page' => $items_per_page, 'template' => $template, 's' => $strm, 'paging' => true, 'toolbar' => 0, 'cols' => $cols, 'colsphone' => $colsphone, 'colspad' => $colspad, 'async' => $async));
        $html = "<div class='w3eden'><form id='wpdm-search-form' style='margin-bottom: 20px'><div class='input-group input-group-lg'><div class='input-group-addon input-group-prepend'><span class=\"input-group-text\"><i class='fas fa-search'></i></span></div><input type='text' name='search' value='" . $strm . "' class='form-control input-lg' /></div></form>{$html}</div>";
        return str_replace(array("\r", "\n"), "", $html);
    }

    /**
     * @usage Callback function for shortcode [wpdm_package id=PID]
     * @param mixed $params
     * @return mixed
     */
    function package($params)
    {
        extract($params);
        if (!isset($id)) return '';
        $id = (int)$id;
        if (get_post_type($id) != 'wpdmpro') return '';
        $postlink = site_url('/');
        if (isset($pagetemplate) && $pagetemplate == 1) {
            $template = get_post_meta($id, '__wpdm_page_template', true);
            $wpdm_package['page_template'] = stripcslashes($template);
            $data = \WPDM\Package::fetchTemplate($template, $id, 'page');
            $siteurl = site_url('/');
            return "<div class='w3eden'>{$data}</div>";
        }

        $template = isset($params['template']) ? $params['template'] : get_post_meta($id, '__wpdm_template', true);
        if ($template == '') $template = 'link-template-calltoaction3.php';
        $html = "<div class='w3eden'>" . \WPDM\Package::fetchTemplate($template, $id, 'link') . "</div>";
        //wp_reset_query();
        //wp_reset_postdata();
        return $html;
    }


    /**
     * @usage Generate direct link to download
     * @param $params
     * @param string $content
     * @return string
     */
    function directLink($params, $content = "")
    {
        extract($params);

        global $wpdb;
        $ID = (int)$params['id'];

        if (\WPDM\Package::isLocked($ID))
            $linkURL = get_permalink($ID);
        else
            $linkURL = home_url("/?wpdmdl=" . $ID);

        $extras = isset($params['extras']) ? wpdm_sanitize_var($params['extras'], 'txt') : "";
        $target = isset($params['target']) ? "target='" . wpdm_sanitize_var($params['target'], 'txt') . "'" : "";
        $class = isset($params['class']) ? "class='" . wpdm_sanitize_var($params['class'], 'txt') . "'" : "";
        $style = isset($params['style']) ? "style='" . wpdm_sanitize_var($params['style'], 'txt') . "'" : "";
        $rel = isset($params['rel']) ? "rel='" . wpdm_sanitize_var($params['rel'], 'txt') . "'" : "";
        $eid = isset($params['eid']) ? "id='" . wpdm_sanitize_var($params['eid'], 'txt') . "'" : "";
        $linkLabel = isset($params['label']) && !empty($params['label']) ? $params['label'] : get_post_meta($ID, '__wpdm_link_label', true);
        $linkLabel = empty($linkLabel) ? get_the_title($ID) : $linkLabel;
        $linkLabel = wpdm_sanitize_var($linkLabel, 'kses');
        return "<a {$target} {$class} {$eid} {$style} {$rel} {$extras} href='$linkURL'>$linkLabel</a>";

    }

    /**
     * @usage Short-code [wpdm_all_packages] to list all packages in tabular format
     * @param array $params
     * @return string
     */
    function allPackages($params = array())
    {
        global $wpdb, $current_user, $wp_query;
        $items = isset($params['items_per_page']) && $params['items_per_page'] > 0 ? $params['items_per_page'] : 20;
        if (isset($params['jstable']) && $params['jstable'] == 1) $items = 2000;
        $cp = isset($wp_query->query_vars['paged']) && $wp_query->query_vars['paged'] > 0 ? $wp_query->query_vars['paged'] : 1;
        $terms = isset($params['categories']) ? explode(",", $params['categories']) : array();
        $tag = isset($params['tag']) ? $params['tag'] : '';
        if (isset($_GET['wpdmc'])) $terms = array(esc_attr($_GET['wpdmc']));
        $offset = ($cp - 1) * $items;
        $total_files = wp_count_posts('wpdmpro')->publish;
        if (count($terms) > 0) {
            $tax_query = array(array(
                'taxonomy' => 'wpdmcategory',
                'field' => 'slug',
                'terms' => $terms,
                'operator' => 'IN',
                'include_children' => false
            ));
        }
        if ($tag != '') {
            $params['tag'] = $tag;
        }
        if (isset($params['login']) && $params['login'] == 1 && !is_user_logged_in())
            return $this->loginForm($params);
        else {
            ob_start();
            include(wpdm_tpl_path("wpdm-all-downloads.php"));
            $data = ob_get_clean();
            return $data;
        }
    }

    /**
     * @usage Show packages by tag
     * @param $params
     * @return string
     */
    function packagesByTag($params)
    {
        if(!$params || !isset($params['id'])) return '';

        $params['tags'] = $params['id'];
        unset($params['id']);
        return $this->packages($params);

    }


    function categoryLink($params)
    {

        $term = (array)get_term($params['id'], 'wpdmcategory');
        $icon = \WPDM\libs\CategoryHandler::icon($params['id']);
        $term['icon'] = $icon ? "<img src='$icon' alt='{$term['name']}' />" : "";
        $params['template'] = isset($params['template']) && $params['template'] != '' ? $params['template'] : 'link-template-category-link.php';
        return \WPDM\Template::output($params['template'], $term);
    }

    function category($params = [])
    {
        return WPDM()->categories->embed($params);
    }


    function userFavourites($params = array())
    {
        global $wpdb, $current_user;
        if (!isset($params['user']) && !is_user_logged_in()) return $this->loginForm();
        ob_start();
        include wpdm_tpl_path('user-favourites.php');
        return ob_get_clean();
    }

    function authors($params = array())
    {
        $sid = isset($params['sid']) ? $params['sid'] : '';
        update_post_meta(get_the_ID(), '__wpdm_users_params' . $sid, $params);
        ob_start();
        include wpdm_tpl_path("list-authors.php");
        return ob_get_clean();
    }

    function listAuthors($params = null)
    {

        if (!$params) $params = get_post_meta(wpdm_query_var('_pid', 'int'), '__wpdm_users_params' . wpdm_query_var('_sid'), true);
        $page = isset($_REQUEST['cp']) && $_REQUEST['cp'] > 0 ? (int)$_REQUEST['cp'] : 1;
        $items_per_page = isset($params['items_per_page']) ? $params['items_per_page'] : 12;
        //$offset = $page * $items_per_page;
        $cols = isset($params['cols']) && in_array($params['cols'], array(1, 2, 3, 4, 6)) ? $params['cols'] : 0;
        if ($cols > 0) $cols_class = "col-md-" . (12 / $cols);

        $args = array(
            'role' => isset($params['role']) ? $params['role'] : '',
            'role__in' => isset($params['role__in']) ? explode(",", $params['role__in']) : array(),
            'role__not_in' => isset($params['role__not_in']) ? explode(",", $params['role__not_in']) : array(),
            'meta_key' => isset($params['meta_key']) ? $params['meta_key'] : '',
            'meta_value' => isset($params['meta_value']) ? $params['meta_value'] : '',
            'meta_compare' => isset($params['meta_compare']) ? $params['meta_compare'] : '',
            //'meta_query'   => array(),
            //'date_query'   => array(),
            'include' => isset($params['include']) ? explode(",", $params['include']) : array(),
            'exclude' => isset($params['exclude']) ? explode(",", $params['exclude']) : array(),
            'orderby' => isset($params['orderby']) ? $params['orderby'] : 'login',
            'order' => isset($params['order']) ? $params['order'] : 'DESC',
            //'offset'       => $offset,
            'search' => isset($params['search']) ? $params['search'] : '',
            'number' => $items_per_page,
            'paged' => $page,
            'count_total' => true,
        );
        $users = new \WP_User_Query($args);
        if ($cols > 0) echo "<div class='row'>";
        foreach ($users->get_results() as $user) {
            if (isset($cols_class)) echo "<div class='$cols_class'>";
            include wpdm_tpl_path("author.php");
            if (isset($cols_class)) echo "</div>";
        }
        if ($cols > 0) echo "</div>";
        $total = $users->get_total();
        $contid = isset($params['sid']) ? "-{$params['sid']}" : '';
        if (isset($params['paging']) && (int)$params['paging'] == 1)
            echo wpdm_paginate_links($total, $items_per_page, $page, 'cp', array('async' => 1, 'container' => "#wpdm-authors{$contid}"));
    }

    function emailToDownload($params){
        if(!isset($params['id'])) return "";
        ob_start();
        include Template::locate("email-to-download.php");
        $cont = ob_get_clean();
        return $cont;
    }


}
