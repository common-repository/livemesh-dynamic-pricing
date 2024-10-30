<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

?>
<div class='ldp-bulk-pricing-settings'>
    <div class='head'>
        <span class='ldp-bulk-row-heading'><strong><label
                        for='message'><?php _e('Min. quantity', 'livemesh-dynamic-pricing'); ?></label></strong>
        </span>
        <span class='ldp-bulk-row-heading'>
            <strong><label for='message'><?php _e('Max. quantity', 'livemesh-dynamic-pricing'); ?></label></strong>
        </span>
        <span class='ldp-bulk-row-heading'>
			<strong><label for='message'><?php _e('Adjustment', 'livemesh-dynamic-pricing'); ?></label></strong>
        </span>
        <span class='ldp-bulk-row-heading'>
			<strong><label for='message'><?php _e('Amount', 'livemesh-dynamic-pricing'); ?></label></strong>
            <?php echo ldp_price_adjustment_help_tip(); ?>
        </span>
    </div>
    <div class='ldp-bulk-pricing-wrapper'>
        <?php
        if ($dynamic_pricings) :
            $i = 0;
            foreach ($dynamic_pricings as $key => $dynamic_pricing) :

                $min = isset($dynamic_pricing['condition']['min']) ? $dynamic_pricing['condition']['min'] : '';
                $max = isset($dynamic_pricing['condition']['max']) ? $dynamic_pricing['condition']['max'] : '';
                $adjustment_type = isset($dynamic_pricing['adjustment']['type']) ? $dynamic_pricing['adjustment']['type'] : 'discount_amount';
                $amount = isset($dynamic_pricing['adjustment']['amount']) ? $dynamic_pricing['adjustment']['amount'] : '';

                require plugin_dir_path(__FILE__) . 'bulk-pricing-row.php';

                $i++;
            endforeach;
        else :
            $i = 0;
            $min = $max = $amount = '';
            $adjustment_type = 'discount_amount'; // default value

            require plugin_dir_path(__FILE__) . 'bulk-pricing-row.php';

        endif;
        ?>
    </div>

    <div class="clearfix">
        <a class="button ldp-bulk-row-add" href="javascript:void(0);">Add row</a>
    </div>
    <div class="ldp-bulk-row-template hidden">
        <?php
        $i = 9999;
        $min = $max = $amount = '';
        $adjustment_type = 'discount_amount'; // default value

        require plugin_dir_path(__FILE__) . 'bulk-pricing-row.php';
        ?>
    </div>
</div>
