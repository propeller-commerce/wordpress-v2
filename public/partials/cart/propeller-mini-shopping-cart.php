<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;
use Propeller\Includes\Controller\UserController;
use Propeller\PropellerHelper;

?>
<svg style="display:none">
    <symbol viewBox="0 0 36 32" id="shape-header-shopping-cart">
        <title><?php echo esc_html(__('Shopping cart', 'propeller-ecommerce-v2')); ?></title>
        <path d="M29 32a4 4 0 0 0 2.788-6.867A1.5 1.5 0 0 0 30.333 24H11.83l-.75-4h20.711a1.5 1.5 0 0 0 1.469-1.194l2.708-13A1.5 1.5 0 0 0 34.499 4H8.08l-.52-2.776A1.5 1.5 0 0 0 6.084 0H.75A.75.75 0 0 0 0 .75v.5c0 .414.336.75.75.75h4.92l4.37 23.31A4 4 0 1 0 17 28v-.005c0-.725-.197-1.41-.536-1.995h9.072a3.96 3.96 0 0 0-.533 1.991L25 28a4 4 0 0 0 4 4zm2.385-14h-20.68L8.455 6h25.43l-2.5 12zM13 30c-1.103 0-2-.897-2-2s.897-2 2-2 2 .897 2 2-.897 2-2 2zm16 0c-1.103 0-2-.897-2-2s.897-2 2-2 2 .897 2 2-.897 2-2 2z" />
    </symbol>
</svg>
<div class="propeller-mini-header-buttons propeller-mini-shoping-cart dropdown" id="propel_mini_cart">
    <a class="btn-header-shopping-cart" href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::SHOPPING_CART_PAGE))); ?>" id="header-button-shoppingcart">
        <span class="cart-icon">
            <svg class="icon icon-shopping-cart">
                <use class="header-shape-shopping-cart" xlink:href="#shape-header-shopping-cart"></use>
            </svg>
            <span class="badge"><?php echo esc_html((int) $this->get_items_count()); ?></span>
        </span>
        <div class="cart-label">
            <span class="cart-total"><span class="symbol"><?php echo wp_kses_post(PropellerHelper::currency()); ?>&nbsp; </span>
                <span class="propel-mini-cart-total-price">
                    <?php if ($this->get_items_count() > 0) {
                        if ($this->get_total_price() > 0)
                            echo esc_html(PropellerHelper::formatPrice($this->get_total_price()));
                        else
                            echo esc_html(PropellerHelper::formatPrice(0, 00));
                    } else echo esc_html(PropellerHelper::formatPrice(0, 00)); ?>
                </span>
            </span>
        </div>
    </a>
</div>