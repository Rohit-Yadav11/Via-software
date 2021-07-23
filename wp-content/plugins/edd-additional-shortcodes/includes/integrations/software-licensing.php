<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EDD_Additional_Shortcodes_SL {

	public function __construct() {
		add_shortcode( 'edd_has_active_licenses',  array( $this, 'has_active_licenses' ) );
		add_shortcode( 'edd_has_expired_licenses', array( $this, 'has_expired_licenses' ) );
	}

	function has_active_licenses( $attributes, $content = null ) {
		$has_active_license = $this->has_license_check( 'active' );
		if ( $has_active_license ) {
			return edd_additional_shortcodes()->maybe_do_shortcode( $content );
		}
	}

	function has_expired_licenses( $attributes, $content = null ) {
		$has_expired_license = $this->has_license_check( 'expired' );
		if ( $has_expired_license ) {
			return edd_additional_shortcodes()->maybe_do_shortcode( $content );
		}
	}

	private function has_license_check( $status = '' ) {
		if ( empty( $status ) ) {
			return false;
		}

		$license_keys = edd_software_licensing()->get_license_keys_of_user();
		foreach ( $license_keys as $license ) {

			$license_status = edd_software_licensing()->get_license_status( $license->ID );

			if ( $status === $license_status ) {
				return true;
			}

		}

		return false;
	}

}