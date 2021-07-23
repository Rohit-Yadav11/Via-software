<?php
global $current_user;
$files = maybe_unserialize(get_post_meta($post->ID, '__wpdm_files', true));

if (!is_array($files)) $files = array();

//if(count($files)>15)
//include(dirname(__FILE__)."/attached-files-datatable.php");
//else {
?>


<div id="currentfiles" class="w3eden">



                    <?php
                    $file_index = 0;
                    if(isset($post->post_author)) {
                        $user = get_user_by('id', $post->post_author);
                        $user_upload_dir = UPLOAD_DIR . $user->user_login . '/';
                    }
                    $fileinfo = get_post_meta($post->ID, '__wpdm_fileinfo', true);
                    if (!$fileinfo) $fileinfo = array();
                    foreach ($files as $id => $value): ++$file_index;
                        if (!isset($fileinfo[$value]) || !@is_array($fileinfo[$value])) $fileinfo[$value] = array('title'=>'','password'=>'');
                          $svalue = $value;
                        if(strlen($value)>50){
                            $svalue = substr($value, 0,23)."...".substr($value, strlen($value)-27);
                        }
                        $imgext = array('png','jpg','jpeg', 'gif');
                        $ext = explode(".", $value);
                        $ext = end($ext);
                        $ext = strtolower($ext);
                        $filepath = file_exists($value)?$value:(file_exists($user_upload_dir.$value)?$user_upload_dir.$value:UPLOAD_DIR.$value);
                        $thumb = "";
                        if(in_array($ext, $imgext))
                            $thumb = wpdm_dynamic_thumb($filepath, array(48, 48));

                        if($ext=='')
                            $ext = '_blank';
                        ?>
                        <div class="cfile">
                            <div class="panel panel-default panel-file">
                                <input class="faz" type="hidden" value="<?php echo $value; ?>" name="file[files][<?php echo $id; ?>]">
                                <div class="panel-heading"><button type="button" class="btn btn-xs btn-secondary pull-right" rel="del"><i class="fas fa-trash color-red"></i></button> <span title="<?php echo $value; ?>"><?php echo strlen($value)<100?$value:substr($value, 0, 80).'...'; ?></span></div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="media">
                                                <div class="pull-left">

                                                    <img class="file-ico" src="<?php echo $thumb?$thumb:\WPDM\libs\FileSystem::fileTypeIcon($ext);?>" />
                                                </div>
                                            <div class="media-body">
                                            <input placeholder="<?php _e( "File Title" , "download-manager" ); ?>" title="<?php _e( "File Title" , "download-manager" ); ?>" class="form-control" type="text" name='file[fileinfo][<?php echo $id; ?>][title]' value="<?php echo !isset($fileinfo[$id]['title'])?esc_html($fileinfo[$value]['title']):esc_html($fileinfo[$id]['title']); ?>" /><br/>
                                            <div class="row">
                                            <div class="col-md-12">
                                            <div class="input-group">
                                            <input placeholder="<?php _e( "File Password" , "download-manager" ); ?>"  title="<?php _e( "File Password" , "download-manager" ); ?>" class="form-control" type="text" id="indpass_<?php echo $file_index; ?>" name='file[fileinfo][<?php echo $id; ?>][password]' value="<?php echo !isset($fileinfo[$id]['password'])?esc_html($fileinfo[$value]['password']):$fileinfo[$id]['password']; ?>">
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-secondary" class="genpass" title='Generate Password' onclick="return generatepass('indpass_<?php echo $file_index; ?>')"><i class="fa fa-ellipsis-h"></i></button>
                                            </span>
                                            </div>
                                                </div>

                                                <?php if (class_exists('WPDMPremiumPackage')){ ?>
                                                    <div class="col-md-12" style="margin-top: 10px">

                                                        <div class="file-access-settings file-access-settings-<?php echo $post->ID; ?>" id="file-access-settings-<?php echo $id; ?>">

                                                            <?php
                                                            $license_req = get_post_meta($post->ID, "__wpdm_enable_license", true);

                                                            $pre_licenses = wpdmpp_get_licenses();
                                                            $license_infs = get_post_meta($post->ID, "__wpdm_license", true);
                                                            $license_infs = maybe_unserialize($license_infs);
                                                            $zl = 0;
                                                            ?>
                                                            <table class="table table-v table-bordered file-price-data file-price-table" <?php if($license_req != 1) echo "style='display:none;'"; ?>>
                                                                <tr>
                                                                    <?php foreach ($pre_licenses as $licid => $lic) { echo "<th>{$lic['name']}</th>"; } ?>
                                                                </tr>
                                                                <tr>
                                                                    <?php foreach ($pre_licenses as $licid => $pre_license){ ?>
                                                                        <td><input min="0" name="file[fileinfo][<?php echo $id; ?>][license_price][<?php echo $licid; ?>]" class="form-control lic-file-price-<?php echo $licid; ?>" id="lic-file-price-<?php echo $licid; ?>" placeholder="Price" value="<?php echo !isset($fileinfo[$id]['license_price']) || !isset($fileinfo[$id]['license_price'][$licid]) || $fileinfo[$id]['license_price'][$licid] == ''?(isset($fileinfo[$id]['price']) && $zl == 0?$fileinfo[$id]['price']:''):$fileinfo[$id]['license_price'][$licid]; ?>" type="text"></td>
                                                                        <?php $zl++; } ?>
                                                                </tr>

                                                                </tbody></table>

                                                            <div class="input-group file-price-data file-price-field" <?php if($license_req == 1) echo "style='display:none;'"; ?>>
                                                                <span class="input-group-addon"><?php _e('Price:','wpdmpo'); ?></span><input class="form-control" type="text" name="file[fileinfo][<?php echo $id; ?>][price]" value="<?php echo !isset($fileinfo[$id]['price'])?'':$fileinfo[$id]['price']; ?>" />
                                                            </div>

                                                        </div>
                                                    </div>
                                                <?php } ?>

                                                </div>
                                                <?php do_action("wpdm_attached_file", $post->ID, $id); ?>
                                            </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    endforeach;
                    ?>



                <?php // if ($files): ?>
                    <script type="text/javascript">


                        jQuery('body').on('click','button[rel=del], button[rel=undo]', function () {

                            if (jQuery(this).attr('rel') == 'del') {

                                jQuery(this).parents('div.panel-file').removeClass('panel-default').addClass('panel-danger').find('input.faz').attr('name', 'del[]');
                                jQuery(this).attr('rel', 'undo').html('<i class="fa fa-sync color-green"></i>');

                            } else {


                                jQuery(this).parents('div.panel-file').removeClass('panel-danger').addClass('panel-default').find('input.faz').attr('name', 'file[files][]');
                                jQuery(this).attr('rel', 'del').html('<i class="fas fa-trash color-red"></i>');


                            }

                            return false;
                        });

                        jQuery(function(){
                            try {
                                jQuery('#currentfiles').sortable();
                            } catch (e) {

                            }
                        });


                    </script>


                <?php //endif; ?>


            </div>
<?php //} ?>