<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;
use Propeller\PropellerHelper;

?>
<div class="mb-4 propeller-account-wrapper">
    <div class="address-box quotations-box">
        <div class="quotations-box-header">
            <h3><?php echo esc_html(__('Current orders', 'propeller-ecommerce-v2')); ?></h3>
            <a href="<?php echo esc_url($obj->buildUrl('', PageController::get_slug(PageType::ORDERS_PAGE))); ?>" class="view-all-link"><?php echo esc_html(__('View all', 'propeller-ecommerce-v2')); ?></a>
        </div>
        <div class="quotations-box-content">
            <?php if (!empty($orders)) : ?>
                <?php foreach ($orders as $order) : ?>
                    <a href="<?php echo esc_url($obj->buildUrl('', PageController::get_slug(PageType::ORDER_DETAILS_PAGE))); ?>?order_id=<?php echo esc_attr((int) $order->id); ?>" class="quotation-item">
                        <div class="quotation-info">
                            <span class="quotation-number">#<?php echo esc_html($order->id); ?></span>
                            <span class="order-status status-<?php echo esc_attr(strtolower($order->status)); ?>"><?php echo esc_html($order->status); ?></span>
                        </div>
                        <div class="quotation-price">
                            <?php echo esc_html(PropellerHelper::currency()); ?> <?php echo esc_html(PropellerHelper::formatPrice($order->total->net)); ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else : ?>
                <p class="no-quotations"><?php echo esc_html(__('You have not placed any orders recently.', 'propeller-ecommerce-v2')); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>