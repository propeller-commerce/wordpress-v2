<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;
?>
<div class="container-fluid px-0 checkout-header-wrapper">
    <div class="row align-items-start">
        <div class="col-12 col-sm me-auto checkout-header">
            <?php if ($order->status == 'REQUEST') { ?>
                <h1><?php echo esc_html(__('Your payment is now being processed.', 'propeller-ecommerce-v2')); ?></h1>
            <?php } else { ?>
                <h1><?php echo esc_html(__('Your payment is now being processed.', 'propeller-ecommerce-v2')); ?></h1>
            <?php } ?>
        </div>
        <div class="col-12 order-details">
            <div>
                <a href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::PAYMENT_CHECK_PAGE), '') . '?order_id=' . $order->id); ?>">
                    <?php echo esc_html(__('Click here', 'propeller-ecommerce-v2')); ?>
                </a>
                <?php echo esc_html(__('to check the status of your payment again.', 'propeller-ecommerce-v2')); ?>
            </div>
        </div>
    </div>
</div>