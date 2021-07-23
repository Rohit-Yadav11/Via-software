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
class Ajdethemes_Widget_Blog_Posts extends \Elementor\Widget_Base {

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
		return 'blog-posts';
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
		return __( 'Blog Posts', 'adri-ajdethemes-elements' );
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
		return 'eicon-wordpress';
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
		return [ 'blog', 'post', 'article', 'wordpress' ];
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
				'label' => __( 'Blog Posts', 'adri-ajdethemes-elements' ),
			]
		);

		$this->add_control(
			'post_layout',
			[
				'label'   => __( 'Select Style', 'adri-ajdethemes-elements' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'post-col',
				'options' => [
					'post-col'  => __( 'Columns', 'adri-ajdethemes-elements' ),
					'post-min'  => __( 'Minimal', 'adri-ajdethemes-elements' ),
				],
			]
        );

		$this->add_control(
			'post_category',
			[
				'label' => __( 'Show by Category', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Enter a category', 'adri-ajdethemes-elements' ),
				'label_block' => true,
			]
		);
		
		$this->add_control(
			'post_tags',
			[
				'label' => __( 'Show by Tabs (slug/s)', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => __( 'Enter slug for a tab/s, separated by ,', 'adri-ajdethemes-elements' ),
				'label_block' => true,
			]
		);
		
		$this->add_control(
			'post_max',
			[
				'label' => __( 'Number of posts', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 3,
				'placeholder' => 3,
				'label_block' => true,
			]
		);
		
		$this->add_control(
			'excerpt_length',
			[
				'label' => __( 'Excerpt length (words)', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'default' => 30,
				'placeholder' => 30,
				'label_block' => true,
				'condition' => [
					'post_layout' => 'post-col',
					'show_excerpt'=> 'yes'
				]
			]
		);

		$this->add_control(
			'show_excerpt',
			[
				'label' => __( 'Show Excerpt', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => __( 'Show', 'adri-ajdethemes-elements' ),
				'label_off' => __( 'Hide', 'adri-ajdethemes-elements' ),
				'condition' => [
					'post_layout' => 'post-col',
				]
			]
		);
		
		$this->add_control(
			'show_author',
			[
				'label' => __( 'Show Author', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => __( 'Show', 'adri-ajdethemes-elements' ),
				'label_off' => __( 'Hide', 'adri-ajdethemes-elements' ),
				'condition' => [
					'post_layout' => 'post-min',
				]
			]
		);
		
		$this->add_control(
			'show_date',
			[
				'label' => __( 'Show Date', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => __( 'Show', 'adri-ajdethemes-elements' ),
				'label_off' => __( 'Hide', 'adri-ajdethemes-elements' ),
			]
		);
		
		$this->add_control(
			'show_category',
			[
				'label' => __( 'Show Category', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => __( 'Show', 'adri-ajdethemes-elements' ),
				'label_off' => __( 'Hide', 'adri-ajdethemes-elements' ),
			]
		);
		
		$this->add_control(
			'show_btn',
			[
				'label' => __( 'Show Button (Read More)', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => __( 'Show', 'adri-ajdethemes-elements' ),
				'label_off' => __( 'Hide', 'adri-ajdethemes-elements' ),
				'condition' => [
					'post_layout' => 'post-col',
				]
			]
		);

		$this->add_control(
			'post_btn_text',
			[
				'label' => __( 'Post button text', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' 		=> __( 'Read More', 'adri-ajdethemes-elements' ),
				'placeholder' 	=> __( 'Enter text for the button', 'adri-ajdethemes-elements' ),
				'label_block' 	=> true,
				'condition' => [
					'post_layout' => 'post-col',
					'show_btn'	  => 'yes'
				]
			]
		);
		
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_content',
			[
				'label' => __( 'Style Blog Posts', 'adri-ajdethemes-elements' ),
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
					'{{WRAPPER}} article.post-col .post-date' => 'background: {{VALUE}};',
					'{{WRAPPER}} article.post-col .post-thumbnail-link:before' => 'background: {{VALUE}};',
					'{{WRAPPER}} article.post-min .post-title a' => 'background-image: linear-gradient({{VALUE}}, {{VALUE}});',
					'{{WRAPPER}} .btn-txt-arr .arr-box' => 'background: {{VALUE}};',
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
					'{{WRAPPER}} article.post-col .post-date' => 'color: {{VALUE}};', 
					'{{WRAPPER}} article.post-col .post-title, article.post-min .post-title' => 'color: {{VALUE}};', 
					'{{WRAPPER}} .btn-txt-arr' => 'color: {{VALUE}};', 
				],
			]
		);

		$this->add_control(
			'excerpt_color',
			[
				'label' => __( 'Excerpt Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'condition' => [
					'post_layout' => 'post-col',
					'show_excerpt'=> 'yes'
				],
				'selectors' => [
					'{{WRAPPER}} article.post-col p' => 'color: {{VALUE}};',
				],
				
			]
		);
		
		$this->add_control(
			'meta_color',
			[
				'label' => __( 'Metadata Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} article.post-col .post-cat' => 'color: {{VALUE}};',
					'{{WRAPPER}} article.post-min .post-date' => 'color: {{VALUE}};',
				],
				
			]
		);
		
		$this->add_control(
			'meta_hover_color',
			[
				'label' => __( 'Metadata Hover Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} article.post-col .post-cat:hover, 
					{{WRAPPER}} article.post-col .post-cat:focus' => 'color: {{VALUE}};',
					'{{WRAPPER}} article.post-min .post-date:hover, 
					{{WRAPPER}} article.post-min .post-date:focus' => 'color: {{VALUE}};',
				],
				
			]
		);

		$this->add_control(
			'date_badge_color',
			[
				'label' => __( 'Date Badge Color', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} article.post-col .post-date' => 'color: {{VALUE}};',
				],
				'condition' => [
					'post_layout' => 'post-col',
				]
				
			]
		);
		
		$this->add_control(
			'date_badge_shadow',
			[
				'label' => __( 'Date Badge Shadow', 'adri-ajdethemes-elements' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} article.post-col .post-date' => 'box-shadow: 5px 5px 0px {{VALUE}};',
				],
				'condition' => [
					'post_layout' => 'post-col',
				]
				
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
		$query = $this->get_query_args();

		if ( ! $query->have_posts() ) {

			$html = '<p>' . esc_html__( 'No posts found.', 'adri-ajdethemes-elements' ) . '</p>';

		} else {

			$html = '<div class="row">';

			while ( $query->have_posts() ) {
				$query->the_post();
				$post = $query->post;

				$post_class = implode( ' ', get_post_class( [$settings['post_layout']] ) );

				$post_button = 
				
				$html .= '<div class="col-lg-4">';
				if ( $settings['post_layout'] === 'post-min' ) {

					$html .= sprintf( '<article class="%1$s" id="post-%2$s">', esc_attr( $post_class ), get_the_ID() );
					$settings['show_category'] === 'yes' ? $html .= sprintf( '<a href="%2$s" class="post-cat">%1$s</a>', get_the_category()[0]->name, esc_url( get_category_link( get_the_category()[0]->term_id ) ) ) : $html .= '';
					$html .= sprintf( '<h2 class="post-title"><a href="%1$s">%2$s</a></h2>', get_permalink(), get_the_title() );
					$html .= '<div class="post-meta">';
					$settings['show_author'] === 'yes' ? $html .= sprintf( '<div class="post-author">%s</div>', get_the_author_link() ) : $html .= '';
					$settings['show_date'] === 'yes' ? $html .= sprintf( '<div class="post-date">%s</div>', get_the_date() ) : $html .= '';
					$html .= '</div>';
					$html .= '</article>';

				} else {

					$html .= sprintf( '<article class="%1$s" id="post-%2$s">', esc_attr( $post_class ), get_the_ID() );
					$settings['show_date'] === 'yes' ? $html .= sprintf( '<div class="post-date"><span>%1$s</span>%2$s</div>', get_the_time( 'd' ), get_the_time( 'M' ) ) : $html .= '';
					$html .= sprintf( '<a href="%1$s" class="post-thumbnail-link">%2$s</a>', get_permalink(), get_the_post_thumbnail( $post, 'large' ) );
					
					$html .= '<div class="post-meta">';
					$settings['show_category'] === 'yes' ? $html .= sprintf( '<a href="%2$s" class="post-cat">%1$s</a>', get_the_category()[0]->name, esc_url( get_category_link( get_the_category()[0]->term_id ) ) ) : $html .= '';
					$html .= '</div>';
	
					$html .= sprintf( '<h2 class="post-title"><a href="%1$s">%2$s</a></h2>', get_permalink(), get_the_title() );
					$settings['show_excerpt'] === 'yes' ? $html .= sprintf( '<p>%s</p>', adri_ajdethemes_excerpt( $settings['excerpt_length'] ) ) : $html .= '';
					$settings['show_btn'] === 'yes' ? $html .= sprintf( '<a href="%1$s" class="btn-txt-arr post-button">%2$s <span class="arr-box"><i class="icon-Arrow-OutRight"></i></span></a>', get_permalink(), $settings['post_btn_text'] ) : $html .= '';
					$html .= '</article>';
				}
				$html .= '</div>';
			}
			wp_reset_postdata();

			$html .= '</div>';
		}
		echo $html;
    }
    	
	protected function get_query_args() {
		$settings = $this->get_settings_for_display();

		$query_args = array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'posts_per_page'      => intval( $settings['post_max'] ),
			'orderby' 			  => 'date',
			'category_name'		  => $settings['post_category'],
			'tag'				  => $settings['post_tags'],
		);

		$query = new WP_Query( $query_args );

		return $query;
	}

}