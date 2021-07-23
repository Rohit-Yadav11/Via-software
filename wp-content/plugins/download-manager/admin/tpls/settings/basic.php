
 <style>
 .frm td{
     padding:5px;
     border-bottom: 1px solid #eeeeee;

     font-size:10pt;

 }
 h4{
     color: #336699;
     margin-bottom: 0px;
 }
 em{
     color: #888;
 }
.wp-switch-editor{
    height: 27px !important;
}
 </style>





                <div class="panel panel-default">
                    <div class="panel-heading"><?php _e( "URL Structure" , "download-manager" ); ?></div>
                    <div class="panel-body">
                        <p><em><?php echo __('If you like, you may enter custom structures for your wpdm category and package URLs here. For example, using "<b>packages</b>" as your category base would make your category links like http://example.org/<b>packages</b>/category-slug/. If you leave these blank the defaults will be used.'); ?><br/>
                                <?php echo __( "Caution: Use unique word for each url base. Also, don't create any page or post with same slug you used for WPDM URL Bases below." , "download-manager" ); ?>
                        </em></p>
                        <div class="form-group">
                            <label><?php echo __( "WPDM Category URL Base" , "download-manager" ); ?></label>
                            <input type="text" class="form-control" name="__wpdm_curl_base" value="<?php echo get_option('__wpdm_curl_base','downloads'); ?>" />
                         </div>
                        <div class="form-group">
                            <label><?php echo __( "WPDM Package URL Base" , "download-manager" ); ?></label>
                            <input type="text" class="form-control" name="__wpdm_purl_base" value="<?php echo get_option('__wpdm_purl_base','download'); ?>" />
                         </div>
                        <div class="form-group">
                            <label><?php echo __( "WPDM Archive Page" , "download-manager" ); ?></label><br/>
                            <select id="wpdmap" class="form-control" name="__wpdm_has_archive" style="width: 120px">
                                <option value="0"><?php _e( "Disabled" , "download-manager" ); ?></option>
                                <option value="1" <?php echo get_option('__wpdm_has_archive')=='1'?'selected':''?>><?php _e( "Enabled" , "download-manager" ); ?></option>
                            </select>

                        </div>
                        <div class="form-group" id="aps" <?php echo get_option('__wpdm_has_archive')=='1'?'':'style="display:none;"'?>>
                            <label><?php _e( "Archive Page Slug" , "download-manager" ); ?></label>
                            <input type="text" class="form-control" name="__wpdm_archive_page_slug" value="<?php echo get_option('__wpdm_archive_page_slug','all-downloads'); ?>" />
                            <em></em>
                        </div>


                    </div>
                </div>



                <div class="panel panel-default">
                    <div class="panel-heading"><?php _e( "Access Settings" , "download-manager" ); ?></div>
                    <div class="panel-body">

                        <div class="form-group">
                            <label><?php echo __( "When user is not allowed to download:" , "download-manager" ); ?></label><br/>
                            <select name="_wpdm_hide_all">
                                <option value="0"><?php echo __( "Only Block Download Link" , "download-manager" ); ?></option>
                                <option value="1" <?php echo get_option('_wpdm_hide_all',0)==1?'selected=selected':''; ?>><?php echo __( "Hide Everything" , "download-manager" ); ?></option>
                            </select>
                         </div>

                       <div class="form-group">
                            <label><?php echo __( "File Browser Root:" , "download-manager" ); ?></label><span title="<?php echo __( "Root dir for server file browser.<br/><b>*Don't add tailing slash (/)</b>" , "download-manager" ); ?>" class="info infoicon">(?)</span>
                           <div class="input-group">
                           <input type="text" class="form-control" value="<?php echo get_option('_wpdm_file_browser_root',str_replace("\\", "/", ABSPATH)); ?>" name="_wpdm_file_browser_root" id="_wpdm_file_browser_root" />
                            <span class="input-group-btn">
                                    <button class="btn btn-secondary ttip" title="<?php _e('Reset Base Dir'); ?>" type="button" onclick="jQuery('#_wpdm_file_browser_root').val('<?php echo rtrim(str_replace("\\", "/", ABSPATH),'/'); ?>');"><i class="fas fa-redo"></i></button>
                                </span>
                       </div>
                       </div>

                        <div class="form-group">
                            <label><?php echo __( "File Browser Access:" , "download-manager" ); ?></label><br/>
                            <input type="hidden" name="_wpdm_file_browser_access[]" value="[NONE]" />
                            <select style="width: 100%" name="_wpdm_file_browser_access[]" multiple="multiple" data-placeholder="<?php _e( "Who will have access to server file browser" , "download-manager" ); ?>">
                                <?php

                                $currentAccess = maybe_unserialize(get_option( '_wpdm_file_browser_access', array('administrator')));
                                $selz = '';

                                ?>

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

                        <br/>
                        <fieldset>
                        <legend><?php echo __( "reCAPTCHA Lock Settings" , "download-manager" ); ?></legend>
                        <div class="form-group">
                            <label><a name="liappid"></a><?php echo __( "reCAPTCHA Site Key" , "download-manager" ); ?></label>
                            <input type="text" class="form-control" name="_wpdm_recaptcha_site_key" value="<?php echo get_option('_wpdm_recaptcha_site_key'); ?>">
                            <em>Register a new site for reCAPTCHA from <a target="_blank" href='https://www.google.com/recaptcha/admin#list'>here</a></em>
                        </div>
                        <div class="form-group">
                            <label><a name="liappid"></a><?php echo __( "reCAPTCHA Secret Key" , "download-manager" ); ?></label>
                            <input type="text" class="form-control" name="_wpdm_recaptcha_secret_key" value="<?php echo get_option('_wpdm_recaptcha_secret_key'); ?>">
                            <em>Register a new site for reCAPTCHA from <a target="_blank" href='https://www.google.com/recaptcha/admin#list'>here</a></em>
                        </div>
                        <div class="form-group">
                            <input type="hidden" value="0" name="__wpdm_recaptcha_regform">
                            <label><input type="checkbox" name="__wpdm_recaptcha_regform" value="1" <?php checked(1, get_option('__wpdm_recaptcha_regform')); ?> > <?php echo __( "Enable sign up form CAPTCHA validation" , "download-manager" ); ?></label>
                        </div>
                        <div class="form-group">
                            <input type="hidden" value="0" name="__wpdm_recaptcha_loginform">
                            <label><input type="checkbox" name="__wpdm_recaptcha_loginform" value="1" <?php checked(1, get_option('__wpdm_recaptcha_loginform')); ?>> <?php echo __( "Enable sign in form CAPTCHA validation" , "download-manager" ); ?></label>
                        </div>
                        </fieldset>

                    </div>
                </div>

 <div class="panel panel-default">
     <div class="panel-heading"><?php _e( "Verification Settings" , "download-manager" ); ?></div>
     <div class="panel-body">

         <fieldset>
             <legend><?php echo __( "Blocked IPs" , "download-manager" ); ?></legend>
             <div class="form-group">
                 <textarea placeholder="<?php _e( "One IP per line" , "download-manager" ); ?>" rows="5" class="form-control" name="__wpdm_blocked_ips"><?php echo esc_attr(get_option('__wpdm_blocked_ips')); ?></textarea>
                 <em><?php _e( "List IP Addresses to blacklist. One IP per line ( Ex: IPv4 - 192.168.23.12 or 192.168.23.1/24 or 192.168.23.* , IPv6 - 2a01:8760:2:3001::1 or 2620:112:3000::/44 )" , "download-manager" ); ?></em>
             </div>
             <div class="form-group">
                 <textarea placeholder="<?php _e( "Message to show when an IP is blocked" , "download-manager" ); ?>" class="form-control" name="__wpdm_blocked_ips_msg"><?php echo get_option('__wpdm_blocked_ips_msg'); ?></textarea>
                 <em><?php _e( "Message to show when an IP is blocked" , "download-manager" ); ?></em>
             </div>
         </fieldset>
         <hr/>

         <fieldset>
             <legend><?php echo __('Block Emails','download-manager'); ?></legend>
                 <div class="form-group">
                     <label><?php echo __( "Blocked Domains:" , "download-manager" ); ?></label><br/>
                     <textarea name="__wpdm_blocked_domains" class="input form-control"><?php echo get_option('__wpdm_blocked_domains',''); ?></textarea>
                     <em>One domain per line</em>
                 </div>

                 <div class="form-group">
                     <label><?php echo __( "Blocked Emails:" , "download-manager" ); ?></label><br/>
                     <textarea name="__wpdm_blocked_emails" class="input form-control"><?php echo get_option('__wpdm_blocked_emails',''); ?></textarea>
                     <em>One email per line</em>
                 </div>
                 <div class="form-group">
                     <textarea placeholder="<?php _e( "Message to show when a email or domain is blocked" , "download-manager" ); ?>" class="form-control" name="__wpdm_blocked_domain_msg"><?php echo get_option('__wpdm_blocked_domain_msg'); ?></textarea>
                     <em><?php _e( "Message to show when a email or domain is blocked" , "download-manager" ); ?></em>
                 </div>
             </fieldset>


     </div>
 </div>



 <div class="panel panel-default">
                    <div class="panel-heading"><?php _e( "Upload Settings" , "download-manager" ); ?></div>
                    <div class="panel-body">

                        <div class="form-group">
                            <label><?php echo __( "When File Already Exists:" , "download-manager" ); ?></label><br/>
                            <select name="__wpdm_overwrrite_file">
                                <option value="0"><?php echo __('Rename New File'); ?></option>
                                <option value="1" <?php echo get_option('__wpdm_overwrrite_file',0)==1?'selected=selected':''; ?>><?php echo __( "Overwrite" , "download-manager" ); ?></option>
                            </select>
                         </div>

                        <hr/>
                        <div class="form-group">
                            <input type="hidden" value="0" name="__wpdm_sanitize_filename" />
                            <label><input style="margin-right: 5px" <?php checked(1, get_option('__wpdm_sanitize_filename',0)); ?> type="checkbox" value="1" name="__wpdm_sanitize_filename"><?php _e( "Sanitize Filename" , "download-manager" ); ?></label><br/>
                            <em><?php _e( "Check the option if you want to sanitize uploaded file names to remove illegal chars" , "download-manager" ); ?></em>
                            <br/>

                        </div>

                        <hr/>
                        <div class="form-group">
                            <input type="hidden" value="0" name="__wpdm_chunk_upload" />
                            <label><input style="margin-right: 5px" <?php checked(1, get_option('__wpdm_chunk_upload',0)); ?> type="checkbox" value="1" name="__wpdm_chunk_upload"><?php _e('Chunk Upload','download-manager'); ?></label><br/>
                            <em><?php _e('Check the option if you want to enable chunk upload to override http upload limits','download-manager'); ?></em>
                            <br/>

                        </div>
                        <div class="form-group">
                            <label><?php _e('Chunk Size','download-manager'); ?></label><br/>
                            <div class="input-group">
                                <input class="form-control" value="<?php echo get_option('__wpdm_chunk_size',1024); ?>" type="number"   name="__wpdm_chunk_size">
                                <div class="input-group-addon">KB</div>
                            </div>
                            <br/>

                        </div>


                    </div>
                </div>


                <div class="panel panel-default">
                    <div class="panel-heading"><?php echo __( "Messages" , "download-manager" ); ?></div>
                    <div class="panel-body">

                        <div class="form-group">
                            <label><?php echo __( "Plugin Update Notice:" , "download-manager" ); ?></label><br>
                            <select name="wpdm_update_notice">
                                <option value="0"><?php echo __( "Enabled" , "download-manager" ); ?></option>
                                <option value="disabled" <?php selected(get_option('wpdm_update_notice'),'disabled'); ?>><?php echo __( "Disabled" , "download-manager" ); ?></option>
                            </select>
                         </div>

                        <div class="form-group">
                            <label for="__wpdm_permission_denied_msg"><?php echo __( "Permission Denied Message for Packages:" , "download-manager" ); ?></label>
                                <textarea id="__wpdm_permission_denied_msg" name="__wpdm_permission_denied_msg" class="form-control"><?php echo stripslashes(get_option('__wpdm_permission_denied_msg'));?></textarea>
                         </div>

                        <div class="form-group">
                            <label><?php echo __( "Download Limit Message:" , "download-manager" ); ?></label>
                            <textarea class="form-control" cols="70" rows="6" name="__wpdm_download_limit_exceeded"><?php echo stripslashes(get_option('__wpdm_download_limit_exceeded','<i class="fa fa-exclamation-triangle"></i> '.__( "Download Limit Exceeded!" , "download-manager" ))); ?></textarea>

                        </div>

                 <div class="form-group">
                            <label><?php echo __( "Login Required Message:" , "download-manager" ); ?></label>
                     <textarea class="form-control" cols="70" rows="6" name="wpdm_login_msg"><?php echo get_option('wpdm_login_msg', false)?stripslashes(get_option('wpdm_login_msg')):('<div class="w3eden"><div class="panel panel-default card"><div class="panel-body card-body"><span class="text-danger">Login is required to access this page</span></div><div class="panel-footer card-footer text-right"><a href="'.wp_login_url().'?redirect_to=[this_url]" class="btn btn-danger wpdmloginmodal-trigger btn-sm"><i class="fa fa-lock"></i> Login</a></div></div></div>'); ?></textarea>
                     <em class="note"><?php echo sprintf(__( "If you want to show login form instead of message user short-code [wpdm_login_form]. To show login form in a modal popup, please follow %s the doc here %s" , "download-manager" ), "<a target='_blank' href='https://www.wpdownloadmanager.com/how-to-add-modal-popup-login-form-in-your-wordpress-site/'>", "</a>"); ?></em>

                 </div>

                    </div>
                </div>

 <div class="panel panel-default">
     <div class="panel-heading"><?php echo __( "File Download" , "download-manager" ); ?></div>
     <div class="panel-body">

         <div class="form-group">
             <label><input onclick="jQuery('#pdm').slideUp();" type="radio" <?php checked(get_option('__wpdm_parallel_download', 1),1); ?>  name="__wpdm_parallel_download" value="1">  <?php _e( "Enable Parallel Download" , "download-manager" ); ?> &nbsp; </label>
             <label><input onclick="jQuery('#pdm').slideDown();" type="radio" <?php checked(get_option('__wpdm_parallel_download', 1),0); ?>  name="__wpdm_parallel_download" value="0">  <?php _e( "Disable Parallel Download" , "download-manager" ); ?></label><br/>
             <em><?php _e( "Enable/Disable multiple simultaneous downloads from same IP address" , "download-manager" ); ?></em>
         </div><hr/>

         <div id="pdm" class="form-group" <?php if((int)get_option('__wpdm_parallel_download', 1) === 1) echo 'style="display:none;"'; ?>>
             <label><?php echo __( "Message to show when a download is blocked", "download-manager" ) ?></label>
             <textarea class="form-control" name="__wpdm_parallel_download_msg"><?php echo get_option('__wpdm_parallel_download_msg', "Another download is in progress from your IP, please wait until finished."); ?></textarea>
             <hr/>
         </div>

         <div class="form-group">
             <label><?php _e( "Private Download Link Usage Limit" , "download-manager" ); ?>:</label><br/>
             <input class="form-control" type="number" name="__wpdm_private_link_usage_limit" value="<?php echo intval(get_option('__wpdm_private_link_usage_limit',3)); ?>">
             <em><?php _e('Private download links ( package with any lock option active ) will expire after it exceeds the limit', "download-manager"); ?></em>
         </div><hr/>
         <div class="form-group">
             <label><?php _e( "Private Download Link Expiration Period" , "download-manager" ); ?>:</label><br/>
             <div class="row">
                 <div class="col-md-6" style="padding-right: 0">
                     <input min="1" step="1" class="form-control" name="__wpdm_private_link_expiration_period" type="number" value="<?php echo intval(get_option('__wpdm_private_link_expiration_period',3)); ?>">
                 </div>
                 <div class="col-md-6">
                     <select name="__wpdm_private_link_expiration_period_unit" class="form-control wpdm-custom-select" style="min-width: 100%;max-width: 100%">
                         <option value="60">Mins</option>
                         <option value="3600" <?php selected(intval(get_option('__wpdm_private_link_expiration_period_unit',0)), 3600); ?>>Hours</option>
                         <option value="86400" <?php selected(intval(get_option('__wpdm_private_link_expiration_period_unit',0)), 86400); ?>>Days</option>
                     </select>
                 </div>
             </div>
             <em><?php _e("Private download links ( package with any lock option active ) will expire after the period starting from it's generation time", "download-manager"); ?></em>
         </div><hr/>
         <div class="form-group">
             <input type="hidden" name="__wpdm_flat_download_url" value="0">
             <label><input type="checkbox" <?php checked(get_option('__wpdm_flat_download_url'),1); ?>  name="__wpdm_flat_download_url" value="1"> <?php _e( "Activate flat download url" , "download-manager" ); ?> <span class="label label-info">beta</span> </label><br/>
             <em><?php echo sprintf(__( "Download url will looks like real file path, ex: %s" , "download-manager" ), home_url("/wpdmdl/pid/fileid/filename.ext")); ?></em>
         </div><hr/>
         <div class="form-group"><input type="hidden" name="__wpdm_mask_link" value="0">
             <label><input type="radio" <?php checked(get_option('__wpdm_mask_dlink', 1),1); ?>  name="__wpdm_mask_dlink" value="1">  <?php _e( "Mask Download Link" , "download-manager" ); ?> &nbsp; </label>
             <label><input type="radio" <?php checked(get_option('__wpdm_mask_dlink', 1),0); ?>  name="__wpdm_mask_dlink" value="0">  <?php _e( "Unmask Download Link" , "download-manager" ); ?></label><br/>
             <em><?php _e( "Check this option if you want to mask/unmask file download link. If you unmask download link, bots will be able the find any public download link easily." , "download-manager" ); ?></em>
         </div><hr/>
         <div class="form-group"><input type="hidden" name="__wpdm_individual_file_download" value="0">
             <label><input type="radio" <?php checked(get_option('__wpdm_individual_file_download', 1),1); ?>  name="__wpdm_individual_file_download" value="1">  <?php _e( "Enable Single File Download" , "download-manager" ); ?> &nbsp; </label>
             <label><input type="radio" <?php checked(get_option('__wpdm_individual_file_download', 1),0); ?>  name="__wpdm_individual_file_download" value="0">  <?php _e( "Disable Single File Download" , "download-manager" ); ?></label><br/>
             <em><?php _e( "Check this option if you want to enable/disable single file download from multi-file package" , "download-manager" ); ?></em>
         </div><hr/>
         <div class="form-group"><input type="hidden" name="__wpdm_cache_zip" value="0">
             <label><input type="checkbox" <?php checked(get_option('__wpdm_cache_zip'),1); ?>  name="__wpdm_cache_zip" value="1">  <?php _e( "Cache created zip file from multi-file package" , "download-manager" ); ?></label><br/>
             <em><?php _e( "Check this option if you want to cache the zip file created from multi-file package when someone tries to download" , "download-manager" ); ?></em>
         </div><hr/>

         <div class="form-group">
             <label><?php echo __( "Download Speed:" , "download-manager" ); ?></label>
             <div class="input-group">
                 <input type=text class="form-control" name="__wpdm_download_speed" value="<?php echo intval(get_option('__wpdm_download_speed',4096)); ?>" />
                 <span class="input-group-addon">KB/s</span>
             </div>
         </div>
         <hr/>
         <em class="note"><?php _e( "If you get broken download, then try enabling/disabling following options, as sometimes server may not support output buffering or partial downloads" , "download-manager" ); ?>:</em>
         <hr/>
         <div class="form-group">
             <label><?php _e( "Resumable Downloads" , "download-manager" ); ?></label><br/>
             <select name="__wpdm_download_resume">
                 <option value="1"><?php _e( "Enabled" , "download-manager" ); ?></option>
                 <option value="2" <?php selected(get_option('__wpdm_download_resume'), 2); ?>><?php _e( "Disabled" , "download-manager" ); ?></option>
             </select>
         </div>
         <div class="form-group">
             <label><?php _e( "Output Buffering" , "download-manager" ); ?></label><br/>
             <select name="__wpdm_support_output_buffer">
                 <option value="1"><?php _e( "Enabled" , "download-manager" ); ?></option>
                 <option value="0" <?php selected(get_option('__wpdm_support_output_buffer'), 0); ?>><?php _e( "Disabled" , "download-manager" ); ?></option>
             </select>
         </div>

         <div class="form-group"><hr/>
             <input type="hidden" value="0" name="__wpdm_open_in_browser" />
             <label><input style="margin-right: 5px" type="checkbox" <?php checked(get_option('__wpdm_open_in_browser'),1); ?> value="1" name="__wpdm_open_in_browser"><?php _e( "Open in Browser" , "download-manager" ); ?></label><br/>
             <em><?php _e( "Try to Open in Browser instead of download when someone clicks on download link" , "download-manager" ); ?></em>
             <br/>

         </div>
         <hr/>
         <div class="form-group">
             <label><?php _e( "Skip Lock for Loggedin User:" , "download-manager" ); ?></label><br/>
             <select style="width: 100%" name="__wpdm_skip_locks[]" multiple="multiple" data-placeholder="<?php _e( "Select..." , "download-manager" ); ?>">
                 <option value="password" <?php if(in_array('password', maybe_unserialize(get_option('__wpdm_skip_locks', array())))) echo 'selected=selected'; ?>>Password</option>
                 <option value="email" <?php if(in_array('email', maybe_unserialize(get_option('__wpdm_skip_locks', array())))) echo 'selected=selected'; ?>>Email</option>
                 <option value="facebooklike" <?php if(in_array('facebooklike', maybe_unserialize(get_option('__wpdm_skip_locks', array())))) echo 'selected=selected'; ?>>Facebook Like</option>
                 <option value="linkedin" <?php if(in_array('linkedin', maybe_unserialize(get_option('__wpdm_skip_locks', array())))) echo 'selected=selected'; ?>>Linkedin Share</option>
                 <option value="gplusone" <?php if(in_array('gplusone', maybe_unserialize(get_option('__wpdm_skip_locks', array())))) echo 'selected=selected'; ?>>Google+</option>
                 <option value="tweet" <?php if(in_array('tweet', maybe_unserialize(get_option('__wpdm_skip_locks', array())))) echo 'selected=selected'; ?>>Tweet</option>
                 <option value="follow" <?php if(in_array('follow', maybe_unserialize(get_option('__wpdm_skip_locks', array())))) echo 'selected=selected'; ?>>Twitter Follow</option>
            </select>

         </div>
     </div>
 </div>





 <div class="panel panel-default">
                    <div class="panel-heading"><?php _e( "Misc Settings" , "download-manager" ); ?></div>
                    <div class="panel-body">


                        <table cellpadding="5" cellspacing="0" class="frm" width="100%">

                            <?php do_action('basic_settings'); ?>

                        </table>

                    </div>



                </div>

                <?php do_action('basic_settings_section'); ?>

<script>
    jQuery(function($) {
        $('#wpdmap').change(function(){

            if(this.value==1)
                $('#aps').slideDown();
            else
                $('#aps').slideUp();
        });
    });
</script>
 <style>
     .w3eden textarea.form-control{
         min-width: 100%;
         max-width: 100%;
         width: 100%;
         height: 70px;
     }
 </style>
