<?php
namespace ElementPack;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! function_exists('is_plugin_active')) { include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); }

final class Manager {
	private $_modules = null;

	private function is_module_active( $module_id ) {

		$module_data = $this->get_module_data( $module_id );
		$options     = get_option( 'element_pack_active_modules', [] );
		
		if ( ! isset( $options[ $module_id ] ) ) {
			return $module_data['default_activation'];
		} else {
			if($options[ $module_id ] == "on"){
				return true;
			} else {
				return false;
			}
		}

		return 'true' === $options[ $module_id ];
	}

	private function get_module_data( $module_id ) {
		return isset( $this->_modules[ $module_id ] ) ? $this->_modules[ $module_id ] : false;
	}

    private function has_module_style( $module_id ) {

	    $module_data = $this->get_module_data( $module_id );

	    if ( isset( $module_data['has_style'] ) ) {
            return $module_data['has_style'];
        } else {
	        return false;
        }

    }

    private function has_module_script( $module_id ) {

	    $module_data = $this->get_module_data( $module_id );

	    if ( isset( $module_data['has_script'] ) ) {
            return $module_data['has_script'];
        } else {
	        return false;
        }

    }

	public function __construct() {
		$modules = [
			'accordion',
			'business-hours',
			'call-out',
			'contact-form',
			'cookie-consent',
			'countdown',
			'custom-gallery',
			'featured-box',
			'flip-box',
			'image-accordion',
			'image-compare',
			'image-magnifier',
			'lightbox',
			'logo-grid',
			'member',
			'panel-slider',
			'progress-pie',
			'price-list',
			'query-control',
			'reading-progress',
			'scroll-button',
			'search',
			'step-flow',
			'slider',
			'toggle',
			'trailer-box',
			'twitter-grid',
			'dual-button',
			'fancy-list',
		];

		$tutor_lms     = element_pack_option( 'tutor-lms', 'element_pack_third_party_widget', 'on' );
		$cf_seven      = element_pack_option( 'contact-form-seven', 'element_pack_third_party_widget', 'on' );
		$we_forms      = element_pack_option( 'we-forms', 'element_pack_third_party_widget', 'on' );
		$fluent_forms  = element_pack_option( 'fluent-forms', 'element_pack_third_party_widget', 'on' );
		$ninja_forms   = element_pack_option( 'ninja-forms', 'element_pack_third_party_widget', 'on' );
		$caldera_forms = element_pack_option( 'caldera-forms', 'element_pack_third_party_widget', 'on' );
		$everest_forms = element_pack_option( 'everest-forms', 'element_pack_third_party_widget', 'on' );
		$formidable_forms = element_pack_option( 'formidable-forms', 'element_pack_third_party_widget', 'on' );

		if ( is_plugin_active( 'tutor/tutor.php' ) and 'on' === $tutor_lms ) {
			$modules[] = 'tutor-lms';
		}

		if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) and 'on' === $cf_seven ) {
			$modules[] = 'contact-form-seven';
		}

		if ( is_plugin_active( 'weforms/weforms.php' ) and 'on' === $we_forms ) {
			$modules[] = 'we-forms';
		}

		if ( is_plugin_active( 'fluentform/fluentform.php' ) and 'on' === $fluent_forms ) {
			$modules[] = 'fluent-forms';
		}

		if ( is_plugin_active( 'ninja-forms/ninja-forms.php' ) and 'on' === $ninja_forms ) {
			$modules[] = 'ninja-forms';
		}

		if ( is_plugin_active( 'caldera-forms/caldera-core.php' ) and 'on' === $caldera_forms ) {
			$modules[] = 'caldera-forms';
		}

		if ( is_plugin_active('everest-forms/everest-forms.php') and 'on' === $everest_forms ) {
            $modules[] = 'everest-forms';
		}
		
		if ( is_plugin_active('formidable/formidable.php') and 'on' === $formidable_forms ) {
            $modules[] = 'formidable-forms';
        }

		// elementor extend
		$backdrop_filter = element_pack_option( 'backdrop_filter', 'element_pack_elementor_extend', 'off' );
		if ( 'on' === $backdrop_filter ) {
			$modules[] = 'backdrop-filter';
		}

		// Fetch all modules data
		foreach ( $modules as $module ) {
			$this->_modules[ $module ] = require BDTEP_MODULES_PATH . $module . '/module.info.php';
		}

		$direction_suffix = is_rtl() ? '.rtl' : '';
		$suffix   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		foreach ( $this->_modules as $module_id => $module_data ) {
			if ( ! $this->is_module_active( $module_id ) ) {
				continue;
			}

			$class_name = str_replace( '-', ' ', $module_id );
			$class_name = str_replace( ' ', '', ucwords( $class_name ) );
			$class_name = __NAMESPACE__ . '\\Modules\\' . $class_name . '\Module';


			// register widget css
            if (  $this->has_module_style( $module_id ) ) {
                wp_register_style( 'ep-' . $module_id, BDTEP_URL . 'assets/css/ep-' . $module_id . $direction_suffix . '.css', [], BDTEP_VER );
            }

            // register widget javascript
			if (  $this->has_module_script( $module_id ) ) {
				wp_register_script( 'ep-' . $module_id, BDTEP_URL . 'assets/js/widgets/ep-' . $module_id . $suffix . '.js', ['jquery', 'bdt-uikit', 'elementor-frontend', 'element-pack-site'], BDTEP_VER, true );

			}

			$class_name::instance();
		}
	}
}
