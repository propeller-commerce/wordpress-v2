<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;
use Propeller\Includes\Object\Product;

if (count($shipment->items)) {
    foreach ($shipment->items as $shipment_item) {
?>
        <div class="row modal-product m-0 px-0">

            <?php if (!is_null($shipment_item->orderItem) && isset($shipment_item->orderItem->product)) { ?>
                <?php $shipment_item->orderItem->product = new Product($shipment_item->orderItem->product); ?>


                <div class="col-2 product-image">
                    <?php if (isset($shipment_item->orderItem->product->cluster) && is_object($shipment_item->orderItem->product->cluster)) { ?>
                        <a href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::CLUSTER_PAGE), $shipment_item->orderItem->product->cluster->slug[0]->value, $shipment_item->orderItem->product->cluster->urlId)); ?>">
                        <?php } else { ?>
                            <a href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $shipment_item->orderItem->product->slug[0]->value, $shipment_item->orderItem->product->urlId)); ?>">
                            <?php } ?>
                            <img class="img-fluid"
                                loading="lazy"
                                src="<?php echo esc_url($shipment_item->orderItem->product->has_images() ? $shipment_item->orderItem->product->images[0]->images[0]->url : $obj->assets_url . '/img/no-image-card.webp'); ?>"
                                alt="<?php echo esc_attr($shipment_item->orderItem->product->name[0]->value); ?>">
                            </a>
                </div>
                <div class="col-10 col-md-7">
                    <?php if (isset($shipment_item->orderItem->product->cluster) && is_object($shipment_item->orderItem->product->cluster)) { ?>
                        <a class="product-name" href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::CLUSTER_PAGE), $shipment_item->orderItem->product->cluster->slug[0]->value, $shipment_item->orderItem->product->cluster->urlId)); ?>">
                        <?php } else { ?>
                            <a class="product-name" href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $shipment_item->orderItem->product->slug[0]->value, $shipment_item->orderItem->product->urlId)); ?>">
                            <?php } ?>
                            <?php echo esc_html($shipment_item->orderItem->product->name[0]->value); ?>
                            </a>
                            <div class="product-sku">
                                <?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>: <?php echo esc_html($shipment_item->orderItem->product->sku); ?>
                            </div>
                </div>
                <div class="offset-2 offset-md-0 col-10 col-md-3">
                    <span class="label-title"><?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?>: </span>
                    <span class="product-quantity align-items-end"><?php echo esc_html($shipment_item->quantity); ?></span>
                </div>

            <?php } ?>

        </div>

    <?php } ?>
    <?php if (count($shipment->trackAndTraces)) { ?>
        <div class="row px-0 mx-0 mt-2 mb-2 align-items-center shipment-track-and-traces"></div>
    <?php } ?>
<?php } ?>
