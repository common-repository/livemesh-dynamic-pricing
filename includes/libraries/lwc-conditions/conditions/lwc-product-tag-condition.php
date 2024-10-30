<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'LWC_Product_Tag_Condition' ) ) {

	class LWC_Product_Tag_Condition extends LWC_Condition {

		public function __construct() {
			$this->name        = __( 'Product tag', 'lwc-conditions' );
			$this->slug        = __( 'product_tag', 'lwc-conditions' );
			$this->group       = __( 'Product', 'lwc-conditions' );
			$this->description = __( 'Compare against the product tags', 'lwc-conditions' );

			parent::__construct();
		}

		public function validate() {
			return isset( $GLOBALS['product'] );
		}

		public function match( $match, $operator, $value ) {

			if ( ! $this->validate() ) {
				return false;
			}

			/** @var $product WC_Product */
			global $product;
			$value = $this->get_value( $value );

			if ( $operator == '==' ) {
				$match = ( has_term( $value, 'product_tag', $product->get_id() ) );
			} elseif ( $operator == '!=' ) {
				$match = ( ! has_term( $value, 'product_tag', $product->get_id() ) );
			}

			return $match;

		}

		public function get_operators() {

			$operators = parent::get_operators();

			unset( $operators['>='] );
			unset( $operators['<='] );

			return $operators;

		}


		public function get_value_field_args() {

			$terms = get_terms( 'product_tag', array( 'hide_empty' => false ) );

			$field_args = array(
				'type' => 'select',
				'options' => wp_list_pluck( $terms, 'name', 'slug' ),
				'class' => array( 'lwc-value', 'wc-enhanced-select' ),
			);

			return $field_args;

		}


	}

}