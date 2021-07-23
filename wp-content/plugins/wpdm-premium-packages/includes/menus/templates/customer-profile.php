<?php
if(!defined("ABSPATH")) die("Shit happens!");
global $wpdb;
$uid = wpdm_query_var('id', 'int');
$customer = get_user_by('id', $uid);

?>
<div class="w3eden payout-entries">
    <div class="panel panel-default" id="wpdm-wrapper-panel">
        <div class="panel-heading">
            <b><i class="fas fa-user-graduate color-purple"></i> &nbsp; <?php _e("Customer Profile","wpdm-premium-packages");?></b>

        </div>
        <div class="panel-body" style="padding-top: 60px">


            <div class="container">
                <div class="row">
                    <div class="col-md-3">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <?= is_object($customer) ? get_avatar($customer->user_email, 512):''; ?>
                            </div>
                        </div>
                        <h2><?= is_object($customer) ? $customer->display_name : esc_attr__( 'User Delete / Not Found', 'wpdm-premium-packages' ) ; ?></h2>
                        <div class="list-group">
                            <?php foreach ($tabs as $view => $_tab){ ?>
                                <a href="edit.php?post_type=wpdmpro&page=customers&view=<?=$view;?>&id=<?=$uid; ?>" class="list-group-item <?= $tab === $view ? 'active' : ''; ?>"><?=$_tab['name'];?></a>
                            <?php } ?>
                            <a href="#" data-target="#wppmsg-to-author" data-user="<?php echo (defined('PM_BASE_DIR')) ? \PrivateMessage\__\__Crypt::encrypt($uid) : ''; ?>" data-toggle="modal" class="list-group-item"><?=esc_attr__( 'Send Message', 'download-manager' );?></a>
                        </div>
                        <?php do_action("wpdm_customer_profile_admin_sidebar_top", $customer); ?>
                        <?php if (is_object($customer)) { ?>
                        <hr/>
                        <div class="panel panel-default card-plain">
                            <div class="panel-heading"><?=esc_attr__( 'Member Since', 'download-manager' );?></div>
                            <div class="panel-body"><?= wp_date(get_option('date_format')." ".get_option('time_format'), strtotime($customer->user_registered)); ?></div>
                        </div>
                        <div class="panel panel-default card-plain">
                            <div class="panel-heading"><?=esc_attr__( 'Email', 'download-manager' );?></div>
                            <div class="panel-body"><?= $customer->user_email; ?></div>
                        </div>
                        <?php } ?>
                        <?php do_action("wpdm_customer_profile_admin_sidebar_bottom", $customer); ?>
                    </div>
                    <div class="col-md-9">
                        <div id="wpdmdd-profile-content">
                             <?php call_user_func($tabs[$tab]['callback']); ?>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </div>

    <?php if(defined('PM_BASE_DIR')) {
        ?>
        <script src="<?=PM_ASSET_URL ?>js/wppmsg.js"></script>
        <?php
        include PM_BASE_DIR.'src/Message/views/pm-modal-form.php';
    } else {
        ?>
        <div class="modal" data-keyboard="false" tabindex="-1" role="dialog" id="wppmsg-to-author">
            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title p-0 m-0"><?= __("Send Message", PM_TEXT_DOMAIN); ?></h5>
                    </div>
                    <div class="modal-body">

                        <p><?=esc_attr__( 'You need the WordPress Private Message plugin to active this feature!', 'download-manager' );?></p>

                    </div>
                    <div class="modal-footer bg-light">
                        <a target="_blank" href="https://www.wpdownloadmanager.com/download/wordpress-private-message/" class="btn btn-success btn-block"><?php _e('Get Private Message Plugin', PM_TEXT_DOMAIN) ?></a>
                    </div>
                </div>
            </div>

        </div>
        <?php
    }
    ?>

</div>
