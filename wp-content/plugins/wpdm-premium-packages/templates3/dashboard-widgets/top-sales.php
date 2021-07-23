<?php
/**
 * User: shahnuralam
 * Date: 5/6/17
 * Time: 11:42 PM
 */
if(!defined('ABSPATH')) die('!');
global $wpdb;

$fdolm      = date("Y-m-d", strtotime("-90 days"));
$ldolm      = date("Y-m-d");
$topsales   = wpdmpp_top_sellings_products("", $fdolm,  $ldolm, 0, 10)
?>
<div class="w3eden">
    <table class="table table-bordered table-hover table-striped" style="margin-bottom: 0">
        <tr>
            <th><?php _e('Item Name','wpdm-premium-packages'); ?></th>
            <th><?php _e('Quantity','wpdm-premium-packages'); ?></th>
            <th><?php _e('Amount','wpdm-premium-packages'); ?></th>
        </tr>

        <?php foreach ($topsales as $item){ ?>
            <tr>
                <td><?php echo get_the_title($item->pid); ?></td>
                <td><?php echo $item->quantities; ?></td>
                <td><?php echo wpdmpp_currency_sign().number_format($item->sales,2,'.', ','); ?></td>
            </tr>
        <?php } ?>
    </table>
</div>