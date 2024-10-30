<?php

// Defaults
$row = ldp_parse_args($row, array(
    'condition' => array('min' => '', 'max' => ''),
    'adjustment' => array('type' => 'discount_amount', 'amount' => '')
));

$adjustment_type = $row['adjustment']['type'];

?>
<tr class="ldp-bulk-row">
    <td>
        <input type='text' class='ldp-condition-min'
               name='dynamic_pricing[<?php echo esc_attr($i); ?>][bulk_rules][<?php echo absint($row_num); ?>][condition][min]'
               value='<?php echo esc_attr($row['condition']['min']); ?>'/>
    </td>
    <td>
        <input type='text' class='ldp-condition-max'
               name='dynamic_pricing[<?php echo esc_attr($i); ?>][bulk_rules][<?php echo absint($row_num); ?>][condition][max]'
               value='<?php echo esc_attr($row['condition']['max']); ?>'/>
    </td>
    <td>
        <?php $adjustment_types = Livemesh_Dynamic_Pricing()->pricing_helper->get_adjustment_types(); ?>
        <select class='ldp-adjustment-type'
                name='dynamic_pricing[<?php echo esc_attr($i); ?>][bulk_rules][<?php echo absint($row_num); ?>][adjustment][type]'>
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
        <div class="lwc-currency-wrap">
            <span class='lwc-currency'><?php echo get_woocommerce_currency_symbol(); ?></span>
            <input type='text' class='ldp-adjustment-amount'
                   name='dynamic_pricing[<?php echo esc_attr($i); ?>][bulk_rules][<?php echo absint($row_num); ?>][adjustment][amount]'
                   value='<?php echo esc_attr($row['adjustment']['amount']); ?>'/>
            <a class='button ldp-bulk-row-delete' href='javascript:void(0);'><span
                        class="dashicons dashicons-minus"></span></a>
        </div>
    </td>
</tr>
