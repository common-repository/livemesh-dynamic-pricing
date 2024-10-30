<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'LWC_Product_Condition' ) ) {

	class LWC_Product_Condition extends LWC_Condition {

		public function __construct() {
			$this->name        = __( 'Product', 'lwc-conditions' );
			$this->slug        = __( 'product', 'lwc-conditions' );
			$this->group       = __( 'Product', 'lwc-conditions' );
			$this->description = __( 'Compare against the current product', 'lwc-conditions' );

			parent::__construct();
		}

		public function validate() {
			return isset( $GLOBALS['product'] );
		}

		public function get_value_for_comparison() {
			/** @var $product WC_Product */
			global $product;

            // To be sure, If we don't get the product object
            if( ! is_a($product, 'WC_Product') ) {
                // Try to get an instance of the WC_Product object from Post id
                $product = wc_get_product( get_the_id() );
            }
			return $product->get_id();
		}

		public function get_operators() {

			$operators = parent::get_operators();

			unset( $operators['>='] );
			unset( $operators['<='] );

			return $operators;

		}

		public function get_value_field_args() {

			$field_args = array(
				'type' => 'text',
				'custom_attributes' => array(
					'data-placeholder' => __( 'Search for a product', 'lwc-conditions' ),
				),
				'class' => array( 'lwc-value', 'wc-product-search' ),
				'options' => array(),
			);

			// Should be a select field in WC 2.7+
			if ( version_compare( WC()->version, '2.7', '>=' ) ) {
				$field_args['type'] = 'select';
			}

			return $field_args;

		}

	}

}