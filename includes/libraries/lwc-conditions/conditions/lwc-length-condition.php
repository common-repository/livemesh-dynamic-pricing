<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'LWC_Length_Condition' ) ) {

	class LWC_Length_Condition extends LWC_Condition {

		public function __construct() {
			$this->name        = __( 'Length', 'lwc-conditions' );
			$this->slug        = __( 'length', 'lwc-conditions' );
			$this->group       = __( 'Product', 'lwc-conditions' );
			$this->description = __( 'Compared to the lengthiest product in cart', 'lwc-conditions' );

			parent::__construct();
		}

		public function get_value_for_comparison() {

			$length = array();
			foreach ( WC()->cart->get_cart() as $item ) :

				/** @var $product WC_Product */
				$product = $item['data'];
				$length[] = $product->get_length();

			endforeach;

			return max( $length );

		}

	}

}