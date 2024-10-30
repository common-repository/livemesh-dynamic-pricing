<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'LWC_Weight_Condition' ) ) {

	class LWC_Weight_Condition extends LWC_Condition {

		public function __construct() {
			$this->name        = __( 'Weight', 'lwc-conditions' );
			$this->slug        = __( 'weight', 'lwc-conditions' );
			$this->group       = __( 'Cart', 'lwc-conditions' );
			$this->description = __( 'Weight calculated on all the cart contents', 'lwc-conditions' );

			parent::__construct();
		}

		public function get_value( $value ) {
			return str_replace( ',', '.', $value );
		}

		public function get_value_for_comparison() {
			return WC()->cart->get_cart_contents_weight();
		}

		public function get_value_field_args() {

			$field_args = array(
				'class' => array( 'input-text', 'lwc-value', 'wc_input_decimal' ),
			);

			return $field_args;

		}
	}

}
