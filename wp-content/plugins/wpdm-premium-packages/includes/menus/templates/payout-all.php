<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

global $wpdb;
$sql = "select * from {$wpdb->prefix}ahm_withdraws order by date desc";
$payouts = $wpdb->get_results($sql);
?>
<form action="" method="post">
    <select name="payout_status" class="form-control wpdm-custom-select" style="display: inline-block;width: 200px">
        <option value="-1"><?php _e('Payout Status:','wpdm-premium-packages'); ?></option>
        <option value="0"><?php _e('Pending','wpdm-premium-packages'); ?></option>
        <option value="1"><?php _e('Completed','wpdm-premium-packages'); ?></option>
        <option value="2"><?php _e('Cancel','wpdm-premium-packages'); ?></option>
    </select>
    <button type="submit" name="pschange" class="btn btn-info"><?php _e('Apply', 'wpdm-premium-packages');  ?></button>
    <table cellspacing="0" class="table table-striped">
        <thead>
        <tr>
            <th><?php echo __("Name", "wpdm-premium-packages"); ?></th>
            <th><?php _e("PayPal Account","wpdm-premium-packages");?></th>
            <th><?php echo __("Amount", "wpdm-premium-packages"); ?></th>
            <th style="width: 150px"><?php echo __("Status", "wpdm-premium-packages"); ?></th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th><?php echo __("Name", "wpdm-premium-packages"); ?></th>
            <th><?php _e("PayPal Account","wpdm-premium-packages");?></th>
            <th><?php echo __("Amount", "wpdm-premium-packages"); ?></th>
            <th style="width: 150px"><?php echo __("Status", "wpdm-premium-packages"); ?></th>
        </tr>
        </tfoot>
        <tbody>
        <?php
        foreach ($payouts as $payout) {
            $sta = 'Completed';
            if ($payout->status == 0) $st = "Pending"; else if ($payout->status == 1) $st = "Completed";
            if($st == 'Completed') $sta = 'Pending';
            $paypal = get_user_meta($payout->uid, 'payment_account', true);
            $_pa = get_user_meta($payout->uid, '__wpdm_public_profile', true);
            $_pa = isset($_pa['paypal'])?$_pa['paypal']:'';
            $paypal = $_pa!= ''?$_pa:$paypal;
            echo "<tr><td><a href='user-edit.php?user_id=" . $payout->uid . "'>" . __(get_userdata($payout->uid)->display_name, "wpdm-premium-packages") . "</a></td><td>{$paypal}</td><td>" . wpdmpp_currency_sign() . number_format($payout->amount, 2) . "</td><td><button type='button' class='pull-right btn btn-xs btn-primary btn-payout-status ttip' title='Change Status' data-status='{$sta}' data-id='{$payout->id}'><i class='fas fa-sync'></i></button><span id='pstatus-{$payout->id}'>" . __($st, "wpdm-premium-packages") . "</span></td></tr>";
        }
        ?>

        </tbody>
    </table>
</form>

<script>
    jQuery(function ($) {
        $('.btn-payout-status').on('click', function () {
            if(!confirm("Changing payout status\r\n–––––––––––––––––––\r\nAre you sure?")) return false;
            var $this = $(this);
            var $pst = $('#pstatus-'+$this.data('id'));
            $this.html("<i class='fas fa-sync fa-spin'></i>");
            $.post(ajaxurl, {action: 'wpdmpp_change_payout_status', id: $(this).data('id'), __psnonce: '<?php echo wp_create_nonce(NONCE_KEY); ?>'}, function (res) {
                $this.html("<i class='fas fa-sync'></i>");
                $pst.html(res.status);

            });
        });
    });
</script>
