<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'LWC_Default_Condition' ) ) {


	/**
	 * Default condition is used when lwc_get_condition() is used for a
	 * invalid (unknown) condition class. This ensures nothing breaks and
	 * the initial $match value is returned > ready to be handled by the filter hook.
	 */
	class LWC_Default_Condition extends LWC_Condition {

		public function __construct() {
			$this->name        = __( 'Default', 'lwc-conditions' );
			$this->slug        = __( 'default', 'lwc-conditions' );
			$this->description = __( 'Default  Condition', 'lwc-conditions' );

			parent::__construct();
		}

		public function match( $match, $operator, $value ) {
			return $match;
		}

	}

}