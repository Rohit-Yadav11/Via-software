<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @package AdriAjdethemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<div class="col-lg-8 center-no-results">
    <section class="no-results">
        <?php 
        if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

            <h1><?php esc_html_e( 'Welcome to the Adri theme', 'adri-ajdethemes' ) ?></h1>
            
            <ul class="onboarding-steps">
                <li>
                    <span class="step-nbr"></span>
                    <p>
                        <?php
                        echo wp_kses(
                            __( 'Make sure to read the <strong>help file</strong>, that is located in the
                            download folder, for detailed instructions how to setup 
                            the theme.', 'adri-ajdethemes' ),
                            array(
                                'strong' => array(),
                            )
                        ) ?>
                    </p>
                </li>
                <li>
                    <span class="step-nbr"></span>
                    <p>
                        <?php
                        printf(
                            wp_kses(
                                __( 'Are you ready to publish your first post? <br/><a href="%1$s">Get started here</a>.', 'adri-ajdethemes' ),
                                array(
                                    'a' => array(
                                        'href' => array(),
                                    ),
                                    'br' => array()
                                )
                            ),
                            esc_url( admin_url( 'post-new.php' ) )
                        ); ?>
                    </p>
                </li>
            </ul>

        <?php
        elseif ( is_search() ) : ?>

            <span class="no-results-icon"></span>
            <p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with a different search.', 'adri-ajdethemes' ) ?></p>

        <?php 
        else : ?>

            <span class="no-results-icon"></span>
            <p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for.', 'adri-ajdethemes' ) ?></p>

        <?php 
        endif; ?>
    </section><!-- end of .no-results -->
</div><!-- end of .col-lg-8 -->