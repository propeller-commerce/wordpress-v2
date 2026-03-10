<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="col add-to-basket pl-30">
    <?php

    use Propeller\Includes\Controller\PageController;
    use Propeller\Includes\Controller\UserController;
    use Propeller\Includes\Enum\PageType;

    if (!UserController::is_propeller_logged_in()) { ?>
        <a class="btn btn-addtobasket d-flex align-items-center justify-content-center" href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::LOGIN_PAGE))); ?>">
            <span class="d-flex text"><?php echo esc_html( __('Request a price', 'propeller-ecommerce-v2') ); ?></span>
        </a>

    <?php } else { ?>
        <a href="#" class="btn-addtobasket d-flex justify-content-center align-items-center btn-price-request" data-id="<?php echo esc_attr($product->productId); ?>" data-code="<?php echo esc_attr($product->sku); ?>" data-name="<?php echo esc_attr($product->name[0]->value); ?>" data-quantity="<?php echo esc_attr($product->minimumQuantity); ?>" data-minquantity="<?php echo esc_attr($product->minimumQuantity); ?>" data-unit="<?php echo esc_attr($product->unit); ?>">
            <?php echo esc_html( __('Request a price', 'propeller-ecommerce-v2') ); ?>
        </a>
        <!-- <div class="price-on-request-content">
        <h6><?php echo esc_html( __('Good to know:', 'propeller-ecommerce-v2') ); ?></h6>
        <p><?php echo esc_html( __('We do not have a current price available for this product. If you would like to receive a quote, please add this item to your request list with the correct amount. When you have added the items to your price list, you can find the items in your account under the heading price list.', 'propeller-ecommerce-v2') ); ?></p>
    </div> -->

    <?php } ?>
</div>