<?php
global $current_user, $wpdb;

global $wp_roles;
$roles = array_reverse($wp_roles->role_names);
$val = get_option( 'wp_user_roles' );
$levels =  array();
foreach ($current_user->roles as $role) {
    $levels[$role] = isset($roles[$role])?$roles[$role]:$role;
}


?><div class="row">
    <div class="col-md-4">
        <div class="panel panel-default dashboard-panel">
            <div class="panel-heading">
                <div style="float:right;border: 0 !important;" class="role-tabs nav nav-tabs" role="tablist">
                    <?php $rc = 0; foreach ($levels as $role => $name){ $rc++; ?>
                        &nbsp;<a href="#<?php echo $role; ?>" class="<?php if($rc==1) echo  'active'; ?>" data-toggle="tab"><i class="fa fa-circle"></i></a>
                    <?php } ?>

                </div>
                <?php _e( "User Level" , "download-manager" ); ?>

            </div>
            <div class="panel-body tab-content" style="margin: 0">
                <?php $rc = 0; foreach ($levels as $role => $name){ $rc++; ?>
                    <h3 class="tab-pane <?php if($rc==1) echo  ' active'; ?>"  role="tabpanel" aria-labelledby="<?php echo $role; ?>" id="<?php echo $role; ?>"><?php echo $name; ?></h3>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-default dashboard-panel">
            <div class="panel-heading"><?php _e( "Total Downloads" , "download-manager" ); ?></div>
            <div class="panel-body">
                <h3><?php echo number_format($wpdb->get_var("select count(*) from {$wpdb->prefix}ahm_download_stats where uid = '{$current_user->ID}'"),0,'.',','); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-default dashboard-panel">
            <div class="panel-heading"><?php _e( "Today's Download" , "download-manager" ); ?></div>
            <div class="panel-body">
                <h3><?php echo number_format($wpdb->get_var("select count(*) from {$wpdb->prefix}ahm_download_stats where uid = '{$current_user->ID}' and `year` = YEAR(CURDATE()) and `month` = MONTH(CURDATE()) and `day` = DAY(CURDATE())"),0,'.',','); ?></h3>
            </div>
        </div>
    </div>
</div>
<?php
if(isset($params['recommended']) && ( term_exists($params['recommended'], 'wpdmcategory') || $params['recommended'] == 'recent')) {
    ?>
    <div class="panel panel-default dashboard-panel">
        <div class="panel-heading"><?php _e( "Recommended Downloads" , "download-manager" ); ?></div>
        <div class="panel-body">
            <div class="panel-row">
                <?php
                $rc = 0;
                $qparams = array(
                    'post_type' => 'wpdmpro',
                    'posts_per_page' => 20,
                    'orderby' => 'rand'
                );

                if($params['recommended'] != 'recent')
                    $qparams['tax_query'] = array(array('taxonomy' => 'wpdmcategory', 'field' => 'slug', 'terms' => $params['recommended']));
                else
                    $qparams['orderby'] = 'date';


                $q = new WP_Query($qparams);
                while ($q->have_posts()) {
                    $q->the_post();
                    if (\WPDM\Package::userCanAccess(get_the_ID())) {
                        ?>
                        <div class="col-md-4">
                            <div class="panel panel-default">

                                <div class="panel-body">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php wpdm_post_thumb(array(400, 300)); ?>
                                    </a>
                                </div>
                                <div class="panel-footer">
                                    <a title="<?php the_title(); ?>" class="ttip" href="<?php the_permalink(); ?>" style="display: block;text-overflow: ellipsis;max-width: 100%; white-space: nowrap; overflow: hidden;">
                                        <?php the_title(); ?>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <?php
                        $rc++;
                        if ($rc >= 3) break;
                    }
                }
                wp_reset_postdata();
                ?>
            </div>
        </div>
    </div>
    <?php
}
?>
<?php
if(isset($params['fav']) && (int)$params['fav'] == 1) {
    $myfavs = maybe_unserialize(get_user_meta(get_current_user_id(), '__wpdm_favs', true));
    $template = '<div class="panel panel-default"><div class="panel-body"><div class="media">   <a class="pull-left" href="[page_url]">   [thumb_40x40]   </a>   <div class="media-body">   <strong style="font-weight: bold">[page_link]</strong><br/>[file_size]</div></div></div><div class="panel-footer">[fav_button_sm]</div></div>';
    ?>
    <div class="panel panel-default dashboard-panel">
        <div class="panel-heading"><?php _e( "My Favourites" , "download-manager" ); ?></div>

            <table class="table">
                <thead>
                <tr>
                    <th><?php _e( "Package Name" , "download-manager" ); ?></th>
                    <th width="70px" class="text-right"><?php _e( "Remove" , "download-manager" ); ?></th>
                </tr>
                </thead>

                <tbody>
                    <?php if(is_array($myfavs)) foreach ($myfavs as $fav){ $fav_post = get_post($fav); if(is_object($fav_post) && $fav_post->post_type == 'wpdmpro'){ ?>

                            <?php //echo \WPDM\Package::fetchTemplate($template, array('ID' => $fav)); ?>
                        <tr id="fav_<?php echo $fav; ?>">
                            <td><a target="_blank" href="<?php echo get_permalink($fav_post->ID); ?>"><?php echo $fav_post->post_title; ?></a></td>
                            <td class="text-right"><?php echo \WPDM\Package::favBtn($fav, array('size' => 'btn-xs rem-fav fav_'.$fav, 'a2f_label' => "<i class='fas fa-trash'></i>", 'rff_label' => "<i class='fas fa-trash'></i>"), false); ?></td>
                        </tr>

                    <?php
                    }}
                    wp_reset_postdata();
                    ?>
                </tbody>
            </table>

    </div>
    <?php
}
?>
<div class="panel panel-default dashboard-panel">
    <div class="panel-heading"><?php _e( "Last 5 Downloads" , "download-manager" ); ?></div>
    <table class="table">
        <thead>
        <tr>
            <th><?php _e( "Package Name" , "download-manager" ); ?></th>
            <th><?php _e( "Download Time" , "download-manager" ); ?></th>
            <th><?php _e( "IP" , "download-manager" ); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $res = $wpdb->get_results("select p.post_title,s.* from {$wpdb->prefix}posts p, {$wpdb->prefix}ahm_download_stats s where s.uid = '{$current_user->ID}' and s.pid = p.ID order by `timestamp` desc limit 0,5");
        foreach($res as $stat){
            ?>
            <tr>
                <td><a href="<?php echo get_permalink($stat->pid); ?>"><?php echo $stat->post_title; ?></a></td>
                <td><?php echo date_i18n(get_option('date_format')." H:i",$stat->timestamp); ?></td>
                <td><?php echo $stat->ip; ?></td>
            </tr>
            <?php
        }
        ?>

        </tbody>
    </table>
</div>
<script>
    jQuery(function ($) {
        $('.rem-fav').on('click', function () {
            var ret = $(this).attr('class').match(/fav_([0-9]+)/);
            if(ret[0] != undefined && ret[0] == 'fav_'+ret[1])
                $('#'+ret[0]).slideUp();
        });
    })
</script>
