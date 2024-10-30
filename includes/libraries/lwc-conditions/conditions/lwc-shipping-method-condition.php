<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'LWC_Shipping_Method_Condition' ) ) {

	class LWC_Shipping_Method_Condition extends LWC_Condition {

		public function __construct() {
			$this->name        = __( 'Shipping method', 'lwc-conditions' );
			$this->slug        = __( 'shipping_method', 'lwc-conditions' );
			$this->group       = __( 'Cart', 'lwc-conditions' );
			$this->description = __( 'Match against the chosen shipping method', 'lwc-conditions' );

			parent::__construct();
		}

		public function match( $match, $operator, $value ) {

			$value                   = $this->get_value( $value );
			$chosen_shipping_methods = $this->get_value_for_comparison();

			if ( '==' == $operator ) :
				$match = ( in_array( $value, $chosen_shipping_methods ) );
			elseif ( '!=' == $operator ) :
				$match = ( ! in_array( $value, $chosen_shipping_methods ) );
			endif;

			return $match;

		}

		public function get_value_for_comparison() {
			$packages_rates = wp_list_pluck( WC()->shipping()->get_packages(), 'rates' );
			$chosen_rate_ids = (array) WC()->session->get( 'chosen_shipping_methods' );

			// Add shipping method IDs
			foreach ( $packages_rates as $package_key => $rates ) {
				foreach ( $rates as $rate ) {
					if ( array_intersect( array( $rate->id, $rate->method_id, $rate->instance_id ), $chosen_rate_ids ) ) {
						$chosen_rate_ids[] = $rate->method_id;
						$chosen_rate_ids[] = $rate->instance_id;
					}
				}
			}

			return $chosen_rate_ids;
		}

		public function get_operators() {

			$operators = parent::get_operators();

			unset( $operators['>='] );
			unset( $operators['<='] );

			return $operators;

		}

		public function get_value_field_args() {

			$field_args = array(
				'type'    => 'select',
				'options' => $this->get_shipping_options(),
			);

			return $field_args;

		}

		private function get_shipping_options() {
			$shipping_options = array();

			// Global shipping methods
			foreach ( WC()->shipping()->load_shipping_methods() as $method ) {
				$shipping_options['Methods (Any zone)'][ $method->id ] = $method->get_method_title();
			}

			// WC Zones
			$shipping_options = array_merge( $shipping_options, $this->get_zone_instances() );

			return $shipping_options;
		}

		/**
		 *
		 * Get the rates that are within a shipping zone.
		 *
		 * @return array List of shipping rates.
		 */
		private function get_zone_instances() {

			$rates = array();

			if ( ! class_exists( 'WC_Shipping_Zones' ) ) {
				return $rates;
			}

			foreach ( $this->get_zones() as $zone ) {
				foreach ( $zone['shipping_methods'] as $instance_id => $rate ) {
					/** @var WC_Shipping_Method $rate */
					$rates[ $zone['zone_name'] ][ $rate->get_rate_id() ] = $rate->get_title();
				}
			}

			// 'Rest of the world' zone
			$row_zone = WC_Shipping_Zones::get_zone( 0 );
			foreach ( $row_zone->get_shipping_methods() as $instance_id => $rate ) {
				$rates[ $row_zone->get_zone_name() ][ $rate->get_rate_id() ] = $rate->get_title();
			}

			return $rates;

		}

		/**
		 *
		 * Get the registered Shipping Zones.
		 *
		 *
		 * @return array List of shipping zones.
		 */
		private function get_zones() {
			$data_store = WC_Data_Store::load( 'shipping-zone' );
			$raw_zones  = $data_store->get_zones();
			$zones      = array();

			foreach ( $raw_zones as $raw_zone ) {
				$zone                                = new WC_Shipping_Zone( $raw_zone );
				$zones[ $zone->get_id() ]            = $zone->get_data();
				$zones[ $zone->get_id() ]['zone_id'] = $zone->get_id();
				$zones[ $zone->get_id() ]['formatted_zone_location'] = $zone->get_formatted_location();
				$zones[ $zone->get_id() ]['shipping_methods']        = $zone->get_shipping_methods( false, 'admin' );
			}

			return $zones;
		}

	}

}