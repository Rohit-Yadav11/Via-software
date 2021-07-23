<?php
/**
 * Dashborad >> Downloads >> Settings >> Premium Package
 *
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$settings = maybe_unserialize(get_option('_wpdmpp_settings'));
?>
<div class="wrap">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#tab1" data-toggle="tab"><?php _e("Basic", "wpdm-premium-packages"); ?></a></li>
        <li><a href="#tab2" data-toggle="tab"><?php _e("Payment", "wpdm-premium-packages"); ?></a></li>
        <li><a href="#tab3" data-toggle="tab"><?php _e("Taxes", "wpdm-premium-packages"); ?></a></li>
    </ul>
    <div class="tab-content">
        <section class="tab-pane active" id="tab1">
            <?php include_once("basic-options.php"); ?>
        </section>
        <section class="tab-pane" id="tab2">
            <?php include_once("payment-options.php"); ?>
        </section>
        <section class="tab-pane" id="tab3">
            <?php include_once("tax-options.php"); ?>
        </section>
    </div>
</div>
