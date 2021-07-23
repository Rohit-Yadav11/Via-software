<?php

namespace WPDM\Elementor\Widgets;

use Elementor\Widget_Base;

class AllPackagesWidget extends Widget_Base
{

    public function get_name()
    {
        return 'wpdm-all-packages';
    }

    public function get_title()
    {
        return 'Packages Table';
    }

    public function get_icon()
    {
        return 'eicon-table';
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

        //categories
        $this->add_control(
            'categories',
            [
                'label' => esc_attr(__('Categories', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => get_wpdmcategory_terms(),
                'default' => []
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

        //order: Select
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


        //cols : text
        $this->add_control(
            'cols',
            [
                'label' => esc_attr(__('Data field name', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'default' => 'title|categories',
                'description' => 'data field name for each column, column separator |, if you want to show multiple data fields in the same column, data field names should be separated by ,'
            ]
        );

        //cols tab
        $this->add_control(
            'colheads',
            [
                'label' => esc_attr(__('column heading', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'default' => 'Title|Categories',
                'description' => 'colheads="Title|Categories|Update Date::200px|Download::100px'
            ]
        );

        
        //jstable: choose
        $this->add_control(
            'jstable',
            [
                'label' => esc_attr(__('Enable datatable.js', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    '0' => ['title' => 'Disable', 'icon' => 'fa fa-times'],
                    '1' => ['title' => 'Enable', 'icon' => 'fa fa-check']
                ],
                'default' => '0'
            ]
        );

      
        //Require login: choose
        $this->add_control(
            'login',
            [
                'label' => esc_attr(__('Require Login', WPDM_ELEMENTOR)),
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
        $cus_settings = array_slice($settings, 0, 10);

        $cus_settings['categories'] = implode(",", $cus_settings['categories']);
        if(trim($cus_settings['categories']) === '')
            unset($cus_settings['categories']);

        echo WPDM()->shortCode->allPackages($cus_settings);

    }
}
