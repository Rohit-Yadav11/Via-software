<?php
/**
 * User: shahnuralam
 * Date: 10/22/17
 * Time: 2:41 AM
 */
$res = $wpdb->get_results("select * from {$wpdb->prefix}ahm_emails order by {$field} {$ord} limit $start, $limit",ARRAY_A);
$total = $wpdb->get_var("select count(*) as t from {$wpdb->prefix}ahm_emails");
?>
<form method="post" action="edit.php?post_type=wpdmpro&page=wpdm-subscribers&task=delete&lockOption=email" id="posts-filter" class="panel-body">


    <div class="clear"></div>


    <table id="subtbl" cellspacing="0" class="table table-striped">
        <thead>
        <tr>
            <th style="width: 50px" class="column-cb check-column text-center" id="cb"><input type="checkbox"></th>
            <th style="width:50px" class="manage-column column-id" ><?php echo __( "ID" , "download-manager" ); ?></th>
            <th><?php echo __( "Email" , "download-manager" ); ?></th>
            <th><?php echo __( "Name" , "download-manager" ); ?></th>
            <th><?php echo __( "Package Name" , "download-manager" ); ?></th>
            <th><?php echo __( "Date" , "download-manager" ); ?></th>
            <th style="width: 180px;text-align: right"><?php echo __( "Action/Status" , "download-manager" ); ?></th>
        </tr>
        </thead>

        <tfoot>
        <tr>
            <th class="column-cb check-column text-center" id="cb"><input type="checkbox"></th>
            <th style="width:50px" class="manage-column column-id" ><?php echo __( "ID" , "download-manager" ); ?></th>
            <th><?php echo __( "Email" , "download-manager" ); ?></th>
            <th><?php echo __( "Name" , "download-manager" ); ?></th>
            <th><?php echo __( "Package Name" , "download-manager" ); ?></th>
            <th><?php echo __( "Date" , "download-manager" ); ?></th>
            <th class="text-right"><?php echo __( "Action/Status" , "download-manager" ); ?></th>
        </tr>
        </tfoot>

        <tbody class="list:post" id="the-list">
        <?php
        $nonce = wp_create_nonce(NONCE_KEY);
        foreach($res as $row) {

            ?>
            <tr valign="top" class="author-self status-inherit" id="__emlrow_<?php echo $row['id']; ?>">

                <th class="check-column text-center" style="padding: 5px 0px !important;"><input type="checkbox" value="<?php echo $row['id']; ?>" name="id[]"></th>
                <td>
                    <?php echo $row['id']; ?>
                </td>
                <td><?php echo $row['email']; ?></td>
                <td><?php $cd = unserialize($row['custom_data']); if($cd) { ?>
                    <div class="btn-group">
                        <?php if(isset($cd['name'])) { ?><button class="btn btn-secondary btn-xs" disabled="disabled" type="button"><?php echo $cd['name']; ?></button><?php } ?>
                        <?php if(!isset($cd['name']) || count($cd) > 1){ ?>
                        <button class="btn btn-secondary btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo isset($cd['name']) ? "<i class='fa fa-angle-double-down'></i>":'Other Info'; ?></button>
                        <div class="dropdown-menu" style="padding: 10px;border: 0;font-size: 12px;min-width: 300px">
                            <?php echo "<table class='table table-bordered' style='margin: 0'><thead><tr><th colspan='2'>". __("Custom Form Data:", "download-manager") ."</th></tr><tr><th>".__( "Field Name", "download-manager" )."</th><th>".__( "Field Value", "download-manager" )."</th></tr></thead>"; foreach($cd as $k=>$v): echo "<tr><td><nobr>".ucfirst($k)."</nobr></td><td><nobr>".(is_array($v)?implode(", ",$v):$v)."</nobr></td></tr>"; endforeach; echo "</table>"; } ?>
                        </div>
                        <?php } ?>
                    </div>
                </td>

                <td>
                    <a href='edit.php?post_type=wpdmpro&page=wpdm-subscribers&pid=<?php echo $row['pid']; ?>'><?php $p =  get_post($row['pid']); if(is_object($p) && $p->post_type =='wpdmpro') echo $p->post_title; else echo "Not Found or Deleted"; ?></a>
                </td>
                <td><?php echo wp_date(get_option('date_format')." ".get_option('time_format'),$row['date']); ?></td>
                <td class="text-right">
                    <?php
                    if ((int)$row['request_status']===2) echo "<a class='btn btn-xs btn-info __wpdm_approvedr' data-nonce='{$nonce}' data-rid='{$row['id']}' href='#'>".__( "Approve", "download-manager" )."</a> <a class='btn btn-xs btn-danger __wpdm_declinedr __wpdm_declinedr_{$row['id']}' data-nonce='{$nonce}' data-rid='{$row['id']}' href='#'>".__( "Decline", "download-manager" )."</a>";
                    else if ((int)$row['request_status']===3) echo "<div disabled='disabled' style='width: 120px' class='btn btn-xs btn-success'><i class='fa fa-check'></i> ".__( "Approved", "download-manager" )."</div>";
                    else echo "<div disabled='disabled' style='width: 120px' class='btn btn-xs btn-info'><i class='fa fa-check-double'></i> ".__( "Downloaded", "download-manager" )."</div>";
                    ?>
                </td>

            </tr>
        <?php } ?>
        </tbody>
    </table>
    <?php
    $cp = $_GET['paged']?(int)$_GET['paged']:1;
    /*
    $page_links = paginate_links( array(
        'base' => add_query_arg( 'paged', '%#%' ),
        'format' => '',
        'prev_text' => __('&laquo;'),
        'next_text' => __('&raquo;'),
        'total' => ceil($total/$limit),
        'current' => $cp
    ));*/

    $page_links = wpdm_paginate_links($total, $limit,  $cp, 'paged');

    ?>

    <div class="well">
        <nobr>
            <input type="submit" class="btn btn-secondary action submitdelete" id="doaction" value="<?php echo __( "Delete Selected" , "download-manager" ); ?>">
            <?php if(isset($_REQUEST['q'])) { ?>
                <input type="button" class="button-secondary action" onclick="location.href='admin.php?page=file-manager'" value="<?php echo __( "Reset Search" , "download-manager" ); ?>">
            <?php } ?>
        </nobr>
        <div class="pull-right">

            <?php  if ( $page_links ) { ?>
                <div class="tablenav-pages"><?php
                    $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
                        number_format_i18n( ( $_GET['paged'] - 1 ) * $limit + 1 ),
                        number_format_i18n( min( $_GET['paged'] * $limit, $total ) ),
                        number_format_i18n( $total ),
                        "<div style='display:inline-block;'>{$page_links}</div>"
                    ); echo $page_links_text; ?></div>
            <?php }  ?>

        </div><div style="clear: both"></div>
    </div>




</form>
<style>
    .w3eden .pagination{ margin:  0; }
    .displaying-num{
        line-height: 34px;
        margin-right: 10px;
        float: left;
    }
</style>
