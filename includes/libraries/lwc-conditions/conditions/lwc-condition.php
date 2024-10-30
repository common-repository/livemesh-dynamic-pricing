<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'LWC_Condition' ) ) {

	abstract class LWC_Condition {

		protected $name = null;
		protected $slug = null;
		protected $group = null;
		protected $description = null;
		protected $value_field_args = array();
		protected $available_operators = array();

		public function __construct() {
			if ( is_null( $this->get_slug() ) ) {
				$this->slug = sanitize_key( $this->get_name() );
			}
		}

		public function get_name() {
			return $this->name;
		}

		public function get_slug() {
			return $this->slug;
		}

		public function get_group() {
			return $this->group;
		}

		/**
		 * @return array
		 */
		public function get_operators() {

			if ( empty( $this->available_operators ) ) {
				$this->available_operators = array(
					'==' => __( 'Equal to', 'lwc-conditions' ),
					'!=' => __( 'Not equal to', 'lwc-conditions' ),
					'>=' => __( 'Greater or equal to', 'lwc-conditions' ),
					'<=' => __( 'Less or equal to ', 'lwc-conditions' ),
				);
			}

			return $this->available_operators;

		}

		public function get_value_field_args() {

			if ( empty( $this->value_field_args ) ) {
				$this->value_field_args = array(
					'type'        => 'text',
					'class'       => array( 'lwc-value' ),
					'placeholder' => '',
				);
			}

			return $this->value_field_args;

		}

		/**
		 *
		 * Get the value the store owner has set.
		 *
		 *
		 * @param $value
		 * @return string
		 */
		public function get_value( $value ) {
			return $value;
		}

		/**
		 *
		 * Get the value the main value should compare against.
		 *
		 *
		 * @return string|mixed The value to compare the store-owner set value against.
		 */
		public function get_value_for_comparison() {

			$compare_value = '';
			return $compare_value;
		}

		public function match( $match, $operator, $value ) {

			if ( ! $this->validate() ) {
				return false;
			}

			$value = $this->get_value( $value );
			$compare_value = $this->get_value_for_comparison();

			if ( '==' == $operator ) :
				$match = ( $compare_value == $value );
			elseif ( '!=' == $operator ) :
				$match = ( $compare_value != $value );
			elseif ( '>=' == $operator ) :
				$match = ( $compare_value >= $value );
			elseif ( '<=' == $operator ) :
				$match = ( $compare_value <= $value );
			endif;

			return $match;
		}

		/**
		 * Validates before matching function.
		 * Can be used for example to verify the global $product exists.
		 */
		public function validate() {
			return true;
		}

		public function get_description() {
			return $this->description;
		}

	}

}
