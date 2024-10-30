<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 *
 * Handle functions for admin settings page.
 *
 */
class LDP_Pricing_Tab {

    public function __construct() {

        // Add WC settings tab
        add_filter('woocommerce_settings_tabs_array', array($this, 'add_pricing_tab'), 60);

        // Settings page contents
        add_action('woocommerce_settings_tabs_ldp_pricing', array($this, 'output_pricing_tab'));

        // Table field type
        add_action('woocommerce_admin_field_dynamic_pricing_table', array($this, 'output_dynamic_pricing_table'));

        // Save settings page
        add_action('woocommerce_update_options_ldp_pricing', array($this, 'save_pricing_options'));
    }


    /**
     *
     * Add the Dynamic Pricing tab to the WooCommerce Settings page.
     *
     * @param array $tabs Default tabs used in WC.
     * @return array       All WC settings tabs including newly added.
     */
    public function add_pricing_tab($tabs) {
        $tabs['ldp_pricing'] = __('Dynamic Pricing', 'livemesh-dynamic-pricing');

        return $tabs;
    }


    /**
     *
     * Get the fields for Dynamic Pricing tab of Settings page.
     *
     *
     */
    public function get_pricing_settings() {

        $settings = apply_filters('livemesh_dynamic_pricing_settings', array(

            array(
                'title' => __('Dynamic Pricing', 'livemesh-dynamic-pricing'),
                'type' => 'title',
                'desc' => '<a href="https://livemeshwp.com/dynamic-pricing-woocommerce/docs/" target="_blank">Open the documentation</a>',
                'id' => 'ldp_general',
            ),

            array(
                'title' => __('Enable Dynamic Pricing', 'livemesh-dynamic-pricing'),
                'desc' => __('When disabled you will still be able to add/modify price rules, but none will be used for customers.', 'livemesh-dynamic-pricing'),
                'id' => 'enable_livemesh_dynamic_pricing',
                'default' => 'yes',
                'type' => 'checkbox',
                'autoload' => false
            ),

            array(
                'title' => __('Dynamic Pricing', 'livemesh-dynamic-pricing'),
                'type' => 'dynamic_pricing_table',
            ),

            array(
                'type' => 'sectionend',
                'id' => 'ldp_end'
            ),

        ));

        return $settings;
    }


    /**
     *
     * Load and render table as a field type.
     *
     * @return string
     */
    public function output_dynamic_pricing_table() {
        require_once plugin_dir_path(__FILE__) . 'views/html-dynamic-pricing-table.php';
    }


    /**
     *
     * Output Dynamic Pricing tab of WooCommerce Settings page using WooCommerce output_fields method
     *
     *
     */
    public function output_pricing_tab() {
        WC_Admin_Settings::output_fields($this->get_pricing_settings());
    }


    /**
     *
     * Save settings using the WooCommerce save_fields() method.
     *
     */
    public function save_pricing_options() {
        WC_Admin_Settings::save_fields($this->get_pricing_settings());
    }
}
