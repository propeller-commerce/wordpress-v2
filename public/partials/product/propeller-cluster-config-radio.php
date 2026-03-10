<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="col-12 px-0 cluster-config-details cluster-radio-options">
    <h3>
        <?php echo esc_html($option->name); ?>
    </h3>

    <?php
    usort(
        $option->options,
        fn($a, $b) =>
        is_numeric($a->value) && is_numeric($b->value)
            ? $a->value <=> $b->value
            : strcmp((string)$a->value, (string)$b->value)
    );
    foreach ($option->options as $config_option) {
        if (empty($config_option->value))
            continue; ?>
        <div class="form-check">
            <input class="form-check-input cluster-config-radio"
                type="radio"
                name="<?php echo esc_attr($option->setting_name); ?>"
                id="opt_<?php echo esc_attr($config_option->value); ?>"
                value="<?php echo esc_attr($config_option->value); ?>"
                data-cluster_id="<?php echo esc_attr($cluster->clusterId); ?>"
                data-slug="<?php echo esc_attr($cluster->get_slug()); ?>"
                data-description="<?php echo esc_html($cluster->name[0]->value); ?>"
                <?php echo esc_html($option->selected == $config_option->value && empty($config_option->disabled) ? 'checked' : ''); ?>
                <?php echo esc_attr($config_option->disabled); ?>>
            <label class="form-check-label <?php echo esc_attr($config_option->disabled); ?>" for="opt_<?php echo esc_attr($config_option->value); ?>">
                <?php echo esc_html($config_option->value); ?>
            </label>
        </div>
    <?php } ?>
</div>