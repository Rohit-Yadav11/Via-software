<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

if (isset($_POST['psub']))
    update_option("wpdmpp_payout_duration", absint( $_POST['payout_duration']) );

if (isset($_POST['csub']))
    update_option("wpdmpp_user_comission", wpdm_sanitize_array($_POST['comission']));

if (isset($_POST['pschange'])) {
    global $wpdb;
    if (isset($_POST['payout_status']) && $_POST['payout_status'] != "-1" && $_POST['payout_status'] != "2") {
        if (isset($_POST['poutid'])) {
            foreach ($_POST['poutid'] as $payout_id) {
                $wpdb->update(
                    "{$wpdb->prefix}ahm_withdraws",
                    array(
                        'status' => sanitize_text_field( $_POST['payout_status'] )
                    ),
                    array('ID' => $payout_id),
                    array(
                        '%d',
                    ),
                    array('%d')
                );
            }
        }
    }

    if (isset($_POST['payout_status']) && $_POST['payout_status'] == "2") {
        if (isset($_POST['poutid'])) {
            foreach ($_POST['poutid'] as $payout_id) {
                $wpdb->query("delete from {$wpdb->prefix}ahm_withdraws where id={$payout_id}");
            }
        }
    }
}

$payout_duration = get_option("wpdmpp_payout_duration");
$comission = get_option("wpdmpp_user_comission");
?>


<div class="w3eden payout-entries">
        <div class="panel panel-default" id="wpdm-wrapper-panel">
            <div class="panel-heading">
                <b><i class="fa fa-bars color-purple"></i> &nbsp; <?php _e("Payouts","wpdm-premium-packages");?></b>
            </div>


        <ul class="nav nav-tabs nav-wrapper-tabs" style="padding: 60px 10px 0 10px;background: #f5f5f5">
            <li class="active"><a href="#tab1" data-toggle="tab"><?php echo __("All Payouts", "wpdm-premium-packages"); ?></a></li>
            <li><a href="#tab2" data-toggle="tab"><?php echo __("Dues", "wpdm-premium-packages"); ?></a></li>
            <li><a href="#tab3" data-toggle="tab"><?php echo __("Payout Settings", "wpdm-premium-packages"); ?></a></li>
        </ul>

        <div class="tab-content panel-body">
            <div class="tab-pane active" id="tab1">
                <?php include_once("payout-all.php"); ?>
            </div>
            <div class="tab-pane" id="tab2">
                <?php include_once("payout-dues.php"); ?>
            </div>
            <div class="tab-pane" id="tab3">
                <?php include_once("payout-settings.php"); ?>
            </div>
        </div>

</div>
</div>
<style>div.notice{ display: none; }</style>