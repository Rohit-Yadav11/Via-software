<?php
/**
 * Show package price/price-range before Add To Cart button.
 *
 * This template can be overridden by copying it to yourtheme/download-manager/add-to-cart/price.php.
 *
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

?>

<h3 class="wpdmpp-product-price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
    <span itemprop="price" id="price-<?php echo $product_id; ?>" content="<?php echo wpdmpp_effective_price($product_id); ?>" >
        <span itemprop="priceCurrency" content="USD"></span>
        <?php echo wpdmpp_price_range($product_id); ?>
    </span>
</h3>