<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if (!$filter->attributeDescription || !is_array($filter->attributeDescription->descriptions) || !count($filter->attributeDescription->descriptions)) {
    return;
}

$attr_lang = array_filter($filter->attributeDescription->descriptions, function ($obj) {
    return $obj->language == PROPELLER_LANG;
});

$attr_description = '';

if (count($attr_lang))
    $attr_description = current($attr_lang)->value;
else
    $attr_description = $filter->attributeDescription->descriptions[0]->value;

$has_valid_options = false;
if (!empty($filter->textFilters)) {
    foreach ($filter->textFilters as $vals) {
        if ($vals->value != '') {
            $count = $vals->count;

            if (isset($_REQUEST['active_filter']) && $_REQUEST['active_filter'] == $filter->attributeDescription->name) {
                if ($vals->countActive > 0)
                    $count = $vals->countActive;
            } else if ($vals->count == 0 && $vals->countActive > 0)
                $count = $vals->countActive;

            if ($count > 0) {
                $has_valid_options = true;
                break;
            }
        }
    }
}

if (!$has_valid_options) {
    return;
}

?>
<div class="filter" id="<?php echo esc_attr($filter->id); ?>">
    <button class="btn-filter" type="button" href="#filtersForm_<?php echo esc_attr($filter->id); ?>" data-bs-toggle="collapse" aria-expanded="<?php echo esc_attr($expanded ? 'true' : 'false'); ?>" aria-controls="filterForm_<?php echo esc_attr($filter->id); ?>">
        <span><?php echo esc_html($attr_description); ?></span>
    </button>

    <div class="text-filter collapse <?php echo esc_html((bool) $expanded ? 'show' : ''); ?>" id="filtersForm_<?php echo esc_attr($filter->id); ?>">
        <form method="get" class="filterForm" id="filterForm_<?php echo esc_attr($filter->id); ?>">
            <input type="hidden" name="prop_value" value="<?php echo esc_attr($this->slug); ?>" />
            <input type="hidden" name="prop_name" value="<?php echo esc_attr($this->prop); ?>" />
            <input type="hidden" name="action" value="<?php echo esc_attr($this->action); ?>" />

            <?php
            sort($filter->textFilters);
            $type = $filter->type;

            foreach ($filter->textFilters as $vals) {
                if ($vals->value != '') {
                    $checked = '';
                    $count = $vals->count;
                    $vals->value = trim($vals->value);

                    if (isset($_REQUEST[$filter->attributeDescription->name])) {

                        $filter_vals = $_REQUEST[$filter->attributeDescription->name];

                        for ($i = 0; $i < count($filter_vals); $i++) {
                            if (!is_array($filter_vals[$i]))
                                $filter_vals[$i] = wp_unslash(rawurldecode($filter_vals[$i]));
                            else
                                $filter_vals[$i] = wp_unslash(rawurldecode($filter_vals[$i][0]));
                        }

                        if (in_array($vals->value, $filter_vals)) {
                            $checked = 'checked';
                        }
                    }

                    if (isset($_REQUEST['active_filter']) && $_REQUEST['active_filter'] == $filter->attributeDescription->name) {
                        if ($vals->countActive > 0)
                            $count = $vals->countActive;
                    } else if ($vals->count == 0 && $vals->countActive > 0)
                        $count = $vals->countActive;

                    if ($count == 0)
                        continue;
            ?>
                    <div class="form-check ">
                        <input
                            type="checkbox"
                            data-id="<?php echo esc_attr($filter->id); ?>"
                            data-type="<?php echo esc_attr($filter->type); ?>"
                            name="<?php echo esc_attr($filter->attributeDescription->name); ?>[]"
                            class="form-check-input styled-checkbox"
                            id="filterForm_<?php echo esc_attr($filter->id); ?>_<?php echo esc_html($vals->value); ?>"
                            value="<?php echo esc_html($vals->value); ?>"
                            <?php echo esc_attr((string) $checked); ?>>
                        <label for="filterForm_<?php echo esc_attr($filter->id); ?>_<?php echo esc_attr($vals->value); ?>" title="<?php echo esc_attr($vals->value); ?>"
                            class="form-check-label"><span class="value"><?php echo esc_html($vals->value); ?></span>

                            <span class="totals"> (<span class="filter-count"><?php echo esc_html($count); ?></span>)</span>
                        </label>
                    </div>
            <?php }
            } ?>
        </form>
    </div>
</div>