<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<div class="wpdmpp-settings-fields">
    <input type="hidden" name="action" value="wpdmpp_save_settings">
    <?php
    global $wpdb;
    $countries = $wpdb->get_results("select * from {$wpdb->prefix}ahm_country order by country_name");
    ?>
    <div class="panel panel-default">
        <div class="panel-heading"><?php _e('Base Country', 'wpdm-premium-packages'); ?></div>
        <div class="panel-body">
            <select class="chosen" name="_wpdmpp_settings[base_country]">
                <option><?php _e('--Select Country--', 'wpdm-premium-packages'); ?></option>
                <?php
                foreach ($countries as $country) {
                    $country->country_name = strtolower($country->country_name);
                    ?>
                    <option value="<?php echo $country->country_code; ?>" <?php selected(isset($settings['base_country']) ? $settings['base_country'] : '', $country->country_code ); ?> >
                        <?php echo ucwords($country->country_name); ?>
                    </option>
                    <?php
                }
                ?>
            </select>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading"><?php _e("Allowed Countries", "wpdm-premium-packages"); ?></div>
        <div class="panel-body">
            <ul id="listbox" style="height: 200px;overflow: auto;">
                <li>
                    <label for="allowed_cn"><input type="checkbox" name="allowed_cn_all" id="allowed_cn"/> <?php _e('Select All/None','wpdm-premium-packages'); ?> </label>
                </li>
                <?php
                foreach ($countries as $country) {
                    $country->country_name = strtolower($country->country_name);
                    ?>
                    <li>
                        <label><input <?php
                            $select = '';
                            if (isset($settings['allow_country'])) {
                                foreach ($settings['allow_country'] as $ac) {
                                    if ($ac == $country->country_code) {
                                        $select = 'checked="checked"';
                                        break;
                                    } else
                                        $select = '';
                                }
                            }
                            echo $select;
                            ?> type="checkbox" class="ccb" name="_wpdmpp_settings[allow_country][]"
                               value="<?php echo $country->country_code; ?>"><?php echo " " . ucwords($country->country_name); ?>
                        </label>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading"><?php _e("Frontend Settings", "wpdm-premium-packages"); ?></div>
        <div class="panel-body">
            <label>
                <input type="checkbox" name="_wpdmpp_settings[billing_address]" <?php if (isset($settings['billing_address']) && $settings['billing_address'] == 1) echo 'checked=checked' ?>
                       value="1"> <?php _e("Ask for billing address on checkout page", "wpdm-premium-packages"); ?>
            </label><br/>
            <label>
                <input type="hidden" name="_wpdmpp_settings[authorize_masterkey]" value="0"/>
                <input type="checkbox" name="_wpdmpp_settings[authorize_masterkey]" <?php if (isset($settings['authorize_masterkey']) && $settings['authorize_masterkey'] == 1) echo 'checked=checked' ?>
                       value="1"> <?php _e("Authorize MasterKey to download premium packages", "wpdm-premium-packages"); ?>
            </label><br/>
            <label>
                <input type="checkbox" name="_wpdmpp_settings[guest_checkout]" <?php if (isset($settings['guest_checkout']) && $settings['guest_checkout'] == 1) echo 'checked=checked' ?>
                          value="1"> <?php _e("Enable guest checkout", "wpdm-premium-packages"); ?>
            </label><br/>
            <input type="hidden" name="_wpdmpp_settings[guest_download]" value="0">
            <label>
                <input type="checkbox" name="_wpdmpp_settings[guest_download]" <?php if (isset($settings['guest_download']) && $settings['guest_download'] == 1) echo 'checked=checked' ?>
                          value="1"> <?php _e("Enable guest download", "wpdm-premium-packages"); ?>
            </label><br/>
            <label><input type="hidden" name="_wpdmpp_settings[disable_multi_file_download]" value="0">
                <input type="checkbox" name="_wpdmpp_settings[disable_multi_file_download]" <?php if (isset($settings['guest_download']) && $settings['disable_multi_file_download'] == 1) echo 'checked=checked' ?>
                       value="1"> <?php _e("Disable multi-file download for purchased items", "wpdm-premium-packages"); ?>
            </label>

            <hr/>

            <label><?php _e("Cart Page :", "wpdm-premium-packages"); ?></label><br>
            <?php
            if ($settings['page_id'])
                $args = array(
                    'show_option_none' => __('None Selected','wpdm-premium-packages'),
                    'name' => '_wpdmpp_settings[page_id]',
                    'selected' => $settings['page_id']
                );
            else
                $args = array(
                    'show_option_none' => __('None Selected','wpdm-premium-packages'),
                    'name' => '_wpdmpp_settings[page_id]'
                );
            wp_dropdown_pages($args);
            ?>
            <hr/>

            <label><?php _e("Orders Page :", "wpdm-premium-packages"); ?></label><br>
            <?php
            if (isset($settings['orders_page_id']))
                $args = array(
                    'name' => '_wpdmpp_settings[orders_page_id]',
                    'show_option_none' => __('None Selected','wpdm-premium-packages'),
                    'selected' => $settings['orders_page_id']
                );
            else
                $args = array(
                    'show_option_none' => __('None Selected','wpdm-premium-packages'),
                    'name' => '_wpdmpp_settings[orders_page_id]'
                );
            wp_dropdown_pages($args);
            ?>
            <hr/>

            <label><?php _e("Guest Order Page :", "wpdm-premium-packages"); ?></label><br>
            <?php
            if (isset($settings['guest_order_page_id']))
                $args = array(
                    'name' => '_wpdmpp_settings[guest_order_page_id]',
                    'show_option_none' => __('None Selected','wpdm-premium-packages'),
                    'selected' => $settings['guest_order_page_id']
                );
            else
                $args = array(
                    'show_option_none' => __('None Selected','wpdm-premium-packages'),
                    'name' => '_wpdmpp_settings[guest_order_page_id]'
                );
            wp_dropdown_pages($args);
            ?>
            <hr/>

            <label><?php _e("Continue Shopping URL:", "wpdm-premium-packages"); ?></label><br/>
            <input type="text" class="form-control" name="_wpdmpp_settings[continue_shopping_url]" size="50" id="continue_shopping_url" value="<?php echo $settings['continue_shopping_url'] ?>"/>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading"><?php _e("Purchase Settings", "wpdm-premium-packages"); ?></div>
        <div class="panel-body">
            <label>
                <input  name="_wpdmpp_settings[no_role_discount]" type="hidden" value="0">
                <input type="checkbox" name="_wpdmpp_settings[no_role_discount]" <?php if (isset($settings['no_role_discount']) && $settings['no_role_discount'] == 1) echo 'checked=checked' ?> value="1">
                <?php echo __("Disable user role based discount", "wpdm-premium-packages"); ?>
            </label><br/>
            <label>
                <input  name="_wpdmpp_settings[no_product_coupon]" type="hidden" value="0">
                <input type="checkbox" name="_wpdmpp_settings[no_product_coupon]" <?php if (isset($settings['no_product_coupon']) && $settings['no_product_coupon'] == 1) echo 'checked=checked' ?> value="1">
                <?php echo __("Disable product specific coupons", "wpdm-premium-packages"); ?>
            </label><br/>
            <label>
                <input  name="_wpdmpp_settings[show_buynow]" type="hidden" value="0">
                <input type="checkbox" name="_wpdmpp_settings[show_buynow]" <?php if (isset($settings['show_buynow']) && $settings['show_buynow'] == 1) echo 'checked=checked' ?>
                       value="1"> <?php echo __("Show <strong>Buy Now</strong> option", "wpdm-premium-packages"); ?>
            </label><br/>
            <label>
                <input type="checkbox" name="_wpdmpp_settings[wpdmpp_after_addtocart_redirect]" id="wpdmpp_after_addtocart_redirect"
                       value="1" <?php if ( isset($settings['wpdmpp_after_addtocart_redirect']) &&  $settings['wpdmpp_after_addtocart_redirect'] == 1 ) echo "checked='checked'"; ?>>
                <?php _e("Redirect to shopping cart after a product is added to the cart", "wpdm-premium-packages"); ?>
            </label><br/>
            <label>
                <input  name="_wpdmpp_settings[cdl_fallback]" type="hidden" value="0">
                <input type="checkbox" name="_wpdmpp_settings[cdl_fallback]" <?php if (isset($settings['cdl_fallback']) && $settings['cdl_fallback'] == 1) echo 'checked=checked' ?>
                          value="1"> <?php echo __("Show 'Add To Cart' button as customer download link fallback", "wpdm-premium-packages"); ?>
            </label><br/>
            <label>
                <input  name="_wpdmpp_settings[license_key_validity]" type="hidden" value="0">
                <input type="checkbox" name="_wpdmpp_settings[license_key_validity]" <?php if (isset($settings['license_key_validity']) && $settings['license_key_validity'] == 1) echo 'checked=checked' ?>
                          value="1"> <?php echo __("Keep license key valid for expired orders", "wpdm-premium-packages"); ?>
            </label><br/>
            <label>
                <input  name="_wpdmpp_settings[order_expiry_alert]" type="hidden" value="0">
                <input type="checkbox" name="_wpdmpp_settings[order_expiry_alert]" <?php if (isset($settings['order_expiry_alert']) && $settings['order_expiry_alert'] == 1) echo 'checked=checked' ?>
                          value="1"> <?php echo __("Send order expiration alert to customer", "wpdm-premium-packages"); ?>
            </label>
            <br/>
            <label>
                <input  name="_wpdmpp_settings[auto_renew]" type="hidden" value="0">
                <input type="checkbox" name="_wpdmpp_settings[auto_renew]" <?php if (isset($settings['auto_renew']) && $settings['auto_renew'] == 1) echo 'checked=checked' ?>
                       value="1"> <?php echo __("Auto renew order on expiration", "wpdm-premium-packages"); ?>
            </label><br/>
            <label>
                <input  name="_wpdmpp_settings[disable_order_notes]" type="hidden" value="0">
                <input type="checkbox" name="_wpdmpp_settings[disable_order_notes]" <?php if (isset($settings['disable_order_notes']) && $settings['disable_order_notes'] == 1) echo 'checked=checked' ?>
                       value="1"> <?php echo __("Disable order notes", "wpdm-premium-packages"); ?>
            </label>
            <br/><br/>

            <div class="form-group">
                <label><?php _e("Order Validity Period:", "wpdm-premium-packages"); ?></label><br>
                <div class="input-group">
                    <input type="text" class="form-control" value="<?php echo (isset($settings['order_validity_period'])) ? $settings['order_validity_period'] : 365; ?>"
                           name="_wpdmpp_settings[order_validity_period]"/>
                    <span class="input-group-addon"><?php _e('Days','wpdm-premium-packages'); ?></span>
                </div>

            </div>
            <div class="form-group">
                <label><?php _e("Order Title:", "wpdm-premium-packages"); ?></label><br>

                    <input type="text" class="form-control" value="<?php echo (isset($settings['order_title']) && $settings['order_title'] != '') ? $settings['order_title'] : get_option('blogname'). '{{PRODUCT_NAME}} Order# {{ORDER_ID}}'; ?>"
                           name="_wpdmpp_settings[order_title]"/>
                    <em class="note"><?php echo sprintf(__('%s = Product Name, %s = Order ID','wpdm-premium-packages'), '{{PRODUCT_NAME}}','{{ORDER_ID}}'); ?></em>
            </div>
            <div class="form-group">
                <label><?php _e("Order ID Prefix:", "wpdm-premium-packages"); ?></label><br>

                <input type="text" class="form-control" value="<?php echo (isset($settings['order_id_prefix']) && $settings['order_id_prefix'] != '') ? $settings['order_id_prefix'] : 'wpdmpp'; ?>"
                       name="_wpdmpp_settings[order_id_prefix]"/>

            </div>



        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading"><?php _e("License Settings", "wpdm-premium-packages"); ?></div>
        <div class="panel-body-ex">
            <table class="table table-striped" style="margin-bottom: 0">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>License Name</th>
                    <th>License Description</th>
                    <th style="width: 90px"><abbr class="ttip" title="Usage Limit">Limit</abbr></th>
                    <th><i class="fa fa-cogs"></i></th>
                </tr>
                </thead>
                <tbody id="licenses">
            <?php
            $pre_licenses = wpdmpp_get_licenses();
            $pre_licenses = maybe_unserialize($pre_licenses);
            foreach ($pre_licenses as $licid => $lic){ ?>

                <tr id="tr_<?php echo $licid; ?>">
                    <td><input type="text" class="form-control" disabled="disabled" value="<?php echo $licid; ?>"></td>
                    <td><input type="text" class="form-control" name="_wpdmpp_settings[licenses][<?php echo $licid; ?>][name]" value="<?php echo esc_attr($lic['name']); ?>"></td>
                    <td><textarea class="form-control" name="_wpdmpp_settings[licenses][<?php echo $licid; ?>][description]"><?php echo isset($lic['description'])?esc_attr($lic['description']):''; ?></textarea></td>
                    <td><input type="number" class="form-control" name="_wpdmpp_settings[licenses][<?php echo $licid; ?>][use]" value="<?php echo esc_attr($lic['use']); ?>"></td>
                    <td><button type="button" data-rowid="#tr_<?php echo $licid; ?>" class="btn btn-danger del-lic"><i class="fas fa-trash-alt"></i></button></td>
                </tr>


            <?php } ?>
                </tbody>

                </table>

        </div>
        <div class="panel-footer text-right">
            <button type="button" id="addlicenses" class="btn btn-secondary btn-sm"><i class="fa fa-plus-circle"></i> <?php _e('Add New License', 'wpdm-premium-packages'); ?></button>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading"><?php _e("Add to Cart & Checkout Buttons", "wpdm-premium-packages"); ?></div>
        <div class="panel-body">
            <div class="form-group">
                <label for="invoice-logo"><?php _e('Add to Cart Button Label','wpdm-premium-packages'); ?></label>
                <input type="text" name="_wpdmpp_settings[a2cbtn_label]" class="form-control" value="<?php echo isset($settings['a2cbtn_label']) ? esc_attr($settings['a2cbtn_label']) : 'Add To Cart'; ?>"/>
            </div>
            <div class="form-group">
                <label><?php _e('Add To Cart Button Style', 'wpdm-premium-packages'); ?>:</label><br/>
                <div class="btn-group btn-group-sm">
                    <label class="btn btn-default <?php echo (isset($settings['a2cbtn_color']) && $settings['a2cbtn_color'] === 'default')?'active':''; ?>"><input <?php checked('btn-default', (isset($settings['a2cbtn_color'])?$settings['a2cbtn_color']:'')); ?> type="radio" value="btn-link" name="_wpdmpp_settings[a2cbtn_color]"> Default</label>
                    <label class="btn btn-primary <?php echo (isset($settings['a2cbtn_color']) && $settings['a2cbtn_color'] === 'primary')?'active':''; ?>"><input <?php checked('btn-primary', (isset($settings['a2cbtn_color'])?$settings['a2cbtn_color']:'')); ?> type="radio" value="btn-primary" name="_wpdmpp_settings[a2cbtn_color]"> Primary</label>
                    <label class="btn btn-secondary <?php echo (isset($settings['a2cbtn_color']) && $settings['a2cbtn_color'] === 'secondary')?'active':''; ?>"><input <?php checked('btn-secondary', (isset($settings['a2cbtn_color'])?$settings['a2cbtn_color']:'')); ?> type="radio" value="btn-secondary" name="_wpdmpp_settings[a2cbtn_color]"> Secondary</label>
                    <label class="btn btn-info <?php echo (isset($settings['a2cbtn_color']) && $settings['a2cbtn_color'] === 'info')?'active':''; ?>"><input <?php checked('btn-info', (isset($settings['a2cbtn_color'])?$settings['a2cbtn_color']:'')); ?> type="radio" value="btn-info" name="_wpdmpp_settings[a2cbtn_color]"> Info</label>
                    <label class="btn btn-success <?php echo (isset($settings['a2cbtn_color']) && $settings['a2cbtn_color'] === 'success')?'active':''; ?>"><input <?php checked('btn-success', (isset($settings['a2cbtn_color'])?$settings['a2cbtn_color']:'')); ?> type="radio" value="btn-success" name="_wpdmpp_settings[a2cbtn_color]"> Success</label>
                    <label class="btn btn-warning <?php echo (isset($settings['a2cbtn_color']) && $settings['a2cbtn_color'] === 'warning')?'active':''; ?>"><input <?php checked('btn-warning', (isset($settings['a2cbtn_color'])?$settings['a2cbtn_color']:'')); ?> type="radio" value="btn-warning" name="_wpdmpp_settings[a2cbtn_color]"> Warning</label>
                    <label class="btn btn-danger <?php echo (isset($settings['a2cbtn_color']) && $settings['a2cbtn_color'] === 'danger')?'active':''; ?>"><input <?php checked('btn-danger', (isset($settings['a2cbtn_color'])?$settings['a2cbtn_color']:'')); ?> type="radio" value="btn-danger" name="_wpdmpp_settings[a2cbtn_color]"> Danger</label>
                </div><br/>
                <em class="note"><?php _e('You can change colors from User Interface settings page'); ?></em>

            </div>

            <div class="form-group">
                <label for="invoice-logo"><?php _e('Checkout Button Label','wpdm-premium-packages'); ?></label>
                <input type="text" name="_wpdmpp_settings[cobtn_label]" class="form-control" value="<?php echo isset($settings['cobtn_label']) ? esc_attr($settings['cobtn_label']) : htmlspecialchars('<i class="money-check-alt mr-2"></i>Complete Payment'); ?>"/>
            </div>
            <div class="form-group">
                <label><?php _e('Checkout Button Style', 'wpdm-premium-packages'); ?>:</label><br/>
                <div class="btn-group btn-group-sm">
                    <label class="btn btn-default <?php echo (isset($settings['cobtn_color']) && $settings['cobtn_color'] === 'default')?'active':''; ?>"><input <?php checked('btn-default', (isset($settings['cobtn_color'])?$settings['cobtn_color']:'')); ?> type="radio" value="btn-link" name="_wpdmpp_settings[cobtn_color]"> Default</label>
                    <label class="btn btn-primary <?php echo (isset($settings['cobtn_color']) && $settings['cobtn_color'] === 'primary')?'active':''; ?>"><input <?php checked('btn-primary', (isset($settings['cobtn_color'])?$settings['cobtn_color']:'')); ?> type="radio" value="btn-primary" name="_wpdmpp_settings[cobtn_color]"> Primary</label>
                    <label class="btn btn-secondary <?php echo (isset($settings['cobtn_color']) && $settings['cobtn_color'] === 'secondary')?'active':''; ?>"><input <?php checked('btn-secondary', (isset($settings['cobtn_color'])?$settings['cobtn_color']:'')); ?> type="radio" value="btn-secondary" name="_wpdmpp_settings[cobtn_color]"> Secondary</label>
                    <label class="btn btn-info <?php echo (isset($settings['cobtn_color']) && $settings['cobtn_color'] === 'info')?'active':''; ?>"><input <?php checked('btn-info', (isset($settings['cobtn_color'])?$settings['cobtn_color']:'')); ?> type="radio" value="btn-info" name="_wpdmpp_settings[cobtn_color]"> Info</label>
                    <label class="btn btn-success <?php echo (isset($settings['cobtn_color']) && $settings['cobtn_color'] === 'success')?'active':''; ?>"><input <?php checked('btn-success', (isset($settings['cobtn_color'])?$settings['cobtn_color']:'')); ?> type="radio" value="btn-success" name="_wpdmpp_settings[cobtn_color]"> Success</label>
                    <label class="btn btn-warning <?php echo (isset($settings['cobtn_color']) && $settings['cobtn_color'] === 'warning')?'active':''; ?>"><input <?php checked('btn-warning', (isset($settings['cobtn_color'])?$settings['cobtn_color']:'')); ?> type="radio" value="btn-warning" name="_wpdmpp_settings[cobtn_color]"> Warning</label>
                    <label class="btn btn-danger <?php echo (isset($settings['cobtn_color']) && $settings['cobtn_color'] === 'danger')?'active':''; ?>"><input <?php checked('btn-danger', (isset($settings['cobtn_color'])?$settings['cobtn_color']:'')); ?> type="radio" value="btn-danger" name="_wpdmpp_settings[cobtn_color]"> Danger</label>
                </div><br/>
                <em class="note"><?php _e('You can change colors from User Interface settings page'); ?></em>

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading"><?php _e('Invoice', 'wpdm-premium-packages'); ?></div>
        <div class="panel-body">
            <div class="form-group">
                <label for="invoice-logo"><?php _e('Invoice Logo URL','wpdm-premium-packages'); ?></label>
                <div class="input-group">
                    <input type="text" name="_wpdmpp_settings[invoice_logo]" id="invoice-logo" class="form-control" value="<?php echo isset($settings['invoice_logo']) ? $settings['invoice_logo'] : ''; ?>"/>
                    <span class="input-group-btn">
                        <button class="btn btn-default btn-media-upload" type="button" rel="#invoice-logo"><i class="far fa-image"></i></button>
                    </span>
                </div>
            </div>
            <div class="form-group">
                <label for="company-address"><?php _e('Company Address', 'wpdm-premium-packages'); ?></label>
                <textarea class="form-control" name="_wpdmpp_settings[invoice_company_address]" id="company-address"><?php echo isset($settings['invoice_company_address']) ? $settings['invoice_company_address'] : ''; ?></textarea>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading"><?php _e('Miscellaneous', 'wpdm-premium-packages'); ?></div>
        <div class="panel-body">
            <label>
            <input type="hidden" name="_wpdmpp_settings[disable_fron_end_css]"  value="0" />
            <input type="checkbox" name="_wpdmpp_settings[disable_fron_end_css]" id="disable_fron_end_css"
                   value="1" <?php if (isset($settings['disable_fron_end_css']) && $settings['disable_fron_end_css'] == 1) echo "checked='checked'"; ?>> <?php _e("Disable plugin CSS from front-end", "wpdm-premium-packages"); ?>
            </label>
        </div>
    </div>

    <?php do_action("wpdmpp_basic_options"); ?>
</div>

<style>
    .w3eden input[type="radio"], .w3eden input[type="checkbox"] {
        line-height: normal;
        margin: -2px 0 0;
    }
    .panel-body label{
        font-weight: 400 !important;
    }
    .wpdmpp-settings-fields{
        margin-top: 20px;
    }
    .btn-group.btn-group-sm .btn {
        font-size: 11px;
    }
</style>
<script>
    jQuery(function ($) {
        $('.__wpdm_a2c_button_color, .__wpdm_a2c_button_size').on('change', function () {
            $('#__wpdm_a2c_button').attr('class', 'btn '+ $('.__wpdm_a2c_button_color:checked').val() + ' ' + $('.__wpdm_a2c_button_size:checked').val());
        });

        $('#__wpdm_a2c_button_br').on('change', function () {
            $('#__wpdm_a2c_button').css('border-radius', $(this).val()+'px');
        });

        $('#__wpdm_a2c_button_label').on('keyup', function () {
            $('#__wpdm_a2c_button').html($(this).val());
        });
    });
</script>
