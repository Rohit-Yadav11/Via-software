<?php
/*
Plugin Name: Log Out Shortcode
Plugin URI: https://om4.io/plugins/log-out-shortcode/
Description: Easily add a log out link to a post or page using a simple <code>[logout]</code> shortcode.
Version: 1.0.8
Author: OM4
Author URI: https://om4.io/
Text Domain: log-out-shortcode
License: GPLv2
 */

/*
Copyright 2014-2016 OM4 (email: plugins@om4.com.au    web: https://om4.io/)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Create logout link
 *
 * @param array  $atts           The shortcode attributes.
 * @param string $content        The shortcode content (if any).
 * @param string $shortcode_name Name of the shortcode.
 * @return string
 */
function logout_shortcode( $atts, $content = '', $shortcode_name = '' ) {
	$defaults = array(
		'text'     => __( 'Log out' ), // This is the default log out text from wp-includes/general-template.php.
		'redirect' => '', // path/URL to redirect to after logging out.
		'class'    => 'logout', // CSS Class(es) to use in link.
	);

	if ( ! is_user_logged_in() ) {
		return '';
	}

	$atts = shortcode_atts( $defaults, $atts );

	if ( 'logout_to_home' === $shortcode_name ) {
		$atts['redirect'] = 'home';
	} elseif ( 'logout_to_current' === $shortcode_name ) {
		$atts['redirect'] = 'current';
	}

	if ( 'home' === $atts['redirect'] ) {
			$atts['redirect'] = home_url();
	} elseif ( 'current' === $atts['redirect'] ) {
		$atts['redirect'] = get_the_permalink();
	}

	$class_html = '';
	if ( '' !== $atts['class'] ) {
		// Multiple classes are separated by a space.
		$classes    = explode( ' ', $atts['class'] );
		$classes    = array_map( 'sanitize_html_class', $classes );
		$class_html = ' class="' . implode( ' ', $classes ) . '"';
	}

	return '<a href="' . esc_url( wp_logout_url( $atts['redirect'] ) ) . '"' . $class_html . '>' . esc_html( $atts['text'] ) . '</a>';

}
add_shortcode( 'logout', 'logout_shortcode' );
add_shortcode( 'logout_to_current', 'logout_shortcode' );
add_shortcode( 'logout_to_home', 'logout_shortcode' );
