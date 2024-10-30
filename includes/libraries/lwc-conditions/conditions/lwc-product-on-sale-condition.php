<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'LWC_Product_On_Sale_Condition' ) ) {

	class LWC_Product_On_Sale_Condition extends LWC_Condition {

		public function __construct() {
			$this->name        = __( 'Product on sale', 'lwc-conditions' );
			$this->slug        = __( 'product_on_sale', 'lwc-conditions' );
			$this->group       = __( 'Product', 'lwc-conditions' );
			$this->description = __( 'Compare if the product in on sale or not', 'lwc-conditions' );

			parent::__construct();
		}

		public function validate() {
			return isset( $GLOBALS['product'] );
		}

		public function get_value_for_comparison() {
			/** @var $product WC_Product */
			global $product;
			return $product->is_on_sale();
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
				'options' => array(
					'1'	=> __( 'Yes', 'lwc-conditions' ),
					'0'	=> __( 'No', 'lwc-conditions' ),
				),
			);

			return $field_args;

		}

	}

}