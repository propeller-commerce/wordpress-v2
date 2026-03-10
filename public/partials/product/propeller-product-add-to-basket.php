<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="col add-to-basket pl-30">
    <?php

    use Propeller\Includes\Controller\PageController;
    use Propeller\Includes\Controller\UserController;
    use Propeller\Includes\Enum\PageType;

    if (!UserController::is_propeller_logged_in() && PROPELLER_WP_SEMICLOSED_PORTAL) { ?>
        <a class="btn btn-addtobasket d-flex align-items-center justify-content-center" href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::LOGIN_PAGE))); ?>">
            <svg class="d-flex d-md-none icon icon-cart" aria-hidden="true">
                <use xlink:href="#shape-shopping-cart"></use>
            </svg>
            <span class="d-none d-md-flex text"><?php echo esc_html(__('Order', 'propeller-ecommerce-v2')); ?></span>
        </a>

    <?php } else { ?>

        <form class="add-to-basket-form d-flex" name="add-product" action="#" method="post">
            <?php $minQuantity = $product->minimumQuantity;
            if ($product->unit >= $product->minimumQuantity)
                $minQuantity = $product->unit;
            ?>
            <input type="hidden" name="product_id" value="<?php echo esc_attr($product->productId); ?>">
            <input type="hidden" name="action" value="cart_add_item">
            <div class="input-group product-quantity align-items-center">
                <label class="visually-hidden" for="quantity-item-<?php echo esc_html($product->productId); ?>"> <?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?></label>
                <span class="input-group-text incr-decr">
                    <button type="button" class="btn-quantity"
                        data-type="minus">-</button>
                </span>
                <input
                    type="number"
                    ondrop="return false;"
                    onpaste="return false;"
                    onkeypress="return event.charCode>=48 && event.charCode<=57"
                    id="quantity-item-<?php echo esc_html($product->productId); ?>"
                    class="quantity large form-control input-number product-quantity-input"
                    name="quantity"
                    autocomplete="off"
                    min="<?php echo esc_attr($minQuantity); ?>"
                    value="<?php echo esc_attr($minQuantity); ?>"
                    data-min="<?php echo esc_attr($minQuantity); ?>"
                    data-unit="<?php echo esc_attr($product->unit); ?>"
                    <?php if (PROPELLER_STOCK_CHECK) { ?>
                    data-stock="<?php echo esc_attr($product->inventory->totalQuantity); ?>"
                    <?php } ?>>
                <span class="input-group-text incr-decr">
                    <button type="button" class="btn-quantity" data-type="plus">+</button>
                </span>
            </div>
            <button class="btn-addtobasket d-flex justify-content-center align-items-center" type="submit">
                <?php echo esc_html(__('In cart', 'propeller-ecommerce-v2')); ?>
            </button>
        </form>

    <?php } ?>
</div>