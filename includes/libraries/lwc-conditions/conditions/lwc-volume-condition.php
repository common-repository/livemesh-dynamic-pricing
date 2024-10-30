<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'LWC_Volume_Condition' ) ) {

	class LWC_Volume_Condition extends LWC_Condition {

		public function __construct() {
			$this->name        = __( 'Volume', 'lwc-conditions' );
			$this->slug        = __( 'volume', 'lwc-conditions' );
			$this->group       = __( 'Cart', 'lwc-conditions' );
			$this->description = __( 'Volume calculated on all the cart contents', 'lwc-conditions' );

			parent::__construct();
		}

		public function get_value( $value ) {
			return str_replace( ',', '.', $value );
		}

		public function get_value_for_comparison() {
			$volume = 0;

			// Get all product stocks
			foreach ( WC()->cart->cart_contents as $item ) :

				$product = wc_get_product( $item['data']->get_id() );
				$volume += (float) ( $product->get_width() * $product->get_height() * $product->get_length() ) * $item['quantity'];

			endforeach;

			return $volume;
		}

	}

}
