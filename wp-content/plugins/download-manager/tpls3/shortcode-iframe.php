<?php
if (!defined('ABSPATH')) die();
/**
 * User: shahnuralam
 * Date: 1/16/18
 * Time: 12:33 AM
 */
error_reporting(0);

$pid = wpdm_query_var('__wpdmxp');
//setup_postdata($post);
//$pack = new \WPDM\Package();
//$pack->Prepare(get_the_ID());

?>
<!DOCTYPE html>
<html style="background: transparent">
<head>
    <title>Download <?php the_title(); ?></title>
    <script>
        var wpdm_home_url = "<?php echo home_url('/'); ?>";
    </script>
    <link rel="stylesheet" href="<?php echo WPDM_BASE_URL; ?>assets/bootstrap3/css/bootstrap.css" />
    <link rel="stylesheet" href="<?php echo WPDM_BASE_URL; ?>assets/css/front.css" />
    <link rel="stylesheet" href="<?php echo WPDM_BASE_URL; ?>assets/font-awesome/css/font-awesome.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Cantarell:400,700" />
    <script src="<?php echo includes_url(); ?>/js/jquery/jquery.js"></script>
    <script src="<?php echo includes_url(); ?>/js/jquery/jquery.form.min.js"></script>
    <script src="<?php echo WPDM_BASE_URL; ?>assets/bootstrap3/js/bootstrap.min.js"></script>
    <script src="<?php echo WPDM_BASE_URL; ?>assets/js/front.js"></script>
    <?php if((isset($pack->PackageData['form_lock']) && $pack->PackageData['form_lock'] == 1)  || $pack->PackageData['base_price'] > 0) wp_head(); ?>
    <style>

        html, body{
            overflow: visible;
            height: 100%;
            width: 100%;
            padding: 0;
            margin: 0;
            font-family: 'Cantarell', sans-serif;
            font-weight: 300;
            font-size: 10pt;
        }
        h4.modal-title{
            font-family: 'Cantarell', sans-serif;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #555555;
            font-size: 11pt;
            display: inline-block;
        }

        .w3eden label{
            font-weight: 400;
        }
        img{
            max-width: 100%;
        }
        .modal-backdrop{
            background: rgba(0,0,0,0.5);
        }


        .modal.fade{
            opacity:1;
        }
        .modal.fade .modal-dialog {
            -webkit-transform: translate(0);
            -moz-transform: translate(0);
            transform: translate(0);
        }

        .modal {
            text-align: center;
            padding: 0!important;
        }

        .wpdm-social-lock.btn {
            display: block;
            width: 100%;
        }

        @media (min-width: 768px) {
            .modal:before {
                content: '';
                display: inline-block;
                height: 100%;
                vertical-align: middle;
                margin-right: -4px;
            }

            .modal-dialog {
                display: inline-block;
                text-align: left;
                vertical-align: middle;
            }

            .wpdm-social-lock.btn {
                display: inline-block;
                width: 47%;
            }
        }

        @-moz-keyframes spin {
            from { -moz-transform: rotate(0deg); }
            to { -moz-transform: rotate(360deg); }
        }
        @-webkit-keyframes spin {
            from { -webkit-transform: rotate(0deg); }
            to { -webkit-transform: rotate(360deg); }
        }
        @keyframes spin {
            from {transform:rotate(0deg);}
            to {transform:rotate(360deg);}
        }
        .spin{
            -webkit-animation-name: spin;
            -webkit-animation-duration: 2000ms;
            -webkit-animation-iteration-count: infinite;
            -webkit-animation-timing-function: linear;
            -moz-animation-name: spin;
            -moz-animation-duration: 2000ms;
            -moz-animation-iteration-count: infinite;
            -moz-animation-timing-function: linear;
            -ms-animation-name: spin;
            -ms-animation-duration: 2000ms;
            -ms-animation-iteration-count: infinite;
            -ms-animation-timing-function: linear;

            animation-name: spin;
            animation-duration: 2000ms;
            animation-iteration-count: infinite;
            animation-timing-function: linear;
            display: inline-block;
        }


        .w3eden .panel-default {
            border-radius: 3px;
            margin-top: 10px !important;
        }
        .w3eden .panel-default:last-child{
            margin-bottom: 0 !important;
        }
        .w3eden .panel-default .panel-heading{
            letter-spacing: 0.5px;
            font-weight: 600;
            background-color: #f6f8f9;
        }

        .w3eden .panel-default .panel-footer{
            background-color: #fafafa;
        }

        .btn{
            outline: none !important;
        }
        .w3eden .panel{
            margin-bottom: 0;
        }
        .w3eden .modal-header{
            border: 0;
        }
        .w3eden .modal-content{
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.2);
            border: 0;
            border-radius: 6px;
            background: rgb(255,255,255);
            background: -moz-linear-gradient(-45deg,  rgba(255,255,255,1) 0%, rgba(243,243,243,1) 50%, rgba(237,237,237,1) 51%, rgba(255,255,255,1) 100%);
            background: -webkit-linear-gradient(-45deg,  rgba(255,255,255,1) 0%,rgba(243,243,243,1) 50%,rgba(237,237,237,1) 51%,rgba(255,255,255,1) 100%);
            background: linear-gradient(135deg,  rgba(255,255,255,1) 0%,rgba(243,243,243,1) 50%,rgba(237,237,237,1) 51%,rgba(255,255,255,1) 100%);
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#ffffff',GradientType=1 );
            overflow: hidden;
            max-width: 100%;
        }
        .w3eden .modal-body{
            max-height:  calc(100vh - 210px);
            overflow-y: auto;
            padding-top: 0 !important;
        }


        .w3eden .input-group-lg .input-group-btn .btn{
            border-top-right-radius: 4px !important;
            border-bottom-right-radius: 4px !important;
        }
        .w3eden .wpforms-field-medium{
            max-width: 100% !important;
            width: 100% !important;
        }

        .w3eden .input-group.input-group-lg .input-group-btn .btn {
            font-size: 11pt !important;
        }



    </style>
    <?php
    $uicolors = maybe_unserialize(get_option('__wpdm_ui_colors', array()));
    $primary = isset($uicolors['primary'])?$uicolors['primary']:'#4a8eff';
    $secondary = isset($uicolors['secondary'])?$uicolors['secondary']:'#4a8eff';
    $success = isset($uicolors['success'])?$uicolors['success']:'#18ce0f';
    $info = isset($uicolors['info'])?$uicolors['info']:'#2CA8FF';
    $warning = isset($uicolors['warning'])?$uicolors['warning']:'#f29e0f';
    $danger = isset($uicolors['danger'])?$uicolors['danger']:'#ff5062';

    ?>
    <style>

        :root{
            --color-primary: <?php echo $primary; ?>;
            --color-primary-rgb: <?php echo wpdm_hex2rgb($primary); ?>;
            --color-primary-hover: <?php echo isset($uicolors['primary'])?$uicolors['primary_hover']:'#4a8eff'; ?>;
            --color-primary-active: <?php echo isset($uicolors['primary'])?$uicolors['primary_active']:'#4a8eff'; ?>;
            --color-secondary: <?php echo $secondary; ?>;
            --color-secondary-rgb: <?php echo wpdm_hex2rgb($secondary); ?>;
            --color-secondary-hover: <?php echo isset($uicolors['secondary'])?$uicolors['secondary_hover']:'#4a8eff'; ?>;
            --color-secondary-active: <?php echo isset($uicolors['secondary'])?$uicolors['secondary_active']:'#4a8eff'; ?>;
            --color-success: <?php echo $success; ?>;
            --color-success-rgb: <?php echo wpdm_hex2rgb($success); ?>;
            --color-success-hover: <?php echo isset($uicolors['success_hover'])?$uicolors['success_hover']:'#4a8eff'; ?>;
            --color-success-active: <?php echo isset($uicolors['success_active'])?$uicolors['success_active']:'#4a8eff'; ?>;
            --color-info: <?php echo $info; ?>;
            --color-info-rgb: <?php echo wpdm_hex2rgb($info); ?>;
            --color-info-hover: <?php echo isset($uicolors['info_hover'])?$uicolors['info_hover']:'#2CA8FF'; ?>;
            --color-info-active: <?php echo isset($uicolors['info_active'])?$uicolors['info_active']:'#2CA8FF'; ?>;
            --color-warning: <?php echo $warning; ?>;
            --color-warning-rgb: <?php echo wpdm_hex2rgb($warning); ?>;
            --color-warning-hover: <?php echo isset($uicolors['warning_hover'])?$uicolors['warning_hover']:'orange'; ?>;
            --color-warning-active: <?php echo isset($uicolors['warning_active'])?$uicolors['warning_active']:'orange'; ?>;
            --color-danger: <?php echo $danger; ?>;
            --color-danger-rgb: <?php echo wpdm_hex2rgb($danger); ?>;
            --color-danger-hover: <?php echo isset($uicolors['danger_hover'])?$uicolors['danger_hover']:'#ff5062'; ?>;
            --color-danger-active: <?php echo isset($uicolors['danger_active'])?$uicolors['danger_active']:'#ff5062'; ?>;
            --color-green: <?php echo isset($uicolors['green'])?$uicolors['green']:'#30b570'; ?>;
            --color-blue: <?php echo isset($uicolors['blue'])?$uicolors['blue']:'#0073ff'; ?>;
            --color-purple: <?php echo isset($uicolors['purple'])?$uicolors['purple']:'#8557D3'; ?>;
            --color-red: <?php echo isset($uicolors['red'])?$uicolors['red']:'#ff5062'; ?>;
            --color-muted: rgba(69, 89, 122, 0.6);
            --wpdm-font: Cantarell, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        }


    </style>
    <?php do_action("wpdm_shortcode_iframe_head"); ?>
</head>
<body class="w3eden" style="background: transparent">

<?php
echo do_shortcode("[wpdm_package id='{$pid}']");
?>

<script>

    jQuery(function ($) {

        $('a').each(function () {
            $(this).attr('target', '_blank');
        });

        $('body').on('click','a', function () {
            $(this).attr('target', '_blank');
        });


        //window.parent.document.wpdm_adjust_frame_height("<?php echo $_REQUEST['frameid']; ?>", $(document).height());
        //window.parent.document.getElementById("<?php echo $_REQUEST['frameid']; ?>").style.height = $(document).height()+"px";
        //window.parent.document.getElementById("<?php echo $_REQUEST['frameid']; ?>").height = $(document).height()+"px";


    });

    function showModal() {
        jQuery('#wpdm-locks').modal('show');
    }
    showModal();
</script>
<div style="display: none">
<?php  if((isset($pack->PackageData['form_lock']) && $pack->PackageData['form_lock'] == 1)  || $pack->PackageData['base_price'] > 0) wp_footer(); ?>
<?php do_action("wpdm_shortcode_iframe_footer"); ?>
</div>
</body>
</html>
