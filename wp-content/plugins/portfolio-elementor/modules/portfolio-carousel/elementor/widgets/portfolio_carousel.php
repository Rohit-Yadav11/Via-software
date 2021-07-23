<?php
namespace ElpugPortfolio\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 *
 * @since 1.0.0
 */
class ELPUG_Portfolio_Carousel extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'portfolio_carousel';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Elementor Portfolio Carousel', 'elpug' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-elementor-square';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'elpug-elements' ];
	}

	/**
	 * Retrieve the list of scripts the widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return [ 'elpug' ];
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */

	protected function _register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Portfolio Carousel Settings', 'elpug' ),
			]
		);

		$args = array(
			'public'   => true,
		);
		$the_post_types = get_post_types($args);
		$this->add_control(
			'post_type',
			[
				'label' => __( 'Post Type to display (default: elemenfolio)', 'powerfolio' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'elemenfolio',
				'options' => $the_post_types,
			]
		);


		$this->add_control(
			'linkto',
			[
				'label' => __( 'Each project links to', 'powerfolio' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'project',
				'options' => [
					'image' => __( 'Featured Image into Lightbox', 'powerfolio' ),
					'project' => __( 'Project Details Page', 'powerfolio' ),				]
			]
		);

		$this->add_control(
			'type',
			[
				'label' => __( 'Display specific portfolio category', 'powerfolio' ),
				'description' => 'Only works with the "elemenfolio" post type.',
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'On', 'powerfolio' ),
				'label_off' => __( 'Off', 'powerfolio' ),
				'return_value' => 'yes',
			]
		);

		$portfolio_taxonomies = get_terms( array('taxonomy' => 'elemenfoliocategory', 'fields' => 'id=>name', 'hide_empty' => false, ) );
		$this->add_control(
			'taxonomy',
			[
				'label' => __( 'If yes, select wich portfolio category to show', 'powerfolio' ),
				'description' => 'Only works with the "elemenfolio" post type.',
				'type' => Controls_Manager::SELECT,
				'default' => 'yes',
				'options' => $portfolio_taxonomies,
			]
		);

		$this->add_control(
			'hover',
			[
				'label' => __( 'Hover Style', 'powerfolio' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'simple',
				'options' => [
					'simple' => __( 'Simple', 'powerfolio' ),
					'hover1' => __( 'From Bottom', 'powerfolio' ),	
					'hover2' => __( 'From Top', 'powerfolio' ),	
					'hover3' => __( 'From Right', 'powerfolio' ),	
					'hover4' => __( 'From Left', 'powerfolio' ),	
					'hover5' => __( 'Hover Effect 5', 'powerfolio' ),	
					'hover6' => __( 'Special 1', 'powerfolio' ),	
					'hover7' => __( 'Text from Left', 'powerfolio' ),		
					'hover8' => __( 'Text from right', 'powerfolio' ),	
					'hover9' => __( 'Text from Top', 'powerfolio' ),		
					'hover10' => __( 'Text from Bottom', 'powerfolio' ),
					'hover11' => __( 'Zoom Out', 'powerfolio' ),		
					'hover12' => __( 'Card from Left', 'powerfolio' ),	
					'hover13' => __( 'Card from Right', 'powerfolio' ),	
					'hover14' => __( 'Card from Bottom', 'powerfolio' ),
				]
			]
		);		

	

		$this->end_controls_section();

		$this->start_controls_section(
			'section_item_description',
			[
				'label' => __( 'Item', 'powerfolio' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		//Hover: Background color

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'bgcolor',
				'label' => __( 'Hover: Background Color', 'powerfolio' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .portfolio-item-infos-wrapper',
			]
		);

		

		//Text Transform
		$this->add_control(
			'text_transform',
			[
				'label' => __( 'Item Description: Text Transform', 'powerfolio' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'' => __( 'None', 'powerfolio' ),
					'uppercase' => __( 'UPPERCASE', 'powerfolio' ),
					'lowercase' => __( 'lowercase', 'powerfolio' ),
					'capitalize' => __( 'Capitalize', 'powerfolio' ),
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-item-infos-wrapper' => 'text-transform: {{VALUE}};',
				],
			]
		);

		//Text Aligment
		$this->add_control(
			'text_align',
			[
				'label' => __( 'Item Description: Text Align', 'powerfolio' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'center' => __( 'Center', 'powerfolio' ),
					'left' => __( 'Left', 'powerfolio' ),
					'right' => __( 'Right', 'powerfolio' ),
				],
				'selectors' => [
					'{{WRAPPER}} .portfolio-item-infos-wrapper' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'vertical_align',
			[
				'label' => __( 'Item Description: Vertical Align', 'powerfolio' ),
				'type' => Controls_Manager::SELECT,
				'default' => '50%',
				'options' => [
					'60px' => __( 'Top', 'powerfolio' ),
					'50%' => __( 'Center', 'powerfolio' ),
					'70%' => __( 'Bottom', 'powerfolio' ),
				],
				'selectors' => [
					'{{WRAPPER}} .elpt-portfolio-content .portfolio-item-infos' => 'top: {{VALUE}};',
				],
			]
		);

		//Border Radius
		$this->add_control(
			'border_radius',
			[
				'label' => __( 'Item: Border Radius', 'powerfolio' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .elpt-portfolio-content .portfolio-item' => 'border-radius: {{SIZE}}{{UNIT}};',
					//'{{WRAPPER}} .elpt-portfolio-content .portfolio-item img' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);
		

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings();		

		//$style = $settings['Portfolio_carousel_style'];

		//$carousellist = $this->get_settings( 'Portfolio_carousel' );

		?>

		<?php echo do_shortcode('[portfolio-carousel hover="'.$settings['hover'].'" post_type="'.$settings['post_type'].'" type="'.$settings['type'] .'" taxonomy="'.$settings['taxonomy'].'" linkto="'.$settings['linkto'].'"]'); ?>

		<?php wp_reset_postdata(); ?>
	

		<?php
	}

	/**
	 * Render the widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	/*protected function _content_template() {
		$sliderheight = $settings['slider_height'];
		?>
		
		<div class="pando-slideshow">
			<?php echo do_shortcode('[pando-slider heightstyle="'.$sliderheight.'"]'); ?>
		</div>


		<?php
	}*/
}