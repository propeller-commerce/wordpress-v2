<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Enum\PageType;
use Propeller\PropellerHelper;

$shoppingCart = new Propeller\Includes\Controller\ShoppingCartController();
$user_prices = SessionController::get(PROPELLER_SPECIFIC_PRICES);

?>
<svg style="display:none">
    <symbol viewBox="0 0 16 12" id="shape-header-checkmark">
        <title><?php echo esc_html(__('Checkmark', 'propeller-ecommerce-v2')); ?></title>
        <path d="m6.566 11.764 9.2-9.253a.808.808 0 0 0 0-1.137L14.634.236a.797.797 0 0 0-1.131 0L6 7.782 2.497 4.259a.797.797 0 0 0-1.131 0L.234 5.397a.808.808 0 0 0 0 1.137l5.2 5.23a.797.797 0 0 0 1.132 0z" />
    </symbol>
    <symbol viewBox="0 0 14 14" id="shape-header-close">
        <title><?php echo esc_html(__('Close', 'propeller-ecommerce-v2')); ?></title>
        <path d="M13.656 12.212c.41.41.41 1.072 0 1.481a1.052 1.052 0 0 1-1.485 0L7 8.5l-5.207 5.193a1.052 1.052 0 0 1-1.485 0 1.045 1.045 0 0 1 0-1.481L5.517 7.02.307 1.788a1.045 1.045 0 0 1 0-1.481 1.052 1.052 0 0 1 1.485 0L7.001 5.54 12.208.348a1.052 1.052 0 0 1 1.485 0c.41.408.41 1.072 0 1.48L8.484 7.02l5.172 5.192z" />
    </symbol>
</svg>
<div id="add-to-basket-modal" class="propeller-add-to-basket-modal modal fade modal-fullscreen-sm-down" tabindex="-1" role="dialog" aria-labelledby="modal-title">
    <div class="modal-dialog modal-add-to-basket modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header propel-modal-header">
                <div id="modal-title" class="modal-title">
                    <svg class="icon icon-checkmark">
                        <use class="header-shape-checkmark" xlink:href="#shape-header-checkmark"></use>
                    </svg>
                    <span><?php echo esc_html(__('The item has been added to your shopping cart', 'propeller-ecommerce-v2')); ?></span>
                </div>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="<?php echo esc_html(__('Close', 'propeller-ecommerce-v2')); ?>">
                    <span aria-hidden="true">
                        <svg class="icon icon-close">
                            <use class="header-shape-close" xlink:href="#shape-header-close"></use>
                        </svg>
                    </span>
                </button>
            </div>
            <div class="modal-body propel-modal-body">
                <div class="modal-product-list">
                    <div class="row modal-product">
                        <div class="image col-2">
                            <img class="img-fluid added-item-img" src="" alt="">
                        </div>
                        <div class="details col-10 col-md-5">
                            <div class="d-inline-block product-name added-item-name"> </div>
                            <div class="added-item-prices">
                                (<span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?></span>
                                <?php if ($user_prices == false) { ?>
                                    <span class="added-item-price"></span>
                                <?php } else { ?>
                                    <span class="added-item-price added-item-priceNet"></span>
                                    <?php } ?>)
                            </div>

                            <div class="product-sku"><?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>: <span class="added-item-sku"></span></div>
                            <div class="product-surcharges">
                                <ul class="added-item-surcharges"></ul>
                            </div>
                        </div>
                        <div class="offset-2 offset-md-0 col-10 col-md-5">
                            <div class="product-price row align-items-center">
                                <span class="col-6 col-md-7 col-lg-6 ms-md-auto quantity"><?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?>:&nbsp; <span class="added-item-quantity"></span></span>
                                <div class="col-6 col-md-5 col-lg-3 d-flex justify-content-end product-item-price">
                                    <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                                    <?php if ($user_prices == false) { ?>
                                        <span class="added-total-price"></span>
                                    <?php } else { ?>
                                        <span class="added-total-price added-total-priceNet"></span>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="offset-2 col-10">
                            <div class="product-childitems">
                                <div class="added-item-childItems <?php echo esc_html(($user_prices ? 'added-item-priceNet' : '')); ?>"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row propeller-shopping-cart-wrapper align-items-end">
                    <div class="col-12 col-md-6 order-md-1 order-2">
                        <a href="/" target="_top" class="btn-continue" data-bs-dismiss="modal">
                            <?php echo esc_html(__('Continue shopping', 'propeller-ecommerce-v2')); ?>
                        </a>
                    </div>
                    <div class="col-12 col-md-6 order-md-2 order-1 modal-product-wrapper">
                        <div class="row modal-btn-wrapper">
                            <div class="col-12">
                                <a href="<?php echo esc_url($shoppingCart->buildUrl('', PageController::get_slug(PageType::SHOPPING_CART_PAGE))); ?>" class="btn-checkout">
                                    <?php echo esc_html(__('Continue to order', 'propeller-ecommerce-v2')); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>