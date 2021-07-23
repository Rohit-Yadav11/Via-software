<?php
$type = wpdm_query_var('type', array('validate' => 'txt', 'default' => 'overview'));
$base_page_uri = "edit.php?post_type=wpdmpro&page=wpdm-stats";
?>
<div class="wrap w3eden">

    <div class="panel panel-default" id="wpdm-wrapper-panel">
        <div class="panel-heading">
            <a class="btn btn-primary btn-sm pull-right" href="<?= $base_page_uri; ?>&task=export" style="font-weight: 400">
                <i class="sinc far fa-arrow-alt-circle-down"></i> <?php _e("Export History", 'download-manager'); ?>
            </a>
            <b><i class="fas fa-chart-line color-purple"></i> &nbsp; <?php echo __("Download Statistics", "download-manager"); ?></b>

        </div>
        <!-- Tabs -->
        <ul id="tabs" class="nav nav-tabs nav-wrapper-tabs" style="padding: 60px 10px 0 10px;background: #f5f5f5">
            <!-- overview -->
            <li <?= ($type == 'overview' && !isset($_GET['task'])) ? 'class="active"' : ''; ?>>
                <a href='<?= $base_page_uri; ?>'> <?php echo __("Overview", "download-manager"); ?> </a>
            </li>
            <!-- history -->
            <li <?= ($type == 'history') ? 'class="active"' : ''; ?>>
                <a href='<?= $base_page_uri; ?>&type=history'><?php echo __("Download History", "download-manager"); ?></a>
            </li>

            <li <?= ($type == 'insight') ? 'class="active"' : ''; ?>>
                <a href='<?= $base_page_uri; ?>&type=insight'><?php echo __("Insights", "download-manager"); ?></a>
            </li>
            <!--<li <?php /*if(isset($_GET['type'])&&$_GET['type']=='pvdpu'){ */ ?>class="active"<?php /*} */ ?>><a href='edit.php?post_type=wpdmpro&page=wpdm-stats&type=pvdpu'><?php /*echo __( "Package vs Date" , "download-manager" ); */ ?></a></li>
            <li <?php /*if(isset($_GET['type'])&&$_GET['type']=='pvupd'){ */ ?>class="active"<?php /*} */ ?>><a href='edit.php?post_type=wpdmpro&page=wpdm-stats&type=pvupd'><?php /*echo __( "Package vs User" , "download-manager" ); */ ?></a></li>-->
        </ul>

        <div class="tab-content" style="padding: 15px;">
            <?php
            include(WPDM_BASE_DIR . "admin/tpls/stats/{$type}.php");

            ?>
        </div>
    </div>