<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;
use Propeller\PropellerHelper;

$count = 0;

foreach ($order->items as $item) {

    if (
        $item->class === 'product' &&
        $item->isBonus !== 'Y' &&
        empty($item->parentOrderItemId)
    )

        $count++;
}

?>
<div class="row order-item g-0">
    <div class="col-md-2"><span class="table-label d-inline-block d-md-none col-5 col-sm-4 px-0"><?php echo esc_html(__('Order number', 'propeller-ecommerce-v2')); ?></span><span class="px-0 col-auto"><?php echo esc_html($order->id); ?></span></div>
    <div class="col-md-3"><span class="table-label d-inline-block d-md-none col-5 col-sm-4 px-0"><?php echo esc_html(__('Date', 'propeller-ecommerce-v2')); ?></span><span class="px-0 col-auto"><?php echo esc_html(gmdate("d-m-Y", strtotime($order->createdAt))); ?></span></div>
    <div class="col-md-1 col-xl-1"><span class="table-label d-inline-block d-md-none col-5 col-sm-4 px-0"><?php echo esc_html(__('Qty', 'propeller-ecommerce-v2')); ?></span><span class="px-0 col-auto"><?php echo esc_html($count); ?></div>
    <div class="col-md-2"><span class="table-label d-inline-block d-md-none col-5 col-sm-4 px-0"><?php echo esc_html(__('Order total', 'propeller-ecommerce-v2')); ?></span><span class="px-0 col-auto"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span> <?php echo esc_html(PropellerHelper::formatPrice($order->total->net)); ?></span></span></div>
    <div class="col-md-2">
        <span class="table-label d-inline-block d-md-none col-5 col-sm-4 px-0"><?php echo esc_html(__('Status', 'propeller-ecommerce-v2')); ?></span>
        <span class="px-0 col-auto">
            <?php
            $orderstatus_array = new \WP_Query(array(
                'post_type' => 'orderstatuses'
            ));

            $matched = false;

            foreach ($orderstatus_array->posts as $post) {
                if ($order->status === $post->post_title) {
                    echo esc_html($post->post_excerpt);
                    $matched = true;
                    break;
                }
            }

            if (!$matched) {
                echo esc_html($order->status);
            }
            ?>
        </span>
    </div>
    <div class="col-md-2 text-md-end"><a href="<?php echo esc_url($obj->buildUrl('', PageController::get_slug(PageType::ORDER_DETAILS_PAGE))); ?>?order_id=<?php echo esc_attr($order->id); ?>" class="order-details-link"><?php echo esc_html(__('View order', 'propeller-ecommerce-v2')); ?></a></div>
</div>