<?php

namespace WPDM\Elementor\Widgets;

use Elementor\Widget_Base;

class DirectLinkWidget extends Widget_Base
{

    public function get_name()
    {
        return 'wpdmdirectlink';
    }

    public function get_title()
    {
        return 'Direct download link';
    }

    public function get_icon()
    {
        return 'eicon-editor-link';
    }

    public function get_categories()
    {
        return ['wpdm'];
    }

    protected function _register_controls()
    {

        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_attr(__('Parameters', WPDM_ELEMENTOR)),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        //Package: Text
        $this->add_control(
            'id',
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
            'target',
            [
                'label' => esc_attr(__('Link Template', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => ['_blank' => '_blank', '_self' => '_self'],
                'default' => '_blank'
            ]
        );

        //label	Download link label
        $this->add_control(
            'label',
            [
                'label' => esc_attr(__('Download link label', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'default' => 'Download',
                'placeholder' => esc_attr(__('Download', WPDM_ELEMENTOR)),
            ]
        );

        //class	CSS class name, in case you want to apply some css style
        $this->add_control(
            'class',
            [
                'label' => esc_attr(__('CSS class name', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
            ]
        );


        //eid HTML element ID
        $this->add_control(
            'eid',
            [
                'label' => esc_attr(__('HTML element ID', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
            ]
        );

        //style	Apple raw css code ( ex: style="color: #3399ff;" )
        $this->add_control(
            'style',
            [
                'label' => esc_attr(__('CSS Style', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'rows' => 10,
                'placeholder' => esc_attr(__('Apple raw css code', WPDM_ELEMENTOR)),
            ]
        );



        $this->end_controls_section();
    }



    protected function render()
    {

        $settings = $this->get_settings_for_display();
        $cus_settings = array_slice($settings, 0, 8);

        echo '<div class="oembed-elementor-widget">';
        // p($cus_settings);
        echo WPDM()->shortCode->directLink($cus_settings);

        echo '</div>';
    }
}
