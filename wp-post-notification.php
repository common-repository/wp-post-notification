<?php
/*
Plugin Name: WP Flash News Notification
Description: Show blog post in a smart way
Author: WP-EXPERTS.IN Team
Author URI: https://www.wp-experts.in/
Version: 1.1
* License GPL2
Copyright 2023  WP-Experts.IN

This program is free software; you can redistribute it andor modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if( !class_exists( 'WpPostNotification' ) ) {
    class WpPostNotification {
        /**
         * Construct the plugin object
         */
        public function __construct() {
			// allow shortcode for text widget 
			add_filter('widget_text','do_shortcode');
            // register actions
			add_action('admin_init', array(&$this, 'wpn_admin_init'));
			add_action('admin_menu', array(&$this, 'wpn_add_menu'));
			add_action( 'admin_bar_menu', array(&$this,'toolbar_link_to_wppn'), 999 );
			add_action('init', array(&$this, 'init_wp_post_notification'));
			add_shortcode('wpn_posts',array(&$this,'wp_post_notification_shortcode'));
			$wpn_flash_enable = get_option('wpn_flash_enable');
			if($wpn_flash_enable)
			add_action('wp_footer', array(&$this, 'wp_post_notification_flash_func'));
			/** register_activation_hook */
			register_activation_hook( __FILE__, array(&$this, 'init_wpn_pro_activate' ) );
			/** register_deactivation_hook */
			register_deactivation_hook( __FILE__, array(&$this, 'init_wpn_pro_deactivate' ) );
						
			add_filter( "plugin_action_links_".plugin_basename( __FILE__ ), array(&$this,'wpn_pro_settings_link' ));
			
			add_action( 'wp_enqueue_scripts', array(&$this,'wpn_pro_scripts_method' ));
        } // END public function __construct
		
		/**
		 * hook to add link under adminmenu bar
		 */		
		public function toolbar_link_to_wppn( $wp_admin_bar ) {
			$args = array(
				'id'    => 'wpn_menu_bar',
				'title' => 'WP Post Notification',
				'href'  => admin_url('options-general.php?page=wpn'),
				'meta'  => array( 'class' => 'wpn-toolbar-page' )
			);
			$wp_admin_bar->add_node( $args );
			//second lavel
			$wp_admin_bar->add_node( array(
				'id'    => 'wpn-second-sub-item',
				'parent' => 'wpn_menu_bar',
				'title' => 'Settings',
				'href'  => admin_url('options-general.php?page=wpn'),
				'meta'  => array(
					'title' => __('Settings'),
					'target' => '_self',
					'class' => 'wpn_menu_item_class'
				),
			));
		}
		
		/**
		* remove wp version param from any enqueued scripts
		*/
		function init_wp_post_notification() {
			if(!is_admin()){
				$wpn_enable = get_option('wpn_enable');
				if($wpn_enable){
				//add_action('wp_footer',array(&$this,'wp_post_notification_func'));
				add_action( 'wp_enqueue_scripts',array(&$this, 'wpn_enqueue_styles' ));
			   }
			}
		}
		
		
		/**
		 * hook into WP's admin_init action hook
		 */
		public function wpn_admin_init() {
			// Set up the settings for this plugin
			$this->wpn_init_settings();
			// Possibly do additional admin_init tasks
		} // END public static function activate
		/**
		 * Initialize some custom settings
		 */     
		public function wpn_init_settings() {
			// register the settings for this plugin
			register_setting('wpn-group', 'wpn_enable');
			register_setting('wpn-group', 'wpn_display_date');
			register_setting('wpn-group', 'wpn_delay_time');
			/** addon fields */
			register_setting('wpn-group', 'wpn_flash_enable');
			register_setting('wpn-group', 'wpn_position');
			register_setting('wpn-group', 'wpn_hide_on_post_type');
			register_setting('wpn-group', 'wpn_number_of_order');
			register_setting('wpn-group', 'wpn_hide_on_home');
			register_setting('wpn-group', 'wpn_hide_on_tags');
			register_setting('wpn-group', 'wpn_hide_on_category');
			register_setting('wpn-group', 'wpn_hide_on_search');
			register_setting('wpn-group', 'wpn_hide_on_author');
			register_setting('wpn-group', 'wpn_hide_on_archive');
			register_setting('wpn-group', 'wpn_hide_on_post_type');
			register_setting('wpn-group', 'wpn_exclude_term_type');
		} // END public function init_custom_settings()
		/**
		 * add a menu
		 */     
		public function wpn_add_menu() {
			add_options_page('WP Post Notification Settings', 'WP Post Notification', 'manage_options', 'wpn', array(&$this, 'wpn_settings_page'));;
		} // END public function add_menu()

		/**
		 * Menu Callback
		 */     
		public function wpn_settings_page() {
			if(!current_user_can('manage_options'))
			{
				wp_die(__('You do not have sufficient permissions to access this page.'));
			}

			// Render the settings template
			include(sprintf("%s/lib/settings.php", dirname(__FILE__)));
			//include(sprintf("%s/css/admin.css", dirname(__FILE__)));
			// Style Files
			wp_register_style( 'wpn_admin_style', plugins_url( 'css/wpn-admin.css',__FILE__ ) );
			wp_enqueue_style( 'wpn_admin_style' );
			// JS files
			wp_register_script('wpn_admin_script', plugins_url('/js/wpn-admin.js',__FILE__ ), array('jquery'));
            wp_enqueue_script('wpn_admin_script');
		} // END public function plugin_settings_page()
        /**
         * Activate the plugin
         */
        public static function init_wpn_pro_activate() {
			// deactiave free plugin
			deactivate_plugins(plugin_basename('wp-post-notification/wp-post-notification.php'), true );
            // Do nothing
        } // END public static function activate
    
        /**
         * Deactivate the plugin
         */     
        public static function init_wpn_pro_deactivate() {
            // Do nothing
        } // END public static function deactivate
       /**
        * Shortcode function  
        */ 
       public static function wp_post_notification_shortcode($attr) {
		   global $post;
		$wpn_delay_time = get_option('wpn_delay_time') ? get_option('wpn_delay_time') : '5000';
		$wpn_display_date = get_option('wpn_display_date') ? get_option('wpn_display_date') : '';
		$wpn_number_order = (int)get_option('wpn_number_of_order') ? get_option('wpn_number_of_order') : '5';

		
		$html= '<div id="wpsn-slideshow">';
		$filters = array(
		'post_status' => 'publish',
		'post_type' => 'post',
		'posts_per_page' => $wpn_number_order,
		'paged' => 1,
		'orderby' => 'ID,modified',
		'order' => 'DESC'
		);

		$postloop = new WP_Query($filters);

		if($postloop->have_posts()):
		
		while ($postloop->have_posts()) {
		$postloop->the_post();

			$html.=' <div class="wpsn-inner">';
            if(has_post_thumbnail(get_the_ID())){
            $html.='<div class="wpn-image">'.get_the_post_thumbnail(get_the_ID(), 'thumbnail').'</div>';
		   }
            $html.='<div class="wpn-content">
                              <span class="wpn-title"><a href="'.get_the_permalink(get_the_ID()).'">'.get_the_title().'</a></span>
                              <span class="wpn-buyer">
                                <span>Posted by </span><a href="'.get_author_posts_url( get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename' ) ).'" class="pink-text">'.get_the_author().'</a> on <span class="date">'.get_the_date().'</span>
                              </span>';
                              
                              if(get_option('wpn_display_date'))
                              {
                              $html.='<span class="wpn-time">
                                 <span style="font-size:80%;">'.human_time_diff(get_post_time('U',false,get_the_ID()), current_time('timestamp')) . " " . __('ago').'</span>
                              </span>';
						      }   
              $html.='</div><div class="clear"></div></div>';
	
		} // end while
		endif; //end parent if
      
              $html.='</div><script tyle="javascript/text">
				 jQuery("#wpsn-slideshow > div:gt(0)").hide();
					setInterval(function() { 
					  jQuery("#wpsn-slideshow > div:first")
						.hide()
						.next()
						.show()
						.end()
						.appendTo("#wpsn-slideshow");
						/*jQuery("#wpsn-slideshow > div:first")
						.hide(500)
						.next()
						.show(500)
						.end()
						.appendTo("#wpsn-slideshow");*/
					},  '.$wpn_delay_time.');
               </script>';
               
		return $html;
		}
		/**
		 *  Flash notification
		 * */
	   public static function wp_post_notification_flash_func($attr) {
		global $post;
		$wpn_number_order = (int)get_option('wpn_number_of_order') ? get_option('wpn_number_of_order') : '5';
		$wpn_delay_time  =   isset($attr['delay_time']) ? $attr['delay_time'] : (get_option('wpn_delay_time') ? get_option('wpn_delay_time') : '5000');
		$wpn_position = get_option('wpn_position');
		$wpn_display_date = get_option('wpn_display_date') ? get_option('wpn_display_date') : '';

		
		$wpn_show_on_post_type=get_option('wpn_show_on_post_type');
		
		if($wpn_show_on_post_type!='' && is_singular($wpn_show_on_post_type)){ return;}
		$html= '<div class="wpsalesnotifier-sec">';
		$filters = array(
		'post_status' => 'publish',
		'post_type' => 'post',
		'posts_per_page' => $wpn_number_order,
		'paged' => 1,
		'orderby' => 'ID,modified',
		'order' => 'DESC'
		);

		$postloop = new WP_Query($filters);
		if($postloop->have_posts()):
		while ($postloop->have_posts()) {
		    $postloop->the_post();
			$html.='<div class="wp-post-notification" id="wpsn-'.get_option('wpn_position').'">';
            $html.='<div class="wpn-image">'.get_the_post_thumbnail(get_the_ID(), 'thumbnail').'</div>';
            $html.='<div class="wpn-inner">
                              <span class="wpn-title"><a href="'.get_the_permalink(get_the_ID()).'">'.get_the_title().'</a></span>
                              <span class="wpn-buyer">
                                 <span>Posted by </span><a href="'.get_author_posts_url( get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename' ) ).'" class="pink-text">'.get_the_author().'</a> on <span class="date">'.get_the_date().'</span>
                              </span>';
                              
                              if(get_option('wpn_display_date'))
                              {
                              $html.='<span class="wpn-time">
                                 <span style="font-size:80%;">'.human_time_diff(get_post_time('U',false,get_the_ID()), current_time('timestamp')) . " " . __('ago').'</span>
                              </span>';
						      }   

              $html.='<a href="javascript:;" class="wpn_sales_close_btn"></a>';
              
              $html.='</div></div>';
				}//end while
				endif; //end parent if
      
              $html.='<script>
				  var wpn_interval=null;
                  var wpn_current_index=-1;
                  var wpn_sales_feeds=[];
                  var showtime=4300;
                  function wpn_hide_prev_feed_notify(index){
                  if(wpn_sales_feeds.eq(wpn_current_index).length > 0){
                  wpn_sales_feeds.eq(wpn_current_index).animate({bottom:"-100px"}, 500); // define hide speed
                  }}
                  function wpn_show_live_feed_notify(index){
                  wpn_sales_feeds.eq(index).animate({bottom:"10px"}, 1000);
                  wpn_current_index=index;
                  }
                  function wpn_show_next_live_notify(){
                  if((wpn_current_index + 1) >=wpn_sales_feeds.length){
                  wpn_current_index=-1;
                  }
                  if(window.console)
                  console.log("will show " +(wpn_current_index+1));
                  wpn_show_live_feed_notify(wpn_current_index + 1);
                  setTimeout(function(){ wpn_hide_prev_feed_notify(wpn_current_index + 1); }, showtime);
                  }
                  function stop_live_notify(){
                  removeInterval(inverval);
                  }
                  function readCookie(name){
                  var nameEQ=escape(name) + "=";
                  var ca=document.cookie.split(";");
                  for(var i=0; i < ca.length; i++){
                  var c=ca[i];
                  while(c.charAt(0)==="") c=c.substring(1, c.length);
                  if(c.indexOf(nameEQ)===0) return unescape(c.substring(nameEQ.length, c.length));
                  }
                  return null;
                  }
                  jQuery(function(){
                  jQuery(".wpn_sales_close_btn").click(function(){
                  var days=30;
                  var date=new Date();
                  date.setTime(date.getTime() +(days *24 *60 *60 *1000));
                  if(window.console)
                  console.log(date.toGMTString());
                  document.cookie="wc_feed_closed=true; expires=" + date.toGMTString() + ";";
                  jQuery(".wp-post-notification").css("display", "none");
                  clearInterval(wpn_interval);
                  return false;
                  });
                  wpn_sales_feeds=jQuery(".wp-post-notification");
                  wpn_show_next_live_notify();
                  wpn_interval=setInterval(wpn_show_next_live_notify,(showtime + '.$wpn_delay_time.')); // define delay time
                  });
               </script>';
               /** Check conditions */
              // Returns the content.
             global $post;
             $returnFlashContent = $html;
			/* front page */
			if(is_home() || is_front_page()):
			$wpn_hide_on_home = get_option('wpn_hide_on_home');
				if((is_home() && is_front_page()) && $wpn_hide_on_home):
					$returnFlashContent='';
				elseif(is_front_page() && $wpn_hide_on_home):
					$returnFlashContent='';
				elseif(is_home() && $wpn_hide_on_home):
				   $returnFlashContent='';
			   else:
			   $returnFlashContent=$html;
			  endif;
			endif;
			/* Tags */
			$wpn_hide_on_tags = get_option('wpn_hide_on_tags');
			if(is_tag() && $wpn_hide_on_tags):
			  $returnFlashContent='';
			  endif;
			/* Category */
			$wpn_hide_on_category = get_option('wpn_hide_on_category');
			if(is_category() && $wpn_hide_on_category):
			  $returnFlashContent='';
			  endif;
			  /* Search */
			  $wpn_hide_on_search = get_option('wpn_hide_on_search');
			if(is_search() && $wpn_hide_on_search):
			  $returnFlashContent='';
			  endif;
			/* Author */
			$wpn_hide_on_author = get_option('wpn_hide_on_author');
			if(is_author() && $wpn_hide_on_author ):
			  $returnFlashContent='';
			  endif; 
			/** custom term */
			$termTyes=get_option('wpn_exclude_term_type');
			if(is_tax() && $termTyes!='')
			{
			 $currentTermType = get_query_var( 'taxonomy' );
				if(is_tax($currentTermType) && in_array($currentTermType,$termTyes)):
				  $returnFlashContent='';
				  endif;
			}
			/* All Archive */
			$wpn_hide_on_archive = get_option('wpn_hide_on_archive');
			if(is_archive() && $wpn_hide_on_archive):
			  $returnFlashContent='';
			  endif;
			/** start post details page*/
			if(is_singular() &&  !is_front_page()):
			  /** exclude post types **/
			    $postTyes=get_option('wpn_hide_on_post_type');
			    $postType = get_queried_object();
			    $currentPostType = $postType->post_type;
				if(is_singular($currentPostType) && ''!=$postTyes && in_array($currentPostType,$postTyes)):
				   $returnFlashContent='';
				  endif;
			endif;// end post detials page
			/* 404 page */
			if(is_404() || is_attachment() ):
			 $returnFlashContent='';
			endif;
               
		echo $returnFlashContent;
		}
	/*-------------------------------------------------
	Start Social Share Buttons Style
	------------------------------------------------- */
	function wpn_enqueue_styles() {
	global $wp_styles;
	wp_register_style( 'wpn_style', plugins_url( 'css/wpn-style.css',__FILE__ ) );
	wp_enqueue_style( 'wpn_style' );  	
	}
	/*-------------------------------------------------
	End Social Share Buttons Style
	------------------------------------------------- */
	// Add the settings link to the plugins page
		function wpn_pro_settings_link($links) { 
			$settings_link = '<a href="admin.php?page=wpn">Settings</a>'; 
			array_unshift($links, $settings_link); 
			return $links; 
		}	
   function wpn_pro_scripts_method() {
	wp_enqueue_script( 'jquery' );
	}
    } // END class WpPostNotification
} // END if(!class_exists('WpPostNotification'))

if(class_exists('WpPostNotification')) {
    // instantiate the plugin class
    $wpn_plugin_template = new WpPostNotification;
	
}
