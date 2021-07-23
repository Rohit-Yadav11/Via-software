<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EDD_Additional_Shortcodes_Core {

	public function __construct() {
		add_shortcode( 'edd_cart_has_contents',     array( $this, 'cart_has_contents' ) );
		add_shortcode( 'edd_items_in_cart',         array( $this, 'items_in_cart' ) );
		add_shortcode( 'edd_cart_is_empty',         array( $this, 'cart_is_empty' ) );
		add_shortcode( 'edd_user_has_purchases',    array( $this, 'user_has_purchases' ) );
		add_shortcode( 'edd_user_has_purchased',    array( $this, 'user_has_purchased' ) );
		add_shortcode( 'edd_user_has_no_purchases', array( $this, 'user_has_no_purchases' ) );
		add_shortcode( 'edd_is_user_logged_in',     array( $this, 'is_user_logged_in' ) );
		add_shortcode( 'edd_is_user_logged_out',    array( $this, 'is_user_logged_out' ) );
	}
	
	function cart_has_contents( $attributes, $content = null ) {
		if ( edd_get_cart_contents() ) {
			return edd_additional_shortcodes()->maybe_do_shortcode( $content );
		}
	}

	function items_in_cart( $attributes, $content = null ) {
		if ( ! edd_get_cart_contents() ) {
			return '';
		}

		$args = shortcode_atts( array( 'ids' => '', 'match' => 'any' ), $attributes, 'edd_items_in_cart' );

		$downloads = explode( ',', str_replace( ' ', '', $args['ids'] ) );
		$match     = $args['match'];

		$matches = 0;
		foreach ( $downloads as $download ) {
			if ( strpos( $download, ':' ) ) {
				$download = explode( ':', $download );
				$item_in_cart = edd_item_in_cart( $download[0], array( 'price_id' => $download[1] ) );
			} else {
				$item_in_cart = edd_item_in_cart( $download );
			}

			if ( $item_in_cart ) {
				$matches++;

				if ( 'any' === strtolower( $match ) ) {
					return $content;
				}
			}
		}

		if ( 'all' === strtolower( $match ) && count( $downloads ) === $matches ) {
			return $content;
		}

		return '';
	}
	
	function cart_is_empty( $attributes, $content = null ) {
		if ( !edd_get_cart_contents() ) {
			return edd_additional_shortcodes()->maybe_do_shortcode( $content );
		}
	}
	
	function user_has_purchases( $attributes, $content = null ) {
		$user_id = get_current_user_id();
		if ( edd_has_purchases( $user_id ) ) {
			return edd_additional_shortcodes()->maybe_do_shortcode( $content );
		}
	}
	
	function user_has_purchased( $attributes, $content = null ) {
		$args      = shortcode_atts( array( 'ids' => '' ), $attributes, 'edd_user_has_purchased' );
		$downloads = explode( ',', str_replace( ' ', '', $args['ids'] ) );

		// If the user is logged out, and we aren't concerned with logged out users, don't show the content
		if ( ! is_user_logged_in() || empty( $downloads ) ) {
			return '';
		}

		$user_id = get_current_user_id();

		if ( ! edd_has_purchases( $user_id ) ) {
			return '';
		}

		foreach ( $downloads as $download ) {
			if ( strpos( $download, ':' ) ) {
				$download = explode( ':', $download );
				$has_purchased = edd_has_user_purchased( $user_id, $download[0], $download[1] );
			} else {
				$has_purchased = edd_has_user_purchased( $user_id, $download );
			}
			if ( $has_purchased ) {
				return $content;
			}
		}

		return '';
	}
	
	function user_has_no_purchases( $attributes, $content = null ) {
		$args = shortcode_atts( array( 'loggedout' => 'true' ), $attributes, 'edd_user_has_no_purchases' );

		// If the user is logged out, and we aren't concerned with logged out users, don't show the content
		if ( $args['loggedout'] == 'false' && ! is_user_logged_in() ) {
			return '';
		}

		$user_id = get_current_user_id();
		if ( !edd_has_purchases( $user_id ) )
			return edd_additional_shortcodes()->maybe_do_shortcode( $content );
	}
	
	function is_user_logged_in( $attributes, $content = null ) {
		if ( ! is_user_logged_in() ) {
			return '';
		}
		return edd_additional_shortcodes()->maybe_do_shortcode( $content );
	}
	
	function is_user_logged_out( $attributes, $content = null ) {
		if ( is_user_logged_in() ) {
			return '';
		}
		return edd_additional_shortcodes()->maybe_do_shortcode( $content );
	}
	
}