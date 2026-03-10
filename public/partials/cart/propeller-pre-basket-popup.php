<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\PropellerHelper;

?>
<svg style="display:none">
    <symbol viewBox="0 0 21 18" id="shape-header-warning">
        <title><?php echo esc_html(__('Warning', 'propeller-ecommerce-v2')); ?></title>
        <path d="m8.745 7.197.232 3.937a.422.422 0 0 0 .42.397h1.455a.422.422 0 0 0 .421-.397l.232-3.937a.422.422 0 0 0-.421-.447H9.166a.422.422 0 0 0-.421.447zm2.857 6.303a1.477 1.477 0 1 0-2.954 0 1.477 1.477 0 0 0 2.954 0zM11.587.843c-.648-1.123-2.274-1.125-2.924 0L.228 15.47C-.42 16.592.39 18 1.689 18H18.56c1.296 0 2.11-1.406 1.462-2.53L11.587.842zM1.87 15.996 9.942 2.004a.211.211 0 0 1 .366 0l8.072 13.992a.21.21 0 0 1-.183.316H2.053a.21.21 0 0 1-.183-.316z" fill="#FFA630" />
    </symbol>
    <symbol viewBox="0 0 14 14" id="shape-header-close">
        <title><?php echo esc_html(__('Close', 'propeller-ecommerce-v2')); ?></title>
        <path d="M13.656 12.212c.41.41.41 1.072 0 1.481a1.052 1.052 0 0 1-1.485 0L7 8.5l-5.207 5.193a1.052 1.052 0 0 1-1.485 0 1.045 1.045 0 0 1 0-1.481L5.517 7.02.307 1.788a1.045 1.045 0 0 1 0-1.481 1.052 1.052 0 0 1 1.485 0L7.001 5.54 12.208.348a1.052 1.052 0 0 1 1.485 0c.41.408.41 1.072 0 1.48L8.484 7.02l5.172 5.192z" fill="#FFA630" />
    </symbol>
</svg>
<div id="add-pre-basket-modal" class="propeller-add-to-basket-modal pre-basket-modal modal fade modal-fullscreen-sm-down" tabindex="-1" role="dialog" aria-labelledby="modal-title">
    <div class="modal-dialog modal-lg modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header propel-modal-header">
                <div id="modal-title" class="modal-title">
                    <svg class="icon icon-checkmark">
                        <use class="header-shape-checkmark" xlink:href="#shape-header-warning"></use>
                    </svg>
                    <span><?php echo esc_html(__('There is not enough stock of this product.', 'propeller-ecommerce-v2')); ?></span>
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
                        <div class="details col">
                            <div class="product-name added-item-name"></div>
                            <div class="product-sku"><?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>: <span class="added-item-sku"></span></div>
                        </div>

                    </div>
                </div>
                <div class="add-pre-basket">
                    <form class="add-pre-basket-form" name="add-product-pre-basket" method="post">
                        <input type="hidden" id="quantity" name="quantity" value="">
                        <input type="hidden" name="product_id" value="">
                        <input type="hidden" id="set_pre_basket_option" name="action" value="set_pre_basket_option">
                        <div class="row product-pre-basket-availability">
                            <div class="col-12">
                                <label class="form-check-label not-enough-stock">
                                    <input class="form-check-input added-item-full-quantity enough-stock" type="radio" name="pre_basket_option" value="">
                                    <span><?php echo esc_html(__('As soon as possible, I would like', 'propeller-ecommerce-v2')); ?> <div class="d-inline added-item-stock"></div> <?php echo esc_html(__('items have delivered', 'propeller-ecommerce-v2')); ?>, <?php echo esc_html(__('the other', 'propeller-ecommerce-v2')); ?> <div class="d-inline added-item-diff"></div> <?php echo esc_html(__('items will be delivered later', 'propeller-ecommerce-v2')); ?></span>
                                </label>
                                <label class="form-check-label not-enough-stock">
                                    <input class="form-check-input added-item-full-stock" type="radio" name="pre_basket_option" value="">
                                    <span><?php echo esc_html(__('Add', 'propeller-ecommerce-v2')); ?> <div class="d-inline added-item-stock"></div> <?php echo esc_html(__('items to my shopping cart', 'propeller-ecommerce-v2')); ?>, <?php echo esc_html(__('for the other', 'propeller-ecommerce-v2')); ?> <div class="d-inline added-item-diff"></div> <?php echo esc_html(__('items I am looking for an alternative.', 'propeller-ecommerce-v2')); ?></span>
                                </label>

                                <label class="form-check-label out-of-stock">
                                    <input class="form-check-input added-item-full-quantity" type="radio" name="pre_basket_option" value="">
                                    <span><?php echo esc_html(__('Add', 'propeller-ecommerce-v2')); ?> <div class="d-inline added-item-diff"></div> <?php echo esc_html(__('items to my shopping cart and deliver them as soon as possible', 'propeller-ecommerce-v2')); ?></span>
                                </label>
                            </div>
                        </div>
                        <div class="row product-pre-basket-options">
                            <div class="col-12">
                                <label class="form-check-label">
                                    <input class="form-check-input added-item-full-quantity" type="radio" name="pre_basket_option" value="">
                                    <span><?php echo esc_html(__('As soon as possible, I would like', 'propeller-ecommerce-v2')); ?> <div class="d-inline added-item-quantity"></div> <?php echo esc_html(__('items have delivered', 'propeller-ecommerce-v2')); ?>, <?php echo esc_html(__('the others', 'propeller-ecommerce-v2')); ?> <div class="d-inline added-item-diff"></div> <?php echo esc_html(__('items will be delivered later', 'propeller-ecommerce-v2')); ?></span>
                                </label>
                                <label class="form-check-label">
                                    <input class="form-check-input added-item-full-stock" type="radio" name="pre_basket_option" value="" checked>
                                    <span><?php echo esc_html(__('Add', 'propeller-ecommerce-v2')); ?> <div class="d-inline added-item-quantity"></div> <?php echo esc_html(__('items to my shopping cart', 'propeller-ecommerce-v2')); ?>, <?php echo esc_html(__('for the other', 'propeller-ecommerce-v2')); ?> <div class="d-inline added-item-diff"></div> <?php echo esc_html(__('items I am looking for an alternative.', 'propeller-ecommerce-v2')); ?></span>
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-auto d-flex">
                                <button class="btn-modal btn-cancel" data-bs-dismiss="modal"><?php echo esc_html(__('Cancel', 'propeller-ecommerce-v2')); ?> </button>
                                <button class="btn-modal btn-proceed" type="submit"><?php echo esc_html(__('Confirm', 'propeller-ecommerce-v2')); ?> </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>