<?php

namespace WPDM\Elementor\Widgets;

use Elementor\Widget_Base;

class CategoryWidget extends Widget_Base
{

    public function get_name()
    {
        return 'wpdmcategory';
    }

    public function get_title()
    {
        return 'Packages By Category';
    }

    public function get_icon()
    {
        return 'eicon-theme-builder';
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


        //categories: multi select
        $this->add_control(
            'catid',
            [
                'label' => esc_attr(__('Include Categories', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => get_wpdmcategory_terms(),
                'default' => []
            ]
        );

        //Operator: Choose
        $this->add_control(
            'operator',
            [
                'label' => esc_attr(__('Operator', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => ['IN' => 'IN', 'NOT IN' => 'NOT IN', 'AND' => 'AND', 'EXISTS' => 'EXISTS', 'NOT EXISTS' => 'NOT EXISTS'],
                'default' => 'IN',
                'description' => esc_attr(__("Use this parameter only when you are using multiple categories.", WPDM_ELEMENTOR))
            ]
        );

        //title: Text
        $this->add_control(
            'title',
            [
                'label' => esc_attr(__('Title', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'description' => esc_attr(__('You can use any text there, if you use “1” then it will show the category title', WPDM_ELEMENTOR)),
                'default' => '1'
            ]
        );

        //description: Text
        $this->add_control(
            'desc',
            [
                'label' => esc_attr(__('Description', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'description' => esc_attr(__('You can use any text there, if you use “1” then it will show the category description', WPDM_ELEMENTOR)),
                'default' => '1'

            ]
        );

        //items per page: text number
        $this->add_control(
            'items_per_page',
            [
                'label' => esc_attr(__('Items Per Page', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'number',
                'default' => '10'
            ]
        );


        //order by: Select
        $this->add_control(
            'order_by',
            [
                'label' => esc_attr(__('Order By', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => ['date' => 'Date', 'title' => 'Title'],
                'default' => 'date',
            ]
        );

        //order: Choose
        $this->add_control(
            'order',
            [
                'label' => esc_attr(__('Order', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'ASC' => ['title' => 'Ascending', 'icon' => 'fa fa-sort-alpha-down'],
                    'DESC' => ['title' => 'Descending', 'icon' => 'fa fa-sort-alpha-up']
                ],
                'default' => 'DESC',
                'show_label' => false
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


        //authors: Text
        $this->add_control(
            'author',
            [
                'label' => esc_attr(__('Authors', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => esc_attr(__('e.g: 1, 2, 3', WPDM_ELEMENTOR)),
                'description' => esc_attr(__('Author IDs seperated by comma', WPDM_ELEMENTOR))
            ]
        );

        //cols web
        $this->add_control(
            'cols',
            [
                'label' => esc_attr(__('Columns In PC', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'number',
                'default' => '3'
            ]
        );

        //cols tab
        $this->add_control(
            'colstab',
            [
                'label' => esc_attr(__('Columns In Tab', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'number',
                'default' => '2'
            ]
        );

        //cols phone
        $this->add_control(
            'colsphone',
            [
                'label' => esc_attr(__('Columns In Phone', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'number',
                'default' => '1'
            ]
        );

        //Show Toolbar: radio
        $this->add_control(
            'toolbar',
            [
                'label' => esc_attr(__('Show Toolbar', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    '1' => ['title' => 'Show', 'icon' => 'fa fa-check'],
                    '0' => ['title' => 'Hide', 'icon' => 'fa fa-times']
                ],
                'default' => '1',
            ]
        );

        //show pagination: radio
        $this->add_control(
            'paging',
            [
                'label' => esc_attr(__('Paging', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    '1' => ['title' => 'Show', 'icon' => 'fa fa-check'],
                    '0' => ['title' => 'Hide', 'icon' => 'fa fa-times']
                ],
                'default' => '0',
            ]
        );


        $this->end_controls_section();
    }



    protected function render()
    {

        $settings = $this->get_settings_for_display();
        $cus_settings = array_slice($settings, 0, 15);

        $cus_settings['id'] = implode(",", $cus_settings['catid']);

        echo WPDM()->shortCode->category($cus_settings);

    }
}
