<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

wp_nonce_field('ldp_price_adjustment_meta_box', 'ldp_price_adjustment_meta_box_nonce');

global $post;

$pricing_method = get_post_meta($post->ID, '_pricing_method', true);
$price_type = get_post_meta($post->ID, '_price_type', true);

if (!$pricing_method) :
    $pricing_method = 'adjustment';
endif;

$pricing_methods = apply_filters('ldp_pricing_methods', array(
    'adjustment' => __('Simple Adjustment', 'livemesh-dynamic-pricing'),
    'bulk' => __('Bulk pricing', 'livemesh-dynamic-pricing'),
));

?>
<div class='ldp ldp-meta-box ldp-settings-meta-box' data-post-id='<?php echo esc_attr($post->ID); ?>'>
    <p class='ldp-option'>
        <label for='pricing_method'><?php _e('Pricing Method', 'livemesh-dynamic-pricing'); ?></label>
        <select name='pricing_method' id='pricing_method' style='width: 200px;'>
            <?php foreach ($pricing_methods as $key => $type) : ?>
                <option <?php selected($pricing_method, $key); ?>
                        value='<?php echo esc_attr($key); ?>'><?php echo esc_html($type); ?></option>
            <?php endforeach; ?>
        </select>
    </p>
    <p class='ldp-option'>
        <label for='price_type'><?php _e('Price Type', 'livemesh-dynamic-pricing'); ?></label>
        <select name='price_type' id='price_type' style='width: 200px;'>
            <option <?php selected($price_type, 'regular_price'); ?>
                    value='regular_price'><?php _e('Regular price', 'livemesh-dynamic-pricing'); ?></option>
            <option <?php selected($price_type, 'sale_price'); ?>
                    value='sale_price'><?php _e('Sale price', 'livemesh-dynamic-pricing'); ?></option>
        </select>
    </p>
    <hr/>
    <div id='ldp-dynamic-pricing-settings'>
        <?php Livemesh_Dynamic_Pricing()->post_type->display_settings_for_pricing_method($pricing_method, $post->ID); ?>
    </div>
</div>
