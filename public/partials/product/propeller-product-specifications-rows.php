<?php
if (! defined('ABSPATH')) exit;
if ($product->has_attributes()) {
    foreach ($product->get_attributes() as $attribute) {
        if (($attribute->get_type() == \Propeller\Includes\Object\Attribute::ATTR_TEXT ||
            $attribute->get_type() == \Propeller\Includes\Object\Attribute::ATTR_ENUM ||
            $attribute->get_type() == \Propeller\Includes\Object\Attribute::ATTR_INTEGER ||
            $attribute->get_type() == \Propeller\Includes\Object\Attribute::ATTR_DECIMAL) && $attribute->has_value()) { ?>
            <div class="row g-0 product-specs">
                <div class="col-sm-6">
                    <?php echo esc_attr($attribute->get_description()); ?>
                </div>
                <div class="col-6">
                    <?php echo esc_attr($attribute->get_value()); ?>
                </div>
            </div>
        <?php
        } else if (($attribute->get_type() == \Propeller\Includes\Object\Attribute::ATTR_DATETIME) && $attribute->has_value()) { ?>
            <div class="row g-0 product-specs">
                <div class="col-sm-6">
                    <?php echo esc_attr($attribute->get_description()); ?>
                </div>
                <div class="col-6">
                    <?php
                    $date = \Propeller\PropellerHelper::parse_localized_date($attribute->get_value());
                    echo esc_html($date->format(PROPELLER_DATE_FORMAT));
                    ?>
                </div>
            </div>
        <?php
        }
    }
}


if ($product->attributeItems->itemsFound > 0) {
    $current_page = isset($product->current_page) ? $product->current_page : 1;
    $items_per_page = isset($product->items_per_page) ? $product->items_per_page : 1000;
    $total_items = $product->attributeItems->itemsFound;
    $items_shown_so_far = $current_page * $items_per_page;


    if ($total_items > $items_shown_so_far) {
        $next_page = $current_page + 1;
        ?>
        <div class="row g-0 mt-5 show-more-container">
            <div class="col-12 text-center">
                <a href="#" class="load-attributes" data-page="<?php echo esc_attr($next_page); ?>" data-offset="<?php echo esc_attr($items_per_page); ?>" data-tab="specifications" data-id="<?php echo esc_attr($product->productId); ?>">
                    <?php echo esc_html(__("Show more", 'propeller-ecommerce-v2')); ?>
                </a>
            </div>
        </div>
<?php
    }
}
