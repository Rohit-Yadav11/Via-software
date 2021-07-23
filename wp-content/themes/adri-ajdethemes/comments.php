<?php
/**
 * The template for displaying comments.
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @package AdriAjdethemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
 * Don't show comments for password protected posts.
 * 
 */
if ( post_password_required() ) {
	return;
}

/*
 * Comment Reply Script.
 * 
 */
if ( comments_open() && get_option( 'thread_comments' ) ) {
	wp_enqueue_script( 'comment-reply' );
}
?>

<div id="comments" class="comments-area">

    <?php
	if ( have_comments() ) :
		?>
		<span class="comments-title">
			<?php
			if ( '1' === get_comments_number() ) {
				printf( wp_kses( '<span>1</span>' . __( ' Comment', 'adri-ajdethemes' ), ['span' => []] ), get_comments_number() );
			} else {
				printf( wp_kses( '<span>%d</span>' . __( ' Comments', 'adri-ajdethemes' ), ['span' => []] ), get_comments_number() );
			} ?>
		</span><!-- .comments-title -->

		<?php the_comments_navigation(); ?>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
					'avatar_size'=> 70,
					'short_ping'  => true,
				)
			);
			?>
		</ol><!-- .comment-list -->

		<?php
		the_comments_navigation();

		// If comments are closed and there are comments.
		if ( ! comments_open() ) :
			?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'adri-ajdethemes' ); ?></p>
			<?php
		endif;

	endif; // Check for have_comments(). ?>

	<?php 
		// Comment form (leave a reply)
		$req 		= get_option( 'require_name_email' );
		$comments_args = array(
			'class_submit'			=> 'btn btn-classic',
			'title_reply_before'	=> '<span class="comment-reply-title">',
			'title_reply_after'		=> '</span>',
			'comment_notes_after' 	=> '',
			// Comment
			'comment_field' 		=> '
				<div class="form-group">
					<label for="comment">' . 
						esc_attr_x( 'Comment', 'noun', 'adri-ajdethemes' ) . 
						( $req ? '<span> *</span>' : '' ) . 
					'</label>
					<textarea 
						id="comment" 
						class="form-style" 
						name="comment" 
						placeholder="' . esc_attr__( 'Your comment', 'adri-ajdethemes' ) . '"
						rows="5"
						required
					></textarea>
				</div>',

			'fields' => apply_filters( 'comment_form_default_fields', array(
				// Name
				'author' => '
				<div class="row">
					<div class="col-lg-4">
						<div class="form-group">
							<label for="author">' . 
								esc_html__( 'Name', 'adri-ajdethemes' ) . 
								( $req ? '<span> *</span>' : '' ) .
							'</label>
							<input 
								id="author" 
								class="form-style" 
								name="author" 
								type="text" 
								placeholder="' . esc_attr__( 'Enter your name', 'adri-ajdethemes' ) . '"
								required
							/>
						</div>
					</div>',
				// Email
				'email'  => '
					<div class="col-lg-4">
						<div class="form-group">
							<label for="email">' . 
								esc_html__( 'Email', 'adri-ajdethemes' ) . 
								( $req ? '<span> *</span>' : '' ) .
							'</label>
							<input 
								id="email" 
								class="form-style" 
								name="email" 
								type="text" 
								size="30"
								placeholder="' . esc_attr__( 'Enter your email', 'adri-ajdethemes' ) . '"
								required
							/>
						</div>
					</div>',
				// Website
				'url'    	=> '
					<div class="col-lg-4">
						<div class="form-group">
							<label for="website">' . 
								esc_html__( 'Website', 'adri-ajdethemes' ) .
							'</label>
							<input 
								id="website" 
								class="form-style" 
								name="website" 
								type="url" 
								size="30"
							/>
						</div>
					</div>
				</div>',
				// Cookie (checkbox)
				'cookies' 	=> '
					<div class="comment-form-cookies-consent form-group-checkbox">
						<input 
							id="wp-comment-cookies-consent" 
							name="wp-comment-cookies-consent" 
							type="checkbox" 
							value="yes"
						>
						<label for="wp-comment-cookies-consent" class="form-style-checkbox" tabindex="0">' . 
							esc_html__( 'Save my name, email, and website in this browser for the next time I comment.', 'adri-ajdethemes' ) . 
						'</label>
					</div>
				' )
			),
		);
		comment_form( $comments_args );
	?>
</div><!-- end of #comments -->