<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'LWC_Payment_Gateway_Condition' ) ) {

	class LWC_Payment_Gateway_Condition extends LWC_Condition {

		public function __construct() {
			$this->name        = __( 'Payment Gateway', 'lwc-conditions' );
			$this->slug        = __( 'payment_gateway', 'lwc-conditions' );
			$this->group       = __( 'Cart', 'lwc-conditions' );
			$this->description = __( 'Payment gateway can only be checked in the checkout', 'lwc-conditions' );

			parent::__construct();
		}

		public function get_value_for_comparison() {
			return WC()->session->get( 'chosen_payment_method' );
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
			);

			foreach ( WC()->payment_gateways->payment_gateways() as $gateway ) :
				$field_args['options'][ $gateway->id ] = $gateway->get_title();
			endforeach;

			return $field_args;

		}

	}

}