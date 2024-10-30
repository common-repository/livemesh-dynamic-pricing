<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'LWC_Contains_Product_Condition' ) ) {

	class LWC_Contains_Product_Condition extends LWC_Condition {

		public function __construct() {
			$this->name        = __( 'Contains product', 'lwc-conditions' );
			$this->slug        = __( 'contains_product', 'lwc-conditions' );
			$this->group       = __( 'Cart', 'lwc-conditions' );
			$this->description = __( 'Check if a product is or is not present in the cart', 'lwc-conditions' );

			parent::__construct();
		}

		public function match( $match, $operator, $value ) {

			$product_ids = $this->get_value_for_comparison();

			if ( '==' == $operator ) :
				$match = ( in_array( $value, $product_ids ) );
			elseif ( '!=' == $operator ) :
				$match = ( ! in_array( $value, $product_ids ) );
			endif;

			return $match;

		}

		public function get_value_for_comparison() {

			$product_ids = array();
			foreach ( WC()->cart->get_cart() as $item ) :
				$product_ids[] = $item['product_id'];
				if ( isset( $item['variation_id'] ) ) {
					$product_ids[] = $item['variation_id'];
				}
			endforeach;

			return $product_ids;

		}

		public function get_operators() {

			$operators = parent::get_operators();

			unset( $operators['>='] );
			unset( $operators['<='] );

			return $operators;

		}

		public function get_value_field_args() {

			$field_args = array(
				'type' => 'select',
				'custom_attributes' => array(
					'data-placeholder' => __( 'Search for a product', 'lwc-conditions' ),
				),
				'class' => array( 'lwc-value', 'wc-product-search' ),
				'options' => array(),
			);

			return $field_args;

		}

	}

}