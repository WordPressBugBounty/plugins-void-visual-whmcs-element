<?php
/**
 * Plugin Name: WPBakery Visual Composer WHMCS Elements
 * Description: Adds Verious Widgets such as Live Domain Searcher, Pricing Table, Knowledge Base in Elementor for being used with your WHMCS or WHMCS Bridge Plugin for Hosting Website.
 * Version:     1.0.4.3
 * Author:      TheInnovs
 * Author URI:  https://theinnovs.com
 * Plugin URI:  https://theinnovs.com/wpb-whmcs-elements-pro/
 * Text Domain: void_wbwhmcse
 */
/* This loads the plugin.php file which is the main one */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'VOID_WBWHMCSE_ELEMENTS_FILE_', __FILE__ );
define( 'WBWHMCSE_NAME', 'void_wbwhmcse' );
define( 'WBWHMCSE_VERSION', '1.0.4.3' );
define( 'VOID_WBWHMCSE_PLUGIN_NAME', 'Innovs WPBakery WHMCS Elements' );

/**
 *
 * Load the plugin after Elementor (and other plugins) are loaded.
 *
 * @since 1.0.0
 */
function void_wbwhmcse_load_elements() {
  // Load localization file
  load_plugin_textdomain( 'void_wbwhmcse' ); 

  void_wbwhmcse_widget_register();
  

  // Notice if the Elementor is not active
  if ( ! did_action( 'js_composer/loaded' ) ) {
    add_action( 'admin_notices', 'void_wbwhmcse_fail_load' );
    return;
  }

    
  // Require the main plugin file
   require( __DIR__ . '/helper/helper.php' ); // load helper functions
   
}
add_action( 'plugins_loaded', 'void_wbwhmcse_load_elements' );   //notiung but checking and notice


require_once plugin_dir_path( __FILE__ ) . '/includes/class-void-visual-whmcs-admin.php';
if( class_exists('Void_Visual_WHMCS_Admin')){
    
    $void_visual_whmcs_admin = new Void_Visual_WHMCS_Admin();
}

require_once plugin_dir_path( __FILE__ ) . '/includes/class-void-visual-whmcs-notice.php';
if( class_exists('Void_Visual_WHMCS_Notice')){
    
    $Void_Visual_WHMCS_Notice = new Void_Visual_WHMCS_Notice();
}
 

function void_wbwhmcse_widget_register(){

  // echo __DIR__ . '/templates/domainSearch.php';
  // require( __DIR__ . '/widgets/section-domain-search.php' ); 

  void_wbwhmcse_includes();

   // Only load Free Domain Search if PRO is NOT active
  if (!apply_filters('void_wbwhmcse_pro_active', false)) {
      if (class_exists('Void_Wbwhmcse_Section_Domain_Search')) {
          new Void_Wbwhmcse_Section_Domain_Search();
      }
  }

   // Only load Free if PRO is NOT active
  if (!apply_filters('void_wbwhmcse_pro_active', false)) {
      if (class_exists('Void_Wbwhmcse_Section_Pricing')) {
          new Void_Wbwhmcse_Section_Pricing();
      }
  }

   if (class_exists('Void_Wbwhmcse_Section_knowledgebase')) {
   $obj_inits = new Void_Wbwhmcse_Section_knowledgebase;
  }
}

function void_wbwhmcse_includes() {
   $void_widgets= array_map('basename', glob(dirname( __FILE__ ) . '/widgets/*.php'));
  foreach($void_widgets as $key => $value){
        require ( __DIR__ . '/widgets/'.$value );
  }
}
 
function void_wbwhmcse_fail_load_out_of_date() {  // if plungin is outdated
  if ( ! current_user_can( 'update_plugins' ) ) {
    return;
  }
  $message = '<p>' . sprintf(__('<strong>%s</strong> Needs <a href="%s">WPBakery Page Builder</a> version higher than the one you have! Please update your <strong>WPBakery Page Builder</strong> plugin.','void_wbwhmcse'), 'https://wpbakery.com',VOID_WBWHMCSE_PLUGIN_NAME ) . '</p>';

  echo '<div class="error">' . $message . '</div>';
}

function void_wbwhmcse_fail_load() {  // if plungin is not isntalled
 
  if ( is_plugin_active( 'js_composer/js_composer.php' ) ) {
    return;
  }
  $message = '<p>' . sprintf(__('<a href="%s">WPBakery Page Builder</a> must be installed for <strong>%s</strong> plugin to work! Please Install it first','void_wbwhmcse'), 'https://wpbakery.com',VOID_WBWHMCSE_PLUGIN_NAME ) . '</p>';
 
  echo '<div class="error">' . $message . '</div>';
}

// add css frontend
 function void_wbwhmcse_add_scripts(){
  wp_register_script( 'domain-search', plugins_url( '/assets/js/domain-search.js', __FILE__), [ 'jquery' ], true, true );
    
  $our_script_array = array(	'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'button_available' => esc_html__( 'Buy Now' ),
                'button_unavailable' => esc_html__( 'Not Available' ),
                'button_info' => esc_html__( 'Search Again' ),
                'domain_available' => esc_html__( 'Congratulations! The domain is available.' ),
                'domain_unavailable' => esc_html__( 'Sorry! The Domain is already taken! Search Another one.'));
   wp_localize_script( 'domain-search', 'domainjs_texts', $our_script_array ); 
   wp_enqueue_style( 'void-whmcse', plugins_url( '/assets/css/style.css' , __FILE__ )); 

}
add_action( 'init', 'void_wbwhmcse_add_scripts' );


//add admin css
function void_wbwhmcse_admin_css(){
    global $pagenow;
    if( $pagenow == 'index.php' || ( !empty( $_GET['page'] ) && $_GET['page']=='void_whcms_pro')){
        wp_enqueue_style( 'void-cf7-admin', plugins_url( 'assets/css/void-cf7-admin.css', __FILE__ ) );
    }
}
add_action( 'admin_enqueue_scripts', 'void_wbwhmcse_admin_css' );


// add plugin activation time

function void_wbwhmcse_activation_time(){
    $get_installation_time = strtotime("now");
    add_option('void_wbwhmcse_elementor_activation_time', $get_installation_time ); 
}
register_activation_hook( __FILE__, 'void_wbwhmcse_activation_time' );

//check if review notice should be shown or not

function void_wbwhmcse_check_installation_time() {

    $spare_me = get_option('void_wbwhmcse_spare_me');
    if( !$spare_me ){
        $install_date = get_option( 'void_wbwhmcse_elementor_activation_time' );
        $past_date = strtotime( '-7 days' );
     
        if ( $past_date >= $install_date ) {
     
            add_action( 'admin_notices', 'void_wbwhmcse_display_admin_notice' );
     
        }
    }
}
add_action( 'admin_init', 'void_wbwhmcse_check_installation_time' );
 
/**
* Display Admin Notice, asking for a review
**/
function void_wbwhmcse_display_admin_notice() {
    // wordpress global variable 
    global $pagenow;
    if( $pagenow == 'index.php' ){
 
        $dont_disturb = esc_url( get_admin_url() . '?spare_me_ewhmcse=1' );
        $plugin_info = get_plugin_data( __FILE__ , true, true );       
        $reviewurl = esc_url( 'https://wordpress.org/support/plugin/void-visual-whmcs-element/reviews/#new-post' );
        $void_url = esc_url( 'https://voidcoders.com' );
     
        printf(__('<div class="void-cf7-review wrap">You have been using <b> %s </b> for a while. We hope you liked it ! Please give us a quick rating, it works as a boost for us to keep working on the plugin ! Also you can visit our <a href="%s" target="_blank">site</a> to get more themes & Plugins<div class="void-cf7-review-btn"><a href="%s" class="button button-primary" target=
            "_blank">Rate Now!</a><a href="%s" class="void-cf7-review-done"> Already Done !</a></div></div>', $plugin_info['TextDomain']), $plugin_info['Name'], $void_url, $reviewurl, $dont_disturb );
    }
}
// remove the notice for the user if review already done or if the user does not want to
function void_wbwhmcse_spare_me(){    
    if( isset( $_GET['spare_me_ewhmcse'] ) && !empty( $_GET['spare_me_ewhmcse'] ) ){
        $spare_me = $_GET['spare_me_ewhmcse'];

        if( $spare_me == 1 ){
            add_option( 'void_wbwhmcse_spare_me' , TRUE );
            update_option( 'void_wbwhmcse_spare_me' , TRUE );
        }
    }
}
add_action( 'admin_init', 'void_wbwhmcse_spare_me', 5 );