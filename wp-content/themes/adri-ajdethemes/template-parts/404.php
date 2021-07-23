<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package AdriAjdethemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<main class="page-404">
    
    <div class="container">
        <div class="row">
            <div class="col-lg-12">

                <div class="content-wrapper">
                    <h1>404 <span><?php esc_html_e( 'Page not found.', 'adri-ajdethemes' ) ?></span></h1>
                    <p><?php esc_html_e( 'It looks like nothing was found at this location ', 'adri-ajdethemes' ); ?>, <a href="<?php echo esc_url( home_url() ); ?>"><?php esc_html_e( 'go back.', 'adri-ajdethemes' ); ?></a></p>
                </div>

            </div>
        </div>
    </div>
    
</main><!-- / .wrapper-404 -->