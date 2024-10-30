<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'LWC_Category_Condition' ) ) {

	class LWC_Category_Condition extends LWC_Condition {

		public function __construct() {
			$this->name        = __( 'Category', 'lwc-conditions' );
			$this->slug        = __( 'category', 'lwc-conditions' );
			$this->group       = __( 'Product', 'lwc-conditions' );
			$this->description = __( 'All products in cart must match the given category', 'lwc-conditions' );

			parent::__construct();
		}

		public function match( $match, $operator, $value ) {

			$value = $this->get_value( $value );
			$match = true;

			if ( '==' == $operator ) :

				foreach ( WC()->cart->get_cart() as $product ) :

					if ( ! has_term( $value, 'product_cat', $product['product_id'] ) ) :
						$match = false;
					endif;

				endforeach;

			elseif ( '!=' == $operator ) :

				foreach ( WC()->cart->get_cart() as $product ) :

					if ( has_term( $value, 'product_cat', $product['product_id'] ) ) :
						$match = false;
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