<?php
namespace ElementPack\Modules\LogoGrid\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Group_Control_Css_Filter;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Logo_Grid extends Module_Base {

	public function get_name() {
		return 'bdt-logo-grid';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Logo Grid', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-logo-grid';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'logo', 'grid', 'client', 'brand', 'showcase' ];
	}

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-all-styles'];
        } else {
            return [ 'ep-logo-grid' ];
        }
    }

    public function get_script_depends() {
        return [ 'popper', 'tippyjs' ];
    }

	// public function get_custom_help_url() {
	// 	return '';
	// }

    protected function _register_controls() {

        $this->start_controls_section(
            'ep_section_logo',
            [
                'label' => __( 'Logo Grid Items', 'bdthemes-element-pack' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();

		$repeater->add_control(
			'image',
			[
				'label'       => __( 'Logo Image', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::MEDIA,
				'default'     => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

        $repeater->add_control(
            'link',
            [
                'label' => __('Website Url', 'bdthemes-element-pack'),
                'type' => Controls_Manager::URL,
                'show_external' => false,
                'label_block' => false,
            ]
        );

        $repeater->add_control(
            'name',
            [
                'label' => __('Brand Name', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Brand Name', 'bdthemes-element-pack'),
            ]
        );

        $repeater->add_control(
            'description',
            [
                'label' => __('Description', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => __('Brand Short Description Type Here.', 'bdthemes-element-pack'),
            ]
        );

        $repeater->add_control(
            'logo_tooltip',
            [
                'label'   => __( 'Tooltip', 'bdthemes-element-pack' ),
                'type'    => Controls_Manager::SWITCHER,
            ]
        );

        $repeater->add_control(
            'logo_tooltip_placement',						
            [
                'label'   => esc_html__( 'Placement', 'bdthemes-element-pack' ),
                'type'    => Controls_Manager::SELECT,
                'default' => 'top',
                'options' => [
                    'top-start'    => esc_html__( 'Top Left', 'bdthemes-element-pack' ),
                    'top'          => esc_html__( 'Top', 'bdthemes-element-pack' ),
                    'top-end'      => esc_html__( 'Top Right', 'bdthemes-element-pack' ),
                    'bottom-start' => esc_html__( 'Bottom Left', 'bdthemes-element-pack' ),
                    'bottom'       => esc_html__( 'Bottom', 'bdthemes-element-pack' ),
                    'bottom-end'   => esc_html__( 'Bottom Right', 'bdthemes-element-pack' ),
                    'left'         => esc_html__( 'Left', 'bdthemes-element-pack' ),
                    'right'        => esc_html__( 'Right', 'bdthemes-element-pack' ),
                ],
                'condition'   => [
                    'logo_tooltip' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'logo_list',
            [
                'show_label' => false,
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ name }}}',
                'default' => [
                    ['image' => ['url' => Utils::get_placeholder_image_src()]],
                    ['image' => ['url' => Utils::get_placeholder_image_src()]],
                    ['image' => ['url' => Utils::get_placeholder_image_src()]],
                    ['image' => ['url' => Utils::get_placeholder_image_src()]],
                    ['image' => ['url' => Utils::get_placeholder_image_src()]],
                    ['image' => ['url' => Utils::get_placeholder_image_src()]],
                    ['image' => ['url' => Utils::get_placeholder_image_src()]],
                    ['image' => ['url' => Utils::get_placeholder_image_src()]],
                ]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'ep_section_layout',
            [
                'label' => __( 'Additional Settings', 'bdthemes-element-pack' ),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'layout',
            [
                'label' => __( 'Grid Layout', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'box' => __( 'Box', 'bdthemes-element-pack' ),
                    'border' => __( 'Border', 'bdthemes-element-pack' ),
                    'tictactoe' => __( 'Plus', 'bdthemes-element-pack' ),
                ],
                'default' => 'box',
                'prefix_class' => 'bdt-logo-grid--',
                'style_transfer' => true,
                'render_type'  => 'template',
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label' => __( 'Columns', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    2 => '2',
                    3 => '3',
                    4 => '4',
                    5 => '5',
                    6 => '6',
                ],
                'desktop_default' => 4,
                'tablet_default'  => 2,
                'mobile_default'  => 2,
                'prefix_class'    => 'bdt-logo-grid--col-%s',
                'style_transfer'  => true,
                'render_type'     => 'template',
            ]
        );

        $this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'thumbnail',
                'default' => 'large',
                'separator' => 'before',
                'exclude' => [
                    'custom'
                ]
            ]
        );

        $this->add_control(
            'image_mask_popover',
            [
                'label'        => esc_html__('Image Mask', 'bdthemes-element-pack') . BDTEP_NC,
                'type'         => Controls_Manager::POPOVER_TOGGLE,
                'render_type'  => 'ui',
                'return_value' => 'yes',
            ]
        );

        $this->start_popover();

        $this->add_control(
            'image_mask_shape',
            [
                'label'     => esc_html__('Masking Shape', 'bdthemes-element-pack'),
                'title'     => esc_html__('Masking Shape', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::CHOOSE,
                'default'   => 'default',
                'options'   => [
                    'default' => [
                        'title' => esc_html__('Default Shapes', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-star',
                    ],
                    'custom'  => [
                        'title' => esc_html__('Custom Shape', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-image-bold',
                    ],
                ],
                'toggle'    => false,
                'condition' => [
                    'image_mask_popover' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'image_mask_shape_default',
            [
                'label'          => _x('Default', 'Mask Image', 'bdthemes-element-pack'),
                'label_block'    => true,
                'show_label'     => false,
                'type'           => Controls_Manager::SELECT,
                'default'        => 0,
                'options'        => element_pack_mask_shapes(),
                'selectors'      => [
                    '{{WRAPPER}} .bdt-image-mask' => '-webkit-mask-image: url({{VALUE}}); mask-image: url({{VALUE}});',
                ],
                'condition'      => [
                    'image_mask_popover' => 'yes',
                    'image_mask_shape'   => 'default',
                ],
                'style_transfer' => true,
            ]
        );

        $this->add_control(
            'image_mask_shape_custom',
            [
                'label'      => _x('Custom Shape', 'Mask Image', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::MEDIA,
                'show_label' => false,
                'selectors'  => [
                    '{{WRAPPER}} .bdt-image-mask' => '-webkit-mask-image: url({{URL}}); mask-image: url({{URL}});',
                ],
                'condition'  => [
                    'image_mask_popover' => 'yes',
                    'image_mask_shape'   => 'custom',
                ],
            ]
        );

        $this->add_control(
            'image_mask_shape_position',
            [
                'label'                => esc_html__('Position', 'bdthemes-element-pack'),
                'type'                 => Controls_Manager::SELECT,
                'default'              => 'center-center',
                'options'              => [
                    'center-center' => esc_html__('Center Center', 'bdthemes-element-pack'),
                    'center-left'   => esc_html__('Center Left', 'bdthemes-element-pack'),
                    'center-right'  => esc_html__('Center Right', 'bdthemes-element-pack'),
                    'top-center'    => esc_html__('Top Center', 'bdthemes-element-pack'),
                    'top-left'      => esc_html__('Top Left', 'bdthemes-element-pack'),
                    'top-right'     => esc_html__('Top Right', 'bdthemes-element-pack'),
                    'bottom-center' => esc_html__('Bottom Center', 'bdthemes-element-pack'),
                    'bottom-left'   => esc_html__('Bottom Left', 'bdthemes-element-pack'),
                    'bottom-right'  => esc_html__('Bottom Right', 'bdthemes-element-pack'),
                ],
                'selectors_dictionary' => [
                    'center-center' => 'center center',
                    'center-left'   => 'center left',
                    'center-right'  => 'center right',
                    'top-center'    => 'top center',
                    'top-left'      => 'top left',
                    'top-right'     => 'top right',
                    'bottom-center' => 'bottom center',
                    'bottom-left'   => 'bottom left',
                    'bottom-right'  => 'bottom right',
                ],
                'selectors'            => [
                    '{{WRAPPER}} .bdt-image-mask' => '-webkit-mask-position: {{VALUE}}; mask-position: {{VALUE}};',
                ],
                'condition'            => [
                    'image_mask_popover' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'image_mask_shape_size',
            [
                'label'     => esc_html__('Size', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'contain',
                'options'   => [
                    'auto'    => esc_html__('Auto', 'bdthemes-element-pack'),
                    'cover'   => esc_html__('Cover', 'bdthemes-element-pack'),
                    'contain' => esc_html__('Contain', 'bdthemes-element-pack'),
                    'initial' => esc_html__('Custom', 'bdthemes-element-pack'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-image-mask' => '-webkit-mask-size: {{VALUE}}; mask-size: {{VALUE}};',
                ],
                'condition' => [
                    'image_mask_popover' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'image_mask_shape_custom_size',
            [
                'label'      => _x('Custom Size', 'Mask Image', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'responsive' => true,
                'size_units' => ['px', 'em', '%', 'vw'],
                'range'      => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    '%'  => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'vw' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default'    => [
                    'size' => 100,
                    'unit' => '%',
                ],
                'required'   => true,
                'selectors'  => [
                    '{{WRAPPER}} .bdt-image-mask' => '-webkit-mask-size: {{SIZE}}{{UNIT}}; mask-size: {{SIZE}}{{UNIT}};',
                ],
                'condition'  => [
                    'image_mask_popover'    => 'yes',
                    'image_mask_shape_size' => 'initial',
                ],
            ]
        );

        $this->add_control(
            'image_mask_shape_repeat',
            [
                'label'                => esc_html__('Repeat', 'bdthemes-element-pack'),
                'type'                 => Controls_Manager::SELECT,
                'default'              => 'no-repeat',
                'options'              => [
                    'repeat'          => esc_html__('Repeat', 'bdthemes-element-pack'),
                    'repeat-x'        => esc_html__('Repeat-x', 'bdthemes-element-pack'),
                    'repeat-y'        => esc_html__('Repeat-y', 'bdthemes-element-pack'),
                    'space'           => esc_html__('Space', 'bdthemes-element-pack'),
                    'round'           => esc_html__('Round', 'bdthemes-element-pack'),
                    'no-repeat'       => esc_html__('No-repeat', 'bdthemes-element-pack'),
                    'repeat-space'    => esc_html__('Repeat Space', 'bdthemes-element-pack'),
                    'round-space'     => esc_html__('Round Space', 'bdthemes-element-pack'),
                    'no-repeat-round' => esc_html__('No-repeat Round', 'bdthemes-element-pack'),
                ],
                'selectors_dictionary' => [
                    'repeat'          => 'repeat',
                    'repeat-x'        => 'repeat-x',
                    'repeat-y'        => 'repeat-y',
                    'space'           => 'space',
                    'round'           => 'round',
                    'no-repeat'       => 'no-repeat',
                    'repeat-space'    => 'repeat space',
                    'round-space'     => 'round space',
                    'no-repeat-round' => 'no-repeat round',
                ],
                'selectors'            => [
                    '{{WRAPPER}} .bdt-image-mask' => '-webkit-mask-repeat: {{VALUE}}; mask-repeat: {{VALUE}};',
                ],
                'condition'            => [
                    'image_mask_popover' => 'yes',
                ],
            ]
        );

        $this->end_popover();

        $this->add_responsive_control(
            'height',
            [
                'label' => __( 'Item Height', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'max' => 500,
                        'min' => 100,
                    ]
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-logo-grid-item' => 'height: {{SIZE}}{{UNIT}};'
                ],
            ]
        );

        $this->add_control(
            'grid_animation_type',
            [
                'label'   => esc_html__( 'Grid Entrance Animation', 'bdthemes-element-pack' ),
                'type'    => Controls_Manager::SELECT,
                'default' => '',
                'options' => element_pack_transition_options(),
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'grid_anim_delay',
            [
                'label'      => esc_html__( 'Animation delay', 'bdthemes-element-pack' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'ms', '' ],
                'range'      => [
                    'ms' => [
                        'min'  => 0,
                        'max'  => 1000,
                        'step' => 5,
                    ],
                ],
                'default'    => [
                    'unit' => 'ms',
                    'size' => 300,
                ],
                'condition' => [
                    'grid_animation_type!' => '',
                ],
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
			'section_tooltip_settings',
			[
				'label' => __( 'Tooltip Settings', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'logo_tooltip_animation',
			[
				'label'   => esc_html__( 'Animation', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'shift-toward',
				'options' => [
					'shift-away'   => esc_html__( 'Shift-Away', 'bdthemes-element-pack' ),
					'shift-toward' => esc_html__( 'Shift-Toward', 'bdthemes-element-pack' ),
					'fade'         => esc_html__( 'Fade', 'bdthemes-element-pack' ),
					'scale'        => esc_html__( 'Scale', 'bdthemes-element-pack' ),
					'perspective'  => esc_html__( 'Perspective', 'bdthemes-element-pack' ),
				],
				'render_type'  => 'template',
			]
		);

		$this->add_control(
			'logo_tooltip_x_offset',
			[
				'label'   => esc_html__( 'Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
			]
		);

		$this->add_control(
			'logo_tooltip_y_offset',
			[
				'label'   => esc_html__( 'Distance', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
			]
		);

		$this->add_control(
			'logo_tooltip_arrow',
			[
				'label'        => esc_html__( 'Arrow', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'logo_tooltip_trigger',
			[
				'label'       => __( 'Trigger on Click', 'bdthemes-element-pack' ),
				'description' => __( 'Don\'t set yes when you set lightbox image with marker.', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SWITCHER,
			]
		);

        $this->end_controls_section();

        //Style
        $this->start_controls_section(
            'ep_section_style_grid',
            [
                'label' => __( 'Logo Grid', 'bdthemes-element-pack' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs( 'ep_image_effects' );

        $this->start_controls_tab(
            'ep_tab_image_effects_normal',
            [
                'label' => __( 'Normal', 'bdthemes-element-pack' ),
            ]
        );

        $this->add_control(
            'grid_bg_color',
            [
                'label' => __( 'Background Color', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-logo-grid-figure' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'grid_border_type',
            [
                'label' => __( 'Border Type', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'none' => __( 'None', 'bdthemes-element-pack' ),
                    'solid' => __( 'Solid', 'bdthemes-element-pack' ),
                    'double' => __( 'Double', 'bdthemes-element-pack' ),
                    'dotted' => __( 'Dotted', 'bdthemes-element-pack' ),
                    'dashed' => __( 'Dashed', 'bdthemes-element-pack' ),
                    'groove' => __( 'Groove', 'bdthemes-element-pack' ),
                ],
                'default' => 'solid',
                'selectors' => [
                    '{{WRAPPER}} .bdt-logo-grid-item' => 'border-style: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'grid_border_width',
            [
                'label' => __( 'Border Width', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
					'size' => 2,
				],
				'tablet_default' => [
					'size' => 2,
				],
				'mobile_default' => [
					'size' => 2,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
                'selectors' => [
                    '(desktop){{WRAPPER}}.bdt-logo-grid--border .bdt-logo-grid-item' => 'border-right-width: {{grid_border_width.SIZE}}{{UNIT}}; border-bottom-width: {{grid_border_width.SIZE}}{{UNIT}};',
                    '(tablet){{WRAPPER}}.bdt-logo-grid--border .bdt-logo-grid-item' => 'border-right-width: {{grid_border_width_tablet.SIZE}}{{UNIT}}; border-bottom-width: {{grid_border_width_tablet.SIZE}}{{UNIT}};',
                    '(mobile){{WRAPPER}}.bdt-logo-grid--border .bdt-logo-grid-item' => 'border-right-width: {{grid_border_width_mobile.SIZE}}{{UNIT}}; border-bottom-width: {{grid_border_width_mobile.SIZE}}{{UNIT}};',

                    '(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col-2 .bdt-logo-grid-item:nth-child(2n+1)' => 'border-left-width: {{grid_border_width.SIZE}}{{UNIT}};',
                    '(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col-3 .bdt-logo-grid-item:nth-child(3n+1)' => 'border-left-width: {{grid_border_width.SIZE}}{{UNIT}};',
                    '(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col-4 .bdt-logo-grid-item:nth-child(4n+1)' => 'border-left-width: {{grid_border_width.SIZE}}{{UNIT}};',
                    '(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col-5 .bdt-logo-grid-item:nth-child(5n+1)' => 'border-left-width: {{grid_border_width.SIZE}}{{UNIT}};',
                    '(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col-6 .bdt-logo-grid-item:nth-child(6n+1)' => 'border-left-width: {{grid_border_width.SIZE}}{{UNIT}};',
                    '(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col-2 .bdt-logo-grid-item:nth-child(-n+2)' => 'border-top-width: {{grid_border_width.SIZE}}{{UNIT}};',
                    '(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col-3 .bdt-logo-grid-item:nth-child(-n+3)' => 'border-top-width: {{grid_border_width.SIZE}}{{UNIT}};',
                    '(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col-4 .bdt-logo-grid-item:nth-child(-n+4)' => 'border-top-width: {{grid_border_width.SIZE}}{{UNIT}};',
                    '(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col-5 .bdt-logo-grid-item:nth-child(-n+5)' => 'border-top-width: {{grid_border_width.SIZE}}{{UNIT}};',
                    '(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col-6 .bdt-logo-grid-item:nth-child(-n+6)' => 'border-top-width: {{grid_border_width.SIZE}}{{UNIT}};',

                    '(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--tablet2 .bdt-logo-grid-item:nth-child(2n+1)' => 'border-left-width: {{grid_border_width_tablet.SIZE}}{{UNIT}};',
                    '(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--tablet3 .bdt-logo-grid-item:nth-child(3n+1)' => 'border-left-width: {{grid_border_width_tablet.SIZE}}{{UNIT}};',
                    '(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--tablet4 .bdt-logo-grid-item:nth-child(4n+1)' => 'border-left-width: {{grid_border_width_tablet.SIZE}}{{UNIT}};',
                    '(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--tablet5 .bdt-logo-grid-item:nth-child(5n+1)' => 'border-left-width: {{grid_border_width_tablet.SIZE}}{{UNIT}};',
                    '(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--tablet6 .bdt-logo-grid-item:nth-child(6n+1)' => 'border-left-width: {{grid_border_width_tablet.SIZE}}{{UNIT}};',
                    '(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--tablet2 .bdt-logo-grid-item:nth-child(-n+2)' => 'border-top-width: {{grid_border_width_tablet.SIZE}}{{UNIT}};',
                    '(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--tablet3 .bdt-logo-grid-item:nth-child(-n+3)' => 'border-top-width: {{grid_border_width_tablet.SIZE}}{{UNIT}};',
                    '(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--tablet4 .bdt-logo-grid-item:nth-child(-n+4)' => 'border-top-width: {{grid_border_width_tablet.SIZE}}{{UNIT}};',
                    '(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--tablet5 .bdt-logo-grid-item:nth-child(-n+5)' => 'border-top-width: {{grid_border_width_tablet.SIZE}}{{UNIT}};',
                    '(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--tablet6 .bdt-logo-grid-item:nth-child(-n+6)' => 'border-top-width: {{grid_border_width_tablet.SIZE}}{{UNIT}};',

                    '(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--mobile2 .bdt-logo-grid-item:nth-child(2n+1)' => 'border-left-width: {{grid_border_width_mobile.SIZE}}{{UNIT}};',
                    '(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--mobile3 .bdt-logo-grid-item:nth-child(3n+1)' => 'border-left-width: {{grid_border_width_mobile.SIZE}}{{UNIT}};',
                    '(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--mobile4 .bdt-logo-grid-item:nth-child(4n+1)' => 'border-left-width: {{grid_border_width_mobile.SIZE}}{{UNIT}};',
                    '(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--mobile5 .bdt-logo-grid-item:nth-child(5n+1)' => 'border-left-width: {{grid_border_width_mobile.SIZE}}{{UNIT}};',
                    '(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--mobile6 .bdt-logo-grid-item:nth-child(6n+1)' => 'border-left-width: {{grid_border_width_mobile.SIZE}}{{UNIT}};',
                    '(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--mobile2 .bdt-logo-grid-item:nth-child(-n+2)' => 'border-top-width: {{grid_border_width_mobile.SIZE}}{{UNIT}};',
                    '(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--mobile3 .bdt-logo-grid-item:nth-child(-n+3)' => 'border-top-width: {{grid_border_width_mobile.SIZE}}{{UNIT}};',
                    '(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--mobile4 .bdt-logo-grid-item:nth-child(-n+4)' => 'border-top-width: {{grid_border_width_mobile.SIZE}}{{UNIT}};',
                    '(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--mobile5 .bdt-logo-grid-item:nth-child(-n+5)' => 'border-top-width: {{grid_border_width_mobile.SIZE}}{{UNIT}};',
                    '(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--mobile6 .bdt-logo-grid-item:nth-child(-n+6)' => 'border-top-width: {{grid_border_width_mobile.SIZE}}{{UNIT}};',

                    '{{WRAPPER}}.bdt-logo-grid--tictactoe .bdt-logo-grid-item' => 'border-top-width: {{SIZE}}{{UNIT}}; border-right-width: {{SIZE}}{{UNIT}};',

                    '{{WRAPPER}}.bdt-logo-grid--box .bdt-logo-grid-item' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'grid_border_type!' => 'none',
                ]
            ]
        );

        $this->add_control(
            'grid_border_color',
            [
                'label' => __( 'Border Color', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-logo-grid-item' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'grid_border_type!' => 'none',
                ]
            ]
        );

        $this->add_responsive_control(
            'grid_border_radius',
            [
                'label' => __( 'Border Radius', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}}.bdt-logo-grid--border .bdt-logo-grid-wrapper, {{WRAPPER}}.bdt-logo-grid--box .bdt-logo-grid-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}}.bdt-logo-grid--border .bdt-logo-grid-item:first-child' => 'border-top-left-radius: {{TOP}}{{UNIT}};',
                    '{{WRAPPER}}.bdt-logo-grid--border .bdt-logo-grid-item:last-child' => 'border-bottom-right-radius: {{BOTTOM}}{{UNIT}};',

                    '(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col-2 .bdt-logo-grid-item:nth-child(2)' => 'border-top-right-radius: {{grid_border_radius.RIGHT}}{{UNIT}};',
                    '(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col-2 .bdt-logo-grid-item:nth-last-child(2)' => 'border-bottom-left-radius: {{grid_border_radius.LEFT}}{{UNIT}};',
                    '(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col-3 .bdt-logo-grid-item:nth-child(3)' => 'border-top-right-radius: {{grid_border_radius.RIGHT}}{{UNIT}};',
                    '(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col-3 .bdt-logo-grid-item:nth-last-child(3)' => 'border-bottom-left-radius: {{grid_border_radius.LEFT}}{{UNIT}};',
                    '(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col-4 .bdt-logo-grid-item:nth-child(4)' => 'border-top-right-radius: {{grid_border_radius.RIGHT}}{{UNIT}};',
                    '(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col-4 .bdt-logo-grid-item:nth-last-child(4)' => 'border-bottom-left-radius: {{grid_border_radius.LEFT}}{{UNIT}};',
                    '(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col-5 .bdt-logo-grid-item:nth-child(5)' => 'border-top-right-radius: {{grid_border_radius.RIGHT}}{{UNIT}};',
                    '(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col-5 .bdt-logo-grid-item:nth-last-child(5)' => 'border-bottom-left-radius: {{grid_border_radius.LEFT}}{{UNIT}};',
                    '(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col-6 .bdt-logo-grid-item:nth-child(6)' => 'border-top-right-radius: {{grid_border_radius.RIGHT}}{{UNIT}};',
                    '(desktop){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col-6 .bdt-logo-grid-item:nth-last-child(6)' => 'border-bottom-left-radius: {{grid_border_radius.LEFT}}{{UNIT}};',

                    '(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--tablet2 .bdt-logo-grid-item:nth-child(2)' => 'border-top-right-radius: {{grid_border_radius_tablet.RIGHT}}{{UNIT}};',
                    '(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--tablet2 .bdt-logo-grid-item:nth-last-child(2)' => 'border-bottom-left-radius: {{grid_border_radius_tablet.LEFT}}{{UNIT}};',
                    '(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--tablet3 .bdt-logo-grid-item:nth-child(3)' => 'border-top-right-radius: {{grid_border_radius_tablet.RIGHT}}{{UNIT}};',
                    '(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--tablet3 .bdt-logo-grid-item:nth-last-child(3)' => 'border-bottom-left-radius: {{grid_border_radius_tablet.LEFT}}{{UNIT}};',
                    '(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--tablet4 .bdt-logo-grid-item:nth-child(4)' => 'border-top-right-radius: {{grid_border_radius_tablet.RIGHT}}{{UNIT}};',
                    '(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--tablet4 .bdt-logo-grid-item:nth-last-child(4)' => 'border-bottom-left-radius: {{grid_border_radius_tablet.LEFT}}{{UNIT}};',
                    '(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--tablet5 .bdt-logo-grid-item:nth-child(5)' => 'border-top-right-radius: {{grid_border_radius_tablet.RIGHT}}{{UNIT}};',
                    '(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--tablet5 .bdt-logo-grid-item:nth-last-child(5)' => 'border-bottom-left-radius: {{grid_border_radius_tablet.LEFT}}{{UNIT}};',
                    '(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--tablet6 .bdt-logo-grid-item:nth-child(6)' => 'border-top-right-radius: {{grid_border_radius_tablet.RIGHT}}{{UNIT}};',
                    '(tablet){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--tablet6 .bdt-logo-grid-item:nth-last-child(6)' => 'border-bottom-left-radius: {{grid_border_radius_tablet.LEFT}}{{UNIT}};',

                    '(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--mobile2 .bdt-logo-grid-item:nth-child(2)' => 'border-top-right-radius: {{grid_border_radius_mobile.RIGHT}}{{UNIT}};',
                    '(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--mobile2 .bdt-logo-grid-item:nth-last-child(2)' => 'border-bottom-left-radius: {{grid_border_radius_mobile.LEFT}}{{UNIT}};',
                    '(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--mobile3 .bdt-logo-grid-item:nth-child(3)' => 'border-top-right-radius: {{grid_border_radius_mobile.RIGHT}}{{UNIT}};',
                    '(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--mobile3 .bdt-logo-grid-item:nth-last-child(3)' => 'border-bottom-left-radius: {{grid_border_radius_mobile.LEFT}}{{UNIT}};',
                    '(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--mobile4 .bdt-logo-grid-item:nth-child(4)' => 'border-top-right-radius: {{grid_border_radius_mobile.RIGHT}}{{UNIT}};',
                    '(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--mobile4 .bdt-logo-grid-item:nth-last-child(4)' => 'border-bottom-left-radius: {{grid_border_radius_mobile.LEFT}}{{UNIT}};',
                    '(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--mobile5 .bdt-logo-grid-item:nth-child(5)' => 'border-top-right-radius: {{grid_border_radius_mobile.RIGHT}}{{UNIT}};',
                    '(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--mobile5 .bdt-logo-grid-item:nth-last-child(5)' => 'border-bottom-left-radius: {{grid_border_radius_mobile.LEFT}}{{UNIT}};',
                    '(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--mobile6 .bdt-logo-grid-item:nth-child(6)' => 'border-top-right-radius: {{grid_border_radius_mobile.RIGHT}}{{UNIT}};',
                    '(mobile){{WRAPPER}}.bdt-logo-grid--border.bdt-logo-grid--col--mobile6 .bdt-logo-grid-item:nth-last-child(6)' => 'border-bottom-left-radius: {{grid_border_radius_mobile.LEFT}}{{UNIT}};',

                    // Tictactoe
                    '{{WRAPPER}}.bdt-logo-grid--tictactoe .bdt-logo-grid-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}}.bdt-logo-grid--tictactoe .bdt-logo-grid-item:first-child' => 'border-top-left-radius: {{TOP}}{{UNIT}};',
                    '{{WRAPPER}}.bdt-logo-grid--tictactoe .bdt-logo-grid-item:last-child' => 'border-bottom-right-radius: {{BOTTOM}}{{UNIT}};',

                    '(desktop){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-logo-grid--col-2 .bdt-logo-grid-item:nth-child(2)' => 'border-top-right-radius: {{grid_border_radius.RIGHT}}{{UNIT}};',
                    '(desktop){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-logo-grid--col-2 .bdt-logo-grid-item:nth-last-child(2)' => 'border-bottom-left-radius: {{grid_border_radius.LEFT}}{{UNIT}};',
                    '(desktop){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-logo-grid--col-3 .bdt-logo-grid-item:nth-child(3)' => 'border-top-right-radius: {{grid_border_radius.RIGHT}}{{UNIT}};',
                    '(desktop){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-logo-grid--col-3 .bdt-logo-grid-item:nth-last-child(3)' => 'border-bottom-left-radius: {{grid_border_radius.LEFT}}{{UNIT}};',
                    '(desktop){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-logo-grid--col-4 .bdt-logo-grid-item:nth-child(4)' => 'border-top-right-radius: {{grid_border_radius.RIGHT}}{{UNIT}};',
                    '(desktop){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-logo-grid--col-4 .bdt-logo-grid-item:nth-last-child(4)' => 'border-bottom-left-radius: {{grid_border_radius.LEFT}}{{UNIT}};',
                    '(desktop){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-logo-grid--col-5 .bdt-logo-grid-item:nth-child(5)' => 'border-top-right-radius: {{grid_border_radius.RIGHT}}{{UNIT}};',
                    '(desktop){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-logo-grid--col-5 .bdt-logo-grid-item:nth-last-child(5)' => 'border-bottom-left-radius: {{grid_border_radius.LEFT}}{{UNIT}};',
                    '(desktop){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-logo-grid--col-6 .bdt-logo-grid-item:nth-child(6)' => 'border-top-right-radius: {{grid_border_radius.RIGHT}}{{UNIT}};',
                    '(desktop){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-logo-grid--col-6 .bdt-logo-grid-item:nth-last-child(6)' => 'border-bottom-left-radius: {{grid_border_radius.LEFT}}{{UNIT}};',

                    '(tablet){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-logo-grid--col--tablet2 .bdt-logo-grid-item:nth-child(2)' => 'border-top-right-radius: {{grid_border_radius_tablet.RIGHT}}{{UNIT}};',
                    '(tablet){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-logo-grid--col--tablet2 .bdt-logo-grid-item:nth-last-child(2)' => 'border-bottom-left-radius: {{grid_border_radius_tablet.LEFT}}{{UNIT}};',
                    '(tablet){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-logo-grid--col--tablet3 .bdt-logo-grid-item:nth-child(3)' => 'border-top-right-radius: {{grid_border_radius_tablet.RIGHT}}{{UNIT}};',
                    '(tablet){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-logo-grid--col--tablet3 .bdt-logo-grid-item:nth-last-child(3)' => 'border-bottom-left-radius: {{grid_border_radius_tablet.LEFT}}{{UNIT}};',
                    '(tablet){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-logo-grid--col--tablet4 .bdt-logo-grid-item:nth-child(4)' => 'border-top-right-radius: {{grid_border_radius_tablet.RIGHT}}{{UNIT}};',
                    '(tablet){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-logo-grid--col--tablet4 .bdt-logo-grid-item:nth-last-child(4)' => 'border-bottom-left-radius: {{grid_border_radius_tablet.LEFT}}{{UNIT}};',
                    '(tablet){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-logo-grid--col--tablet5 .bdt-logo-grid-item:nth-child(5)' => 'border-top-right-radius: {{grid_border_radius_tablet.RIGHT}}{{UNIT}};',
                    '(tablet){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-logo-grid--col--tablet5 .bdt-logo-grid-item:nth-last-child(5)' => 'border-bottom-left-radius: {{grid_border_radius_tablet.LEFT}}{{UNIT}};',
                    '(tablet){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-logo-grid--col--tablet6 .bdt-logo-grid-item:nth-child(6)' => 'border-top-right-radius: {{grid_border_radius_tablet.RIGHT}}{{UNIT}};',
                    '(tablet){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-logo-grid--col--tablet6 .bdt-logo-grid-item:nth-last-child(6)' => 'border-bottom-left-radius: {{grid_border_radius_tablet.LEFT}}{{UNIT}};',

                    '(mobile){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-logo-grid--col--mobile2 .bdt-logo-grid-item:nth-child(2)' => 'border-top-right-radius: {{grid_border_radius_mobile.RIGHT}}{{UNIT}};',
                    '(mobile){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-logo-grid--col--mobile2 .bdt-logo-grid-item:nth-last-child(2)' => 'border-bottom-left-radius: {{grid_border_radius_mobile.LEFT}}{{UNIT}};',
                    '(mobile){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-logo-grid--col--mobile3 .bdt-logo-grid-item:nth-child(3)' => 'border-top-right-radius: {{grid_border_radius_mobile.RIGHT}}{{UNIT}};',
                    '(mobile){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-logo-grid--col--mobile3 .bdt-logo-grid-item:nth-last-child(3)' => 'border-bottom-left-radius: {{grid_border_radius_mobile.LEFT}}{{UNIT}};',
                    '(mobile){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-logo-grid--col--mobile4 .bdt-logo-grid-item:nth-child(4)' => 'border-top-right-radius: {{grid_border_radius_mobile.RIGHT}}{{UNIT}};',
                    '(mobile){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-logo-grid--col--mobile4 .bdt-logo-grid-item:nth-last-child(4)' => 'border-bottom-left-radius: {{grid_border_radius_mobile.LEFT}}{{UNIT}};',
                    '(mobile){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-logo-grid--col--mobile5 .bdt-logo-grid-item:nth-child(5)' => 'border-top-right-radius: {{grid_border_radius_mobile.RIGHT}}{{UNIT}};',
                    '(mobile){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-logo-grid--col--mobile5 .bdt-logo-grid-item:nth-last-child(5)' => 'border-bottom-left-radius: {{grid_border_radius_mobile.LEFT}}{{UNIT}};',
                    '(mobile){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-logo-grid--col--mobile6 .bdt-logo-grid-item:nth-child(6)' => 'border-top-right-radius: {{grid_border_radius_mobile.RIGHT}}{{UNIT}};',
                    '(mobile){{WRAPPER}}.bdt-logo-grid--tictactoe.bdt-logo-grid--col--mobile6 .bdt-logo-grid-item:nth-last-child(6)' => 'border-bottom-left-radius: {{grid_border_radius_mobile.LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'padding',
            [
                'label' => __( 'Padding', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-logo-grid-figure' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'grid_box_shadow',
                'exclude' => [
                    'box_shadow_position',
                ],
                'selector' => '{{WRAPPER}}.bdt-logo-grid--tictactoe .bdt-logo-grid-wrapper, {{WRAPPER}}.bdt-logo-grid--border .bdt-logo-grid-wrapper, {{WRAPPER}}.bdt-logo-grid--box .bdt-logo-grid-item'
            ]
        );

        $this->add_control(
            'image_opacity',
            [
                'label' => __( 'Opacity', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-logo-grid-figure img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'image_css_filters',
                'selector' => '{{WRAPPER}} .bdt-logo-grid-figure img',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab( 'hover',
            [
                'label' => __( 'Hover', 'bdthemes-element-pack' ),
            ]
        );

        $this->add_control(
            'grid_bg_hover_color',
            [
                'label' => __( 'Background Color', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-logo-grid-item:hover .bdt-logo-grid-figure' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'grid_border_hover_color',
            [
                'label' => __( 'Border Color', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-logo-grid-item:hover' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'grid_border_type!' => 'none',
                    'layout'    => 'box',
                ]
            ]
        );

        $this->add_control(
            'image_opacity_hover',
            [
                'label' => __( 'Opacity', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-logo-grid-figure:hover img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'image_css_filters_hover',
                'selector' => '{{WRAPPER}} .bdt-logo-grid-figure:hover img',
            ]
        );

        $this->add_control(
            'image_bg_hover_transition',
            [
                'label' => __( 'Transition Duration', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-logo-grid-figure:hover img' => 'transition-duration: {{SIZE}}s;',
                ],
            ]
        );

        $this->add_control(
            'hover_animation',
            [
                'label' => __( 'Hover Animation', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::HOVER_ANIMATION,
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
			'section_style_tooltip',
			[
				'label' => esc_html__( 'Tooltip', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'logo_tooltip_width',
			[
				'label'      => esc_html__( 'Width', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [
					'px', 'em',
				],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tippy-tooltip' => 'width: {{SIZE}}{{UNIT}};',
				],
				'render_type' => 'template',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'logo_tooltip_typography',
				'selector' => '{{WRAPPER}} .tippy-tooltip .tippy-content',
			]
		);

		$this->add_control(
			'logo_tooltip_color',
			[
				'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tippy-tooltip' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'logo_tooltip_text_align',
			[
				'label'   => esc_html__( 'Text Alignment', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'left'    => [
						'title' => esc_html__( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'fas fa-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'fas fa-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'fas fa-align-right',
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .tippy-tooltip .tippy-content' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'logo_tooltip_background',
				'selector' => '{{WRAPPER}} .tippy-tooltip, {{WRAPPER}} .tippy-tooltip .tippy-backdrop',
			]
		);

		$this->add_control(
			'logo_tooltip_arrow_color',
			[
				'label'     => esc_html__( 'Arrow Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tippy-popper[x-placement^=left] .tippy-arrow'  => 'border-left-color: {{VALUE}}',
					'{{WRAPPER}} .tippy-popper[x-placement^=right] .tippy-arrow' => 'border-right-color: {{VALUE}}',
					'{{WRAPPER}} .tippy-popper[x-placement^=top] .tippy-arrow'   => 'border-top-color: {{VALUE}}',
					'{{WRAPPER}} .tippy-popper[x-placement^=bottom] .tippy-arrow'=> 'border-bottom-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'logo_tooltip_padding',
			[
				'label'      => __( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tippy-tooltip' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'render_type'  => 'template',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'logo_tooltip_border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .tippy-tooltip',
			]
		);

		$this->add_responsive_control(
			'logo_tooltip_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tippy-tooltip' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'logo_tooltip_box_shadow',
				'selector' => '{{WRAPPER}} .tippy-tooltip',
			]
		);

		$this->end_controls_section();
        
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute( 'logo-grid', 'class', 'bdt-logo-grid-wrapper' );

        if($settings['grid_animation_type'] !== ''){
            $this->add_render_attribute( 'logo-grid', 'bdt-scrollspy', 'cls: bdt-animation-' . esc_attr($settings['grid_animation_type']) . ';' );
            $this->add_render_attribute( 'logo-grid', 'bdt-scrollspy', 'delay: ' . esc_attr($settings['grid_anim_delay']['size']) . ';' );
            $this->add_render_attribute( 'logo-grid', 'bdt-scrollspy', 'target: > .bdt-logo-grid-item' . ';' );
        } 

        if ( empty($settings['logo_list'] ) ) {
            return;
        }

        ?>

        <div <?php $this->print_render_attribute_string( 'logo-grid' ); ?>>
            <?php
            foreach ( $settings['logo_list'] as $index => $item ) :
                $image = wp_get_attachment_image_url( $item['image']['id'], $settings['thumbnail_size'] );
                $repeater_key = 'grid_item' . $index;
                $tag = 'div';
                $tooltip_content = '<div><strong>' . $item['name'] . '</strong></div>' . $item['description']; 
                $image_alt = $item['name'] . ' : ' . $item['description']; 
                $this->add_render_attribute( $repeater_key, 'class', 'bdt-logo-grid-item' );
                $this->add_render_attribute( $repeater_key, 'data-tippy-content', $tooltip_content, true);

                if ( $item['link']['url'] ) {
                    $tag = 'a';
                    $this->add_render_attribute( $repeater_key, 'class', 'bdt-logo-grid-link' );
                    $this->add_render_attribute( $repeater_key, 'target', '_blank' );
                    $this->add_render_attribute( $repeater_key, 'rel', 'noopener' );
                    $this->add_render_attribute( $repeater_key, 'href', esc_url( $item['link']['url'] ) );
                }

                if ($item['name'] and $item['description'] and $item['logo_tooltip']) {
					// Tooltip settings
					$this->add_render_attribute( $repeater_key, 'class', 'bdt-tippy-tooltip' );
					$this->add_render_attribute( $repeater_key, 'data-tippy', '', true );

					if ($item['logo_tooltip_placement']) {
						$this->add_render_attribute( $repeater_key, 'data-tippy-placement', $item['logo_tooltip_placement'], true );
					}

					if ($settings['logo_tooltip_animation']) {
						$this->add_render_attribute( $repeater_key, 'data-tippy-animation', $settings['logo_tooltip_animation'], true );
					}

					if ($settings['logo_tooltip_x_offset']['size'] or $settings['logo_tooltip_y_offset']['size']) {
						$this->add_render_attribute( $repeater_key, 'data-tippy-offset', $settings['logo_tooltip_x_offset']['size'] .','. $settings['logo_tooltip_y_offset']['size'], true );
					}

					if ('yes' == $settings['logo_tooltip_arrow']) {
						$this->add_render_attribute( $repeater_key, 'data-tippy-arrow', 'true', true );
					}

					if ('yes' == $settings['logo_tooltip_trigger']) {
						$this->add_render_attribute( $repeater_key, 'data-tippy-trigger', 'click', true );
					}

                }
                
                ?>
                <<?php echo $tag; ?> <?php $this->print_render_attribute_string( $repeater_key ); ?>>
                    <figure class="bdt-logo-grid-figure">
                    <?php if ( $image ) :
 

                            echo wp_get_attachment_image(
                                $item['image']['id'],
                                $settings['thumbnail_size'],
                                false,
                                [
                                    'class' => 'bdt-logo-grid-img bdt-image-mask elementor-animation-' . esc_attr( $settings['hover_animation'] ),
                                    'alt'=> esc_attr( $image_alt ),
                                ]
                            );
                        
                        else :
                            printf( '<img class="bdt-logo-grid-img bdt-image-mask elementor-animation-%s" src="%s" alt="%s">',
                                esc_attr( $settings['hover_animation'] ),
                                Utils::get_placeholder_image_src(),
                                esc_attr( $image_alt )
                                );
                        endif; ?>

                    </figure>
                </<?php echo $tag; ?>>
            <?php endforeach; ?>
        </div>

        <?php
    }
}
