<?php
/**
 * Pricing options for premium package. This template is used for both front and admin pricing options.
 *
 * This template can be overridden by copying it to yourtheme/download-manager/metaboxes/wpdm-pp-settings.php.
 *
 * @version     1.0.0
 */
if (!defined('ABSPATH')) {
    exit;
}

if( ! is_admin() ):
    $task = get_query_var('adb_page');
    $task = explode("/", $task);
    if($task[0] == 'edit-package') $pid = $task[1];

    if(isset($pid))
        $post = get_post($pid);
    else {
        $post = new stdClass();
        $post->ID = 0;
        $post->post_title = '';
        $post->post_content = '';
    }
endif;
$vid = uniqid();
$oid = uniqid();
$base_price = (double)get_post_meta($post->ID,'__wpdm_base_price',true);
?>
<div class="w3eden" id="wpdm-pp-settings">
    <div class="row">
        <div class="col-md-12 wpdm-full-front">
            <div class="panel panel-default">
                <div class="panel-heading"><?php _e('Product Code','wpdm-premium-packages'); ?></div>
                <div class="panel-body">
                    <input type="text" placeholder="<?php echo __( "Define a unique product code", "download-manager" ); ?>" class="form-control input-lg" name="file[product_code]" value="<?php echo esc_attr(get_post_meta(get_the_ID(), '__wpdm_product_code', true)); ?>" />
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading"><?php _e('Pricing','wpdm-premium-packages'); ?></div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="base-price-field"><?php echo __('Base Price','wpdm-premium-packages'); ?></label>
                            <input type="text" size="16" class="form-control input-lg" id="base-price-field" name="file[base_price]"  value="<?php  echo number_format($base_price,2,'.','');?>">
                        </div>
                        <div class="col-md-4">
                            <label for="sales-price-field"><?php echo __('Sales Price','wpdm-premium-packages'); ?></label>
                            <input type="text" class="form-control input-lg" size="16" id="sales-price-field" name="file[sales_price]"  value="<?php $sales_price = get_post_meta($post->ID,'__wpdm_sales_price',true); echo number_format((double)$sales_price,2,'.','');?>">
                        </div>
                        <div class="col-md-4">
                            <label for="sales-price-expire-field"><?php echo __('Valid Until','wpdm-premium-packages'); ?></label>
                            <input type="text" class="form-control input-lg" size="16" id="sales-price-expire-field" name="file[sales_price_expire]"  value="<?php echo get_post_meta($post->ID,'__wpdm_sales_price_expire',true);?>">

                        </div>

                    </div>
                </div>
                <div class="panel-footer">
                    <input type="hidden" name="file[pay_as_you_want]" value="0">
                    <label><input type="checkbox" name="file[pay_as_you_want]" <?php checked(1, get_post_meta($post->ID, '__wpdm_pay_as_you_want', true)); ?> value="1"> <?php _e('Pay as you want ( The base price will be treated as the minimum amount )','wpdm-premium-packages'); ?> </label>
                </div>
                <?php $price_variation = get_post_meta($post->ID,'__wpdm_price_variation',true);  ?>
                <div class="panel-footer">
                    <label>
                        <input style="margin: 0;line-height: 10px" type="checkbox" <?php if($price_variation!='') echo "checked='checked'"; else echo "";?> name="file[price_variation]" id="price_variation" value="1" name="price_variation" > <?php echo __('Activate Extra Gigs','wpdm-premium-packages'); ?>
                    </label>
                </div>
                <div id="price_dis_table" style="<?php if($price_variation != '') echo ""; else echo "display: none;";?>">
                <div class="panel-body">
                    <div id="vdivs">
                        <?php
                        $variation =  get_post_meta($post->ID,'__wpdm_variation',true);
                        if(is_array($variation)){
                            foreach($variation as $key=>$vname){ ?>
                                <div id="variation_div_<?php echo $key;?>" class="panel panel-default">
                                    <div class="panel-heading">
                                        <?php _e('Group ID#','wpdm-premium-packages');  ?> <?php echo $key;?> <i class="info fa fa-info" title="Use the Group ID when building add to cart URL"></i>
                                        <a class="delet_vdiv pull-right" rel="variation_div_<?php echo $key;?>" title="delete this gig"><i class="fa fa-times-circle text-danger"></i></a>
                                    </div>
                                    <table class="table table-vt" id="voption_table_<?php echo $key;?>">
                                        <tr><td colspan="5"><label><input style="margin: 0" type="checkbox" name="file[variation][<?php echo $key;?>][multiple]" placeholder="Multiple Select" <?php if(isset($vname['multiple'])) echo "checked='checked'"; ?> > &nbsp;<?php echo __('Multiple Select','wpdm-premium-packages'); ?></label></td></tr>
                                        <tr><td colspan="5"><input class="form-control" type="text" name="file[variation][<?php echo $key;?>][vname]" id="" placeholder="<?php _e('Group Name','wpdm-premium-packages');  ?>" title="<?php _e('Enter a Gig Name','wpdm-premium-packages');  ?>" value="<?php echo $vname['vname'];?>"></td></tr>
                                        <tr>
                                            <th style="width: 200px"><?php _e('Gig Name','wpdm-premium-packages'); ?></th>
                                            <th><?php _e('Gig Description','wpdm-premium-packages'); ?></th>
                                            <th width="150px"><?php _e('Gig ID ','wpdm-premium-packages'); ?><i class="info fa fa-info" title="Use the Gig ID when building add to cart URL"></i></th>
                                            <th width="150px"><?php _e('Extra Cost','wpdm-premium-packages'); ?></th>
                                            <th width="50px"><?php _e('Delete','wpdm-premium-packages'); ?></th>
                                        </tr>
                                        <?php
                                        if($vname){
                                            foreach($vname as $optionkey=>$optionval){
                                                if($optionkey!="vname" && $optionkey != "multiple"){?>
                                                    <tr id="voption<?php echo $optionkey;?>">
                                                        <td><input type="text" name="file[variation][<?php echo $key;?>][<?php echo $optionkey;?>][option_name]"  placeholder="Gig Name" class="form-control input-sm" value="<?php echo esc_attr($optionval['option_name']);?>"></td>
                                                        <td><textarea name="file[variation][<?php echo $key;?>][<?php echo $optionkey;?>][option_description]"  placeholder="Gig Description" class="form-control input-sm"><?php echo isset($optionval['option_description'])?htmlspecialchars(strip_tags($optionval['option_description'],'<script>')):'';?></textarea></td>
                                                        <td><input class="form-control input-sm" value="<?php echo $optionkey;?>" readonly type="text"></td>
                                                        <td><div class="input-group input-group-sm"><span class="input-group-addon"><i class="fa fa-plus-circle"></i></span><input style="max-width: 70px" min="0" name="file[variation][<?php echo $key;?>][<?php echo $optionkey;?>][option_price]" id="" size="5" class="form-control" type="number" placeholder="price" value="<?php echo $optionval['option_price'];?>"></div></td>
                                                        <td><i class="delet_voption fa fa-times-circle text-danger" rel="voption<?php echo $optionkey;?>" title="Delete this option" style="cursor:pointer"></i></td>
                                                    </tr>
                                                <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </table>
                                    <div style="clear: both;"></div>
                                    <div class="panel-footer">
                                    <input type="button" class="btn btn-default btn-sm add_voption" rel="<?php echo $key;?>" value="<?php _e('Add Gig','wpdm-premium-packages'); ?>">
                                    </div>
                                </div>
                            <?php
                            }
                        } ?>
                    </div>
                </div>
                <div class="panel-footer"><input type="button" class="btn btn-primary" id="add_variation" value="<?php _e('Add Gig Group','wpdm-premium-packages'); ?>"></div>
                </div>

            </div>
            <div class="panel panel-default">
                <div class="panel-heading">

                    <?php _e('Free Downloads','wpdm-premium-packages'); ?>
                </div>
                <div class="panel-body">
                    <div class="list-group" id="free-files">
                        <?php
                            $free_downloads = get_post_meta($post->ID, '__wpdm_free_downloads', true);
                        if(is_array($free_downloads)){
                            foreach ($free_downloads as $free_download){
                                $id = uniqid();
                            ?>

                            <div class="list-group-item" id="fdl<?php echo $id; ?>">
                                <div class="input-group">
                                    <input type="text" class="form-control" readonly="readonly" value="<?php echo $free_download; ?>" name="file[free_downloads][]" />
                                    <span class="input-group-addon delete-freedl" style='background: #ffffff' data-id="#fdl<?php echo $id; ?>"><i class="fas fa-trash color-red"></i></span>
                                </div>
                            </div>

                            <?php
                            }
                        }
                        ?>
                    </div>
                </div><div class="panel-footer">
                    <button type="button" id="addfreedls" class="btn btn-info wpdm-windows btn-sm"><i class="fa fa-plus-circle"></i> <?php _e('Add Free File(s)','wpdm-premium-packages'); ?></button>
                </div>
            </div>

            <script type="text/javascript">

                jQuery(function ($){
                    $('#price_variation').on('click', function(){
                        if($('#price_variation').is(":checked")){
                            $('#variation_heading').text("Gig Options");
                            $('#price_dis_table').show();

                        }else{
                            $('#variation_heading').text("Pricing");
                            $('#price_dis_table').hide()  ;
                        }
                    });
                    $('#add_variation').on("click", function (){
                        var tm=new Date().getTime();
                        $('#vdivs').append('<div id="variation_div_'+tm+'" class="panel panel-default"><div class="panel-heading"><?php _e('Group ID','wpdm-premium-packages'); ?># '+tm+'<a class="delet_vdiv pull-right" rel="variation_div_'+tm+'" title="delete this variation"><i class="fa fa-times-circle text-danger"></i></a></div><table class="table table-v" id="voption_table_'+tm+'"><tr><td colspan="5"><label><input type="checkbox" style="margin: 0 !important;" name="file[variation]['+tm+'][multiple]"> <?php _e('Multiple Select','wpdm-premium-packages'); ?></label></td></tr><tr><td colspan="5"><input type="text" name="file[variation]['+tm+'][vname]" id="" class="form-control" placeholder="Group Name"></td></tr><tr><th>Gig Name</th><th>Gig Description</th><th>Gig ID</th><th width="150px">Extra Cost</th><th width="50px">Delete</th></tr><tr id="voption_'+tm+'"><td><input type="text" name="file[variation]['+tm+']['+tm+'][option_name]" id="" placeholder="Gig Name" class="form-control input-sm"></td><td><textarea name="file[variation]['+tm+']['+tm+'][option_description]"  placeholder="Gig Description" class="form-control"></textarea></td><td><input type="text"  placeholder="Gig Name" disabled=disabled value="'+tm+'" class="form-control input-sm"></td><td><div class="input-group input-group-sm"><span class="input-group-addon"><span class="input-group-text"><i class="fa fa-plus-circle"></i></span></span><input type="number" class="form-control" style="max-width: 70px" min=0 name="file[variation]['+tm+']['+tm+'][option_price]" id="" placeholder="<?php _e('Price','wpdm-premium-packages'); ?>"></div></td><td><i class="delet_voption fa fa-times-circle text-danger" rel="voption_'+tm+'" title="delete this option" alt="" style="cursor:pointer"></i></td></tr></table><div style="clear: both;"></div><div class="panel-footer"><input type="button" class="btn btn-secondary btn-sm add_voption" rel="'+tm+'" value="Add Gig"></div></div>');
                    });
                    $('body').on("click", '.delet_vdiv', function(){
                        if(confirm("Are you sure to remove"))
                            $('#'+$(this).attr("rel")).remove();
                    });
                    $('body').on("click", '.add_voption' , function (){
                        var tm=new Date().getTime();
                        $('#voption_table_'+$(this).attr("rel")).append('<tr id="voption'+tm+'"><td><input type="text" name="file[variation]['+$(this).attr("rel")+']['+tm+'][option_name]"  placeholder="Gig Name" class="form-control input-sm"></td><td><textarea name="file[variation]['+jQuery(this).attr("rel")+']['+tm+'][option_description]"  placeholder="Gig Description" class="form-control"></textarea></td><td><input type="text"  placeholder="Gig Name" disabled=disabled value="'+tm+'" class="form-control input-sm"></td><td><div class="input-group input-group-sm"><span class="input-group-addon"><i class="fa fa-plus-circle"></i></span><input type="number" name="file[variation]['+$(this).attr("rel")+']['+tm+'][option_price]" size="5" id="" placeholder="<?php _e('Price','wpdm-premium-packages'); ?>" class="form-control" style="max-width:70px"></div></td><td><i class="delet_voption fa fa-times-circle text-danger" rel="voption'+tm+'" title="delete this option" alt="" style="cursor:pointer"></i></td></tr>');
                    });

                    $('body').on("click", '.delet_voption', function(){
                        if(confirm("Are you sure to remove"))
                            $('#'+$(this).attr("rel")).remove();
                    });
                });

            </script>

            <!-- Tick to Enable Licensing For this package -->
            <div class="panel panel-default">
                <div class="panel-heading"><?php _e('Licensing Option','wpdm-premium-packages'); ?></div>
                <div class="panel-body">
                    <label>
                        <input type="checkbox" id="licreq"  style="margin: 0 !important;" value="1" name="file[enable_license]" <?php if(get_post_meta($post->ID, "__wpdm_enable_license", true)==1) echo 'checked="checked"'; ?> > &nbsp;<?php _e('Enable Licensing','wpdm-premium-packages'); ?>
                    </label>
                    <div id="licopt" style="display:<?php echo (get_post_meta($post->ID, "__wpdm_enable_license", true)==1)?"block":"none"; ?>" >
                    <hr/>

                        <?php
                        $pre_licenses = wpdmpp_get_licenses();
                        $license_infs = get_post_meta($post->ID, "__wpdm_license", true);
                        $license_infs = maybe_unserialize($license_infs);
                        $zl = 0;
                        ?>
                        <table class="table table-v table-bordered" id="voption_table">
                            <tbody>
                            <tr>
                                <th><?php _e('License Name','wpdm-premium-packages'); ?></th>
                                <th width="150px"><?php _e('Price','wpdm-premium-packages'); ?></th>
                                <th width="50px"><?php _e('Available','wpdm-premium-packages'); ?></th>
                            </tr>
                            <?php foreach ($pre_licenses as $licid => $pre_license): ?>

                            <tr id="row_<?php echo $licid; ?>">
                                <td><?php echo $pre_license['name']; ?></td>
                                <td><input style="max-width: 120px" min="0"
                                           name="file[license][<?php echo $licid; ?>][price]"
                                           class="form-control" data-index="<?php echo $zl;?>"
                                           id="lic-price-<?php echo $licid; ?>"
                                           placeholder="Price" <?php echo $zl == 0 ? 'disabled=disabled' : ''; ?>
                                           value="<?php echo $zl == 0 ? number_format($base_price,2) : ( isset($license_infs[$licid] ) && isset($license_infs[$licid]['price']) ? $license_infs[$licid]['price']:''); ?>"
                                           type="number">
                                </td>
                                <td class="text-center">
                                    <input type="hidden" name="file[license][<?php echo $licid; ?>][active]" value="<?php echo $zl == 0 ? 1 : 0 ?>">
                                    <input class="lic-enable" data-lic="<?php echo $licid; ?>"
                                           id="lic-enable-<?php echo $licid; ?>"
                                           type="checkbox" <?php echo $zl == 0 ? 'disabled=disabled':''; ?> <?php echo (isset($license_infs[$licid]) && $license_infs[$licid]['active'] == 1) || $zl == 0?'checked=checked':''; ?>
                                           value="1" name="file[license][<?php echo $licid; ?>][active]">
                                </td>
                            </tr>
                            <?php $zl++; ?>

                            <?php endforeach; ?>

                            </tbody>
                        </table>
                        <label>
                            <input type="checkbox" id="lickreq"  style="margin: 0 !important;" value="1" name="file[enable_license_key]" <?php if(get_post_meta($post->ID, "__wpdm_enable_license_key", true)==1) echo 'checked="checked"'; ?> > &nbsp;<?php _e('License Key Required','wpdm-premium-packages'); ?>
                        </label>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12  wpdm-full-front">
            <div id="wpdmpp_discount">
                <?php if(is_admin()){ ?>
                <div class="panel panel-default">
                    <div class="panel-heading"><?php _e('Role Based Discount','wpdm-premium-packages'); ?></div>
                    <?php $discount = get_post_meta($post->ID, '__wpdm_discount', true);  ?>
                    <table class="table table-v">
                        <tr>
                            <th align="left"><?php _e('Role','wpdm-premium-packages'); ?></th>
                            <th align="left"><?php _e('Discount','wpdm-premium-packages'); ?> (%)</th>
                        </tr>
                        <?php
                        global $wp_roles;
                        $roles = array_reverse($wp_roles->role_names);
                        foreach( $roles as $role => $name ) {
                        if(  isset($currentAccess) ) $sel = ( in_array($role,$currentAccess) ) ? 'checked' : '';
                        ?>
                        <tr>
                            <td><?php echo $name; ?> (<?php echo $role; ?>) </td>
                            <td><input class="form-control input-sm" style="width: 70px" type="text" size="8" name="file[discount][<?php echo $role; ?>]" value="<?php if(isset($discount[$role])) echo $discount[$role]; ?>"></td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
                <?php } ?>

            </div>
        </div>
    </div>
</div>
<div style="clear: both;"></div>

<script type="text/javascript">
    var cdtm = new Date().getTime();

    jQuery(function ($) {
        $('#currentfiles').bind("DOMSubtreeModified",function(){
            var html = "";
            $('.faz').each(function () {
                html += "<option value='"+$(this).val()+"'>"+$(this).val()+"</option>"
            });
            $('#free_downloads').html(html);
            console.log(html);
            $('#free_downloads').chosen();
        });

        var _file_frame;

        $('body').on('click', '.delete-freedl', function( event ){
            if(!confirm('Are you sure?')) return false;
            $($(this).data('id')).slideUp(function () {
                $(this).remove();
            });
        });
        $('body').on('click', '#addfreedls', function( event ){

            event.preventDefault();

            // If the media frame already exists, reopen it.
            if ( _file_frame ) {
                _file_frame.open();
                return;
            }

            // Create the media frame.
            _file_frame = wp.media.frames.file_frame = wp.media({
                title: $( this ).data( 'uploader_title' ),
                button: {
                    text: $( this ).data( 'uploader_button_text' )
                },
                multiple: false  // Set to true to allow multiple files to be selected
            });

            // When an image is selected, run a callback.
            _file_frame.on( 'select', function() {
                // We set multiple to false so only get one image from the uploader
                attachment = _file_frame.state().get('selection').first().toJSON();
                var file = attachment.url;
                var newDate = new Date;
                var ID = newDate.getTime();

                /*file = file.replace('<?php echo home_url('/'); ?>','<?php echo str_replace("\\", "/", ABSPATH); ?>');*/
                $('#free-files').append("<div class='list-group-item'  id='"+ID+"'><div class='input-group'><input class='form-control' readonly=readonly name='file[free_downloads][]' value='"+file+"' /><span class='input-group-addon delete-freedl' style='background: #ffffff' data-id='#"+ID+"'><i class='fas fa-trash color-red'></i></span></div></div>");


            });

            // Finally, open the modal
            _file_frame.open();
            return false;
        });


    });

</script>
<style>#free_downloads_chosen{ width: 100% !important; }.delete_pp_coupon,.delete-freedl{ cursor: pointer; } button .screen-reader-text,.search-form .screen-reader-text{ display: none; } .</style>
