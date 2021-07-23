<?php
/**
 * The template for displaying the secondary menu.
 *
 * @package AdriAjdethemes
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


if ( (has_nav_menu( 'secondary-menu-left' ) && wp_nav_menu( array( 'theme_location' => 'secondary-menu-left', 'echo' => false ) ) !== false) || (has_nav_menu( 'secondary-menu-right' ) && wp_nav_menu( array( 'theme_location' => 'secondary-menu-right', 'echo' => false ) ) !== false) ) {
    
    echo '<div class="secondary-menu">';

        adri_ajdethemes_has_nav_grid();

            if ( has_nav_menu( 'secondary-menu-left' ) ) {
                wp_nav_menu( array( 'theme_location' => 'secondary-menu-left' ) );
            }

            if ( has_nav_menu( 'secondary-menu-right' ) ) {
                wp_nav_menu( array( 'theme_location' => 'secondary-menu-right' ) );
            }

        adri_ajdethemes_has_nav_grid_end();

    echo '</div><!-- end of .secondary-menu -->';

}