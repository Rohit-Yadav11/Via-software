<?php
namespace ElementPack\Modules\PriceList\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Repeater;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Price_List extends Module_Base {

	public function get_name() {
		return 'bdt-price-list';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Price List', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-price-list';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'price', 'lsit', 'rate', 'cost', 'value' ];
	}

	public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-all-styles'];
        } else {
            return [ 'ep-price-list' ];
        }
    }

	public function get_custom_help_url() {
		return 'https://youtu.be/QsXkIYwfXt4';
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_content_list',
			[
				'label' => esc_html__( 'List', 'bdthemes-element-pack' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'price', 
			[
				'label'   => esc_html__( 'Price', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'title', 
			[
				'label'       => esc_html__( 'Title', 'bdthemes-element-pack' ),
				'default'     => esc_html__( 'First item on the list', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => 'true',
				'dynamic'     => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'item_description', 
			[
				'label'   => esc_html__( 'Description', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXTAREA,
				'dynamic' => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'image', 
			[
				'label'   => esc_html__( 'Image', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [],
				'dynamic' => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'link', 
			[
				'label'   => esc_html__( 'Link', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::URL,
				'default' => [ 'url' => '#' ],
				'dynamic' => [ 'active' => true ],
			]
		);

		$this->add_control(
			'price_list',
			[
				'label'  => esc_html__( 'List Items', 'bdthemes-element-pack' ),
				'type'   => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'title' => esc_html__( 'First item on the list', 'bdthemes-element-pack' ),
						'price' => '$20',
						'link'  => [ 'url' => '#' ],
					],
					[
						'title' => esc_html__( 'Second item on the list', 'bdthemes-element-pack' ),
						'price' => '$9',
						'link'  => [ 'url' => '#' ],
					],
					[
						'title' => esc_html__( 'Third item on the list', 'bdthemes-element-pack' ),
						'price' => '$32',
						'link'  => [ 'url' => '#' ],
					],
				],
				'title_field' => '{{{ title }}}',
			]
		);
		$this->end_controls_section();

        $this->start_controls_section(
            'section_style_item_style',
            [
                'label'      => esc_html__( 'Item Style', 'bdthemes-element-pack' ),
                'tab'        => Controls_Manager::TAB_STYLE,
                'show_label' => false,
            ]
        );

        $this->add_control(
            'row_gap',
            [
                'label' => esc_html__( 'Rows Gap', 'bdthemes-element-pack' ),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                    'em' => [
                        'max' => 5,
                        'step' => 0.1,
                    ],
                ],
                'size_units' => [ 'px', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-price-list li:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'vertical_align',
            [
                'label'       => esc_html__( 'Vertical Align', 'bdthemes-element-pack' ),
                'type'        => Controls_Manager::SELECT,
                'description' => 'When you will take image then you understand its function',
                'options'     => [
                    'middle' => esc_html__( 'Middle', 'bdthemes-element-pack' ),
                    'top'    => esc_html__( 'Top', 'bdthemes-element-pack' ),
                    'bottom' => esc_html__( 'Bottom', 'bdthemes-element-pack' ),
                ],
                'default' => 'middle',
                'separator' => 'after',
            ]
        );


        $this->add_control(
            'heading__title',
            [
                'label' => esc_html__( 'Title', 'bdthemes-element-pack' ),
                'type'  => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'heading_color',
            [
                'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-price-list .bdt-price-list-price' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-price-list .bdt-price-list-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'heading_typography',
                'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
                'selector' => '{{WRAPPER}} .bdt-price-list-header',
            ]
        );

        $this->add_control(
            'heading_item_description',
            [
                'label'     => esc_html__( 'Description', 'bdthemes-element-pack' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'description_color',
            [
                'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-price-list-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'description_typography',
                'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
                'selector' => '{{WRAPPER}} .bdt-price-list-description',
            ]
        );

        $this->add_control(
            'heading_separator',
            [
                'label'     => esc_html__( 'Separator', 'bdthemes-element-pack' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'separator_style',
            [
                'label'   => esc_html__( 'Style', 'bdthemes-element-pack' ),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    'solid'  => esc_html__( 'Solid', 'bdthemes-element-pack' ),
                    'dotted' => esc_html__( 'Dotted', 'bdthemes-element-pack' ),
                    'dashed' => esc_html__( 'Dashed', 'bdthemes-element-pack' ),
                    'double' => esc_html__( 'Double', 'bdthemes-element-pack' ),
                    'none'   => esc_html__( 'None', 'bdthemes-element-pack' ),
                ],
                'default'   => 'dashed',
                'selectors' => [
                    '{{WRAPPER}} .bdt-price-list-separator' => 'border-bottom-style: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'separator_weight',
            [
                'label' => esc_html__( 'Weight', 'bdthemes-element-pack' ),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 10,
                    ],
                ],
                'condition' => [
                    'separator_style!' => 'none',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-price-list-separator' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
                ],
                'default' => [
                    'size' => 1,
                ],
            ]
        );

        $this->add_control(
            'separator_color',
            [
                'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-price-list-separator' => 'border-bottom-color: {{VALUE}};',
                ],
                'condition' => [
                    'separator_style!' => 'none',
                ],
            ]
        );

        $this->add_control(
            'separator_spacing',
            [
                'label' => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 40,
                    ],
                ],
                'condition' => [
                    'separator_style!' => 'none',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-price-list-separator' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

		$this->start_controls_section(
			'section_style_image_style',
			[
				'label'      => esc_html__( 'Image Style', 'bdthemes-element-pack' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			]
		);

		$this->add_control(
			'image_size',
			[
				'label' => esc_html__( 'Image Size', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 250,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list-image' => 'width: {{SIZE}}{{UNIT}}; height: auto;',
				],
			]
		);

		$this->add_control(
			'image_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-price-list-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_spacing',
			[
				'label' => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'body.rtl {{WRAPPER}} .bdt-price-list-image'                              => 'padding-left: calc({{SIZE}}{{UNIT}}/2);',
					'body.rtl {{WRAPPER}} .bdt-price-list-image + .bdt-price-list-text'       => 'padding-right: calc({{SIZE}}{{UNIT}}/2);',
					'body:not(.rtl) {{WRAPPER}} .bdt-price-list-image'                        => 'padding-right: calc({{SIZE}}{{UNIT}}/2);',
					'body:not(.rtl) {{WRAPPER}} .bdt-price-list-image + .bdt-price-list-text' => 'padding-left: calc({{SIZE}}{{UNIT}}/2);',
				],
				'default' => [
					'size' => 20,
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_price',
			[
				'label'      => esc_html__( 'Price', 'bdthemes-element-pack' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			]
		);

		$this->add_control(
			'price_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list .bdt-price-list-price' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'price_hover_color',
			[
				'label'     => esc_html__( 'Hover Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list li:hover .bdt-price-list-price' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'price_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#4AB8F8',
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list .bdt-price-list-price' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-price-list .bdt-price-list-price',
			]
		);

		$this->add_control(
			'price_border_radius',
			[
				'label'   => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 50,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list .bdt-price-list-price' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'price_width',
			[
				'label'   => esc_html__( 'Width', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 50,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list .bdt-price-list-price' => 'width: {{SIZE}}{{UNIT}}; text-align: center;',
				],
			]
		);

		$this->add_control(
			'price_height',
			[
				'label' => esc_html__( 'Height', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-price-list .bdt-price-list-price' => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; vertical-align: middle;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'price_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-price-list .bdt-price-list-price',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'price_typography',
				'selector' => '{{WRAPPER}} .bdt-price-list .bdt-price-list-price',
			]
		);

		$this->end_controls_section();
	}

	private function render_image( $item, $settings ) {
		$image_id  = $item['image']['id'];
		$image_src = wp_get_attachment_image_src( $image_id, 'full' );
		$image_src = $image_src[0];

		return sprintf( '<img src="%s" alt="%s" />', $image_src, $item['title'] );
	}

	private function render_item_header( $item ) {
		$settings      = $this->get_settings_for_display();
		$url           = $item['link']['url'];
		$item_id       = $item['_id'];
		$bdt_has_image = $item['image']['url'] ? 'bdt-has-image ' : '';

        if ( $url ) {
            $unique_link_id = 'item-link-' . $item_id;

            $this->add_render_attribute( $unique_link_id, 'class', 'bdt-grid bdt-grid-collapse bdt-flex-'. esc_attr($settings['vertical_align']) );
            $this->add_render_attribute( $unique_link_id, 'class', esc_attr($bdt_has_image) );


            $target = $item['link']['is_external'] ? '_blank' : '_self';

            $this->add_render_attribute( $unique_link_id, 'onclick', "window.open('" . $url . "', '$target')" );

			return '<li class="bdt-price-list-item"><div ' . $this->get_render_attribute_string( $unique_link_id ) . 'bdt-grid>';
		} else {
			return '<li class="bdt-price-list-item bdt-grid bdt-grid-small '.esc_attr($bdt_has_image).'bdt-flex-'. esc_attr($settings['vertical_align']) .'" bdt-grid>';
		}
	}

	private function render_item_footer( $item ) {
		if ( $item['link']['url'] ) {
			return '</div></li>';
		} else {
			return '</li>';
		}
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		?>
		<ul class="bdt-price-list">

		<?php foreach ( $settings['price_list'] as $item ) :
			echo $this->render_item_header( $item );

			if ( ! empty( $item['image']['url'] ) ) : ?>
				<div class="bdt-price-list-image bdt-width-auto">
					<?php echo $this->render_image( $item, $settings ); ?>
				</div>
			<?php endif; ?>

			<div class="bdt-price-list-text bdt-width-expand">
				<div>
					<div class="bdt-price-list-header bdt-grid bdt-grid-small bdt-flex-middle" bdt-grid>
						<span class="bdt-price-list-title"><?php echo esc_html($item['title']); ?></span>

						<?php if ( 'none' != $settings['separator_style'] ) : ?>
							<span class="bdt-price-list-separator bdt-width-expand"></span>
						<?php endif; ?>

					</div>

                    <?php if ( $item['item_description'] ) : ?>
                        <p class="bdt-price-list-description"><?php echo $this->parse_text_editor($item['item_description']); ?></p>
                    <?php endif; ?>
				</div>
			</div>
			<div class="bdt-width-auto bdt-flex-inline">
				<span class="bdt-price-list-price"><?php echo esc_html($item['price']); ?></span>
			</div>

			<?php echo $this->render_item_footer( $item ); ?>

		<?php endforeach; ?>

		</ul>
		<?php
	}

	protected function _content_template() {
		?>
		<ul class="bdt-price-list">
			<#
				for ( var i in settings.price_list ) {
					var item = settings.price_list[i],
						item_open_wrap = '<li class="bdt-price-list-item bdt-grid bdt-grid-small bdt-flex-' + settings.vertical_align + '" bdt-grid>',
						item_close_wrap = '</li>';
					if ( item.link.url ) {
						item_open_wrap = '<li class="bdt-price-list-item"><div class="bdt-grid bdt-grid-small bdt-flex-' + settings.vertical_align + '" href="' + item.link.url + '" class="bdt-price-list-item bdt-link-reset" bdt-grid>';
						item_close_wrap = '</div></li>';
					} #>
					{{{ item_open_wrap }}}
					<# if ( item.image && item.image.id ) {

						var image = {
							id: item.image.id,
							url: item.image.url,
							size: settings.image_size,
							dimension: settings.image_custom_dimension,
						};

						var image_url = elementor.imagesManager.getImageUrl( image );

						if (  image_url ) { #>
							<div class="bdt-price-list-image bdt-width-auto"><img src="{{ image_url }}" alt="{{ item.title }}"></div>
						<# } #>

					<# } #>

					<div class="bdt-price-list-text bdt-width-expand">
						<div>
							<div class="bdt-price-list-header bdt-grid bdt-grid-small bdt-flex-middle" bdt-grid>
								<span class="bdt-price-list-title">{{{ item.title }}}</span>
								<span class="bdt-price-list-separator bdt-width-expand"></span>
							</div>
							<p class="bdt-price-list-description">{{{ item.item_description }}}</p>
						</div>
					</div>
					<div class="bdt-width-auto bdt-flex-inline">
						<span class="bdt-price-list-price">{{{ item.price }}}</span>
					</div>
					{{{ item_close_wrap }}}
			 <# } #>
		</ul>
	<?php }
}
