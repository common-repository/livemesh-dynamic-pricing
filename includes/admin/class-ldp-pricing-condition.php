<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// Exit if accessed directly
/**
 * LDP Pricing Condition class.
 *
 * Represents a single condition in a condition group.
 *
 */
class LDP_Pricing_Condition
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
    /**
     * Constructor.
     *
     *
     */
    public function __construct(
        $id = null,
        $group = 0,
        $condition = 'day',
        $operator = null,
        $value = null
    )
    {
        $this->id = $id;
        $this->group = $group;
        $this->condition = $condition;
        $this->operator = $operator;
        $this->value = $value;
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
        require 'views/html-condition-row.php';
    }
    
    /**
     *
     * Get a list of the available conditions for the plugin.
     *
     *
     * @return array List of available conditions.
     */
    public function get_conditions()
    {
        $conditions = array(
            __( 'General', 'livemesh-dynamic-pricing' ) => array(
            'day'  => __( 'Day', 'livemesh-dynamic-pricing' ),
            'time' => __( 'Time', 'livemesh-dynamic-pricing' ),
        ),
            __( 'Product', 'livemesh-dynamic-pricing' ) => array(
            'product'    => __( 'Product', 'livemesh-dynamic-pricing' ),
            'variation'  => __( 'Variation', 'livemesh-dynamic-pricing' ),
            'quantity'   => __( 'Quantity', 'livemesh-dynamic-pricing' ),
            'price'      => __( 'Price', 'livemesh-dynamic-pricing' ),
            'sale_price' => __( 'Sale price', 'livemesh-dynamic-pricing' ),
            'category'   => __( 'Category', 'livemesh-dynamic-pricing' ),
        ),
            __( 'User', 'livemesh-dynamic-pricing' )    => array(
            'role' => __( 'User role', 'livemesh-dynamic-pricing' ),
            'user' => __( 'User', 'livemesh-dynamic-pricing' ),
        ),
        );
        return apply_filters( 'ldp_conditions', $conditions );
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
        return apply_filters( 'ldp_operators', $lwc_condition->get_operators(), $lwc_condition );
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
        
        if ( $this->condition == 'product' && ($product = wc_get_product( $this->value )) ) {
            $field_args['custom_attributes']['data-selected'] = $product->get_formatted_name();
            // WC < 2.7
            $field_args['options'][$this->value] = $product->get_formatted_name();
            // WC >= 2.7
        }
        
        switch ( $this->condition ) {
            case 'variation':
                $products = get_posts( array(
                    'posts_per_page' => '-1',
                    'post_type'      => 'product_variation',
                    'orderby'        => 'title',
                    'order'          => 'ASC',
                ) );
                $field_args['field'] = 'select';
                $field_args['class'][] = 'wc-enhanced-select';
                $field_args['options'] = wp_list_pluck( $products, 'post_title', 'ID' );
                break;
            case 'user':
                $field_args['field'] = 'select';
                $field_args['class'][] = 'wc-enhanced-select';
                $users = get_users( array(
                    'fields' => array( 'ID', 'user_nicename' ),
                ) );
                $users = wp_list_pluck( $users, 'user_nicename', 'ID' );
                $field_args['options'] = $users;
                break;
        }
        $field_args = apply_filters( 'ldp_values', $field_args, $this->condition );
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