<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use \Elementor\Core\Schemes;
use \Elementor\Core\Settings\Manager;

/**
 * Section Title - Ajdethemes Elementor Widget.
 *
 * @since 1.0.0
 */
class Ajdethemes_Widget_Portfolio extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve oEmbed widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'adri-ajdethemes-elements-portfolio';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve oEmbed widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Portfolio', 'adri-ajdethemes-elements' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve oEmbed widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the oEmbed widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'ajdethemes-elements' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'image', 'feature', 'service', 'image box', 'content' ];
	}
	

	/**
	 * Register oEmbed widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() {

		$this->start_controls_section(
			'section_feature_image',
			[
				'label' => __( 'Portfolio', 'adri-ajdethemes-elements' ),
			]
		);

		$this->add_control(
			'style',
			[
				'label'   => __( 'Portfolio Style', 'adri-ajdethemes-elements' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'classic',
				'options' => [
					'classic'  => __( 'Classic', 'adri-ajdethemes-elements' ),
					'neue'  => __( 'Neue', 'adri-ajdethemes-elements' ),
					'minimal'  => __( 'Minimal', 'adri-ajdethemes-elements' ),
				],
			]
		);

		$this->add_control(
			'grid',
			[
				'label'   => __( 'Portfolio Grid', 'adri-ajdethemes-elements' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'col-lg-4',
				'options' => [
					'col-md-6'  => __( '2 Columns', 'adri-ajdethemes-elements' ),
					'col-lg-4 col-md-6'  => __( '3 Columns', 'adri-ajdethemes-elements' ),
					'col-lg-3 col-md-6'  => __( '4 Columns', 'adri-ajdethemes-elements' ),
				],
			]
		);
				
		$this->add_control(
			'grid_gap',
			[
				'label' => __( 'Grid Gap', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => __( 'On', 'adri-ajdethemes-elements' ),
				'label_off' => __( 'Off', 'adri-ajdethemes-elements' ),
			]
		);

		$this->add_control(
			'has_filters',
			[
				'label' => __( 'Show filters', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'On', 'adri-ajdethemes-elements' ),
				'label_off' => __( 'Off', 'adri-ajdethemes-elements' ),
			]
		);

		$this->add_control(
			'all_filter',
			[
				'label' => __( '"All" Filter Text', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'All', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Enter text for the "all" filter', 'adri-ajdethemes-elements' ),
				'label_block' => true,
				'condition' => [
					'has_filters' => 'yes',
				],
			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'image',
			[
				'label' => __( 'Choose Image', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => 'https://placehold.it/760x720',
				],
			]
		);

		$repeater->add_control(
			'title',
			[
				'label' => __( 'Title', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Project title here', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Enter a title', 'adri-ajdethemes-elements' ),
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
			]
		);
		
		$repeater->add_control(
			'subtitle',
			[
				'label' => __( 'Subtitle', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Business', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Optional subtitle', 'adri-ajdethemes-elements' ),
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'lightbox_on',
			[
				'label' => __( 'Open in lightbox', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => __( 'On', 'adri-ajdethemes-elements' ),
				'label_off' => __( 'Off', 'adri-ajdethemes-elements' ),
				'separator' => 'before',
			]
		);
		
		$repeater->add_control(
			'link',
			[
				'label' => __( 'Add Link', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'https://your-project-page.com', 'adri-ajdethemes-elements' ),
				'condition' => [
					'lightbox_on' => '',
				],
			]
		);

		$repeater->add_control(
			'video_lightbox_on',
			[
				'label' => __( 'Open in lightbox as video', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'On', 'adri-ajdethemes-elements' ),
				'label_off' => __( 'Off', 'adri-ajdethemes-elements' ),
				'condition' => [
					'lightbox_on' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'video_link',
			[
				'label' => __( 'Add Video Link', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => 'https://youtube.com/your-video',
				'condition' => [
					'video_lightbox_on' => 'yes',
				],
			]
		);

		$repeater->add_control(
			'link_text',
			[
				'label' => __( 'Link Text', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Expand', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Add link text', 'adri-ajdethemes-elements' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'pf_filters',
			[
				'label' => __( 'Add Filters', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Business Advisory', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Add filters separated by empty space', 'adri-ajdethemes-elements' ),
				'label_block' => true,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'portfolio_items',
			[
				'label' => __( 'Portfolio Items', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'separator' => 'before',
				'default' => [
					[
						'image' => __( 'https://placehold.it/570x390', 'adri-ajdethemes-elements' ),
						'title' => __( 'Project title 1', 'adri-ajdethemes-elements' ),
						'subtitle' => __( 'Consulting', 'adri-ajdethemes-elements' ),
						'lightbox_on' => 'yes',
						'video_lightbox_on' => '',
						'link' => 'https://your-project-page.com',
						'pf_filters' => 'Business Consulting'
					],					
					[
						'image' => __( 'https://placehold.it/570x390', 'adri-ajdethemes-elements' ),
						'title' => __( 'Project title 2', 'adri-ajdethemes-elements' ),
						'subtitle' => __( 'Finance', 'adri-ajdethemes-elements' ),
						'lightbox_on' => 'yes',
						'video_lightbox_on' => '',
						'link' => 'https://your-project-page.com',
						'pf_filters' => 'Marketing Consulting'
					],
					[
						'image' => __( 'https://placehold.it/570x390', 'adri-ajdethemes-elements' ),
						'title' => __( 'Project title 3', 'adri-ajdethemes-elements' ),
						'subtitle' => __( 'Business', 'adri-ajdethemes-elements' ),
						'lightbox_on' => 'yes',
						'video_lightbox_on' => '',
						'link' => 'https://your-project-page.com',
						'pf_filters' => 'WebDesign Advisory Marketing'
					],
					
				],
				'title_field' => '{{{ title }}}',
			]
        );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_content',
			[
				'label' => __( 'Inline Icon', 'adri-ajdethemes-elements' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_size',
			[
				'label' => __( 'Title HTML Tag', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				],
				'default' => 'h4',
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Title Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .pf-neue .pf-img-link .pf-caption .pf-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pf-classic .pf-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pf-minimal .pf-title' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'subtitle_color',
			[
				'label' => __( 'Subtitle Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'condition' => [
					'style' => ['classic', 'minimal'],
				],
				'selectors' => [
					'{{WRAPPER}} .pf-classic .pf-subtitle' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pf-minimal .pf-subtitle' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'primary_color',
			[
				'label' => __( 'Primary Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .pf-neue:hover' => 'box-shadow: 15px 15px 0 {{VALUE}};',
					'{{WRAPPER}} .pf-neue .pf-img-link .pf-caption .pf-accent-line' => 'background: {{VALUE}};',
					'{{WRAPPER}} .pf-neue .pf-img-link .pf-caption .pf-link-plus' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pf-link-symbol' => 'background: {{VALUE}};',
					
				],
			]
		);
		
		$this->add_control(
			'secondary_color',
			[
				'label' => __( 'Secondary Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .pf-neue .pf-img-link:after' => 'background: {{VALUE}};',
					'{{WRAPPER}} .pf-link-symbol span' => 'background: {{VALUE}};',
					'{{WRAPPER}} .pf-link-symbol span:before' => 'background: {{VALUE}};',
					'{{WRAPPER}} .pf-classic .pf-img-link:after' => 'background: {{VALUE}};',
					'{{WRAPPER}} .open-lightbox-video .pf-link-symbol span:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'overlay_color',
			[
				'label' => __( 'Overlay (hover)', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .pf-classic .pf-img-link:after' => 'background: {{VALUE}};',
					'{{WRAPPER}} .pf-neue .pf-img-link:after' => 'background: {{VALUE}};',
					'{{WRAPPER}} .pf-minimal .pf-img-link:after' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render oEmbed widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ($settings['has_filters'] === 'yes') {
			$filters = $this->get_portfolio_filters();
			echo $filters;
		}

		$html = '';
		$settings['grid_gap'] === 'yes' ? $html .= '<div class="row portfolio_grid">' : $html .= '<div class="row portfolio_grid no-gutters">';

		foreach( $settings['portfolio_items'] as $index => $item ) {

			$image_setting_key = $this->get_repeater_setting_key( 'image', 'portfolio_items', $index );
			$title_setting_key = $this->get_repeater_setting_key( 'title', 'portfolio_items', $index );
			$subtitle_setting_key = $this->get_repeater_setting_key( 'subtitle', 'portfolio_items', $index );

			$this->add_render_attribute( $title_setting_key, [
                'class' => [ 'pf-title' ],
            ] );
			$this->add_render_attribute( $subtitle_setting_key, [
                'class' => [ 'pf-subtitle' ],
			] );
			
			$has_lightbox = '';
			$link_url = $item['link']['url'];
			$lightbox_title = '';

			if ( $item['lightbox_on'] === 'yes' ) {

				$has_lightbox = 'open-lightbox';
				$link_url = $item['image']['url'];
				$lightbox_title = 'title="' . $item['title'] . '"';

			}

			if ( $item['video_lightbox_on'] === 'yes' ) {

				$has_lightbox .= ' open-lightbox-video';
				$link_url = $item['video_link']['url'];

			}
			
			$link_tag = '<a href="' . $link_url . '" class="pf-img-link ' .  $has_lightbox . '" ' . $lightbox_title . '>';

			$item['subtitle'] ? $subtitle_html = sprintf( '<span %2$s>%1$s</span>', $item['subtitle'], $this->get_render_attribute_string( $subtitle_setting_key ) ) : $subtitle_html = '';

			if ( $settings['style'] === 'minimal' ) {

				$html .= sprintf( '<div class="%1$s pf-item %2$s">', $settings['grid'], $item['pf_filters'] );
				$html .= '<figure class="pf-minimal">';
				$html .= $link_tag;
				$html .= sprintf( '<img src="%1$s" alt="%2$s">', $item['image']['url'], $item['title'] );
				$html .= '<div class="pf-link-symbol"><span></span></div>';
				$html .= '</a>';
				$html .= '<figcaption>';
				$html .= sprintf( '<%3$s %2$s>%1$s</%3$s>', $item['title'], $this->get_render_attribute_string( $title_setting_key ), $settings['title_size'] );
				$html .= $subtitle_html;
				$html .= '</figcaption>';
				$html .= '</figure>';
				$html .= '</div>';

			} elseif ( $settings['style'] === 'neue' ) {

				$html .= sprintf( '<div class="%1$s pf-item %2$s">', $settings['grid'], $item['pf_filters'] );

				$html .= '<div class="pf-neue">';
				$html .= $link_tag;
				$html .= '<div class="pf-img-wrapper">';
				$html .= sprintf( '<img src="%1$s" alt="%2$s">', $item['image']['url'], $item['title'] );
				$html .= '</div>';

				$html .= '<div class="pf-caption">';
				$html .= '<span class="pf-accent-line"></span>';
				$html .= sprintf( '<%3$s %2$s>%1$s</%3$s>', $item['title'], $this->get_render_attribute_string( $title_setting_key ), $settings['title_size'] );
				$html .= sprintf( '<div class="pf-link-plus">%1$s <div class="pf-link-symbol"><span></span></div></div>', $item['link_text'] );
				$html .= '</div>';
				$html .= '</a>';
				$html .= '</div>';

				$html .= '</div>';

			} else {

				$html .= sprintf( '<div class="%1$s pf-item %2$s">', $settings['grid'], $item['pf_filters'] );
				$html .= '<figure class="pf-classic">';
				$html .= $link_tag;
				$html .= sprintf( '<img src="%1$s" alt="%2$s">', $item['image']['url'], $item['title'] );
				$html .= '<div class="pf-link-symbol"><span></span></div>';
				$html .= '</a>';
				$html .= '<figcaption>';
				$html .= sprintf( '<%3$s %2$s>%1$s</%3$s>', $item['title'], $this->get_render_attribute_string( $title_setting_key ), $settings['title_size'] );
				$html .= $subtitle_html;
				$html .= '</figcaption>';
				$html .= '</figure>';
				$html .= '</div>';

			}
		}

		$html .= '</div>';

		echo $html;
		
		?>
		<?php
	}

	/**
	 * Render the portfolio filters.
	 * 
	 * 
	 * 1. Looping trough each portfolio item, getting all filters
	 * in separate array for each portfolio item (using explode);
	 * 
	 * 2. To remove the duplicates, we loop trough the filters, this
	 * give a string with the filters separated by ",";
	 * 
	 * 3. Then we are removing the duplicates (array_unique) and creating 
	 * array with explode again.
	 * 
	 * 4. Then finally, loop trough that array to output the filter button 
	 * markup without the duplicates;
	 * 
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function get_portfolio_filters() {
		$settings = $this->get_settings_for_display();

		$filters_unique = '';
		$filters_output = '
		<div class="pf-filters">
			<button data-filter="*" class="is-checked">' . $settings['all_filter'] . '</button>';
		
		foreach( $settings['portfolio_items'] as $index => $item ) {
			$all_filters = explode( ' ', $item['pf_filters'] );
			
			foreach( $all_filters as $filter ) {
				$filters_unique .= $filter . ',';
			}
		}

		$filters_markup =  array_unique( explode( ',', $filters_unique ) );

		foreach( $filters_markup as $filter_item ) {
			$filters_output .= '<button data-filter=".' . $filter_item . '">' . $filter_item . '</button>';
		}

		$filters_output .= '</div>';
		
		return $filters_output;
	}
	
    /**
	 * Render image box widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 2.9.0
	 */
    protected function content_template() {
	?>

		<# if ( settings.has_filters ) {#>
			<div class="pf-filters">
				<button data-filter="*" class="is-checked">{{{ settings.all_filter }}}</button>
				<button data-filter=".other">Filter 1</button>
				<button data-filter=".other">Filter 2</button>
				<button data-filter=".other">Consulting</button>
				<button data-filter=".other">Marketing</button>
			</div>
		<# } #>


		<# if ( settings.grid_gap === 'yes' ) { #>
		<div class="row portfolio_grid">
		<# } else { #>
		<div class="row portfolio_grid no-gutters">
		<# } #>

		<# _.each( settings.portfolio_items, function( item, index ) {

			var has_lightbox = '';
			if ( item.video_lightbox_on === 'yes' ) {
				has_lightbox += ' open-lightbox-video';
			}

			
			if ( settings.style === 'minimal' ) { #>

				<div class="{{{ settings.grid }}} pf-item {{{ item.pf_filters }}}">
					<figure class="pf-minimal">
						<a href="#" class="pf-img-link {{{ has_lightbox }}}">
							<img src="{{{ item.image.url }}}" alt="{{{ item.title }}}">
							<div class="pf-link-symbol"><span></span></div>
						</a>
						<figcaption>
							<{{{ settings.title_size }}} class="pf-title">{{{ item.title }}}</{{{ settings.title_size }}}>
							<# if ( item.subtitle ) { #>
								<span class="pf-subtitle">{{{ item.subtitle }}}</span>
							<# } #>
						</figcaption>
					</figure>
				</div>

			<# } else if ( settings.style === 'neue' ) { #>

				<div class="{{{ settings.grid }}} pf-item {{{ item.pf_filters }}}">
					<div class="pf-neue">
						<a href="#" class="pf-img-link {{{ has_lightbox }}}">
							<div class="pf-img-wrapper">
								<img src="{{{ item.image.url }}}" alt="{{{ item.title }}}">
							</div>
							<div class="pf-caption">
								<span class="pf-accent-line"></span>
								<{{{ settings.title_size }}} class="pf-title">{{{ item.title }}}</{{{ settings.title_size }}}>
								<span class="pf-link-plus">{{{ item.link_text }}} <div class="pf-link-symbol"><span></span></div></span>
							</div>
						</a>
					</div>
				</div>

			<# } else { #>
				
				<div class="{{{ settings.grid }}} pf-item {{{ item.pf_filters }}}">
					<figure class="pf-classic">
						<a href="#" class="pf-img-link {{{ has_lightbox }}}">
							<img src="{{{ item.image.url }}}" alt="{{{ item.title }}}">
							<div class="pf-link-symbol"><span></span></div>
						</a>
						<figcaption>
							<{{{ settings.title_size }}} class="pf-title">{{{ item.title }}}</{{{ settings.title_size }}}>
							<# if ( item.subtitle ) { #>
								<span class="pf-subtitle">{{{ item.subtitle }}}</span>
							<# } #>
						</figcaption>
					</figure>
				</div>

			<# } #>

		<# }); #>

		</div>

        <?php
    }

}