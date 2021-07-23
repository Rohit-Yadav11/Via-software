<?php

namespace ElementPack\Includes;

/**
 * Element_Pack_WPML class
 */
class Element_Pack_WPML {

	/**
	 * A reference to an instance of this class.
	 * @since 3.1.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Constructor for the class
	 */
	public function init() {

		// WPML String Translation plugin exist check
		if ( defined( 'WPML_ST_VERSION' ) ) {

			if ( class_exists( 'WPML_Elementor_Module_With_Items' ) ) {
				$this->load_wpml_modules();
			}

			add_filter( 'wpml_elementor_widgets_to_translate', array( $this, 'add_translatable_nodes' ) );
		}

	}

	/**
	 * Load wpml required repeater class files.
	 * @return void
	 */
	public function load_wpml_modules() {
		require_once( BDTEP_INC_PATH . 'compatiblity/wpml/class-wpml-element-pack-accordion.php' );
		require_once( BDTEP_INC_PATH . 'compatiblity/wpml/class-wpml-element-pack-business-hours.php' );
		require_once( BDTEP_INC_PATH . 'compatiblity/wpml/class-wpml-element-pack-custom-gallery.php' );
		require_once( BDTEP_INC_PATH . 'compatiblity/wpml/class-wpml-element-pack-fancy-list.php' );
		require_once( BDTEP_INC_PATH . 'compatiblity/wpml/class-wpml-element-pack-image-accordion.php' );
		require_once( BDTEP_INC_PATH . 'compatiblity/wpml/class-wpml-element-pack-logo-grid.php' );
		require_once( BDTEP_INC_PATH . 'compatiblity/wpml/class-wpml-element-pack-member.php' );
		require_once( BDTEP_INC_PATH . 'compatiblity/wpml/class-wpml-element-pack-panel-slider.php' );
		require_once( BDTEP_INC_PATH . 'compatiblity/wpml/class-wpml-element-pack-price-list.php' );
		require_once( BDTEP_INC_PATH . 'compatiblity/wpml/class-wpml-element-pack-slider.php' );
	}

	/**
	 * Add element pack translation nodes
	 * @param array $nodes_to_translate
	 * @return array
	 */
	public function add_translatable_nodes( $nodes_to_translate ) {

		$nodes_to_translate[ 'bdt-accordion' ] = [
			'conditions' => [ 'widgetType' => 'bdt-accordion' ],
			'fields'     => [],
			'integration-class' => 'WPML_ElementPack_Accordion',
		];

		$nodes_to_translate[ 'bdt-business-hours' ] = [
			'conditions' => [ 'widgetType' => 'bdt-business-hours' ],
			'fields'     => [
				[
					'field'       => 'dynamic_open_day',
					'type'        => esc_html__( 'Open Status', 'bdthemes-element-pack' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'dynamic_close_day',
					'type'        => esc_html__( 'Close Status', 'bdthemes-element-pack' ),
					'editor_type' => 'AREA',
				],
			],
			'integration-class' => 'WPML_ElementPack_Business_Hours',
		];

		$nodes_to_translate[ 'bdt-call-out' ] = [
			'conditions' => [ 'widgetType' => 'bdt-call-out' ],
			'fields'     => [
				[
					'field'       => 'title',
					'type'        => esc_html__( 'Title', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description',
					'type'        => esc_html__( 'Description', 'bdthemes-element-pack' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'button_text',
					'type'        => esc_html__( 'Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-contact-form' ] = [
			'conditions' => [ 'widgetType' => 'bdt-contact-form' ],
			'fields'     => [
				[
					'field'       => 'button_text',
					'type'        => esc_html__( 'Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'user_name_label',
					'type'        => esc_html__( 'Label', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'user_name_placeholder',
					'type'        => esc_html__( 'Placeholder', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'contact_label',
					'type'        => esc_html__( 'Label', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'contact_placeholder',
					'type'        => esc_html__( 'Placeholder', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'subject_label',
					'type'        => esc_html__( 'Label', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'subject_placeholder',
					'type'        => esc_html__( 'Placeholder', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'email_address_label',
					'type'        => esc_html__( 'Label', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'email_placeholder',
					'type'        => esc_html__( 'Placeholder', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'message_label',
					'type'        => esc_html__( 'Label', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'message_placeholder',
					'type'        => esc_html__( 'Placeholder', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'additional_message',
					'type'        => esc_html__( 'Message', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-cookie-consent' ] = [
			'conditions' => [ 'widgetType' => 'bdt-cookie-consent' ],
			'fields'     => [
				[
					'field'       => 'message',
					'type'        => esc_html__( 'Message', 'bdthemes-element-pack' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'button_text',
					'type'        => esc_html__( 'Button Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'learn_more_text',
					'type'        => esc_html__( 'Learn More Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-countdown' ] = [
			'conditions' => [ 'widgetType' => 'bdt-countdown' ],
			'fields'     => [
				[
					'field'       => 'label_days',
					'type'        => esc_html__( 'Days', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'label_hours',
					'type'        => esc_html__( 'Hours', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'label_minutes',
					'type'        => esc_html__( 'Minutes', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'label_seconds',
					'type'        => esc_html__( 'Seconds', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-custom-gallery' ] = [
			'conditions' => [ 'widgetType' => 'bdt-custom-gallery' ],
			'fields'     => [],
			'integration-class' => 'WPML_ElementPack_Custom_Gallery',
		];

		$nodes_to_translate[ 'bdt-dual-button' ] = [
			'conditions' => [ 'widgetType' => 'bdt-dual-button' ],
			'fields'     => [
				[
					'field'       => 'middle_text',
					'type'        => esc_html__( 'Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'button_a_text',
					'type'        => esc_html__( 'Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'button_a_onclick_event',
					'type'        => esc_html__( 'OnClick Event', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'button_b_text',
					'type'        => esc_html__( 'Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'button_b_onclick_event',
					'type'        => esc_html__( 'OnClick Event', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-fancy-list' ] = [
			'conditions' => [ 'widgetType' => 'bdt-fancy-list' ],
			'fields'     => [],
			'integration-class' => 'WPML_ElementPack_Fancy_List',
		];

		$nodes_to_translate[ 'bdt-featured-box' ] = [
			'conditions' => [ 'widgetType' => 'bdt-featured-box' ],
			'fields'     => [
				[
					'field'       => 'title_text',
					'type'        => esc_html__( 'Title', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'sub_title_text',
					'type'        => esc_html__( 'Sub Title', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description_text',
					'type'        => esc_html__( 'Content', 'bdthemes-element-pack' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'readmore_text',
					'type'        => esc_html__( 'Read More Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'badge_text',
					'type'        => esc_html__( 'Badge Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-image-accordion' ] = [
			'conditions' => [ 'widgetType' => 'bdt-image-accordion' ],
			'fields'     => [],
			'integration-class' => 'WPML_ElementPack_Image_Accordion',
		];

		$nodes_to_translate[ 'bdt-logo-grid' ] = [
			'conditions' => [ 'widgetType' => 'bdt-logo-grid' ],
			'fields'     => [],
			'integration-class' => 'WPML_ElementPack_Logo_Grid',
		];

		$nodes_to_translate[ 'bdt-flip-box' ] = [
			'conditions' => [ 'widgetType' => 'bdt-flip-box' ],
			'fields'     => [
				[
					'field'       => 'front_title_text',
					'type'        => esc_html__( 'Title', 'bdthemes-element-pack-lite' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'front_description_text',
					'type'        => esc_html__( 'Description', 'bdthemes-element-pack-lite' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'back_title_text',
					'type'        => esc_html__( 'Title', 'bdthemes-element-pack-lite' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'back_description_text',
					'type'        => esc_html__( 'Description', 'bdthemes-element-pack-lite' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'button_text',
					'type'        => esc_html__( 'Button Text', 'bdthemes-element-pack-lite' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-image-compare' ] = [
			'conditions' => [ 'widgetType' => 'bdt-image-compare' ],
			'fields'     => [
				[
					'field'       => 'before_label',
					'type'        => esc_html__( 'Before Label', 'bdthemes-element-pack-lite' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'after_label',
					'type'        => esc_html__( 'After Label', 'bdthemes-element-pack-lite' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'lightbox' ] = [
			'conditions' => [ 'widgetType' => 'lightbox' ],
			'fields'     => [
				[
					'field'       => 'button_text',
					'type'        => esc_html__( 'Button Text', 'bdthemes-element-pack-lite' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'content_caption',
					'type'        => esc_html__( 'Content Caption', 'bdthemes-element-pack-lite' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-member' ] = [
			'conditions' => [ 'widgetType' => 'bdt-member' ],
			'fields'     => [
				[
					'field'       => 'name',
					'type'        => esc_html__( 'Member Name', 'bdthemes-element-pack-lite' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description_text',
					'type'        => esc_html__( 'Member Description', 'bdthemes-element-pack-lite' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'role',
					'type'        => esc_html__( 'Member Role', 'bdthemes-element-pack-lite' ),
					'editor_type' => 'LINE',
				],

			],
			'integration-class' => 'WPML_ElementPack_Team_Member',
		];

		$nodes_to_translate[ 'bdt-progress-pie' ] = [
			'conditions' => [ 'widgetType' => 'bdt-progress-pie' ],
			'fields'     => [
				[
					'field'       => 'percent',
					'type'        => esc_html__( 'Percent', 'bdthemes-element-pack-lite' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'title',
					'type'        => esc_html__( 'Progress Pie Title', 'bdthemes-element-pack-lite' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'before',
					'type'        => esc_html__( 'Before Text', 'bdthemes-element-pack-lite' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'text',
					'type'        => esc_html__( 'Middle Text', 'bdthemes-element-pack-lite' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'after',
					'type'        => esc_html__( 'After Text', 'bdthemes-element-pack-lite' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-scroll-button' ] = [
			'conditions' => [ 'widgetType' => 'bdt-scroll-button' ],
			'fields'     => [
				[
					'field'       => 'scroll_button_text',
					'type'        => esc_html__( 'Button Text', 'bdthemes-element-pack-lite' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'section_id',
					'type'        => esc_html__( 'Section ID', 'bdthemes-element-pack-lite' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-slider' ] = [
			'conditions' => [ 'widgetType' => 'bdt-slider' ],
			'fields'     => [
				[
					'field'       => 'button_text',
					'type'        => esc_html__( 'Button Text', 'bdthemes-element-pack-lite' ),
					'editor_type' => 'LINE',
				],
			],
			'integration-class' => 'WPML_ElementPack_Slider',
		];

		$nodes_to_translate[ 'bdt-search' ] = [
			'conditions' => [ 'widgetType' => 'bdt-search' ],
			'fields'     => [
				[
					'field'       => 'placeholder',
					'type'        => esc_html__( 'Placeholder', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-panel-slider' ] = [
			'conditions' => [ 'widgetType' => 'bdt-panel-slider' ],
			'fields'     => [],
			'integration-class' => 'WPML_ElementPack_Panel_Slider',
		];

		$nodes_to_translate[ 'bdt-price-list' ] = [
			'conditions' => [ 'widgetType' => 'bdt-price-list' ],
			'fields'     => [],
			'integration-class' => 'WPML_ElementPack_Price_List',
		];

		$nodes_to_translate[ 'bdt-step-flow' ] = [
			'conditions' => [ 'widgetType' => 'bdt-step-flow' ],
			'fields'     => [
				[
					'field'       => 'title_text',
					'type'        => esc_html__( 'Title', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description_text',
					'type'        => esc_html__( 'Content', 'bdthemes-element-pack' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'readmore_text',
					'type'        => esc_html__( 'Read More Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'badge_text',
					'type'        => esc_html__( 'Badge Text', 'bdthemes-element-pack' ),
					'editor_type' => 'LINE',
				],
			],
		];

		$nodes_to_translate[ 'bdt-toggle' ] = [
			'conditions' => [ 'widgetType' => 'bdt-toggle' ],
			'fields'     => [
				[
					'field'       => 'toggle_title',
					'type'        => esc_html__( 'Normal Title', 'bdthemes-element-pack-lite' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'toggle_open_title',
					'type'        => esc_html__( 'Opened Title', 'bdthemes-element-pack-lite' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'toggle_content',
					'type'        => esc_html__( 'Content', 'bdthemes-element-pack-lite' ),
					'editor_type' => 'AREA',
				],
			],
		];

		$nodes_to_translate[ 'bdt-trailer-box' ] = [
			'conditions' => [ 'widgetType' => 'bdt-trailer-box' ],
			'fields'     => [
				[
					'field'       => 'pre_title',
					'type'        => esc_html__( 'Pre Title', 'bdthemes-element-pack-lite' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'title',
					'type'        => esc_html__( 'Title', 'bdthemes-element-pack-lite' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'content',
					'type'        => esc_html__( 'Content', 'bdthemes-element-pack-lite' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'button_text',
					'type'        => esc_html__( 'Text', 'bdthemes-element-pack-lite' ),
					'editor_type' => 'LINE',
				],
			],
		];

		return $nodes_to_translate;
	}

	/**
	 * Returns the instance.
	 * @since  3.1.0
	 * @return object
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
}
