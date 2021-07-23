<?php

if(!defined('ABSPATH')) die("!");

the_post();

?><!DOCTYPE html>
<html style="background: transparent">
<head>
    <title><?php the_title(); ?></title>
    <script>
        var wpdm_home_url = "<?php echo home_url('/'); ?>";
    </script>
    <link rel="stylesheet" href="<?php echo WPDM_BASE_URL; ?>assets/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" href="<?php echo WPDM_BASE_URL; ?>assets/css/front.css" />
    <link rel="stylesheet" href="<?php echo WPDM_BASE_URL; ?>assets/font-awesome/css/font-awesome.min.css" />

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">

    <?php
    WPDM()->apply::uiColors();
    ?>
<style>
    img{
        max-width: 100%;
    }

    body {
        font-size: .875rem;
    }

    .feather {
        width: 16px;
        height: 16px;
        vertical-align: text-bottom;
    }

    /*
     * Sidebar
     */

    #wpdm-dashboard-sidebar {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        z-index: 100; /* Behind the navbar */
        padding: 48px 0 0; /* Height of navbar */
        box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
        background: #ffffff;
        width: 250px !important;
    }

    #wpdm-dashboard-sidebar-sticky {
        position: relative;
        top: 0;
        height: calc(100vh - 48px);
        padding-top: .5rem;
        overflow-x: hidden;
        overflow-y: auto; /* Scrollable contents if viewport is shorter than content. */
    }

    @supports ((position: -webkit-sticky) or (position: sticky)) {
        #wpdm-dashboard-sidebar-sticky {
            position: -webkit-sticky;
            position: sticky;
        }
    }

    #wpdm-dashboard-sidebar .nav-link {
        font-weight: 500;
        color: #333;
    }

    #wpdm-dashboard-sidebar .nav-link .feather {
        margin-right: 4px;
        color: #999;
    }

    #wpdm-dashboard-sidebar .nav-link.active {
        color: #007bff;
    }

    #wpdm-dashboard-sidebar .nav-link:hover .feather,
    #wpdm-dashboard-sidebar .nav-link.active .feather {
        color: inherit;
    }

    #wpdm-dashboard-sidebar-heading {
        font-size: .75rem;
        text-transform: uppercase;
    }

    /*
     * Content
     */

    [role="main"] {
        padding-top: 133px; /* Space for fixed navbar */
    }

    @media (min-width: 768px) {
        [role="main"] {
            padding-top: 48px; /* Space for fixed navbar */
        }
    }

    /*
     * Navbar
     */

    .navbar-brand {
        padding-top: .75rem;
        padding-bottom: .75rem;
        font-size: 1rem;
        background-color: rgba(0, 0, 0, .25);
        box-shadow: inset -1px 0 0 rgba(0, 0, 0, .25);
    }

    .navbar .form-control {
        padding: .75rem 1rem;
        border-width: 0;
        border-radius: 0;
    }

    .form-control-dark {
        color: #fff;
        background-color: rgba(255, 255, 255, .1);
        border-color: rgba(255, 255, 255, .1);
    }

    .form-control-dark:focus {
        border-color: transparent;
        box-shadow: 0 0 0 3px rgba(255, 255, 255, .25);
    }

    .w3eden #wpdm-dashboard-sidebar .adp-item{
        font-size: 15px;
        letter-spacing: 1px;
        font-weight: 400;
        line-height: 40px;
        margin-bottom: 10px;
        color: #6e84a3;
    }
    .w3eden #wpdm-dashboard-sidebar .adp-item .far,
    .w3eden #wpdm-dashboard-sidebar .adp-item .fas,
    .w3eden #wpdm-dashboard-sidebar .adp-item .fa{
        width: 40px;
        line-height: 40px;
        height: 40px;
        text-align: center;
        border-radius: 2px;
        box-shadow: 0 0 9px rgba(110, 132, 163, 0.2);
        margin-right: 10px;
    }
    #wpdm-dashboard-content{
        margin-top: 90px;
        max-width: 100% !important;
        width: calc(100% - 250px);
        margin-left: 250px;
        padding: 0 30px;
    }

    .w3eden .w3eden.author-dashbboard #tabs{
        height: calc(100% - 264px);
        overflow: auto;
        padding: 10px 30px !important;
    }
    #logo-block{
        text-align: center;
        padding: 50px 0;
        background: rgba(227, 235, 246, 0.2);
        margin-bottom: 20px;
        border-bottom: 1px solid #e3ebf6 !important;
    }

</style>

</head>
<body class="w3eden g-sidenav-show g-sidenav-pinned">
<div class="d-flex flex-column fixed-top flex-md-row align-items-center p-3 px-md-4 mb-3 bg-white border-bottom shadow-sm">
    <h5 class="my-0 mr-md-auto font-weight-normal"><?php bloginfo('name'); ?></h5>
    <nav class="my-2 my-md-0 mr-md-3">
        <a class="p-2 text-dark" href="#">Features</a>
        <a class="p-2 text-dark" href="#">Enterprise</a>
        <a class="p-2 text-dark" href="#">Support</a>
        <a class="p-2 text-dark" href="#">Pricing</a>
    </nav>
    <a class="btn btn-outline-primary" href="#">Sign up</a>
</div>
<div class="container-fluid">
    <?php
    the_content();
    ?>
</div>

</body>
</html>
