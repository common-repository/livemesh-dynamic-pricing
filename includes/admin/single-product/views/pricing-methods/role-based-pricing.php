<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

global $post;
$product = wc_get_product($post);

if (empty($dynamic_price))
    $dynamic_price = array();

$dynamic_price = ldp_parse_args($dynamic_price, array(
    'condition' => array(
        'role' => '',
        'variation' => ''),
    'adjustment' => array(
        'price_type' => 'regular_price',
        'type' => 'discount_amount',
        'amount' => ''),
));

$selected_role = $dynamic_price['condition']['role'];
$selected_variation = $dynamic_price['condition']['variation'];
$price_type = $dynamic_price['adjustment']['price_type'];
$adjustment_type = $dynamic_price['adjustment']['type'];
$pricing_amount = $dynamic_price['adjustment']['amount'];
$user_roles = wp_list_pluck(get_editable_roles(), 'name');

?><input type='hidden' class='ldp-dynamic-pricing-type' name='dynamic_pricing[<?php echo esc_attr($i); ?>][type]'
         value='role'>

<table cellpadding='0' cellspacing='0'>
    <tbody>

    <tr>
        <td colspan='3'>
            <h4 style='padding-left: 0 !important; margin: 0;'><?php _e('Condition', 'livemesh-dynamic-pricing'); ?></h4>
        </td>
    </tr>

    <tr>
        <td class='ldp-dynamic-pricing-condition-role' colspan='2'>
            <label><?php _e('User role', 'livemesh-dynamic-pricing'); ?>:</label>
            <select class='' name='dynamic_pricing[<?php echo esc_attr($i); ?>][condition][role]'>
                <?php foreach ($user_roles as $key => $role) : ?>
                    <option <?php selected($key, $selected_role); ?>
                            value='<?php echo esc_attr($key); ?>'><?php echo esc_attr($role); ?></option>
                <?php endforeach; ?>
                <option <?php selected('not_logged_in', $selected_role); ?>
                        value='not_logged_in'><?php _e('Not logged in', 'livemesh-dynamic-pricing'); ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td colspan='3'>
            <hr/>
        </td>
    </tr>
    <tr>
        <td colspan='3'>
            <h4 style='padding-left: 0 !important; margin:0;'><?php _e('Adjustment', 'livemesh-dynamic-pricing'); ?></h4>
        </td>
    </tr>
    <tr>
        <td>
            <label><?php _e('Price type', 'livemesh-dynamic-pricing'); ?>:</label>
            <select name='dynamic_pricing[<?php echo esc_attr($i); ?>][adjustment][price_type]'>
                <option value='regular_price' <?php selected($dynamic_price['adjustment']['price_type'], 'regular_price'); ?>><?php _e('Regular price', 'livemesh-dynamic-pricing'); ?></option>
                <option value='sale_price' <?php selected($dynamic_price['adjustment']['price_type'], 'sale_price'); ?>><?php _e('Sale price', 'livemesh-dynamic-pricing'); ?></option>
            </select>
        </td>

        <td>
            <label><?php _e('Adjustment', 'livemesh-dynamic-pricing'); ?>:</label>
            <?php $adjustment_types = Livemesh_Dynamic_Pricing()->pricing_helper->get_adjustment_types(); ?>
            <select class='ldp-adjustment-type'
                    name='dynamic_pricing[<?php echo esc_attr($i); ?>][adjustment][type]'>
                <?php foreach ($adjustment_types as $key => $values) : ?>
                    <?php if (!is_array($values)) : ?>
                        <option
                                value='<?php echo esc_attr($key); ?>' <?php selected($key, $adjustment_type); ?>><?php echo esc_attr($values); ?></option>
                    <?php else : ?>
                        <optgroup label='<?php echo esc_attr($key); ?>'>
                            <?php foreach ($values as $k => $v) : ?>
                                <option value='<?php echo esc_attr($k); ?>' <?php selected($k, $adjustment_type); ?>><?php echo esc_attr($v); ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </td>
        <td>
            <label><?php _e('Amount', 'woocommerce'); ?>:</label>
            <div class="lwc-currency-wrap">
                <span class='lwc-currency'><?php echo get_woocommerce_currency_symbol(); ?></span>
                <input type='text' class='ldp-adjustment-amount'
                       name='dynamic_pricing[<?php echo esc_attr($i); ?>][adjustment][amount]'
                       value='<?php echo esc_attr($pricing_amount); ?>'/><?php
                echo ldp_price_adjustment_help_tip();
                ?></div>
        </td>

        <td>
            <?php if ($product && $product->is_type('variable')) : ?>
                <label><?php _e('Variation', 'livemesh-dynamic-pricing'); ?>:</label>
                <select name='dynamic_pricing[<?php echo esc_attr($i); ?>][condition][variation]'
                        class="wc-enhanced-select"
                        style="min-width: 200px;">
                    <option
                            value='' <?php selected($selected_variation, 'any'); ?>><?php _e('Any variation', 'livemesh-dynamic-pricing'); ?></option>
                    <?php foreach ($product->get_children() as $variation_id) : ?>
                        <?php $variation = wc_get_product($variation_id); ?>
                        <option value='<?php echo absint($variation->get_id()); ?>' <?php selected($selected_variation, $variation->get_id()); ?>><?php echo esc_html($variation->get_formatted_name()); ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </td>
    </tr>

    </tbody>
</table>
