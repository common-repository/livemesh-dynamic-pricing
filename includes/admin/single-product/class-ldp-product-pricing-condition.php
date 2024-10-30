<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// Exit if accessed directly
/**
 * Condition class.
 *
 * Represents a single condition in a condition group.
 *
 */
class LDP_Product_Pricing_Condition
{
    /**
     * Condition ID.
     *
     *
     * @var string $id Condition ID.
     */
    public  $id ;
    /**
     * Condition.
     *
     *
     * @var string $condition Condition slug.
     */
    public  $condition ;
    /**
     * Operator.
     *
     *
     * @var string $operator Operator slug.
     */
    public  $operator ;
    /**
     * Value.
     *
     *
     * @var string $value Condition value.
     */
    public  $value ;
    /**
     * Group ID.
     *
     *
     * @var string $group Condition group ID.
     */
    public  $group ;
    public function __construct(
        $id = null,
        $group = 0,
        $condition = 'day',
        $operator = null,
        $value = null,
        $index = 0
    )
    {
        $this->id = $id;
        $this->group = $group;
        $this->condition = $condition;
        $this->operator = $operator;
        $this->value = $value;
        $this->index = $index;
        if ( !$id ) {
            $this->id = rand();
        }
    }
    
    /**
     *
     * Output the full condition row which includes: condition, operator, value, add/delete buttons and
     * the description.
     *
     */
    public function output_condition_row()
    {
        $wp_condition = $this;
        require 'views/html-product-condition-row.php';
    }
    
    /**
     *
     * Get a list with the available conditions.
     *
     *
     * @return array List of available conditions for a condition row.
     */
    public function get_conditions()
    {
        $conditions = array(
            __( 'General', 'livemesh-dynamic-pricing' ) => array(
            'day'  => __( 'Day', 'livemesh-dynamic-pricing' ),
            'time' => __( 'Time', 'livemesh-dynamic-pricing' ),
        ),
            __( 'Product', 'livemesh-dynamic-pricing' ) => array(
            'variation' => __( 'Variation', 'livemesh-dynamic-pricing' ),
            'quantity'  => __( 'Quantity', 'livemesh-dynamic-pricing' ),
            'price'     => __( 'Price', 'livemesh-dynamic-pricing' ),
        ),
            __( 'User', 'livemesh-dynamic-pricing' )    => array(
            'role' => __( 'User role', 'livemesh-dynamic-pricing' ),
        ),
        );
        $conditions = apply_filters( 'ldp_single_conditions', $conditions );
        return $conditions;
    }
    
    /**
     *
     * Get a list with the available operators for the conditions.
     *
     *
     * @return array List of available operators.
     */
    public function get_operators()
    {
        $lwc_condition = lwc_get_condition( $this->condition );
        return apply_filters( 'ldp_single_operators', $lwc_condition->get_operators(), $lwc_condition );
    }
    
    /**
     *
     * Get the value field args that are condition dependent. This usually includes
     * type, class and placeholder.
     *
     *
     * @return array
     */
    public function get_value_field_args()
    {
        // Defaults
        $default_field_args = array(
            'name'        => 'conditions[' . absint( $this->group ) . '][' . absint( $this->id ) . '][value]',
            'placeholder' => '',
            'type'        => 'text',
            'class'       => array( 'lwc-value' ),
        );
        $field_args = $default_field_args;
        if ( $condition = lwc_get_condition( $this->condition ) ) {
            $field_args = wp_parse_args( $condition->get_value_field_args(), $field_args );
        }
        
        if ( $this->condition == 'contains_product' && ($product = wc_get_product( $this->value )) ) {
            $field_args['custom_attributes']['data-selected'] = $product->get_formatted_name();
            // WC < 2.7
            $field_args['options'][$this->value] = $product->get_formatted_name();
            // WC >= 2.7
        }
        
        switch ( $this->condition ) {
            case 'variation':
                $field_args['field'] = 'select';
                $field_args['options'] = array();
                $post_id = ( isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : get_the_ID() );
                $product = wc_get_product( $post_id );
                if ( $product && method_exists( $product, 'get_available_variations' ) && ($variations = $product->get_available_variations()) ) {
                    foreach ( $variations as $product ) {
                        $field_args['options'][$product['variation_id']] = '#' . $product['variation_id'];
                    }
                }
                break;
            case 'price':
                $field_args['field'] = 'text';
                $field_args['placeholder'] = __( 'Price', 'livemesh-dynamic-pricing' );
                break;
            case 'role':
                $field_args['field'] = 'select';
                $roles = array_keys( get_editable_roles() );
                $field_args['options'] = array_combine( $roles, $roles );
                $field_args['options'] = array_merge( $field_args['options'], array(
                    'not_logged_in' => 'Not logged in',
                ) );
                break;
        }
        $field_args = apply_filters( 'ldp_single_values', $field_args, $this->condition );
        return $field_args;
    }
    
    /**
     *
     * Return the description related to this condition.
     *
     *
     */
    public function get_description()
    {
        $descriptions = lwc_condition_descriptions();
        return ( isset( $descriptions[$this->condition] ) ? $descriptions[$this->condition] : '' );
    }

}