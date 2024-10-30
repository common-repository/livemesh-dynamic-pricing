<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// Exit if accessed directly
/**
 * Class LDP_Match_Conditions.
 *
 * The LDP Match Conditions class handles the matching rules for the price adjustments.
 *
 */
class LDP_Match_Conditions
{
    public function __construct()
    {
        // Product
        add_filter(
            'ldp_match_condition_product',
            array( $this, 'match_condition_product' ),
            10,
            4
        );
        add_filter(
            'ldp_match_condition_variation',
            array( $this, 'match_condition_variation' ),
            10,
            4
        );
        add_filter(
            'ldp_match_condition_quantity',
            array( $this, 'match_condition_quantity' ),
            10,
            4
        );
        add_filter(
            'ldp_match_condition_price',
            array( $this, 'match_condition_price' ),
            10,
            4
        );
        add_filter(
            'ldp_match_condition_sale_price',
            array( $this, 'match_condition_sale_price' ),
            10,
            4
        );
        add_filter(
            'ldp_match_condition_category',
            array( $this, 'match_condition_product_category' ),
            10,
            4
        );
        // User
        add_filter(
            'ldp_match_condition_user',
            array( $this, 'match_condition_user' ),
            10,
            4
        );
        add_filter(
            'lwc-conditions/condition/match',
            array( $this, 'apply_filter_match_condition' ),
            10,
            5
        );
    }
    
    /**
     * Add the appropriate filters required for the matching functionality.
     *
     */
    function apply_filter_match_condition(
        $match,
        $condition,
        $operator,
        $value,
        $args
    )
    {
        if ( !isset( $args['context'] ) || $args['context'] != 'ldp' ) {
            return $match;
        }
        if ( has_filter( 'ldp_match_condition_' . $condition ) && isset( $args['product'] ) ) {
            $match = apply_filters(
                'ldp_match_condition_' . $condition,
                $match,
                $operator,
                $value,
                $args['product']
            );
        }
        return $match;
    }
    
    /**
     *
     * Match the condition value against the product.
     *
     * 
     *
     * @param  bool       $match    Current match value.
     * @param  string     $operator Operator selected by the user in the condition row.
     * @param  mixed      $value    Value given by the user in the condition row.
     * @param  WC_Product $product  Product being processed.
     * @return bool  Matching result, true if results match, otherwise false.
     */
    public function match_condition_product(
        $match,
        $operator,
        $value,
        $product
    )
    {
        $product_ids = array( $product->get_id() );
        if ( $product->is_type( 'variation' ) ) {
            $product_ids[] = ( method_exists( $product, 'get_parent_id' ) ? $product->get_parent_id() : $product->variation_id );
        }
        
        if ( '==' == $operator ) {
            $match = in_array( $value, $product_ids );
        } elseif ( '!=' == $operator ) {
            $match = !in_array( $value, $product_ids );
        }
        
        return $match;
    }
    
    /**
     *
     * Match the condition value against the product variation.
     *
     * @param  bool       $match    Current match value.
     * @param  string     $operator Operator selected by the user in the condition row.
     * @param  mixed      $value    Value given by the user in the condition row.
     * @param  WC_Product $product  Product being processed.
     * @return bool                 Matching result, true if results match, otherwise false.
     */
    public function match_condition_variation(
        $match,
        $operator,
        $value,
        $product
    )
    {
        
        if ( '==' == $operator ) {
            $match = $product->get_id() == $value;
        } elseif ( '!=' == $operator ) {
            $match = $product->get_id() != $value;
        }
        
        return $match;
    }
    
    /**
     *
     * Match the condition value against the product quantity in the cart.
     *
     * 
     *
     * @param  bool       $match    Current match value.
     * @param  string     $operator Operator selected by the user in the condition row.
     * @param  mixed      $value    Value given by the user in the condition row.
     * @param  WC_Product $product  Product being processed.
     * @return bool                 Matching result, true if results match, otherwise false.
     */
    public function match_condition_quantity(
        $match,
        $operator,
        $value,
        $product
    )
    {
        if ( !isset( WC()->cart ) ) {
            return;
        }
        $items = WC()->cart->get_cart_item_quantities();
        $item_qty = ( isset( $items[$product->get_id()] ) ? $items[$product->get_id()] : 1 );
        
        if ( '==' == $operator ) {
            $match = $item_qty == $value;
        } elseif ( '!=' == $operator ) {
            $match = $item_qty != $value;
        } elseif ( '>=' == $operator ) {
            $match = $item_qty >= $value;
        } elseif ( '<=' == $operator ) {
            $match = $item_qty <= $value;
        }
        
        return $match;
    }
    
    /**
     *
     * Match the condition value against product price.
     *
     * 
     *
     * @param  bool       $match    Current match value.
     * @param  string     $operator Operator selected by the user in the condition row.
     * @param  mixed      $value    Value given by the user in the condition row.
     * @param  WC_Product $product  Product being processed.
     * @return bool                 Matching result, true if results match, otherwise false.
     */
    public function match_condition_price(
        $match,
        $operator,
        $value,
        $product
    )
    {
        ldp_remove_price_filters();
        $price = $product->get_price();
        ldp_add_price_filters();
        
        if ( '==' == $operator ) {
            $match = $price == $value;
        } elseif ( '!=' == $operator ) {
            $match = $price != $value;
        } elseif ( '>=' == $operator ) {
            $match = $price >= $value;
        } elseif ( '<=' == $operator ) {
            $match = $price <= $value;
        }
        
        return $match;
    }
    
    /**
     *
     * Match the condition value against product sale price.
     *
     *
     * @param  bool       $match    Current match value.
     * @param  string     $operator Operator selected by the user in the condition row.
     * @param  mixed      $value    Value given by the user in the condition row.
     * @param  WC_Product $product  Product being processed.
     * @return bool                 Matching result, true if results match, otherwise false.
     */
    public function match_condition_sale_price(
        $match,
        $operator,
        $value,
        $product
    )
    {
        ldp_remove_price_filters();
        $sale_price = $product->get_sale_price();
        ldp_add_price_filters();
        
        if ( '==' == $operator ) {
            $match = $sale_price == $value;
        } elseif ( '!=' == $operator ) {
            $match = $sale_price != $value;
        } elseif ( '>=' == $operator ) {
            $match = $sale_price >= $value;
        } elseif ( '<=' == $operator ) {
            $match = $sale_price <= $value;
        }
        
        return $match;
    }
    
    /**
     *
     * Match the condition value against the product category.
     *
     *
     * @param  bool       $match    Current match value.
     * @param  string     $operator Operator selected by the user in the condition row.
     * @param  mixed      $value    Value given by the user in the condition row.
     * @param  WC_Product $product  Product being processed.
     * @return bool                 Matching result, true if results match, otherwise false.
     */
    public function match_condition_product_category(
        $match,
        $operator,
        $value,
        $product
    )
    {
        $parent_id = $product->get_parent_id();
        $product_id = ( !empty($parent_id) ? $parent_id : $product->get_id() );
        
        if ( '==' == $operator ) {
            $match = has_term( $value, 'product_cat', $product_id );
        } elseif ( '!=' == $operator ) {
            $match = !has_term( $value, 'product_cat', $product_id );
        }
        
        return $match;
    }
    
    /**
     *
     * Match the condition value against the user.
     *
     *
     * @param  bool       $match    Current match value.
     * @param  string     $operator Operator selected by the user in the condition row.
     * @param  mixed      $value    Value given by the user in the condition row.
     * @param  WC_Product $product  Product being processed.
     * @return bool                 Matching result, true if results match, otherwise false.
     */
    public function match_condition_user(
        $match,
        $operator,
        $value,
        $product
    )
    {
        if ( is_user_logged_in() ) {
            
            if ( '==' == $operator ) {
                $match = get_current_user_id() == $value;
            } elseif ( '!=' == $operator ) {
                $match = get_current_user_id() != $value;
            }
        
        }
        return $match;
    }

}