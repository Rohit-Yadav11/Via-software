

                 <div class="panel panel-default">
                     <div class="panel-heading"><?php echo __( "Front-end Settings" , "download-manager" ); ?></div>
                     <div class="panel-body">


                         <div class="form-group">
                             <label for="__wpdm_login_url"><?php echo __( "Login Page" , "download-manager" ); ?></label><br/>
                             <?php wp_dropdown_pages(array('name' => '__wpdm_login_url', 'id' => '__wpdm_login_url', 'show_option_none' => __( "None Selected" , "download-manager" ), 'option_none_value' => '' , 'selected' => get_option('__wpdm_login_url'))) ?>
                             <label style="margin-top: 2px"><input type="hidden" name="__wpdm_clean_login" value="0"><input <?php checked(1, get_option('__wpdm_clean_login', 0)); ?> style="margin: 0 3px 0 5px" value="1" name="__wpdm_clean_login" type="checkbox" /> <?php _e("Clean login page", "download-manager");  ?></label><br/>
                             <em class="note"><?php printf(__( "The page where you used short-code %s" , "download-manager" ),'<input style="width: 145px" readonly="readonly" type="text" value="[wpdm_login_form]" class="txtsc">'); ?></em><br/>
                             <label style="margin-top: 2px"><input type="hidden" name="__wpdm_modal_login" value="0"><input <?php checked(1, get_option('__wpdm_modal_login', 0)); ?> style="margin: 0 3px 0 5px" value="1" name="__wpdm_modal_login" type="checkbox" /> <?php _e("Enable modal login form", "download-manager");  ?></label>
                             <hr/>
                         </div>

                         <!-- div class="form-group">
                             <input type="hidden" name="__wpdm_login_modal" value="0" />
                             <label><input type="checkbox" name="__wpdm_login_modal" value="1" /> <?php echo __( "Enable Modal Login" , "download-manager" ); ?></label>
                         </div -->

                         <div class="form-group">
                             <label for="__wpdm_register_url"><?php echo __( "Register Page" , "download-manager" ); ?></label><br/>
                             <?php wp_dropdown_pages(array('name' => '__wpdm_register_url', 'id' => '__wpdm_register_url', 'show_option_none' => __( "None Selected" , "download-manager" ), 'option_none_value' => '' , 'selected' => get_option('__wpdm_register_url'))) ?>
                             <label style="margin-top: 2px"><input type="hidden" name="__wpdm_clean_signup" value="0"><input <?php checked(1, get_option('__wpdm_clean_signup', 0)); ?> style="margin: 0 3px 0 5px" value="1" name="__wpdm_clean_signup" type="checkbox" /> <?php _e("Clean signup page", "download-manager");  ?></label><br/>
                             <em class="note"><?php printf(__( "The page where you used the short-code %s" , "download-manager" ),'<input style="width: 135px" readonly="readonly" type="text" value="[wpdm_reg_form]" class="txtsc">'); ?></em>
                             <label style="margin-top: 2px;display: block"><input type="hidden" name="__wpdm_signup_email_verify" value="0"><input <?php checked(1, get_option('__wpdm_signup_email_verify', 0)); ?> style="margin: 0 3px 0 5px" value="1" name="__wpdm_signup_email_verify" type="checkbox" /> <?php _e("Enable email verification on signup", "download-manager");  ?></label>
                             <label style="margin-top: 2px;display: block"><input type="hidden" name="__wpdm_signup_autologin" value="0"><input <?php checked(1, get_option('__wpdm_signup_autologin', 0)); ?> style="margin: 0 3px 0 5px" value="1" name="__wpdm_signup_autologin" type="checkbox" /> <?php _e("Login automatically after signup is completed", "download-manager");  ?></label>
                         </div>
                         <hr/>
                        <div class="form-group">
                             <label for="__wpdm_author_profile"><?php echo __( "Public Profile Page" , "download-manager" ); ?></label><br/>
                             <?php wp_dropdown_pages(array('name' => '__wpdm_author_profile', 'id' => '__wpdm_author_profile', 'show_option_none' => __( "None Selected" , "download-manager" ), 'option_none_value' => '' , 'selected' => get_option('__wpdm_author_profile'))) ?><br/>
                             <em class="note"><?php printf(__( "The page where you used the short-code %s" , "download-manager" ),'<input style="width: 155px;" readonly="readonly" type="text" value="[wpdm_user_profile]" class="txtsc">'); ?></em>
                         </div>

                         <div class="form-group">
                             <label for="__wpdm_front_end_access_blocked"><?php echo __( "Message For Blocked Users:" , "download-manager" ); ?></label>
                             <textarea id="__wpdm_front_end_access_blocked" name="__wpdm_front_end_access_blocked" class="form-control"><?php echo stripslashes(get_option('__wpdm_front_end_access_blocked'));?></textarea>
                         </div>


                         <div class="form-group">
                             <label><?php echo __( "When Someone Create a Package:" , "download-manager" ); ?></label><br/>
                             <select name="__wpdm_ips_frontend">
                                 <option value="publish"><?php echo __('Publish Instantly'); ?></option>
                                 <option value="pending" <?php selected(get_option('__wpdm_ips_frontend'), 'pending'); ?>><?php echo __( "Pending for Review" , "download-manager" ); ?></option>
                             </select>
                         </div>

                         <div class="form-group">
                             <label><?php echo __( "When File Already Exists:" , "download-manager" ); ?></label><br/>
                             <select name="__wpdm_overwrite_file_frontend">
                                 <option value="0"><?php echo __('Rename New File'); ?></option>
                                 <option value="1" <?php echo get_option('__wpdm_overwrite_file_frontend',0)==1?'selected=selected':''; ?>><?php echo __( "Overwrite" , "download-manager" ); ?></option>
                             </select>
                         </div>

                         <div class="form-group">
                         <label><?php echo __( "Allowed File Types From Front-end:" , "download-manager" ); ?></label>
                         <input type="text" class="form-control" placeholder="txt,png,jpeg,gif,jpg,psd" value="<?php echo get_option('__wpdm_allowed_file_types','*'); ?>" name="__wpdm_allowed_file_types" />
                         </div>

                         <div class="form-group">
                         <label><?php echo __( "Max Upload Size From Front-end" , "download-manager" ); ?></label>
                         <input type="text" class="form-control" style="width: 100px;display: inline" title="0 for system default" name="__wpdm_max_upload_size" value="<?php echo get_option('__wpdm_max_upload_size',(wp_max_upload_size()/1048576)); ?>"> MB<br/>
                         </div>

                         <div class="form-group"><hr/>
                             <input type="hidden" value="0" name="__wpdm_disable_new_package_email" />
                             <label><input style="margin: 0 10px 0 0" type="checkbox" name="__wpdm_disable_new_package_email" value="1" <?php checked(1, get_option('__wpdm_disable_new_package_email')); ?>><?php echo __( "Disable New Package Notification Email" , "download-manager" ); ?></label><br/>
                             <em><?php echo __( "Check if you do not want to receive email notification when someone creates new package from frontend" , "download-manager" ); ?></em>
                         </div>


                         <div class="form-group"><hr/>
                             <input type="hidden" value="0" name="__wpdm_file_list_paging" />
                             <label><input style="margin: 0 10px 0 0" type="checkbox" <?php checked(get_option('__wpdm_file_list_paging',0),1); ?> value="1" name="__wpdm_file_list_paging"><?php _e( "Enable Search in File List" , "download-manager" ); ?></label><br/>
                             <em><?php _e( "Check this option if you want to enable pagination & search in file list where there are more than 30 files attached with a package" , "download-manager" ); ?></em>
                             <br/>

                         </div>

                         <!-- div class="form-group"><hr/>
                             <input type="hidden" value="0" name="__wpdm_ajax_popup" />
                             <label><input style="margin: 0 10px 0 0" type="checkbox" <?php checked(get_option('__wpdm_ajax_popup',0),1); ?> value="1" name="__wpdm_ajax_popup"><?php _e( "Load Lock Options Using Ajax" , "download-manager" ); ?></label><br/>
                             <em><?php _e( "Check this option if you want to load lock options in popover using ajax" , "download-manager" ); ?></em>
                             <br/>

                         </div -->

                         <div class="form-group"><hr/>
                             <input type="hidden" value="0" name="__wpdm_rss_feed_main" />
                             <label><input style="margin: 0 10px 0 0" type="checkbox" <?php checked(get_option('__wpdm_rss_feed_main'),1); ?> value="1" name="__wpdm_rss_feed_main"><?php _e( "Include Packages in Main RSS Feed" , "download-manager" ); ?></label><br/>
                             <em><?php printf(__( "Check this option if you want to show wpdm packages in your main <a target=\"_blank\" href=\"%s\">RSS Feed</a>" , "download-manager" ), get_bloginfo('rss_url')); ?></em>
                             <br/>

                         </div>

                         <?php do_action("wpdm_settings_frontend_general"); ?>


                     </div>
                 </div>

                 <div class="panel panel-default">
                     <div class="panel-heading"><?php echo __( "Archive Page Options" , "download-manager" ); ?></div>
                     <div class="panel-body">
                         <div class="panel panel-default panel-light" id="cpi">
                             <label  class="panel-heading" style="display: block;border-bottom: 1px solid #e5e5e5"><input type="radio" name="__wpdm_cpage_style"  <?php checked(get_option('__wpdm_cpage_style'),'basic'); ?> value="basic"> <?php echo __( "Do Not Apply", "download-manager" ) ?></label>
                             <div class="panel-body">
                                <?php echo __( "Keep current template as it is provided with the active theme", "download-manager" ); ?>
                             </div>
                             <label  class="panel-heading" style="display: block;border-bottom: 1px solid #ddd;border-top: 1px solid #ddd;border-radius: 0;"><input type="radio" name="__wpdm_cpage_style" <?php checked(get_option('__wpdm_cpage_style'),'ltpl'); ?> value="ltpl"> <?php echo __( "Use Link Template", "download-manager" ) ?></label>
                             <div class="panel-body">
                                 <?php
                                 $cpage = maybe_unserialize(get_option('__wpdm_cpage'));
                                 $cpage = !is_array($cpage) ? [ 'template' => 'link-template-default', 'cols' => 2, 'colsphone' => 1, 'colspad' => 1, 'heading' => 1 ] : $cpage;
                                 ?>

                                 <div class="form-group">
                                     <div class="row">
                                         <div class="col-md-4">
                                             <label><?php echo __( "Link Template:" , "download-manager" ); ?></label><br/>
                                             <?php
                                             echo WPDM()->packageTemplate->dropdown(array('name' => '__wpdm_cpage[template]', 'selected' => $cpage['template'] ));
                                             ?>
                                         </div>
                                         <div class="col-md-4">
                                             <label><?php echo __( "Items Per Page:" , "download-manager" ); ?></label><br/>
                                             <input type="number" class="form-control" name="__wpdm_cpage[items_per_page]" value="<?php echo isset($cpage['items_per_page']) ? $cpage['items_per_page'] : 12; ?>">
                                         </div>
                                         <div class="col-md-4">
                                             <label><?php echo __( "Toolbar:" , "download-manager" ); ?></label>
                                             <div class="input-group" style="display: flex">
                                                 <label class="form-control" style="margin: 0;"><input type="radio" name="__wpdm_cpage[heading]" value="1" <?php checked($cpage['heading'], 1); ?>> <?php echo __( "Show", "download-manager" ) ?></label>
                                                 <label class="form-control" style="margin: 0 0 0 -1px;"><input type="radio" name="__wpdm_cpage[heading]" value="0" <?php checked($cpage['heading'], 0); ?>> <?php echo __( "Hide", "download-manager" ) ?></label>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                                 <div class="form-group">
                                     <label><?php echo __( "Number of Columns", "download-manager" ) ?>:</label>
                                     <div class="row">
                                         <div class="col-md-4">
                                             <div class="input-group">
                                                 <select class="form-control system-ui wpdm-custom-select" name="__wpdm_cpage[cols]">
                                                     <option value="1">1 Col</option>
                                                     <option value="2" <?php selected(2, $cpage['cols']) ?> >2 Cols</option>
                                                     <option value="3" <?php selected(3, $cpage['cols']) ?> >3 Cols</option>
                                                     <option value="4" <?php selected(4, $cpage['cols']) ?> >4 Cols</option>
                                                     <option value="6" <?php selected(6, $cpage['cols']) ?>>6 Cols</option>
                                                     <option value="12" <?php selected(12, $cpage['cols']) ?>>12 Cols</option>
                                                 </select>
                                                 <div class="input-group-addon">
                                                     <i class="fa fa-laptop"></i>
                                                 </div>
                                             </div>
                                         </div>
                                         <div class="col-md-4">
                                             <div class="input-group">
                                                 <select class="form-control system-ui wpdm-custom-select" name="__wpdm_cpage[colspad]">
                                                     <option value="1">1 Col</option>
                                                     <option value="2" <?php selected(2, $cpage['colspad']) ?> >2 Cols</option>
                                                     <option value="3" <?php selected(3, $cpage['colspad']) ?> >3 Cols</option>
                                                     <option value="4" <?php selected(4, $cpage['colspad']) ?> >4 Cols</option>
                                                     <option value="6" <?php selected(6, $cpage['colspad']) ?>>6 Cols</option>
                                                     <option value="12" <?php selected(12, $cpage['colspad']) ?>>12 Cols</option>
                                                 </select>
                                                 <div class="input-group-addon">
                                                     <i class="fa fa-tablet-alt"></i>
                                                 </div>
                                             </div>
                                         </div>
                                         <div class="col-md-4">
                                             <div class="input-group">
                                                 <select class="form-control system-ui wpdm-custom-select" name="__wpdm_cpage[colsphone]">
                                                     <option value="1">1 Col</option>
                                                     <option value="2" <?php selected(2, $cpage['colsphone']) ?> >2 Cols</option>
                                                     <option value="3" <?php selected(3, $cpage['colsphone']) ?> >3 Cols</option>
                                                     <option value="4" <?php selected(4, $cpage['colsphone']) ?> >4 Cols</option>
                                                     <option value="6" <?php selected(6, $cpage['colsphone']) ?>>6 Cols</option>
                                                     <option value="12" <?php selected(12, $cpage['colsphone']) ?>>12 Cols</option>
                                                 </select>
                                                 <div class="input-group-addon">
                                                     <i class="fa fa-mobile"></i>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>

                     </div>





                 </div>

                 <?php

                 include dirname(__FILE__).'/profile-dashboard.php';

                 include dirname(__FILE__).'/author-dashboard.php';
                 ?>


                 <div class="panel panel-default">
                     <div class="panel-heading"><?php echo __( "Allowed Sign up Roles" , "download-manager" ); ?></div>
                     <div class="panel-body-table">
                         <table class="table table-striped">
                             <thead>
                             <tr>
                                 <td colspan="3">
                                     <div class="note"><?php _e( "You can add role id with signup form shortcode as parameter or select role when using WPDM Gutenberg block for signup form and users signed up using that form will be assigned to that selected role. Here you can select the allowed role to use with the signup form", "download-manager" ) ?></div>
                                 </td>
                             </tr>
                             <tr>
                                 <th style="width: 20px"></th>
                                 <th>Role Name</th>
                                 <th align="right">Role ID</th>
                             </tr>
                             </thead>
                             <tbody>
                             <?php
                             global $wp_roles;
                             $roles = array_reverse($wp_roles->role_names);
                             $signupRoles = get_option('__wpdm_signup_roles');
                             if(!is_array($signupRoles)) $signupRoles = array();
                             foreach( $roles as $role => $name ) {
                                 if($role !== 'administrator') {
                                     ?>

                                     <tr>
                                         <td>
                                             <input id="__<?php echo $role; ?>" <?php checked(in_array($role, $signupRoles), 1) ?>
                                                    type="checkbox" name="__wpdm_signup_roles[]"
                                                    value="<?php echo $role; ?>"></td>
                                         <td width="250px"><label
                                                     for="__<?php echo $role; ?>"><?php echo $name; ?></label></td>
                                         <td align="right"><input style="font-family: monospace;background: #ffffff"
                                                                  type="text" class="form-control input-sm"
                                                                  readonly="readonly" value="<?php echo $role; ?>"></td>
                                     </tr>
                                     <?php
                                 }
                                 } ?>
                             </tbody>
                         </table>
                     </div>
                 </div>




                 <?php do_action("wpdm_settings_frontend"); ?>


<style> legend{ font-weight: 800; } fieldset#cpi legend input { margin: 0 !important; }</style>
