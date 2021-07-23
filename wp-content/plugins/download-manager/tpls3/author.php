<?php
/**
 * User: shahnuralam
 * Date: 6/25/18
 * Time: 12:29 AM
 */
if (!defined('ABSPATH')) die();
?>
<div class="panel panel-default panel-author">
    <div class="panel-body text-center">
        <a href="<?php echo get_author_posts_url($user->ID); ?>"><?php echo get_avatar($user->user_email, 512, '', $user->display_name, array('class' => 'img-circle')); ?></a>
        <h3 class="author-name"><a href="<?php echo get_author_posts_url($user->ID); ?>"><?php echo $user->display_name; ?></a></h3>
        <small class="text-muted"><?php echo $apc = count_user_posts( $user->ID , "wpdmpro"  ); ?> <?php echo $apc>1?'Items':'Item'; ?></small>
    </div>
</div>

