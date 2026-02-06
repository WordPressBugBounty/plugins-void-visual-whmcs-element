<?php

class Void_Visual_WHMCS_Admin{

    public $plugin_name;
    public $version;

    public function __construct(){

        $this->plugin_name = WBWHMCSE_NAME;
        $this->version = WBWHMCSE_VERSION;

        add_action( 'admin_menu', [ $this, 'admin_menu_create' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ] );
    }

    public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . '../admin/assets/css/admin.css', array(), $this->version, 'all' );

	}

    public function admin_menu_create(){

        add_menu_page(
            __( 'VOID WB WHMCS', 'void_wbwhmcse' ), // Page title
            'WPB WHMCS',                   // Menu title
            'manage_options',                      // Capability
            'void_wb_whmcs_page',                  // Menu slug
            [ $this, 'void_wb_whmcse_cb' ],       // Callback function
            '',                                    // Icon URL (empty string for no icon)
            11                                     // Position
        );

        if( ! function_exists('void_wbwhmcse_pro_load_elements')){
            add_submenu_page( 
                'void_wb_whmcs_page', 
                'Go Pro', 
                '<span class="dashicons dashicons-star-filled" style="color: #4cb696; font-size: 17px"></span> Go Pro', 
                'manage_options', 
                'void_wpwhcms_go_pro', 
                [ $this, 'void_wp_whcms_go_pro_cb' ]
            );
        }
        
    }

    // Callback for the main menu page
    public function void_wb_whmcse_cb() {
        require_once plugin_dir_path( __FILE__ ) . '../admin/partials/whmcs.php';
    }

    public function void_wp_whcms_go_pro_cb() {
        require_once plugin_dir_path( __FILE__ ) . '../admin/partials/go-pro.php';
    }
}

