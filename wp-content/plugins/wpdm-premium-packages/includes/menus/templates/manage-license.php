<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb;
?>
<div class="w3eden">
<div class="panel panel-default" id="wpdm-wrapper-panel">
    <div class="panel-heading">
        <b><i class="fas fa-id-card color-purple"></i> &nbsp; <?php _e('Licenses', 'wpdm-premium-packages'); ?></b>
        <div class="pull-right">
            <a href="#" class="btn btn-sm wpdm-facebook" id="server" data-toggle="modal" data-target="#myModal"><i class="fa fa-server" style="margin: 0"></i></a>
            <a href="edit.php?post_type=wpdmpro&page=pp-license&task=NewLicense" class="btn btn-sm btn-primary"><i class="fa fa-plus-circle"></i> Add New</a>
            <a href="#" class="btn btn-sm btn-info src-license"><i class="fas fa-search"></i> Search</a>
            <a href="#" class="btn btn-sm btn-default" id="apply"><i class="fas fa-trash"></i> Delete Selected</a>
        </div>
        <div style="clear: both"></div>
    </div>
    <div class="panel-body">
        <br/><br/><br/>
    <form method="get" id="search-license-form" action="edit.php">
        <input type="hidden" name="post_type" value="wpdmpro">
        <input type="hidden" name="page" value="pp-license">
        <input type="hidden" name="task" value="search_license">
        <div class="panel panel-default" id="src-license" style="display:none;">

            <div class="panel-body">
                <div class="col-md-3">
                    <input type="text" placeholder="<?php _e('Order ID:','wpdm-premium-packages'); ?>" class="form-control" name="oid" value="<?php echo isset($_REQUEST['oid']) ? sanitize_text_field($_REQUEST['oid']) : ''; ?>">
                </div>
                <div class="col-md-3">
                    <input type="text" placeholder="<?php _e('License No:','wpdm-premium-packages'); ?>" class="form-control" name="licenseno" value="<?php echo isset($_REQUEST['licenseno']) ? sanitize_text_field($_REQUEST['licenseno']) : ''; ?>">
                </div>
                <div class="col-md-4">
                    <input type="text" placeholder="<?php _e('Website/IP:','wpdm-premium-packages'); ?>" class="form-control" name="link" value="<?php echo isset($_REQUEST['link']) ? esc_url($_REQUEST['link']) : ''; ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-default btn-block action"><i class="fas fa-search fa-green"></i> Search License</button>
                </div>

            </div>
            <div class="panel-footer">
                <b><?php printf(__('%d license(s) found','wpdm-premium-packages'), $t); ?></b>
            </div>
        </div>
    </form>
    <form method="get" action="edit.php"  id="pp-license-form">
        <input type="hidden" name="post_type" value="wpdmpro">
        <input type="hidden" name="page" value="pp-license">
        <input type="hidden" name="task" value="delete_selected">
        <div class="clear"></div>
        <div class="panel panel-default">
        <table cellspacing="0" class="table table-striped table-hover table-wpdmpp">
            <thead>
            <tr>
                <th style="width: 20px" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
                <th style="" class="manage-column column-media" id="media" scope="col"><?php _e('License Key','wpdm-premium-packages'); ?></th>
                <th style="" class="manage-column column-author" id="author" scope="col"><?php _e('Product Name','wpdm-premium-packages'); ?></th>
                <th style="" class="manage-column column-author" id="author" scope="col"><?php _e('Order ID','wpdm-premium-packages'); ?></th>
                <th style="" class="manage-column column-parent" id="parent" scope="col"><?php _e('Activation Date','wpdm-premium-packages'); ?></th>
                <th style="" class="manage-column column-parent" id="parent" scope="col"><?php _e('Expire Date','wpdm-premium-packages'); ?></th>
                <th style="" class="manage-column column-parent" id="parent" scope="col"><?php _e('Domains','wpdm-premium-packages'); ?></th>
            </tr>
            </thead>

            <tfoot>
            <tr>
                <th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
                <th style="" class="manage-column column-media" id="media" scope="col"><?php _e('License Key','wpdm-premium-packages'); ?></th>
                <th style="" class="manage-column column-author" id="author" scope="col"><?php _e('Product Name','wpdm-premium-packages'); ?></th>
                <th style="" class="manage-column column-author" id="author" scope="col"><?php _e('Order ID','wpdm-premium-packages'); ?></th>
                <th style="" class="manage-column column-parent" id="parent" scope="col"><?php _e('Activation Date','wpdm-premium-packages'); ?></th>
                <th style="" class="manage-column column-parent" id="parent" scope="col"><?php _e('Expire Date','wpdm-premium-packages'); ?></th>
                <th style="" class="manage-column column-parent" id="parent" scope="col"><?php _e('Domains','wpdm-premium-packages'); ?></th>
            </tr>
            </tfoot>

            <tbody class="list:post" id="the-list">
            <?php
            foreach ($licenses as $i => $license) {
                $license->domain = maybe_unserialize($license->domain);
                $license->domain = is_array($license->domain)?$license->domain:array();

                $_license = $wpdb->get_var("select license from {$wpdb->prefix}ahm_order_items where pid = '{$license->pid}' and oid = '{$license->oid}'");
                $_license = maybe_unserialize($_license);
                $_license = isset($_license['info'], $_license['info']['name'])?'<span class="ttip color-purple" title="'.esc_html($_license['info']['description']).'"> <i class="fa fa-check-square"></i> '.sprintf(__("%s License","wpdm-premium-packages"), $_license['info']['name']).'</span>':'';


                ?>
                <tr valign="top" class="author-self status-inherit" id="post-8">
                    <th class="check-column" scope="row"><input type="checkbox" value="<?php echo $license->id; ?>" name="id[]"></th>
                    <td class="media column-media">
                        <strong>
                            <a title="Edit" href="edit.php?post_type=wpdmpro&page=pp-license&task=editlicense&id=<?php echo $license->id; ?>"><?php echo $license->licenseno; ?></a>
                        </strong>
                    </td>
                    <td class="author column-author"><?php echo $license->productname." {$_license}"; ?></td>
                    <td class="author column-author">
                        <a target="_blank" href="edit.php?post_type=wpdmpro&page=orders&task=vieworder&id=<?php echo $license->oid; ?>"><?php echo $license->oid; ?></a>
                    </td>
                    <td class="parent column-parent"><?php echo $license->activation_date ? date(get_option('date_format'), $license->activation_date) : 'Inactive'; ?></td>
                    <td class="parent column-parent"><?php echo $license->expire_date > 0 ? date(get_option('date_format'), $license->expire_date) : 'N/A'; ?></td>
                    <td class="parent column-parent"><a href="" class="unlock-license pull-right color-green" style="width: 100px;text-align: left;text-decoration: none;outline: none;" data-lid="<?php echo $license->id; ?>"><i class="fas fa-unlock-alt"></i> Unlock</a><span id="dcnt-<?php echo $license->id; ?>"> <?php echo count($license->domain)."</span> / ".($license->domain_limit?$license->domain_limit:'NoLimit'); ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        </div>
        <?php
        $page_links = paginate_links(array(
            'base' => add_query_arg('paged', '%#%'),
            'format' => '',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => ceil($t / $l),
            'current' => $p
        ));
        ?>

        <div id="ajax-response"></div>
        <div class="tablenav">
            <?php if ($page_links) {
                    $paged = isset($_GET['paged'])?(int)$_GET['paged']:1;
                ?>
                <div class="tablenav-pages">
                    <?php $page_links_text = sprintf('<span class="displaying-num">' . __('Displaying %s&#8211;%s of %s') . '</span>%s',
                        number_format_i18n(($paged - 1) * $l + 1),
                        number_format_i18n(min($paged * $l, $t)),
                        number_format_i18n($t),
                        $page_links
                    );
                    echo $page_links_text; ?></div>
            <?php } ?>


            <br class="clear">
        </div>

    </form>
    </div>
    </div>
    <br class="clear">

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php _e('License Integration','wpdm-premium-packages'); ?></h4>
                </div>
                <div class="modal-body">
                    <div class="input-group">
                        <div class="input-group-addon">License Server URL</div>
                        <input type="text" readonly="readonly" style="background: #ffffff" class="form-control" value="<?php echo home_url('/'); ?>">
                    </div><br/>
                    <div class="panel panel-default">
                        <div class="panel-heading">Requited Parameters</div>
                    <table class="table table-striped">
                        <tr><th>Parameter Name</th><th>Parameter Value</th></tr>
                        <tr><td>wpdmLicense</td><td>validate</td></tr>
                        <tr><td>licenseKey</td><td>[license-key]</td></tr>
                        <tr><td>doamin</td><td>[domain_name_or_ip]</td></tr>
                        <tr><td>productId</td><td>[product_code]</td></tr>
                    </table>
                    </div>

                    <a href="https://www.wpdownloadmanager.com/doc/admin-panel-3/license-manager/" target="_blank">More Details</a>

                </div>

            </div>
        </div>
    </div>
</div>

<script>
    jQuery(function ($) {
        $('.src-license').click(function (e) {
            e.preventDefault();
            $('#src-license').slideToggle();
        });
        $('#apply').on('click', function (e) {
            e.preventDefault();
            $('#pp-license-form').submit();
        });
        $('body').on('click', '.unlock-license', function (e) {
            e.preventDefault();
            var $this = $(this);
            $this.html('<i class="fas fa-sun fa-spin"></i> <?php _e('Wait...', 'wpdm-premium-packages'); ?>');
            var lid = $(this).data('lid');
            $.post(ajaxurl, {action: 'wpdm_unlock_license', __suc: '<?php echo wp_create_nonce( NONCE_KEY ); ?>', unlock_license: $(this).data('lid')}, function (res) {
                if(res === 'ok'){
                    $this.html('<i class="fas fa-check-square"></i> <?php _e('Unlocked', 'wpdm-premium-packages'); ?>');
                    $('#dcnt-'+lid).html("0");
                }
            })
        });

        $('#search-license-form').submit(function (e) {
            e.preventDefault();
            WPDM.blockUI('#pp-license-form');
            $('#search-license-form').ajaxSubmit({
                success: function (res) {
                    $('#pp-license-form').html($(res).find('#pp-license-form').html());
                    WPDM.unblockUI('#pp-license-form');
                }
            });
        });

    });
</script>
