<?php

namespace WPDM\Elementor\Widgets;

use Elementor\Widget_Base;

class FrontendWidget extends Widget_Base
{

    public function get_name()
    {
        return 'wpdmfrontend';
    }

    public function get_title()
    {
        return 'Author Dashboard';
    }

    public function get_icon()
    {
        return 'eicon-dashboard';
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


        //logo	optional, add the logo or any image URL you want to show on top of the login form
        $this->add_control(
            'logo',
            [
                'label' => esc_attr(__('Logo URL', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'url',
                'placeholder' => esc_attr(__('Logo Url', WPDM_ELEMENTOR)),
                'description' => 'optional, add the logo or any image URL you want to show on top of the login form'
            ]
        );


        //flaturl	Optional parameter, default value 0, use flaturl=1 for flat url
        $this->add_control(
            'flaturl',
            [
                'label' => esc_attr(__('Flat Url', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    '0' => ['title' => 'No', 'icon' => 'fa fa-times'],
                    '1' => ['title' => 'Yes', 'icon' => 'fa fa-check']
                ],
                'default' => '0'
            ]
        );

        //hide
        $this->add_control(
            'hide',
            [
                'label' => esc_attr(__('hide', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => ['settings' => 'Settings', 'images' => 'Images', 'cats' => 'Categories', 'tags' => 'Tags'],
                'default' => []
            ]
        );


        $this->end_controls_section();
    }



    protected function render()
    {

        $settings = $this->get_settings_for_display();
        $cus_settings = array_slice($settings, 0, 4);
        $cus_settings['hide'] = implode(',', $cus_settings['hide']);

        echo '<div class="oembed-elementor-widget">';
       // p($cus_settings);
        echo WPDM()->authorDashboard->dashboard($cus_settings);

        echo '</div>';
    }
}
