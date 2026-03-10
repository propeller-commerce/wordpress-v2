<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="col-12 px-0 cluster-config-details">
    <h3>
        <?php echo esc_html($option->name); ?>
    </h3>
    <div class="dropdown">
        <select class="cluster-config-dropdown" 
                name="<?php echo esc_attr($option->setting_name); ?>" 
                data-placeholder="<?php echo esc_attr( __('Select an option', 'propeller-ecommerce-v2') ); ?>" 
                id="<?php echo esc_attr($option->setting_name); ?>" 
                data-cluster_id="<?php echo esc_attr($cluster->clusterId); ?>" 
                data-slug="<?php echo esc_attr($cluster->get_slug()); ?>" 
                required>
            <?php
                foreach ($option->options as $config_option) {
                    $selected = $config_option->value == $option->selected ? 'selected' : '';
            ?>
                <option value="<?php echo esc_attr($config_option->value); ?>" <?php echo esc_attr($selected); ?> <?php echo esc_attr($config_option->disabled ? 'disabled' : ''); ?>>
                    <?php echo esc_html($config_option->value); ?>
                </option>
            <?php } ?>
        </select>

    </div>
</div>