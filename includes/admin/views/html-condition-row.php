<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

$condition = lwc_get_condition($wp_condition->condition);

?>
<div class='lwc-condition-wrap'>

    <!-- Condition -->
    <span class='lwc-condition-field-wrap'>
        <?php

        $condition_field_args = array(
            'type' => 'select',
            'name' => 'conditions[' . absint($wp_condition->group) . '][' . absint($wp_condition->id) . '][condition]',
            'class' => array('lwc-condition'),
            'options' => $wp_condition->get_conditions(),
            'value' => $wp_condition->condition,
            'custom_attr' => array(
                'data-id' => absint($wp_condition->id),
            ),
        );
        lwc_html_field($condition_field_args);

        ?>
    </span>

    <!-- Operator -->
    <span class='lwc-operator-field-wrap'>
        <?php
        $operator_field_args = array(
            'type' => 'select',
            'name' => 'conditions[' . absint($wp_condition->group) . '][' . absint($wp_condition->id) . '][operator]',
            'class' => array('lwc-operator'),
            'options' => $wp_condition->get_operators(),
            'value' => $wp_condition->operator,
        );
        lwc_html_field($operator_field_args);
        ?>
    </span>

    <!-- Value -->
    <span class='lwc-value-field-wrap'>
        <?php
        $value_field_args = wp_parse_args(array('value' => $wp_condition->value), $wp_condition->get_value_field_args());
        lwc_html_field($value_field_args);
        ?>
    </span>

    <!-- Delete-->
    <a class='button lwc-condition-delete' href='javascript:void(0);'></a>
    <?php if ($desc = $wp_condition->get_description()) :?>
        <span class='lwc-description'>
        <span class="woocommerce-help-tip" data-tip="<?php echo wp_kses_post($desc); ?>"></span>
        </span>
    <?php else :?>
        <span class='lwc-description lwc-no-description'>
    <?php endif; ?>
</div>
