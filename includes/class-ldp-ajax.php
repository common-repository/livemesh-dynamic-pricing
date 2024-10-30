<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * LDP_AJAX class.
 *
 * Handles all AJAX related calls.
 *
 */
class LDP_Ajax {


	/**
	 * Constructor.
	 *
	 * Add all AJAX actions.
	 *
	 */
	public function __construct() {

		// Update elements
		add_action( 'wp_ajax_ldp_update_condition_value', array( $this, 'update_condition_value' ) );
		add_action( 'wp_ajax_ldp_update_single_condition_value', array( $this, 'update_single_condition_value' ), 9 ); // should be before the one above

		// Update on change of pricing method - bulk, adjustment
		add_action( 'wp_ajax_ldp_update_pricing_method', array( $this, 'update_pricing_method' ) );

		// Single dynamic pricing rule
		add_action( 'wp_ajax_ldp_single_pricing_condition', array( $this, 'display_single_pricing_condition' ) );

	}


	/**
	 * Update condition value field. Output the HTML of the value field according to the condition key.
	 *
	 * 
	 */
	public function update_condition_value() {

		check_ajax_referer( 'lwc-ajax-nonce', 'nonce' );

        $condition_id = sanitize_key($_POST['id']);
        $condition_group = wc_clean($_POST['group']);
        $condition = wc_clean($_POST['condition']);

		$wp_condition     = new LDP_Pricing_Condition($condition_id ,$condition_group ,$condition );
		$value_field_args = $wp_condition->get_value_field_args();

		?><span class='lwc-value-field-wrap'><?php
			lwc_html_field( $value_field_args );
		?></span><?php

		die();
	}


	/**
	 *
	 * Update the condition rows values to match the condition key.
	 * This function is used for the single product conditions.
	 *
	 * 
	 */
	public function update_single_condition_value() {

		check_ajax_referer( 'lwc-ajax-nonce', 'nonce' );

        $condition_id = sanitize_key($_POST['id']);
        $condition_group = wc_clean($_POST['group']);
        $condition = wc_clean($_POST['condition']);
        $index = sanitize_key($_POST['index']);

		$wp_condition             = new LDP_Product_Pricing_Condition( $condition_id, $condition_group, $condition, null, null, $index );
		$value_field_args         = $wp_condition->get_value_field_args();
		$value_field_args['name'] = 'dynamic_pricing[' . absint( $wp_condition->index ) . '][condition][' . absint( $wp_condition->group ) . '][' . absint( $wp_condition->id ) . '][value]';

		?><span class='lwc-value-field-wrap'><?php
			lwc_html_field( $value_field_args );
		?></span><?php

		die();
	}


	/**
	 *
	 * Render the settings since the drop down value for pricing method has changed.
	 *
	 * 
	 */
	public function update_pricing_method() {
        
		check_ajax_referer( 'lwc-ajax-nonce', 'nonce' );

		Livemesh_Dynamic_Pricing()->post_type->display_settings_for_pricing_method( esc_attr( $_POST['pricing_method'] ), absint( $_POST['post_id'] ) );
        
		die();
	}


	/**
	 *
	 * Display a new single pricing condition.
	 *
	 */
	public function display_single_pricing_condition() {
		check_ajax_referer( 'lwc-ajax-nonce', 'nonce' );

		global $post;

		$post           = get_post( absint( $_POST['post_id'] ) );
		$condition_type = sanitize_text_field( $_POST['condition_type'] );
		$i              = absint( $_POST['index'] );
		$dynamic_price = array();

		require plugin_dir_path( __FILE__ ) . 'admin/single-product/views/html-product-data-pricing-rule.php';
		die;
	}


}
