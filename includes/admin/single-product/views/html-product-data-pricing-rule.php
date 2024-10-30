<div class='ldp-dynamic-pricing-condition wc-metabox closed' rel='<?php 
echo  '' ;
/* $position */
?>'>

    <h3 class="ldp-dynamic-pricing-title-wrap">
        <strong class='ldp-dynamic-pricing-name'><?php 
// Pricing title

if ( 'bulk' == $condition_type ) {
    $title = __( 'Bulk', 'livemesh-dynamic-pricing' );
    //				$title .= isset( $dynamic_price['condition'] ) ? ' - Min. ' . $dynamic_price['condition']['min'] . ' - Max. ' . $dynamic_price['condition']['max'] : '';
} elseif ( 'role' == $condition_type ) {
    $title = __( 'Role', 'livemesh-dynamic-pricing' );
    $title .= ( isset( $dynamic_price['condition'] ) ? ' - ' . $dynamic_price['condition']['role'] : '' );
} elseif ( 'custom' == $condition_type ) {
    $title = __( 'Custom', 'livemesh-dynamic-pricing' );
} else {
    $title = ucfirst( $condition_type );
}

$title = apply_filters( 'ldp_pricing_rule_title', $title, $dynamic_price );
?><span class='ldp-dynamic-pricing-title'
                    title="<?php 
echo  esc_attr( strip_tags( $title ) ) ;
?>"><?php 
echo  wp_kses_post( $title ) ;
?></span><?php 
?><span style=''><?php 

if ( !empty($dynamic_price['adjustment']['amount']) ) {
    $adjustment_type = ( isset( $dynamic_price['adjustment']['type'] ) ? $dynamic_price['adjustment']['type'] : 'discount_amount' );
    $amount = str_replace( ',', '.', $dynamic_price['adjustment']['amount'] );
    
    if ( $adjustment_type == 'discount_percentage' ) {
        echo  '-' . esc_html( $amount ) . '%' ;
    } elseif ( $adjustment_type == 'discount_amount' ) {
        echo  '-' . wc_price( $amount ) ;
    }

}

?></span><?php 
?></strong>
        <div class='handlediv' title='<?php 
_e( 'Click to toggle', 'woocommerce' );
?>'></div>
        <a href="#" class="remove_row delete"><?php 
esc_html_e( 'Remove', 'woocommerce' );
?></a>
    </h3>

    <div class="ldp-dynamic-pricing-data wc-metabox-content" data-index="<?php 
echo  absint( $i ) ;
?>"><?php 
do_action( 'display_product_dynamic_pricing_' . $condition_type, $i, $dynamic_price );
?></div>

</div>
