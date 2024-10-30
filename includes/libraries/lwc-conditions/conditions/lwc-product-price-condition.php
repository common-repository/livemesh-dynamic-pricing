<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'LWC_Product_Price_Condition' ) ) {

	class LWC_Product_Price_Condition extends LWC_Condition {

		public function __construct() {
			$this->name        = __( 'Product Price', 'lwc-conditions' );
			$this->slug        = __( 'product_price', 'lwc-conditions' );
			$this->group       = __( 'Product', 'lwc-conditions' );
			$this->description = __( 'Compare against the product price', 'lwc-conditions' );

			parent::__construct();
		}

		public function validate() {
			return isset( $GLOBALS['product'] );
		}

		public function get_value_for_comparison() {
			/** @var $product WC_Product */
			global $product;
			return $product->get_price();
		}

	}

}