<?php
/**
 * User: shahnuralam
 * Date: 24/11/18
 * Time: 4:14 PM
 */
?>


<div class="w3eden">
<div class="modal fade" id="wpdmModalLoginForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">


                <?php echo do_shortcode("[wpdm_login_form]"); ?>


    </div>
</div>
    <div class="btn-wpdm-modal-login pos-left-top">
        <?php if(!is_user_logged_in()){ ?>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#wpdmModalLoginForm">
        <i class="fas fa-lock"></i> <?php _e('Login'); ?>
    </button>
        <?php } else { ?>
            <a class="btn btn-success" href="<?php wpdm_user_dashboard_url(); ?>">
                <i class="fas fa-user-circle"></i> <?php _e('Account Dashboard'); ?>
            </a>
        <?php } ?>
    </div>
</div>
<style>
    .btn-wpdm-modal-login{
        position: fixed !important;
        z-index: 99 !important;
    }
    .btn-wpdm-modal-login .btn{
        line-height: 32px;
        height: 32px;
        font-size: 8pt;
        padding: 0 15px !important;
    }

    .pos-left-top{
        top: 100px;
        left: 30px;
    }
    .pos-left-top .btn{
        transform-origin: 0 0;
        border-radius: 4px 4px 0px 0 !important;
        transform: rotate(90deg);
    }

    .pos-right-top{
        top: 100px;
        right: 0;
    }
    .pos-right-top .btn{
        transform-origin: right bottom;
        border-radius: 4px 4px 0 0 !important;
        transform: rotate(-90deg);
    }
</style>

