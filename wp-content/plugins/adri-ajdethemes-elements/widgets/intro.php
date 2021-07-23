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
class Ajdethemes_Widget_Intro extends \Elementor\Widget_Base {

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
		return 'adri-ajdethemes-elements-intro';
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
		return __( 'Intro', 'adri-ajdethemes-elements' );
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
		return 'eicon-posts-group';
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
		return [ 'about', 'intro', 'welcome', 'feature', 'description', 'text' ];
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
			'section_intro',
			[
				'label' => __( 'Intro Layout', 'adri-ajdethemes-elements' ),
			]
		);

		$this->add_control(
			'style',
			[
				'label'   => __( 'Select Style', 'adri-ajdethemes-elements' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'style-2',
				'options' => [
					'style-1' => __( 'Style 1', 'adri-ajdethemes-elements' ),
					'style-2' => __( 'Style 2', 'adri-ajdethemes-elements' ),
					'style-3' => __( 'Style 3', 'adri-ajdethemes-elements' ),
					'style-img'   => __( 'Style - Image', 'adri-ajdethemes-elements' ),
					'style-img-2' => __( 'Style - Image 2', 'adri-ajdethemes-elements' ),
				],
			]
		);
		
		$this->add_control(
			'has_parallax',
			[
				'label' => __( 'Parallax Effect', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'label_off',
				'label_on' => __( 'Enable', 'adri-ajdethemes-elements' ),
				'label_off' => __( 'Disable', 'adri-ajdethemes-elements' ),
				'condition' => [
					'style' => ['style-1', 'style-2', 'style-3', 'style-img-2'],
				],
			]
		);

		$this->add_control(
			'image_s1',
			[
				'label' => __( 'Image', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => 'https://placehold.it/615x620',
				],
				'condition' => [
					'style' => 'style-1',
				],
			]
		);
		
		$this->add_control(
			'image_s2',
			[
				'label' => __( 'Image', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => 'https://placehold.it/770x590',
				],
				'condition' => [
					'style' => 'style-2',
				],
			]
		);

		$this->add_control(
			'image_1',
			[
				'label' => __( 'Image 1', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => 'https://placehold.it/300x150/eee/ccc',
				],
				'condition' => [
					'style' => 'style-3',
				]
			]
		);
		
		$this->add_control(
			'image_2',
			[
				'label' => __( 'Image 2', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => 'https://placehold.it/370x300/333/ccc',
				],
				'condition' => [
					'style' => 'style-3',
				]
			]
		);
		
		$this->add_control(
			'image_3',
			[
				'label' => __( 'Image 3', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => 'https://placehold.it/240x240/ddd/aaa',
				],
				'condition' => [
					'style' => 'style-3',
				]
			]
		);

		$this->add_control(
			'image_si',
			[
				'label' => __( 'Image', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => 'https://placehold.it/570x600',
				],
				'condition' => [
					'style' => 'style-img',
				],
			]
		);
		
		$this->add_control(
			'image_si2',
			[
				'label' => __( 'Image', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => 'https://placehold.it/470x410',
				],
				'condition' => [
					'style' => 'style-img-2',
				],
			]
		);
		
		$this->add_control(
			'image_deco',
			[
				'label' => __( 'Choose Decoration Graphic', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => 'https://placehold.it/200x490/ddd/fff',
				],
				'condition' => [
					'style' => [ 'style-2', 'style-img-2' ],
				],
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Title example here', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Enter your title', 'adri-ajdethemes-elements' ),
				'label_block' => true,
				'condition' => [
					'style' => [ 'style-1', 'style-2', 'style-3' ],
				],
			]
		);
		
		$this->add_control(
			'subtitle',
			[
				'label' => __( 'Subtitle', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Title example here', 'adri-ajdethemes-elements' ),
				'placeholder' => __( 'Enter your title', 'adri-ajdethemes-elements' ),
				'label_block' => true,
				'condition' => [
					'style' => [ 'style-1', 'style-2', 'style-3' ],
				],
			]
		);

		$this->add_control(
			'description',
			[
				'label' => __( 'Description', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'default' => '<p>Nulla fugiat incididunt exercitation fugiat sint magna dolor nisi occaecat laborum do duis adipisicing cupidatat este. Laboris aute occaecat labore ut excepteur quis elit anim. Sunt enim sit sit anim magna excepteur est duis est eiusmod dolore tempor laboris.</p><br/><p>Enim dolore aliquip incididunt magna consequat Lorem magna ea ea cupidatat pariatur deserunt. Esse ea voluptate reprehenderit excepteur incididunt lorem reprehenderit ea labore ex aliqua.</p>',
				'placeholder' => __( 'Enter your description', 'adri-ajdethemes-elements' ),
				'condition' => [
					'style' => 'style-1',
				],
			]
		);
		
		$this->add_control(
			'description_s2',
			[
				'label' => __( 'Description', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'default' => '<p>Velit consequat eu cupidatat irure qui amit dolore qui consectetur in non culpa labore duis. Velit ad nisi dolore fugiat pariatur amit consequat. Velit consequat eu cupidatat estirure qui dolore qui consectetur.</p><p>Velit consequat eu cupidatat irure qui este dolore qui consectetur in non culpa labore duis. Velit ad nisi dolore fugiat pariatur amit consequat. Velit consequat eu cupidatat.</p><br/><a href="#" class="btn btn-reg">Learn More</a>',
				'placeholder' => __( 'Enter your description', 'adri-ajdethemes-elements' ),
				'condition' => [
					'style' => 'style-2',
				],
			]
		);

		$this->add_control(
			'show_footer_info',
			[
				'label' => __( 'Show Description Footer', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'label_off',
				'label_on' => __( 'Show', 'adri-ajdethemes-elements' ),
				'label_off' => __( 'Hide', 'adri-ajdethemes-elements' ),
				'separator' => 'before',
				'condition' => [
					'style' => 'style-1',
				],
			]
		);

		$this->add_control(
			'footer_info',
			[
				'label' => __( 'Footer Info', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'default' => '<h5>Jorge Watson</h5><span>Founder &amp; CEO</span><br/><img style="margin-top: 10px;" src="https://placehold.it/128x60" alt="Signature" />',
				'placeholder' => __( 'Enter your info', 'adri-ajdethemes-elements' ),
				'condition' => [
					'show_footer_info' => 'yes',
					'style' => 'style-1',
				],
			]
		);

		$this->add_control(
			'show_footer_cta',
			[
				'label' => __( 'Show Description Footer', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'label_off',
				'label_on' => __( 'Show', 'adri-ajdethemes-elements' ),
				'label_off' => __( 'Hide', 'adri-ajdethemes-elements' ),
				'condition' => [
					'style' => 'style-1',
				],
			]
		);

		$this->add_control(
			'footer_cta',
			[
				'label' => __( 'Footer CTA', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'default' => '<h5>Call to ask any question</h5><strong><a style="color: #222222;" href="tel:1234567890">1234-567-890</a></strong>',
				'placeholder' => __( 'Enter call to action message', 'adri-ajdethemes-elements' ),
				'condition' => [
					'show_footer_cta' => 'yes',
					'style' => 'style-1',
				],
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
			'primary_color',
			[
				'label' => __( 'Primary Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .ft-intro .ft-i-img-wrapper:before' => 'background: {{VALUE}};',
					'{{WRAPPER}} .ft-intro-3 .ft-i-title' => 'background: {{VALUE}};',
					'{{WRAPPER}} .ft-intro-img' => 'background: {{VALUE}};',
					'{{WRAPPER}} .ft-intro-img-2:before' => 'background: {{VALUE}};',
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
					'{{WRAPPER}} .ft-image-frame:hover .ft-img-wrapper:before' => 'background: {{VALUE}};', 
					'{{WRAPPER}} .ft-image-frame:focus .ft-img-wrapper:before' => 'background: {{VALUE}};', 
					'{{WRAPPER}} .focus-on.ft-image-frame .ft-img-wrapper:before' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'intro_card_bg',
			[
				'label' => __( 'Intro Text Background', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .ft-intro figcaption' => 'background: {{VALUE}};',
					'{{WRAPPER}} .ft-intro-2 figcaption' => 'background: {{VALUE}};',
					'{{WRAPPER}} .ft-intro-3 .ft-i-title' => 'background: {{VALUE}};',
				],
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
					'{{WRAPPER}} .section-title .st-title' => 'color: {{VALUE}};',
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
				'selectors' => [
					'{{WRAPPER}} .section-title .st-subtitle' => 'color: {{VALUE}};',
				],
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
		// Subtitle
        $this->add_render_attribute( 'subtitle', 'class', 'st-subtitle' );
        $this->add_inline_editing_attributes( 'subtitle', 'none' );
        // Title
        $this->add_render_attribute( 'title', 'class', 'st-title' );
        $this->add_inline_editing_attributes( 'title' );
        // Description
        $this->add_render_attribute( 'description', 'class', 'ft-i-description' );
        $this->add_inline_editing_attributes( 'description' );
        // Description S2
        $this->add_render_attribute( 'description_s2', 'class', 'ft-i-description' );
        $this->add_inline_editing_attributes( 'description_s2' );
        // Footer Info
        $this->add_render_attribute( 'footer_info', 'class', 'ft-i-footer-info' );
        $this->add_inline_editing_attributes( 'footer_info' );
        // Footer CTA
        $this->add_render_attribute( 'footer_cta', 'class', 'ft-i-footer-cta' );
        $this->add_inline_editing_attributes( 'footer_cta' );

		switch ( $settings['style'] ) {
			case 'style-1':

				$html = '<figure class="ft-intro">';
				$html .= '<div class="ft-i-img-wrapper">';
				$html .= sprintf( '<img %3$s src="%1$s" alt="%2$s">', $settings['image_s1']['url'], $settings['title'], $settings['has_parallax'] ? 'class="rellax" data-rellax-speed="-1"' : '' );
				$html .= '</div>';

				$html .= $settings['has_parallax'] ? '<figcaption class="rellax" data-rellax-speed="3">' : '<figcaption>';
				$html .= '<div class="ft-i-title">';
				$html .= '<div class="section-title st-underline">';
				$html .= $settings['subtitle'] ? sprintf( '<span %2$s>%1$s</span>', $settings['subtitle'], $this->get_render_attribute_string( 'subtitle' ) ) : '';
				$html .= $settings['title'] ? sprintf( '<%2$s %3$s>%1$s</%2$s>', $settings['title'], $settings['title_size'], $this->get_render_attribute_string( 'title' ) ) : '';
				$html .= '</div>';
				$html .= '</div>';

				$html .= sprintf( '<div %2$s>%1$s</div>', $settings['description'], $this->get_render_attribute_string( 'description' ) );

				$html .= '<div class="ft-i-footer">';

				if ( $settings['show_footer_info'] === 'yes' ) {
					$html .= sprintf( '<div %2$s>%1$s</div>', $settings['footer_info'], $this->get_render_attribute_string( 'footer_info' ) );
				}

				if ( $settings['show_footer_cta'] === 'yes' ) {
					$html .= sprintf( '<div %2$s>%1$s</div>', $settings['footer_cta'], $this->get_render_attribute_string( 'footer_cta' ) );
				}

				$html .= '</div>';
				$html .= '</figcaption>';
				$html .= '</figure>';

				echo $html;

			break;
				
			case 'style-3':

				$html = '<div class="ft-intro-3">';
				$html .= '<div class="ft-img-wrapper">';

				$html .= sprintf( '<img %3$s src="%1$s" alt="%2$s">', 
							$settings['image_1']['url'], 
							$settings['title'], 
							$settings['has_parallax'] ? 'class="ft-i-img rellax" data-rellax-speed="1"' : 'class="ft-i-img"' 
						);
				$html .= sprintf( '<img %3$s src="%1$s" alt="%2$s">', 
							$settings['image_2']['url'], 
							$settings['title'],
							$settings['has_parallax'] ? 'class="ft-i-img-2 rellax" data-rellax-speed="-1"' : 'class="ft-i-img-2"'
						);
				$html .= sprintf( '<img %3$s src="%1$s" alt="%2$s">', 
							$settings['image_3']['url'], 
							$settings['title'],
							$settings['has_parallax'] ? 'class="ft-i-img-3 rellax" data-rellax-speed="-1"' : 'class="ft-i-img-3"'
						);

				$html .= '<div class="ft-i-title">';
				$html .= '<div class="section-title">';
				$html .= $settings['subtitle'] ? sprintf( '<span %2$s>%1$s</span>', $settings['subtitle'], $this->get_render_attribute_string( 'subtitle' ) ) : '';
				$html .= $settings['title'] ? sprintf( '<%2$s %3$s>%1$s</%2$s>', $settings['title'], $settings['title_size'], $this->get_render_attribute_string( 'title' ) ) : '';	
				$html .= '</div>';
				$html .= '</div>';

				$html .= '</div>';
				$html .= '</div>';

				echo $html;

			break;
				
			case 'style-img':
				
				$html = '<div class="ft-intro-img focus-on">';
				$html .= sprintf( '<img src="%1$s" alt="%2$s">', $settings['image_si']['url'], $settings['title'] );
				$html .= '</div>';
				
				echo $html;	

			break;
				
			case 'style-img-2':
				
				$html = '<div class="ft-intro-img-2 on-scroll focus-on">';
				$html .= sprintf( '<img %3$s src="%1$s" alt="%2$s">', 
							$settings['image_si2']['url'], 
							$settings['title'],
							$settings['has_parallax'] === true ? 'class="ft-i-img-main rellax" data-rellax-speed="-1"' : 'class="ft-i-img-main"'
						);
				$html .= sprintf( '<img %3$s src="%1$s" alt="%2$s">', 
							$settings['image_deco']['url'], 
							$settings['title'],
							$settings['has_parallax'] === true ? 'class="ft-i-deco rellax" data-rellax-speed="2"' : 'class="ft-i-deco"'
						);
				$html .= '</div>';
				
				echo $html;

			break;
			
			default:

				$html = '<figure class="ft-intro-2">';
				$html .= '<div class="ft-i-img-wrapper">';
				$html .= sprintf( '<img class="img-deco" src="%1$s" alt="%2$s">', $settings['image_deco']['url'], $settings['title'] );
				$html .= sprintf( '<img %3$s src="%1$s" alt="%2$s">', $settings['image_s2']['url'], $settings['title'], $settings['has_parallax'] ? 'class="img-main rellax" data-rellax-speed="2"' : 'class="img-main"' );
				$html .= '</div>';

				$html .= $settings['has_parallax'] ? '<figcaption class="rellax" data-rellax-speed="-1">' : '<figcaption>';
				$html .= '<div class="ft-i-title">';
				$html .= '<div class="section-title">';
				$html .= $settings['subtitle'] ? sprintf( '<span %2$s>%1$s</span>', $settings['subtitle'], $this->get_render_attribute_string( 'subtitle' ) ) : '';
				$html .= $settings['title'] ? sprintf( '<%2$s %3$s>%1$s</%2$s>', $settings['title'], $settings['title_size'], $this->get_render_attribute_string( 'title' ) ) : '';
				$html .= '</div>';
				$html .= '</div>';

				$html .= sprintf( '<div %2$s>%1$s</div>', $settings['description_s2'], $this->get_render_attribute_string( 'description_s2' ) );
				$html .= '</figcaption>';
				$html .= '</figure>';

				echo $html;

			break;
		}
		?>


		<?php
    }
    
    /**
	 * Render image box widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 2.9.0
	 */
    protected function content_template() {}

}