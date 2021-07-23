<?php
/**
 *  Template for Saved Cart UI
 *
 * This template can be overridden by copying it to yourtheme/download-manager/checkout-cart/cart-save.php.
 *
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
} ?>

<div id="wpdm-save-cart" class="d-none mb-2">
    <div class="panel panel-primary">
        <div class="panel-body">
            <input type=hidden id="cartid"  class="form-control group-item" value="">
            <div class="input-group">
                <div class="input-group-prepend"><span class="input-group-text"><strong><?php _e('Saved Cart URL','wpdm-premium-packages'); ?></strong></span></div>
                <input type=text readonly=readonly style="background: #fff;cursor: copy" onclick="this.select();" id="carturl"  class="form-control group-item" value="">
            </div>
        </div>

    </div>
</div>
