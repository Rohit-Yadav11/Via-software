<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $wpdb;
?>
<div class="w3eden">
    <div class="panel panel-default" id="wpdm-wrapper-panel">
        <div class="panel-heading">
            <b><i class="fas fa-id-card color-purple"></i> &nbsp; <?php _e('New License', 'wpdm-premium-packages'); ?></b>
        </div>
        <div class="panel-body"><br/><br/><br/>
                <div class="container">
                    <div class="row">
                        <div class="col-md-8 col-md-offset-2">
                <form method="post" action="" id="add-license-form">
                    <input type="hidden" name="do" value="addlicense">
                    <?php wp_nonce_field(NONCE_KEY, '__suc'); ?>
                    <div class="form-group">
                        <label><?php _e('License No:','wpdm-premium-packages'); ?></label>
                        <input id="title" class="form-control input-lg" type="text"  name="license[licenseno]" readonly="readonly" value="<?php echo \WPDMPP\Libs\LicenseManager::generate_licensekey(); ?>">
                    </div>
                    <div class="row">
                        <div class="col-md-4"> <div class="form-group">
                                <label><?php _e('Order ID:','wpdm-premium-packages'); ?></label>
                                <input id="title" class="form-control" type="text"  name="license[oid]">
                            </div></div>
                        <div class="col-md-4"><div class="form-group">
                                <label><?php _e('Product ID:','wpdm-premium-packages'); ?><span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input class="form-control" type="text" id="lpid" required="required"  name="license[pid]">
                                    <div class="input-group-btn"><button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal"><i class="fas fa-search-plus"></i></button></div>
                                </div>
                            </div></div>
                        <div class="col-md-4"><div class="form-group">
                                <label><?php _e('Domain Limit:','wpdm-premium-packages'); ?></label>
                                <input class="form-control" type="number" size="5" min="0" step="1"  name="license[domain_limit]" value="1"/>
                            </div></div>

                    </div>
                    <div class="form-group">
                        <label><?php _e('Domains:','wpdm-premium-packages'); ?></label>
                        <textarea class="form-control" cols="60" rows="6" name="license[domain]"></textarea>
                        <em><?php _e("One domain per line. Don't use 'http://' or 'www' only 'domain.com'","wpdm-premium-packages"); ?></em>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group">
                                <label><?php _e('Activation Date:','wpdm-premium-packages'); ?></label>
                                <input class="form-control" id="actdate" type="text" name="license[activation_date]" value="" />
                            </div></div>
                        <div class="col-md-6"><div class="form-group">
                                <label><?php _e('Expire Period:','wpdm-premium-packages'); ?></label>

                                    <input id="expdate" class="form-control" type="text"  name="license[expire_date]" value=""/>

                            </div></div>

                    </div>



                    <div class="form-group well text-right">
                        <button class="btn btn-primary btn-lg"><i class="far fa-hdd"></i> Add New License</button>
                    </div>

                </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel"><?php _e('Select Product','wpdm-premium-packages'); ?></h4>
                    </div>
                    <div class="modal-body">
                       <div class="input-group">
                           <input type="text" placeholder="<?php _e('Search Product...','wpdm-premium-packages'); ?>" class="form-control" id="srcp">
                           <div class="input-group-btn"><button type="button" class="btn btn-default" id="srcpnow"><i class="fas fa-search"></i></button></div>
                       </div>
                        <br/>
                        <div class="list-group" id="productlist">

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
<script>
    jQuery(function($){
        $('#actdate, #expdate').datetimepicker({dateFormat:"yy-mm-dd", timeFormat: "hh:mm tt"});
        $('#srcpnow').on('click', function () {
            $.post(ajaxurl, {action: 'wp-link-ajax', _ajax_linking_nonce: '<?php echo wp_create_nonce( 'internal-linking' ); ?>', page: 1, search: $('#srcp').val()}, function (res) {
                res = JSON.parse(res);
                $(res).each(function( i, package ) {
                    if(package.info == 'Package')
                    $( "#productlist").append( "<div class='list-group-item'><a style='opacity: 1;margin-top: -3px;margin-right: -5px' href='#' data-dismiss='modal' data-pid='"+package.ID+"' class='close pull-right insert-pid'><i class='fa fa-plus-circle color-green'></i></a>"+package.title+"</div>" );
                });
            });
        });
        $('body').on('click', '.insert-pid', function (e) {
            e.preventDefault();
            $('#lpid').val($(this).data('pid'));
            $('#myModal').modal('close');
        });
    });
</script>
