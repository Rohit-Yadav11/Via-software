<?php
namespace ElementPack\Modules\TutorLms\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Utils;

use ElementPack\Modules\QueryControl\Controls\Group_Control_Posts;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Tutor_Lms_Course_Carousel extends Module_Base {
	private $_query = null;

	public function get_name() {
		return 'bdt-tutor-lms-course-carousel';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Tutor LMS Course Carousel', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-tutor-lms-course-carousel';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'tutor', 'elearning', 'lms', 'course', 'course carousel', 'carousel' ];
	}

	public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-all-styles'];
        } else {
            return ['element-pack-font', 'ep-tutor-lms'];
        }
    }

	public function get_script_depends() {
      	return [ 'imagesloaded', 'bdt-uikit-icons' ];
    }

	public function on_import( $element ) {
		if ( ! get_post_type_object( $element['settings']['posts_post_type'] ) ) {
			$element['settings']['posts_post_type'] = 'post';
		}

		return $element;
	}

	public function on_export( $element ) {
		$element = Group_Control_Posts::on_export_remove_setting_from_element( $element, 'posts' );
		return $element;
	}

	public function get_query() {
		return $this->_query;
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/VYrIYQESjXs';
	}

	public function _register_controls() {
		$this->register_section_controls();
	}

	private function register_section_controls() {
		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__( 'Layout', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);
		
		$this->add_responsive_control(
			'columns',
			[
				'label'          => esc_html__( 'Columns', 'bdthemes-element-pack' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				],
			]
		);

		$this->add_responsive_control(
			'item_gap',
			[
				'label'   => esc_html__( 'Column Gap', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 35,
				],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'         => 'thumbnail_size',
				'label'        => esc_html__( 'Image Size', 'bdthemes-element-pack' ),
				'exclude'      => [ 'custom' ],
				'default'      => 'medium',
				'prefix_class' => 'bdt-tutor--thumbnail-size-',
			]
		);

		$this->add_control(
			'item_ratio',
			[
				'label'   => esc_html__( 'Item Height', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 50,
						'max'  => 500,
						'step' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course-item .bdt-tutor-course-header a img' => 'height: {{SIZE}}px',
				],
			]
        );
        
        $this->add_control(
			'show_meta_label',
			[
				'label'   => esc_html__( 'Show Meta Label', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_meta_wishlist',
			[
				'label'   => esc_html__( 'Show Wishlist Meta', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_rating',
			[
				'label'   => esc_html__( 'Show Rating', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_title',
			[
				'label'   => esc_html__( 'Show Title', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_user_clock',
			[
				'label'   => esc_html__( 'Show User Meta', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_author_avatar',
			[
				'label'   => esc_html__( 'Show Avatar', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_author_name',
			[
				'label'   => esc_html__( 'Show Author Name', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_category',
			[
				'label'   => esc_html__( 'Show Category', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_cart_btn_price',
			[
				'label'   => esc_html__( 'Show Price/Cart Button', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_query',
			[
				'label' => esc_html__( 'Query', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'source',
			[
				'label'   => _x( 'Source', 'Posts Query Control', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					''        => esc_html__( 'Show All', 'bdthemes-element-pack' ),
					'by_name' => esc_html__( 'Manual Selection', 'bdthemes-element-pack' ),
				],
				'label_block' => true,
			]
		);



		$this->add_control(
			'post_categories',
			[
				'label'       => esc_html__( 'Categories', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => element_pack_get_category('course-category'),
				'default'     => [],
				'label_block' => true,
				'multiple'    => true,
				'condition'   => [
					'source'    => 'by_name',
				],
			]
		);

		$this->add_control(
			'limit',
			[
				'label'   => esc_html__( 'Limit', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 9,
			]
		);

		$this->add_control(
			'orderby',
			[
				'label'   => esc_html__( 'Order by', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => [
					'date'     => esc_html__( 'Date', 'bdthemes-element-pack' ),
					'title'    => esc_html__( 'Title', 'bdthemes-element-pack' ),
					'category' => esc_html__( 'Category', 'bdthemes-element-pack' ),
					'rand'     => esc_html__( 'Random', 'bdthemes-element-pack' ),
				],
			]
		);

		$this->add_control(
			'order',
			[
				'label'   => esc_html__( 'Order', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => [
					'DESC' => esc_html__( 'Descending', 'bdthemes-element-pack' ),
					'ASC'  => esc_html__( 'Ascending', 'bdthemes-element-pack' ),
				],
			]
		);

		$this->add_control(
			'offset',
			[
				'label'     => esc_html__( 'Offset', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0,
				'condition' => [
					'posts_post_type!' => 'by_id',
				],
			]
		);

		$this->end_controls_section();

        $this->start_controls_section(
			'section_content_navigation',
			[
				'label' => __( 'Navigation', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'navigation',
			[
				'label'   => __( 'Navigation', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'arrows',
				'options' => [
					'both'            => esc_html__( 'Arrows and Dots', 'bdthemes-element-pack' ),
					'arrows-fraction' => esc_html__( 'Arrows and Fraction', 'bdthemes-element-pack' ),
					'arrows'          => esc_html__( 'Arrows', 'bdthemes-element-pack' ),
					'dots'            => esc_html__( 'Dots', 'bdthemes-element-pack' ),
					'progressbar'     => esc_html__( 'Progress', 'bdthemes-element-pack' ),
					'none'            => esc_html__( 'None', 'bdthemes-element-pack' ),
				],
				'prefix_class' => 'bdt-navigation-type-',
				'render_type' => 'template',				
			]
		);

		$this->add_control(
			'dynamic_bullets',
			[
				'label'     => __( 'Dynamic Bullets?', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'navigation' => [ 'dots', 'both' ],
				],
			]
		);

		$this->add_control(
			'show_scrollbar',
			[
				'label'     => __( 'Show Scrollbar?', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
			]
		);
		
		$this->add_control(
			'both_position',
			[
				'label'     => __( 'Arrows and Dots Position', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center',
				'options'   => element_pack_navigation_position(),
				'condition' => [
					'navigation' => 'both',
				],
				
			]
		);

		$this->add_control(
			'arrows_fraction_position',
			[
				'label'     => __( 'Arrows and Fraction Position', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center',
				'options'   => element_pack_navigation_position(),
				'condition' => [
					'navigation' => 'arrows-fraction',
				],
				
			]
		);

		$this->add_control(
			'arrows_position',
			[
				'label'     => __( 'Arrows Position', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center',
				'options'   => element_pack_navigation_position(),
				'condition' => [
					'navigation' => 'arrows',
				],
				
			]
		);

		$this->add_control(
			'dots_position',
			[
				'label'     => __( 'Dots Position', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bottom-center',
				'options'   => element_pack_pagination_position(),
				'condition' => [
					'navigation' => 'dots',
				],
				
			]
		);

		$this->add_control(
			'progress_position',
			[
				'label'   => __( 'Progress Position', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'bottom',
				'options' => [
					'bottom' => esc_html__('Bottom', 'bdthemes-element-pack'),
					'top'    => esc_html__('Top', 'bdthemes-element-pack'),
				],
				'condition' => [
					'navigation' => 'progressbar',
				],
				
			]
		);

		$this->add_control(
			'nav_arrows_icon',
			[
				'label'   => esc_html__( 'Arrows Icon', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '5',
				'options' => [
					'1' => esc_html__('Style 1', 'bdthemes-element-pack'),
					'2' => esc_html__('Style 2', 'bdthemes-element-pack'),
					'3' => esc_html__('Style 3', 'bdthemes-element-pack'),
					'4' => esc_html__('Style 4', 'bdthemes-element-pack'),
					'5' => esc_html__('Style 5', 'bdthemes-element-pack'),
					'6' => esc_html__('Style 6', 'bdthemes-element-pack'),
					'7' => esc_html__('Style 7', 'bdthemes-element-pack'),
					'8' => esc_html__('Style 8', 'bdthemes-element-pack'),
					'9' => esc_html__('Style 9', 'bdthemes-element-pack'),
					'10' => esc_html__('Style 10', 'bdthemes-element-pack'),
					'11' => esc_html__('Style 11', 'bdthemes-element-pack'),
					'12' => esc_html__('Style 12', 'bdthemes-element-pack'),
					'13' => esc_html__('Style 13', 'bdthemes-element-pack'),
					'14' => esc_html__('Style 14', 'bdthemes-element-pack'),
					'15' => esc_html__('Style 15', 'bdthemes-element-pack'),
					'16' => esc_html__('Style 16', 'bdthemes-element-pack'),
					'17' => esc_html__('Style 17', 'bdthemes-element-pack'),
					'18' => esc_html__('Style 18', 'bdthemes-element-pack'),
					'circle-1' => esc_html__('Style 19', 'bdthemes-element-pack'),
					'circle-2' => esc_html__('Style 20', 'bdthemes-element-pack'),
					'circle-3' => esc_html__('Style 21', 'bdthemes-element-pack'),
					'circle-4' => esc_html__('Style 22', 'bdthemes-element-pack'),
					'square-1' => esc_html__('Style 23', 'bdthemes-element-pack'),
				],
				'condition' => [
					'navigation' => ['arrows-fraction', 'both', 'arrows'],
				],
			]
		);

		$this->add_control(
			'hide_arrow_on_mobile',
			[
				'label'     => __( 'Hide Arrow on Mobile ?', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'navigation' => [ 'arrows-fraction', 'arrows', 'both' ],
				],				
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_carousel_settings',
			[
				'label' => __( 'Carousel Settings', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'skin',
			[
				'label'   => esc_html__( 'Layout', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'carousel',
				'options' => [
					'carousel'  => esc_html__( 'Carousel', 'bdthemes-element-pack' ),
					'coverflow' => esc_html__( 'Coverflow', 'bdthemes-element-pack' ),
				],
				'prefix_class' => 'bdt-carousel-style-',
				'render_type'  => 'template',
			]
		);

		$this->add_control(
            'coverflow_toggle',
            [
                'label' => __( 'Coverflow Effect', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::POPOVER_TOGGLE,
                'return_value' => 'yes',
				'condition' => [
					'skin' => 'coverflow'
				]
            ]
		);

		$this->start_popover();
		
		$this->add_control(
			'coverflow_rotate',
			[
				'label'   => esc_html__( 'Rotate', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 50,
				],
				'range' => [
					'px' => [
						'min'  => -360,
						'max'  => 360,
						'step' => 5,
					],
				],
				'condition' => [
                    'coverflow_toggle' => 'yes'
				],
				'render_type'  => 'template',
			]
		);

        $this->add_control(
			'coverflow_stretch',
			[
				'label' => __( 'Stretch', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min'  => 0,
						'step' => 10,
						'max'  => 100,
					],
				],
				'condition' => [
                    'coverflow_toggle' => 'yes'
				],
				'render_type'  => 'template',
			]
		);

        $this->add_control(
			'coverflow_modifier',
			[
				'label' => __( 'Modifier', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'min'  => 1,
						'step' => 1,
						'max'  => 10,
					],
				],
				'condition' => [
                    'coverflow_toggle' => 'yes'
				],
				'render_type'  => 'template',
			]
		);

		$this->add_control(
			'coverflow_depth',
			[
				'label' => __( 'Depth', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'default' => [
					'size' => 100,
				],
				'range' => [
					'px' => [
						'min'  => 0,
						'step' => 10,
						'max'  => 1000,
					],
				],
				'condition' => [
                    'coverflow_toggle' => 'yes'
				],
				'render_type'  => 'template',
			]
		);

		$this->end_popover();

		$this->add_control(
			'hr_05',
			[
				'type' => Controls_Manager::DIVIDER,
				'condition' => [
					'skin' => 'coverflow'
				]
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'   => __( 'Autoplay', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				
			]
		);

		$this->add_control(
			'autoplay_speed',
			[
				'label'     => esc_html__( 'Autoplay Speed (ms)', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'condition' => [
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'pauseonhover',
			[
				'label' => esc_html__( 'Pause on Hover', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'slides_to_scroll',
			[
				'type'      => Controls_Manager::SELECT,
				'label'     => esc_html__( 'Slides to Scroll', 'bdthemes-element-pack' ),
				'options'   => [
					1 => '1',
					2 => '2',
					3 => '3',
					4 => '4',
					5 => '5',
					6 => '6',
				],
				'default'   => '1',
			]
		);

		$this->add_control(
			'observer',
			[
				'label' => esc_html__( 'Observer', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'centered_slides',
			[
				'label'   => __( 'Center Slide', 'bdthemes-element-pack' ),
				'description'   => __( 'Use even items from Layout > Columns settings for better preview.', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'grab_cursor',
			[
				'label'   => __( 'Grab Cursor', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'loop',
			[
				'label'   => __( 'Loop', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				
			]
		);

		$this->add_control(
			'speed',
			[
				'label'   => __( 'Animation Speed (ms)', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 500,
				],
				'range' => [
					'px' => [
						'min'  => 1000,
						'max'  => 10000,
						'step' => 100,
					],
				],
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_tlms_cg_item_style',
			[
				'label' => esc_html__( 'Item', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tlms_cg_item_tabs' );

		$this->start_controls_tab( 
			'tlms_cg_item_tabs_normal',
			[
				'label' => __( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'tlms_cg_item_background',
			[
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tlms_cg_item_border',
				'selector' => '{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item',
			]
		);

		$this->add_responsive_control(
			'tlms_cg_item_radius',
			[
				'label' => __( 'Radius', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tlms_cg_item_shadow',
				'selector' => '{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item',
			]
		);
		
		$this->end_controls_tab();

		$this->start_controls_tab( 
			'tlms_cg_item_tabs_hover',
			[
				'label' => __( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'tlms_cg_item_hover_background',
			[
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tlms_cg_item_hover_border_color',
			[
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'tlms_cg_item_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'tlms_cg_item_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item:hover',
			]
		);

		$this->add_responsive_control(
			'tlms_cg_item_shadow_padding',
			[
				'label'       => __( 'Match Padding', 'bdthemes-element-pack' ),
				'description' => __( 'You have to add padding for matching overlaping hover shadow', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [
					'px' => [
						'min'  => 0,
						'step' => 1,
						'max'  => 50,
					]
				],
				'default' => [
					'size' => 10
				],
				'selectors' => [
					'{{WRAPPER}} .swiper-container' => 'padding: {{SIZE}}{{UNIT}}; margin: 0 -{{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_tlms_cg_header_style',
			[
				'label' => esc_html__( 'Header', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'tlms_cg_header_image_heading',
			[
				'label' => esc_html__( 'Image', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'tlms_cg_image_radius',
			[
				'label' => __( 'Radius', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item .bdt-tutor-course-header a, {WRAPPER}} .bdt-tutor-course.bdt-tutor-course-item .bdt-tutor-course-header a img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'tlms_cg_header_meta_heading',
			[
				'label' => esc_html__( 'Meta Label', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::HEADING,
				'separator'	=> 'before',
				'condition'	=> [
					'show_meta_label'	=> 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'tlms_cg_header_meta_label_tabs' );

		$this->start_controls_tab( 
			'tlms_cg_header_meta_label_normal',
			[
				'label' => __( 'Normal', 'bdthemes-element-pack' ),
				'condition'	=> [
					'show_meta_label'	=> 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_header_meta_label_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-level' => 'color: {{VALUE}};',
				],
				'condition'	=> [
					'show_meta_label'	=> 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_header_meta_label_background',
			[
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-level' => 'background: {{VALUE}};',
				],
				'condition'	=> [
					'show_meta_label'	=> 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tlms_cg_header_meta_label_border',
				'selector' => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-level',
				'condition'	=> [
					'show_meta_label'	=> 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'tlms_cg_header_meta_label_radius',
			[
				'label' => __( 'Radius', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-level' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'	=> [
					'show_meta_label'	=> 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_header_meta_label_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-level' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'	=> [
					'show_meta_label'	=> 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'tlms_cg_header_meta_label_typography',
				'label'     => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector'  => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-level',
				'condition'	=> [
					'show_meta_label'	=> 'yes',
				],
			]
		);
		
		$this->end_controls_tab();

		$this->start_controls_tab( 
			'tlms_cg_header_meta_label_hover',
			[
				'label' => __( 'Hover', 'bdthemes-element-pack' ),
				'condition'	=> [
					'show_meta_label'	=> 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_header_meta_label_hover_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-level:hover' => 'color: {{VALUE}};',
				],
				'condition'	=> [
					'show_meta_label'	=> 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_header_meta_label_hover_background',
			[
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-level:hover' => 'background: {{VALUE}};',
				],
				'condition'	=> [
					'show_meta_label'	=> 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_header_meta_label_hover_border_color',
			[
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-level:hover' => 'border-color: {{VALUE}};',
				],
				'condition'	=> [
					'show_meta_label'	=> 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'tlms_cg_header_meta_wishlist_heading',
			[
				'label' => esc_html__( 'Wishlist Meta', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::HEADING,
				'separator'	=> 'before',
				'condition'	=> [
					'show_meta_wishlist' => 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'tlms_cg_header_meta_wishlist_tabs' );

		$this->start_controls_tab( 
			'tlms_cg_header_meta_wishlist_normal',
			[
				'label' => __( 'Normal', 'bdthemes-element-pack' ),
				'condition'	=> [
					'show_meta_wishlist' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_header_meta_wishlist_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-header-meta .bdt-tutor-course-wishlist a' => 'color: {{VALUE}};',
				],
				'condition'	=> [
					'show_meta_wishlist' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_header_meta_wishlist_background',
			[
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-header-meta .bdt-tutor-course-wishlist' => 'background: {{VALUE}};',
				],
				'condition'	=> [
					'show_meta_wishlist' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tlms_cg_header_meta_wishlist_border',
				'selector' => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-header-meta .bdt-tutor-course-wishlist',
				'condition'	=> [
					'show_meta_wishlist' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'tlms_cg_header_meta_wishlist_radius',
			[
				'label' => __( 'Radius', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-header-meta .bdt-tutor-course-wishlist' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'	=> [
					'show_meta_wishlist' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_header_meta_wishlist_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-header-meta .bdt-tutor-course-wishlist' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'	=> [
					'show_meta_wishlist' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'tlms_cg_header_meta_wishlist_typography',
				'label'     => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector'  => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-header-meta .bdt-tutor-course-wishlist',
				'condition'	=> [
					'show_meta_wishlist' => 'yes',
				],
			]
		);
		
		$this->end_controls_tab();

		$this->start_controls_tab( 
			'tlms_cg_header_meta_wishlist_hover',
			[
				'label' => __( 'Hover', 'bdthemes-element-pack' ),
				'condition'	=> [
					'show_meta_wishlist' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_header_meta_wishlist_hover_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-header-meta .bdt-tutor-course-wishlist:hover a' => 'color: {{VALUE}};',
				],
				'condition'	=> [
					'show_meta_wishlist' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_header_meta_wishlist_hover_background',
			[
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-header-meta .bdt-tutor-course-wishlist:hover' => 'background: {{VALUE}};',
				],
				'condition'	=> [
					'show_meta_wishlist' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_header_meta_wishlist_hover_border_color',
			[
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-header-meta .bdt-tutor-course-wishlist:hover' => 'border-color: {{VALUE}};',
				],
				'conditions'   => [
					'relation'	=> 'or',
					'terms' => [
						[
							'name'  => 'tlms_cg_header_meta_wishlist_border_border!',
							'value' => '',
						],
						[
							'name'  => 'show_meta_wishlist',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_tlms_cg_content_area_style',
			[
				'label' => esc_html__( 'Content', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'conditions'   => [
					'relation'	=> 'or',
					'terms' => [
						[
							'name'  => 'show_rating',
							'value' => 'yes',
						],
						[
							'name'  => 'show_title',
							'value' => 'yes',
						],
						[
							'name'  => 'show_user_clock',
							'value' => 'yes',
						],
						[
							'name'     => 'show_category',
							'value'    => 'yes',
						],
						[
							'name'     => 'show_author_avatar',
							'value'    => 'yes',
						],
						[
							'name'     => 'show_author_name',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_background',
			[
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-course-container' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-course-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tlms_cg_content_tabs_style' );

		$this->start_controls_tab( 
			'tlms_cg_content_rating',
			[
				'label' => __( 'Rating', 'bdthemes-element-pack' ),
				'condition'	=> [
					'show_rating' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_rating_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .tutor-star-rating-group' => 'color: {{VALUE}};',
				],
				'condition'	=> [
					'show_rating' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_rating_icon_indent',
			[
				'label'   => esc_html__( 'Icon Spacing', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .tutor-star-rating-group i'  => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition'	=> [
					'show_rating' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'tlms_cg_content_rating_typography',
				'label'     => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector'  => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-rating-wrap',
				'condition'	=> [
					'show_rating' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_rating_spacing',
			[
				'label'   => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .tutor-star-rating-group'  => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'	=> [
					'show_rating' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 
			'tlms_cg_content_title',
			[
				'label' => __( 'Title', 'bdthemes-element-pack' ),
				'condition'	=> [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_title_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-title h2 a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_title_hover_color',
			[
				'label'     => __( 'Hover Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-title h2 a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'tlms_cg_content_title_typography',
				'label'     => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector'  => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-title h2',
			]
		);

		$this->add_control(
			'tlms_cg_content_title_spacing',
			[
				'label'   => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-title h2'  => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 
			'tlms_cg_content_meta',
			[
				'label' => __( 'User Meta', 'bdthemes-element-pack' ),
				'condition'	=> [
					'show_user_clock' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_meta_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-meta' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'tlms_cg_content_meta_typography',
				'label'     => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector'  => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-meta',
			]
		);

		$this->add_control(
			'tlms_cg_content_meta_spacing',
			[
				'label'   => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-meta'  => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 
			'tlms_cg_content_author',
			[
				'label' => __( 'Author', 'bdthemes-element-pack' ),
				'conditions'   => [
					'relation'	=> 'or',
					'terms' => [
						[
							'name'  => 'show_author_name',
							'value' => 'yes',
						],
						[
							'name'     => 'show_category',
							'value'    => 'yes',
						],
						[
							'name'     => 'show_author_avatar',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_author_heading',
			[
				'label' => esc_html__( 'Avatar', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::HEADING,
				'condition'	=> [
					'show_author_avatar' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_author_avatar_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author .bdt-tutor-single-course-avatar .tutor-text-avatar' => 'color: {{VALUE}} !important;',
				],
				'condition'	=> [
					'show_author_avatar' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_author_avatar_background',
			[
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author .bdt-tutor-single-course-avatar .tutor-text-avatar' => 'background-color: {{VALUE}} !important;',
				],
				'condition'	=> [
					'show_author_avatar' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_author_avatar_height_width',
			[
				'label'   => esc_html__( 'Width', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author .bdt-tutor-single-course-avatar .tutor-text-avatar'  => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
				'condition'	=> [
					'show_author_avatar' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'tlms_cg_content_author_avatar_radius',
			[
				'label' => __( 'Radius', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author .bdt-tutor-single-course-avatar .tutor-text-avatar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'	=> [
					'show_author_avatar' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'tlms_cg_content_author_avatar_typography',
				'label'     => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector'  => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author .bdt-tutor-single-course-avatar .tutor-text-avatar',
				'condition'	=> [
					'show_author_avatar' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_author_avatar_spacing',
			[
				'label'   => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author .bdt-tutor-single-course-avatar .tutor-text-avatar'  => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition'	=> [
					'show_author_avatar' => 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_category_heading',
			[
				'label' => esc_html__( 'Author Name / Category', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::HEADING,
				'separator'	=> 'before',
				'conditions'   => [
					'relation'	=> 'or',
					'terms' => [
						[
							'name'  => 'show_author_name',
							'value' => 'yes',
						],
						[
							'name'     => 'show_category',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_author_category_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author > div a' => 'color: {{VALUE}}',
				],
				'conditions'   => [
					'relation'	=> 'or',
					'terms' => [
						[
							'name'  => 'show_author_name',
							'value' => 'yes',
						],
						[
							'name'     => 'show_category',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_author_category_hover_color',
			[
				'label'     => __( 'Hover Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author > div a:hover' => 'color: {{VALUE}}',
				],
				'conditions'   => [
					'relation'	=> 'or',
					'terms' => [
						[
							'name'  => 'show_author_name',
							'value' => 'yes',
						],
						[
							'name'     => 'show_category',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'tlms_cg_content_category_sub_text_color',
			[
				'label'     => __( 'Sub Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author > div span' => 'color: {{VALUE}}',
				],
				'conditions'   => [
					'relation'	=> 'or',
					'terms' => [
						[
							'name'  => 'show_author_name',
							'value' => 'yes',
						],
						[
							'name'     => 'show_category',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'tlms_cg_content_category_typography',
				'label'     => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector'  => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-author',
				'conditions'   => [
					'relation'	=> 'or',
					'terms' => [
						[
							'name'  => 'show_author_name',
							'value' => 'yes',
						],
						[
							'name'     => 'show_category',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_tlms_cg_footer_area_style',
			[
				'label' => esc_html__( 'Footer', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'	=> [
					'show_cart_btn_price'	=> 'yes',
				],
			]
		);

		$this->add_control(
			'tlms_cg_footer_area_background',
			[
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-course-footer' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tlms_cg_footer_area_border',
				'selector' => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-course-footer',
			]
		);

		$this->add_control(
			'tlms_cg_footer_area_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-loop-course-footer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'tlms_cg_footer_price_typography',
				'label'     => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector'  => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price',
			]
		);

		$this->add_control(
			'tlms_cg_footer_price_heading',
			[
				'label' => esc_html__( 'Price', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::HEADING,
				'separator'	=> 'before'
			]
		);

		$this->add_control(
			'tlms_cg_footer_price_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price, {{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price .price *' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tlms_cg_footer_cart_heading',
			[
				'label' => esc_html__( 'Cart Button', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::HEADING,
				'separator'	=> 'before'
			]
		);

		$this->start_controls_tabs( 'tlms_cg_footer_cart_tabs' );

		$this->start_controls_tab( 
			'tlms_cg_footer_cart_normal',
			[
				'label' => __( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'tlms_cg_footer_cart_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price > .price .tutor-loop-cart-btn-wrap a, {{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price > .price .tutor-loop-cart-btn-wrap a::before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tlms_cg_footer_cart_background',
			[
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price > .price .tutor-loop-cart-btn-wrap a' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tlms_cg_footer_cart_border',
				'selector' => '{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price > .price .tutor-loop-cart-btn-wrap a',
			]
		);

		$this->add_responsive_control(
			'tlms_cg_footer_cart_radius',
			[
				'label' => __( 'Radius', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price > .price .tutor-loop-cart-btn-wrap a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'tlms_cg_footer_cart_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price > .price .tutor-loop-cart-btn-wrap a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 
			'tlms_cg_footer_cart_hover',
			[
				'label' => __( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'tlms_cg_footer_cart_hover_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price > .price .tutor-loop-cart-btn-wrap a:hover, {{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price > .price .tutor-loop-cart-btn-wrap a:hover:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tlms_cg_footer_cart_hover_background',
			[
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price > .price .tutor-loop-cart-btn-wrap a:hover' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tlms_cg_footer_cart_hover_border_color',
			[
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-course .bdt-tutor-course-loop-price > .price .tutor-loop-cart-btn-wrap a:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'tlms_cg_footer_cart_border_border!' => '',
				],
			]
		);
		
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

        $this->start_controls_section(
			'section_style_navigation',
			[
				'label'     => __( 'Navigation', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'  => 'navigation',
							'operator' => '!=',
							'value' => 'none',
						],
						[
							'name'     => 'show_scrollbar',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'arrows_heading',
			[
				'label'     => __( 'Arrows', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'navigation!' => [ 'dots', 'progressbar', 'none' ],
				],
			]
		);

		$this->start_controls_tabs( 'tabs_navigation_arrows_style' );

		$this->start_controls_tab(
			'tabs_nav_arrows_normal',
			[
				'label' => __( 'Normal', 'bdthemes-element-pack' ),
				'condition' => [
					'navigation!' => [ 'dots', 'progressbar', 'none' ],
				],
			]
		);

		$this->add_control(
			'arrows_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-lms-course-carousel .bdt-navigation-prev i, {{WRAPPER}} .bdt-tutor-lms-course-carousel .bdt-navigation-next i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'navigation!' => [ 'dots', 'progressbar', 'none' ],
				],
			]
		);

		$this->add_control(
			'arrows_background',
			[
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-lms-course-carousel .bdt-navigation-prev, {{WRAPPER}} .bdt-tutor-lms-course-carousel .bdt-navigation-next' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation!' => [ 'dots', 'progressbar', 'none' ],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'nav_arrows_border',
				'selector'    => '{{WRAPPER}} .bdt-tutor-lms-course-carousel .bdt-navigation-prev, {{WRAPPER}} .bdt-tutor-lms-course-carousel .bdt-navigation-next',
				'condition' => [
					'navigation!' => [ 'dots', 'progressbar', 'none' ],
				],
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-tutor-lms-course-carousel .bdt-navigation-prev, {{WRAPPER}} .bdt-tutor-lms-course-carousel .bdt-navigation-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => [ 'dots', 'progressbar', 'none' ],
				],
			]
		);

		$this->add_responsive_control(
			'arrows_padding',
			[
				'label' => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-lms-course-carousel .bdt-navigation-prev, {{WRAPPER}} .bdt-tutor-lms-course-carousel .bdt-navigation-next' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => [ 'dots', 'progressbar', 'none' ],
				],
			]
		);

		$this->add_control(
			'arrows_size',
			[
				'label' => __( 'Size', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-lms-course-carousel .bdt-navigation-prev i,
					{{WRAPPER}} .bdt-tutor-lms-course-carousel .bdt-navigation-next i' => 'font-size: {{SIZE || 24}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => [ 'dots', 'progressbar', 'none' ],
				],
			]
		);

		$this->add_control(
			'arrows_space',
			[
				'label' => __( 'Space Between Arrows', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-lms-course-carousel .bdt-navigation-prev' => 'margin-right: {{SIZE}}px;',
					'{{WRAPPER}} .bdt-tutor-lms-course-carousel .bdt-navigation-next' => 'margin-left: {{SIZE}}px;',
				],
				'condition' => [
					'navigation!' => [ 'dots', 'progressbar', 'none' ],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tabs_nav_arrows_hover',
			[
				'label' => __( 'Hover', 'bdthemes-element-pack' ),
				'condition' => [
					'navigation!' => [ 'dots', 'progressbar', 'none' ],
				],
			]
		);

		$this->add_control(
			'arrows_hover_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-lms-course-carousel .bdt-navigation-prev:hover i, {{WRAPPER}} .bdt-tutor-lms-course-carousel .bdt-navigation-next:hover i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'navigation!' => [ 'dots', 'progressbar', 'none' ],
				],
			]
		);

		$this->add_control(
			'arrows_hover_background',
			[
				'label'     => __( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-lms-course-carousel .bdt-navigation-prev:hover, {{WRAPPER}} .bdt-tutor-lms-course-carousel .bdt-navigation-next:hover' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation!' => [ 'dots', 'progressbar', 'none' ],
				],
			]
		);

		$this->add_control(
			'nav_arrows_hover_border_color',
			[
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-lms-course-carousel .bdt-navigation-prev:hover, {{WRAPPER}} .bdt-tutor-lms-course-carousel .bdt-navigation-next:hover'  => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'nav_arrows_border_border!' => '',
					'navigation!' => [ 'dots', 'progressbar', 'none' ],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'hr_1',
			[
				'type' => Controls_Manager::DIVIDER,
				'condition' => [
					'navigation!' => [ 'arrows', 'arrows-fraction', 'progressbar', 'none' ],
				],
			]
		);

		$this->add_control(
			'dots_heading',
			[
				'label'     => __( 'Dots', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'navigation!' => [ 'arrows', 'arrows-fraction', 'progressbar', 'none' ],
				],
			]
		);

		$this->add_control(
			'hr_11',
			[
				'type' => Controls_Manager::DIVIDER,
				'condition' => [
					'navigation!' => [ 'arrows', 'arrows-fraction', 'progressbar', 'none' ],
				],
			]
		);

		$this->add_control(
			'dots_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-lms-course-carousel .swiper-pagination-bullet' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation!' => [ 'arrows', 'arrows-fraction', 'progressbar', 'none' ],
				],
			]
		);

		$this->add_control(
			'active_dot_color',
			[
				'label'     => __( 'Active Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-lms-course-carousel .swiper-pagination-bullet-active' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation!' => [ 'arrows', 'arrows-fraction', 'progressbar', 'none' ],
				],
			]
		);

		$this->add_control(
			'dots_size',
			[
				'label' => __( 'Size', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-lms-course-carousel .swiper-pagination-bullet' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => [ 'arrows', 'arrows-fraction', 'progressbar', 'none' ],
				],
			]
		);

		$this->add_control(
			'hr_2',
			[
				'type' => Controls_Manager::DIVIDER,
				'condition' => [
					'navigation' => 'arrows-fraction',
				],
			]
		);

		$this->add_control(
			'fraction_heading',
			[
				'label'     => __( 'Fraction', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'navigation' => 'arrows-fraction',
				],
			]
		);

		$this->add_control(
			'hr_12',
			[
				'type' => Controls_Manager::DIVIDER,
				'condition' => [
					'navigation' => 'arrows-fraction',
				],
			]
		);

		$this->add_control(
			'fraction_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-lms-course-carousel .swiper-pagination-fraction' => 'color: {{VALUE}}',
				],
				'condition' => [
					'navigation!' => [ 'arrows', 'dots', 'progressbar', 'none' ],
				],
			]
		);

		$this->add_control(
			'active_fraction_color',
			[
				'label'     => __( 'Active Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-lms-course-carousel .swiper-pagination-current' => 'color: {{VALUE}}',
				],
				'condition' => [
					'navigation!' => [ 'arrows', 'dots', 'progressbar', 'none' ],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'fraction_typography',
				'label'     => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				//'scheme'    => Schemes\Typography::TYPOGRAPHY_4,
				'selector'  => '{{WRAPPER}} .bdt-tutor-lms-course-carousel .swiper-pagination-fraction',
				'condition' => [
					'navigation!' => [ 'arrows', 'dots', 'progressbar', 'none' ],
				],
			]
		);

		$this->add_control(
			'hr_3',
			[
				'type' => Controls_Manager::DIVIDER,
				'condition' => [
					'navigation' => 'progressbar',
				],
			]
		);

		$this->add_control(
			'progresbar_heading',
			[
				'label'     => __( 'Progresbar', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'navigation' => 'progressbar',
				],
			]
		);

		$this->add_control(
			'hr_13',
			[
				'type' => Controls_Manager::DIVIDER,
				'condition' => [
					'navigation' => 'progressbar',
				],
			]
		);

		$this->add_control(
			'progresbar_color',
			[
				'label'     => __( 'Bar Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-lms-course-carousel .swiper-pagination-progressbar' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => 'progressbar',
				],
			]
		);

		$this->add_control(
			'progres_color',
			[
				'label'     => __( 'Progress Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-lms-course-carousel .swiper-pagination-progressbar .swiper-pagination-progressbar-fill' => 'background: {{VALUE}}',
				],
				'condition' => [
					'navigation' => 'progressbar',
				],
			]
		);

		$this->add_control(
			'hr_4',
			[
				'type' => Controls_Manager::DIVIDER,
				'condition'   => [
					'show_scrollbar' => 'yes'
				],
			]
		);

		$this->add_control(
			'scrollbar_heading',
			[
				'label'     => __( 'Scrollbar', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'condition'   => [
					'show_scrollbar' => 'yes'
				],
			]
		);

		$this->add_control(
			'hr_14',
			[
				'type' => Controls_Manager::DIVIDER,
				'condition'   => [
					'show_scrollbar' => 'yes'
				],
			]
		);

		$this->add_control(
			'scrollbar_color',
			[
				'label'     => __( 'Bar Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-lms-course-carousel .swiper-scrollbar' => 'background: {{VALUE}}',
				],
				'condition'   => [
					'show_scrollbar' => 'yes'
				],
			]
		);

		$this->add_control(
			'scrollbar_drag_color',
			[
				'label'     => __( 'Drag Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-lms-course-carousel .swiper-scrollbar .swiper-scrollbar-drag' => 'background: {{VALUE}}',
				],
				'condition'   => [
					'show_scrollbar' => 'yes'
				],
			]
		);

		$this->add_control(
			'scrollbar_height',
			[
				'label'   => __( 'Height', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-lms-course-carousel .swiper-container-horizontal > .swiper-scrollbar' => 'height: {{SIZE}}px;',
				],
				'condition'   => [
					'show_scrollbar' => 'yes'
				],
			]
		);

		$this->add_control(
			'hr_5',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_control(
			'navi_offset_heading',
			[
				'label'     => __( 'Offset', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'hr_6',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_responsive_control(
			'arrows_ncx_position',
			[
				'label'   => __( 'Arrows Horizontal Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'arrows',
						],
						[
							'name'     => 'arrows_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-tutor-lms-arrows-ncx: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'arrows_ncy_position',
			[
				'label'   => __( 'Arrows Vertical Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 40,
				],
				'tablet_default' => [
					'size' => 40,
				],
				'mobile_default' => [
					'size' => 40,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-tutor-lms-arrows-ncy: {{SIZE}}px;'
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'arrows',
						],
						[
							'name'     => 'arrows_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'arrows_acx_position',
			[
				'label'   => __( 'Arrows Horizontal Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => -60,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-lms-course-carousel .bdt-navigation-prev' => 'left: {{SIZE}}px;',
					'{{WRAPPER}} .bdt-tutor-lms-course-carousel .bdt-navigation-next' => 'right: {{SIZE}}px;',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'arrows',
						],
						[
							'name'  => 'arrows_position',
							'value' => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'dots_nnx_position',
			[
				'label'   => __( 'Dots Horizontal Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'dots',
						],
						[
							'name'     => 'dots_position',
							'operator' => '!=',
							'value'    => '',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-tutor-lms-dots-nnx: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'dots_nny_position',
			[
				'label'   => __( 'Dots Vertical Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 30,
				],
				'tablet_default' => [
					'size' => 30,
				],
				'mobile_default' => [
					'size' => 30,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'dots',
						],
						[
							'name'     => 'dots_position',
							'operator' => '!=',
							'value'    => '',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-tutor-lms-dots-nny: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'both_ncx_position',
			[
				'label'   => __( 'Arrows & Dots Horizontal Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'both',
						],
						[
							'name'     => 'both_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-tutor-lms-both-ncx: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'both_ncy_position',
			[
				'label'   => __( 'Arrows & Dots Vertical Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 40,
				],
				'tablet_default' => [
					'size' => 40,
				],
				'mobile_default' => [
					'size' => 40,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'both',
						],
						[
							'name'     => 'both_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-tutor-lms-both-ncy: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'both_cx_position',
			[
				'label'   => __( 'Arrows Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => -60,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-lms-course-carousel .bdt-navigation-prev' => 'left: {{SIZE}}px;',
					'{{WRAPPER}} .bdt-tutor-lms-course-carousel .bdt-navigation-next' => 'right: {{SIZE}}px;',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'both',
						],
						[
							'name'  => 'both_position',
							'value' => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'both_cy_position',
			[
				'label'   => __( 'Dots Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 30,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-lms-course-carousel .bdt-dots-container' => 'transform: translateY({{SIZE}}px);',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'both',
						],
						[
							'name'  => 'both_position',
							'value' => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'arrows_fraction_ncx_position',
			[
				'label'   => __( 'Arrows & Fraction Horizontal Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'arrows-fraction',
						],
						[
							'name'     => 'arrows_fraction_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-tutor-lms-arrows-fraction-ncx: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'arrows_fraction_ncy_position',
			[
				'label'   => __( 'Arrows & Fraction Vertical Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 40,
				],
				'tablet_default' => [
					'size' => 40,
				],
				'mobile_default' => [
					'size' => 40,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'arrows-fraction',
						],
						[
							'name'     => 'arrows_fraction_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-tutor-lms-arrows-fraction-ncy: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'arrows_fraction_cx_position',
			[
				'label'   => __( 'Arrows Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => -60,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-lms-course-carousel .bdt-navigation-prev' => 'left: {{SIZE}}px;',
					'{{WRAPPER}} .bdt-tutor-lms-course-carousel .bdt-navigation-next' => 'right: {{SIZE}}px;',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'arrows-fraction',
						],
						[
							'name'  => 'arrows_fraction_position',
							'value' => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'arrows_fraction_cy_position',
			[
				'label'   => __( 'Fraction Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 30,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-lms-course-carousel .swiper-pagination-fraction' => 'transform: translateY({{SIZE}}px);',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'arrows-fraction',
						],
						[
							'name'  => 'arrows_fraction_position',
							'value' => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'progress_y_position',
			[
				'label'   => __( 'Progress Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 15,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-lms-course-carousel .swiper-pagination-progressbar' => 'transform: translateY({{SIZE}}px);',
				],
				'condition' => [
					'navigation' => 'progressbar',
				],
			]
		);

		$this->add_responsive_control(
			'scrollbar_vertical_offset',
			[
				'label'   => __( 'Scrollbar Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-tutor-lms-course-carousel .swiper-container-horizontal > .swiper-scrollbar' => 'bottom: {{SIZE}}px;',
				],
				'condition'   => [
					'show_scrollbar' => 'yes'
				],
			]
		);

		$this->end_controls_section();
	}

	public function get_taxonomies() {
		$taxonomies = get_taxonomies( [ 'show_in_nav_menus' => true ], 'objects' );

		$options = [ '' => '' ];

		foreach ( $taxonomies as $taxonomy ) {
			$options[ $taxonomy->name ] = $taxonomy->label;
		}

		return $options;
	}

	public function query_posts() {
		$settings = $this->get_settings_for_display();

		if ( get_query_var('paged') ) { $paged = get_query_var('paged'); } 
		elseif ( get_query_var('page') ) { $paged = get_query_var('page'); } 
		else { $paged = 1; }

		$args = array(
			'post_type'      => 'courses',
			'posts_per_page' => $settings['limit'],
			'orderby'        => $settings['orderby'],
			'order'          => $settings['order'],
			'post_status'    => 'publish',
			'paged'          => $paged
		);

		if ( 'by_name' === $settings['source'] and !empty($settings['post_categories']) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'course-category',
				'field'    => 'slug',
				'terms'    => $settings['post_categories'],
			);
		}

		$query = new \WP_Query( $args );

		return $query;
	}

	public function render() {
		$settings = $this->get_settings_for_display();

		$wp_query = $this->query_posts();

		if ( ! $wp_query->found_posts ) {
			return;
		}

		$this->render_header();

		while ( $wp_query->have_posts() ) {
			$wp_query->the_post();

			$this->render_post();
		}

		$this->render_footer();
		
		wp_reset_postdata();

	}
    
    public function render_header() {
		$settings = $this->get_settings_for_display();
		$id       = 'bdt-tutor-lms-course-carousel' . $this->get_id();

		$elementor_vp_lg = get_option( 'elementor_viewport_lg' );
		$elementor_vp_md = get_option( 'elementor_viewport_md' );
		$viewport_lg     = ! empty($elementor_vp_lg) ? $elementor_vp_lg - 1 : 1023;
		$viewport_md     = ! empty($elementor_vp_md) ? $elementor_vp_md - 1 : 767;

		$this->add_render_attribute('courses', 'id', esc_attr($id) );

        $this->add_render_attribute('courses', 'class', ['bdt-tutor-lms-course-carousel']);

		if ('arrows' == $settings['navigation']) {
			$this->add_render_attribute( 'courses', 'class', 'bdt-arrows-align-'. $settings['arrows_position'] );
		} elseif ('dots' == $settings['navigation']) {
			$this->add_render_attribute( 'courses', 'class', 'bdt-dots-align-'. $settings['dots_position'] );
		} elseif ('both' == $settings['navigation']) {
			$this->add_render_attribute( 'courses', 'class', 'bdt-arrows-dots-align-'. $settings['both_position'] );
		} elseif ('arrows-fraction' == $settings['navigation']) {
			$this->add_render_attribute( 'courses', 'class', 'bdt-arrows-dots-align-'. $settings['arrows_fraction_position'] );
		}

		if ('arrows-fraction' == $settings['navigation'] ) {
			$pagination_type = 'fraction';
		} elseif ('both' == $settings['navigation'] or 'dots' == $settings['navigation']) {
			$pagination_type = 'bullets';
		} elseif ('progressbar' == $settings['navigation'] ) {
			$pagination_type = 'progressbar';
		} else {
			$pagination_type = '';
		}

		$this->add_render_attribute(
			[
				'courses' => [
					'data-settings' => [
						wp_json_encode(array_filter([
							"autoplay"       => ( "yes" == $settings["autoplay"] ) ? [ "delay" => $settings["autoplay_speed"] ] : false,
							"loop"           => ($settings["loop"] == "yes") ? true : false,
							"speed"          => $settings["speed"]["size"],
							"pauseOnHover"   => ("yes" == $settings["pauseonhover"]) ? true : false,
							"slidesPerView"  => (int) $settings["columns_mobile"],
							"slidesPerGroup" => $settings["slides_to_scroll"],
							"spaceBetween"   => $settings["item_gap"]["size"],
							"centeredSlides" => ($settings["centered_slides"] === "yes") ? true : false,
							"grabCursor"     => ($settings["grab_cursor"] === "yes") ? true : false,
							"effect"         => $settings["skin"],
							"observer"       => ($settings["observer"]) ? true : false,
							"observeParents" => ($settings["observer"]) ? true : false,
							"breakpoints"    => [
								(int) $viewport_md => [
									"slidesPerView" => (int) $settings["columns_tablet"],
									"spaceBetween"  => $settings["item_gap"]["size"],
									"slidesPerGroup" => $settings["slides_to_scroll"],
								],
								(int) $viewport_lg => [
                                    "slidesPerView" => (int) $settings["columns"],
									"spaceBetween"  => $settings["item_gap"]["size"],
									"slidesPerGroup" => $settings["slides_to_scroll"],
                                ]
					      	],
			      	        "navigation" => [
			      				"nextEl" => "#" . $id . " .bdt-navigation-next",
			      				"prevEl" => "#" . $id . " .bdt-navigation-prev",
			      			],
			      			"pagination" => [
								"el"             => "#" . $id . " .swiper-pagination",
								"type"           => $pagination_type,
								"clickable"      => "true",
								'dynamicBullets' => ("yes" == $settings["dynamic_bullets"]) ? true : false,
							],
							"scrollbar" => [
								"el"            => "#" . $id . " .swiper-scrollbar",
								"hide"          => "true",
							],
							'coverflowEffect' => [
								'rotate'       => ( "yes" == $settings["coverflow_toggle"] ) ? $settings["coverflow_rotate"]["size"] : 50,
								'stretch'      => ( "yes" == $settings["coverflow_toggle"] ) ? $settings["coverflow_stretch"]["size"] : 0,
								'depth'        => ( "yes" == $settings["coverflow_toggle"] ) ? $settings["coverflow_depth"]["size"] : 100,
								'modifier'     => ( "yes" == $settings["coverflow_toggle"] ) ? $settings["coverflow_modifier"]["size"] : 1,
								'slideShadows' => true,
							],

				        ]))
					]
				]
			]
		);
		
		$this->add_render_attribute( 'tutor-courses', 'class', 'swiper-container' );

        $this->add_render_attribute('tutor-courses-wrapper', 'class', 'swiper-wrapper');
        
		?>
		<div <?php echo $this->get_render_attribute_string( 'courses' ); ?>>
			<div <?php echo $this->get_render_attribute_string( 'tutor-courses' ); ?>>
				<div <?php echo $this->get_render_attribute_string( 'tutor-courses-wrapper' ); ?>>
		<?php
	}

	public function render_footer() {
		$settings = $this->get_settings_for_display();
		
		?>
				</div>
				<?php if ( 'yes' === $settings['show_scrollbar'] ) : ?>
				<div class="swiper-scrollbar"></div>
				<?php endif; ?>
			</div>
			
			<?php if ('both' == $settings['navigation']) : ?>
				<?php $this->render_both_navigation(); ?>
				<?php if ( 'center' === $settings['both_position'] ) : ?>
					<div class="bdt-position-z-index bdt-position-bottom">
						<div class="bdt-dots-container">
							<div class="swiper-pagination"></div>
						</div>
					</div>
				<?php endif; ?>
			<?php elseif ('arrows-fraction' == $settings['navigation']) : ?>
				<?php $this->render_arrows_fraction(); ?>
				<?php if ( 'center' === $settings['arrows_fraction_position'] ) : ?>
					<div class="bdt-dots-container">
						<div class="swiper-pagination"></div>
					</div>
				<?php endif; ?>
			<?php else : ?>			
				<?php $this->render_pagination(); ?>
				<?php $this->render_navigation(); ?>
			<?php endif; ?>
			
		</div>

		<?php
	}

    public function render_navigation() {
		$settings = $this->get_settings_for_display();
		$hide_arrow_on_mobile = $settings['hide_arrow_on_mobile'] ? ' bdt-visible@m' : '';
		
		if ( 'arrows' == $settings['navigation'] ) : ?>
			<div class="bdt-position-z-index bdt-position-<?php echo esc_attr( $settings['arrows_position'] . $hide_arrow_on_mobile ); ?>">
				<div class="bdt-arrows-container bdt-slidenav-container">
					<a href="" class="bdt-navigation-prev bdt-slidenav-previous bdt-icon bdt-slidenav">
						<i class="ep-arrow-left-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
					</a>
					<a href="" class="bdt-navigation-next bdt-slidenav-next bdt-icon bdt-slidenav">
						<i class="ep-arrow-right-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
					</a>
				</div>
			</div>
		<?php endif;
	}

	public function render_pagination() {
		$settings = $this->get_settings_for_display();
		
		if ( 'dots' == $settings['navigation'] or 'arrows-fraction' == $settings['navigation'] ) : ?>
			<div class="bdt-position-z-index bdt-position-<?php echo esc_attr($settings['dots_position']); ?>">
				<div class="bdt-dots-container">
					<div class="swiper-pagination"></div>
				</div>
			</div>

		<?php elseif ( 'progressbar' == $settings['navigation'] ) : ?>
			<div class="swiper-pagination bdt-position-z-index bdt-position-<?php echo esc_attr($settings['progress_position']); ?>"></div>
		<?php endif;
	}

	public function render_both_navigation() {
		$settings = $this->get_settings_for_display();
		$hide_arrow_on_mobile = $settings['hide_arrow_on_mobile'] ? 'bdt-visible@m' : '';
		
		?>
		<div class="bdt-position-z-index bdt-position-<?php echo esc_attr($settings['both_position']); ?>">
			<div class="bdt-arrows-dots-container bdt-slidenav-container ">
				
				<div class="bdt-flex bdt-flex-middle">
					<div class="<?php echo esc_attr( $hide_arrow_on_mobile ); ?>">
						<a href="" class="bdt-navigation-prev bdt-slidenav-previous bdt-icon bdt-slidenav">
							<i class="ep-arrow-left-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
						</a>
					</div>

					<?php if ('center' !== $settings['both_position']) : ?>
						<div class="swiper-pagination"></div>
					<?php endif; ?>
					
					<div class="<?php echo esc_attr( $hide_arrow_on_mobile ); ?>">
						<a href="" class="bdt-navigation-next bdt-slidenav-next bdt-icon bdt-slidenav">
							<i class="ep-arrow-right-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
						</a>
					</div>
					
				</div>
			</div>
		</div>		
		<?php
	}

	public function render_arrows_fraction() {
		$settings             = $this->get_settings_for_display();
		$hide_arrow_on_mobile = $settings['hide_arrow_on_mobile'] ? 'bdt-visible@m' : '';
		
		?>
		<div class="bdt-position-z-index bdt-position-<?php echo esc_attr($settings['arrows_fraction_position']); ?>">
			<div class="bdt-arrows-fraction-container bdt-slidenav-container ">
				
				<div class="bdt-flex bdt-flex-middle">
					<div class="<?php echo esc_attr( $hide_arrow_on_mobile ); ?>">
						<a href="" class="bdt-navigation-prev bdt-slidenav-previous bdt-icon bdt-slidenav">
							<i class="ep-arrow-left-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
						</a>
					</div>

					<?php if ('center' !== $settings['arrows_fraction_position']) : ?>
						<div class="swiper-pagination"></div>
					<?php endif; ?>
					
					<div class="<?php echo esc_attr( $hide_arrow_on_mobile ); ?>">
						<a href="" class="bdt-navigation-next bdt-slidenav-next bdt-icon bdt-slidenav">
							<i class="ep-arrow-right-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
						</a>
					</div>
					
				</div>
			</div>
		</div>		
		<?php
	}

	public function render_thumbnail() {
		$settings = $this->get_settings_for_display();

		$course_id = get_the_ID();

		$settings['thumbnail_size'] = [
			'id' => get_post_thumbnail_id(),
		];

		$thumbnail_html      = Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail_size' );
		$placeholder_img_src = Utils::get_placeholder_image_src();
		$img_url             = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );

		if ( ! $thumbnail_html ) {
			$thumbnail_html = '<img src="' . esc_url( $placeholder_img_src) . '" alt="' . get_the_title() . '">';
		}

		?>

		<div class="bdt-tutor-course-header">
			<a href="<?php the_permalink(); ?>"> <?php echo $thumbnail_html ?>  </a>
			<div class="bdt-tutor-course-loop-header-meta">
				<?php
				$is_wishlisted = tutor_utils()->is_wishlisted($course_id);
				$has_wish_list = '';
				if ($is_wishlisted){
					$has_wish_list = 'has-wish-listed';
				}

				if ( 'yes' == $settings['show_meta_label'] ) {
					echo '<span class="bdt-tutor-course-loop-level">'.get_tutor_course_level().'</span>';
				}

				if ( 'yes' == $settings['show_meta_wishlist'] ) {
					echo '<span class="bdt-tutor-course-wishlist"><a href="javascript:;" class="tutor-icon-fav-line tutor-course-wishlist-btn '.$has_wish_list.' " data-course-id="'.$course_id.'"></a> </span>';
				}

				?>
			</div>
		</div>


		<?php
	}

	public function render_title() {
		$settings = $this->get_settings_for_display();

		if ( ! $settings['show_title'] ) {
			return;
		}

		?>
		<div class="bdt-tutor-course-loop-title">
			<h2>
				<a href="<?php echo get_the_permalink(); ?>">
					<?php the_title() ?>
				</a>
			</h2>
		</div>

		<?php
	}

	public function render_meta() {
		$settings = $this->get_settings_for_display();

		global $post, $authordata;
		$profile_url = tutor_utils()->profile_url($authordata->ID);
		?>

		<?php if ( 'yes' == $settings['show_user_clock'] ) : ?>
		<div class="bdt-tutor-course-loop-meta">
			<?php
			$course_duration = get_tutor_course_duration_context();
			$course_students = tutor_utils()->count_enrolled_users_by_course();
			?>
			<div class="bdt-tutor-single-loop-meta">
				<i class='tutor-icon-user'></i><span><?php echo $course_students; ?></span>
			</div>
			<?php
			if(!empty($course_duration)) { ?>
				<div class="bdt-tutor-single-loop-meta">
					<i class='tutor-icon-clock'></i> <span><?php echo $course_duration; ?></span>
				</div>
			<?php } ?>
		</div>
		<?php endif; ?>

		<div class="bdt-tutor-loop-author">

			<?php if ( 'yes' == $settings['show_author_avatar'] ) : ?>
				<div class="bdt-tutor-single-course-avatar">
					<a href="<?php echo $profile_url; ?>"> <?php echo tutor_utils()->get_tutor_avatar($post->post_author); ?></a>
				</div>
			<?php endif; ?>

			<?php if ( 'yes' == $settings['show_author_name'] ) : ?>
				<div class="bdt-tutor-single-course-author-name">
					<span><?php _e('by', 'tutor'); ?></span>
					<a href="<?php echo $profile_url; ?>"><?php echo get_the_author(); ?></a>
				</div>
			<?php endif; ?>
			
			<?php if ( 'yes' == $settings['show_category'] ) : ?>
				<div class="bdt-tutor-course-lising-category">
					<?php
					$course_categories = get_tutor_course_categories();
					if(!empty($course_categories) && is_array($course_categories ) && count($course_categories)){
						?>
						<span><?php esc_html_e('In', 'tutor') ?></span>
						<?php
						foreach ($course_categories as $course_category){
							$category_name = $course_category->name;
							$category_link = get_term_link($course_category->term_id);
							echo "<a href='$category_link'>$category_name </a>";
						}
					}
					?>
				</div>
			<?php endif; ?>

		</div>

		<?php
	}

	public function render_rating() {
		$settings = $this->get_settings_for_display();

		if ( ! $settings['show_rating'] ) {
			return;
		}

		?>

		<div class="bdt-tutor-loop-rating-wrap">
			<?php
			$course_rating = tutor_utils()->get_course_rating();
			tutor_utils()->star_rating_generator($course_rating->rating_avg);
			?>
			<span class="bdt-tutor-rating-count">
				<?php
				if ($course_rating->rating_avg > 0) {
					echo apply_filters('tutor_course_rating_average', $course_rating->rating_avg);
					echo '<i>(' . apply_filters('tutor_course_rating_count', $course_rating->rating_count) . ')</i>';
				}
				?>
			</span>
		</div>

		<?php
	}

	public function render_price() {
		$settings = $this->get_settings_for_display();

		if ( ! $settings['show_cart_btn_price'] ) {
			return;
		}

		?>
		<div class="bdt-tutor-loop-course-footer">
			<div class="bdt-tutor-course-loop-price">
				<?php
				$course_id = get_the_ID();
				$enroll_btn = '<div  class="tutor-loop-cart-btn-wrap"><a href="'. get_the_permalink(). '">'.__('Get Enrolled', 'tutor'). '</a></div>';
				$price_html = '<div class="price"> '.__('Free', 'tutor').$enroll_btn. '</div>';
				if (tutor_utils()->is_course_purchasable()) {
					$enroll_btn = tutor_course_loop_add_to_cart(false);

					$product_id = tutor_utils()->get_course_product_id($course_id);
					$product    = wc_get_product( $product_id );

					if ( $product ) {
						$price_html = '<div class="price"> '.$product->get_price_html().$enroll_btn.' </div>';
					}
				}

				echo $price_html;
				?>
			</div>
		</div>

		<?php
	}

	public function render_desc() {
		?>
		<div class="bdt-tutor-loop-course-container">
			<?php
			$this->render_rating();
			$this->render_title();
			$this->render_meta();
			?>
		</div>
		
		<?php $this->render_price(); ?>
		<?php
	}
	
	public function render_post() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('tutor-course-item', 'class', 'bdt-tutor-course bdt-tutor-course-item swiper-slide', true);
		
		?>
			<div <?php echo $this->get_render_attribute_string( 'tutor-course-item' ); ?>>
				<?php $this->render_thumbnail(); ?>
				<?php $this->render_desc(); ?>
			</div>
		<?php
	}
}
