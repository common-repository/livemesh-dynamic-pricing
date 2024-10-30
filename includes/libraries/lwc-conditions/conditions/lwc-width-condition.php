<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'LWC_Width_Condition' ) ) {

	class LWC_Width_Condition extends LWC_Condition {

		public function __construct() {
			$this->name        = __( 'Width', 'lwc-conditions' );
			$this->slug        = __( 'width', 'lwc-conditions' );
			$this->group       = __( 'Product', 'lwc-conditions' );
			$this->description = __( 'Compared to the widest product in cart', 'lwc-conditions' );

			parent::__construct();
		}

		public function get_value_for_comparison() {

			$width = array();
			foreach ( WC()->cart->get_cart() as $item ) :

				/** @var $product WC_Product */
				$product = $item['data'];
				$width[] = $product->get_width();

			endforeach;

			return max( $width );

		}

	}

}