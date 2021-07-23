<?php
global $wpdb, $current_user, $wp_query;

$limit = 12;
 
$cond[] = "uid='{$current_user->ID}'";
$Q = wpdm_query_var('q','txt');
$paged = wpdm_query_var('pg','num');
$paged = $paged>0?$paged:1;

$start = $paged?(($paged-1)*$limit):0;
$field = wpdm_query_var('sfield')?wpdm_query_var('sfield'):'publish_date';
$ord = wpdm_query_var('sorder')?wpdm_query_var('sorder'):'desc';

$author = $current_user->ID;
$params = array('post_status'=>array('publish','pending','draft'), 'post_type'=>'wpdmpro', 'author'=> $author, 'offset'=>$start, 'posts_per_page' => $limit);
$params['orderby'] = $field;
$params['order'] = $ord;
if(isset($sparams['base_category'])){
    $params['tax_query'] = array(
        array(
            'taxonomy' => 'wpdmcategory',
            'field'    => 'slug',
            'terms'    => $sparams['base_category'],
            'include_children' => true
        )
    );
}
if($field=='download_count'){
    $params['orderby'] = 'meta_value_num';
    $params['meta_key'] = '__wpdm_download_count';
    $params['order'] = $ord;
}

if($Q) $params['s'] = $Q;

$apfid = get_option('__wpdm_package_form', 0);
$apfpc = get_post($apfid);
$apfurl = $apfurl_ap = '';
if(has_shortcode($apfpc->post_content, "wpdm_package_form")) {
    $apfurl = get_permalink($apfid);
    $apfurl_ap = $apfurl;
    $apfurl = strstr($apfurl, '?')?$apfurl.'&id=%d':$apfurl.'?id=%d';
}

$edit_url = $burl . '/edit-package/%d/';

if(get_post_meta(get_the_ID(),'__urlfix', true) == 1 || !get_option('permalink_structure'))
    $edit_url = $burl.$sap.'adb_page=edit-package/%d/';

$edit_url = $apfurl != ''?$apfurl:$edit_url;

$res = new WP_Query($params);
if(!isset($qr)) $qr = '';
?>

<div class="wpdm-front wpdmpro">

<form method="post" action="" id="posts-filter">
    <input type="hidden" name="do" value="search" />
<div class="row">
    <div class="col-md-6"><?php if($apfurl_ap !=''){ ?><a href="<?php echo $apfurl_ap; ?>" class="btn btn-primary btn-addnew"><i class="fa fa-plus-circle"></i> Add New</a><?php } ?></div>
    <div class="col-md-2">
        <select class="form-control">
            <option <?php echo wpdm_query_var('sfield')=='title' && wpdm_query_var('sorder') == 'asc'?'selected=selected':''; ?> value="<?php echo  $burl.$sap;?>sfield=title&sorder=asc<?php echo $qr; ?>&pg=<?php echo $paged;?>"><?php _e( "Title" , "download-manager" ); ?> [⬆]</option>
            <option <?php echo wpdm_query_var('sfield')=='title' && wpdm_query_var('sorder') == 'desc'?'selected=selected':''; ?> value="<?php echo  $burl.$sap;?>sfield=title&sorder=desc<?php echo $qr; ?>&pg=<?php echo $paged;?>"><?php _e( "Title" , "download-manager" ); ?> [⬇]</option>
            <option <?php echo wpdm_query_var('sfield')=='download_count' && wpdm_query_var('sorder') == 'asc'?'selected=selected':''; ?> value="<?php echo  $burl.$sap;?>sfield=download_count&sorder=asc<?php echo $qr; ?>&pg=<?php echo $paged;?>"><?php _e( "Download Count" , "download-manager" ); ?> [⬆]</option>
            <option <?php echo wpdm_query_var('sfield')=='download_count' && wpdm_query_var('sorder') == 'desc'?'selected=selected':''; ?> value="<?php echo  $burl.$sap;?>sfield=download_count&sorder=desc<?php echo $qr; ?>&pg=<?php echo $paged;?>"><?php _e( "Download Count" , "download-manager" ); ?> [⬇]</option>
            <option <?php echo wpdm_query_var('sfield')=='publish_date' && wpdm_query_var('sorder') == 'asc'?'selected=selected':''; ?> value="<?php echo  $burl.$sap;?>sfield=publish_date&sorder=asc<?php echo $qr; ?>&pg=<?php echo $paged;?>"><?php _e( "Publish Date" , "download-manager" ); ?> [⬆]</option>
            <option <?php echo wpdm_query_var('sfield')=='publish_date' && wpdm_query_var('sorder') == 'desc'?'selected=selected':''; ?> value="<?php echo  $burl.$sap;?>sfield=publish_date&sorder=desc<?php echo $qr; ?>&pg=<?php echo $paged;?>"><?php _e( "Publish Date" , "download-manager" ); ?> [⬇]</option>
        </select>
    </div>
    <div class="col-md-4"><input placeholder="🔎 &nbsp; <?php _e( "Search..." , "download-manager" ); ?>" type="text" id="sfld" class="form-control" name="q" value="<?php echo $Q; ?>"></div>
</div>
<br/>
    <div class="row">


    <?php while($res->have_posts()) { $res->the_post(); global $post;
                   
        ?>
        <div class="col-md-4" id="post-<?php echo $post->ID ?>">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="media">
                        <div class="pull-left"><?php wpdm_post_thumb(array(48,48), true, array('default' => WPDM_BASE_DIR.'assets/images/img-404.png')) ?></div>
                        <div class="media-body">
                            <strong><?php the_title(); ?></strong><br/>
                            <small>
                                <span class="text-success wpdmtip" title="<?php _e( "Download Count" , "download-manager" ); ?>"><i class="fa fa-download"></i> <?php echo (int)get_post_meta(get_the_ID(), '__wpdm_download_count', true); ?>&nbsp;</span>
                                <span class="text-info wpdmtip" title="<?php _e( "View Count" , "download-manager" ); ?>"><i class="fa fa-eye"></i> <?php echo get_post_meta(get_the_ID(), '__wpdm_view_count', true); ?>&nbsp;</span>
                                <?php if(function_exists('wpdmpp_product_price')){ ?>
                                    <span class="text-danger wpdmtip" title="<?php _e( "Product Price" , "download-manager" ); ?>"><i class="fa fa-shopping-cart"></i> <?php echo wpdmpp_currency_sign(). wpdmpp_product_price(get_the_ID()); ?>&nbsp;</span>
                                    <span class="text-primary wpdmtip" title="<?php _e( "Sales Count" , "download-manager" ); ?>"><i class="fa fa-shopping-bag"></i> <?php echo wpdmpp_total_purchase(get_the_ID()); ?>&nbsp;</span>
                                <?php } ?>
                            </small>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <span title="<?php echo $post->post_status=='publish'?sprintf(__( "Published On %s" , "download-manager" ), get_the_date()):__( "Pending Review" , "download-manager" );?>" class="pull-right wpdmtip label-poststat label <?php echo $post->post_status=='publish'?'label-success':'label-warning';?>"><?php echo $post->post_status=='publish'?'<i class="fa fa-check-circle"></i> ':'<i class="fa fa-hourglass"></i> ';?> <?php echo ucfirst($post->post_status);?></span>
                    <a class="btn btn-primary btn-xs" href="<?php echo sprintf($edit_url, get_the_ID()); ?>"><i class="fas fa-pencil-alt"></i> <?php _e( "Edit" , "download-manager" ) ;?></a> <a class="btn btn-xs btn-info" target="_blank" href='<?php echo get_permalink($post->ID); ?>'><i class="fa fa-eye"></i>  <?php _e( "View" , "download-manager" ) ;?></a> <a href="#" class="delp btn btn-danger btn-xs" onclick="return false;" data-toggle="popover" data-content="Are You Sure? <a style='margin:0 5px' href='#' class='canceldelete btn btn-secondary btn-xs pull-right'>No</a> <a href='#' class='submitdelete btn btn-danger btn-xs pull-right' rel='<?php the_ID(); ?>'>Yes</a>" title="Delete Package" ><i class="fas fa-trash"></i>  <?php _e( "Delete" , "download-manager" ) ;?></a>
                </div>
            </div>
        </div>

     <?php } ?>

</div>
    <?php
    global $wp_query;
    $cp = $paged;

    $page_links = paginate_links( array(
        'base' => add_query_arg( 'pg', '%#%' ),
        'format' => '',
        'prev_text' => __('&laquo;'),
        'next_text' => __('&raquo;'),
        'total' => ceil($res->found_posts/$limit),
        'current' => $cp
    ));


    ?>


        <?php if ( $page_links ) { ?>
            <div class="tablenav-pages"><?php $page_links_text = sprintf( '<span style="margin-right:20px;" class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
                    number_format_i18n( ( $cp - 1 ) * $limit + 1 ),
                    number_format_i18n( min( $cp * $limit, $res->found_posts ) ),
                    number_format_i18n( $res->found_posts ),
                    $page_links
                ); echo $page_links_text; ?></div>
        <?php }
        wp_reset_query();
        ?>


</form>

</div>

<script language="JavaScript">
<!--
  jQuery(function($){
     jQuery('body').on('click', '.submitdelete' ,function(){
          var id = '#post-'+this.rel;
          jQuery('#li-'+this.rel).html("<a href='#'><i class='fa fa-time'></i> Deleting...</a>");
          jQuery.post('<?php echo admin_url().'/admin-ajax.php?action=delete_package_frontend&ID=';?>'+this.rel,function(){
              jQuery(id).fadeOut();
          }) ;
          return false;
     });
     jQuery('.delp').popover({placement:'left', html:true});

      jQuery('body').on('click', '.canceldelete',function(){
          jQuery('.delp').popover('hide');
          return false;
      });
      jQuery('.wpdmtip').tooltip().css('cursor', 'pointer');

  });
//-->
</script>
<style>
    .popover-content{ padding: 20px;line-height: 25px; }
    @media (max-width: 500px) {
        .btn-group-mbl{ font-size: 9px; margin-bottom:10px; }
        .btn-group-mbl .btn{ font-size: 9px; font-weight: 400; }
        .btn-addnew{ margin-bottom: 10px; display: block; }

    }
    .label.label-poststat{
        line-height: 25px; padding: 0px 13px; border-radius: 1px; font-weight: 400; cursor: pointer;
    }
</style>