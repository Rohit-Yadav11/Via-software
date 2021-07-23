<?php
/*
Plugin Name: Download Manager
Plugin URI: https://www.wpdownloadmanager.com/purchases/
Description: Manage, Protect and Track file downloads, and sell digital products from your WordPress site. A complete digital asset management solution.
Author: W3 Eden, Inc.
Author URI: https://www.wpdownloadmanager.com/
Version: 5.3.3
Text Domain: download-manager
Domain Path: /languages
*/

namespace WPDM;

use WPDM\admin\WordPressDownloadManagerAdmin;
use WPDM\libs\Apply;
use WPDM\libs\CategoryHandler;
use WPDM\libs\Crypt;
use WPDM\libs\DownloadStats;
use WPDM\libs\FileSystem;
use WPDM\libs\MediaHandler;
use WPDM\libs\PackageTemplate;
use WPDM\libs\PageTemplate;
use WPDM\libs\User;
use WPDM\libs\UserAgent;

global $WPDM, $wpdm_asset;

//if(!isset($_SESSION) && !strstr($_SERVER['REQUEST_URI'], 'wpdm-media/') && (!isset($_REQUEST['action']) || $_REQUEST['action'] !== 'edit-theme-plugin-file') && !strstr($_SERVER['REQUEST_URI'], 'wpdm-download/') && !isset($_REQUEST['wpdmdl']) && !isset($_GET['health-check-troubleshoot-enable-plugin']))
//    @session_start();

define('WPDM_Version','5.3.3');

$upload_dir = wp_upload_dir();
$upload_base_url = $upload_dir['baseurl'];
$upload_dir = $upload_dir['basedir'];

if(!defined('WPDM_ADMIN_CAP'))
    define('WPDM_ADMIN_CAP','manage_options');

if(!defined('WPDM_MENU_ACCESS_CAP'))
    define('WPDM_MENU_ACCESS_CAP','manage_options');

define('WPDM_BASE_DIR',dirname(__FILE__).'/');

define('WPDM_BASE_URL',plugins_url('/download-manager/'));

if(!defined('UPLOAD_DIR'))
    define('UPLOAD_DIR',$upload_dir.'/download-manager-files/');

if(!defined('WPDM_CACHE_DIR')) {
    define('WPDM_CACHE_DIR', $upload_dir . '/wpdm-cache/');
    define('WPDM_CACHE_URL', $upload_base_url . '/wpdm-cache/');
}

if(!defined('WPDM_TPL_FALLBACK'))
    define('WPDM_TPL_FALLBACK', dirname(__FILE__) . '/tpls/');

if(!defined('WPDM_TPL_DIR')) {
    if((int)get_option('__wpdm_bsversion', '') !== 3)
        define('WPDM_TPL_DIR', dirname(__FILE__) . '/tpls/');
    else
        define('WPDM_TPL_DIR', dirname(__FILE__) . '/tpls3/');
}

if(!defined('UPLOAD_BASE'))
    define('UPLOAD_BASE',$upload_dir);

if(!defined('WPDM_USE_GLOBAL'))
    define('WPDM_USE_GLOBAL', null);


if(!defined('WPDM_FONTAWESOME_URL'))
    define('WPDM_FONTAWESOME_URL','https://use.fontawesome.com/releases/v5.12.1/css/all.css');

if(!defined('NONCE_KEY') || !defined('NONCE_SALT')){
    //To avoid warning when not defined
    define('NONCE_KEY',       'Bm|_Ek@F|HdkA7)=alSJg5_<z-j-JmhK<l&*.d<J+/71?&7pL~XBXnF4jKz>{Apx');
    define('NONCE_SALT',       'XffybIqfklKjegGdRp7EU4kprZX00NESOE8olZ2BZ8+BQTw3bXXSbzeGssgZ');
    /**
     * Generate WordPress Security Keys and Salts from https://api.wordpress.org/secret-key/1.1/salt/ and place them in your wp-config.php
     */
}

@ini_set('upload_tmp_dir',WPDM_CACHE_DIR);


class WordPressDownloadManager{

    public $authorDashboard;
    public $userDashboard;
    public $user;
    public $userProfile;
    public $apply;
    public $fileSystem;
    public $admin;
    public $category;
    public $categories;
    public $asset;
    public $shortCode;
    public $template;
    public $packageTemplate;
    public $setting;
    public $crypt;
    public $downloadHistory;
    public $bsversion = '';
    public $userAgent;
    public $message;
    public $ui;

    private static $wpdm_instance = null;

    public static function instance(){
        if ( is_null( self::$wpdm_instance ) ) {
            self::$wpdm_instance = new self();
        }
        return self::$wpdm_instance;
    }

    function __construct(){

        register_activation_hook(__FILE__, array($this, 'install'));

        add_action( 'upgrader_process_complete', array($this, 'update'), 10, 2);


        $this->bsversion = get_option('__wpdm_bsversion', '');

        add_action( 'init', array($this, 'registerPostTypeTaxonomy'), 1 );
        add_action( 'init', array($this, 'registerScripts'), 1 );

        add_action( 'plugins_loaded', array($this, 'loadTextdomain') );
        add_action( 'wp_enqueue_scripts', array($this, 'enqueueScripts') );

        add_action( 'wp_head', array($this, 'wpHead'), 0 );
        add_action( 'wp_footer', array($this, 'wpFooter') );

        spl_autoload_register( array( $this, 'autoLoad' ) );


        include_once(dirname(__FILE__) . "/wpdm-functions.php");
        include_once(dirname(__FILE__) . "/libs/class.CronJobs.php");
        include_once(dirname(__FILE__) . "/libs/class.AssetManager.php");
        include_once(dirname(__FILE__) . "/libs/class.SocialConnect.php");

        include(dirname(__FILE__)."/wpdm-core.php");


        $this->authorDashboard  = new AuthorDashboard();
        $this->userDashboard    = new UserDashboard();
        $this->userProfile      = new UserProfile();
        $this->user             = new User();
        $this->apply            = new Apply();
        $this->admin            = new WordPressDownloadManagerAdmin();
        $this->shortCode        = new ShortCodes();
                                  new MediaHandler();
                                  new MediaAccessControl();
        $this->package          = new Package();
        $this->category         = new CategoryHandler();
        $this->categories       = $this->category;
        $this->setting          = new Settings();
        $this->fileSystem       = new FileSystem();
        $this->template         = new Template();
        $this->packageTemplate  = new PackageTemplate();
        $this->crypt            = new Crypt();
        $this->downloadHistory  = new DownloadStats();
        $this->userAgent        = new UserAgent();
        $this->message          = new \WPDM_Messages();
        $this->ui               = new UI();

        new PageTemplate();

    }

    /**
     * @usage Install Plugin
     */
    function install(){

        Installer::init();

        $this->registerPostTypeTaxonomy();

        flush_rewrite_rules();
        self::createDir();

    }

    /**
     * Update plugin
     * @param $upgrader_object
     * @param $options
     */
    function update( $upgrader_object, $options ) {
        $current_plugin_path_name = plugin_basename( __FILE__ );

        if ($options['action'] == 'update' && $options['type'] == 'plugin' ){
            foreach($options['plugins'] as $each_plugin){
                if ($each_plugin==$current_plugin_path_name){
                    if(Installer::dbUpdateRequired()){
                        Installer::updateDB();
                        return;
                    }
                }
            }
        }
    }


    /**
     * @usage Load Plugin Text Domain
     */
    function loadTextdomain(){
        load_plugin_textdomain('download-manager', WP_PLUGIN_URL . "/download-manager/languages/", 'download-manager/languages/');
    }

    /**
     * @usage Register WPDM Post Type and Taxonomy
     */
    public function registerPostTypeTaxonomy()
    {
        $labels = array(
            'name'                  => __( "Downloads" , "download-manager" ),
            'singular_name'         => __( "Package" , "download-manager" ),
            'add_new'               => __( "Add New" , "download-manager" ),
            'add_new_item'          => __( "Add New Package" , "download-manager" ),
            'edit_item'             => __( "Edit Package" , "download-manager" ),
            'new_item'              => __( "New Package" , "download-manager" ),
            'all_items'             => __( "All Packages" , "download-manager" ),
            'view_item'             => __( "View Package" , "download-manager" ),
            'search_items'          => __( "Search Packages" , "download-manager" ),
            'not_found'             => __( "No Package Found" , "download-manager" ),
            'not_found_in_trash'    => __( "No Packages found in Trash" , "download-manager" ),
            'parent_item_colon'     => '',
            'menu_name'             => __( "Downloads" , "download-manager" )

        );

        $tslug = get_option('__wpdm_purl_base', 'download');
        if(!strpos("_$tslug", "%"))
            $slug = sanitize_title($tslug);
        else
            $slug = $tslug;

        $args = array(
            'labels'                => $labels,
            'public'                => true,
            'publicly_queryable'    => get_option('__wpdm_publicly_queryable', 1),
            'show_ui'               => true,
            'show_in_menu'          => true,
            'show_in_nav_menus'     => true,
            'query_var'             => true,
            'rewrite'               => array('slug' => $slug, 'with_front' => (bool)get_option('__wpdm_purl_with_front', false)), //get_option('__wpdm_purl_base','download')
            'capability_type'       => 'post',
            'has_archive'           => (get_option('__wpdm_has_archive', false)==false?false:sanitize_title(get_option('__wpdm_archive_page_slug', 'all-downloads'))),
            'hierarchical'          => false,
            //'taxonomies'            => array('post_tag'),
            'menu_icon'             => 'dashicons-download',
            'exclude_from_search'   => (bool)get_option('__wpdm_exclude_from_search', false),
            'supports'              => array('title', 'editor', 'publicize', 'excerpt', 'custom-fields', 'thumbnail', 'comments','author')

        );

        $wpdm_tags = !defined('WPDM_USE_POST_TAGS') || WPDM_USE_POST_TAGS === false;


        if(!$wpdm_tags)
            $args['supports'][] = 'post_tag';


        register_post_type('wpdmpro', $args);


        $labels = array(
            'name'                  => __( "Categories" , "download-manager" ),
            'singular_name'         => __( "Category" , "download-manager" ),
            'search_items'          => __( "Search Categories" , "download-manager" ),
            'all_items'             => __( "All Categories" , "download-manager" ),
            'parent_item'           => __( "Parent Category" , "download-manager" ),
            'parent_item_colon'     => __( "Parent Category:" , "download-manager" ),
            'edit_item'             => __( "Edit Category" , "download-manager" ),
            'update_item'           => __( "Update Category" , "download-manager" ),
            'add_new_item'          => __( "Add New Category" , "download-manager" ),
            'new_item_name'         => __( "New Category Name" , "download-manager" ),
            'menu_name'             => __( "Categories" , "download-manager" ),
        );

        $args = array(
            'hierarchical'          => true,
            'labels'                => $labels,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'query_var'             => true,
            'rewrite'               => array('slug' => sanitize_title(get_option('__wpdm_curl_base', 'download-category'))),
        );

        register_taxonomy('wpdmcategory', array('wpdmpro'), $args);

        if($wpdm_tags) {
            $labels = array(
                'name' => __("Tags", "download-manager"),
                'singular_name' => __("Tag", "download-manager"),
                'search_items' => __("Search Document Tags", "download-manager"),
                'all_items' => __("All Tags", "download-manager"),
                'edit_item' => __("Edit Tag", "download-manager"),
                'update_item' => __("Update Tag", "download-manager"),
                'add_new_item' => __("Add New Tag", "download-manager"),
                'new_item_name' => __("New Tag Name", "download-manager"),
                'menu_name' => __("Tags", "download-manager"),
            );

            $args = array(
                'hierarchical' => false,
                'labels' => $labels,
                'show_ui' => true,
                'show_admin_column' => true,
                'query_var' => true,
                'rewrite' => array('slug' => sanitize_title(get_option('__wpdm_turl_base', 'document-tag'))),
            );

            register_taxonomy('wpdmtag', array('wpdmpro'), $args);

            //unregister_taxonomy_for_object_type('post_tag', 'wpdmpro');
        }

    }

    /**
     * @usage Create upload dir
     */
    public static function createDir()
    {
        if (!file_exists(UPLOAD_BASE)) {
            @mkdir(UPLOAD_BASE, 0755);
            @chmod(UPLOAD_BASE, 0755);
        }
        if(!file_exists(UPLOAD_DIR)) {
            @mkdir(UPLOAD_DIR, 0755);
            @chmod(UPLOAD_DIR, 0755);
        }

        if(!file_exists(WPDM_CACHE_DIR)) {
            @mkdir(WPDM_CACHE_DIR, 0755);
            @chmod(WPDM_CACHE_DIR, 0755);
        }

        $_upload_dir = wp_upload_dir();
        $_upload_dir = $_upload_dir['basedir'];
        $tags_dir = $_upload_dir.'/wpdm-custom-tags/';
        if(!file_exists($tags_dir)) {
            @mkdir($tags_dir, 0755);
            @chmod($tags_dir, 0755);
            FileSystem::blockHTTPAccess($tags_dir);
        }

        if(!file_exists($_upload_dir.'/wpdm-file-type-icons/')) {
            @mkdir($_upload_dir.'/wpdm-file-type-icons/', 0755);
            @chmod($_upload_dir.'/wpdm-file-type-icons/', 0755);
        }

        self::setHtaccess();

    }


    /**
     * @usage Protect Download Dir using .htaccess rules
     */
    public static function setHtaccess()
    {
        FileSystem::blockHTTPAccess(UPLOAD_DIR);
    }

    function registerScripts(){

        if(is_admin()) return;

        wp_register_style('wpdm-front-bootstrap3', plugins_url('/download-manager/assets/bootstrap3/css/bootstrap.min.css'));
        wp_register_style('wpdm-front-bootstrap', plugins_url('/download-manager/assets/bootstrap/css/bootstrap.min.css'));
        wp_register_style('wpdm-font-awesome', WPDM_FONTAWESOME_URL);
        wp_register_style('wpdm-front3', plugins_url() . '/download-manager/assets/css/front3.css');
        wp_register_style('wpdm-front', plugins_url() . '/download-manager/assets/css/front.css', 99999999);

        wp_register_script('wpdm-front-bootstrap3', plugins_url('/download-manager/assets/bootstrap3/js/bootstrap.min.js'), array('jquery'));
        wp_register_script('wpdm-poper', plugins_url('/download-manager/assets/bootstrap/js/popper.min.js'), array('jquery'));
        wp_register_script('wpdm-front-bootstrap', plugins_url('/download-manager/assets/bootstrap/js/bootstrap.min.js'), array('jquery'));
        wp_register_script('jquery-validate', plugins_url('/download-manager/assets/js/jquery.validate.min.js'), array('jquery'));

    }

    /**
     * @usage Enqueue all styles and scripts
     */
    function enqueueScripts()
    {
        global $post, $wpdm_asset;

        if(is_admin()) return;

        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-form');

        //wp_register_style('font-awesome', WPDM_BASE_URL . 'assets/font-awesome/css/font-awesome.min.css');

        $wpdmss = maybe_unserialize(get_option('__wpdm_disable_scripts', array()));

        if (is_array($wpdmss) && !in_array('wpdm-font-awesome', $wpdmss)) {
            wp_enqueue_style('wpdm-font-awesome');
        }


        if(is_object($post) && ( wpdm_query_var('adb_page') != '' || get_option('__wpdm_author_dashboard') == $post->ID || has_shortcode($post->post_content,'wpdm_frontend') || has_shortcode($post->post_content,'wpdm_package_form') || has_shortcode($post->post_content,'wpdm_user_dashboard') || (function_exists('has_block') && has_block('download-manager/dashboard')) || has_shortcode($post->post_content,'wpdm-file-browser') ) ){
            wp_enqueue_script('jquery-validate');
            wp_enqueue_script('jquery-ui');
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_script('thickbox');
            wp_enqueue_style('thickbox');
            wp_enqueue_script('media-upload');
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_script('jquery-ui-slider');
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script('jquery-ui-timepicker', WPDM_BASE_URL.'assets/js/jquery-ui-timepicker-addon.js',array('jquery','jquery-ui-core','jquery-ui-datepicker','jquery-ui-slider') );
            wp_enqueue_media();
            wp_enqueue_style('jqui-css', plugins_url('/download-manager/assets/jqui/theme/jquery-ui.css'));

            wp_enqueue_script('chosen', plugins_url('/download-manager/assets/js/chosen.jquery.min.js'), array('jquery'));
            wp_enqueue_style('chosen-css', plugins_url('/download-manager/assets/css/chosen.css'), 999999);
        }

        /**
         * Enable/disable nivo lighbox
         * 0 = Disable
         * 1 = Enable in single page only
         * 2 = Enable everywhere
         */
        $nivo_lightbox = apply_filters("wpdm_nivo_lightbox", $nivo_lightbox = 1);
        if($nivo_lightbox && ( is_singular('wpdmpro') || $nivo_lightbox === 2) ){
            wp_enqueue_script("nivo-lightbox", plugins_url('/download-manager/assets/js/nivo-lightbox.min.js'), array('jquery'));
            wp_enqueue_style("nivo-lightbox", plugins_url('/download-manager/assets/css/nivo-lightbox.css'));
            wp_enqueue_style("nivo-lightbox-theme", plugins_url('/download-manager/assets/css/themes/default/default.css'));
        }

        $bsversion = $this->bsversion;

        if (is_array($wpdmss) && !in_array('wpdm-bootstrap-css', $wpdmss)) {
            wp_enqueue_style('wpdm-front-bootstrap' . $bsversion);
        }

        if (is_array($wpdmss) && !in_array('wpdm-front', $wpdmss)) {
            wp_enqueue_style('wpdm-front' . $bsversion);
        }


        if (is_array($wpdmss) && !in_array('wpdm-bootstrap-js', $wpdmss)) {
            wp_enqueue_script('wpdm-poper');
            wp_enqueue_script('wpdm-front-bootstrap' . $bsversion);
        }

        //wp_enqueue_script('audio-player', plugins_url('/download-manager/assets/js/audio-player.js'), array('jquery'));
        wp_register_script('frontjs', plugins_url('/download-manager/assets/js/front.js'), array('jquery'), WPDM_Version);

        $wpdm_asset = array(
            'bsversion' => $bsversion,
            'spinner' => '<i class="fas fa-sun fa-spin"></i>'
        );
        $this->asset = $wpdm_asset;
        $wpdm_asset = apply_filters("wpdm_js_vars", $wpdm_asset);


        wp_localize_script('frontjs', 'wpdm_url', array(
            'home' => esc_url_raw(home_url('/')),
            'site' => esc_url_raw(site_url('/')),
            'ajax' => esc_url_raw(admin_url('/admin-ajax.php'))
        ));

        wp_localize_script('frontjs', 'wpdm_asset', $wpdm_asset);

        wp_enqueue_script('frontjs');


    }

    /**
     * @usage insert code in wp head
     */
    function wpHead(){
        ?>

        <script>
            var wpdm_site_url = '<?php echo site_url('/'); ?>';
            var wpdm_home_url = '<?php echo home_url('/'); ?>';
            var ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';
            var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
            var wpdm_ajax_url = '<?php echo admin_url('admin-ajax.php'); ?>';
            var wpdm_ajax_popup = '<?php echo get_option('__wpdm_ajax_popup', 0); ?>';
        </script>


        <?php
    }

    /**
     * @usage insert code in wp footer
     */
    function wpFooter(){
        global $post;
        $post_content = is_object($post) && isset($post) ? $post->post_content : '';

        //Enable/disable view count
        $view_count = apply_filters("wpdm_view_count", $view_count  = 1);

        //Enable/disable nivo lighbox, $nivo_lightbox === 2 <--- Enable everywhere
        $nivo_lightbox = apply_filters("wpdm_nivo_lightbox", $nivo_lightbox = 1);

        if(get_option('__wpdm_modal_login', 0)
            && !has_shortcode($post_content, 'wpdm_user_dashboard')
            && !has_shortcode($post_content, 'wpdm_frontend')
            && !has_shortcode($post_content, 'wpdm_login_form')
            && !has_shortcode($post_content, 'wpdm_reg_form')
        )
                echo $this->user->modalLoginForm();
            ?>
            <script>
                jQuery(function($){

                    <?php if(is_singular('wpdmpro') && $view_count){ ?>
                    setTimeout(function (){
                        $.get('<?php echo home_url('/?__wpdm_view_count='.wp_create_nonce(NONCE_KEY).'&id='.get_the_ID()); ?>');
                    }, 2000)
                    <?php } ?>

                    <?php if($nivo_lightbox && ( is_singular('wpdmpro') || $nivo_lightbox === 2) ){ ?>
                    try {
                        $('a.wpdm-lightbox').nivoLightbox();
                    } catch (e) {

                    }
                    <?php } ?>
                });
            </script>

            <?php
    }



    /**
     * @param $name
     * @usage Class autoloader
     */
    function autoLoad($name) {
        if(!strstr("_".$name, 'WPDM')) return;
        /*
        $name = str_replace("WPDM_","", $name);
        $name = str_replace("WPDM\\","", $name);
        $relative_path = str_replace("\\", "/", $name);
        $parts = explode("\\", $name);
        $class_file = end($parts);
        $name = basename($name);
        if(strlen($name) < 40 && file_exists(WPDM_BASE_DIR."libs/class.{$name}.php")){
            require_once WPDM_BASE_DIR."libs/class.{$name}.php";
        } else if(file_exists(WPDM_BASE_DIR.str_replace($class_file, 'class.'.$class_file.'.php', $relative_path))){
            require_once WPDM_BASE_DIR.str_replace($class_file, 'class.'.$class_file.'.php', $relative_path);
        }
        */
        $originClass = $name;
        $name = str_replace("WPDM_","", $name);
        $name = str_replace("WPDM\\","", $name);
        //$relative_path = str_replace("\\", "/", $name);
        $parts = explode("\\", $name);
        $class_file = end($parts);
        $class_file = 'class.'.$class_file.'.php';
        $parts[count($parts)-1] = $class_file;
        $relative_path = implode("/", $parts);


        $classPath = WPDM_BASE_DIR.$relative_path;
        $x_classPath = WPDM_BASE_DIR.str_replace("class.", "libs/class.", $relative_path);

        if(strlen($class_file) < 40 && file_exists($classPath)){
            require_once $classPath;
        } else if(strlen($class_file) < 40 && file_exists($x_classPath)){
            require_once $x_classPath;
        }
    }

}

$WPDM = WordPressDownloadManager::instance();

