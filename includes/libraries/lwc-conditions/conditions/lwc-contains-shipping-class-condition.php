<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'LWC_Contains_Shipping_Class_Condition' ) ) {

	class LWC_Contains_Shipping_Class_Condition extends LWC_Condition {

		public function __construct() {
			$this->name        = __( 'Contains shipping class', 'lwc-conditions' );
			$this->slug        = __( 'contains_shipping_class', 'lwc-conditions' );
			$this->group       = __( 'Cart', 'lwc-conditions' );
			$this->description = __( 'Check if a shipping class is or is not present in the cart', 'lwc-conditions' );

			parent::__construct();
		}

		public function match( $match, $operator, $value ) {

			foreach ( WC()->cart->get_cart() as $product ) {

				$id      = ! empty( $product['variation_id'] ) ? $product['variation_id'] : $product['product_id'];
				$product = wc_get_product( $id );

				if ( $operator == '==' ) {
					if ( $product->get_shipping_class() == $value ) {
						return true;
					}
				} elseif ( $operator == '!=' ) {
					$match = true;
					if ( $product->get_shipping_class() == $value ) {
						return false;
					}
				}

			}

			return $match;

		}

		public function get_operators() {

			$operators = parent::get_operators();

			unset( $operators['>='] );
			unset( $operators['<='] );

			return $operators;

		}

		public function get_value_field_args() {

			$shipping_classes = get_terms( 'product_shipping_class', array( 'hide_empty' => false ) );
			$shipping_classes = array_merge(
				array( '-1' => __( 'No shipping class', 'woocommerce' ) ),
				wp_list_pluck( $shipping_classes, 'name', 'slug' )
			);

			$field_args = array(
				'type' => 'select',
				'options' => $shipping_classes,
				'class' => array( 'lwc-value', 'wc-enhanced-select' ),
			);

			return $field_args;

		}

	}

}
