<?php
namespace ElementPack;

use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Admin {

	public function __construct() {
		add_action( 'admin_init', [ $this, 'admin_script' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ] );

		add_filter( 'plugin_row_meta', [ $this, 'plugin_row_meta' ], 10, 2 );
	}

	public function enqueue_styles() {

		$direction_suffix = is_rtl() ? '.rtl' : '';
		$suffix           = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_style( 'element-pack-editor', BDTEP_ASSETS_URL . 'css/element-pack-editor' . $direction_suffix . '.css', [], BDTEP_VER );

		wp_enqueue_style( 'bdt-uikit', BDTEP_ASSETS_URL . 'css/bdt-uikit' . $direction_suffix . '.css', [], '3.2' );
		wp_enqueue_style( 'bdthemes-element-pack-admin', BDTEP_ASSETS_URL . 'css/admin' . $direction_suffix . '.css', [], BDTEP_VER );
		wp_enqueue_style( 'element-pack-editor' );

		wp_enqueue_script( 'bdt-uikit', BDTEP_ASSETS_URL . 'js/bdt-uikit' . $suffix . '.js', [ 'jquery' ], BDTEP_VER );


	}


	public function plugin_row_meta( $plugin_meta, $plugin_file ) {
		if ( BDTEP_PBNAME === $plugin_file ) {
			$row_meta = [
				'docs' => '<a href="https://elementpack.pro/contact/" aria-label="' . esc_attr( __( 'Go for Get Support', 'bdthemes-element-pack-lite' ) ) . '" target="_blank">' . __( 'Get Support', 'bdthemes-element-pack-lite' ) . '</a>',
				'video' => '<a href="https://www.youtube.com/playlist?list=PLP0S85GEw7DOJf_cbgUIL20qqwqb5x8KA" aria-label="' . esc_attr( __( 'View Element Pack Video Tutorials', 'bdthemes-element-pack-lite' ) ) . '" target="_blank">' . __( 'Video Tutorials', 'bdthemes-element-pack-lite' ) . '</a>',
			];

			$plugin_meta = array_merge( $plugin_meta, $row_meta );
		}

		return $plugin_meta;
	}

	public function admin_script() {
		if ( is_admin() ) { // for Admin Dashboard Only
			// Embed the Script on our Plugin's Option Page Only
			if ( isset( $_GET['page'] ) && $_GET['page'] == 'element_pack_options' ) {
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'jquery-form' );
			}
		}
	}
	
}
