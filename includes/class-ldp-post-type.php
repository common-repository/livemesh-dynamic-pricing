<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class LDP_Post_Type
 *
 * Initialize the LDP post type
 *
 */
class LDP_Post_Type {
    
	public function __construct() {

		// Register post type
		add_action( 'init', array( $this, 'register_post_type' ) );

		// Add/save meta boxes
		add_action( 'add_meta_boxes', array( $this, 'add_post_meta_boxes' ) );

		add_action( 'save_post', array( $this, 'save_post_meta' ) );

		// Clear product transients
		add_action( 'save_post', array( $this, 'clear_transient_cache' ) );

		// Edit user notices
		add_filter( 'post_updated_messages', array( $this, 'custom_post_updated_messages' ) );

		// Redirect after delete
		add_action( 'load-edit.php', array( $this, 'redirect_after_trash' ) );

		// Keep WC menu open while in LDP edit screen
		add_action( 'admin_head', array( $this, 'highlight_menu' ) );
	}


	/**
	 * 
	 * Register the LDP Dynamic Pricing post type.
	 *
	 */
	public function register_post_type() {

		$labels = array(
			'name'               => __( 'Dynamic Pricing', 'livemesh-dynamic-pricing' ),
			'singular_name'      => __( 'Dynamic Pricing', 'livemesh-dynamic-pricing' ),
			'add_new'            => __( 'Add New', 'livemesh-dynamic-pricing' ),
			'add_new_item'       => __( 'Add New Dynamic Pricing', 'livemesh-dynamic-pricing' ),
			'edit_item'          => __( 'Edit Dynamic Pricing', 'livemesh-dynamic-pricing' ),
			'new_item'           => __( 'New Dynamic Pricing', 'livemesh-dynamic-pricing' ),
			'view_item'          => __( 'View Dynamic Pricing', 'livemesh-dynamic-pricing' ),
			'search_items'       => __( 'Search Dynamic Pricing', 'livemesh-dynamic-pricing' ),
			'not_found'          => __( 'No Dynamic Pricing found', 'livemesh-dynamic-pricing' ),
			'not_found_in_trash' => __( 'No Dynamic Pricing found in Trash', 'livemesh-dynamic-pricing' ),
		);

		register_post_type( 'ldp_dynamic_pricing', array(
			'show_ui'            => true,
			'show_in_menu'       => false,
			'publicly_queryable' => false,
			'capability_type'    => 'post',
			'map_meta_cap'       => true,
			'rewrite'            => array( 'slug' => 'ldp_dynamic_pricing', 'with_front' => true ),
			'_builtin'           => false,
			'query_var'          => true,
			'supports'           => array( 'title' ),
			'labels'             => $labels,
		) );
	}


    /**
     *
     * Modify the notice messages text for the 'ldp_dynamic_pricing' post type.
     *
     * @param array $messages Existing list of messages.
     * @return array Modified list of messages.
     */
    function custom_post_updated_messages($messages) {

        $post = get_post();
        $post_type = get_post_type($post);
        $post_type_object = get_post_type_object($post_type);

        $messages['ldp_dynamic_pricing'] = array(
            0 => '',
            1 => __('Dynamic Pricing updated.', 'livemesh-dynamic-pricing'),
            2 => __('Custom field updated.', 'livemesh-dynamic-pricing'),
            3 => __('Custom field deleted.', 'livemesh-dynamic-pricing'),
            4 => __('Dynamic Pricing updated.', 'livemesh-dynamic-pricing'),
            5 => isset($_GET['revision']) ?
                sprintf(__('Dynamic Pricing restored to revision from %s', 'livemesh-dynamic-pricing'), wp_post_revision_title((int)$_GET['revision'], false))
                : false,
            6 => __('Dynamic Pricing published.', 'livemesh-dynamic-pricing'),
            7 => __('Dynamic Pricing saved.', 'livemesh-dynamic-pricing'),
            8 => __('Dynamic Pricing submitted.', 'livemesh-dynamic-pricing'),
            9 => sprintf(
                __('Dynamic Pricing scheduled for: <strong>%1$s</strong>.', 'livemesh-dynamic-pricing'),
                date_i18n(__('M j, Y @ G:i', 'livemesh-dynamic-pricing'), strtotime($post->post_date))
            ),
            10 => __('Dynamic Pricing draft updated.', 'livemesh-dynamic-pricing'),
        );

        $permalink = admin_url('/admin.php?page=wc-settings&tab=ldp_pricing');
        $overview_link = sprintf(' <a href="%s">%s</a>', esc_url($permalink), __('Return to overview.', 'livemesh-dynamic-pricing'));
        $messages['ldp_dynamic_pricing'][1] .= $overview_link;
        $messages['ldp_dynamic_pricing'][6] .= $overview_link;
        $messages['ldp_dynamic_pricing'][9] .= $overview_link;
        $messages['ldp_dynamic_pricing'][8] .= $overview_link;
        $messages['ldp_dynamic_pricing'][10] .= $overview_link;

        return $messages;
    }


	/**
	 * 
	 * Add the conditions and adjustment meta boxes to the 'ldp_dynamic_pricing' post type.
	 *
	 * 
	 */
	public function add_post_meta_boxes() {

		// Pricing Conditions meta box
		$conditions_title = __( 'Dynamic Pricing conditions', 'livemesh-dynamic-pricing' );
		add_meta_box( 'ldp-conditions', $conditions_title, array( $this, 'meta_box_conditions_output' ), 'ldp_dynamic_pricing', 'normal' );

		// Price Adjustment meta box
		$pricing_title = __( 'Price Adjustment', 'livemesh-dynamic-pricing' );
		add_meta_box( 'ldp-settings', $pricing_title, array( $this, 'meta_box_price_adjustment_output' ), 'ldp_dynamic_pricing', 'normal' );
	}


	/**
	 * 
	 * Output the conditions meta box contents.
	 *
	 */
	public function meta_box_conditions_output() {
		require_once plugin_dir_path( __FILE__ ) . 'admin/views/meta-box-conditions.php';
	}


	/**
	 * Output price adjustment meta box contents.
	 *
	 * 
	 */
	public function meta_box_price_adjustment_output() {
		require_once plugin_dir_path( __FILE__ ) . 'admin/views/meta-box-price-adjustment.php';
	}


	/**
	 *
	 * Output the settings for pricing method chosen by the user.
	 * These are the settings fields in the meta box below the conditions.
	 *
	 *
	 * @param string $type Price pricing method.
     * @param int $post_id ID of the current dynamic pricing post.
	 */
	public function display_settings_for_pricing_method( $type = 'bulk', $post_id ) {

		$dynamic_pricings = get_post_meta( $post_id, '_dynamic_pricing', true );
		switch ( $type ) {

			case 'adjustment' :
				$dynamic_pricings = array_slice( (array) $dynamic_pricings, 0, 1 ); // 'Simple Adjustment' only supports one field
				require plugin_dir_path( __FILE__ ) . 'admin/views/pricing-methods/simple-adjustment.php';
				break;

			case 'bulk' :
				require plugin_dir_path( __FILE__ ) . 'admin/views/pricing-methods/bulk-pricing.php';
				break;

			default:
				do_action( 'ldp_settings_for_pricing_method_' . $type, $type, $post_id );
				break;
		}

		do_action( 'ldp_after_meta_box_price_adjustment', $type, $post_id );
	}


	/**
	 *
	 * Validate and save post meta. This value contains
	 * all other data other than the conditions.
	 *
	 *
	 * @param int $post_id ID of the post being saved.
	 * @return int|void
	 */
	public function save_post_meta( $post_id ) {

		if ( ! isset( $_POST['ldp_price_adjustment_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['ldp_price_adjustment_meta_box_nonce'], 'ldp_price_adjustment_meta_box' ) ) {
			return $post_id;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return $post_id;
		}

		$posted = wp_parse_args( $_POST, array(
			'pricing_method' => '',
			'price_type' => '',
			'dynamic_pricing' => '',
			'conditions' => array(),
		) );

		if ( isset( $posted['dynamic_pricing']['9999'] ) ) {
			unset( $posted['dynamic_pricing']['9999'] );
		}

		// Pricing settings
		update_post_meta( $post_id, '_pricing_method', esc_attr( $posted['pricing_method'] ) );
		update_post_meta( $post_id, '_price_type', esc_attr( $posted['price_type'] ) );
		update_post_meta( $post_id, '_dynamic_pricing', wc_clean($posted['dynamic_pricing'] ));

		// Conditions
		update_post_meta( $post_id, '_pricing_conditions', lwc_sanitize_conditions( $_POST['conditions'] ) );
	}


	/**
	 *
	 * Clear the full product transient cache to ensure the new dynamic pricing is displayed.
	 *
	 *
	 * @param int $post_id
	 * @return bool
	 */
	public function clear_transient_cache( $post_id ) {

		if ( ! isset( $_POST['ldp_price_adjustment_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['ldp_price_adjustment_meta_box_nonce'], 'ldp_price_adjustment_meta_box' ) ) {
			return false;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return false;
		}

		global $wpdb;

		// Clear product price cache
		$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_%wc_var_prices_%'" );

		return true;
	}


	/**
	 *
	 * Redirect user after deleting a dynamic_pricing post.
	 *
	 * 
	 */
	public function redirect_after_trash() {

		$screen = get_current_screen();

		if ( 'edit-ldp_dynamic_pricing' == $screen->id ) {
			if ( isset( $_GET['trashed'] ) && intval( $_GET['trashed'] ) > 0 ) {

				$redirect = admin_url( '/admin.php?page=wc-settings&tab=ldp_pricing' );
				wp_redirect( $redirect );
				exit();
			}
		}
	}


	/**
	 *
	 * Highlights the correct top level admin menu item for post type add screens.
	 *
	 */
	public function highlight_menu() {
		global $parent_file, $submenu_file, $post_type;

		if ( 'ldp_dynamic_pricing' == $post_type ) {
			$parent_file  = 'woocommerce';
			$submenu_file = 'wc-settings';
		}
	}


}
