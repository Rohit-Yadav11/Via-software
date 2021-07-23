<?php
/**
 * User: shahnuralam
 * Date: 5/6/17
 * Time: 8:14 PM
 */
if(!defined('ABSPATH')) die('!');
if(!\WPDM\Session::get('daily_sales')) {
    $daily_sales = wpdmpp_daily_sales('', '', date("Y-m-d", strtotime("-6 Days")), date("Y-m-d", strtotime("Tomorrow")));
    \WPDM\Session::set('daily_sales', $daily_sales);
} else
    $daily_sales = \WPDM\Session::get('daily_sales');


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

$this_Week = wpdmpp_total_sales('', '', $ldolw, date("Y-m-d", strtotime("Tomorrow")));

?>
<div class="w3eden">

    <script type="text/javascript">
        jQuery.getScript('https://www.gstatic.com/charts/loader.js', function () {
            google.charts.load('current', {'packages':['corechart']});
            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {
                var data = google.visualization.arrayToDataTable([
                    ['Date', '$', '#'],
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

    <div id="chart_div" style="width: 100%; height: 200px;" class="panel panel-default"></div>

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
            <span class="badge pull-right"><?php echo wpdmpp_currency_sign().$this_Week ?></span>
            <?php _e('This Week','wpdm-premium-packages'); ?>
        </div>
        <div class="list-group-item">
            <span class="badge pull-right"><?php echo wpdmpp_currency_sign().wpdmpp_total_sales('', '', $fdolw, $ldolw)?></span>
            <?php _e('Last Week','wpdm-premium-packages'); ?>
        </div>
        <div class="list-group-item">
            <span class="badge pull-right"><?php echo wpdmpp_currency_sign().wpdmpp_total_sales('', '', date("Y-m-01"), date("Y-m-d", strtotime("Tomorrow")))?></span>
            <?php _e('This Month','wpdm-premium-packages'); ?>
        </div>
        <div class="list-group-item">
            <span class="badge pull-right"><?php echo wpdmpp_currency_sign().wpdmpp_total_sales('', '', $fdolm, $ldolm)?></span>
            <?php _e('Last Month','wpdm-premium-packages'); ?>
        </div>
        <div class="list-group-item">
            <span class="badge pull-right"><?php echo wpdmpp_currency_sign().number_format(wpdmpp_total_sales('', '', date("Y-01-01"), date("Y-m-d", strtotime("Tomorrow"))),2,'.',',');?></span>
            <?php _e('This Year','wpdm-premium-packages'); ?>
        </div>
        <div class="list-group-item">
            <span class="badge pull-right"><?php echo wpdmpp_currency_sign().number_format(wpdmpp_total_sales('', '', "$last_year-01-01", date("Y-01-01")),2,'.',',');?></span>
            <?php _e('Last Year','wpdm-premium-packages'); ?>
        </div>
        <div class="list-group-item">
            <span class="badge pull-right"><?php echo wpdmpp_currency_sign().number_format(wpdmpp_total_sales('', '', "1990-01-01", date("Y-m-d", strtotime('Tomorrow'))),2,'.',',');?></span>
            <?php _e('Total','wpdm-premium-packages'); ?>
        </div>
    </div>

</div>


