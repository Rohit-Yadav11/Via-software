<?php
/**
 * Show all license options before Add To Cart button.
 *
 * This template is active in pacakges where "License Key Required" is enabled to sell different license variation of same product.
 *
 * This template can be overridden by copying it to yourtheme/download-manager/add-to-cart/select-license.php.
 *
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if(count($active_lics) > 0){ ?>
    <div class="licenses wpdmpp-license-select">
        <div class="license-heading"><strong><?php _e('License', 'wpdm-premium-packages'); ?></strong></div>
        <?php
        $license_count = 0;
        foreach ($active_lics as $licid => $lic) {
            $vindex = $licid;
            $field_type = "radio";
            $checked = ($field_type == 'radio' && $license_count == 0) ? 'checked=checked' : '';
            $license_price = number_format((double)$lic['price'], 2, ".", "");
            $license_price_with_currency_sign = wpdmpp_price_format($license_price, true, true);
            $license_price_html = (floatval($lic['price']) != 0) ? " ( {$license_price_with_currency_sign} )" : "";

            if($sales_price > 0 && $license_count == 0)  {
                $sales_price_info = wpdmpp_sales_price_info($product_id);
                $sales_price_with_currency_sign = wpdmpp_currency_sign_position() == 'before' ? $currency_sign.$sales_price : $sales_price.$currency_sign;
                $license_price_html = " ( <u class='ttip' title='{$sales_price_info}'>{$sales_price_with_currency_sign}</u> )";
                $license_price = $sales_price;
            }
            ?>
            <div class="wpdmpp-license-item">
            <label class="input-group mb-3">
                <div class="d-inline-block">
                    <input type="<?php echo $field_type; ?>" <?php echo $checked; ?>
                           data-product-id="<?php echo $product_id; ?>" data-price="<?php echo $license_price; ?>"
                           name="license"
                           class="wpdm-<?php echo $field_type; ?> wpdm-<?php echo $field_type; ?>-m price-variation price-variation-<?php echo $product_id; ?> license-<?php echo $product_id; ?>"
                           value="<?php echo $licid; ?>">
                </div>
                <div class="d-inline-block">
                <div><strong><?php echo $lic['name']; ?> <?php echo $license_price_html; ?></strong></div>
                    <span class="license-info text-muted"><?php echo $lic['description']; ?></span>
                </div>

            </label>
            </div>
            <?php
            $license_count++;
        }
        ?>
    </div>
<?php } ?>
