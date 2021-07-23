<?php
/**
 * Show Extra Gigs options before Add To Cart button.
 *
 * This template is active in pacakges where Extra Gigs is active.
 *
 * This template can be overridden by copying it to yourtheme/download-manager/add-to-cart/extra-gigs.php.
 *
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$gigs_html = "";
$price_variation = get_post_meta($product_id,"__wpdm_price_variation",true);
$currency_sign = wpdmpp_currency_sign();
$variation = get_post_meta($product_id,"__wpdm_variation",true);
if( is_array($variation) && $price_variation){

    foreach( $variation as $key => $value ) {

        $vtype = "radio";

        if( isset($value['multiple']) ){
            $multiple = "multiple='multiple'";
            $vtype = "checkbox";
        }
        else $multiple = "";
        $gigs_html .= '<div class="wpdmpp-extra-gigs"><div class="gigs-heading">'.ucfirst($value['vname']).'</div><div class="gigs-body">';

        // Variation type Select
        /*
         $gigs_html = '<select name="variation[]" id="var_price_'.uniqid().'"' . $multiple .' >';
        foreach($value as $optionkey=>$optionvalue){
            if(is_array($optionvalue)){
                $vari = (intval($optionvalue['option_price'])!=0)?" ( + {$currency_sign}".number_format($optionvalue['option_price'],2,".","")." )":"";
                $gigs_html .='<option value="'.$optionkey.'">'." ".$optionvalue['option_name'].$vari.'</option>';
            }
        }
        $gigs_html .= '</select>';
        */

        // Variation type Radio

        $vcount = 0;

        foreach($value as $optionkey => $optionvalue){
            if(is_array($optionvalue)){
                $optionvalue['option_price'] = floatval($optionvalue['option_price']);
                $vindex = $vtype == 'radio'?$key:'';
                $cfirst = ($vtype == 'radio' && $vcount == 0)?'checked=checked':'';

                if( wpdmpp_currency_sign_position() == 'before' )
                    $vari = ( intval($optionvalue['option_price']) != 0 ) ? " ( + {$currency_sign}".number_format($optionvalue['option_price'],2,".","")." )" : "";
                else
                    $vari = ( intval($optionvalue['option_price']) != 0 ) ? " ( + ".number_format($optionvalue['option_price'],2,".","")."{$currency_sign} )" : "";

                $gigs_html .='<label data-placement="left" class="d-block ttip" title="'.(isset($optionvalue['option_description'])?esc_attr($optionvalue['option_description']):'').'"><input class="wpdmpp-extra-gig wpdm-'.$vtype.'" type='.$vtype.' '.$cfirst.' data-product-id="'.$product_id.'" data-price="'.number_format($optionvalue['option_price'],2,".","").'" name="variation['.$vindex.']" class="wpdm-'.$vtype.' wpdmpp-extra-gig wpdmpp-extra-gig-'.$product_id.'"  value="'.$optionkey.'"> '." ".$optionvalue['option_name'].$vari."</label>";

                $vcount++;
            }
        }

        $gigs_html .= "</div></div>";
    }
}

return $gigs_html;