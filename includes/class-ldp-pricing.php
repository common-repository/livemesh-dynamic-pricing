<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


class LDP_Pricing {


    /**
     *
     * Get all the universal pricing rules from the database.
     *
     *
     * @param array $args Arguments for the WP_Query
     * @return array List of post IDs of dynamic pricings.
     */
    public function get_universal_pricing_rules($args = array()) {

        if (false === $return = wp_cache_get('ldp_universal_pricing_rules', 'ldp')) {

            $query = new WP_Query(wp_parse_args($args, array(
                'post_type' => 'ldp_dynamic_pricing',
                'post_status' => 'publish',
                'posts_per_page' => 1000,
                'orderby' => 'menu_order',
                'order' => 'ASC',
                'update_post_term_cache' => false,
                'no_found_rows' => true
            )));

            $return = $query->posts;
            wp_cache_set('ldp_universal_pricing_rules', $return, 'ldp');
        }

        return $return;
    }

    /**
     *
     * Get the single product dynamic pricing rules .
     *
     * @param WC_Product $single_product Product with pricing rules defined.
     * @return array List of dynamic pricings.
     */
    public function get_single_product_pricing_rules($single_product) {

        $product = $single_product->is_type('variation') ? wc_get_product($single_product->get_parent_id()) : $single_product;
        $pricing_rules = (array)$product->get_meta('_ldp_dynamic_pricing');

        return apply_filters('ldp_get_single_product_pricing_rules', array_filter($pricing_rules), $single_product->get_id());
    }

    /**
     *
     * This is the main function that applies all the dynamic pricing rules.
     * It gets the universal and single product pricing rules and applies them
     *
     *
     *
     * @param float $price Current price of the product.
     * @param WC_Product $product Product being processed.
     * @param string $apply_to The price type - regular or sale price to which rules are to be applied
     * @return float  Modified price after applying the dynamic pricing conditions/rules.
     */
    public function apply_dynamic_pricing_rules($price, $product, $apply_to = 'regular_price') {

        if ($price === '') {
            return $price;
        }

        // Single dynamic prices
        $universal_pricings = $this->get_universal_pricing_rules();
        $single_product_pricings = $this->get_single_product_pricing_rules($product);

        if (!empty($universal_pricings) || !empty($single_product_pricings)) {

            /**
             * Create a very specific hash. When the hash exists in the cache
             * it will take that value so the rest of the method won't be
             * executed too many times.
             */
            $price_hash = md5(json_encode(apply_filters('ldp_price_hash', array(
                'item_quantities' => WC()->cart->get_cart_item_quantities(),
                'price' => $price,
                'price_type' => $apply_to,
                'product_id' => $product->get_id(),
                'dynamic_price' => array(
                    'single' => $single_product_pricings,
                    'universal' => wp_list_pluck($universal_pricings, 'ID'),
                ),
            ))));

            // Save the price so the rest of the method isn't executed multiple times.
            if (false !== wp_cache_get($price_hash, 'product_pricing')) {
                $price = wp_cache_get($price_hash, 'product_pricing');
            }
            else {
                $price = $this->apply_single_product_dynamic_pricing($price, $apply_to, $product);
                $price = $this->apply_universal_dynamic_pricing($price, $apply_to, $product);
                wp_cache_set($price_hash, $price, 'product_pricing');
            }
        }

        return $price;
    }


    /**
     *
     * Apply the universal pricing rules applicable to all products matching
     * the rules/conditions defined
     *
     *
     * @param float $price Current price of the product.
     * @param string $apply_to The price type - regular or sale
     * @param WC_Product $product Product being processed
     * @return float Modified price of the product.
     */
    public function apply_universal_dynamic_pricing($price, $apply_to = 'regular_price', $product) {

        $dynamic_pricings = $this->get_universal_pricing_rules();

        if ($dynamic_pricings) {
            foreach ($dynamic_pricings as $post) {

                $conditions = get_post_meta($post->ID, '_pricing_conditions', true);
                $pricing_rules = get_post_meta($post->ID, '_dynamic_pricing', true);
                $pricing_method = get_post_meta($post->ID, '_pricing_method', true);
                $price_type = get_post_meta($post->ID, '_price_type', true);

                if (!empty($price_type) && $price_type != $apply_to) {
                    continue;
                }

                if (lwc_match_conditions($conditions, array('context' => 'ldp', 'product' => $product))) {

                    /**
                     *
                     * Allow various pricing methods (bulk, adjustment) to hook
                     * in here to first match their conditions, and afterwards apply their
                     * prices.
                     *
                     *
                     * @hooked ldp_apply_dynamic_pricing_bulk
                     * @hooked ldp_apply_dynamic_pricing_adjustment
                     */
                    $price = apply_filters('ldp_apply_dynamic_pricing_' . $pricing_method, $price, $pricing_rules, $product);
                }
            }
        }

        return $price;
    }


    /**
     *
     * Apply the pricing rules set in the Pricing tab of Product Data settings table of a single product page
     *
     *
     * @param float $price Current price of the product.
     * @param string $apply_to The price type - regular or sale
     * @param WC_Product $product Product being processed.
     * @return float Modified price of the product.
     */
    public function apply_single_product_dynamic_pricing($price, $apply_to = 'regular_price', $product) {
        $dynamic_pricings = $this->get_single_product_pricing_rules($product);

        foreach ($dynamic_pricings as $dynamic_pricing) {

            $price_type = $dynamic_pricing['adjustment']['price_type'];
            if (!empty($price_type) && $price_type != $apply_to) {
                continue;
            }

            /**
             *
             * Allow various pricing methods (bulk, role, custom) to hook
             * in here to first match their conditions, and afterwards apply their
             * prices.
             *
             *
             *
             * @hooked ldp_apply_dynamic_pricing_bulk
             * @hooked ldp_apply_dynamic_pricing_role
             * @hooked ldp_apply_dynamic_pricing_custom
             */
            $price = apply_filters('ldp_apply_dynamic_pricing_' . $dynamic_pricing['type'], $price, $dynamic_pricing, $product);
        }

        return $price;
    }

}
