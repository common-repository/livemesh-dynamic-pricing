<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// Exit if accessed directly
/**
 * Get quantities for cart items
 *
 * @return array
 */
function ldp_get_cart_item_quantities()
{
    $quantities = array();
    if ( WC()->cart ) {
        foreach ( WC()->cart->get_cart() as $cart_item_key => $item ) {
            /** @var WC_Product $product */
            $product = $item['data'];
            $p_id = $item['product_id'];
            $v_id = $item['variation_id'];
            
            if ( $product->is_type( 'variation' ) ) {
                $quantities[$v_id] = ( isset( $quantities[$v_id] ) ? $quantities[$v_id] + $item['quantity'] : $item['quantity'] );
                $quantities[$p_id] = ( isset( $quantities[$p_id] ) ? $quantities[$p_id] + $item['quantity'] : $item['quantity'] );
            } else {
                $quantities[$p_id] = ( isset( $quantities[$p_id] ) ? $quantities[$p_id] + $item['quantity'] : $item['quantity'] );
            }
        
        }
    }
    return $quantities;
}

/**
 *
 * Show the possible price adjustments that can be configured as a help tip.
 *
 * @return string
 */
function ldp_price_adjustment_help_tip()
{
    return wc_help_tip( '<strong>Set a price, examples;</strong><br/>
		<table>
			<tr><td>10 with Fixed Discount</td><td>Deduct ' . get_woocommerce_currency_symbol() . '10</td></tr>
			<tr><td>10 with Percentage Discount</td><td>Deduct 10%</td></tr>
		</table>' );
}

/**
 * Recursively parse arguments, looking for defaults
 *
 * @return array
 */
if ( !function_exists( 'ldp_parse_args' ) ) {
    function ldp_parse_args( $args, $defaults )
    {
        $new_args = (array) $defaults;
        foreach ( $args as $key => $value ) {
            
            if ( is_array( $value ) && isset( $new_args[$key] ) ) {
                $new_args[$key] = ldp_parse_args( $value, $new_args[$key] );
            } else {
                $new_args[$key] = $value;
            }
        
        }
        return $new_args;
    }

}