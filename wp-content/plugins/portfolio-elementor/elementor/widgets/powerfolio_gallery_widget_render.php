<?php
$pro_version = true; //pe_fs()->can_use_premium_code__premium_only();		
$settings = $this->get_settings();

$postsperpage = 50;
$showfilter = $settings['showfilter'];
$style = $settings['style'];
$columns = $settings['columns'];
$margin = $settings['margin'];
$linkto = 'image';
$hover = $settings['hover'];
$zoom_effect = '';
if ( array_key_exists('zoom_effect', $settings ) ) {
    $zoom_effect = $settings['zoom_effect'];
}

$tax_text = $settings['tax_text'];
$post_type = '';


$portfolio_type = '';
$portfolio_isotope = 'elpt-portfolio-content-isotope';

    $retour ='';	

    $retour .='<div class="elpt-portfolio">';			

        //Filter
        if ($showfilter != 'no') {
            $retour .='<div class="elpt-portfolio-filter">';
            
            $all_button_data_filter = apply_filters( 'elpt_gallery_all_button_data_filter', '*' );

            if ($tax_text !='') {
                $retour .='<button class="portfolio-filter-item item-active" data-filter="'.$all_button_data_filter.'" style="background-color:' .';">'.$tax_text.'</button>';
            } else {
                //$retour .='<button class="portfolio-filter-item item-active" data-filter="*" style="background-color:' .';">'.esc_html('All', 'elemenfolio').'</button>';
            }   

            //Get all Tags
            $tag_list = array();
            foreach($settings['list'] as $item) {
                $tag = $item['list_filter_tag'];
                if ( ! in_array ($tag, $tag_list ) ){
                    $tag_list[] = $tag;
                } 
            }
            
            //Sort tags in alphabetical order
            sort($tag_list);

            //Filter tag list
            $tag_list = apply_filters( 'elpt_gallery_terms_list', $tag_list );
            
            //List Tags
            foreach($tag_list as $item) {
                $item_slug = elpt_get_text_slug($item);
                $retour .='<button class="portfolio-filter-item" style="background-color:' .';" data-filter=".elemenfoliocategory-'.esc_attr($item_slug).'">'.$item.'</button>';
            }

            $retour .='</div>';
        }				

        //Portfolio style
        if ($style == 'masonry' ) {
            $portfoliostyle = 'elpt-portfolio-style-masonry';
        }
        else if ($style == 'specialgrid1' ) {
            $portfoliostyle = 'elpt-portfolio-special-grid-1';
        }
        else if ($style == 'specialgrid2' ) {
            $portfoliostyle = 'elpt-portfolio-special-grid-2';
        }
        else if ($style == 'specialgrid3' ) {
            $portfoliostyle = 'elpt-portfolio-special-grid-3';
        }
        else if ($style == 'specialgrid4' ) {
            $portfoliostyle = 'elpt-portfolio-special-grid-4';
        }
        else if ($style == 'specialgrid5' ) {
            $portfoliostyle = 'elpt-portfolio-special-grid-5';
        }	
        else if ($style == 'specialgrid6' ) {
            $portfoliostyle = 'elpt-portfolio-special-grid-6';
        }	
        else if ($style == 'grid_builder' ) {
            $portfoliostyle = 'elpt-portfolio-grid-builder';
            $portfolio_isotope = 'elpt-portfolio-content-packery';
        }					
        else {
            $portfoliostyle = 'elpt-portfolio-style-box';
        }

        //Columns
        if ($columns == '2') {
            $portfoliocolumns = 'elpt-portfolio-columns-2';
        }
        else if ($columns == '3') {
            $portfoliocolumns = 'elpt-portfolio-columns-3';
        }
        else if ($columns == '5') {
            $portfoliocolumns = 'elpt-portfolio-columns-5';
        }
        else if ($columns == '6') {
            $portfoliocolumns = 'elpt-portfolio-columns-6';
        }
        else {
            $portfoliocolumns = 'elpt-portfolio-columns-4';
        }

        //Margin
        if ($margin == 'yes' ) {
            $portfoliomargin = 'elpt-portfolio-margin';
        }
        else {
            $portfoliomargin = '';
        }
    

        $retour .='<div class="elpt-portfolio-content '.$portfolio_isotope.' '.$portfoliostyle.' '.$zoom_effect.' '.$hover.' '.$portfoliocolumns.' '. $portfoliomargin.'">';

            foreach($settings['list'] as $item){

                //print_r($item);

                $portfolio_image_ready = $item['list_image']['url'];                    

                //Fancybox or link
                $portfolio_link = '#';
                
                if ( array_key_exists('list_external_link', $item ) && $item['list_external_link']['url'] != '' ) {
                    $portfolio_link = $item['list_external_link']['url']; 
                }

                $portfolio_link_class = '';
                $portfolio_link_rel = '';
                $portfolio_link_target = '';
                $portfolio_link_follow = '';
                if ( array_key_exists('linkto', $item ) ) {
                    $linkto = $item['linkto'];
                }  
                else {
                    $linkto = 'image';
                }              
                if ( $linkto == 'image') {
                    $portfolio_link = $portfolio_image_ready;
                    $portfolio_link_class = 'elpt-portfolio-lightbox';
                    $portfolio_link_rel = 'rel="elpt-portfolio"';
                }
                elseif ( $linkto == 'link' && array_key_exists('list_external_link', $item ) ) {
                    $portfolio_link = $item['list_external_link']['url'];
                    if ( $item['list_external_link']['is_external'] == true ) {
                        $portfolio_link_target = 'target="_blank"';
                    }
                    if ( $item['list_external_link']['nofollow'] == true ) {
                        $portfolio_link_follow = 'rel="nofollow"';
                    }
                    
                }
                
                $classes = 'elemenfoliocategory-'.elpt_get_text_slug($item['list_filter_tag']); 
                
                $retour .='<div class="portfolio-item-wrapper '.$classes.'">';
                    $retour .='<a href="'.esc_url($portfolio_link) .'" class="portfolio-item '.esc_attr($portfolio_link_class) .'" '.esc_attr($portfolio_link_rel) .' style="background-image: url('.esc_url($portfolio_image_ready).')" title="'.get_the_title().'" '.$portfolio_link_target.' '.$portfolio_link_follow.'>';
                        $retour .='<img src="'.esc_url($portfolio_image_ready) .'" title="'.get_the_title().'" alt="'.get_the_title().'"/>';
                        $retour .='<div class="portfolio-item-infos-wrapper" style="background-color:' .';"><div class="portfolio-item-infos">';
                            $retour .='<div class="portfolio-item-title">'.get_the_title().'</div>';
                            $retour .='<div class="portfolio-item-category">';
                                $thisterm = $item['list_filter_tag'];
                                $retour .='<span class="elpt-portfolio-cat">' .esc_html($thisterm) .'</span>';                                     									
                            $retour .='</div>';
                        $retour .='</div></div>';
                    $retour .='</a>';
                $retour .='</div>';

            }

        $retour .='</div>';

    $retour .='</div>';		

echo $retour;
?>