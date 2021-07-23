<?php

namespace WPDM\Elementor\Widgets;

use Elementor\Widget_Base;

class UserProfileWidget extends Widget_Base
{

    public function get_name()
    {
        return 'wpdm-user-profile';
    }

    public function get_title()
    {
        return 'User Profile';
    }

    public function get_icon()
    {
        return 'fa fa-user-o';
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


        $this->end_controls_section();
    }



    protected function render()
    {

        $settings = $this->get_settings_for_display();
        $cus_settings = array_slice($settings, 0, 5);

        echo '<div class="oembed-elementor-widget">';
        // p($cus_settings);
        echo WPDM()->userProfile->profile($cus_settings);

        echo '</div>';
    }
}
