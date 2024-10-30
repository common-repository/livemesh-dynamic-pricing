<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

/**
 * Class LDP_Product_Pricing_Panel.
 *
 * LDP_Product_Pricing_Panel class handles the code for the Dynamic Pricing tab under Product Data settings.
 *
 */
class LDP_Product_Pricing_Panel {


    public function __construct() {

        // Add Pricing tab
        add_filter('woocommerce_product_data_tabs', array($this, 'add_product_pricing_tab'));

        // Output Pricing tab
        add_action('woocommerce_product_data_panels', array($this, 'product_pricing_panel'));

        // Save product meta
        add_action('woocommerce_admin_process_product_object', array($this, 'save_product_meta'));

        // Condition callbacks
        add_action('display_product_dynamic_pricing_bulk', array($this, 'display_product_dynamic_pricing_bulk'), 10, 2);
        add_action('display_product_dynamic_pricing_role', array($this, 'display_product_dynamic_pricing_role'), 10, 2);
        add_action('display_product_dynamic_pricing_custom', array($this, 'display_product_dynamic_pricing_custom'), 10, 2);

    }


    /**
     *
     * Add 'Pricing' to the Product Data meta box in Product edit page.
     *
     *
     * @param array $tabs List of current settings tabs.
     * @return array       List of edited settings tabs.
     */
    public function add_product_pricing_tab($tabs) {
        $tabs['ldp_pricing'] = array(
            'label' => __('Dynamic Pricing', 'livemesh-dynamic-pricing'),
            'target' => 'ldp_dynamic_pricing_data',
            'class' => array('show_if_simple show_if_variable'),
        );

        return $tabs;
    }


    /**
     *
     * Output settings to the Dynamic Pricing tab.
     *
     *
     */
    public function product_pricing_panel() {
        require_once plugin_dir_path(__FILE__) . 'views/html-product-data-pricing.php';
    }


    /**
     *
     * Save the dynamic pricing rules created in the Dynamic Pricing tab as product meta.
     *
     * @param WC_Product $product Product object being saved.
     */
    public function save_product_meta($product) {

        // Settings fields
        $dynamic_pricings = isset($_POST['dynamic_pricing']) ? wc_clean($_POST['dynamic_pricing']) : array();

        foreach ($dynamic_pricings as $k => $v) {
            if ($v['type'] == 'custom') {
                $dynamic_pricings[$k]['condition'] = lwc_sanitize_conditions($dynamic_pricings[$k]['condition']);
            }

            if (isset($v['bulk_rules']['9999'])) {
                unset($dynamic_pricings[$k]['bulk_rules']['9999']);
            }
        }

        $product->update_meta_data('_ldp_dynamic_pricing', $dynamic_pricings);
    }


    /**
     *
     * Callback for the bulk pricing condition. This displays the
     * bulk pricing settings.
     *
     * @param int $i Index of the new condition.
     * @param array $dynamic_price Array of dynamic pricing data.
     */
    public function display_product_dynamic_pricing_bulk($i = 0, $dynamic_price) {
        require plugin_dir_path(__FILE__) . 'views/pricing-methods/bulk-pricing.php';
    }


    /**
     *
     * Callback for the role pricing condition. This displays the
     * role based pricing settings.
     *
     *
     * @param int $i Index of the new condition.
     * @param array $dynamic_price Array of dynamic pricing data.
     */
    public function display_product_dynamic_pricing_role($i = 0, $dynamic_price) {
        require plugin_dir_path(__FILE__) . 'views/pricing-methods/role-based-pricing.php';
    }


    /**
     *
     * Callback for the custom pricing condition. This displays the
     * custom pricing settings.
     *
     * @param int $i Index of the new condition.
     * @param array $dynamic_price Array of dynamic pricing data.
     */
    public function display_product_dynamic_pricing_custom($i = 0, $dynamic_price) {
        require plugin_dir_path(__FILE__) . 'views/pricing-methods/custom-pricing.php';
    }


}
