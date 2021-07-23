<?php
/**
 * User: shahnuralam
 * Date: 2/6/18
 * Time: 1:06 PM
 */
if (!defined('ABSPATH')) die();

if(is_user_logged_in()){

$daily_sales = wpdmpp_daily_sales(get_current_user_id(), '');
$dates = array_keys($daily_sales['quantities']);
foreach ($dates as &$date) {
    $date = date("M d", strtotime($date));
}
?>
<div class="w3eden">
<div id="wpdmpp-seller-dashboard">
    <div class="row">
        <div class="col-md-3">
            <div class="statistic-box purple">
                <div>
                    <div class="number"><?php echo count_user_posts(get_current_user_id(), 'wpdmpro'); ?></div>
                    <div class="caption">
                        <div>Products</div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="statistic-box yellow">
                <div>
                    <div class="number"><?php echo (int)wpdm_total_downloads(get_current_user_id()); ?></div>
                    <div class="caption">
                        <div>Downloads</div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="statistic-box red">
                <div>
                    <div class="number"><?php echo wpdm_total_views(get_current_user_id()); ?></div>
                    <div class="caption">
                        <div>Views</div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="statistic-box green">
                <div>
                    <div class="number"><?php echo wpdmpp_currency_sign(get_current_user_id()) . wpdmpp_total_sales(get_current_user_id()); ?></div>
                    <div class="caption">
                        <div>Sales</div>
                    </div>

                </div>
            </div>
        </div>


        <div class="col-md-12">
            <section class="panel panel-default" style="overflow: hidden">
                <div class="panel-heading" style="padding-bottom: 10px">
                    <h3 class="panel-title"><?php _e('This Month','wpdm-premium-packages'); ?></h3>
                </div>
                <?php /* still in to do list
                    <div class="widget-chart-combo-header bg-primary hidden-xs-down">
                        <div class="widget-chart-combo-header-left">
                            <select class="select2 select2-white">
                                <option value="">All Products</option>
                            </select>
                        </div>
                        <div class="widget-chart-combo-header-right"
                             style="width: 400px;padding: 0 90px 0 0;max-width: 100%;">
                            <div class="input-group text-center">
                                <input style="width: 50%;height:38px;line-height: 38px"
                                       placeholder="From Date" type="date" class="form-control"
                                       value="<?php echo date("Y-m-01"); ?>">
                                <input style="width: 50%;height:38px;line-height: 38px"
                                       placeholder="To Date" type="date" class="form-control"
                                       value="<?php echo date("Y-m-d", strtotime("last day of this month")); ?>">
                            </div>
                            <button type="button" class="btn btn-grey" style="width: 80px">Apply</button>
                        </div>
                    </div>
                        */ ?>
                <div class="panel-body">
                    <div class="widget-chart-combo-content-in">
                        <div style="padding: 0 20px;">
                            <canvas id="downloadhis" style="width:100%;max-height: 350px"></canvas>
                            <small>
                                <span class="color-blue pull-right"><i class="fa fa-adjust"></i> <?php _e('Amount ( Sales )','wpdm-premium-packages'); ?></span>
                                <span class="color-purple"><i class="fa fa-adjust"></i> <?php _e('Item Count ( Sales )','wpdm-premium-packages'); ?></span>
                            </small>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="row">

                        <div class="col-md-3">
                            <div class="number"><?php echo array_sum($daily_sales['quantities']); ?></div>
                            <?php _e('Sales Quantity','wpdm-premium-packages'); ?>
                        </div>

                        <div class="col-md-3">
                            <div class="number"><?php echo wpdmpp_currency_sign(get_current_user_id()) . number_format(array_sum($daily_sales['sales']), 2, '.', ','); ?></div>
                            <?php _e('Sales Amount','wpdm-premium-packages'); ?>
                        </div>

                        <div class="col-md-3">
                            <div class="number"><?php echo wpdmpp_currency_sign(get_current_user_id()) . number_format(max($daily_sales['sales']), 2); ?></div>
                            <?php _e('Max Sale','wpdm-premium-packages'); ?>
                        </div>

                        <div class="col-md-3">
                            <div class="number"><?php echo wpdmpp_currency_sign(get_current_user_id()) . number_format(array_sum($daily_sales['sales']) / 30, 2); ?>
                                / <?php _e('day','wpdm-premium-packages'); ?>
                            </div>
                            <?php _e('Average Sale','wpdm-premium-packages'); ?>
                        </div>

                    </div>
                </div>
            </section><!--.widget-chart-combo-->
        </div>
        <div class="col-md-6 dahsboard-column">
            <section class="box-typical box-typical-dashboard panel panel-default scrollable">
                <header class="box-typical-header panel-heading">
                    <h3 class="panel-title"><?php _e('Recent Orders','wpdm-premium-packages'); ?></h3>
                </header>
                <table class="table table-striped wpdm-table-clean">
                    <tr>
                        <th>
                            <div>Date</div>
                        </th>
                        <th>
                            <div>Product Name</div>
                        </th>
                        <th align="center">
                            <div>Price</div>
                        </th>
                    </tr>
                    <?php $msps = wpdmpp_recent_sales(get_current_user_id());
                    foreach ($msps as $product) { ?>

                        <tr>
                            <td><?php echo date(get_option("date_format") . " H:i", $product->time_stamp); ?></td>
                            <td>
                                <?php echo $product->post_title; ?>
                            </td>
                            <td class="color-blue-grey" nowrap
                                align="center"><?php echo wpdmpp_currency_sign(get_current_user_id()) . number_format($product->price, 2, '.', ','); ?></td>
                        </tr>
                    <?php } ?>

                </table>
            </section><!--.box-typical-dashboard-->
        </div>
        <div class="col-md-6 dahsboard-column">
            <section class="box-typical box-typical-dashboard panel panel-default scrollable">
                <header class="box-typical-header panel-heading">
                    <h3 class="panel-title"><?php _e('Top Selling Products','wpdm-premium-packages'); ?></h3>
                </header>
                <table class="table table-striped wpdm-table-clean">
                    <tr>
                        <th>
                            <div>Product Name</div>
                        </th>
                        <th align="center">
                            <div>Sales Amount</div>
                        </th>
                    </tr>
                    <?php $msps = wpdmpp_top_sellings_products(get_current_user_id());
                    foreach ($msps as $product) { ?>

                        <tr>
                            <td>
                                <?php echo get_the_title($product->pid); ?>
                            </td>
                            <td class="color-blue-grey" nowrap
                                align="center"><?php echo wpdmpp_currency_sign(get_current_user_id()) . number_format($product->sales, 2, '.', ','); ?></td>
                        </tr>
                    <?php } ?>

                </table>
            </section><!--.box-typical-dashboard-->
        </div>
    </div>


</div>
</div>
    <?php wp_print_scripts("wpdmpp-seller-dashboard"); ?>
    <script type="text/javascript">

        var overlayData = {
            labels: ['<?php echo implode("','", $dates); ?>'],
            scaleFontColor: '#fff',
            scaleGridLineColor: 'rgba(255, 255, 255, 0.4)',
            scaleLineColor: 'rgba(0, 0, 0, 0)',
            datasets: [{
                label: "Sales",
                type: "bar",
                yAxesGroup: "1",
                fillColor: "rgba(0, 143, 251,0.5)",
                strokeColor: "rgba(0, 143, 251,0.8)",
                highlightFill: "rgba(0, 143, 251,0.75)",
                highlightStroke: "rgba(0, 143, 251,1)",
                data: [<?php echo implode(",", $daily_sales['sales']); ?>]
            }, {
                label: "Quantiries",
                type: "line",
                yAxesGroup: "2",
                fillColor: "rgba(172, 107, 236,0.5)",
                strokeColor: "rgba(172, 107, 236,0.8)",
                highlightFill: "rgba(172, 107, 236,0.75)",
                highlightStroke: "rgba(172, 107, 236,1)",
                data: [<?php echo implode(",", $daily_sales['quantities']); ?>]
            }],
            yAxes: [{
                name: "1",
                scalePositionLeft: false,
                scaleFontColor: "rgba(0, 143, 251,0.8)"
            }, {
                name: "2",
                scalePositionLeft: true,
                scaleFontColor: "rgba(172, 107, 236,1)"
            }],
            legend: {
                display: true,
                labels: {
                    fontColor: 'rgb(255, 255, 255)'
                }
            },
            scales: {
                xAxes: [{
                    gridLines: {
                        color: "rgba(255, 255, 255, 0.2)",
                    }
                }],
                yAxes: [{
                    gridLines: {
                        color: "rgba(255, 255, 255, 0.2)",
                    }
                }]
            }
        };

        window.onload = function () {


            window.myOverlayChart = new Chart(document.getElementById("downloadhis").getContext("2d")).Overlay(overlayData, {
                populateSparseData: true,
                overlayBars: false,
                datasetFill: true
            });
        };


    </script>

<?php } else {
    echo do_shortcode("[wpdm_login_form logo='".get_site_icon_url()."']");
}

