<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

if (empty($dynamic_price))
    $dynamic_price = array();

$dynamic_price = ldp_parse_args($dynamic_price, array(
    'adjustment' => array(
        'price_type' => 'regular_price',
        'type' => 'discount_amount',
        'amount' => ''),
));

$price_type = $dynamic_price['adjustment']['price_type'];
$adjustment_type = $dynamic_price['adjustment']['type'];;
$pricing_amount = $dynamic_price['adjustment']['amount'];

$condition_groups = isset($dynamic_price['condition']) ? $dynamic_price['condition'] : '';

?><input type='hidden' class='ldp-dynamic-pricing-type' name='dynamic_pricing[<?php echo esc_attr($i); ?>][type]'
         value='custom'>

<table cellpadding='0' cellspacing='0'>
    <tbody>
    <tr>
        <td colspan='2'><h4
                    style='padding-left: 0 !important; margin: 0;'><?php _e('Condition', 'livemesh-dynamic-pricing'); ?></h4>
        </td>
    </tr>

    <tr>
        <td colspan='3' class='ldp-dynamic-pricing-condition-custom'>
            <div class="lwc-conditions lwc-conditions-meta-box">
                <div class='lwc-condition-groups'>
                    <p>
                        <strong><?php _e('Match one of the condition groups to apply the pricing adjustment', 'livemesh-dynamic-pricing'); ?></strong>
                    </p>
                    <?php
                    if (!empty($condition_groups)) :
                        foreach ($condition_groups as $condition_group => $conditions) :
                            include __DIR__ . '/../html-product-condition-group.php';
                        endforeach;
                    else :
                        $condition_group = '0';
                        include __DIR__ . '/../html-product-condition-group.php';
                    endif;
                    ?>
                </div>

                <div class='lwc-condition-group-template hidden' style='display: none'>
                    <?php
                    $condition_group = '9999';
                    $conditions = array();
                    include __DIR__ . '/../html-product-condition-group.php';
                    ?>
                </div>
                <a class='button lwc-condition-group-add'
                   href='javascript:void(0);'><?php _e('Add \'Or\' group', 'livemesh-dynamic-pricing'); ?></a>
            </div>

        </td>
    </tr>
    <tr>
        <td colspan='2'>
            <hr/>
        </td>
    </tr>
    <tr>
        <td colspan='2'><h4
                    style='padding-left: 0 !important; margin:0;'><?php _e('Adjustment', 'livemesh-dynamic-pricing'); ?></h4>
        </td>
    </tr>

    <tr>
        <td>
            <label><?php _e('Price type', 'livemesh-dynamic-pricing'); ?>:</label>
            <select name='dynamic_pricing[<?php echo esc_attr($i); ?>][adjustment][price_type]'>
                <option value='regular_price' <?php selected($price_type, 'regular_price'); ?>><?php _e('Regular price', 'livemesh-dynamic-pricing'); ?></option>
                <option value='sale_price' <?php selected($price_type, 'sale_price'); ?>><?php _e('Sale price', 'livemesh-dynamic-pricing'); ?></option>
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
            <label><?php _e('Amount', 'livemesh-dynamic-pricing'); ?>:</label>
            <div class="lwc-currency-wrap">
                <span class='lwc-currency'><?php echo get_woocommerce_currency_symbol(); ?></span>
                <input type='text' class='ldp-adjustment-amount'
                       name='dynamic_pricing[<?php echo esc_attr($i); ?>][adjustment][amount]'
                       value='<?php echo esc_attr($pricing_amount); ?>'/>
                <?php echo ldp_price_adjustment_help_tip(); ?>
            </div>
        </td>
    </tr>
    </tbody>
</table>
