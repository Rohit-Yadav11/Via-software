<?php
/*
Plugin Name: Portfolio Carousel Widget for Elementor
Plugin URI: http://wppug.com
Description: 
Author: wppug
Version: 1.0
Author URI: http://wppug.com
*/
function elpug_portfolio_carousel() {
	/*
	 * Elementor
	*/
	require ('elementor/extend-elementor.php');

	/*
	 * Shortcodes
	 */
	require ('portfolio-carousel-shortcodes.php');
}

elpug_portfolio_carousel();