<?php
/**
 * User: shahnuralam
 * Date: 4/23/18
 * Time: 10:42 PM
 */
if (!defined('ABSPATH')) die();

if(count($renews) > 0){
    ?>

    <div class="panel panel-default panel-invoices dashboard-panel">
        <div class="panel-heading">
            <?php _e('Order Renew Invoices', 'wpdm-premium-packages'); ?>
        </div>
    <table class="table table-hover">
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
            <tr id="renew_row_<?php echo $renew->ID; ?>">
                <td><?php echo date(get_option('date_format'), $renew->date); ?></td>
                <td style="width: 150px">
                    <a onclick="window.open('?id=<?php echo wpdm_query_var('id', 'txt'); ?>&wpdminvoice=1&renew=<?php echo $renew->date; ?>','Invoice','height=720, width = 850, toolbar=0'); return false;" href="#" class="btn btn-xs btn-primary btn-invoice">Invoice</a>
                    <a href="#" class="btn btn-danger btn-xs" onclick="return delete_renew_entry(<?php echo $renew->ID; ?>, '<?php echo wp_create_nonce(NONCE_KEY); ?>');"><?php _e('Delete', 'wpdm-premium-packages'); ?></a>
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
    </div>




<?php }