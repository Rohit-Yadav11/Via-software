<?php

/**
 * User: shahnuralam
 * Date: 5/6/17
 * Time: 7:58 PM
 */
namespace WPDM\libs;

class DashboardWidgets
{

    function __construct()
    {
        add_action('wp_dashboard_setup', array($this, 'addDashboardWidget'));
    }

    function overview()
    {
        include WPDM_BASE_DIR . 'admin/tpls/dashboard-widgets/overview.php';
    }

    function socialOverview()
    {
        include WPDM_BASE_DIR . 'admin/tpls/dashboard-widgets/social.php';
    }

    function specialOffer()
    {
        include WPDM_BASE_DIR . 'admin/tpls/dashboard-widgets/offer.php';
    }


    function addDashboardWidget()
    {
        wp_add_dashboard_widget('wpdm_overview', __( "Download Manager Overview" , "download-manager" ), array($this, 'overview'));
        wp_add_dashboard_widget('wpdm_social_overview', __( "Social Overview" , "download-manager" ), array($this, 'socialOverview'));
        wp_add_dashboard_widget('wpdm_offer', __( "Special Offer" , "download-manager" ), array($this, 'specialOffer'));

    }

}

