<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'LWC_Product_Shipping_Class_Condition' ) ) {

	class LWC_Product_Shipping_Class_Condition extends LWC_Condition {

		public function __construct() {
			$this->name        = __( 'Product Shipping Class', 'lwc-conditions' );
			$this->slug        = __( 'product_shipping_class', 'lwc-conditions' );
			$this->group       = __( 'Product', 'lwc-conditions' );
			$this->description = __( 'Compare against the product shipping class', 'lwc-conditions' );

			parent::__construct();
		}

		public function validate() {
			return isset( $GLOBALS['product'] );
		}

		public function get_value_for_comparison() {
			/** @var $product WC_Product */
			global $product;
			return $product->get_shipping_class();
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