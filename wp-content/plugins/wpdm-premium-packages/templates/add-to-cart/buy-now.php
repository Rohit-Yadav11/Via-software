<?php
/**
 * User: shahnuralam
 * Create Date: 24/12/18 3:47 PM
 * Last Updated: 15/06/19
 * Version: 1.2
 */
if (!defined('ABSPATH')) die();
if((int)get_wpdmpp_option('show_buynow') === 1){
    if(!isset($params)) $params = array();
    ?>
    <div class="w3eden">

        <div class="wpdmpp-buy-now buy-now-<?php echo $product_id; ?>">

            <?php if(isset($params, $params['title'])){ ?>
            <div class="card card-default">
                <div class="card-header"><?php echo str_replace("{price}", wpdmpp_price_format(wpdmpp_product_price($product_id), true, true), $params['title']); ?></div>
                <div class="card-body">
                    <?php } ?>

                    <div class="wpdmpp-buy-now-<?php echo $product_id; ?>" id="wpdmpp-buy-now-<?php echo $product_id; ?>">
                        <?php if(isset($params, $params['showprice']) && (int)$params['showprice'] === 1){ ?>
                            <div class="wpdmpp-buynow-price" id="wpdmpp-buynow-price-<?php echo $product_id; ?>">
                                <h2 class="text-center"><?php echo wpdmpp_price_format(wpdmpp_product_price($product_id), true, true); ?></h2>
                            </div>
                        <?php } ?>

                        <?php
                        $pp = new \WPDMPP\Libs\PaymentMethods\Paypal();
                        $buynow['Paypal'] = $pp->buyNowButton($product_id);
                        $buynow = apply_filters("wpdmpp_buynow_options", $buynow, $params);
                        foreach ($buynow as $pm => $buynow_html){
                            echo "<div id='buynow-{$pm}'>";
                            echo $buynow_html;
                            echo "</div>";
                        }
                        ?>

                    </div>
                    <?php if(isset($params, $params['title'])){ ?>

                </div>
            </div>
        <?php } ?>

        </div>
    </div>

    <style>


        .wpdmpp-buy-now{
            margin: 10px 0;
            max-width: 330px;
        }

        .wpdmpp-buynow-price h2{
            margin: 0 0 20px;
            font-weight: 700;
            font-family: var(--fetfont);
            font-size: 18pt;
        }

        #wpdmpp-paypal-button-container *,
        #wpdmpp-paypal-button-container {
            max-width: 100% !important;
            width: 100%;
        }
        .zoid-outlet{
            min-width: 100% !important;
        }

    </style>
<?php }
