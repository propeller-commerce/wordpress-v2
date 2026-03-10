<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;

?>
<div class="container-fluid px-0 checkout-header-wrapper">
    <div class="row align-items-start">
        <div class="col-12 col-sm me-auto checkout-header">
            <?php if ($order->status == 'REQUEST') { ?>
                <h1><?php echo esc_html(__('Thank you for your quote request!', 'propeller-ecommerce-v2')); ?></h1>
            <?php } else { ?>
                <h1><?php echo esc_html(__('Thank you for your order!', 'propeller-ecommerce-v2')); ?></h1>
            <?php } ?>
        </div>
        <div class="col-12 order-details">
            <?php if ($order->status == 'REQUEST') { ?>
                <div><?php echo esc_html(__('Your quote request with request number', 'propeller-ecommerce-v2')); ?> <span class="order-number"><?php echo esc_html($order->id); ?></span> <?php echo esc_html(__('has been placed.', 'propeller-ecommerce-v2')); ?></div>
                <div><?php echo esc_html(__('Your quote request confirmation has been sent to', 'propeller-ecommerce-v2')); ?> <span class="user-email"><?php echo esc_html($order->email); ?></span>.</div>
            <?php } else { ?>
                <div><?php echo esc_html(__('Your order with order number', 'propeller-ecommerce-v2')); ?> <span class="order-number"><?php echo esc_html($order->id); ?></span> <?php echo esc_html(__('has been placed.', 'propeller-ecommerce-v2')); ?></div>
                <div><?php echo esc_html(__('Your order confirmation has been sent to', 'propeller-ecommerce-v2')); ?> <span class="user-email"><?php echo esc_html($order->email); ?></span>. <?php echo esc_html(__('You can also find it in', 'propeller-ecommerce-v2')); ?> <a href="<?php echo esc_url($obj->buildUrl('', PageController::get_slug(PageType::MY_ACCOUNT_PAGE))); ?>"><?php echo esc_html(__('your account.', 'propeller-ecommerce-v2')); ?></a></div>
            <?php } ?>
        </div>
    </div>
</div>