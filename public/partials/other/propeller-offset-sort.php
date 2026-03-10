<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\UserController;

if (!(!UserController::is_propeller_logged_in() && PROPELLER_WP_SEMICLOSED_PORTAL)) {
    $obj->sort_arr = [
        "LAST_MODIFIED_AT" => __('Date changed', 'propeller-ecommerce-v2'),
        "CREATED_AT" => __('Date created', 'propeller-ecommerce-v2'),
        "NAME" => __('Name', 'propeller-ecommerce-v2'),
        "PRICE" => __('Price', 'propeller-ecommerce-v2'),
        "RELEVANCE" => __('Relevance', 'propeller-ecommerce-v2'),
        "SKU" => __('SKU', 'propeller-ecommerce-v2'),
        "SUPPLIER_CODE" => __('Supplier code', 'propeller-ecommerce-v2'),
        "PRIORITY" => __('Priority', 'propeller-ecommerce-v2'),
    ];
} else {
    $obj->sort_arr = [
        "LAST_MODIFIED_AT" => __('Date changed', 'propeller-ecommerce-v2'),
        "CREATED_AT" => __('Date created', 'propeller-ecommerce-v2'),
        "NAME" => __('Name', 'propeller-ecommerce-v2'),
        "RELEVANCE" => __('Relevance', 'propeller-ecommerce-v2'),
        "SKU" => __('SKU', 'propeller-ecommerce-v2'),
        "SUPPLIER_CODE" => __('Supplier code', 'propeller-ecommerce-v2'),
        "PRIORITY" => __('Priority', 'propeller-ecommerce-v2'),
    ];
}

$obj->sort_order = [
    "ASC" => __('Asc', 'propeller-ecommerce-v2'),
    "DESC" => __('Desc', 'propeller-ecommerce-v2'),
];

$obj->offset_arr = [12, 24, 48];

if (is_array($sort))
    $sort = implode(',', $sort);

?>
<div class="col-auto d-none d-sm-flex align-items-center catalog-offset">
    <label class="label"><?php echo esc_html(__('Show per page', 'propeller-ecommerce-v2')); ?></label>
    <div class="dropdown sticky-dropdown-menu">
        <select name="catalog-offset" class="form-control" data-prop_name="<?php echo esc_attr($prop_name); ?>" data-prop_value="<?php echo esc_attr($prop_value); ?>" data-action="<?php echo esc_attr($do_action); ?>" data-liststyle="<?php echo esc_attr($obj->filters->get_liststyle()); ?>" data-obid="<?php echo esc_attr($obid); ?>">
            <?php foreach ($obj->offset_arr as $o) { ?>
                <?php
                $selected = '';

                if (isset($_REQUEST['offset']))
                    $selected = (int) $_REQUEST['offset'] == (int) $o ? 'selected' : '';
                else
                    $selected = (int) $o == (int) PROPELLER_DEFAULT_OFFSET ? 'selected' : '';


                ?>
                <option value="<?php echo esc_attr($o); ?>" <?php echo esc_html($selected); ?>><?php echo esc_html($o); ?></option>
            <?php } ?>
        </select>
    </div>
</div>
<div class="col-auto d-flex align-items-center catalog-sort">
    <label class="label"><?php echo esc_html(__('Sort by', 'propeller-ecommerce-v2')); ?></label>
    <div class="dropdown sticky-dropdown-menu">
        <select name="catalog-sort" class="form-control" data-prop_name="<?php echo esc_attr($prop_name); ?>" data-prop_value="<?php echo esc_attr($prop_value); ?>" data-action="<?php echo esc_attr($do_action); ?>" data-obid="<?php echo esc_attr($obid); ?>" data-liststyle="<?php echo esc_attr($obj->filters->get_liststyle()); ?>">
            <option value="default" <?php echo esc_html(($sort == 'default') ? 'selected' : ''); ?>><?php echo esc_html(__("Default sorting", 'propeller-ecommerce-v2')); ?></option>
            <?php foreach ($obj->sort_arr as $sort_key => $sort_val) { ?>
                <?php foreach ($obj->sort_order as $order_key => $order_val) { ?>
                    <?php /* translators: %s, %s: sorting field, sorting direction */ ?>
                    <option value="<?php echo esc_attr($sort_key . ',' . $order_key); ?>" <?php echo esc_attr(($sort == $sort_key . ',' . $order_key) ? 'selected' : ''); ?>><?php echo esc_html($sort_val . ', ' . $order_val); ?></option>
                <?php } ?>
            <?php } ?>
        </select>
    </div>
</div>