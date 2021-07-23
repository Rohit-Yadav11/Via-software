<?php

namespace WPDM\Elementor\Widgets;

use Elementor\Widget_Base;

class RegFormWidget extends Widget_Base
{

    public function get_name()
    {
        return 'wpdmregform';
    }

    public function get_title()
    {
        return 'Registration Form';
    }

    public function get_icon()
    {
        return 'eicon-person';
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


        // captcha: optional, default value "true", shows/hides captcha with registration form on true/false
        $this->add_control(
            'captcha',
            [
                'label' => esc_attr(__('Captcha', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    '0' => ['title' => 'No', 'icon' => 'fa fa-times'],
                    '1' => ['title' => 'Yes', 'icon' => 'fa fa-check']
                ],
                'default' => '1'
            ]
        );
        //verifyemail: optional, the default value is true, if the value is false, it shows the password field with registration form and doesn't send verification email, otherwise, it doesn't show the password field and send the password to registered email.
        $this->add_control(
            'verifyemail',
            [
                'label' => esc_attr(__('Verify Email', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    '0' => ['title' => 'No', 'icon' => 'fa fa-times'],
                    '1' => ['title' => 'Yes', 'icon' => 'fa fa-check']
                ],
                'default' => '1'
            ]
        );

        //autologin: optional, default value is "false" when "verifyemail" is true, the default value is "true" when "verifyemail" is 0, after registration, it will auto-login when the value is true otherwise redirect to the login page.
        $this->add_control(
            'autologin',
            [
                'label' => esc_attr(__('Auto Login', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    '0' => ['title' => 'No', 'icon' => 'fa fa-times'],
                    '1' => ['title' => 'Yes', 'icon' => 'fa fa-check']
                ],
                'default' => '0'
            ]
        );


        //logo: optional, add a logo or any image URL you want to show at the top of the registration form
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



        $this->end_controls_section();
    }



    protected function render()
    {

        $settings = $this->get_settings_for_display();
        $cus_settings = array_slice($settings, 0, 5);
        $cus_settings['captcha'] = (bool) $cus_settings['captcha'];
        $cus_settings['verifyemail'] = (bool) $cus_settings['verifyemail'];
        $cus_settings['autologin'] = (bool) $cus_settings['autologin'];

        echo WPDM()->shortCode->registerForm($cus_settings);

    }
}
