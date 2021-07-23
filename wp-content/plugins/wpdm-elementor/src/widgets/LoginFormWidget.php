<?php

namespace WPDM\Elementor\Widgets;

use Elementor\Widget_Base;

class LoginFormWidget extends Widget_Base
{

    public function get_name()
    {
        return 'wpdmloginform';
    }

    public function get_title()
    {
        return 'Login Form';
    }

    public function get_icon()
    {
        return 'eicon-site-identity';
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


        //redirect: optional, use an URL where you want users to redirect after login
        $this->add_control(
            'redirect',
            [
                'label' => esc_attr(__('Redirect URL', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'url',
                'placeholder' => esc_attr(__('Redirect Url', WPDM_ELEMENTOR)),
                'description' => 'optional, use an URL where you want users to redirect after login'
            ]
        );


        //logo: optional, add the logo or any image URL you want to show on top of the login form
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


        //regurl: optional, in case, if you have multiple login pages and multiple signup page, you may mention the signup page URL for this login form. Otherwise, it will use the standard signup page URL.
        $this->add_control(
            'regurl',
            [
                'label' => esc_attr(__("Registration URL", WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'url',
                'placeholder' => esc_attr(__('Logo Url', WPDM_ELEMENTOR)),
                'description' => 'optional, in case, if you have multiple login pages and multiple signup page, you may mention the signup page URL for this login form. Otherwise, it will use the standard signup page URL.'
            ]
        );

        //note_before: optional, text/note to show above the login form
        $this->add_control(
            'note_before',
            [
                'label' => esc_attr(__("Note Before", WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => esc_attr(__('Note before', WPDM_ELEMENTOR)),
                'description' => 'optional, text/note to show above the login form'
            ]
        );

        // note_after: optional, text/note to show below the login form
        $this->add_control(
            'note_after',
            [
                'label' => esc_attr(__("Note After", WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => esc_attr(__('Note After', WPDM_ELEMENTOR)),
                'description' => 'optional, text/note to show below the login form'
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
        echo WPDM()->shortCode->loginForm($cus_settings);

        echo '</div>';
    }
}
