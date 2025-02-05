<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'LWC_Contains_Category_Condition' ) ) {

	class LWC_Contains_Category_Condition extends LWC_Condition {

		public function __construct() {
			$this->name        = __( 'Contains Category', 'lwc-conditions' );
			$this->slug        = __( 'contains_category', 'lwc-conditions' );
			$this->group       = __( 'Cart', 'lwc-conditions' );
			$this->description = __( 'Cart must contain at least one product with the selected category', 'lwc-conditions' );

			parent::__construct();
		}

		public function match( $match, $operator, $value ) {

			$value = $this->get_value( $value );

			if ( '==' == $operator ) :

				foreach ( WC()->cart->get_cart() as $product ) :

					if ( has_term( $value, 'product_cat', $product['product_id'] ) ) :
						return true;
					endif;

				endforeach;

			elseif ( '!=' == $operator ) :

				$match = true;
				foreach ( WC()->cart->get_cart() as $product ) :

					if ( has_term( $value, 'product_cat', $product['product_id'] ) ) :
						return false;
					endif;

				endforeach;

			endif;

			return $match;

		}

		public function get_operators() {

			$operators = parent::get_operators();

			unset( $operators['>='] );
			unset( $operators['<='] );

			return $operators;

		}

		public function get_value_field_args() {

			$categories = get_terms( 'product_cat', array( 'hide_empty' => false ) );
			$field_args = array(
				'type' => 'select',
				'class' => array( 'lwc-value', 'wc-enhanced-select' ),
				'options' => wp_list_pluck( $categories, 'name', 'slug' ),
			);

			return $field_args;

		}

	}

}