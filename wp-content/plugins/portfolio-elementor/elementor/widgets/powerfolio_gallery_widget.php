<?php

namespace Powerfolio\Widgets;

use  Elementor\Widget_Base ;
use  Elementor\Controls_Manager ;
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// Exit if accessed directly
/**
 *
 * @since 1.0.0
 */
class ELPT_Powerfolio_Gallery_Widget extends Widget_Base
{
    /**
     * Retrieve the widget name.
     *
     * @since 1.0.0
     *
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name()
    {
        return 'powerfolio_gallery';
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
    public function get_title()
    {
        return __( 'Image Gallery (Powerfolio)', 'powerfolio' );
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
    public function get_icon()
    {
        return 'eicon-gallery-justified';
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
    public function get_categories()
    {
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
    public function get_script_depends()
    {
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
    protected function register_controls()
    {
        $pro_version = true;
        //pe_fs()->can_use_premium_code__premium_only();
        //=========== Main Settings	==============
        $this->start_controls_section( 'section_content', [
            'label' => __( 'General Settings', 'powerfolio' ),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );
        //======= Gallery ============
        $repeater = new \Elementor\Repeater();
        $repeater->add_control( 'list_title', [
            'label'       => __( 'Title', 'powerfolio' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => __( 'List Title', 'powerfolio' ),
            'label_block' => true,
        ] );
        $repeater->add_control( 'list_filter_tag', [
            'label'       => __( 'Tag (To use with filter)', 'powerfolio' ),
            'type'        => Controls_Manager::TEXT,
            'default'     => __( 'Tag 1', 'powerfolio' ),
            'label_block' => true,
        ] );
        $repeater->add_control( 'list_image', [
            'label'   => __( 'Choose Image', 'powerfolio' ),
            'type'    => Controls_Manager::MEDIA,
            'default' => [
            'url' => \Elementor\Utils::get_placeholder_image_src(),
        ],
        ] );
        // END - PRO Version Snippet
        $this->add_control( 'list', [
            'label'       => __( 'Gallery Items', 'Powerfolio' ),
            'type'        => Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => [ [
            'list_title'   => __( 'Title #1', 'powerfolio' ),
            'list_content' => __( 'Item content. Click the edit button to change this text.', 'powerfolio' ),
        ], [
            'list_title'   => __( 'Title #2', 'powerfolio' ),
            'list_content' => __( 'Item content. Click the edit button to change this text.', 'powerfolio' ),
        ] ],
            'title_field' => '{{{ list_title }}}',
        ] );
        // ====== END Gallery =========
        $showfilter_description = '';
        $this->add_control( 'showfilter', [
            'label'   => __( 'Show category filter?', 'powerfolio' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'yes',
            'options' => [
            'yes' => __( 'Yes', 'powerfolio' ),
            'no'  => __( 'No', 'powerfolio' ),
        ],
        ] );
        $this->add_control( 'tax_text', [
            'label'     => __( 'All Categories - Button Text', '' ),
            'type'      => \Elementor\Controls_Manager::TEXT,
            'default'   => __( 'All', '' ),
            'condition' => [
            'showfilter' => 'yes',
        ],
        ] );
        $this->add_control( 'Upgrade_note3', [
            'label'           => '',
            'type'            => \Elementor\Controls_Manager::RAW_HTML,
            'raw'             => get_upgrade_message(),
            'content_classes' => 'your-class',
        ] );
        $this->end_controls_section();
        //=========== END - Main Settings	==============
        //=========== Grid Settings	==============
        $this->start_controls_section( 'section_grid', [
            'label' => __( 'Grid Settings', 'powerfolio' ),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );
        //Grid Options
        $style_options = array(
            'masonry' => __( 'Masonry', 'powerfolio' ),
            'box'     => __( 'Boxes', 'powerfolio' ),
        );
        $description = __( 'Upgrade your plan to get access to the special grids and also to the Grid Builder - Our exclusive feature! <a href="https://checkout.freemius.com/mode/dialog/plugin/7226/plan/12571/">CLICK TO UPGRADE</a>', 'powerfolio' );
        // END - PRO Version Snippet
        //Style
        $this->add_control( 'style', [
            'label'       => __( 'Grid Style', 'powerfolio' ),
            'type'        => Controls_Manager::SELECT,
            'default'     => 'box',
            'description' => $description,
            'options'     => $style_options,
        ] );
        //columns
        $this->add_control( 'columns', [
            'label'      => __( 'Number of columns', 'powerfolio' ),
            'type'       => Controls_Manager::SELECT,
            'default'    => '3',
            'conditions' => array(
            'relation' => 'or',
            'terms'    => array( array(
            'name'     => 'style',
            'operator' => '==',
            'value'    => 'box',
        ), array(
            'name'     => 'style',
            'operator' => '==',
            'value'    => 'masonry',
        ) ),
        ),
            'options'    => [
            '2' => __( 'Two Columns', 'powerfolio' ),
            '3' => __( 'Three Columns', 'powerfolio' ),
            '4' => __( 'Four Columns', 'powerfolio' ),
            '5' => __( 'Five Columns', 'powerfolio' ),
            '6' => __( 'Six Columns', 'powerfolio' ),
        ],
        ] );
        $margin_description = '';
        $this->add_control( 'margin', [
            'label'        => __( 'Use item margin?', 'powerfolio' ),
            'description'  => $margin_description,
            'type'         => Controls_Manager::SWITCHER,
            'default'      => 'yes',
            'return_value' => 'yes',
            'conditions'   => array(
            'relation' => 'or',
            'terms'    => array( array(
            'name'     => 'style',
            'operator' => '==',
            'value'    => 'box',
        ), array(
            'name'     => 'style',
            'operator' => '==',
            'value'    => 'masonry',
        ), array(
            'name'     => 'style',
            'operator' => '==',
            'value'    => 'grid_builder',
        ) ),
        ),
        ] );
        //Margin Size
        $this->add_control( 'margin_size', [
            'label'      => __( 'Additional Margin (px)', 'powerfolio' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'conditions' => array(
            'relation' => 'or',
            'terms'    => array( array(
            'name'     => 'margin',
            'operator' => '==',
            'value'    => 'yes',
        ) ),
        ),
            'range'      => [
            'px' => [
            'min'  => 0,
            'max'  => 20,
            'step' => 1,
        ],
        ],
            'default'    => [
            'unit' => 'px',
            'size' => 0,
        ],
            'selectors'  => [
            '{{WRAPPER}} .elpt-portfolio-content.elpt-portfolio-margin .portfolio-item-wrapper' => 'padding-right: calc(5px + {{SIZE}}{{UNIT}}); padding-left: calc(5px + {{SIZE}}{{UNIT}}); padding-bottom: calc((5px + {{SIZE}}{{UNIT}})*2);',
        ],
        ] );
        //================================== GRID BUILDER ========================
        for ( $i = 1 ;  $i <= 20 ;  $i++ ) {
            //width
            $item = 'item_' . $i;
            $this->add_control( $item . '_heading', [
                'label'      => __( 'Item ' . $i . '', 'powerfolio' ),
                'type'       => \Elementor\Controls_Manager::HEADING,
                'separator'  => 'before',
                'conditions' => array(
                'relation' => 'and',
                'terms'    => array( array(
                'name'     => 'style',
                'operator' => '==',
                'value'    => 'grid_builder',
            ) ),
            ),
            ] );
            $this->add_control( $item, [
                'label'      => __( 'Width (%)', 'powerfolio' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ '%' ],
                'default'    => [
                'unit' => '%',
                'size' => 25,
            ],
                'range'      => [
                '%' => [
                'min'  => 10,
                'max'  => 100,
                'step' => 5,
            ],
            ],
                'conditions' => array(
                'relation' => 'and',
                'terms'    => array( array(
                'name'     => 'style',
                'operator' => '==',
                'value'    => 'grid_builder',
            ) ),
            ),
                'selectors'  => [
                '{{WRAPPER}} .elpt-portfolio-content .portfolio-item-wrapper:nth-child(' . $i . ')' => 'width: {{SIZE}}{{UNIT}} !important;',
            ],
            ] );
            //height
            $itemh = 'item_height_' . $i;
            $this->add_control( $itemh, [
                'label'      => __( 'Height (px)', 'powerfolio' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'default'    => [
                'unit' => 'px',
                'size' => 280,
            ],
                'range'      => [
                'px' => [
                'min'  => 20,
                'max'  => 840,
                'step' => 20,
            ],
            ],
                'conditions' => array(
                'relation' => 'and',
                'terms'    => array( array(
                'name'     => 'style',
                'operator' => '==',
                'value'    => 'grid_builder',
            ) ),
            ),
                'selectors'  => [
                '{{WRAPPER}} .elpt-portfolio-content .portfolio-item-wrapper:nth-child(' . $i . ') a' => 'height: {{SIZE}}{{UNIT}} !important;',
            ],
            ] );
            /*$this->add_control(
            			'hr_'.$i,
            			[
            				'type' => \Elementor\Controls_Manager::DIVIDER,
            				'conditions' => array(
            					'relation' => 'and',
            					'terms'    => array(
            						array(
            							'name'     => 'style',
            							'operator' => '==',
            							'value'   => 'grid_builder',
            						),
            						array(
            							'name'     => 'postsperpage',
            							'operator' => '>=',
            							 'value'   => $i,
            						)
            					)
            				),
            			]
            		);*/
        }
        //================================== END OF GRID BUILDER==================
        //Box Height
        $this->add_control( 'box_height', [
            'label'      => __( 'Box Height (px)', 'powerfolio' ),
            'type'       => Controls_Manager::SLIDER,
            'size_units' => [ 'px' ],
            'conditions' => array(
            'relation' => 'or',
            'terms'    => array( array(
            'name'     => 'style',
            'operator' => '==',
            'value'    => 'box',
        ), array(
            'name'     => 'style',
            'operator' => '==',
            'value'    => 'specialgrid5',
        ), array(
            'name'     => 'style',
            'operator' => '==',
            'value'    => 'specialgrid6',
        ) ),
        ),
            'range'      => [
            'px' => [
            'min'  => 10,
            'max'  => 800,
            'step' => 1,
        ],
        ],
            'default'    => [
            'unit' => 'px',
            'size' => 250,
        ],
            'selectors'  => [
            '{{WRAPPER}} .elpt-portfolio-content.elpt-portfolio-style-box .portfolio-item'              => 'height: {{SIZE}}{{UNIT}};',
            '{{WRAPPER}} .elpt-portfolio-content.elpt-portfolio-special-grid-5 .portfolio-item-wrapper' => 'height: {{SIZE}}{{UNIT}};',
            '{{WRAPPER}} .elpt-portfolio-content.elpt-portfolio-special-grid-5 .portfolio-item'         => 'height: {{SIZE}}{{UNIT}};',
            '{{WRAPPER}} .elpt-portfolio-content.elpt-portfolio-special-grid-6 .portfolio-item-wrapper' => 'height: {{SIZE}}{{UNIT}};',
            '{{WRAPPER}} .elpt-portfolio-content.elpt-portfolio-special-grid-6 .portfolio-item'         => 'height: {{SIZE}}{{UNIT}};',
        ],
        ] );
        $this->add_control( 'Upgrade_note2', [
            'label'           => '',
            'type'            => \Elementor\Controls_Manager::RAW_HTML,
            'raw'             => get_upgrade_message(),
            'content_classes' => 'your-class',
        ] );
        $this->end_controls_section();
        //=========== END - Grid Settings	==============
        //=========== Hover Settings	==============
        $this->start_controls_section( 'section_hover', [
            'label' => __( 'Hover Settings', 'powerfolio' ),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );
        $description = __( 'Upgrade your plan to get access to 15+ hover effects! <a href="https://checkout.freemius.com/mode/dialog/plugin/7226/plan/12571/">CLICK TO UPGRADE</a>', 'powerfolio' );
        $this->add_control( 'hover', [
            'label'       => __( 'Hover Style', 'powerfolio' ),
            'type'        => Controls_Manager::SELECT,
            'default'     => 'simple',
            'description' => $description,
            'options'     => [
            'simple' => __( 'Simple', 'powerfolio' ),
            'hover1' => __( 'From Bottom', 'powerfolio' ),
            'hover2' => __( 'From Top', 'powerfolio' ),
        ],
        ] );
        $this->add_control( 'Upgrade_note', [
            'label'           => '',
            'type'            => \Elementor\Controls_Manager::RAW_HTML,
            'raw'             => get_upgrade_message(),
            'content_classes' => 'your-class',
        ] );
        //=========== END - Grid Settings	==============
        $this->end_controls_section();
        //=========== ADVANCED SECTION	==============
        $this->start_controls_section( 'section_advanced', [
            'label' => __( 'Advanced', 'powerfolio' ),
            'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
        ] );
        $this->add_control( 'custom_js', [
            'label'    => __( 'Custom JS', 'powerfolio' ),
            'type'     => \Elementor\Controls_Manager::CODE,
            'language' => 'javascript',
            'rows'     => 20,
        ] );
        $this->add_control( 'Upgrade_note4', [
            'label'           => '',
            'type'            => \Elementor\Controls_Manager::RAW_HTML,
            'raw'             => get_upgrade_message(),
            'content_classes' => 'your-class',
        ] );
        $this->end_controls_section();
        //=========== END - ADVANCED SECTION	==============
        //==========================================================================================
        $this->start_controls_section( 'section_item_description', [
            'label' => __( 'Item', 'powerfolio' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );
        //Hover: Background color
        $this->add_group_control( \Elementor\Group_Control_Background::get_type(), [
            'name'     => 'bgcolor',
            'label'    => __( 'Hover: Background Color', 'powerfolio' ),
            'types'    => [ 'classic', 'gradient' ],
            'selector' => '{{WRAPPER}} .portfolio-item-infos-wrapper',
        ] );
        $this->add_control( 'Upgrade_note6', [
            'label'           => '',
            'type'            => \Elementor\Controls_Manager::RAW_HTML,
            'raw'             => get_upgrade_message(),
            'content_classes' => 'your-class',
        ] );
        // END - PRO Version Snippets
        $this->end_controls_section();
        $this->start_controls_section( 'section_style', [
            'label' => __( 'Filter', 'powerfolio' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ] );
        $this->add_control( 'Upgrade_note7', [
            'label'           => '',
            'type'            => \Elementor\Controls_Manager::RAW_HTML,
            'raw'             => get_upgrade_message(),
            'content_classes' => 'your-class',
        ] );
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
    protected function render()
    {
        include 'powerfolio_gallery_widget_render.php';
        ?>
		<script><?php 
        echo  $settings['custom_js'] ;
        ?></script>
		<?php 
    }

}