<?php


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ELPT_Admin_Page {

	// Construct - Hook everything
	public function __construct() {
	    add_action('admin_menu', array( __CLASS__, 'elpt_setup_menu'));
	    //add_action('admin_enqueue_scripts', array( __CLASS__, 'wpmld_css_and_js') );
	 }
	
	public static function elpt_setup_menu(){

		//Enqueue color picker
		wp_enqueue_style( 'wp-color-picker' );
		//wp_enqueue_script( 'powerfolio-js', get_template_directory_uri().'/myscript.js', array( 'wp-color-picker','jquery' ), false, true );
		wp_enqueue_script( 'powerfolio-js', plugin_dir_url( __FILE__ ) .  '../js/powerfolio-admin.js', array( 'wp-color-picker' ), '20151218', true );

		//Create Admin Page
		$page_title = 'Powerfolio';
		$menu_title = 'Powerfolio';
		$capability = 'edit_posts';
		$menu_slug = 'elementor_portfolio';
		$function = array( __CLASS__, 'elpt_options_page');
		$icon_url = 'dashicons-layout';
		$position = 99;

		add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );

		//Create Settings
		$option_group = 'elpt';

		register_setting( $option_group, 'elpt_color' );

		// Color Section
		$settings_section = 'elpt_main';
		$page = 'elpt';
		add_settings_section( $settings_section, __( 'Settings', 'elpt' ), '', $page );
		add_settings_field( 'elpt_color', __('Color Scheme', 'elpt'), array( __CLASS__, 'elpt_color_callback'), $page, 'elpt_main' );

		//Shortcode Section
		//add_settings_section( 'elpt_howto', __( 'How to display the portfolio grid', 'elpt' ), 'elpt_shortcode_callback', $page );
	}

	//Fields Callback
	public static function elpt_color_callback(){
		echo '<input type="text" name="elpt_color" class="color-picker" value="' .get_option("elpt_color") .'"> Select the main color of your website <br>';
	}	

	//Texts
	public static function elpt_shortcode_callback() {
		
	}


	//Page
	public static function elpt_options_page() {
	?>
		<div class="wrap">
			<!-- Intro -->
			<h1><?php esc_html_e( 'Powerfolio', 'powerfolio' ) ?></h1>
			
			<h2><?php esc_html_e( 'Welcome to Powerfolio (Portfolio for Elementor)!', 'powerfolio' ) ?></h2>
			<?php if ( pe_fs()->is_not_paying() ) { ?>
				<a href="<?php echo pe_fs()->get_upgrade_url(); ?>" style="padding: 10px; background: #ff4e56; color: #fff; border:0px solid #ccc; border-radius: 6px; text-decoration: none; display: block; margin-bottom: 15px;"><?php _e( '<strong>WARNING:</strong> You are now using the free version of the plugin. Upgrade to PRO to unlock all features. <strong>CLICK HERE TO UPGRADE</strong>', 'powerfolio' ) ?></a>
			<?php } ?>
			
			<div style="padding: 10px; border: 1px solid #ccc; border-radius: 6px; background: #fff;">
				<h2><?php esc_html_e( 'Version 2.0 - New features!', 'powerfolio' ) ?></h2></p>
				<ul>
					<li><?php _e( '<strong>- Grid Builder:</strong> Customize the width and height of each item of your gallery grid and create exclusive layouts with a packery layout! (PRO version only)', 'powerfolio' ) ?></li>
					<li><?php _e( '<strong>- Image Gallery Widget:</strong> A new widget to create filterable image galleries direct from the Elementor screen! It have the same features of the portfolio widget, but there is no need to use the portfolio post type for that.', 'powerfolio' ) ?></li>
					<li><?php _e( '<strong>- WP Filters:</strong> We added some filters on the plugin code, which makes it easier to customize some parts of the plugin - PHP knowledge required.', 'powerfolio' ) ?></li>
					<li><?php _e( '<strong>- Customization Options:</strong> We added some new customization options on widgets, and we plan to add some more!', 'powerfolio' ) ?></li>
					<li><?php _e( '<strong>- Code improvements:</strong> We did some code improvement to speed up the performance.', 'powerfolio' ) ?></li>
				</ul>
			</div>
			<!-- /intro -->

			<!-- How to use -->
			<h2><?php esc_html_e( 'Getting Started', 'powerfolio' ) ?></h2>
			<p><?php esc_html_e( "Please check the following video tutorial with the first steps on how to use the plugin.", 'powerfolio' ) ?></p>

			<iframe width="560" height="315" src="https://www.youtube.com/embed/eikLVsTO0yw" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			<p><?php _e( '<strong>You can also follow the following steps to start using the plugin:</strong>', 'powerfolio' ) ?></p>
			<ul>
			<li>- <?php esc_html_e( 'Go to Settings > Permalinks and re-save your permalink structure.', 'powerfolio' ) ?></li>
				<li>- <?php esc_html_e( 'Go to Portfolio > Add new Item', 'powerfolio' ) ?></li>
				<li>- <?php esc_html_e( 'Add the first item of your portfolio. Similar to a regular post. Make sure to add a featured image.', 'powerfolio' ) ?></li>
				<li>- <?php esc_html_e( 'Repeat the steps to add some items to your portfolio.', 'powerfolio' ) ?></li>
				<li>- <?php esc_html_e( 'Create a new page using Elementor.', 'powerfolio' ) ?></li>
				<li>- <?php esc_html_e( 'Drag and drop the portfolio widget to your page.', 'powerfolio' ) ?></li>
				<li>- <?php esc_html_e( 'Customize it using the widget options and publish the page.', 'powerfolio' ) ?></li>
				<li>- <?php esc_html_e( 'Done! Your new portfolio is ready!', 'powerfolio' ) ?></li>
			</ul>
			
			
			
			<!-- /How to use -->
			<br/><br/><br/><hr/><br/>
			<!-- Settings -->
			<form action="options.php" method="post">				
				<?php settings_fields( 'elpt' ); ?>
				<?php do_settings_sections( 'elpt' ); ?>
				<input name="Submit" type="submit" value="<?php esc_attr_e( 'Save Changes', 'powerfolio' ); ?>" class="button button-primary" />
				<br/><br/><br/><hr/><br/>
				<!-- / Settings -->				
			</form>

			<!-- Shortcode -->			
			<h2><?php esc_html_e( 'Display using a shortcode', 'powerfolio' ); ?></h2>
			<p><?php esc_html_e( 'You can also display the portfolio grid on a page/post (or on Gutenberg) using the [powerfolio] shortcode.', 'powerfolio' ); ?></p>
			<code>[powerfolio]</code>
			<p><?php esc_html_e( 'You can customize it using these options:', 'powerfolio' ); ?></p>
				<ul>
					<li><strong><?php esc_html_e('postsperpage'); ?></strong>: <?php esc_html_e( 'Set a number of posts to show', 'powerfolio' ); ?> <i>(eg: postsperpage="12").</i></li>
					<li><strong><?php esc_html_e('type' ); ?></strong>: <?php esc_html_e( 'Set it to yes if you want to show a specific portfolio category. Options: ', 'powerfolio' ); ?>  <i>yes/no. (eg: type="yes")</i>.</li>
					<li><strong><?php esc_html_e('taxonomy'); ?></strong>: <?php esc_html_e( 'Set the specific taxonomy slug. You need to set type="yes" to use this feature.', 'powerfolio' ); ?>  <i>(eg: taxonomy="print")</i>.</li>
					<li><strong><?php esc_html_e('showfilter' ); ?></strong>: <?php esc_html_e( 'Show the category filter on the top of the grid. Options: ', 'powerfolio' ); ?>  <i> yes/no. (eg: showfilter="yes")</i>.</li>
					<li><strong><?php esc_html_e('style'); ?></strong>: <?php esc_html_e( 'Set the grid style of the portfolio. Options: ', 'powerfolio' ); ?>  <i> masonry/box. (eg: style="box")</i>.</li>
					<li><strong><?php esc_html_e('linkto'); ?></strong>: <?php esc_html_e( 'Set the link type of the portfolio item. If is set to image, it will open the Featured Image on a lightbox. Options: ', 'powerfolio' ); ?>  <i> image/project. (eg: linkto="image")</i>.</li>
					<li><strong><?php esc_html_e('columns'); ?></strong>: <?php esc_html_e( 'Set the columns per row of the portfolio grid.  Options: ', 'powerfolio' ); ?>  <i> 2/3/4. (eg: columns="4")</i>.</li>
					<li><strong><?php esc_html_e('margin'); ?></strong>: <?php esc_html_e( 'Choose if you want a margin between the items or no.  Options: ', 'powerfolio' ); ?>  <i> yes/no. (eg: margin="no")</i>.</li>
				</ul>
			<h3><?php esc_html_e( 'Example of a complete shortcode:', 'powerfolio' ); ?></h3>
			<code>[powerfolio postsperpage="12" type="no" showfilter="yes" style="masonry" linkto="image" columns="4" margin="no"]</code>		
			<h3><?php esc_html_e( 'Example of a complete shortcode without the set properties:', 'powerfolio' ); ?></h3>
			<code>[powerfolio postsperpage="" type="" taxonomy="" showfilter="" style="" linkto="" columns="" margin=""]</code>				
			<!-- /Shortcode -->
			
		</div>
		<div>
			
		</div>
	<?php
	}
}

$admin_pages = new ELPT_Admin_Page(); 