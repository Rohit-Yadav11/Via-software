<?php

if ( ! defined ( 'ABSPATH' ) ) {
	exit;
}

/**
 * These functions are only kept for backwards compatibility, but will call their new respective methods
 * @param      $attributes
 * @param null $content
 *
 * @return mixed
 */

function edd_asc_cart_has_contents( $attributes, $content = null ) {
	return edd_additional_shortcodes()->shortcodes->cart_has_contents( $attributes, $content );
}

function edd_asc_cart_is_empty( $attributes, $content = null ) {
	return edd_additional_shortcodes()->shortcodes->cart_is_empty( $attributes, $content );
}

function edd_asc_user_has_purchases( $attributes, $content = null ) {
	return edd_additional_shortcodes()->shortcodes->user_has_purchases( $attributes, $content );
}

function edd_asc_user_has_purchased( $attributes, $content = null ) {
	return edd_additional_shortcodes()->shortcodes->user_has_purchased( $attributes, $content );
}

function edd_asc_user_has_no_purchases( $attributes, $content = null ) {
	return edd_additional_shortcodes()->shortcodes->user_has_no_purchases( $attributes, $content );
}

function edd_asc_is_user_logged_in( $attributes, $content = null ) {
	return edd_additional_shortcodes()->shortcodes->is_user_logged_in( $attributes, $content );
}

function edd_asc_is_user_logged_out( $attributes, $content = null ) {
	return edd_additional_shortcodes()->shortcodes->is_user_logged_out( $attributes, $content );
}

function edd_asc_maybe_do_shortcode( $content )  {
	return edd_additional_shortcodes()->maybe_do_shortcode( $content );
}