<?php

//Activate PRO version
if ( !function_exists( 'elpt_activate' ) ) {
    //Flush rewrite rules after plugin activation
    function elpt_activate()
    {
        if ( !get_option( 'elpt_flush_rewrite_rules_flag' ) ) {
            add_option( 'elpt_flush_rewrite_rules_flag', true );
        }
    }  
    register_activation_hook( __FILE__, array( 'ELPT_portfolio_Post_Types', 'elpt_add_cpt_support' ) );  
}

//Turn text into a slug
function elpt_get_text_slug($text) {
    // strip out all whitespace
    $text = preg_replace('/\s+/', '_', $text);
    // convert the string to all lowercase
    $text = strtolower($text);

    return $text;
}

//Upgrade message
function get_upgrade_message() {
	$raw ='';	
	$raw .='<div style="border: 1px solid #eee; padding: 10px; background: #eee; border-radius: 6px;">';	
		$raw .='<h3 style="font-weight: bold; tet-transform: uppercase; font-size: 14px; margin-bottom: 10px; text-trasnform: uppercase;">'.__('ENABLE ALL FEATURES', 'powerfolio').'</h3>';	
		$raw .='<p style="margin-bottom: 10px; font-size: 12px; line-heigh: 22px;">'.__( 'Upgrade your plugin to PRO version and unlock all features!', 'powerfolio' ).'</p>';	
		$raw .='<a href="' . pe_fs()->get_upgrade_url() . '" style="background: #ea0e59; color: #fff; font-weight: bold; padding: 5px 10px; border-radius: 3px; display: inline-block; font-size: 14px; text-transform: uppercase;">'.__( 'Click here to Upgrade', 'powerfolio' ).'</a>';	
		//$raw .='<hr style="margin-top: 20px; margin-bottom: 20px;">';
		$raw .='<p style="margin-bottom: 10px; font-size: 12px; font-style: italic; margin-top: 5px;">'.__( 'Get access to the Special Grids, Grid Builder, extra customization options, adn much more!', 'powerfolio' ).'</p>';	
			/*$raw .='<ul style="list-style-type: circle; list-style-position: outside; font-style: italic;">';
				$raw .='<li style="margin-bottom: 5px;">'.__('- Grid Builder', 'powerfolio').'</li>';
				$raw .='<li style="margin-bottom: 5px;">'.__('- All customization options enabled for both widgets (portfolio and image gallery)', 'powerfolio').'</li>';
				$raw .='<li style="margin-bottom: 5px;">'.__('- 15+ hover effects', 'powerfolio').'</li>';
				$raw .='<li style="margin-bottom: 5px;">'.__('- 8 grid styles', 'powerfolio').'</li>';						
				$raw .='<li style="margin-bottom: 5px;">'.__('- Extra CSS effects', 'powerfolio').'</li>';
				$raw .='<li style="margin-bottom: 5px;">'.__('- Option to display a specific portfolio category', 'powerfolio').'</li>';
				$raw .='<li style="margin-bottom: 5px;">'.__('- Option to display content from any post type to the grid', 'powerfolio').'</li>';
				$raw .='<li style="margin-bottom: 5px;">'.__('- Extra customization options', 'powerfolio').'</li>';
			$raw .='</ul>';*/
		
	$raw .='</div>';

	return $raw;
}