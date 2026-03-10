<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="col-12 px-0 cluster-config-details cluster-image-options">
    <h3>
        <?php echo esc_html($option->name); ?>
    </h3>
    <?php
    $count = 0;
    foreach ($option->options as $config_option) {
        if ($config_option->disabled == '')
            $count++;
    } ?>
    <?php foreach ($option->options as $config_option) { ?>
        <div class="form-check-inline">
            <input class="form-check-input btn-check cluster-config-radio"
                type="radio"
                autocomplete="off"
                name="<?php echo esc_attr($option->setting_name); ?>"
                id="opt_<?php echo esc_attr($config_option->value); ?>"
                value="<?php echo esc_attr($config_option->value); ?>"
                data-cluster_id="<?php echo esc_attr($cluster->clusterId); ?>"
                data-slug="<?php echo esc_attr($cluster->get_slug()); ?>"
                <?php echo esc_html(($option->selected == $config_option->value && empty($config_option->disabled)) || ($count == 1  && empty($config_option->disabled)) ? 'checked' : ''); ?>
                <?php echo esc_attr($config_option->disabled); ?>>
            <label class="form-check-label <?php echo esc_html(($option->selected == $config_option->value) || ($count == 1  && empty($config_option->disabled)) ? 'active' : ''); ?> <?php echo esc_attr($config_option->disabled); ?>" for="opt_<?php echo esc_attr($config_option->value); ?>">
                <img src="<?php echo esc_attr($config_option->value); ?>" class="img-fluid <?php echo esc_attr($config_option->disabled); ?>" />
            </label>
        </div>
    <?php } ?>
</div>