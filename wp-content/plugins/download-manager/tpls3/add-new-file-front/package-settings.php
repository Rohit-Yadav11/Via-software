<div class="panel panel-default"
     <?php if (in_array('settings', $hide)) { ?>style="display: none"<?php } ?>
     id="package-settings-section">
    <div class="panel-heading"><b><?php _e("Package Settings", "download-manager"); ?></b>
    </div>
    <div class="panel-body-set">
        <div>
        <ul class="nav nav-tabs">
            <li class="nav-item"><a class="nav-link active" href="#package-settings" data-toggle="tab"><?php echo __( "Package Settings" , "download-manager" ); ?></a></li>
            <li class="nav-item"><a class="nav-link" href="#lock-options" data-toggle="tab"><?php echo __( "Lock Options" , "download-manager" ); ?></a></li>
            <li class="nav-item"><a class="nav-link" href="#package-icons" data-toggle="tab"><?php echo __( "Icons" , "download-manager" ); ?></a></li>
            <?php

            $etabs = apply_filters('wpdm_package_settings_tabs',array());
            foreach($etabs as $id=>$tab){
                echo "<li><a href='#{$id}' data-toggle='tab'>{$tab['name']}</a></li>";

            } ?>
        </ul>

        <div class="tab-content">
        <div id="package-settings" class="tab-pane active">
            <div id="version_row" class="form-group-row row">
                <div class="col-md-3"><?php echo __( "Version:" , "download-manager" ); ?></div>
                <div class="col-md-9"><input size="10" type="text" class="form-control" value="<?php echo get_post_meta($post->ID,'__wpdm_version',true); ?>" name="file[version]" /></div>
            </div>
            <div id="link_label_row" class="form-group-row row">
                <div class="col-md-3"><?php echo __( "Link Label:" , "download-manager" ); ?></div>
                <div class="col-md-9"><input size="10" type="text" class="form-control" value="<?php echo get_post_meta($post->ID,'__wpdm_link_label',true); ?>" name="file[link_label]" /></div>
            </div>
            <div id="stock_limit_row" class="form-group-row row">
                <div class="col-md-3"><?php echo __( "Stock&nbsp;Limit:" , "download-manager" ); ?></div>
                <div class="col-md-9"><input size="10" class="form-control" type="text" name="file[quota]" value="<?php echo get_post_meta($post->ID,'__wpdm_quota',true); ?>" /></div>
            </div>
            <div id="downliad_limit_row" class="form-group-row row">
                <div class="col-md-3"><?php echo __( "Download&nbsp;Limit:" , "download-manager" ); ?></div>
                <div class="col-md-9"><div class="input-group"><input size="10" class="form-control" type="text" name="file[download_limit_per_user]" value="<?php echo get_post_meta($post->ID,'__wpdm_download_limit_per_user', true); ?>" /><div class="input-group-btn"><span class="input-group-text">/ user</span></div></div></div>
            </div>

            <div id="download_count_row" class="form-group-row row">
                <div class="col-md-3"><?php echo __( "View Count:" , "download-manager" ); ?></div>
                <div class="col-md-9"><input size="10" class="form-control" type="text" name="file[view_count]" value="<?php echo get_post_meta($post->ID,'__wpdm_view_count',true); ?>" /></div>
            </div>

            <div id="download_count_row" class="form-group-row row">
                <div class="col-md-3"><?php echo __( "Download&nbsp;Count:" , "download-manager" ); ?></div>
                <div class="col-md-9"><input size="10" class="form-control" type="text" name="file[download_count]" value="<?php echo get_post_meta($post->ID,'__wpdm_download_count',true); ?>" /></div>
            </div>

            <div id="package_size_row" class="form-group-row row">
                <div class="col-md-3"><?php echo __( "Package&nbsp;Size:" , "download-manager" ); ?></div>
                <div class="col-md-9"><input size="10" class="form-control" type="text" name="file[package_size]" value="<?php echo get_post_meta($post->ID,'__wpdm_package_size',true); ?>" /></div>
            </div>

            <div id="access_row" class="form-group-row row">
                <div  class="col-md-3"><?php echo __( "Allow Access:" , "download-manager" ); ?></div>
                <div class="col-md-9">
                    <select name="file[access][]" class="chzn-select role" multiple="multiple" id="access" style="width: 100%;max-width: 100%">
                        <?php

                        $currentAccess = get_post_meta($post->ID, '__wpdm_access', true);
                        $selz = '';
                        if(  $currentAccess ) $selz = (in_array('guest',$currentAccess))?'selected=selected':'';
                        if(!isset($post->ID)) $selz = 'selected=selected';
                        ?>

                        <option value="guest" <?php echo $selz  ?>><?php echo __( "All Visitors" , "download-manager" ); ?></option>
                        <?php
                        global $wp_roles;
                        $roles = array_reverse($wp_roles->role_names);
                        foreach( $roles as $role => $name ) {



                            if(  $currentAccess ) $sel = (in_array($role,$currentAccess))?'selected=selected':'';
                            else $sel = '';



                            ?>
                            <option value="<?php echo $role; ?>" <?php echo $sel  ?>> <?php echo $name; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div id="individual_file_download_row" class="form-group-row row">
                <div class="col-md-3"><?php echo __( "Single Download:" , "download-manager" ); ?></div>
                <div class="col-md-9">


                    <div id="eid">
                        <input class="wpdm-radio" type="radio" value="-1" id="radio3" name="file[individual_file_download]" <?php if(get_post_meta($post->ID,'__wpdm_individual_file_download', true) == -1 || get_post_meta($post->ID,'__wpdm_individual_file_download', true) == null) echo 'checked=checked';?> /> <label for="radio3"><?php _e( "Use Global" , "download-manager" ); ?> &nbsp; </label>
                        <input class="wpdm-radio" type="radio" value="1" id="radio2" name="file[individual_file_download]" <?php checked(get_post_meta($post->ID,'__wpdm_individual_file_download', true),1);?> /> <label for="radio2"><?php _e( "Enable" , "download-manager" ); ?> &nbsp; </label>
                        <input class="wpdm-radio" type="radio" value="0" id="radio1" name="file[individual_file_download]" <?php checked(get_post_meta($post->ID,'__wpdm_individual_file_download', true),0);?> /> <label for="radio1"><?php _e( "Disable" , "download-manager" ); ?></label>
                        <i class="info fa fa-info" title="<?php _e( "Enable/Disable single file download from multi-file package" , "download-manager" ); ?>"></i>
                    </div>

                </div>
            </div>

            <div id="cache_zip_row" class="form-group-row row">
                <div class="col-md-3"><?php echo __( "Cache Zip File:" , "download-manager" ); ?></div>
                <div class="col-md-9">


                    <div id="eid">
                        <input class="wpdm-radio" type="radio" value="-1" id="radio5" name="file[cache_zip]" <?php if(get_post_meta($post->ID,'__wpdm_cache_zip', true) == -1 || get_post_meta($post->ID,'__wpdm_cache_zip', true) == null) echo 'checked=checked';?> /> <label for="radio5"><?php _e( "Use Global" , "download-manager" ); ?> &nbsp; </label>
                        <input class="wpdm-radio" type="radio" value="1" id="radio6" name="file[cache_zip]" <?php checked(get_post_meta($post->ID,'__wpdm_cache_zip', true),1);?> /> <label for="radio6"><?php _e( "Enable" , "download-manager" ); ?> &nbsp; </label>
                        <input class="wpdm-radio" type="radio" value="0" id="radio7" name="file[cache_zip]" <?php checked(get_post_meta($post->ID,'__wpdm_cache_zip', true),0);?> /> <label for="radio7"><?php _e( "Disable" , "download-manager" ); ?></label>
                        <i class="info fa fa-info" title="<?php _e( "Do you want to cache the zip file created from attached files" , "download-manager" ); ?>"></i>
                    </div>

                </div>
            </div>


            <div id="template_row" class="form-group-row row">
                <div class="col-md-3"><?php echo __( "Link Template:" , "download-manager" ); ?></div>
                <div class="col-md-9"><?php

                    echo WPDM\admin\menus\Templates::Dropdown(array('name' => 'file[template]', 'id'=>'lnk_tpl', 'selected' => get_post_meta($post->ID,'__wpdm_template',true)), true);

                    ?>

                </div>
            </div>


            <div id="page_template_row" class="form-group-row row">
                <div class="col-md-3"><?php echo __( "Page Template:" , "download-manager" ); ?></div>
                <div class="col-md-9"><?php
                    echo WPDM\admin\menus\Templates::Dropdown(array('type'=>'page','name' => 'file[page_template]', 'id'=>'pge_tpl', 'selected' => get_post_meta($post->ID,'__wpdm_page_template',true)), true);
                    ?>

                </div>
            </div>


            <div class="clear"></div>
        </div>

            <?php include wpdm_tpl_path('add-new-file-front/lock-options.php', __DIR__); ?>
            <?php include wpdm_tpl_path('add-new-file-front/icons.php', __DIR__); ?>

        <?php foreach($etabs as $id=>$tab){
             echo "<div class='tab-pane' id='{$id}'>";
             call_user_func($tab['callback']);
             echo "</div>";
        } ?>

        </div>
        </div>
    </div>
</div>






<!-- all js --- - -->

<script type="text/javascript">

    jQuery(document).ready(function() {

        // Uploading files
        var file_frame;




        jQuery('body').on('click', '#wpdm-featured-image', function( event ){

            event.preventDefault();

            // If the media frame already exists, reopen it.
            if ( file_frame ) {
                file_frame.open();
                return;
            }

            // Create the media frame.
            file_frame = wp.media.frames.file_frame = wp.media({
                title: jQuery( this ).data( 'uploader_title' ),
                button: {
                    text: jQuery( this ).data( 'uploader_button_text' )
                },
                multiple: false  // Set to true to allow multiple files to be selected
            });

            // When an image is selected, run a callback.
            file_frame.on( 'select', function() {
                // We set multiple to false so only get one image from the uploader
                console.log(file_frame);
                attachment = file_frame.state().get('selection').first().toJSON();
                jQuery('#fpvw').val(attachment.url);
                jQuery('#rmvp').remove();
                jQuery('#img').html("<img src='"+attachment.url+"' style='max-width:100%'/><input type='hidden' name='file[preview]' value='"+attachment.url+"' >");
                jQuery('#img').after('<div class="panel-footer"  id="rmvp"><a href="#" class="text-danger">Remove Featured Image</a></div>');
                file_frame.close();
                // Do something with attachment.id and/or attachment.url here
            });

            // Finally, open the modal
            file_frame.open();
        });


        jQuery('body').on('click', ".cb-enable",function(){
            var parent = jQuery(this).parents('.switch');
            jQuery('.cb-disable',parent).removeClass('selected');
            jQuery(this).addClass('selected');
            jQuery('.checkbox',parent).attr('checked', true);
        });
        jQuery('body').on('click', ".cb-disable",function(){
            var parent = jQuery(this).parents('.switch');
            jQuery('.cb-enable',parent).removeClass('selected');
            jQuery(this).addClass('selected');
            jQuery('.checkbox',parent).attr('checked', false);
        });



        jQuery('body').on('click', '#rmvp',function(){
            jQuery('#fpvw').val('');
            jQuery('#mpim').slideUp().remove();
            jQuery(this).fadeOut();
            jQuery('#img').html('<a href="#" id="wpdm-featured-image">Add Featured Image</a> <input type="hidden" name="file[preview]" value="" id="fpvw" />');
            return false;
        });
        jQuery('body').on('click', '.wpdm-label',function(){
            //alert(jQuery(this).attr('class'));
            if(jQuery(this).hasClass('wpdm-checked')) jQuery(this).addClass('wpdm-unchecked').removeClass('wpdm-checked');
            else jQuery(this).addClass('wpdm-checked').removeClass('wpdm-unchecked');

        });


        jQuery(window).scroll(function(){
            if(jQuery(window).scrollTop()>100)
                jQuery('#action').addClass('action-float').removeClass('action');
            else
                jQuery('#action').removeClass('action-float').addClass('action');
        });

        jQuery("#wpdm-settings select").chosen({no_results_text: ""});

        jQuery('.handlediv').click(function(){
            jQuery(this).parent().find('.inside').slideToggle();
        });

        jQuery('.handle').click(function(){
            jQuery(this).parent().find('.inside').slideToggle();
        });


        jQuery('.nopro').click(function(){
            if(this.checked) jQuery('.wpdmlock').removeAttr('checked');
        });

        jQuery('.wpdmlock').click(function(){
            if(this.checked) {
                jQuery('#'+jQuery(this).attr('rel')).slideDown();
                jQuery('.nopro').removeAttr('checked');
            } else {
                jQuery('#'+jQuery(this).attr('rel')).slideUp();
            }
        });




    });




    function wpdm_view_package(){

    }




    <?php /* if(is_array($file)&&get_post_meta($file['id'],'__wpdm_lock',true)!='') { ?>
    jQuery('#<?php echo get_post_meta($file['id'],'__wpdm_lock',true); ?>').show();
    <?php } */ ?>
</script>
