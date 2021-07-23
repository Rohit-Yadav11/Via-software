<?php
/**
 * Pricing sales overview metabox for premium package. Displayed on edit package screen.
 *
 * This template can be overridden by copying it to yourtheme/download-manager/metaboxes/product-sales-overview.php.
 *
 * @version     1.0.0
 */
if (!defined('ABSPATH')) {
    exit;
}
global $wpdb;

$pid = wpdm_query_var('post', 'int');
$total_sales = wpdmpp_total_sales('', $pid, "1990-01-01", date("Y-m-d", strtotime('Tomorrow')));
if((float)$total_sales == 0) {
    if ((float)wpdmpp_effective_price($pid) <= 0) {
        die('<div style="padding:20px;text-align: center">Free Item!</div>');
    } else
        die('<div style="padding:20px;text-align: center">No Sale Yet!</div>');
}

$daily_sales = wpdmpp_daily_sales('',$pid, date("Y-m-d", strtotime("-6 Days")), date("Y-m-d", strtotime("Tomorrow")));

$date = new DateTime();
$date->modify('this week -6 days');
$fdolw =  $date->format('Y-m-d');

$date = new DateTime();
$date->modify('this week');
$ldolw =  $date->format('Y-m-d');

$date = new DateTime();
$date->modify('first day of last month');
$fdolm = $date->format('Y-m-d');

$date = new DateTime();
$date->modify('first day of this month');
$ldolm = $date->format('Y-m-d');

$dn = 0;

$last_year = date("Y")-1;

$total_renews = $pid > 0 ? $wpdb->get_var("SELECT sum(ori.price) as total_renews FROM `wp_ahm_order_renews` orn, `wp_ahm_order_items`  ori WHERE orn.order_id = ori.oid and ori.pid = ".$pid) : 0.00;

?>
<div class="w3eden">

    <script type="text/javascript">
        jQuery.getScript('https://www.gstatic.com/charts/loader.js', function () {

            google.charts.load('current', {'packages':['corechart']});
            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {
                var data = google.visualization.arrayToDataTable([
                    ['Date', 'Amount ($)', 'Quantity (#)'],
                    <?php foreach ($daily_sales['sales'] as $date => $sale){ ?>
                    ['<?php echo date("D", strtotime($date)); ?>',  <?php echo $sale;?>,      <?php echo $daily_sales['quantities'][$date];?>] <?php if($dn++ < 6) echo ','; else break; ?>
                    <?php } ?>
                ]);

                var options = {
                    title: '<?php _e('Last 7 Days Sales','wpdm-premium-packages'); ?>',
                    hAxis: {title: 'Day',  titleTextStyle: {color: '#333'}},
                    vAxis: {minValue: 0}
                };

                var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
                chart.draw(data, options);
            }
        });
    </script>

    <div id="chart_div" style="width: 100%; height: 200px;overflow: hidden" class="panel panel-default"></div>

    <div class="row text-center">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-body"><h2><?php echo wpdmpp_currency_sign().number_format($daily_sales['sales'][date("Y-m-d")],2); ?></h2></div>
                <div class="panel-footer"><?php _e('Today','wpdm-premium-packages'); ?></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-body"><h2><?php echo wpdmpp_currency_sign().number_format($daily_sales['sales'][date("Y-m-d", strtotime("Yesterday"))],2); ?></h2></div>
                <div class="panel-footer"><?php _e('Yesterday','wpdm-premium-packages'); ?></div>
            </div>
        </div>
    </div>
    <div class="list-group" style="margin: 0">

        <div class="list-group-item">
            <span class="badge pull-right"><?php echo wpdmpp_currency_sign().wpdmpp_total_sales('', $pid, $ldolw, date("Y-m-d", strtotime("Tomorrow")))?></span>
            <?php _e('This Week','wpdm-premium-packages'); ?>
        </div>
        <div class="list-group-item">
            <span class="badge pull-right"><?php echo wpdmpp_currency_sign().wpdmpp_total_sales('', $pid, $fdolw, $ldolw)?></span>
            <?php _e('Last Week','wpdm-premium-packages'); ?>
        </div>
        <div class="list-group-item">
            <span class="badge pull-right"><?php echo wpdmpp_currency_sign().wpdmpp_total_sales('', $pid, date("Y-m-01"), date("Y-m-d", strtotime("Tomorrow")))?></span>
            <?php _e('This Month','wpdm-premium-packages'); ?>
        </div>
        <div class="list-group-item">
            <span class="badge pull-right"><?php echo wpdmpp_currency_sign().wpdmpp_total_sales('', $pid, $fdolm, $ldolm)?></span>
            <?php _e('Last Month','wpdm-premium-packages'); ?>
        </div>
        <div class="list-group-item">
            <span class="badge pull-right"><?php echo wpdmpp_currency_sign().number_format(wpdmpp_total_sales('', $pid, date("Y-01-01"), date("Y-m-d", strtotime("Tomorrow"))),2,'.',',');?></span>
            <?php _e('This Year','wpdm-premium-packages'); ?>
        </div>
        <div class="list-group-item">
            <span class="badge pull-right"><?php echo wpdmpp_currency_sign().number_format(wpdmpp_total_sales('', $pid, "$last_year-01-01", date("Y-01-01")),2,'.',',');?></span>
            <?php _e('Last Year','wpdm-premium-packages'); ?>
        </div>
        <div class="list-group-item">
            <span class="badge pull-right"><?php echo wpdmpp_currency_sign().number_format($total_sales,2,'.',',');?></span>
            <?php _e('Total Sales','wpdm-premium-packages'); ?>
        </div>
        <div class="list-group-item">
            <span class="badge pull-right"><?php echo wpdmpp_currency_sign().number_format($total_renews,2,'.',',');?></span>
            <?php _e('Total Renews','wpdm-premium-packages'); ?>
        </div>
        <div class="list-group-item">
            <span class="badge pull-right"><?php echo wpdmpp_currency_sign().number_format(($total_sales + $total_renews),2,'.',',');?></span>
            <?php _e('Total Earning','wpdm-premium-packages'); ?>
        </div>
    </div>

</div>
