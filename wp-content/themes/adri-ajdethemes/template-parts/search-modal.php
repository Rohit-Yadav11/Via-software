<?php
/**
 * Search modal
 *
 * @package AdriAjdethemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
} ?>


<aside class="search-modal">
    <button class="dark-bg-click-close"></button>
    <button class="x-close"><?php esc_html_e( 'close', 'adri-ajdethemes' ); ?></button>

    <div class="search-modal-content">

        <form method="get" class="search-form" action="<?php echo esc_url(home_url( '/' )); ?>">
            <label>
                <span class="screen-reader-text"><?php echo _x( 'Search for:', 'label', 'adri-ajdethemes' ) ?></span>
            </label>
            <input type="search" class="search-field"
                    placeholder="<?php echo esc_attr_x( 'Search …', 'placeholder', 'adri-ajdethemes' ) ?>"
                    value="<?php echo get_search_query() ?>" name="s"
                    title="<?php echo esc_attr_x( 'Search for:', 'label', 'adri-ajdethemes' ) ?>" required autofocus />
            <button><i class="fas fa-search"></i></button>
        </form>

    </div><!-- end of .search-modal-content -->
</aside><!-- end of .search-modal -->