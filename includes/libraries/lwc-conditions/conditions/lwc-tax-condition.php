<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'LWC_Tax_Condition' ) ) {

	class LWC_Tax_Condition extends LWC_Condition {

		public function __construct() {
			$this->name        = __( 'Tax', 'lwc-conditions' );
			$this->slug        = __( 'tax', 'lwc-conditions' );
			$this->group       = __( 'Cart', 'lwc-conditions' );
			$this->description = __( 'Compared against the tax total amount', 'lwc-conditions' );

			parent::__construct();
		}

		public function get_value( $value ) {
			return str_replace( ',', '.', $value );
		}

		public function get_value_for_comparison() {
			if ( method_exists( WC()->cart, 'get_cart_contents_tax' ) ) { // WC 3.2+
				return WC()->cart->get_cart_contents_tax();
			}

			return array_sum( (array) WC()->cart->taxes );
		}

		public function get_value_field_args() {

			$field_args = array(
				'class' => array( 'input-text', 'lwc-value', 'wc_input_price' ),
			);

			return $field_args;

		}
	}

}