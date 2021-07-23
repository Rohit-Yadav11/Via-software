<?php

if (!class_exists('ELPTFeedbackNotice')) {
    class ELPTFeedbackNotice {
        /**
         * The Constructor
         */
        public function __construct() {
            // register actions
         
            if(is_admin()){
                add_action( 'admin_notices',array($this,'elpt_admin_notice_for_reviews'));
                add_action( 'admin_print_scripts', array($this, 'elpt_load_script' ) );
                add_action( 'wp_ajax_elpt_dismiss_notice',array($this,'elpt_dismiss_review_notice' ) );
            }
        }

    /**
	 * Load script to dismiss notices.
	 *
	 * @return void
	 */
	public function elpt_load_script() {
        $alreadyRated =get_option( 'elpt-alreadyRated' )!=false?get_option( 'elpt-alreadyRated'):"no";

        // check user already rated 
        if( $alreadyRated=="yes") {
               return;
           }
        $elpt_review_css = ".cool-feedback-notice-wrapper.notice.notice-info.is-dismissible {
            padding: 5px;
            display: inline-block;
            width: 100%;
        }
        .cool-feedback-notice-wrapper .logo_container {
            width:80px;
            display:inline-block;
            margin-right: 10px;
            vertical-align: top;
        }
        .cool-feedback-notice-wrapper .logo_container img {
            width:100%;
            height:auto;
        }
        .cool-feedback-notice-wrapper .message_container {
            width: calc(100% - 120px);
            display: inline-block;
            margin: 0;
            vertical-align: top;
        }
        .cool-feedback-notice-wrapper ul li {
            float: left;
            margin: 0px 5px;
        }
        .clrfix{
            clear:both;
        }";

        _e("<style>".$elpt_review_css."</style>");

        add_action( 'admin_print_footer_scripts', function () { 
            ?>
            <script>
              
              jQuery(document).ready(function ($) {
            	$('.elpt_dismiss_notice').on('click', function (event) {
            		var $this = $(this);
            		var wrapper=$this.parents('.cool-feedback-notice-wrapper');
            		var ajaxURL=wrapper.data('ajax-url');
            		var ajaxCallback=wrapper.data('ajax-callback');
            		
            		$.post(ajaxURL, { 'action':ajaxCallback }, function( data ) {
            			wrapper.slideUp('fast');
            		  }, "json");

            	});
            });
               
            </script>
        <?php 
        } );

    }
    // ajax callback for review notice
    public function elpt_dismiss_review_notice(){
        $rs=update_option( 'elpt-alreadyRated','yes' );
        echo  json_encode( array("success"=>"true") );
        exit;
    }
   // admin notice  
    public function elpt_admin_notice_for_reviews(){

        if( !current_user_can( 'update_plugins' ) ){
            return;
        }
         // get installation dates and rated settings
        $installation_date = get_option( 'elpt-installDate' );
        $alreadyRated =get_option( 'elpt-alreadyRated' )!=false?get_option( 'elpt-alreadyRated'):"no";

        // check user already rated 
        if( $alreadyRated=="yes") {
            return;
        }

        // grab plugin installation date and compare it with current date
        $display_date = date( 'Y-m-d h:i:s' );
        $install_date= new DateTime( $installation_date );
        $current_date = new DateTime( $display_date );
        $difference = $install_date->diff($current_date);
        $diff_days= $difference->days;
        
        // check if installation days is greator then week
        if (isset($diff_days) && $diff_days>=3) {
            echo $this->elpt_create_notice_content();
        }
}  

    // generated review notice HTML
    function elpt_create_notice_content(){
        $ajax_url=admin_url( 'admin-ajax.php' );
        $elpt_URL = plugin_dir_url( __DIR__ );
        $ajax_callback='elpt_dismiss_notice';
        $wrap_cls="notice notice-info is-dismissible";
        $img_path= $elpt_URL.'img/logo.png';
        $p_name="PowerFolio: Portfolio & Image Gallery for Elementor";
        $like_it_text='Rate Now! ★★★★★';
        $already_rated_text=esc_html__( 'I already rated it', 'elemenfolio' );
        $not_interested=esc_html__( 'Not Interested', 'elemenfolio' );
        $not_like_it_text=esc_html__( 'No, not good enough, i do not like to rate it!', 'elemenfolio' );
        $p_link=esc_url('https://wordpress.org/support/plugin/portfolio-elementor/reviews/#new-post');
        $pro_url=esc_url('https://wordpress.org/support/plugin/portfolio-elementor/reviews/#new-post');
       
        $message="Thanks for using <b>$p_name</b> WordPress plugin. We hope it meets your expectations! <br/>Please give us a quick rating, it works as a boost for us to keep working on improving the plugin!<br/>";
      
        $html='<div data-ajax-url="%8$s"  data-ajax-callback="%9$s" class="cool-feedback-notice-wrapper %1$s">
        <div class="logo_container"><a href="%5$s"><img src="%2$s" alt="%3$s"></a></div>
        <div class="message_container">%4$s
        <div class="callto_action">
        <ul>
            <li class="love_it"><a href="%5$s" class="like_it_btn button button-primary" target="_new" title="%6$s">%6$s</a></li>
            <li class="already_rated"><a href="javascript:void(0);" class="already_rated_btn button elpt_dismiss_notice" title="%7$s">%7$s</a></li>
            <li class="already_rated"><a href="javascript:void(0);" class="already_rated_btn button elpt_dismiss_notice" title="%11$s">%11$s</a></li>
           
        </ul>
        <div class="clrfix"></div>
        </div>
        </div>
        </div>';

 return sprintf($html,
        $wrap_cls,
        $img_path,
        $p_name,
        $message,
        $p_link,
        $like_it_text,
        $already_rated_text,
        $ajax_url,// 8
        $ajax_callback,//9
        $pro_url,//10
        $not_interested
        );
        
       }

    } //class end

} 



