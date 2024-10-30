<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'LWC_Day_Condition' ) ) {

	class LWC_Day_Condition extends LWC_Condition {

		public function __construct() {
			$this->name        = __( 'Day', 'lwc-conditions' );
			$this->slug        = __( 'day', 'lwc-conditions' );
			$this->group       = __( 'General', 'lwc-conditions' );
			$this->description = sprintf( __( 'Compare to day. You can use ranges with greater/less operators. Current day: %s', 'lwc-conditions' ), date_i18n( 'l' ) );

			parent::__construct();
		}

		public function get_value_for_comparison() {
			return current_time( 'N' ); // Returns current day
		}

		public function get_value_field_args() {

			$days[1] = 'Monday';
			$days[2] = 'Tuesday';
			$days[3] = 'Wednesday';
			$days[4] = 'Thursday';
			$days[5] = 'Friday';
			$days[6] = 'Saturday';
			$days[7] = 'Sunday';

			$field_args = array(
				'type' => 'select',
				'class' => array( 'lwc-value' ),
				'options' => $days,
			);

			return $field_args;

		}

	}

}