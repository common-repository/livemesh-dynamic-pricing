<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// Exit if accessed directly
class LDP_Pricing_Helper
{
    /**
     * Constructor.
     *
     */
    public function __construct()
    {
        add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'add_price_filters' ) );
        add_filter(
            'ldp_apply_dynamic_pricing_bulk',
            array( $this, 'apply_dynamic_pricing_bulk' ),
            10,
            3
        );
        add_filter(
            'ldp_apply_dynamic_pricing_role',
            array( $this, 'apply_dynamic_pricing_role' ),
            10,
            3
        );
        add_filter(
            'ldp_apply_dynamic_pricing_custom',
            array( $this, 'apply_dynamic_pricing_custom' ),
            10,
            3
        );
        add_filter(
            'ldp_apply_dynamic_pricing_adjustment',
            array( $this, 'apply_dynamic_pricing_adjustment' ),
            10,
            3
        );
    }
    
    /**
     *
     * Add the WP filters required for modifying regular and sale prices of WooCommerce products
     *
     */
    function add_price_filters()
    {
        // Nothing to do if Dynamic Pricing is disabled
        if ( 'no' == get_option( 'enable_livemesh_dynamic_pricing', 'yes' ) ) {
            return;
        }
        // Only show on front-end
        if ( is_admin() && (!defined( 'DOING_AJAX' ) || defined( 'DOING_AJAX' ) && DOING_AJAX == false) ) {
            return;
        }
        // Simple product
        add_filter(
            'woocommerce_product_get_price',
            array( $this, 'get_modified_product_price' ),
            5,
            2
        );
        add_filter(
            'woocommerce_product_get_regular_price',
            array( $this, 'get_modified_regular_price' ),
            5,
            2
        );
        add_filter(
            'woocommerce_product_get_sale_price',
            array( $this, 'get_modified_sale_price' ),
            5,
            2
        );
        // Variable product
        add_filter(
            'woocommerce_product_variation_get_price',
            array( $this, 'get_modified_product_price' ),
            5,
            2
        );
        add_filter(
            'woocommerce_product_variation_get_regular_price',
            array( $this, 'get_modified_regular_price' ),
            5,
            2
        );
        add_filter(
            'woocommerce_product_variation_get_sale_price',
            array( $this, 'get_modified_sale_price' ),
            5,
            2
        );
        // Variable product
        add_filter(
            'woocommerce_variation_prices_price',
            array( $this, 'get_modified_product_price' ),
            5,
            2
        );
        add_filter(
            'woocommerce_variation_regular_price',
            array( $this, 'get_modified_regular_price' ),
            5,
            2
        );
        add_filter(
            'woocommerce_variation_prices_sale_price',
            array( $this, 'get_modified_sale_price' ),
            5,
            2
        );
    }
    
    /**
     *
     * Removes all WP filters to prevent endless loop.
     *
     */
    function remove_price_filters()
    {
        // Bail if Dynamic Pricing is disabled
        if ( 'no' == get_option( 'enable_livemesh_dynamic_pricing', 'yes' ) ) {
            return;
        }
        // Only show on front-end
        if ( is_admin() && (!defined( 'DOING_AJAX' ) || defined( 'DOING_AJAX' ) && DOING_AJAX == false) ) {
            return;
        }
        // Simple product
        remove_filter( 'woocommerce_product_get_price', array( $this, 'get_modified_product_price' ), 5 );
        remove_filter( 'woocommerce_product_get_regular_price', array( $this, 'get_modified_regular_price' ), 5 );
        remove_filter( 'woocommerce_product_get_sale_price', array( $this, 'get_modified_sale_price' ), 5 );
        // Variable product
        remove_filter( 'woocommerce_product_variation_get_price', array( $this, 'get_modified_product_price' ), 5 );
        remove_filter( 'woocommerce_product_variation_get_regular_price', array( $this, 'get_modified_regular_price' ), 5 );
        remove_filter( 'woocommerce_product_variation_get_sale_price', array( $this, 'get_modified_sale_price' ), 5 );
        // Variable product
        remove_filter( 'woocommerce_variation_prices_price', array( $this, 'get_modified_product_price' ), 5 );
        remove_filter( 'woocommerce_variation_prices_regular_price', array( $this, 'get_modified_regular_price' ), 5 );
        remove_filter( 'woocommerce_variation_prices_sale_price', array( $this, 'get_modified_sale_price' ), 5 );
    }
    
    /**
     *
     * Filter and modify the regular price of the product.
     *
     *
     * @param $price
     * @param WC_Product $product
     * @return float
     */
    function get_modified_regular_price( $price, $product )
    {
        // Skip 'variable' products ('variation' is the one we want)
        if ( $product->get_type() == 'variable' ) {
            return $price;
        }
        $this->remove_price_filters();
        $ldp_pricing = new LDP_Pricing();
        $price = wc_format_decimal( $ldp_pricing->apply_dynamic_pricing_rules( $price, $product, 'regular_price' ), wc_get_price_decimals() );
        $this->add_price_filters();
        return $price;
    }
    
    /**
     * Filter and modify the sale price of the product.
     *
     * @param $price
     * @param WC_Product $product
     * @return float
     */
    function get_modified_sale_price( $price, $product )
    {
        // Skip 'variable' products ('variation' is the one we want)
        if ( $product->get_type() == 'variable' ) {
            return $price;
        }
        $this->remove_price_filters();
        $regular_price = $product->get_regular_price();
        $sale_price = $product->get_sale_price();
        $this->add_price_filters();
        $ldp_pricing = new LDP_Pricing();
        $modified_regular_price = wc_format_decimal( $ldp_pricing->apply_dynamic_pricing_rules( $regular_price, $product, 'regular_price' ), wc_get_price_decimals() );
        $modified_sale_price = wc_format_decimal( $ldp_pricing->apply_dynamic_pricing_rules( $modified_regular_price, $product, 'sale_price' ), wc_get_price_decimals() );
        // Retain the original sale price set by the user if it is lower than the adjusted sale price
        $price = ( $sale_price < $modified_sale_price && !empty($sale_price) ? $sale_price : $modified_sale_price );
        if ( $sale_price == '' && $price == $modified_regular_price ) {
            $price = '';
        }
        return $price;
    }
    
    /**
     *
     * Apply filter to modify the sale price of the product.
     *
     * @param float $price
     * @param WC_Product $product
     * @return mixed
     */
    function get_modified_product_price( $price, $product )
    {
        // Skip 'variable' products ('variation' is the one we want)
        if ( $product->get_type() == 'variable' ) {
            //		return $price;
        }
        $this->remove_price_filters();
        $regular_price = $product->get_regular_price();
        $sale_price = $product->get_sale_price();
        $this->add_price_filters();
        $modified_regular_price = $this->get_modified_regular_price( $regular_price, $product );
        $modified_sale_price = $this->get_modified_sale_price( $sale_price, $product );
        if ( $regular_price !== '' ) {
            $price = $modified_regular_price;
        }
        if ( $sale_price !== '' && $modified_sale_price <= $sale_price && $modified_sale_price < $price ) {
            $price = $modified_sale_price;
        }
        if ( $modified_sale_price !== '' && $modified_sale_price < $price ) {
            $price = $modified_sale_price;
        }
        return $price;
    }
    
    /*******************************************************
     *  Price adjustments
     ********************************************************/
    /**
     *
     * Apply the bulk pricing adjustment rules. The rules are set in the product edit window and also in the Pricing tab of the WooCommerce Settings page.
     *
     *
     * @hooked ldp_apply_dynamic_pricing_bulk
     *
     * @param float $price Current price of the product.
     * @param array $dynamic_pricings Array of the dynamic pricing settings.
     * @param WC_Product $product Product being processed
     * @return float Modified price of the product.
     */
    function apply_dynamic_pricing_bulk( $price, $dynamic_pricings, $product )
    {
        // If a single dynamic pricing is passed, ensure its put in a array (done by single pricing rules)
        $bulk_rules = array();
        
        if ( isset( $dynamic_pricings['condition'] ) ) {
            $bulk_rules = array( $dynamic_pricings );
        } elseif ( isset( $dynamic_pricings['bulk_rules'] ) ) {
            $bulk_rules = $dynamic_pricings['bulk_rules'];
        } else {
            $bulk_rules = $dynamic_pricings;
        }
        
        $p_id = $product->get_id();
        $parent_id = $product->get_parent_id();
        $items = ldp_get_cart_item_quantities();
        $item_qty = ( isset( $items[$p_id] ) ? $items[$p_id] : 1 );
        if ( !empty($dynamic_pricings['variation']) && ($variation = $dynamic_pricings['variation']) ) {
            
            if ( 'all' == $variation ) {
                $item_qty = ( isset( $items[$parent_id] ) ? $items[$parent_id] : 1 );
            } elseif ( 'any' != $variation && $product->get_id() != $variation ) {
                return $price;
            }
        
        }
        foreach ( $bulk_rules as $rule ) {
            // Bail if min/max requirements are not met
            if ( !$this->check_min_max_quantities( $item_qty, $rule['condition']['min'], $rule['condition']['max'] ) ) {
                continue;
            }
            $price = $this->apply_price_adjustment( $price, $rule['adjustment']['amount'], $rule['adjustment']['type'] );
        }
        return $price;
    }
    
    /**
     *
     * Apply the dynamic prices of a single product for a user role. The rules are set in the product edit window.
     *
     *
     * @hooked ldp_apply_dynamic_pricing_role
     *
     * @param float $price Current price of the product.
     * @param array $dynamic_pricing Array of the dynamic price settings.
     * @param WC_Product $product Product being processed
     * @return float Modified price of the product.
     */
    function apply_dynamic_pricing_role( $price, $dynamic_pricing, $product )
    {
        $variation_condition = $dynamic_pricing['condition']['variation'] ?? '';
        if ( !empty($variation_condition) && $variation_condition != $product->get_id() ) {
            return $price;
        }
        
        if ( is_user_logged_in() && ($user = wp_get_current_user()) ) {
            if ( in_array( $dynamic_pricing['condition']['role'], $user->roles ) ) {
                $price = $this->apply_price_adjustment( $price, $dynamic_pricing['adjustment']['amount'], $dynamic_pricing['adjustment']['type'] );
            }
        } elseif ( 'not_logged_in' == $dynamic_pricing['condition']['role'] ) {
            $price = $this->apply_price_adjustment( $price, $dynamic_pricing['adjustment']['amount'], $dynamic_pricing['adjustment']['type'] );
        }
        
        return $price;
    }
    
    /**
     *
     * Apply the custom pricing rules and adjustments set for a product. The rules are set in the product edit window.
     *
     * @hooked apply_dynamic_pricing_custom
     *
     * @param float $price Current price of the product.
     * @param array $dynamic_pricing Array of the dynamic price settings.
     * @param WC_Product $product Product being processed
     * @return float Modified price of the product.
     */
    function apply_dynamic_pricing_custom( $price, $dynamic_pricing, $product )
    {
        if ( lwc_match_conditions( $dynamic_pricing['condition'], array(
            'context' => 'ldp',
            'product' => $product,
        ) ) ) {
            $price = $this->apply_price_adjustment( $price, $dynamic_pricing['adjustment']['amount'], $dynamic_pricing['adjustment']['type'] );
        }
        return $price;
    }
    
    /**
     *
     * Apply the universal dynamic pricing rules captured for simple adjustment. Set in the Pricing tab of WooCommerce Settings page.
     *
     *
     * @hooked ldp_apply_dynamic_pricing_adjustment
     *
     * @param float $price Current price of the product.
     * @param array $dynamic_pricings Array of the dynamic pricing settings.
     * @param WC_Product $product Product being processed
     * @return float                         Modified price of the product.
     */
    function apply_dynamic_pricing_adjustment( $price, $dynamic_pricings, $product )
    {
        // Bail if $dynamic_pricings is invalid
        if ( !$dynamic_pricings ) {
            return $price;
        }
        $dynamic_pricing = reset( $dynamic_pricings );
        $price = $this->apply_price_adjustment( $price, $dynamic_pricing['adjustment']['amount'], $dynamic_pricing['adjustment']['type'] );
        return $price;
    }
    
    /**
     *
     * Apply the pricing adjustment to the product price depending on the adjustment type set by the user
     *
     *
     * @param float $original_price Original price.
     * @param string $price_adjustment Price adjustment amount to apply
     * @param string $adjustment_type Price adjustment type specifying the type of adjustment to be made to the price
     * @return float New price with the price adjustment applied.
     */
    function apply_price_adjustment( $original_price, $price_adjustment, $adjustment_type = 'discount_amount' )
    {
        if ( empty($price_adjustment) ) {
            return $original_price;
        }
        $price_adjustment = str_replace( ',', '.', $price_adjustment );
        $new_price = wc_format_decimal( $original_price );
        
        if ( $adjustment_type === 'discount_percentage' ) {
            // -10% - Give 10% off
            $new_price = $original_price - $original_price / 100 * $price_adjustment;
        } elseif ( $adjustment_type === 'discount_amount' ) {
            // -10 - $10 off
            $new_price = $original_price - $price_adjustment;
        }
        
        return $new_price;
    }
    
    /**
     *
     * Check whether the minimum and maximum product quantities (added to the cart) match.
     *
     *
     * @param int $quantity Actual quantity used.
     * @param int $min Minimum quantity required.
     * @param int $max Maximum quantity allowed.
     * @return bool True when the Quantity matches the requirements, false otherwise.
     */
    function check_min_max_quantities( $quantity, $min, $max )
    {
        if ( !empty($min) && $quantity < $min ) {
            return false;
        }
        // Bail if maximum is not set, or item qty is not met
        if ( !empty($max) && $quantity > $max ) {
            return false;
        }
        // Bail if both max and min are empty
        if ( empty($min) && empty($max) ) {
            return false;
        }
        return true;
    }
    
    /**
     *
     * Get a list of adjustment types.
     *
     *
     * @return array List of adjustment types
     */
    function get_adjustment_types()
    {
        $adjustment_types = array(
            'discount_amount'     => __( 'Fixed Discount', 'livemesh-dynamic-pricing' ),
            'discount_percentage' => __( 'Percentage Discount', 'livemesh-dynamic-pricing' ),
        );
        $adjustment_types = apply_filters( 'ldp_adjustment_types', $adjustment_types );
        return $adjustment_types;
    }

}