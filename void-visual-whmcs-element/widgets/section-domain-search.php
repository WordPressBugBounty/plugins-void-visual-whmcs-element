<?php
/**
 * Domain Search Widgets - Free + Pro (Override System)
 *
 * - Free plugin registers base widget (shortcode: void_wbwhmcse_laouts_search) and base VC params.
 * - Free plugin will NOT register when PRO is active (it respects a filter).
 * - PRO plugin sets the filter and registers its extended VC params & rendering using the SAME shortcode/base.
 * - PRO class extends the Free class and overrides only what's needed.
 *
 * Usage:
 * - In Free plugin main file include this class and instantiate: new Void_Wbwhmcse_Section_Domain_Search();
 * - In Pro plugin main file, BEFORE including/instantiating, call:
 *      add_filter('void_wbwhmcse_pro_active', '__return_true');
 *   then include this file and instantiate: new Void_Wbwhmcse_Pro_Section_Domain_Search();
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Free Class: Base Domain Search Widget
 */
class Void_Wbwhmcse_Section_Domain_Search {

    /**
     * Base shortcode alias (shared between free and pro)
     */
    const SHORTCODE_BASE = 'void_wbwhmcse_laouts_search';

    public function __construct() {
        // If PRO has declared itself active, do not register the free widget.
        if ( apply_filters('void_wbwhmcse_pro_active', false) ) {
            return;
        }

        add_action('init', array($this, 'register_search_controls'));
        add_shortcode(self::SHORTCODE_BASE, array($this, 'render'));
    }

    /**
     * Register VC map for the free widget
     */
    public function register_search_controls() {
		
        if (! function_exists('vc_map')) {
            return;
        }

        vc_map(array(
            'name' => __('Domain Search', 'void_wbwhmcse'),
            'base' => self::SHORTCODE_BASE,
            'category' => esc_html__('INNOVS ELEMENTS', 'void_wbwhmcse'),
            'content_element' => true,
            'icon' => 'icon-wpb-vc_icon',
            'params' => array(
                array(
                    'type' => 'textfield',
                    'heading' => __('Title', 'void_wbwhmcse'),
                    'param_name' => 'input_title',
                    'value' => 'Domain Search',
                    'group' => 'Content',
                ),
                array(
                    'type' => 'textfield',
                    'heading' => __('Input Placeholder', 'void_wbwhmcse'),
                    'param_name' => 'input_placeholder',
                    'value' => 'Enter your domain name here',
                    'group' => 'Content',
                ),
                array(
                    'type' => 'textfield',
                    'heading' => __('Whmcs Url', 'void_wbwhmcse'),
                    'param_name' => 'whmcs_url',
                    'value' => 'https://voidcoders.com/voidwhmcs',
                    'description' => __('Used when you do not have WHMCS Bridge Plugin installed to get/send data. Do not add (/). Just input direct url of your whmcs area (not admin url). ex: https://testsite/whmcs', 'void_wbwhmcse'),
                    'group' => 'Content',
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __('Show Button', 'void_wbwhmcse'),
                    'param_name' => 'input_show_button',
                    'value' => array('Yes' => 'yes', 'No' => 'no'),
                    'save_always' => true,
                    'group' => 'Button',
                ),
                array(
                    'type' => 'textfield',
                    'heading' => __('Button Text', 'void_wbwhmcse'),
                    'param_name' => 'button_text',
                    'description' => 'Default label is Search Domain',
                    'dependency' => array('element' => 'input_show_button', 'value' => 'yes'),
                    'group' => 'Button',
                ),
                array(
                    'type' => 'colorpicker',
                    'heading' => __('Button Color', 'void_wbwhmcse'),
                    'param_name' => 'button_color',
                    'value' => '#1e73be',
                    'group' => 'Button',
                ),
                array(
                    'type' => 'colorpicker',
                    'heading' => __('Button Text Color', 'void_wbwhmcse'),
                    'param_name' => 'button_text_color',
                    'value' => '#fff',
                    'group' => 'Button',
                ),
                array(
                    'type' => 'textfield',
                    'heading' => __('Button Text Size', 'void_wbwhmcse'),
                    'param_name' => 'button_text_size',
                    'value' => '14',
                    'group' => 'Button',
                ),
                array(
                    'type' => 'textfield',
                    'heading' => __('Search Input Border', 'void_wbwhmcse'),
                    'param_name' => 'search_input_border',
                    'value' => '1px solid gray',
                    'group' => 'Button',
                ),
                array(
                    'type' => 'html',
                    'heading' => __('<h3 style="padding: 10px;background: #2b4b80;color: #fff;">Limited Offer : 60% Discount on Release</h3>\n<a target="_blank" href="https://voidcoders.com/wpbakery-visual-composer-whmcs-elements-pro-subscription/" >Subscribe to get pro version when released</a>', 'void_wbwhmcse'),
                    'param_name' => 'pro_feature',
                    'group' => 'Pro Features',
                ),
            ),
        ));
    }

    /**
     * Render the free form (non-AJAX)
     */
    public function render($atts, $content = null) {
        global $whmcs_bridge_enabled;

        $atts = shortcode_atts(array(
            'direct_search' => 'no',
            'input_title' => 'Domain Search',
            'input_placeholder' => 'Enter your domain name here',
            'input_show_button' => 'yes',
            'button_text' => 'Search Domain',
            'search_input_border' => '1px solid gray',
            'button_color' => '#1e73be',
            'button_text_color' => '#fff',
            'button_text_size' => '14',
            'whmcs_url' => 'https://voidcoders.com/voidwhmcs',
            'active' => 'yes',
            'active_text' => '',
        ), $atts);

        // Sanitize
        $input_placeholder   = esc_attr($atts['input_placeholder']);
        $search_input_border = esc_attr($atts['search_input_border']);
        $button_color        = esc_attr($atts['button_color']);
        $button_text_color   = esc_attr($atts['button_text_color']);
        $button_text_size    = intval($atts['button_text_size']);
        $button_text         = esc_attr($atts['button_text']);
        $whmcs_url           = esc_url($atts['whmcs_url']);
        $input_show_button   = esc_attr($atts['input_show_button']);

        $form_action = ($whmcs_bridge_enabled == 1)
            ? esc_url(void_wbwhmcse_whmcs_bridge_url() . '?ccce=domainchecker')
            : esc_url($whmcs_url . '/domainchecker.php');

        ob_start();
        ?>
        <div class="sda-form-area">
			<div class="sda-form-input-box">
				<form method="post" action="<?php echo $form_action; ?>">
					
					<input
						style="border: <?php echo $search_input_border; ?>"
						type="text"
						name="domain"
						required
						autocomplete="off"
						placeholder="<?php echo $input_placeholder; ?>"
					>

					<?php if ($input_show_button === 'yes') : ?>
						<input
							type="submit"
							value="<?php echo $button_text; ?>"
							style="background-color: <?php echo $button_color; ?>; color: <?php echo $button_text_color; ?>; font-size: <?php echo $button_text_size; ?>px;"
						>
					<?php endif; ?>
					
				</form>
			</div>

        </div>
        <?php

        return ob_get_clean();
    }
}


