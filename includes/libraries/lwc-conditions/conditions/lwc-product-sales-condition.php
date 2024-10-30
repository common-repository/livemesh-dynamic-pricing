<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'LWC_Product_Sales_Condition' ) ) {

	class LWC_Product_Sales_Condition extends LWC_Condition {

		public function __construct() {
			$this->name        = __( 'Product sales', 'lwc-conditions' );
			$this->slug        = __( 'product_sales', 'lwc-conditions' );
			$this->group       = __( 'Product', 'lwc-conditions' );
			$this->description = __( 'Compare against the total product sales', 'lwc-conditions' );

			parent::__construct();
		}


		public function validate() {
			return isset( $GLOBALS['product'] );
		}

		public function get_value_for_comparison() {
			/** @var $product WC_Product */
			global $product;

			if ( method_exists( $product, 'get_total_sales' ) ) { // WC 2.7
				return $product->get_total_sales();
			} else { // < WC 2.7
				return get_post_meta( $product->get_id(), 'total_sales', true );
			}
		}

	}

}