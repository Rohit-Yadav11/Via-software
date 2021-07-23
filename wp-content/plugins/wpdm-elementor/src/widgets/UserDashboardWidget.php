<?php

namespace WPDM\Elementor\Widgets;

use Elementor\Widget_Base;

class UserDashboardWidget extends Widget_Base
{

    public function get_name()
    {
        return 'wpdmuserdashboard';
    }

    public function get_title()
    {
        return 'User Dashboard';
    }

    public function get_icon()
    {
        return 'eicon-thumbnails-half';
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

        //recommended	value should be a category slug if you want to recommend specific category items, if you want to show recent items, use recommend="recent", skip the parameter recommended if you want to hide recommended items on the dashboard.
        $this->add_control(
            'recommended',
            [
                'label' => esc_attr(__('Recommended', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'default' => 'recent',
                'placeholder' => esc_attr(__('Recommended', WPDM_ELEMENTOR)),
                'description' => 'value should be a category slug if you want to recommend specific category items, if you want to show recent items, use recommend="recent", skip the parameter recommended if you want to hide recommended items on the dashboard'
            ]
        );

        //fav	fav=1 if you want to show users favorite section and skip the fav parameter if you want to hide the section
        $this->add_control(
            'fav',
            [
                'label' => esc_attr(__('Show Favorite Section', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    '0' => ['title' => 'No', 'icon' => 'fa fa-times'],
                    '1' => ['title' => 'Yes', 'icon' => 'fa fa-check']
                ],
                'default' => '1'
            ]
        );

        //signup	use signup=1 if you want to show login + signup form when a user is not logged in, signup=0 or skip the parameter if you only want to show the login form.
        $this->add_control(
            'signup',
            [
                'label' => esc_attr(__('show login + signup', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    '0' => ['title' => 'No', 'icon' => 'fa fa-times'],
                    '1' => ['title' => 'Yes', 'icon' => 'fa fa-check']
                ],
                'default' => '1'
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


        $this->end_controls_section();
    }



    protected function render()
    {

        $settings = $this->get_settings_for_display();
        $cus_settings = array_slice($settings, 0, 3);

        echo '<div class="oembed-elementor-widget">';
       // p($cus_settings);
        echo WPDM()->userDashboard->Dashboard($cus_settings);

        echo '</div>';
    }
}
