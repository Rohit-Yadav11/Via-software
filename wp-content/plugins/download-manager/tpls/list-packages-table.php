<?php
if(!defined("ABSPATH")) die();
?>

<div class="wpdm-front wpdmpro" id="wpdm-package-list">


<form method="post" action="" id="posts-filter">
    <input type="hidden" name="do" value="search" />
<div class="card card-width-table">
    <div class="card-header" style="padding: 10px">


<div class="input-group input-group-lg input-group-x">
    <?php if($admin == 1){ ?>
    <a href="?adb_page=manage-packs" class="input-group-prepend __wpdm_load_async" data-container="#wpdm-package-list"><span class="input-group-text border-0"><?php echo __( "All Items", "download-manager" ) ?></span></a>
    <a href="?adb_page=manage-packs&_author=<?php echo $current_user->ID ?>" class="input-group-prepend __wpdm_load_async" data-container="#wpdm-package-list"><span class="input-group-text border-0" style="border-left: 1px solid #ddd !important;border-right: 1px solid #ddd !important;z-index: 9;"><?php echo __( "My Items", "download-manager" ) ?></span></a>
    <?php } ?>
    <input placeholder="<?php _e( "Search..." , "download-manager" ); ?>" type="text" style="height: 50px;line-height: 50px;padding: 0 20px" id="sfld" class="form-control" name="q" value="<?php echo $Q; ?>"><div class="input-group-btn"><button class="btn btn-lg" style="height: 50px;line-height: 50px;padding: 0 20px"><i class="fas fa-search color-green"></i></button></div>
</div>

</div>


    <table cellspacing="0" class="table table-hover manage-packages-frontend table-striped m-0">
    <thead>
    <tr>

    <th style="" class="sortable <?php echo wpdm_query_var('sorder')=='asc'?'asc':'desc'; ?>" id="media" scope="col"><a href='<?php echo  $base_url.$sap;?>sfield=title&sorder=<?php echo wpdm_query_var('sorder')=='asc'?'desc':'asc'; ?><?php echo $qr; ?>&pg=<?php echo $paged;?>'><span><?php _e( "Title" , "download-manager" ); ?></span> <?php if(wpdm_query_var('sfield')=='title') { echo wpdm_query_var('sorder')=='asc'?'<i class="fa fa-chevron-up" style="color:#D2322D;margin-left:10px"></i>':'<i class="fa fa-chevron-down" style="color:#D2322D;margin-left:10px"></i>'; } ?></a></th>
    <?php if(!wp_is_mobile()) { ?>
        <?php if($admin == 1){ ?>
            <th width="120" style="" scope="col"><?php _e( "Author" , "download-manager" ); ?></th>
        <?php } ?>
    <th width="120" style="" class="sortable  <?php echo wpdm_query_var('sorder')=='asc'?'asc':'desc'; ?>" id="parent" scope="col"><a href='<?php echo  $base_url.$sap;?>sfield=download_count&sorder=<?php echo wpdm_query_var('sorder')=='asc'?'desc':'asc'; ?><?php echo $qr; ?>&pg=<?php echo $paged;?>'><span><?php _e( "Downloads" , "download-manager" ); ?></span><?php if(wpdm_query_var('sfield')=='download_count') { echo wpdm_query_var('sorder')=='asc'?'<i class="fa fa-chevron-up" style="color:#D2322D;margin-left:10px"></i>':'<i class="fa fa-chevron-down" style="color:#D2322D;margin-left:10px"></i>'; } ?></a></th>
    <th style="" class="" id="media" scope="col" align="center"><a href='<?php echo  $base_url.$sap;?>sfield=publish_date&sorder=<?php echo wpdm_query_var('sorder')=='asc'?'desc':'asc'; ?><?php echo $qr; ?>&pg=<?php echo $paged;?>'><span><?php _e( "Publish Date" , "download-manager" ); ?></span> <?php if(wpdm_query_var('sfield')=='publish_date') { echo wpdm_query_var('sorder')=='asc'?'<i class="fa fa-chevron-up" style="color:#D2322D;margin-left:10px"></i>':'<i class="fa fa-chevron-down" style="color:#D2322D;margin-left:10px"></i>'; } ?></a></th>
    <th style="" class="" id="media" scope="col" align="center"><?php _e( "Status" , "download-manager" ); ?></th>
    <th style="width: 140px" class="manage-column column-media" id="media" scope="col" align="center"><?php _e( "Actions" , "download-manager" ); ?></th>
    <?php } ?>
    </tr>
    </thead>


    <tbody class="list:post" id="the-list">
    <?php while($query_packages->have_posts()) { $query_packages->the_post(); global $post;

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
                    <?php if(wp_is_mobile()){ ?>
                        <div class="actions">
                            <hr/>
                            <?php do_action("wpdm_package_action_button", $post); ?>
                            <a class="btn btn-primary btn-sm" href="<?php echo sprintf($edit_url, get_the_ID()); ?>"><i class="fas fa-pencil-alt"></i></a>
                            <a class="btn btn-sm btn-success" target="_blank" href='<?php echo get_permalink($post->ID); ?>'><i class="fa fa-eye"></i></a>
                            <a href="#" class="delp btn btn-danger btn-sm" data-id="<?php the_ID(); ?>" data-title="<?php the_title(); ?>" ><i class="fas fa-trash"></i></a>
                        </div>
                    <?php } ?>
                </td>
        <?php if(!wp_is_mobile()) { ?>
                <?php if($admin == 1){ ?>
                <td class=""><a class="__wpdm_load_async" data-container="#wpdm-package-list" href="?adb_page=manage-packs&_author=<?php echo $post->post_author; ?>"><?php echo get_user_by('id', $post->post_author)->display_name; ?></a></td>
                <?php } ?>
                <td class=""><?php echo (int)get_post_meta(get_the_ID(),'__wpdm_download_count', true); ?></td>
                <td class=""><?php echo $post->post_status=='publish'?get_the_date():'Not Yet';?></td>
                <td class=" <?php echo $post->post_status=='publish'?'text-success':'text-danger';?>"><?php echo ucfirst($post->post_status);?></td>
                <td class="actions text-center">
                    <nobr>
                        <?php do_action("wpdm_package_action_button", $post); ?>
                        <a class="btn btn-primary btn-sm" href="<?php echo sprintf($edit_url, get_the_ID()); ?>"><i class="fas fa-pencil-alt"></i></a>
                        <a class="btn btn-sm btn-success" target="_blank" href='<?php echo get_permalink($post->ID); ?>'><i class="fa fa-eye"></i></a>
                        <a href="#" class="delp btn btn-danger btn-sm" data-id="<?php the_ID(); ?>" data-title="<?php the_title(); ?>" ><i class="fas fa-trash"></i></a>
                    </nobr>
                </td>
        <?php } ?>

    </tr>
     <?php } ?>
    </tbody>
</table>

    <?php
    global $wp_query;
    $cp = $paged;
    ?>


    <div class="card-footer p-2">

        <?php
            echo wpdm_paginate_links($query_packages->found_posts, $limit, $cp, 'pg');

        wp_reset_query();
        ?>

    </div>
</div>

</form>

</div>

<script language="JavaScript">
<!--
  jQuery(function($){

      $('body').on('click', '.delp', function (e) {
          e.preventDefault();
          var pid = $(this).data('id');
          var id = '#post-'+ pid;
          WPDM.confirm("<?php _e('Are you sure?', 'download-manager'); ?>", "<?php _e('Deleting', 'download-manager'); ?> <span class='text-info'>"+$(this).data('title')+"</span>", [
              {
                  'label': 'No',
                  'class': 'btn btn-secondary',
                  'callback': function () {
                      $(this).modal('hide');
                  }
              },
              {
                  'label': 'Yes, Delete',
                  'class': 'btn btn-danger',
                  'callback': function () {
                      $(this).find('.modal-body').html('<i class="fa fa-sun fa-spin"></i> Deleting...');
                      var confirm = $(this);
                      $.post('<?php echo admin_url().'/admin-ajax.php?action=delete_package_frontend&ID=';?>'+pid, function (data) {
                          confirm.modal('hide');
                          $(id).fadeOut();
                      });
                  }
              }
          ]);
      });



      $('body').on('click', 'a.page-numbers',function(e){
          e.preventDefault();
          var _cont = '#wpdm-package-list';
          $(_cont).addClass('blockui');
          $.get(this.href, function (res) {
              $(_cont).html($(res).find(_cont).html());
              $(_cont).removeClass('blockui');
          });
          return false;
      });

  });
//-->
</script>
