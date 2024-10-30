<div class='ldp-bulk-row ldp-bulk-price-wrap'>
    <input type='hidden' name='dynamic_pricing[<?php echo esc_attr($i); ?>][adjustment][price_type]'
           value='regular_price'>
    <span class='ldp-bulk-row-field ldp-bulk-row-min'>
		<input type='number' name='dynamic_pricing[<?php echo esc_attr($i); ?>][condition][min]'
               value='<?php echo esc_attr($min); ?>'>
	</span>
    <span class='ldp-bulk-row-field ldp-bulk-row-max'>
		<input type='number' name='dynamic_pricing[<?php echo esc_attr($i); ?>][condition][max]'
               value='<?php echo esc_attr($max); ?>'>
	</span>
    <span class='ldp-bulk-row-field ldp-bulk-row-adjustment-type'>
        <?php $adjustment_types = Livemesh_Dynamic_Pricing()->pricing_helper->get_adjustment_types(); ?>
        <select name='dynamic_pricing[<?php echo esc_attr($i); ?>][adjustment][type]'>
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
    </span>
    <span class='ldp-bulk-row-field ldp-bulk-row-amount'>
		<div class="lwc-currency-wrap">
			<span class='lwc-currency'><?php echo get_woocommerce_currency_symbol(); ?></span>
			<input type='text' name='dynamic_pricing[<?php echo esc_attr($i); ?>][adjustment][amount]'
                   value='<?php echo esc_attr($amount); ?>'>
		</div>
	</span>
    <span class='ldp-bulk-row-actions'>
		<a class='button ldp-bulk-row-delete' href='javascript:void(0);'>-</a>
	</span>
</div>
