<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\UserController;
use Propeller\PropellerHelper;

?>
<?php if (!(!UserController::is_propeller_logged_in() && PROPELLER_WP_SEMICLOSED_PORTAL)) { ?>
    <div class="filter" id="price_filter_container">
        <button class="btn-filter" type="button" href="#filterForm_prices" data-bs-toggle="collapse" aria-expanded="<?php echo esc_attr($expanded ? 'true' : 'false'); ?>" aria-controls="filterForm_prices">
            <span><?php echo esc_html(__('Price', 'propeller-ecommerce-v2')); ?></span>
        </button>
        <div class="numeric-filter collapse <?php echo esc_html((bool) $expanded ? 'show' : ''); ?>" id="filterForm_prices">
            <form method="get" class="filterForm filterFormNumeric">
                <input type="hidden" name="prop_value" value="<?php echo esc_attr($this->slug); ?>" />
                <input type="hidden" name="prop_name" value="<?php echo esc_attr($this->prop); ?>" />
                <input type="hidden" name="action" value="<?php echo esc_attr($this->action); ?>" />
                <div class="slider-container">
                    <div id="price_slider" class="slider" data-prop_value="<?php echo esc_attr($this->slug); ?>" data-prop_name="<?php echo esc_attr($this->prop); ?>" data-action="<?php echo esc_attr($this->action); ?>" data-min="<?php echo esc_attr($filter->min); ?>" data-max="<?php echo esc_attr($filter->max); ?>"></div>
                </div>
                <div class="input-wrapper">
                    <div class="input-group min">
                        <div class="input-group-text"><span><?php echo esc_html(PropellerHelper::currency()); ?></span></div>
                        <input type="number" name="price[from]" ondrop="return false;" onpaste="return false;" onkeypress="return event.charCode>=48 && event.charCode<=57" value="<?php echo esc_attr($filter->min); ?>" class="form-control form-control-sm numeric-min" data-min="<?php echo esc_attr($filter->min); ?>" min="<?php echo esc_attr($filter->min); ?>" max="<?php echo esc_attr($filter->max); ?>">
                    </div>
                    <div class="price-tot"><span><?php echo esc_html(__('from', 'propeller-ecommerce-v2')); ?></span></div>
                    <div class="input-group max">
                        <div class="input-group-text"><span><?php echo esc_html(PropellerHelper::currency()); ?></span></div>
                        <input type="number" name="price[to]" ondrop="return false;" onpaste="return false;" onkeypress="return event.charCode>=48 && event.charCode<=57" value="<?php echo esc_attr($filter->max); ?>" class="form-control form-control-sm numeric-max" data-max="<?php echo esc_attr($filter->max); ?>" min="<?php echo esc_attr($filter->min); ?>" max="<?php echo esc_attr($filter->max); ?>">
                    </div>
                </div>

            </form>
        </div>

    </div>
<?php } ?>
