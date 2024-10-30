<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'LWC_Role_Condition' ) ) {

	class LWC_Role_Condition extends LWC_Condition {

		public function __construct() {
			$this->name        = __( 'Role', 'lwc-conditions' );
			$this->slug        = __( 'role', 'lwc-conditions' );
			$this->group       = __( 'User', 'lwc-conditions' );
			$this->description = __( 'Compare against the user role', 'lwc-conditions' );

			parent::__construct();
		}

		public function match( $match, $operator, $value ) {

			$value     = $this->get_value( $value );
			$user_caps = $this->get_value_for_comparison();

			if ( '==' == $operator ) :
				$match = ( array_key_exists( $value, $user_caps ) );
			elseif ( '!=' == $operator ) :
				$match = ( ! array_key_exists( $value, $user_caps ) );
			endif;

			return $match;

		}

		public function get_value_for_comparison() {
			if ( is_user_logged_in() ) {
				global $current_user;
				return $current_user->caps;
			} else {
				return array( 'not_logged_in' => 'not_logged_in' );
			}

		}

		public function get_operators() {

			$operators = parent::get_operators();

			unset( $operators['>='] );
			unset( $operators['<='] );

			return $operators;

		}

		public function get_value_field_args() {

			$user_roles = array_keys( get_editable_roles() );
			$user_roles = array_combine( $user_roles, $user_roles );
			$user_roles['not_logged_in'] = __( 'Guest user', 'lwc-conditions' );

			$field_args = array(
				'type' => 'select',
				'class' => array( 'lwc-value' ),
				'options' => $user_roles,
			);

			return $field_args;

		}

	}

}