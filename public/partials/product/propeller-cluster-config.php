<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Enum\PageType;
use Propeller\Includes\Enum\ProductStatus;
use Propeller\PropellerHelper;

$user_prices = SessionController::get(PROPELLER_SPECIFIC_PRICES);

?>
<svg style="display:none">
    <symbol viewBox="0 0 23 21" id="shape-price-request">
        <title><?php echo esc_html(__('Shopping cart', 'propeller-ecommerce-v2')); ?></title>
        <path d="M21.562 3H5.05l-.325-1.735A.938.938 0 0 0 3.803.5H.47A.469.469 0 0 0 0 .969v.312c0 .26.21.469.469.469h3.075l2.731 14.568A2.5 2.5 0 1 0 10.625 18v-.003c0-.453-.123-.88-.335-1.247h5.67a2.475 2.475 0 0 0-.333 1.245l-.002.005a2.5 2.5 0 1 0 4.243-1.792.938.938 0 0 0-.91-.708H7.394L6.925 13H19.87a.938.938 0 0 0 .917-.746l1.693-8.125A.938.938 0 0 0 21.562 3zM9.375 18a1.25 1.25 0 1 1-2.5 0 1.25 1.25 0 0 1 2.5 0zm8.75 1.25a1.25 1.25 0 1 1 0-2.5 1.25 1.25 0 0 1 0 2.5zm1.49-7.5H6.691l-1.407-7.5h15.894l-1.563 7.5z" />
    </symbol>
</svg>
<?php if (!(!UserController::is_propeller_logged_in() && PROPELLER_WP_SEMICLOSED_PORTAL)) { ?>
    <div class="row g-0 align-items-end">
        <div class="col pe-0 add-to-basket">
            <form class="add-to-basket-form cluster-add-to-basket-form validate" name="add-product" method="post">
                <input type="hidden" name="action" value="cart_add_item">
                <input type="hidden" name="cluster_id" value="<?php echo esc_attr($cluster->urlId); ?>">
                <input type="hidden" name="product_id" value="<?php echo esc_attr($cluster->defaultProduct->productId); ?>">
                <input type="hidden" name="options" value="<?php echo esc_attr(implode(',', $cluster->selected_options)); ?>">

                <?php
                foreach ($cluster->get_config_options() as $option)
                    echo wp_kses_post(apply_filters('propel_cluster_config_' . strtolower($option->type), $option, $cluster, $obj));

                foreach ($cluster->get_options() as $option)
                    echo wp_kses_post(apply_filters('propel_cluster_config_option', $option, $cluster, $obj));
                ?>

                <?php if (!$cluster->defaultProduct->is_price_on_request()) { ?>
                    <?php if ($cluster->defaultProduct->status == ProductStatus::N) { ?>
                        <h4 class="text-danger d-flex justify-content-center align-items-center">
                            <?php echo esc_html(__('This product is currently not available', 'propeller-ecommerce-v2')); ?>
                        </h4>
                    <?php } else if ($cluster->defaultProduct->orderable == 'N') { ?>
                        <h4 class="text-danger d-flex justify-content-center align-items-center">
                            <?php echo esc_html(__('This product is currently not orderable', 'propeller-ecommerce-v2')); ?>
                        </h4>
                    <?php } else {
                        $minQuantity = $cluster->defaultProduct->minimumQuantity;
                        if ($cluster->defaultProduct->unit >= $cluster->defaultProduct->minimumQuantity)
                            $minQuantity = $cluster->defaultProduct->unit;
                    ?>
                        <div class="d-flex">
                            <div class="input-group product-quantity align-items-center">
                                <label class="visually-hidden" for="quantity-item"><?php echo esc_html(__("Quantity", 'propeller-ecommerce-v2')); ?></label>
                                <span class="input-group-text incr-decr">
                                    <button type="button" class="btn-quantity" data-type="minus">-</button>
                                </span>
                                <input type="number" ondrop="return false;" onpaste="return false;" onkeypress="return event.charCode>=48 && event.charCode<=57" id="quantity-item" class="quantity large form-control input-number product-quantity-input" name="quantity" autocomplete="off" min="<?php echo esc_attr($minQuantity); ?>" value="<?php echo esc_attr($minQuantity); ?>" data-min="<?php echo esc_attr($minQuantity); ?>" data-unit="<?php echo esc_attr($cluster->defaultProduct->unit); ?>">
                                <span class="input-group-text incr-decr">
                                    <button type="button" class="btn-quantity" data-type="plus">+</button>
                                </span>
                            </div>
                            <button class="btn-addtobasket d-flex justify-content-center align-items-center" type="submit">
                                <?php echo esc_html(__('In cart', 'propeller-ecommerce-v2')); ?>
                            </button>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <?php if (!UserController::is_propeller_logged_in()) { ?>
                        <a href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::LOGIN_PAGE))); ?>" class="btn-addtobasket d-flex justify-content-center align-items-center">

                            <?php echo esc_html(__('Request a price', 'propeller-ecommerce-v2')); ?>
                        </a>
                    <?php } else { ?>
                        <button type="submit" class="btn-addtobasket d-flex justify-content-center align-items-center btn-price-request btn-cluster-price-request" data-cluster_id="<?php echo esc_attr($cluster->clusterId); ?>" data-id="<?php echo esc_attr($cluster_product->productId); ?>" data-code="<?php echo esc_attr($cluster_product->sku); ?>" data-name="<?php echo esc_attr($cluster_product->name[0]->value); ?>" data-quantity="<?php echo esc_attr($cluster_product->minimumQuantity); ?>" data-minquantity="<?php echo esc_attr($cluster_product->minimumQuantity); ?>" data-unit="<?php echo esc_attr($cluster_product->unit); ?>">

                            <?php echo esc_html(__('Request a price', 'propeller-ecommerce-v2')); ?>
                        </button>

                <?php }
                } ?>
            </form>

        </div>
        <?php if (UserController::is_propeller_logged_in()) { ?>
            <div class="col-auto">
                <?php echo esc_html(apply_filters('propel_cluster_add_favorite', $cluster, $this)); ?>
            </div>
        <?php } ?>
    </div>
<?php } ?>
