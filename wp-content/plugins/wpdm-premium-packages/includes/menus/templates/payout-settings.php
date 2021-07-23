<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<form action="" method="post">
    <div class="panel panel-default">
        <div class="panel-heading"><?php _e("Commissions","wpdm-premium-packages");?></div>
        <table class="table table-striped">
            <tr>
                <th align="left"><?php _e("Role","wpdm-premium-packages");?></th>
                <th align="left" style="width: 130px"><?php _e("Commission (%)","wpdm-premium-packages");?></th>
            </tr>
            <tr>
                <td><?php _e("Default","wpdm-premium-packages");?> </td>
                <td><input  class="form-control input-sm" style="width: 80px" type="number" size="8" name="comission[default]" value="<?php echo isset($comission['default']) ? $comission['default'] : ''; ?>"></td>
            </tr>
            <?php
            global $wp_roles;
            $roles = array_reverse($wp_roles->role_names);
            foreach( $roles as $role => $name ) {
                if(  isset($currentAccess) ) $sel = (in_array($role,$currentAccess))?'checked':''; ?>
                <tr>
                    <td><?php echo $name; ?> (<?php echo $role; ?>) </td>
                    <td><input type="number" class="form-control input-sm" style="width: 80px" size="8" name="comission[<?php echo $role; ?>]" value="<?php echo (is_array($comission) && isset($comission[$role]))?(double)$comission[$role]:''; ?>"></td>
                </tr>
            <?php } ?>
            <tr>
                <td colspan="2"><input type="submit" class="btn btn-primary" value="Submit" name="csub"></td>
            </tr>
        </table>
    </div>
</form>
<div class="panel panel-default">
    <div class="panel-heading"><?php _e("Payout Duration","wpdm-premium-packages");?></div>
    <div class="panel-body">
        <form action="" method="post">
            <?php _e("Duration of payout to mature :","wpdm-premium-packages");?>
            <input class="form-control input-sm" style="width: 80px;display: inline" type="number" name="payout_duration" value="<?php echo $payout_duration;?>" >  <?php _e("Days","wpdm-premium-packages");?>
            <br/><br/><input type="submit" class="btn btn-primary" name="psub" value="Submit">
        </form>
    </div>
</div>
