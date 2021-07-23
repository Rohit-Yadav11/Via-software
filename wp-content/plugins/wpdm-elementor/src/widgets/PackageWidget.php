<?php

namespace WPDM\Elementor\Widgets;

use Elementor\Widget_Base;

class PackageWidget extends Widget_Base {

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
        return 'wpdmpackage';
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
        return __( 'Package', 'plugin-name' );
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
        return 'eicon-download-button';
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
        return [ 'wpdm' ];
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
            'content_section',
            [
                'label' => __( 'Parameters', 'plugin-name' ),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

       /* $this->add_control(
            'url',
            [
                'label' => __( 'URL to embed', 'plugin-name' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'url',
                'placeholder' => __( 'https://your-link.com', 'plugin-name' ),
            ]
        );*/

        //package ID: Text
        $this->add_control(
            'pid',
            [
                'label' => esc_attr(__('Package', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'placeholder' => esc_attr(__('Package', WPDM_ELEMENTOR)),
                'select2options' => [
                    'placeholder' => 'Type Package title',
                    'ajax' => [
                        'url' =>  get_rest_url(null, 'wpdm-elementor/v1/search-packages'),
                        'dataType' => 'json',
                        'delay' => 250
                    ],
                    'minimumInputLength' => 2
                ]
            ]
        );


        //link template: select
        $this->add_control(
            'ltemplate',
            [
                'label' => esc_attr(__('Link Template', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => get_wpdm_link_templates(),
                'default' => 'link-template-panel'
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
        echo WPDM()->shortCode->package(['id' => $settings['pid'], 'template' => $settings['ltemplate']]);

    }

}

