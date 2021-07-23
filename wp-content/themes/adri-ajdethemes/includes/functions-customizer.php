<?php
/**
 * Theme Customizer Settings
 *
 * @package AdriAjdethemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'adri_ajdethemes_customize_register' ) ) {

    function adri_ajdethemes_customize_register( $wp_customize ) {

        require_once ADRI_AJDETHEMES_INC_DIR . '/class-customizer-alpha-color-picker.php';

        // Logo ------------

        $wp_customize->add_setting( 'nav_site_logo', array(
            'priority'          => 60,
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => 'esc_url',
        ));

        $wp_customize->add_control( new WP_Customize_Image_Control(
                $wp_customize, 'nav_site_logo',
                array(
                    'label'      => esc_html__( 'Site Logo', 'adri-ajdethemes' ),
                    'description'   => esc_html__('Add your logo in the navigation menu. For best look, match the logo color with the theme brand color. Recommended size 83x43 pixels.', 'adri-ajdethemes'),
                    'settings'   => 'nav_site_logo',
                    'section'    => 'title_tagline',
                    'priority'   => 60,
                )
            )
        );
        
        // Transparent Logo ------------

        $wp_customize->add_setting( 'nav_site_trans_logo', array(
            'priority'          => 60,
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => 'esc_url',
        ));

        $wp_customize->add_control( new WP_Customize_Image_Control(
                $wp_customize, 'nav_site_trans_logo',
                array(
                    'label'      => esc_html__( 'Transparent Nav Site Logo', 'adri-ajdethemes' ),
                    'description'   => esc_html__('Use different logo when the navigation background is transparent.', 'adri-ajdethemes'),
                    'settings'   => 'nav_site_trans_logo',
                    'section'    => 'title_tagline',
                    'priority'   => 60,
                    'active_callback' => function() use ( $wp_customize ) {
                        return ( true === $wp_customize->get_setting( 'nav_is_trans' )->value() );
                    }
                )
            )
        );


        /**
         * Blog - Section
         * 
         */
    
        $wp_customize->add_section('blog', array(
            'title'         => esc_html__('Blog', 'adri-ajdethemes'),
            'priority'      => 200,
            'description'   => esc_html__('Theme specific blog options.', 'adri-ajdethemes')
        ));
    
        // Blog Layout ------------

        $wp_customize->add_setting('blog_layout', array(
            'capability'        => 'edit_theme_options',
            'default'           => 'post-classic',
            'sanitize_callback' => 'sanitize_select'
        ));
    
        $wp_customize->add_control('blog_layout', array(
                'type'      => 'radio',
                'section'   => 'blog',
                'priority'  => 10,
                'label'     => esc_html__('Blog layout:', 'adri-ajdethemes'),
                'choices'  => array(
                    'post-classic'  => esc_html__( 'Classic', 'adri-ajdethemes' ),
                    'post-col'      => esc_html__( 'Columns', 'adri-ajdethemes' ),
                    'post-card'     => esc_html__( 'Cards', 'adri-ajdethemes' ),
                    'post-min'      => esc_html__( 'Minimal (no image)', 'adri-ajdethemes' ),
                ),
        ));
    
        // Blog Show Author ------------
    
        $wp_customize->add_setting('blog_author', array(
            'capability'        => 'edit_theme_options',
            'default'           => true,
            'sanitize_callback' => 'sanitize_checkbox',
        ));
    
        $wp_customize->add_control('blog_author', array(
            'type'     => 'checkbox',
            'section'  => 'blog',
            'priority' => 10,
            'label'    => esc_html__( 'Show the author', 'adri-ajdethemes' ),
        ));
        
        // Blog Show Categories ------------
    
        $wp_customize->add_setting('blog_cat', array(
            'capability'        => 'edit_theme_options',
            'default'           => true,
            'sanitize_callback' => 'sanitize_checkbox',
        ));
    
        $wp_customize->add_control('blog_cat', array(
            'type'     => 'checkbox',
            'section'  => 'blog',
            'priority' => 10,
            'label'    => esc_html__( 'Show the categories', 'adri-ajdethemes' ),
        ));
        
        // Blog Show Date ------------
    
        $wp_customize->add_setting('blog_date', array(
            'capability'        => 'edit_theme_options',
            'default'           => true,
            'sanitize_callback' => 'sanitize_checkbox',
        ));
    
        $wp_customize->add_control('blog_date', array(
            'type'     => 'checkbox',
            'section'  => 'blog',
            'priority' => 10,
            'label'    => esc_html__( 'Show the date', 'adri-ajdethemes' ),
        ));
        
        // Blog Comments ------------
    
        $wp_customize->add_setting('blog_comments', array(
            'capability'        => 'edit_theme_options',
            'default'           => true,
            'sanitize_callback' => 'sanitize_checkbox',
        ));
    
        $wp_customize->add_control('blog_comments', array(
            'type'     => 'checkbox',
            'section'  => 'blog',
            'priority' => 10,
            'label'    => esc_html__( 'Show the comments number', 'adri-ajdethemes' ),
            'active_callback' => function() use ( $wp_customize ) {
                return ( 'post-classic' === $wp_customize->get_setting( 'blog_layout' )->value() );
            }
        ));
        
        // Blog Excerpt Length ------------
    
        $wp_customize->add_setting('blog_excerpt_length', array(
            'capability'        => 'edit_theme_options',
            'default'           => 25,
            'sanitize_callback' => 'sanitize_text_field',
        ));
    
        $wp_customize->add_control('blog_excerpt_length', array(
            'type'     => 'number',
            'section'  => 'blog',
            'priority' => 10,
            'label'    => esc_html__( 'Posts excerpt length in words', 'adri-ajdethemes' ),
            'active_callback' => function() use ( $wp_customize ) {
                return ( 
                    'post-min' !== $wp_customize->get_setting( 'blog_layout' )->value() &&
                    'post-card' !== $wp_customize->get_setting( 'blog_layout' )->value()
                );
            }
        ));
    
        // Blog Show "Read More" ------------
    
        $wp_customize->add_setting('blog_more_btn', array(
            'capability'        => 'edit_theme_options',
            'default'           => true,
            'sanitize_callback' => 'sanitize_checkbox',
        ));
    
        $wp_customize->add_control('blog_more_btn', array(
            'type'     => 'checkbox',
            'section'  => 'blog',
            'priority' => 10,
            'label'    => esc_html__( 'Show "read more" button on each blog post.', 'adri-ajdethemes' ),
        ));
    
        // Blog "Read More" Text ------------
    
        $wp_customize->add_setting('blog_more_btn_text', array(
            'capability'        => 'edit_theme_options',
            'default'           => 'Read More',
            'sanitize_callback' => 'sanitize_text_field'
        ));
        
        $wp_customize->add_control('blog_more_btn_text', array(
            'type'      => 'text',
            'section'   => 'blog',
            'priority'  => 10,
            'label'     => esc_html__('"Read more" button text', 'adri-ajdethemes'),
            'active_callback' => function() use ( $wp_customize ) {
                return ( $wp_customize->get_setting( 'blog_more_btn' )->value() );
            }
        ));
        
        // Blog Sidebar ------------
    
        $wp_customize->add_setting('blog_sidebar', array(
            'capability'        => 'edit_theme_options',
            'default'           => 'sb_right',
            'sanitize_callback' => 'sanitize_select'
        ));
    
        $wp_customize->add_control('blog_sidebar', array(
            'type'      => 'radio',
            'section'   => 'blog',
            'priority'  => 10,
            'label'     => esc_html__('Sidebar position:', 'adri-ajdethemes'),
            'choices'   => array(
                'sb_left'     => esc_html__( 'Left', 'adri-ajdethemes' ),
                'sb_right'    => esc_html__( 'Right', 'adri-ajdethemes' ),
                'sb_bottom'   => esc_html__( 'Bottom', 'adri-ajdethemes' ),
                'sb_null'     => esc_html__( 'No sidebar', 'adri-ajdethemes' ),
            ),
        ));

        // Blog Post Nav ------------

        $wp_customize->add_setting('blog_post_nav', array(
            'capability'        => 'edit_theme_options',
            'default'           => true,
            'sanitize_callback' => 'sanitize_checkbox',
        ));
    
        $wp_customize->add_control('blog_post_nav', array(
            'type'     => 'checkbox',
            'section'  => 'blog',
            'priority' => 10,
            'label'    => esc_html__( 'Show post navigation (prev/next)', 'adri-ajdethemes' ),
        ));
        
        // Blog Recent Posts ------------

        $wp_customize->add_setting('blog_recent_posts', array(
            'capability'        => 'edit_theme_options',
            'default'           => true,
            'sanitize_callback' => 'sanitize_checkbox',
        ));
    
        $wp_customize->add_control('blog_recent_posts', array(
            'type'     => 'checkbox',
            'section'  => 'blog',
            'priority' => 10,
            'label'    => esc_html__( 'Show recent posts', 'adri-ajdethemes' ),
        ));
    

        /**
         * Page Title
         * 
         */
    
        $wp_customize->add_section('page_title', array(
            'title'         => esc_html__('Page Title', 'adri-ajdethemes'),
            'priority'      => 200,
            'description'   => esc_html__('Page title options for the blog and single blog post pages.', 'adri-ajdethemes')
        ));
        
        // Page Title - Layout ------------
    
        $wp_customize->add_setting('page_title_layout', array(
            'capability'        => 'edit_theme_options',
            'default'           => 'st-as-pt',
            'sanitize_callback' => 'sanitize_select'
        ));
    
        $wp_customize->add_control('page_title_layout', array(
            'type'      => 'radio',
            'section'   => 'page_title',
            'priority'  => 10,
            'label'     => esc_html__('Page title size:', 'adri-ajdethemes'),
            'choices'   => array(
                'pt-sm'         => esc_html__( 'Small', 'adri-ajdethemes' ),
                'pt-md'         => esc_html__( 'Medium', 'adri-ajdethemes' ),
                'pt-lg'         => esc_html__( 'Large', 'adri-ajdethemes' ),
                'st-as-pt'      => esc_html__( 'Use section title as page title', 'adri-ajdethemes' ),
                'pt_disable'    => esc_html__( 'No page title', 'adri-ajdethemes' ),
            ),
        ));

        // Page Title - Title ------------
    
        $wp_customize->add_setting('page_title_title', array(
            'capability'        => 'edit_theme_options',
            'default'           => 'Blog',
            'sanitize_callback' => 'sanitize_text_field'
        ));
        
        $wp_customize->add_control('page_title_title', array(
            'type'      => 'text',
            'section'   => 'page_title',
            'priority'  => 10,
            'label'     => esc_html__('Title', 'adri-ajdethemes'),
            'active_callback' => function() use ( $wp_customize ) {
                return ( 'pt_disable' !== $wp_customize->get_setting( 'page_title_layout' )->value() );
            }
        ));
        
        // Page Title - Subtitle ------------
    
        $wp_customize->add_setting('page_title_subtitle', array(
            'capability'        => 'edit_theme_options',
            'default'           => 'Our blog',
            'sanitize_callback' => 'sanitize_text_field'
        ));
        
        $wp_customize->add_control('page_title_subtitle', array(
            'type'      => 'text',
            'section'   => 'page_title',
            'priority'  => 10,
            'label'     => esc_html__('Subtitle (only blog posts)', 'adri-ajdethemes'),
            'active_callback' => function() use ( $wp_customize ) {
                return ( 
                    'pt_disable' !== $wp_customize->get_setting( 'page_title_layout' )->value()
                );
            }
        ));

        // Page Title - Text Align ------------
    
        $wp_customize->add_setting('page_title_txt_align', array(
            'capability'        => 'edit_theme_options',
            'default'           => 'text-center',
            'sanitize_callback' => 'sanitize_select'
        ));
    
        $wp_customize->add_control('page_title_txt_align', array(
            'type'      => 'radio',
            'section'   => 'page_title',
            'priority'  => 10,
            'label'     => esc_html__('Title text align:', 'adri-ajdethemes'),
            'choices'   => array(
                ''              => esc_html__( 'Left', 'adri-ajdethemes' ),
                'text-center'   => esc_html__( 'Center', 'adri-ajdethemes' ),
                'text-right'    => esc_html__( 'Right', 'adri-ajdethemes' ),
            ),
            'active_callback' => function() use ( $wp_customize ) {
                return ( 'pt_disable' !== $wp_customize->get_setting( 'page_title_layout' )->value() );
            }
        ));

        // Page Title - Background Color ------------

        $wp_customize->add_setting( 'page_title_bg', array(
                'default'           => 'rgba(16, 69, 71, 0.05)',
                'type'              => 'theme_mod',
                'capability'        => 'edit_theme_options',
                'sanitize_callback' => 'sanitize_color',
            )
        );

        $wp_customize->add_control( new Adri_Ajdethemes_Customize_Alpha_Color_Control( 
            $wp_customize, 'page_title_bg', array(
                    'label'        => esc_html__( 'Background', 'adri-ajdethemes' ),
                    'section'      => 'page_title',
                    'settings'     => 'page_title_bg',
                    'show_opacity' => true,
                    'palette'      => array(
                        'rgb(16, 69, 71)',
                        '#f4f4f4',
                        '#F2CD5D',
                        '#cccccc',
                        '#222222',
                        '#66686F',
                        '#AAABAF',
                    ),
                    'active_callback' => function() use ( $wp_customize ) {
                        return ( 
                            ! $wp_customize->get_setting( 'page_title_bg_img' )->value() &&
                            'pt_disable' !== $wp_customize->get_setting( 'page_title_layout' )->value() &&
                            'st-as-pt' !== $wp_customize->get_setting( 'page_title_layout' )->value()
                        );
                    }
                )
            )
        );

        // Page Title - Title Color ------------

        $wp_customize->add_setting( 'page_title_txt_color', array(
                'default'           => '#104547',
                'type'              => 'theme_mod',
                'capability'        => 'edit_theme_options',
                'sanitize_callback' => 'sanitize_color',
            )
        );

        $wp_customize->add_control( new Adri_Ajdethemes_Customize_Alpha_Color_Control( 
            $wp_customize, 'page_title_txt_color', array(
                    'label'        => esc_html__( 'Title Color', 'adri-ajdethemes' ),
                    'section'      => 'page_title',
                    'settings'     => 'page_title_txt_color',
                    'show_opacity' => true,
                    'palette'      => array(
                        '#104547',
                        '#f4f4f4',
                        '#F2CD5D',
                        '#cccccc',
                        '#222222',
                        '#66686F',
                        '#AAABAF',
                    ),
                    'active_callback' => function() use ( $wp_customize ) {
                        return ( 
                            'pt_disable' !== $wp_customize->get_setting( 'page_title_layout' )->value() &&
                            'st-as-pt' !== $wp_customize->get_setting( 'page_title_layout' )->value()
                        );
                    }
                )
            )
        );

        // Page Title - Subtitle Color ------------

        $wp_customize->add_setting( 'page_subtitle_txt_color', array(
                'default'           => '#ffd400',
                'type'              => 'theme_mod',
                'capability'        => 'edit_theme_options',
                'sanitize_callback' => 'sanitize_color',
            )
        );

        $wp_customize->add_control( new Adri_Ajdethemes_Customize_Alpha_Color_Control( 
            $wp_customize, 'page_subtitle_txt_color', array(
                    'label'        => esc_html__( 'Subtitle Color', 'adri-ajdethemes' ),
                    'section'      => 'page_title',
                    'settings'     => 'page_subtitle_txt_color',
                    'show_opacity' => true,
                    'palette'      => array(
                        '#104547',
                        '#f4f4f4',
                        '#F2CD5D',
                        '#cccccc',
                        '#222222',
                        '#66686F',
                        '#AAABAF',
                    ),
                    'active_callback' => function() use ( $wp_customize ) {
                        return ( 
                            'pt_disable' !== $wp_customize->get_setting( 'page_title_layout' )->value() &&
                            'st-as-pt' !== $wp_customize->get_setting( 'page_title_layout' )->value()
                        );
                    }
                )
            )
        );

        // Page Title - Background Image ------------

        $wp_customize->add_setting( 'page_title_bg_img', array(
            'capability'        => 'edit_theme_options',
            'sanitize_callback' => 'esc_url'
        ));

        $wp_customize->add_control( new WP_Customize_Image_Control(
                $wp_customize, 'page_title_bg_img',
                array(
                    'label'      => esc_html__( 'Background Image', 'adri-ajdethemes' ),
                    'section'    => 'page_title',
                    'settings'   => 'page_title_bg_img',
                    'active_callback' => function() use ( $wp_customize ) {
                        return ( 
                            'pt_disable' !== $wp_customize->get_setting( 'page_title_layout' )->value() &&
                            'st-as-pt' !== $wp_customize->get_setting( 'page_title_layout' )->value()
                        );
                    }
                )
            )
        );

        // Page Title - Background Overlay ------------

        $wp_customize->add_setting( 'page_title_bg_overlay', array(
                'default'           => 'rgba(244,244,244, 0.9)',
                'type'              => 'theme_mod',
                'capability'        => 'edit_theme_options',
                'sanitize_callback' => 'sanitize_color',
            )
        );

        $wp_customize->add_control( new Adri_Ajdethemes_Customize_Alpha_Color_Control( 
            $wp_customize, 'page_title_bg_overlay', array(
                    'label'        => esc_html__( 'Background Overlay', 'adri-ajdethemes' ),
                    'section'      => 'page_title',
                    'settings'     => 'page_title_bg_overlay',
                    'show_opacity' => true,
                    'palette'      => array(
                        '#104547',
                        '#f4f4f4',
                        '#F2CD5D',
                        '#cccccc',
                        '#222222',
                        '#66686F',
                        '#AAABAF',
                    ),
                    'active_callback' => function() use ( $wp_customize ) {
                        return ( 
                            $wp_customize->get_setting( 'page_title_bg_img' )->value()  &&
                            'pt_disable' !== $wp_customize->get_setting( 'page_title_layout' )->value() &&
                            'st-as-pt' !== $wp_customize->get_setting( 'page_title_layout' )->value()
                        );
                    }
                )
            )
        );


        /**
         * Navigation - Section
         * 
         */
    
        $wp_customize->add_section('nav', array(
            'title'         => esc_html__('Navigation', 'adri-ajdethemes'),
            'priority'      => 200,
            'description'   => esc_html__('Theme specific navigation options.', 'adri-ajdethemes')
        ));

        // Navigation Grid/FullWidth ------------
        
        $wp_customize->add_setting('nav_grid', array(
            'capability'        => 'edit_theme_options',
            'default'           => 'grid',
            'sanitize_callback' => 'sanitize_select'
        ));
    
        $wp_customize->add_control('nav_grid', array(
            'type'      => 'radio',
            'section'   => 'nav',
            'priority'  => 10,
            'label'     => esc_html__('Navbar width:', 'adri-ajdethemes'),
            'choices'  => array(
                'grid'   => esc_html__( 'Grid', 'adri-ajdethemes' ),
                'fw'     => esc_html__( 'Full width', 'adri-ajdethemes' ),
            ),
        ));

        // Navbar - Transparent Nav ------------

        $wp_customize->add_setting('nav_is_trans', array(
            'capability'        => 'edit_theme_options',
            'default'           => false,
            'sanitize_callback' => 'sanitize_checkbox',
        ));
        $wp_customize->add_control('nav_is_trans', array(
                'type'     => 'checkbox',
                'section'  => 'nav',
                'priority' => 10,
                'label'    => esc_html__( 'Enable transparent menu navbar.', 'adri-ajdethemes' ),
        ));

        // Navbar - Transparent Nav On Front Page ------------

        $wp_customize->add_setting('nav_is_trans_only_home', array(
            'capability'        => 'edit_theme_options',
            'default'           => true,
            'sanitize_callback' => 'sanitize_checkbox',
        ));
        $wp_customize->add_control('nav_is_trans_only_home', array(
                'type'     => 'checkbox',
                'section'  => 'nav',
                'priority' => 10,
                'label'    => esc_html__( 'Transparent navbar only on the homepage/front-page, and blog post with large featured image.', 'adri-ajdethemes' ),
                'active_callback' => function() use ( $wp_customize ) {
                    return ( true === $wp_customize->get_setting( 'nav_is_trans' )->value() );
                }
        ));

        // Navbar - Transparent Nav Color Style ------------
    
        $wp_customize->add_setting('nav_trans_color', array(
            'capability'        => 'edit_theme_options',
            'default'           => 'nav-trans-light',
            'sanitize_callback' => 'sanitize_select'
        ));
    
        $wp_customize->add_control('nav_trans_color', array(
                'type'      => 'radio',
                'section'   => 'nav',
                'priority'  => 10,
                'label'     => esc_html__('Transparent nav. menu items color style:', 'adri-ajdethemes'),
                'choices'  => array(
                    'nav-trans-light'  => esc_html__( 'Light color style', 'adri-ajdethemes' ),
                    'nav-trans-dark'   => esc_html__( 'Dark color style', 'adri-ajdethemes' ),
                ),
                'active_callback' => function() use ( $wp_customize ) {
                    return ( true === $wp_customize->get_setting( 'nav_is_trans' )->value() );
                }
        ));

        // Navbar - Transparent Menu Item ACCENT Style ------------
    
        $wp_customize->add_setting('nav_trans_menu_item_accent', array(
            'capability'        => 'edit_theme_options',
            'default'           => '',
            'sanitize_callback' => 'sanitize_select'
        ));
    
        $wp_customize->add_control('nav_trans_menu_item_accent', array(
                'type'      => 'radio',
                'section'   => 'nav',
                'priority'  => 10,
                'label'     => esc_html__('Transparent nav. menu item ACCENT (desktop) color style:', 'adri-ajdethemes'),
                'choices'  => array(
                    ''  => esc_html__( 'Accent Color - Primary (default)', 'adri-ajdethemes' ),
                    'nav-trans-menu-item-accent-light'   => esc_html__( 'Light Color', 'adri-ajdethemes' ),
                    'nav-trans-menu-item-accent-dark'   => esc_html__( 'Dark Color', 'adri-ajdethemes' ),
                ),
                'active_callback' => function() use ( $wp_customize ) {
                    return ( true === $wp_customize->get_setting( 'nav_is_trans' )->value() );
                }
        ));

        // Navbar - Sticky Nav ------------

        $wp_customize->add_setting('nav_is_sticky', array(
            'capability'        => 'edit_theme_options',
            'default'           => false,
            'sanitize_callback' => 'sanitize_checkbox',
        ));
        $wp_customize->add_control('nav_is_sticky', array(
                'type'     => 'checkbox',
                'section'  => 'nav',
                'priority' => 10,
                'label'    => esc_html__( 'Enable sticky menu navbar.', 'adri-ajdethemes' ),
        ));
        
        // Navbar - Show Search ------------

        $wp_customize->add_setting('nav_has_search', array(
            'capability'        => 'edit_theme_options',
            'default'           => true,
            'sanitize_callback' => 'sanitize_checkbox',
        ));
        $wp_customize->add_control('nav_has_search', array(
                'type'     => 'checkbox',
                'section'  => 'nav',
                'priority' => 10,
                'label'    => esc_html__( 'Show search icon in the nav. menu.', 'adri-ajdethemes' ),
        ));

        // Navbar - Show Cart ------------
        
        $wp_customize->add_setting('nav_has_cart', array(
            'capability'        => 'edit_theme_options',
            'default'           => true,
            'sanitize_callback' => 'sanitize_checkbox',
        ));
        $wp_customize->add_control('nav_has_cart', array(
                'type'     => 'checkbox',
                'section'  => 'nav',
                'priority' => 10,
                'label'    => esc_html__( 'Show cart icon in the nav. menu.', 'adri-ajdethemes' ),
        ));

        // Navbar - Show Social Icons ------------
        
        $wp_customize->add_setting('nav_has_social_icons', array(
            'capability'        => 'edit_theme_options',
            'default'           => true,
            'sanitize_callback' => 'sanitize_checkbox',
        ));
        $wp_customize->add_control('nav_has_social_icons', array(
                'type'     => 'checkbox',
                'section'  => 'nav',
                'priority' => 10,
                'label'    => esc_html__( 'Show social media icons in the nav. menu.', 'adri-ajdethemes' ),
        ));

        // Navbar - Color Style ------------
    
        $wp_customize->add_setting('nav_style', array(
            'capability'        => 'edit_theme_options',
            'default'           => '',
            'sanitize_callback' => 'sanitize_select'
        ));
    
        $wp_customize->add_control('nav_style', array(
                'type'      => 'radio',
                'section'   => 'nav',
                'priority'  => 10,
                'label'     => esc_html__('Navbar color style:', 'adri-ajdethemes'),
                'choices'  => array(
                    ''  => esc_html__( 'Light navbar (default)', 'adri-ajdethemes' ),
                    'nav-dark'   => esc_html__( 'Dark navbar', 'adri-ajdethemes' ),
                )
        ));
                
        // Dropdown - Color Style ------------
    
        $wp_customize->add_setting('dropdown_style', array(
            'capability'        => 'edit_theme_options',
            'default'           => '',
            'sanitize_callback' => 'sanitize_select'
        ));
    
        $wp_customize->add_control('dropdown_style', array(
                'type'      => 'radio',
                'section'   => 'nav',
                'priority'  => 10,
                'label'     => esc_html__('Dropdown (desktop) color style:', 'adri-ajdethemes'),
                'choices'  => array(
                    ''   => esc_html__( 'Dark style (default)', 'adri-ajdethemes' ),
                    'nav-dropdown-light'  => esc_html__( 'Light style', 'adri-ajdethemes' ),
                )
        ));

        // Nav - Menu Item ACCENT Style ------------
    
        $wp_customize->add_setting('nav_menu_item_accent', array(
            'capability'        => 'edit_theme_options',
            'default'           => '',
            'sanitize_callback' => 'sanitize_select'
        ));
    
        $wp_customize->add_control('nav_menu_item_accent', array(
                'type'      => 'radio',
                'section'   => 'nav',
                'priority'  => 10,
                'label'     => esc_html__('Menu item ACCENT (desktop) color style:', 'adri-ajdethemes'),
                'choices'  => array(
                    ''  => esc_html__( 'Accent Color - Primary (default)', 'adri-ajdethemes' ),
                    'nav-menu-item-accent-light'   => esc_html__( 'Light Color', 'adri-ajdethemes' ),
                    'nav-menu-item-accent-dark'   => esc_html__( 'Dark Color', 'adri-ajdethemes' ),
                )
        ));


        /**
         * Footer - Section
         * 
         */
    
        $wp_customize->add_section('footer', array(
            'title'         => esc_html__('Footer', 'adri-ajdethemes'),
            'priority'      => 200,
            'description'   => esc_html__('Theme specific footer options.', 'adri-ajdethemes')
        ));

        // Footer copyright ------------

        $wp_customize->add_setting('footer_txt_left', array(
            'capability'        => 'edit_theme_options',
            'default'           => '© 2020 All rights reserved.',
            'sanitize_callback' => 'wp_kses_post'
        ));
        $wp_customize->add_control('footer_txt_left', array(
            'type'      => 'text',
            'section'   => 'footer',
            'priority'  => 10,
            'label'     => esc_html__('Footer bottom text:', 'adri-ajdethemes')
        ));

        // Footer scroll icon ------------
    
        $wp_customize->add_setting('footer_scroll', array(
                'capability'        => 'edit_theme_options',
                'default'           => true,
                'sanitize_callback' => 'sanitize_checkbox',
            )
        );
        $wp_customize->add_control('footer_scroll', array(
                'type'     => 'checkbox',
                'section'  => 'footer',
                'priority' => 10,
                'label'    => esc_html__( 'Show "scroll top" icon, in the footer.', 'adri-ajdethemes' ),
            )
        );
        
        // Footer social icons ------------
    
        $wp_customize->add_setting('footer_social_icons', array(
                'capability'        => 'edit_theme_options',
                'default'           => true,
                'sanitize_callback' => 'sanitize_checkbox',
            )
        );
        $wp_customize->add_control('footer_social_icons', array(
                'type'     => 'checkbox',
                'section'  => 'footer',
                'priority' => 10,
                'label'    => esc_html__( 'Show social icons icon, in the footer.', 'adri-ajdethemes' ),
            )
        );

        // Footer "reveal" effect ------------

        $wp_customize->add_setting('footer_has_reveal', array(
                'capability'        => 'edit_theme_options',
                'default'           => false,
                'sanitize_callback' => 'sanitize_checkbox',
            )
        );
        $wp_customize->add_control('footer_has_reveal', array(
                'type'     => 'checkbox',
                'section'  => 'footer',
                'priority' => 10,
                'label'    => esc_html__( 'Enable footer "reveal" effect.', 'adri-ajdethemes' ),
            )
        );

        // Footer height
        $wp_customize->add_setting('footer_height', array(
            'capability'        => 'edit_theme_options',
            'default'           => 530,
            'sanitize_callback' => 'esc_html'
        ));
        $wp_customize->add_control('footer_height', array(
            'type'      => 'text',
            'section'   => 'footer',
            'priority'  => 10,
            'label'     => esc_html__('Footer height (in pixels, without px):', 'adri-ajdethemes'),
            'active_callback' => function() use ( $wp_customize ) {
                return ( true === $wp_customize->get_setting( 'footer_has_reveal' )->value() );
            }
        ));

        // Footer Color Style ------------
    
        $wp_customize->add_setting('footer_text_color_style', array(
            'capability'        => 'edit_theme_options',
            'default'           => 'footer-color-dark',
            'sanitize_callback' => 'sanitize_select'
        ));
    
        $wp_customize->add_control('footer_text_color_style', array(
                'type'      => 'radio',
                'section'   => 'footer',
                'priority'  => 10,
                'label'     => esc_html__('Footer text color:', 'adri-ajdethemes'),
                'choices'  => array(
                    'footer-color-dark'   => esc_html__( 'Dark color style', 'adri-ajdethemes' ),
                    'footer-color-light'  => esc_html__( 'Light color style', 'adri-ajdethemes' ),
                )
        ));


        /**
         * Social Icons - Section
         * 
         */
    
        $wp_customize->add_section('social', array(
            'title'         => esc_html__('Social Icons', 'adri-ajdethemes'),
            'priority'      => 200,
            'description'   => esc_html__('Display your social profiles with social media icons (FontAwesome) in multiple locations.', 'adri-ajdethemes')
        ));

        $wp_customize->add_setting('social_icons_shortcode', array(
            'capability'        => 'edit_theme_options',
            'default'           => 'Enter a shortcode for social media icons',
            'sanitize_callback' => 'wp_kses_post',
        ));
        $wp_customize->add_control('social_icons_shortcode', array(
            'type'      => 'textarea',
            'section'   => 'social',
            'priority'  => 10,
            'label'     => esc_html__('Social media icons shortcode:', 'adri-ajdethemes'),
        ));

        
        /**
         * Colors - Section
         * 
         */
    
        $wp_customize->add_section('colors', array(
            'title'         => esc_html__('Colors', 'adri-ajdethemes'),
            'priority'      => 200,
            'description'   => esc_html__('Change the theme brand colors. After publish, close the customizer and hard refresh the page (Cmd/Ctrl + Sift + R). This color changes must be done in the parent theme to have effect.', 'adri-ajdethemes')
        ));    
            
        // note: controls added via customizer-scss vars loop function.
    }
    add_action( 'customize_register', 'adri_ajdethemes_customize_register' );
}

function sanitize_select( $input, $setting ) {
    $input   = sanitize_key( $input );
    $choices = $setting->manager->get_control( $setting->id )->choices;
    return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
}

function sanitize_checkbox( $checked ) {
    return ( ( isset( $checked ) && true === $checked ) ? true : false );
}

/**
 * Sanitize colors.
 *
 * @since 1.0.0
 * @param string $value The color.
 * @return string
 */
function sanitize_color( $value ) {
	// This pattern will check and match 3/6/8-character hex, rgb, rgba, hsl, & hsla colors.
	$pattern = '/^(\#[\da-f]{3}|\#[\da-f]{6}|\#[\da-f]{8}|rgba\(((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*,\s*){2}((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*)(,\s*(0\.\d+|1))\)|hsla\(\s*((\d{1,2}|[1-2]\d{2}|3([0-5]\d|60)))\s*,\s*((\d{1,2}|100)\s*%)\s*,\s*((\d{1,2}|100)\s*%)(,\s*(0\.\d+|1))\)|rgb\(((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*,\s*){2}((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*)|hsl\(\s*((\d{1,2}|[1-2]\d{2}|3([0-5]\d|60)))\s*,\s*((\d{1,2}|100)\s*%)\s*,\s*((\d{1,2}|100)\s*%)\))$/';
	\preg_match( $pattern, $value, $matches );
	// Return the 1st match found.
	if ( isset( $matches[0] ) ) {
		if ( is_string( $matches[0] ) ) {
			return $matches[0];
		}
		if ( is_array( $matches[0] ) && isset( $matches[0][0] ) ) {
			return $matches[0][0];
		}
	}
	// If no match was found, return an empty string.
	return '';
}