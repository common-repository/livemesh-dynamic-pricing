<?php

global $post;

$dynamic_pricing = get_post_meta( $post->ID, '_ldp_dynamic_pricing', true );

?><div id='ldp_dynamic_pricing_data' class='panel wc-metaboxes-wrapper' style="display: none;">

	<p class='toolbar'>
		<a href='#' class='close_all'><?php _e( 'Close all', 'woocommerce' ); ?></a>&nbsp;/&nbsp;<a href='#' class='expand_all'><?php _e( 'Expand all', 'woocommerce' ); ?></a>
	</p>

	<div class='ldp-product-pricing wc-metaboxes'><?php

		$i = 0;
		if ( $dynamic_pricing ) :
			foreach ( $dynamic_pricing as $dynamic_price ) :

				$condition_type = $dynamic_price['type'];
				require plugin_dir_path( __FILE__ ) . 'html-product-data-pricing-rule.php';

				$i++;
			endforeach;
		endif;


	?></div>

	<p class='toolbar toolbar-add'>

		<button type='button' class='button button-primary ldp-add-dynamic-pricing-button'><?php _e( 'Add', 'livemesh-dynamic-pricing' ); ?></button>

		<select id='ldp-add-dynamic-pricing' class='ldp-add-dynamic-pricing'><?php

			$pricing_conditions = apply_filters( 'ldp_single_pricing_conditions', array(
				'bulk'   => __( 'Bulk price', 'livemesh-dynamic-pricing' ),
				'role'   => __( 'Role based price', 'livemesh-dynamic-pricing' ),
				'custom' => __( 'Custom', 'livemesh-dynamic-pricing' ),
			) );
			foreach ( $pricing_conditions as $key => $value ) :
				?><option value='<?php echo esc_attr( $key ); ?>'><?php echo esc_attr( $value ); ?></option><?php
			endforeach;

		?></select>

		<label for='ldp-add-dynamic-pricing'><?php _e( 'Add a new pricing condition', 'livemesh-dynamic-pricing' ); ?></label>

	</p><?php

	do_action( 'woocommerce_product_options_dynamic_pricing' );

?></div>
