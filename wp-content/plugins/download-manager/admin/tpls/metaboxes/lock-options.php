<div id="lock-options"  class="tab-pane">
    <?php echo __( "You can use one or more of following methods to lock your package download:" , "download-manager" ); ?>
    <br/>
    <br/>
    <div class="wpdm-accordion w3eden">

        <!-- Terms Lock -->
        <div class="panel panel-default">
            <h3 class="panel-heading"><label><input type="checkbox" class="wpdmlock" rel='terms' name="file[terms_lock]" <?php if(get_post_meta($post->ID,'__wpdm_terms_lock', true)=='1') echo "checked=checked"; ?> value="1"><span class="checkx"><i class="fas fa-check-double"></i></span><?php echo __( "Must Agree with Terms" , "download-manager" ); ?></label></h3>
            <div  id="terms" class="fwpdmlock panel-body" <?php if(get_post_meta($post->ID,'__wpdm_terms_lock', true)!='1') echo "style='display:none'"; ?> >
                <div class="form-group">
                    <label><?php echo __( "Terms Page:" , "download-manager" ); ?></label><br/>
                    <?php wp_dropdown_pages(['name' => 'file[terms_page]', 'class' => 'form-control d-block', 'id' => 'wpdm_terms_page', 'show_option_none' => __( 'Use custom content below', 'download-manager' ), 'selected' => get_post_meta($post->ID, '__wpdm_terms_page', true)]) ?>
                </div>
                <div class="form-group">
                <label for="pps_z"><?php echo __( "Terms Title:" , "download-manager" ); ?></label>
                <input type="text" class="form-control input-lg" name="file[terms_title]" value="<?php echo esc_html(stripslashes(get_post_meta($post->ID,'__wpdm_terms_title', true))); ?>" />
                </div>
                <div class="form-group">
                <label for="pps_z"><?php echo __( "Terms and Conditions:" , "download-manager" ); ?></label>
                    <?php
                    wp_editor(stripslashes(get_post_meta($post->ID,'__wpdm_terms_conditions', true)), "tc_z", ['textarea_name'  =>  'file[terms_conditions]', 'media_buttons' => false]);
                    ?>
                </div>
                <label for="pps_z"><?php echo __( "Terms Checkbox Label:" , "download-manager" ); ?></label>
                <input type="text" class="form-control input-lg" name="file[terms_check_label]" value="<?php echo esc_html(stripslashes(get_post_meta($post->ID,'__wpdm_terms_check_label', true))); ?>" />


            </div>
        </div>

        <!-- Password Lock -->
        <div class="panel panel-default">
        <h3 class="panel-heading"><label><input type="checkbox" class="wpdmlock" rel='password' name="file[password_lock]" <?php if(get_post_meta($post->ID,'__wpdm_password_lock', true)=='1') echo "checked=checked"; ?> value="1"><span class="checkx"><i class="fas fa-check-double"></i></span><?php echo __( "Enable Password Lock" , "download-manager" ); ?></label></h3>
        <div  id="password" class="fwpdmlock panel-body" <?php if(get_post_meta($post->ID,'__wpdm_password_lock', true)!='1') echo "style='display:none'"; ?> >
            <div class="form-group">

                <label><?php echo __( "Password:" , "download-manager" ); ?></label>
                <div class="input-group"><input class="form-control" type="text" name="file[password]" id="pps_z" value="<?php echo esc_attr(get_post_meta($post->ID,'__wpdm_password', true)); ?>" />
                    <span class="input-group-btn">
                        <button class="btn btn-secondary" onclick="return generatepass('pps_z');" type="button"><i class="fa fa-ellipsis-h"></i></button>
                      </span>
                </div>
                <div class="note"><?php echo __( "You can use single or multiple password for a package. If you are using multiple password then separate each password by []. example [password1][password2]", "download-manager" ) ?></div>
            </div>
            <div class="form-group">
                <label><?php echo __( "PW Usage Limit:" , "download-manager" ); ?></label>
                <div class="input-group">
                    <input size="10" class="form-control" type="number" name="file[password_usage_limit]" value="<?php echo (int)get_post_meta($post->ID,'__wpdm_password_usage_limit', true); ?>" />
                    <span class="input-group-addon">
                       <span class="input-group-text"> / <?php echo __( "password" , "download-manager" ); ?></span>
                    </span>
                    <span class="input-group-addon">
                       <label  class="input-group-text" style="color: var(--color-info);"><input type="checkbox" name="file[password_usage]" value="0" /> <?php echo __( "Reset Password Usage Count" , "download-manager" ); ?></label>
                    </span>
                </div>
                <div class="note"><?php echo __( "Password will expire after it exceed this usage limit" , "download-manager" ); ?></div>
            </div>

        </div>
        </div>

        <!-- Linkedin Lock -->
        <div class="panel panel-default">
            <h3 class="panel-heading"><label><input type="checkbox" rel="linkedin" class="wpdmlock" name="file[linkedin_lock]" <?php if(get_post_meta($post->ID,'__wpdm_linkedin_lock', true)=='1') echo "checked=checked"; ?> value="1"><span class="checkx"><i class="fas fa-check-double"></i></span><?php echo __( "LinkedIn Share Lock" , "download-manager" ); ?></label></h3>
        <div id="linkedin" class="frm fwpdmlock panel-body" <?php if(get_post_meta($post->ID,'__wpdm_linkedin_lock', true)!='1') echo "style='display:none'"; ?> >
            <table class="table table-stripped">
                <tr>
                    <td>
                        </br><textarea class="form-control" name="file[linkedin_message]"><?php echo get_post_meta($post->ID,'__wpdm_linkedin_message', true) ?></textarea>
                    </td>
                </tr>
                <tr><td>
                        <?php _e( "URL to share (keep empty for current page url):" , "download-manager" ); ?>
                        <br/><input class="form-control input-sm" type="text" name="file[linkedin_url]" value="<?php echo get_post_meta($post->ID,'__wpdm_linkedin_url', true) ?>" />
                    </td>
                </tr>
            </table>
        </div>
            </div>


        <!-- Tweet Lock -->
        <div class="panel panel-default">
            <h3 class="panel-heading"><label><input type="checkbox" rel="tweeter" class="wpdmlock" name="file[tweet_lock]" <?php if(get_post_meta($post->ID,'__wpdm_tweet_lock', true)=='1') echo "checked=checked"; ?> value="1"><span class="checkx"><i class="fas fa-check-double"></i></span><?php echo __( "Tweet Lock" , "download-manager" ); ?></label></h3>
        <div id="tweeter" class="frm fwpdmlock panel-body" <?php if(get_post_meta($post->ID,'__wpdm_tweet_lock', true)!='1') echo "style='display:none'"; ?> >
            <table width="100%"  cellpadding="0" cellspacing="0" >
                <tr>
                    <td><?php echo __( "Custom tweet message:" , "download-manager" ); ?>
                       <br/><textarea class="form-control" type="text" name="file[tweet_message]"><?php echo get_post_meta($post->ID,'__wpdm_tweet_message', true) ?></textarea></td>
                </tr>
            </table>
        </div>
        </div>

        <!-- Google+ Lock -->
        <div class="panel panel-default">
            <h3 class="panel-heading"><label><input type="checkbox" rel="gplusone" class="wpdmlock" name="file[gplusone_lock]" <?php if(get_post_meta($post->ID,'__wpdm_gplusone_lock', true)=='1') echo "checked=checked"; ?> value="1"><span class="checkx"><i class="fas fa-check-double"></i></span><?php echo __( "Enable Google Connect Lock" , "download-manager" ); ?></label></h3>
        <div id="gplusone" class="frm fwpdmlock panel-body" <?php if(get_post_meta($post->ID,'__wpdm_gplusone_lock', true)!='1') echo "style='display:none'"; ?> >

            <div class="list-group">
                <label class="list-group-item">
                    <input type="hidden" name="file[gc_scopes_contacts]" value="0">
                    <input type="checkbox" name="file[gc_scopes_contacts]" <?php checked(get_post_meta($post->ID,'__wpdm_gc_scopes_contacts', true), 1); ?> value="1"> <?php _e( "Request Access To User's Contact List" , "download-manager" ); ?>
                </label>
            </div>

        </div>
        </div>

        <!-- Twitter Follow Lock -->
        <div class="panel panel-default">
            <h3 class="panel-heading"><label><input type="checkbox" rel="gplusshare" class="wpdmlock" name="file[twitterfollow_lock]" <?php if(get_post_meta($post->ID,'__wpdm_twitterfollow_lock', true)=='1') echo "checked=checked"; ?> value="1"><span class="checkx"><i class="fas fa-check-double"></i></span><?php echo __( "Enable Twitter Follow Lock" , "download-manager" ); ?></label></h3>
            <div id="gplusshare" class="frm fwpdmlock panel-body" <?php if(get_post_meta($post->ID,'__wpdm_twitterfollow_lock', true)!='1') echo "style='display:none'"; ?> >
                <table width="100%"  cellpadding="0" cellspacing="0" >
                    <tr>
                        <td width="90px"><?php echo __( "Twiiter Handle:" , "download-manager" ); ?></td>
                        <td><input size="10" class="form-control input-sm" style="width: 200px;display: inline;" type="text" name="file[twitter_handle]" value="<?php echo get_post_meta($post->ID,'__wpdm_twitter_handle', true) ?>" /></td>
                    </tr>
                </table>
            </div>
        </div>


        <!-- Facebook Like Lock -->
        <div class="panel panel-default">
            <h3 class="panel-heading"><label><input type="checkbox" rel="facebooklike" class="wpdmlock" name="file[facebooklike_lock]" <?php if(get_post_meta($post->ID,'__wpdm_facebooklike_lock', true)=='1') echo "checked=checked"; ?> value="1"><span class="checkx"><i class="fas fa-check-double"></i></span><?php echo __( "Enable Facebook Like Lock" , "download-manager" ); ?></label></h3>
        <div id="facebooklike" class="frm fwpdmlock panel-body" <?php if(get_post_meta($post->ID,'__wpdm_facebooklike_lock', true)!=1) echo "style='display:none;'"; ?> >
            <table  width="100%" cellpadding="0" cellspacing="0">
                <?php if(get_option('_wpdm_facebook_app_id')=='') echo "<tr><td colspan=2>You have to add a Facebook appID <a target='_blank' href='edit.php?post_type=wpdmpro&page=settings&tab=social-connects'>here</a></td></tr>"; ?>
                <tr>
                    <td width="90px"><?php echo __( "URL to Like:" , "download-manager" ); ?></td>
                    <td><input size="10" style="width: 200px;display: inline;"  class="form-control input-sm" type="text" name="file[facebook_like]" value="<?php echo get_post_meta($post->ID,'__wpdm_facebook_like', true) ?>" /></td>
                </tr>
            </table>
        </div>
        </div>


        <!-- Email Lock -->
        <div class="panel panel-default">
            <h3 class="panel-heading"><label><input type="checkbox" rel="email" class="wpdmlock" name="file[email_lock]" <?php if(get_post_meta($post->ID,'__wpdm_email_lock', true)=='1') echo "checked=checked"; ?> value="1"><span class="checkx"><i class="fas fa-check-double"></i></span><?php echo __( "Enable Email Lock" , "download-manager" ); ?></label></h3>
        <div id="email" class="frm fwpdmlock panel-body"  <?php if(get_post_meta($post->ID,'__wpdm_email_lock', true)!='1') echo "style='display:none'"; ?> >
            <div class="form-group">
                <label><?php echo __( "Title" , "download-manager" ); ?></label>
                <input type="text" class="form-control" name="file[email_lock_title]" value="<?php echo ($elt = get_post_meta($post->ID,'__wpdm_email_lock_title', true)) !=''?$elt:'Subscribe To Download'; ?>">
            </div>
            <div class="form-group">
                <?php if(isset($post->ID)) do_action('wpdm_custom_form_field',$post->ID); ?>
            </div>
            <div class="form-group">
                <?php echo __( "Will ask for email (and checked custom data) before download" , "download-manager" ); ?>
            </div>
            <div class="form-group">
                <fieldset>
                    <legend><?php echo __( "Message after submit:" , "download-manager" ); ?></legend>
                    <textarea class="form-control" name="file[email_lock_msg]"><?php echo sanitize_textarea_field(get_post_meta($post->ID, '__wpdm_email_lock_msg', true)); ?></textarea>
                </fieldset>


            </div>

        </div>
        </div>


        <!-- Captcha Lock -->
        <div class="panel panel-default">
            <h3 class="panel-heading"><label><input type="checkbox" rel="captcha" class="wpdmlock" name="file[captcha_lock]" <?php if(get_post_meta($post->ID,'__wpdm_captcha_lock', true)=='1') echo "checked=checked"; ?> value="1"><span class="checkx"><i class="fas fa-check-double"></i></span><?php echo __( "Enable Captcha Lock" , "download-manager" ); ?></label></h3>
            <div id="captcha" class="frm fwpdmlock panel-body"  <?php if(get_post_meta($post->ID,'__wpdm_captcha_lock', true)!='1') echo "style='display:none'"; ?> >

                <a href="edit.php?post_type=wpdmpro&page=settings"><?php if(!get_option('_wpdm_recaptcha_site_key') || !get_option('_wpdm_recaptcha_secret_key')) _e( "Please configure reCAPTCHA" , "download-manager" ); ?></a>
                <?php _e( "Users will be asked for reCAPTCHA verification before download." , "download-manager" ); ?>

            </div>
        </div>



        <?php do_action('wpdm_download_lock_option',$post); ?>
    </div>
    <div class="clear"></div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="generatepass">
    <div class="modal-dialog" role="document" style="max-width: 400px">
        <div class="modal-content">
            <div class="modal-header" style="border-bottom: 0">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-key"></i> <?php echo __( "Generate Password", "download-manager" ) ?></h4>
            </div>
            <div class="modal-body-np">
                <div class="pfs panel panel-default card card-default" style="border-radius:0;box-shadow: none;border: 0 !important;">
                    <div class="panel-heading card-header" style="border-top: 1px solid #ddd !important;border-radius:0;"><b><?php _e( "Password Lenght & Count" , "download-manager" ); ?></b></div>
                    <div class="panel-body card-body">
                        <div class="row">
                            <div class="col-md-6">

                                    <b><?php _e( "Number of passwords:" , "download-manager" ); ?></b><Br/>
                                    <input class="form-control" type="number" id='pcnt' value="">

                            </div>
                            <div  class="col-md-6">

                                    <b><?php _e( "Password length:" , "download-manager" ); ?></b><Br/>
                                    <input  class="form-control" type="number" id='ncp' value="">

                            </div>
                        </div>
                    </div>
                    <div class="panel-heading card-header" style="border-radius:0;border-top: 1px solid #ddd"><b><?php _e( "Password Strength" , "download-manager" ); ?></b></div>
                    <div class="panel-body card-body">
                        <div class="row">
                            <div class="col-md-7">
                                <input style="padding:0;" type="range" min="1" max="4" value="2" class="slider" id="passtrn">
                                <div class="row">
                                    <div class="col-md-6" style="color: var(--color-danger);"><?php echo __( "Weak", "download-manager" ) ?></div>
                                    <div class="col-md-6 text-right" style="color: var(--color-success);"><?php echo __( "Strong", "download-manager" ) ?></div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <input type="button" id="gpsc" class="btn btn-secondary btn-lg btn-block" value="Generate" />
                            </div>
                        </div>
                    </div>
                    <div class="panel-heading card-header" style="border-radius:0;border-top: 1px solid #dddddd"><b><?php _e( "Generated Passwords" , "download-manager" ); ?></b></div>
                    <div class="panel-body card-body">
                        <textarea id="ps" class="form-control"></textarea>
                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <input type="button" id="pins" class="btn btn-primary btn-lg btn-block" value="<?php _e( "Insert Password(s)" , "download-manager" ); ?>" />
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
