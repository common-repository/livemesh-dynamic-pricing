<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'LWC_Height_Condition' ) ) {

	class LWC_Height_Condition extends LWC_Condition {

		public function __construct() {
			$this->name        = __( 'Height', 'lwc-conditions' );
			$this->slug        = __( 'height', 'lwc-conditions' );
			$this->group       = __( 'Product', 'lwc-conditions' );
			$this->description = __( 'Compared to the highest product in cart', 'lwc-conditions' );

			parent::__construct();
		}

		public function get_value_for_comparison() {

			$height = array();
			foreach ( WC()->cart->get_cart() as $item ) :

				/** @var $product WC_Product */
				$product = $item['data'];
				$height[] = $product->get_height();

			endforeach;

			return max( $height );

		}

	}

}