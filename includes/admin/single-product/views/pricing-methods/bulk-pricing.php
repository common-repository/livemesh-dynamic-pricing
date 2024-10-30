<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;
$product = wc_get_product($post);

if (empty($dynamic_price))
    $dynamic_price = array();

$dynamic_price = ldp_parse_args($dynamic_price, array(
    'adjustment' => array(
        'price_type' => 'regular_price'
    ),
    'bulk_rules' => array(),
    'variation' => 'all'
));

$bulk_rows = array();
$bulk_rows = $dynamic_price['bulk_rules'];

?><input type='hidden' class='ldp-dynamic-pricing-type' name='dynamic_pricing[<?php echo esc_attr( $i ); ?>][type]' value='bulk'>

<table cellpadding='0' cellspacing='0'>

	<thead>
		<tr>
			<th><label class="alignleft"><?php _e( 'Minimum', 'livemesh-dynamic-pricing' ); ?></label></th>
			<th><label class="alignleft"><?php _e( 'Maximum', 'livemesh-dynamic-pricing' ); ?></label></th>
            <th><label class="alignleft"><?php _e( 'Adjustment', 'livemesh-dynamic-pricing' ); ?></label></th>
			<th><label class="alignleft"><?php _e( 'Amount', 'livemesh-dynamic-pricing' ); ?> <?php echo ldp_price_adjustment_help_tip(); ?></label></th>
		</tr>
	</thead>

	<tbody class="ldp-bulk-pricing-wrapper"><?php

		if ( ! empty( $bulk_rows ) ) :
            $row_num = 0;
			foreach ( $bulk_rows as $key => $row ) :
				require plugin_dir_path( __FILE__ ) . 'bulk-pricing-row.php';
                $row_num++;
			endforeach;
		else :
			$row_num = 0;
			$row = array();
			require plugin_dir_path( __FILE__ ) . 'bulk-pricing-row.php';
		endif;

	?></tbody>
	<tfoot>

		<tr>
			<td>
				<div class="clearfix">
					<a class="button ldp-bulk-row-add" href="javascript:void(0);">Add row</a>
				</div>
				<div class="ldp-bulk-row-template-wrap hidden"><?php
					?><table class="ldp-bulk-row-template hidden"><?php
						$row_num = 9999;
						$row = array();
						require plugin_dir_path( __FILE__ ) . 'bulk-pricing-row.php';
					?></table><?php
				?></div>

			</td>
		</tr>

		<tr><td colspan='3'><hr /></td></tr>

		<tr>
			<td>
				<label><?php _e( 'Price type', 'livemesh-dynamic-pricing' ); ?>:</label>
				<select name='dynamic_pricing[<?php echo esc_attr( $i ); ?>][adjustment][price_type]'>
					<option value='regular_price' <?php selected( $dynamic_price['adjustment']['price_type'], 'regular_price' ); ?>><?php _e( 'Regular price', 'livemesh-dynamic-pricing' ); ?></option>
					<option value='sale_price' <?php selected( $dynamic_price['adjustment']['price_type'], 'sale_price' ); ?>><?php _e( 'Sale price', 'livemesh-dynamic-pricing' ); ?></option>
				</select>
			</td>
			<td colspan="2"><?php

				if ( $product && $product->is_type( 'variable' ) ) :

					?><label><?php _e( 'Variation', 'livemesh-dynamic-pricing' ); ?>:</label>
					<select name='dynamic_pricing[<?php echo esc_attr( $i ); ?>][variation]' class="wc-enhanced-select">
						<option value='all' <?php selected( $dynamic_price['variation'], 'all' ); ?>><?php _e( 'All variations combined', 'livemesh-dynamic-pricing' ); ?></option>
						<option value='any' <?php selected( $dynamic_price['variation'], 'any' ); ?>><?php _e( 'Any variation (uses per-variation quantity)', 'livemesh-dynamic-pricing' ); ?></option><?php

						foreach ( $product->get_children() as $variation_id ) :
							$variation = wc_get_product( $variation_id );
							?><option value='<?php echo absint( $variation->get_id() ); ?>' <?php selected( $dynamic_price['variation'], $variation->get_id() ); ?>><?php echo esc_html($variation->get_formatted_name()); ?></option><?php
						endforeach;

					?></select><?php

				endif;

			?></td>

		</tr>

	</tfoot>
</table>
