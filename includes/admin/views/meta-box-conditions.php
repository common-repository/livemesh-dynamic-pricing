<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

global $post;
$condition_groups = get_post_meta($post->ID, '_pricing_conditions', true);

?>

<div class="lwc-conditions lwc-conditions-meta-box">
    <div class='lwc-condition-groups'>
        <p>
            <strong><?php _e('Match one of the condition groups to apply the price adjustment', 'livemesh-dynamic-pricing'); ?></strong>
        </p>
        <?php if (!empty($condition_groups)) : ?>
            <?php foreach ($condition_groups as $condition_group => $conditions) : ?>
                <?php include 'html-condition-group.php'; ?>
            <?php endforeach; ?>
        <?php else : ?>
            <?php
            $condition_group = '0';
            include 'html-condition-group.php';
            ?>
        <?php endif; ?>
    </div>
    <div class='lwc-condition-group-template hidden' style='display: none'>
        <?php
        $condition_group = '9999';
        $conditions = array();
        include 'html-condition-group.php';
        ?>
    </div>
    <a class='button lwc-condition-group-add'
       href='javascript:void(0);'><?php _e('Add \'Or\' group', 'livemesh-dynamic-pricing'); ?></a>
</div>