<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;
use Propeller\PropellerHelper;

?>

<div class="order-product-item">
    <div class="row g-0 align-items-start">
        <div class="col-2 col-md-2 col-lg-1 px-4 product-image order-1">
            <a href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $item->product->slug[0]->value, $item->product->urlId)); ?>">
                <img class="img-fluid"
                    loading="lazy"
                    src="<?php echo esc_url($item->product->has_images() ? $item->product->images[0]->images[0]->url : $obj->assets_url . '/img/no-image-card.webp'); ?>"
                    alt="<?php echo esc_attr($item->product->name[0]->value); ?>">
            </a>
        </div>
        <div class="col-10 col-md-4 col-lg-5 pe-5 product-description order-2">
            <a class="product-name" href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $item->product->slug[0]->value, $item->product->urlId)); ?>">
                <?php echo esc_html($item->name); ?>
            </a>
            <div class="product-sku">
                <?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>: <?php echo esc_html($item->sku); ?>
            </div>

        </div>
        <!-- <div class="offset-2 offset-md-0 col-10 col-md-2 reference order-md-3 order-last ">
            <?php if (!empty($item->notes)) { ?>
                <div class="d-block d-md-none label-title"><?php echo esc_html(__('Reference', 'propeller-ecommerce-v2')); ?></div>
            <?php } ?>
            <?php echo esc_html($item->notes); ?>
        </div>-->
        <div class="offset-2 offset-md-0 col-2 col-md-2 order-4">
            <div class="d-block d-md-none label-title"><?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?></div>
            <span class="product-quantity"><?php echo esc_html($item->quantity); ?></span>
        </div>
        <div class="col-2 order-5 price-per-item">
            <div class="d-block d-md-none label-title"><?php echo esc_html(__('Price', 'propeller-ecommerce-v2')); ?></div>
            <span class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                <?php echo esc_html(PropellerHelper::formatPrice($item->priceTotal)); ?>
            </span>
        </div>
        <div class="col-2 order-5 order-md-last order-status">
            <div class="d-block d-md-none label-title"><?php echo esc_html(__('Status', 'propeller-ecommerce-v2')); ?></div>
            <?php
            $itemStatus = 'Unknown';

            if (!empty($order->shipments)) {
                foreach ($order->shipments as $shipment) {
                    if (isset($shipment->items)) {
                        foreach ($shipment->items as $shipmentItem) {
                            if ($shipmentItem->orderItemId == $item->id) {
                                if ($shipmentItem->quantity == $item->quantity)
                                    $itemStatus = __('Send', 'propeller-ecommerce-v2');
                                else if ($shipmentItem->quantity < $item->quantity && $shipmentItem->quantity != 0)
                                    $itemStatus = __('Backorder', 'propeller-ecommerce-v2');
                                else if ($shipmentItem->quantity == 0)
                                    $itemStatus = __('Canceled', 'propeller-ecommerce-v2');
                            }
                        }
                    }
                }
            }
            ?>
            <span class="shipping-sent <?php echo esc_html(strtolower($itemStatus)); ?>"><?php echo esc_html($itemStatus); ?></span>
        </div>

    </div>
</div>