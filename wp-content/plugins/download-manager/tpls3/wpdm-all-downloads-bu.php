<?php
global $btnclass;
$p = serialize($params);
$tid = md5($p);
static $n = 0;
if(!isset($params['items_per_page'])) $params['items_per_page'] = 20;
$unid = isset($params['id'])?$params['id']:'';
$column_positions = array();

if(isset($params['jstable']) && $params['jstable']==1):

  $datatable_col = ( isset($params['order_by']) && $params['order_by'] == 'title' ) ? '0' : '2';
  $datatable_order = ( isset($params['order']) && $params['order'] == 'DESC' ) ? 'desc' : 'asc';
  ?>
    <script src="<?php echo WPDM_BASE_URL.'assets/js/jquery.dataTables.min.js' ?>"></script>
    <link href="<?php echo WPDM_BASE_URL.'assets/css/jquery.dataTables.min.css' ?>" rel="stylesheet" />
    <style>
        #wpdmmydls-<?php echo $tid; ?>{
            border-bottom: 1px solid #dddddd;
            border-top: 3px solid #bbb;
            font-size: 10pt;
        }
        #wpdmmydls-<?php echo $tid; ?> th{
            background-color: #e8e8e8;
            border-bottom: 0;
        }

    #wpdmmydls-<?php echo $tid; ?>_filter input[type=search],
    #wpdmmydls-<?php echo $tid; ?>_length select{
        padding: 5px !important;
        border-radius: 3px !important;
        border: 1px solid #dddddd !important;
    }

        #wpdmmydls-<?php echo $tid; ?> .package-title{
            color:#36597C;
            font-size: 11pt;
            font-weight: 400;
        }
        #wpdmmydls-<?php echo $tid; ?> small{
            font-size: 9pt;
        }

    </style>
    <script>
        jQuery(function($){
            $('#wpdmmydls-<?php echo $tid; ?>').dataTable({
                "order": [[ <?php echo $datatable_col; ?>, "<?php echo $datatable_order; ?>" ]],
                "language": {
                    "lengthMenu": "<?php _e( "Display _MENU_ downloads per page" , "download-manager" )?>",
                    "zeroRecords": "<?php _e( "Nothing _START_ to - sorry" , "download-manager" )?>",
                    "info": "<?php _e( "Showing _START_ to _END_ of _TOTAL_ downloads" , "download-manager" )?>",
                    "infoEmpty": "<?php _e( "No downloads available" , "download-manager" )?>",
                    "infoFiltered": "<?php _e( "(filtered from _MAX_ total downloads)" , "download-manager" );?>",
                    "emptyTable":     "<?php _e( "No data available in table" , "download-manager" );?>",
                    "infoPostFix":    "",
                    "thousands":      ",",
                    "loadingRecords": "<?php _e( "Loading..." , "download-manager" ); ?>",
                    "processing":     "<?php _e( "Processing..." , "download-manager" ); ?>",
                    "search":         "<?php _e( "Search:" , "download-manager" ); ?>",
                    "paginate": {
                        "first":      "<?php _e( "First" , "download-manager" ); ?>",
                        "last":       "<?php _e( "Last" , "download-manager" ); ?>",
                        "next":       "<?php _e( "Next" , "download-manager" ); ?>",
                        "previous":   "<?php _e( "Previous" , "download-manager" ); ?>"
                    },
                    "aria": {
                        "sortAscending":  " : <?php _e( "activate to sort column ascending" , "download-manager" ); ?>",
                        "sortDescending": ": <?php _e( "activate to sort column descending" , "download-manager" ); ?>"
                    }
                },
                "iDisplayLength": <?php echo $params['items_per_page'] ?>,
                "aLengthMenu": [[<?php echo $params['items_per_page']; ?>, 10, 25, 50, -1], [<?php echo $params['items_per_page']; ?>, 10, 25, 50, "<?php _e( "All" , "download-manager" ); ?>"]]
            });
        });
    </script>
<?php endif; ?>

<div class="w3eden">
    <div class="container-fluid" id="wpdm-all-packages">
        <table id="wpdmmydls-<?php echo $tid; ?>" class="table table-striped wpdm-all-packages-table">
            <thead>
            <tr>
                <th class=""><?php _e( "Title" , "download-manager" ); ?></th>
                <th class="hidden-sm hidden-xs"><?php _e( "Categories" , "download-manager" ); ?></th>
                <th class="hidden-xs"><?php _e( "Create Date" , "download-manager" ); ?></th>
                <th style="width: 100px;"><?php _e( "Download" , "download-manager" ); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php


            $cfurl = get_permalink();
            $current_page = wpdm_query_var('pg'.$unid);
            if(strpos($cfurl, "?")) $cfurl.="&wpdmc="; else $cfurl.="?wpdmc=";
            $offset = $current_page>1?($current_page-1)*$items:0;
            $query_params = array("post_type"=>"wpdmpro","posts_per_page"=>$items,"offset"=>$offset);
            if(isset($tax_query)) $query_params['tax_query'] = $tax_query;
            $query_params['orderby'] = (isset($params['order_by']))?$params['order_by']:'date';

            $order_field = isset($params['order_by']) ? $params['order_by'] : 'date';
            $order = isset($params['order']) ? $params['order'] : 'DESC';

            $order_fields = array('__wpdm_download_count','__wpdm_view_count','__wpdm_package_size_b');
            if(!in_array( "__wpdm_".$order_field, $order_fields)) {
                $query_params['orderby'] = $order_field;
                $query_params['order'] = $order;
            } else {
                $query_params['orderby'] = 'meta_value_num';
                $query_params['meta_key'] = "__wpdm_".$order_field;
                $query_params['order'] = $order;
            }

            $q = new WP_Query($query_params);
            $total_files = $q->found_posts;
            while ($q->have_posts()): $q->the_post();

                $ext = "_blank";
                $data = wpdm_custom_data(get_the_ID());
                $data['files'] = \WPDM\Package::getFiles(get_the_ID());
                if(isset($data['files'])&&count($data['files']) > 0){
                    $tfiles = $data['files'];
                    $tfile = array_shift($tfiles);
                    $tmpvar = explode(".",$tfile);
                    $ext = count($tmpvar) > 1 ? end($tmpvar) : $ext;
                } else $data['files'] = array();

                $data['icon'] = isset($data['icon'])?str_replace('download-manager/file-type-icons','download-manager/assets/file-type-icons',$data['icon']):WPDM_BASE_URL.'assets/file-type-icons/download4.png';
                $ext = isset($data['icon']) && $data['icon'] != ''?$data['icon']:$ext.".png";

                $cats = wp_get_post_terms(get_the_ID(), 'wpdmcategory');
                $fcats = array();

                foreach($cats as $cat){
                    $fcats[] = "<a class='sbyc' href='{$cfurl}{$cat->slug}'>{$cat->name}</a>";
                }
                $cats = @implode(", ", $fcats);
                $data['ID'] = $data['id'] = get_the_ID();
                $data['title'] = get_the_title();
                if($ext=='') $ext = '_blank.png';
                if($ext==basename($ext)) $ext = plugins_url("download-manager/assets/file-type-icons/".$ext);
                if(isset($params['dllink']) && $params['dllink'] == 'popup')
                $data['download_link'] = DownloadLink($data, 0, array('popstyle' => 'popup'));
                else
                $data['download_link'] = DownloadLink($data, 0);
                $data = apply_filters("wpdm_after_prepare_package_data", $data);
                $download_link = $data['download_link'];
                if(isset($data['base_price']) && $data['base_price'] > 0) $download_link = "<a href='".get_permalink($data['ID'])."'>".__( "Buy Now" , "download-manager" )." @ {$data['currency']}{$data['effective_price']}</a>";
                if($download_link != 'blocked' && $download_link != 'loginform' && \WPDM\Package::userCanAccess($data['ID'])){

                ?>

                <tr>
                    <td style="background-image: url('<?php echo $ext ; ?>');background-size: 32px;background-position: 5px 8px;background-repeat:  no-repeat;padding-left: 43px;line-height: normal;">
                        <a class="package-title" href='<?php echo the_permalink(); ?>'><?php the_title(); ?></a><br/>
                        <small><i class="fa fa-folder"></i> &nbsp; <?php echo count($data['files']); ?> <?php _e( "files" , "download-manager" ); ?> &nbsp;&nbsp;
                            <i class="fa fa-download"></i> &nbsp; <?php echo isset($data['download_count'])?$data['download_count']:0; ?>
                            <?php echo isset($data['download_count']) && $data['download_count'] > 1 ?  __( "downloads" , "download-manager" ) : __( "download" , "download-manager" ); ?><br/>
                        <span class="hidden-md hidden-lg"><?php echo $cats; ?></br></span>
                        <span class="hidden-md hidden-lg hidden-sm"><?php echo get_the_date(); ?></span>
                        </small>
                    </td>
                    <td class="hidden-sm hidden-xs"><?php echo $cats; ?></td>
                    <td class="hidden-xs" data-order="<?php echo strtotime(get_the_date()); ?>"><?php echo get_the_date(); ?></td>
                    <td><?php echo $download_link; ?></td>
                </tr>
            <?php } endwhile; ?>
            <?php if((!isset($params['jstable']) || $params['jstable']==0) && $total_files==0): ?>
                <tr>
                    <td colspan="4" class="text-center">

                        <?php echo isset($params['no_data_msg']) && $params['no_data_msg']!=''?$params['no_data_msg']:__( "No Packages Found" , "download-manager" ); ?>

                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>

        <?php
        global $wp_rewrite,$wp_query;

        isset($wp_query->query_vars['paged']) && $wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;
        $current_page = $current_page == 0?1:$current_page;
        $pagination = array(
            'base' => @add_query_arg('pg'.$unid,'%#%'),
            'format' => '',
            'total' => ceil($total_files/$items),
            'current' => $current_page,
            'show_all' => false,
            'type' => 'list',
            'prev_next'    => True,
            'prev_text' => '<i class="icon icon-angle-left"></i> Previous',
            'next_text' => 'Next <i class="icon icon-angle-right"></i>',
        );

        //if( $wp_rewrite->using_permalinks() && !isset($_GET['wpdmc']) )
        //    $pagination['base'] = user_trailingslashit( trailingslashit( remove_query_arg('s',get_pagenum_link(1) ) ) . 'pg_'.$tid.'/%#%/', 'pg_'.$tid);

        if( !empty($wp_query->query_vars['s']) )
            $pagination['add_args'] = array('s'=>get_query_var('s'));

        if( isset($_GET['wpdmc']) )
            $pagination['wpdmc'] = array('wpdmc'=>$_GET['wpdmc']);

        echo  "<div class='text-center'>".str_replace("<ul class='page-numbers'>","<ul class='page-numbers pagination pagination-centered'>",paginate_links($pagination))."</div>";

        wp_reset_query();
        ?>

    </div>
</div>
