<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'LWC_Product_Length_Condition' ) ) {

	class LWC_Product_Length_Condition extends LWC_Condition {

		public function __construct() {
			$this->name        = __( 'Product Length', 'lwc-conditions' );
			$this->slug        = __( 'product_length', 'lwc-conditions' );
			$this->group       = __( 'Product', 'lwc-conditions' );
			$this->description = __( 'Compare against the product length', 'lwc-conditions' );

			parent::__construct();
		}

		public function validate() {
			return isset( $GLOBALS['product'] );
		}

		public function get_value_for_comparison() {
			/** @var $product WC_Product */
			global $product;
			return $product->get_length();
		}

		public function get_operators() {

			$operators = parent::get_operators();

			unset( $operators['>='] );
			unset( $operators['<='] );

			return $operators;

		}

	}

}