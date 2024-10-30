<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

$pricings = get_posts(array('posts_per_page' => '-1', 'post_type' => 'ldp_dynamic_pricing', 'post_status' => array('draft', 'publish'), 'orderby' => 'menu_order', 'order' => 'ASC'));

?>
<tr valign='top'>
    <th scope='row' class='titledesc'><?php
        _e('Dynamic Pricing', 'livemesh-dynamic-pricing'); ?>:<br/>
    </th>
    <td class='forminp' id='ldp-dynamic-pricing-overview'>

        <table class='wp-list-table lwc-conditions-post-table lwc-sortable-post-table widefat'>
            <thead>
            <tr>
                <th style='width: 17px;' class="column-cb check-column"></th>
                <th style='padding-left: 10px;'
                    class="column-primary"><?php _e('Title', 'livemesh-dynamic-pricing'); ?></th>
                <th style='padding-left: 10px;'><?php _e('Adjustment type', 'livemesh-dynamic-pricing'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php $i = 0; ?>
            <?php foreach ($pricings as $pricing) : ?>
                <?php $alt = ($i++) % 2 == 0 ? 'alternate' : ''; ?>
                <tr class='<?php echo esc_attr($alt); ?>'>
                    <th class='sort check-column'>
                        <input type='hidden' name='sort[]' value='<?php echo esc_attr($pricing->ID); ?>'/>
                    </th>
                    <td class="column-primary">
                        <strong>
                            <a href='<?php echo get_edit_post_link($pricing->ID); ?>' class='row-title'
                               title='<?php _e('Edit Pricing', 'livemesh-dynamic-pricing'); ?>'>
                                <?php echo _draft_or_post_title($pricing->ID); ?>
                            </a>
                            <?php echo _post_states($pricing); ?>
                        </strong>
                        <div class='row-actions'>
								<span class='edit'>
									<a href='<?php echo get_edit_post_link($pricing->ID); ?>'
                                       title='<?php _e('Edit Pricing', 'livemesh-dynamic-pricing'); ?>'>
										<?php _e('Edit', 'livemesh-dynamic-pricing'); ?>
									</a>
									|
								</span>
                            <span class='trash'>
									<a href='<?php echo get_delete_post_link($pricing->ID); ?>'
                                       title='<?php _e('Delete Pricing', 'livemesh-dynamic-pricing'); ?>'><?php
                                        _e('Delete', 'livemesh-dynamic-pricing');
                                        ?></a>
								</span>
                        </div>
                    </td>
                    <td>
                        <?php
                        $pricing_method = get_post_meta($pricing->ID, '_pricing_method', true);
                        $pricing_methods = apply_filters('ldp_pricing_methods', array(
                            'adjustment' => __('Simple Adjustment', 'livemesh-dynamic-pricing'),
                            'bulk' => __('Bulk pricing', 'livemesh-dynamic-pricing'),
                        ));
                        echo isset($pricing_methods[$pricing_method]) ? esc_html($pricing_methods[$pricing_method]) : '';
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($pricings)) : ?>
                <tr>
                    <td colspan='2'
                        style="display: table-cell;"><?php _e('There are no Dynamic Price rules. Yet...', 'livemesh-dynamic-pricing'); ?></td>
                </tr>
            <?php endif; ?>
            </tbody>
            <tfoot>
            <tr>
                <th colspan='4' style='padding-left: 10px; display: table-cell;'>
                    <a href='<?php echo admin_url('post-new.php?post_type=ldp_dynamic_pricing'); ?>'
                       class='add button'><?php _e('Add Dynamic Pricing', 'livemesh-dynamic-pricing'); ?></a>
                </th>
            </tr>
            </tfoot>
        </table>
    </td>
    <?php if (ldp_fs()->is_not_paying()) : ?>
        <section class="lds-upgrade-notice">
            <a href="<?php echo ldp_fs()->get_upgrade_url(); ?>"><?php echo __('Upgrade to the Premium Version!', 'livemesh-dynamic-pricing'); ?></a>
        </section>
    <?php endif; ?>
</tr>
