<?php

namespace WPDM\Elementor\Widgets;

use Elementor\Widget_Base;

class SearchResultWidget extends Widget_Base
{

    public function get_name()
    {
        return 'wpdmsearchresult';
    }

    public function get_title()
    {
        return 'Search Result';
    }

    public function get_icon()
    {
        return 'eicon-search-results';
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

        //link template: select
        $this->add_control(
            'template',
            [
                'label' => esc_attr(__('Link Template', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => get_wpdm_link_templates(),
                'default' => 'link-default-default'
            ]
        );

        //init: 1 if you want to show some results initially, like the latest packages, skip the parameter or 0 if you don't want to show any package until you search
        $this->add_control(
            'init',
            [
                'label' => esc_attr(__('Require Login', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    '0' => ['title' => 'No', 'icon' => 'fa fa-times'],
                    '1' => ['title' => 'Yes', 'icon' => 'fa fa-check']
                ],
                'default' => '1'
            ]
        );
        
        //cols: 1 or 2 or 3 or 4 columns search result
        $this->add_control(
            'cols',
            [
                'label' => esc_attr(__('Columns', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'number',
                'default' => '3'
            ]
        );

        $this->end_controls_section();
    }



    protected function render()
    {

        $settings = $this->get_settings_for_display();
        $cus_settings = array_slice($settings, 0, 4);


        echo WPDM()->shortCode->searchResult($cus_settings);

    }
}
