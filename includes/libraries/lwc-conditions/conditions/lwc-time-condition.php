<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'LWC_Time_Condition' ) ) {

	class LWC_Time_Condition extends LWC_Condition {

		public function __construct() {
			$this->name        = __( 'Time', 'lwc-conditions' );
			$this->slug        = __( 'time', 'lwc-conditions' );
			$this->group       = __( 'General', 'lwc-conditions' );
			$this->description = sprintf( __( 'Compares current server time to user given time. Current time: %s', 'lwc-conditions' ), date_i18n( get_option( 'time_format' ) ) );

			parent::__construct();
		}

		public function get_value( $value ) {
			return date_i18n( 'H:i', strtotime( $value ) ); // Returns set time in Hour:Minutes
		}

		public function get_value_for_comparison() {
			return current_time( 'H:i' ); // Compares against current time in Hour:Minutes
		}

		public function get_value_field_args() {

			$field_args = array(
				'type' => 'text',
				'class' => array( 'lwc-value' ),
				'placeholder' => sprintf( __( 'Current time is: %s', 'lwc-conditions' ), current_time( 'H:i' ) ),
			);

			return $field_args;

		}

	}

}