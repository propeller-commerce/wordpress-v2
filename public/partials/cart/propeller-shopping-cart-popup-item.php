<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\SessionController;
use Propeller\PropellerHelper;

$user_prices = SessionController::get(PROPELLER_SPECIFIC_PRICES);

?>

<div class="row modal-product m-0">
    <div class="image col-2">
        <?php if ($added_item->product->has_images()) { ?>
            <img class="img-fluid added-item-img" src="<?php echo esc_url($added_item->product->images[0]->images[0]->url); ?>" alt="<?php echo esc_attr($added_item->product->name[0]->value); ?>">
        <?php } else { ?>
            <span class="no-image"></span>
        <?php } ?>
    </div>
    <div class="details col-10 col-md-5">
        <div class="product-name added-item-name">
            <?php echo esc_html($added_item->product->name[0]->value); ?>
        </div>
        <div class="product-sku"><?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>:
            <span class="added-item-sku"><?php echo esc_html($added_item->product->sku); ?></span>
        </div>
    </div>
    <div class="offset-2 offset-md-0 col-10 col-md-5">
        <div class="product-price row align-items-center">
            <span class="col-6 col-md-7 col-lg-6 quantity"><?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?>:&nbsp;
                <span class="added-item-quantity"><?php echo esc_html($added_item->quantity); ?></span>
            </span>
            <div class="col-6 col-md-5 col-lg-6 d-flex justify-content-end product-item-price">
                <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                <?php if ($user_prices == false) { ?>
                    <span class="added-item-price"><?php echo esc_html(PropellerHelper::formatPrice($added_item->totalPrice)); ?></span>
                <?php } else { ?>
                    <span class="added-item-price added-item-priceNet"><?php echo esc_html(PropellerHelper::formatPrice($added_item->totalPriceNet)); ?></span>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php
    if (isset($added_item->childItems) && is_array($added_item->childItems) && count($added_item->childItems)) { ?>
        <div class="offset-2 col-10">
            <?php
            foreach ($added_item->childItems as $child) {
                apply_filters('propel_order_details_popup_cluster_item', $child, $obj);
            } ?>
        </div>
    <?php }
    ?>
</div>

<?php // }