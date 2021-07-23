<?php
/**
 * User: shahnuralam
 * Date: 4/23/18
 * Time: 10:42 PM
 */
if (!defined('ABSPATH')) die();
if(count($renews) > 0){
    ?>

    <div class="card card-default card-invoices">
        <div class="card-header">
            <?php _e('Order Renew Invoices', 'wpdm-premium-packages'); ?>
        </div>
    <table class="table table-hover  wpdm-table-clean">
        <thead>
            <tr>
                <th><?php _e('Renew Date', 'wpdm-premium-packages'); ?></th>
                <th style="width: 120px;"><?php _e('Invoice', 'wpdm-premium-packages'); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
        foreach ($renews as $renew) {
            ?>
            <tr>
                <td><?php echo date(get_option('date_format'), $renew->date); ?></td>
                <td><a onclick="window.open('?id=<?php echo $orderObj->oid; ?>&wpdminvoice=1&renew=<?php echo $renew->date; ?>','Invoice','height=720, width = 850, toolbar=0'); return false;" href="#" class="btn btn-xs btn-primary btn-invoice">Get Invoice</a></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
    </div>




<?php }