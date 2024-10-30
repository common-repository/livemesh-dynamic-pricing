<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'LWC_Quantity_Condition' ) ) {

	class LWC_Quantity_Condition extends LWC_Condition {

		public function __construct() {
			$this->name        = __( 'Quantity', 'lwc-conditions' );
			$this->slug        = __( 'quantity', 'lwc-conditions' );
			$this->group       = __( 'Cart', 'lwc-conditions' );
			$this->description = __( 'Compared against the quantity of items in the cart', 'lwc-conditions' );

			parent::__construct();
		}

		public function get_value_for_comparison() {
			return WC()->cart->get_cart_contents_count();
		}

		public function get_value_field_args() {

			$field_args = array(
				'type' => 'number',
			);

			return $field_args;

		}

	}

}