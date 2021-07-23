<?php
/**
 * Themed search form.
 *
 * @package AdriAjdethemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>


<form method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <span class="screen-reader-text"><?php esc_html_e( 'Search for:', 'adri-ajdethemes' ) ?></span>

    <div class="form-group">
        <label for="widget_search"><?php esc_html_e( 'Search a keyword', 'adri-ajdethemes' ) ?></label>

        <input 
            id="widget_search"
            class="search-field form-style"
            type="search" 
            value="<?php echo esc_attr( get_search_query() ) ?>" 
            name="s"
            title="<?php esc_attr_e( 'Search for:', 'adri-ajdethemes' ) ?>" />

        <button><i class="fas fa-search"></i></button>
    </div>
    
</form>