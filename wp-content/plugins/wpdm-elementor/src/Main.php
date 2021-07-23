<?php

namespace WPDM\Elementor;

use Elementor\Elements_Manager;
use Elementor\Widgets_Manager;
use WPDM\Elementor\API\API;
use WPDM\Elementor\Widgets\AllPackagesWidget;
use WPDM\Elementor\Widgets\CategoryWidget;
use WPDM\Elementor\Widgets\DirectLinkWidget;
use WPDM\Elementor\Widgets\FrontendWidget;
use WPDM\Elementor\Widgets\LoginFormWidget;
use WPDM\Elementor\Widgets\PackagesWidget;
use WPDM\Elementor\Widgets\PackageWidget;
use WPDM\Elementor\Widgets\RegFormWidget;
use WPDM\Elementor\Widgets\SearchResultWidget;
use WPDM\Elementor\Widgets\TagWidget;
use WPDM\Elementor\Widgets\UserDashboardWidget;
use WPDM\Elementor\Widgets\UserProfileWidget;

final class Main
{

    /**
     * 
     * 
     */
    public static function getInstance()
    {
        static $instance;
        if (is_null($instance)) {
            $instance = new self;
        }
        return $instance;
    }

    /**
     * 
     * 
     */
    private function __construct()
    {
        API::getInstance();
        add_action("plugin_loaded", [$this, 'pluginLoaded']);

    }

    function pluginLoaded(){
        add_action( 'elementor/init', [ $this, 'addHooks' ] );
    }

    /**
     * 
     * 
     */
    function addHooks()
    {
        add_action('elementor/elements/categories_registered', [$this, 'registerCategory']);
        add_action('elementor/widgets/widgets_registered', [$this, 'registerWidgets'], 99);
    }

    /**
     * 
     * 
     */
    public function registerCategory(Elements_Manager $elementsManager)
    {
        $elementsManager->add_category('wpdm', ['title' => 'Download Manager']);
    }

    /**
     * 
     * 
     */
    public function registerWidgets(Widgets_Manager $widget_manager)
    {

        require_once __DIR__.'/includes.php';


        $widget_manager->register_widget_type(new PackagesWidget());

        $widget_manager->register_widget_type(new PackageWidget());

        $widget_manager->register_widget_type(new CategoryWidget());

        //$widget_manager->register_widget_type(new TagWidget());

        $widget_manager->register_widget_type(new AllPackagesWidget());

        $widget_manager->register_widget_type(new SearchResultWidget());

        $widget_manager->register_widget_type(new RegFormWidget());

        $widget_manager->register_widget_type(new LoginFormWidget());

        $widget_manager->register_widget_type(new FrontendWidget());

        $widget_manager->register_widget_type(new UserDashboardWidget());

        $widget_manager->register_widget_type(new DirectLinkWidget());

        //$widget_manager->register_widget_type(new UserProfileWidget());

    }

}
