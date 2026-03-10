<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;
use Propeller\PropellerHelper;

?>
<svg style="display:none">
    <symbol viewBox="0 0 23 21" id="shape-header-checkout-shopping-cart">
        <title><?php echo esc_html(__('Shopping cart', 'propeller-ecommerce-v2')); ?></title>
        <path d="M21.562 3H5.05l-.325-1.735A.938.938 0 0 0 3.803.5H.47A.469.469 0 0 0 0 .969v.312c0 .26.21.469.469.469h3.075l2.731 14.568A2.5 2.5 0 1 0 10.625 18v-.003c0-.453-.123-.88-.335-1.247h5.67a2.475 2.475 0 0 0-.333 1.245l-.002.005a2.5 2.5 0 1 0 4.243-1.792.938.938 0 0 0-.91-.708H7.394L6.925 13H19.87a.938.938 0 0 0 .917-.746l1.693-8.125A.938.938 0 0 0 21.562 3zM9.375 18a1.25 1.25 0 1 1-2.5 0 1.25 1.25 0 0 1 2.5 0zm8.75 1.25a1.25 1.25 0 1 1 0-2.5 1.25 1.25 0 0 1 0 2.5zm1.49-7.5H6.691l-1.407-7.5h15.894l-1.563 7.5z" />
    </symbol>
</svg>
<div class="propeller-mini-header-checkout-cart">
    <a class="btn-header-checkout-shopping-cart" href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::SHOPPING_CART_PAGE))); ?>" id="header-button-shoppingcart">
        <span class="cart-icon">
            <svg class="icon icon-shopping-cart">
                <use class="header-shape-shopping-cart" xlink:href="#shape-header-checkout-shopping-cart"></use>
            </svg>
        </span>
        <span class="cart-label d-none d-md-flex">
            <span class="cart-title"><?php echo esc_html(__('Shopping cart', 'propeller-ecommerce-v2')); ?></span>
            <span class="cart-total"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                <span class="propel-mini-cart-total-price"><?php echo esc_html((int) $this->get_items_count() > 0 ? PropellerHelper::formatPrice($this->get_total_price()) : PropellerHelper::formatPrice(0, 00)); ?></span>
            </span>
        </span>
    </a>
</div>