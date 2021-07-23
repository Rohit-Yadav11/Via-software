<?php

namespace WPDM\Elementor\Widgets;

use Elementor\Widget_Base;

class PackagesWidget extends Widget_Base
{

    public function get_name()
    {
        return 'wpdmpackages';
    }

    public function get_title()
    {
        return 'Packages';
    }

    public function get_icon()
    {
        return 'eicon-posts-grid';
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

        //search keywords : TEXT filed
        $this->add_control(
            'search',
            [
                'label' => esc_attr(__('Search Keywords', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => esc_attr(__('search keywords', WPDM_ELEMENTOR)),
            ]
        );

        //include categories: multi select
        $wpdmcategory_terms = get_terms(['taxonomoy' => 'wpdmcategory']);
        foreach($wpdmcategory_terms as $k => $t){
            $wpdmcategory_terms[$t->slug] = $t->name;
            unset($wpdmcategory_terms[$k]);
        };


        $this->add_control(
            'categories',
            [
                'label' => esc_attr(__('Include Categories', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $wpdmcategory_terms,
                'default' => []
            ]
        );

        //include children: radio
        $this->add_control(
            'include_children',
            [
                'label' => esc_attr(__('Include Children', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    '0' => ['title' => 'No', 'icon' => 'fa fa-times'],
                    '1' => ['title' => 'Yes', 'icon' => 'fa fa-check']
                ],
                'default' => '0'
            ]
        );

        //category match: radios

        //exclude categories: multi select

        //tags: multi select
        $post_tag_terms = get_terms(['taxonomy' => 'wpdmtag']);
        if(is_array($post_tag_terms)) {
            foreach ($post_tag_terms as $k => $t) {
                $post_tag_terms[$t->slug] = $t->name;
                unset($post_tag_terms[$k]);
            };
            $this->add_control(
                'tags',
                [
                    'label' => esc_attr(__('Tags', WPDM_ELEMENTOR)),
                    'type' => \Elementor\Controls_Manager::SELECT2,
                    'multiple' => true,
                    'options' => $post_tag_terms,
                    'default' => []
                ]
            );
        }

        //exclude packages with text: text

        //authors: Text
        $this->add_control(
            'author',
            [
                'label' => esc_attr(__('Authors', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::TEXT,
                'input_type' => 'text',
                'placeholder' => esc_attr(__('1, 2, 3', WPDM_ELEMENTOR)),
                'description' => esc_attr(__('Author IDs seperated by comma', WPDM_ELEMENTOR))
            ]
        );

        //exclude packages from author: text



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

        //link template: select
        $link_templates = WPDM()->packageTemplate->getTemplates('link');
        array_walk($link_templates, function (&$v, &$k) {
            $k = strrpos($k, ".") ? substr($k, 0, strrpos($k, ".")) : $k;
            $v = $k;
        });

        $this->add_control(
            'template',
            [
                'label' => esc_attr(__('Link Template', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => $link_templates,
                'default' => 'link-template-panel'
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

        //enable async request: radio
        $this->add_control(
            'async',
            [
                'label' => esc_attr(__('Async', WPDM_ELEMENTOR)),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    '1' => ['title' => 'Enable', 'icon' => 'fa fa-check'],
                    '0' => ['title' => 'Disable', 'icon' => 'fa fa-times']
                ],
                'default' => '1',
            ]
        );

        $this->end_controls_section();
    }



    protected function render()
    {

        $settings = $this->get_settings_for_display();
        $cus_settings = array_slice($settings, 0, 20);

        if(isset($cus_settings['categories']) && is_array($cus_settings['categories']))
            $cus_settings['categories'] = implode(",", $cus_settings['categories']);
        if(isset($cus_settings['tags']) && is_array($cus_settings['tags']))
            $cus_settings['tags'] = implode(",", $cus_settings['tags']);

        //echo '<div class="oembed-elementor-widget">';

        echo WPDM()->shortCode->packages($cus_settings);

        //echo '</div>';
    }
}
