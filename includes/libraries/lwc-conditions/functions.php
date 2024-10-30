<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// Exit if accessed directly
if ( is_admin() ) {
    require_once plugin_dir_path( __FILE__ ) . 'admin-functions.php';
}
require_once 'conditions/lwc-condition.php';
require_once 'conditions/lwc-default-condition.php';
// General
require_once 'conditions/lwc-page-condition.php';
require_once 'conditions/lwc-day-condition.php';
require_once 'conditions/lwc-time-condition.php';
require_once 'conditions/lwc-subtotal-condition.php';
require_once 'conditions/lwc-subtotal-ex-tax-condition.php';
require_once 'conditions/lwc-tax-condition.php';
require_once 'conditions/lwc-quantity-condition.php';
require_once 'conditions/lwc-contains-product-condition.php';
require_once 'conditions/lwc-coupon-condition.php';
require_once 'conditions/lwc-weight-condition.php';
require_once 'conditions/lwc-contains-shipping-class-condition.php';
require_once 'conditions/lwc-contains-category-condition.php';
require_once 'conditions/lwc-shipping-method-condition.php';
require_once 'conditions/lwc-payment-gateway-condition.php';
require_once 'conditions/lwc-role-condition.php';
require_once 'conditions/lwc-category-condition.php';
// Product (single based)
require_once 'conditions/lwc-product-condition.php';
require_once 'conditions/lwc-product-category-condition.php';
require_once 'conditions/lwc-product-shipping-class-condition.php';
require_once 'conditions/lwc-product-tag-condition.php';
require_once 'conditions/lwc-product-height-condition.php';
require_once 'conditions/lwc-product-length-condition.php';
require_once 'conditions/lwc-product-price-condition.php';
require_once 'conditions/lwc-product-sale-price-condition.php';
require_once 'conditions/lwc-product-sales-condition.php';
require_once 'conditions/lwc-product-on-sale-condition.php';
if ( !function_exists( 'lwc_get_registered_conditions' ) ) {
    /**
     *
     * @return LWC_Condition[] List of condition classes
     */
    function lwc_get_registered_conditions()
    {
        $conditions = array(
            new LWC_Page_Condition(),
            new LWC_Day_Condition(),
            new LWC_Time_Condition(),
            new LWC_Subtotal_Condition(),
            new LWC_Subtotal_Ex_Tax_Condition(),
            new LWC_Tax_Condition(),
            new LWC_Quantity_Condition(),
            new LWC_Contains_Product_Condition(),
            new LWC_Coupon_Condition(),
            new LWC_Weight_Condition(),
            new LWC_Contains_Category_Condition(),
            new LWC_Role_Condition(),
            new LWC_Category_Condition(),
            new LWC_Product_Condition(),
            new LWC_Product_Category_Condition(),
            new LWC_Product_Shipping_Class_Condition(),
            new LWC_Product_Tag_Condition(),
            new LWC_Product_Price_Condition(),
            new LWC_Product_Sale_Price_Condition(),
            new LWC_Product_Sales_Condition(),
            new LWC_Product_On_Sale_Condition()
        );
        return apply_filters( 'lwc-conditions/registered_conditions', $conditions );
    }

}
if ( !function_exists( 'lwc_get_condition' ) ) {
    /**
     * Get a instance of a LWC_Condition class.
     *
     * @param string $condition Name of the condition to get.
     * @return LWC_Condition|bool LWC_Condition instance when class exists, false otherwise.
     */
    function lwc_get_condition( $condition )
    {
        $class_name = 'LWC_' . implode( '_', array_map( 'ucfirst', explode( '_', $condition ) ) ) . '_Condition';
        $class_name = apply_filters( 'lwc_get_condition_class_name', $class_name, $condition );
        
        if ( class_exists( $class_name ) ) {
            return new $class_name();
        } else {
            return new LWC_Default_Condition();
        }
    
    }

}
if ( !function_exists( 'lwc_match_conditions' ) ) {
    /**
     * Check if conditions match, if all conditions in one condition group
     * matches it will return true and the fee will be applied.
     *
     * @param  array $condition_groups List of condition groups containing their conditions.
     * @param array $args Arguments to pass to the matching method.
     * @return bool true if all the conditions in one of the condition groups matches true.
     */
    function lwc_match_conditions( $condition_groups = array(), $args = array() )
    {
        if ( empty($condition_groups) || !is_array( $condition_groups ) ) {
            return false;
        }
        foreach ( $condition_groups as $condition_group => $conditions ) {
            $match_condition_group = true;
            foreach ( $conditions as $condition ) {
                $condition = apply_filters( 'lwc-conditions/condition', $condition );
                // BC helper
                $lwc_condition = lwc_get_condition( $condition['condition'] );
                // Match the condition - pass any custom ($)args as parameters.
                $match = call_user_func_array( array( $lwc_condition, 'match' ), array(
                    false,
                    $condition['operator'],
                    $condition['value'],
                    $args
                ) );
                // Filter the matched result - BC helper
                $parameters = array(
                    'lwc-conditions/condition/match',
                    $match,
                    $condition['condition'],
                    $condition['operator'],
                    $condition['value'],
                    $args
                );
                $match = call_user_func_array( 'apply_filters', $parameters );
                // Original - simple - way
                //				$match         = $lwc_condition->match( false, $condition['operator'], $condition['value'] );
                //				$match         = apply_filters( 'lwc-conditions/condition/match', $match, $condition['condition'], $condition['operator'], $condition['value'] );
                if ( false == $match ) {
                    $match_condition_group = false;
                }
            }
            // return true if one condition group matches
            if ( true == $match_condition_group ) {
                return true;
            }
        }
        return false;
    }

}
if ( !function_exists( 'lwc_sanitize_conditions' ) ) {
    /**
     * Go over all the conditions and sanitize them. Used when the conditions are being saved.
     *
     * @param  array $conditions The list of conditions.
     * @return array
     */
    function lwc_sanitize_conditions( $conditions )
    {
        $sanitized_conditions = array();
        foreach ( $conditions as $group_key => $condition_group ) {
            if ( $group_key == '9999' ) {
                continue;
            }
            // Template group
            foreach ( $condition_group as $condition_id => $condition_values ) {
                if ( $condition_id == '9999' ) {
                    continue;
                }
                // Template condition
                if ( !isset( $condition_values['value'] ) ) {
                    $condition_values['value'] = '';
                }
                foreach ( $condition_values as $condition_key => $condition_value ) {
                    switch ( $condition_key ) {
                        default:
                            $condition_value = sanitize_text_field( $condition_value );
                            break;
                        case 'condition':
                            $condition_value = sanitize_key( $condition_value );
                            break;
                        case 'operator':
                            $condition_value = ( in_array( $condition_value, array(
                                '==',
                                '!=',
                                '>=',
                                '<='
                            ) ) ? $condition_value : '==' );
                            break;
                        case 'value':
                            
                            if ( is_array( $condition_value ) ) {
                                $condition_value = array_map( 'sanitize_text_field', $condition_value );
                            } elseif ( is_string( $condition_value ) ) {
                                $condition_value = sanitize_text_field( $condition_value );
                            }
                            
                            break;
                    }
                    $sanitized_conditions[$group_key][$condition_id][$condition_key] = $condition_value;
                }
            }
        }
        return $sanitized_conditions;
    }

}
if ( !function_exists( 'lwc_condition_operators' ) ) {
    /**
     * Get a list of the available operators for all the conditions.
     * Mainly used to determine which operators to show per condition.
     *
     *
     * @return array List of condition operators.
     */
    function lwc_condition_operators()
    {
        $condition_operators = array(
            'default' => array(
            '==' => __( 'Equal to', 'lwc-conditions' ),
            '!=' => __( 'Not equal to', 'lwc-conditions' ),
            '>=' => __( 'Greater or equal to', 'lwc-conditions' ),
            '<=' => __( 'Less or equal to ', 'lwc-conditions' ),
        ),
        );
        foreach ( lwc_get_registered_conditions() as $condition ) {
            $condition_operators[$condition->get_slug()] = $condition->get_operators();
        }
        return apply_filters( 'lwc-conditions/condition_operators', $condition_operators );
    }

}
if ( !function_exists( 'lwc_condition_descriptions' ) ) {
    /**
     *
     * Get a list of the available operators for all the conditions.
     * Mainly used to determine which operators to show per condition.
     *
     *
     * @return array List of condition operators.
     */
    function lwc_condition_descriptions()
    {
        $condition_descriptions = array();
        foreach ( lwc_get_registered_conditions() as $condition ) {
            $condition_descriptions[$condition->get_slug()] = $condition->get_description();
        }
        return apply_filters( 'lwc-conditions/condition_descriptions', $condition_descriptions );
    }

}