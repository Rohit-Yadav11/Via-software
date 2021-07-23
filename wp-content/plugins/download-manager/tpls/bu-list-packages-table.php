<?php
global $wpdb, $current_user, $wp_query;

$limit = 10;

$flaturl = isset($params['flaturl'])?$params['flaturl']:0;

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

$edit_url = $burl.$sap.'adb_page=edit-package/%d/';
if($flaturl == 1)
    $edit_url = $burl . '/edit-package/%d/';


$res = new WP_Query($params);
if(!isset($qr)) $qr = '';
?>

<div class="wpdm-front wpdmpro">
 

<form method="post" action="" id="posts-filter">
    <input type="hidden" name="do" value="search" />
<div class="card card-width-table">
    <div class="card-header" style="padding: 10px">


<div class="input-group input-group-lg input-group-x"><input placeholder="<?php _e( "Search..." , "download-manager" ); ?>" type="text" id="sfld" class="form-control" name="q" value="<?php echo $Q; ?>"><div class="input-group-btn"><button class="btn btn-lg"><i class="fas fa-search color-green"></i></button></div></div>

</div>


        <table cellspacing="0" class="table table-hover manage-packages-frontend table-striped">
    <thead>
    <tr>

    <th style="" class="manage-column column-media sortable <?php echo wpdm_query_var('sorder')=='asc'?'asc':'desc'; ?>" id="media" scope="col"><a href='<?php echo  $burl.$sap;?>sfield=title&sorder=<?php echo wpdm_query_var('sorder')=='asc'?'desc':'asc'; ?><?php echo $qr; ?>&pg=<?php echo $paged;?>'><span><?php _e( "Package Title" , "download-manager" ); ?></span> <?php if(wpdm_query_var('sfield')=='title') { echo wpdm_query_var('sorder')=='asc'?'<i class="fa fa-chevron-up" style="color:#D2322D;margin-left:10px"></i>':'<i class="fa fa-chevron-down" style="color:#D2322D;margin-left:10px"></i>'; } ?></a></th>
    <th width="120" style="" class="manage-column column-parent sortable hidden-xs <?php echo wpdm_query_var('sorder')=='asc'?'asc':'desc'; ?>" id="parent" scope="col"><a href='<?php echo  $burl.$sap;?>sfield=download_count&sorder=<?php echo wpdm_query_var('sorder')=='asc'?'desc':'asc'; ?><?php echo $qr; ?>&pg=<?php echo $paged;?>'><span><?php _e( "Downloads" , "download-manager" ); ?></span><?php if(wpdm_query_var('sfield')=='download_count') { echo wpdm_query_var('sorder')=='asc'?'<i class="fa fa-chevron-up" style="color:#D2322D;margin-left:10px"></i>':'<i class="fa fa-chevron-down" style="color:#D2322D;margin-left:10px"></i>'; } ?></a></th>
    <th style="" class="manage-column column-media hidden-xs" id="media" scope="col" align="center"><a href='<?php echo  $burl.$sap;?>sfield=publish_date&sorder=<?php echo wpdm_query_var('sorder')=='asc'?'desc':'asc'; ?><?php echo $qr; ?>&pg=<?php echo $paged;?>'><span><?php _e( "Publish Date" , "download-manager" ); ?></span> <?php if(wpdm_query_var('sfield')=='publish_date') { echo wpdm_query_var('sorder')=='asc'?'<i class="fa fa-chevron-up" style="color:#D2322D;margin-left:10px"></i>':'<i class="fa fa-chevron-down" style="color:#D2322D;margin-left:10px"></i>'; } ?></a></th>
    <th style="" class="manage-column column-media hidden-xs" id="media" scope="col" align="center"><?php _e( "Status" , "download-manager" ); ?></th>
    <th style="width: 140px" class="manage-column column-media" id="media" scope="col" align="center"><?php _e( "Actions" , "download-manager" ); ?></th>
    </tr>
    </thead>


    <tbody class="list:post" id="the-list">
    <?php while($res->have_posts()) { $res->the_post(); global $post;

        $file_count = \WPDM\Package::fileCount(get_the_ID());

                   
        ?>
    <tr valign="top" class="alternate author-self status-inherit" id="post-<?php the_ID(); ?>">


                  
                <td>
                    <a title="Edit" href="<?php echo sprintf($edit_url, get_the_ID()); ?>" class="d-block"><strong><?php the_title();?></strong></a>
                    <div class="text-muted">
                        <small><i class="far fa-copy"></i> <?php echo $file_count; ?> file<?php echo $file_count > 1?'s':''; ?> </small>&nbsp;
                        <small> <i class="far fa-hdd"></i> <?php echo (get_post_meta(get_the_ID(), '__wpdm_package_size', true)); ?></small>&nbsp;
                        <small> <i class="far fa-eye"></i> <?php echo (get_post_meta(get_the_ID(), '__wpdm_view_count', true)); ?> views</small>
                    </div>
                </td>
                <td class="parent column-parent hidden-xs"><?php echo (int)get_post_meta(get_the_ID(),'__wpdm_download_count', true); ?></td>
                <td class="parent column-parent hidden-xs"><?php echo $post->post_status=='publish'?get_the_date():'Not Yet';?></td>
                <td class="parent column-parent hidden-xs <?php echo $post->post_status=='publish'?'text-success':'text-danger';?>"><?php echo ucfirst($post->post_status);?></td>
                <td class="actions text-center">
                    <nobr>
                        <?php do_action("wpdm_package_action_button", $post); ?>
                        <a class="btn btn-primary btn-sm" href="<?php echo sprintf($edit_url, get_the_ID()); ?>"><i class="fas fa-pencil-alt"></i></a>
                        <a class="btn btn-sm btn-success" target="_blank" href='<?php echo get_permalink($post->ID); ?>'><i class="fa fa-eye"></i></a>
                        <a href="#" class="delp btn btn-danger btn-sm" onclick="return false;" data-toggle="popover" data-content="Are You Sure? <a style='margin:0 5px' href='#' class='canceldelete btn btn-secondary btn-xs pull-right'>No</a> <a href='#' class='submitdelete btn btn-danger btn-xs pull-right' rel='<?php the_ID(); ?>'>Yes</a>" title="Delete Package" ><i class="fas fa-trash"></i></a>
                    </nobr>
                </td>

    </tr>
     <?php } ?>
    </tbody>
</table>

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


    <div class="card-footer">

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

    </div>
</div>

</form>

</div>
<style>
    .w3eden .input-group-x .input-group-btn .btn,
    .w3eden .input-group-x .form-control{
        border: 0 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        background: #ffffff !important;
        font-weight: 400 !important;
        letter-spacing: 1px;
    }

    .w3eden .input-group-x{
        border-radius: 3px;
        border: 1px solid #dddddd;
        overflow: hidden;
    }
    .card-width-table .table{
        margin-bottom: 0;
    }
    .card-width-table .card-footer{
        border-top: 0;
    }
    thead{
        background: #e3ebf3;
    }
    .table thead th{
        border: 0 !important;
    }
    .table tr:first-child td{
        border-top: 0 !important;
    }
    .actions{
        vertical-align: middle;
    }
    .actions .btn-sm{
        font-size: 9px;
        padding: 8px 10px;
        border-radius: 2px;
    }
</style>
<script language="JavaScript">
<!--
  jQuery(function(){
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

  });
//-->
</script> 