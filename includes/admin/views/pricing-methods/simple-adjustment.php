<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

?>
<p class='ldp-simple-adjustment-settings'>

<div class='head'>
    <span style='width: 30%; display: inline-block;' class=''><strong>
            <label for='message'><?php _e('Adjustment', 'livemesh-dynamic-pricing'); ?></label></strong></span>
</div>

<div class='ldp-simple-adjustment-wrapper'>
    <input type='hidden' name='dynamic_pricing[0][adjustment][price_type]' value='regular_price'>
    <div class='ldp-simple-adjustment'>
        <?php if ($dynamic_pricings) : ?>
            <?php foreach ($dynamic_pricings as $key => $dynamic_pricing) : ?>
                <?php
                $amount = isset($dynamic_pricing['adjustment']['amount']) ? $dynamic_pricing['adjustment']['amount'] : '';
                $adjustment_type = isset($dynamic_pricing['adjustment']['type']) ? $dynamic_pricing['adjustment']['type'] : 'discount_amount';
                ?>

                <span class='ldp-adjustment-type'>
                <?php $adjustment_types = Livemesh_Dynamic_Pricing()->pricing_helper->get_adjustment_types(); ?>
                    <select name='dynamic_pricing[0][adjustment][type]'>
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
                <span style='width: 30%; display: flex; align-items: center;' class=''>
						<div class="lwc-currency-wrap">
							<span class='lwc-currency'><?php echo get_woocommerce_currency_symbol(); ?></span>
							<input type='text' name='dynamic_pricing[0][adjustment][amount]'
                                   value='<?php echo esc_attr($amount); ?>'>
						</div>
                    <?php echo ldp_price_adjustment_help_tip(); ?>
                </span>
            <?php endforeach; ?>
        <?php else : ?>
            <span class='ldp-adjustment-type'>
                <?php $adjustment_types = Livemesh_Dynamic_Pricing()->pricing_helper->get_adjustment_types(); ?>
                <select name='dynamic_pricing[0][adjustment][type]'>
                    <?php foreach ($adjustment_types as $key => $values) : ?>
                        <?php if (!is_array($values)) : ?>
                            <option
                                    value='<?php echo esc_attr($key); ?>'><?php echo esc_attr($values); ?></option>
                        <?php else : ?>
                            <optgroup label='<?php echo esc_attr($key); ?>'>
                            <?php foreach ($values as $k => $v) : ?>
                                <option value='<?php echo esc_attr($k); ?>'><?php echo esc_attr($v); ?></option>
                            <?php endforeach; ?>
                            </optgroup>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </span>
            <span style='width: 30%; display: flex; align-items: center;' class=''>
					<div class="lwc-currency-wrap">
						<span class='lwc-currency'><?php echo get_woocommerce_currency_symbol(); ?></span>
						<input type='text' name='dynamic_pricing[0][adjustment][amount]' value=''>
					</div>
                <?php echo ldp_price_adjustment_help_tip(); ?>
            </span>
        <?php endif; ?>
    </div>
</div>

</p>
