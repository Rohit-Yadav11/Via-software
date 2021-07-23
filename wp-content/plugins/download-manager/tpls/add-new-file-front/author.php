<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 15/12/19 18:53
 */

if(!defined("ABSPATH")) die();

$adminAccess = maybe_unserialize(get_option( '__wpdm_front_end_admin', array()));
$allowed = array_intersect($adminAccess, $current_user->roles);
$roles = maybe_unserialize(get_option( '__wpdm_front_end_access', array()));
$cauthor = $pid ? $post->post_author : $current_user->ID;
if(count($allowed) > 0){
    ?>
    <div class="card card-default">
        <div class="card-header"><?php _e('Author', 'download-manager') ?></div>
        <div class="card-body">
            <?php wp_dropdown_users( array( 'name' => 'pack[post_author]', 'class' => 'form-control', 'role__in' => $roles, 'selected' => $cauthor ) ); ?>
        </div>
    </div>
    <?php
}
?>
