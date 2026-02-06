<?php

    class Void_Visual_WHMCS_Notice{

        public function __construct() {
            add_action( 'admin_notices', [$this, 'my_plugin_admin_notice'] );
            add_action( 'wp_ajax_void_dismiss_notice', [$this, 'handle_dismiss_notice'] );
        }
    
        public function my_plugin_admin_notice() {
            // Check if the notice should be displayed
            if ( $this->should_display_notice() ) {
                ?>
                <div class="notice notice-success is-dismissible void-wb-whmcs-pro-notice">
                    <h4>
                        <?php 
                        echo wp_kses(
                            __(
                                'Grab the Pro Version of WPBakery WHMCS Now! Best Cyber Monday deals are live. <a href="https://theinnovs.com/wpb-whmcs-elements-pro" target="_blank">Act now!</a>',
                                'void_wbwhmcse'
                            ),
                            array(
                                'a' => array(
                                    'href' => array(),
                                    'target' => array()
                                )
                            )
                        );
                        ?>
                    </h4>
                </div>
                <script type="text/javascript">
                    jQuery(document).ready(function($) {
                        $(document).on('click', '.void-wb-whmcs-pro-notice .notice-dismiss', function() {
                            // AJAX request to dismiss the notice
                            $.post(ajaxurl, {
                                action: 'void_dismiss_notice'
                            });
                        });
                    });
                </script>
                <?php
            }
        }
    
        public function handle_dismiss_notice() {
            // Store the current time when the notice is dismissed
            update_option( 'void_wb_pro_notice', time() );
            wp_die(); // Required to end AJAX calls properly
        }
    
        private function should_display_notice() {
            $dismissed_time = get_option( 'void_wb_pro_notice' );
            // Check if the notice has been dismissed and if 7 days have passed
            if ( $dismissed_time ) {
                $seven_days_in_seconds = 7 * DAY_IN_SECONDS;
                return ( ( time() - $dismissed_time ) > $seven_days_in_seconds );
            }
            return true; // Show the notice if it has never been dismissed
        }

    }
