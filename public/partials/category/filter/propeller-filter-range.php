<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$attr_lang = array_filter($filter->attributeDescription->descriptions, function($obj) {
    return $obj->language == PROPELLER_LANG;
});

$attr_description = '';

if (count($attr_lang))
    $attr_description = current($attr_lang)->value;
else 
    $attr_description = $filter->attributeDescription->descriptions[0]->value;

?>
<div class="filter" id="<?php echo esc_attr($filter->id); ?>">
    <button class="btn-filter" type="button" href="#filtersForm_<?php echo esc_attr($filter->id); ?>" data-bs-toggle="collapse" aria-expanded="<?php echo esc_attr($expanded ? 'true': 'false'); ?>" aria-controls="filterForm_<?php echo esc_attr($filter->id); ?>">
        <span><?php echo esc_html($attr_description); ?></span>
    </button>  

    <div class="numeric-filter collapse <?php echo esc_html( (bool) $expanded ? 'show': '' ); ?>" id="filtersForm_<?php echo esc_attr($filter->id); ?>">
        <form method="get" class="filterForm filterFormNumeric" id="filterForm_<?php echo esc_attr($filter->id); ?>">
            <input type="hidden" name="prop_value" value="<?php echo esc_attr($this->slug); ?>" />
            <input type="hidden" name="prop_name" value="<?php echo esc_attr($this->prop); ?>" />
            <input type="hidden" name="action" value="<?php echo esc_attr($this->action); ?>" />

            <div class="slider-container">
                <div id="<?php echo esc_attr($filter->id); ?>_slider" class="slider" data-prop_value="<?php echo esc_attr($this->slug); ?>" data-prop_name="<?php echo esc_attr($this->prop); ?>" data-action="<?php echo esc_attr($this->action); ?>" data-min="<?php echo esc_attr($filter->decimalRangeFilter->min); ?>" data-max="<?php echo esc_attr($filter->decimalRangeFilter->max); ?>"></div>
            </div>

            <div class="input-wrapper">
                <div class="input-group min">
                    <input type="number" name="<?php echo esc_attr($filter->attributeDescription->name); ?>[from]" ondrop="return false;" onpaste="return false;" onkeypress="return event.charCode>=48 && event.charCode<=57" value="<?php echo esc_attr($filter->decimalRangeFilter->min); ?>" class="form-control form-control-sm numeric-min" data-min="<?php echo esc_attr($filter->decimalRangeFilter->min); ?>" min="<?php echo esc_attr($filter->decimalRangeFilter->min); ?>" max="<?php echo esc_attr($filter->decimalRangeFilter->max); ?>">
                </div>
                <div class="price-tot"><span><?php echo esc_html(__('from', 'propeller-ecommerce-v2')); ?></span></div>
                <div class="input-group max">
                    <input type="number" name="<?php echo esc_attr($filter->attributeDescription->name); ?>[to]" ondrop="return false;" onpaste="return false;" onkeypress="return event.charCode>=48 && event.charCode<=57" value="<?php echo esc_attr($filter->decimalRangeFilter->max); ?>" class="form-control form-control-sm numeric-max" data-max="<?php echo esc_attr($filter->decimalRangeFilter->max); ?>" min="<?php echo esc_attr($filter->decimalRangeFilter->min); ?>" max="<?php echo esc_attr($filter->decimalRangeFilter->max); ?>">
                </div>
            </div>

        </form>
    </div>

</div>