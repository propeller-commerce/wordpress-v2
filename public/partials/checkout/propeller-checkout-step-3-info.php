<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;
use Propeller\PropellerHelper;

?>
<div class="row align-items-start">
    <div class="col-10 col-md-3 col-lg-3">
        <div class="checkout-step"><?php echo esc_html(__('Step 3', 'propeller-ecommerce-v2')); ?></div>
        <div class="checkout-title"><?php echo esc_html(__('Payment details', 'propeller-ecommerce-v2')); ?></div>
    </div>
    <div class="col-12 col-md-7 col-lg-7 ms-md-auto order-3 order-md-2 user-details">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="addr-title"><?php echo esc_html(__('Payment method', 'propeller-ecommerce-v2')); ?></div>
                <div class="user-addr-details">
                    <span>
                        <?php
                        $paymethods_array = new \WP_Query(array(
                            'post_type' => 'paymethods'
                        ));

                        $matched = false;

                        foreach ($paymethods_array->posts as $post) {
                            if ($cart->paymentData->method === $post->post_title) {
                                echo esc_html($post->post_excerpt);
                                $matched = true;
                                break;
                            }
                        }

                        if (!$matched) {
                            echo esc_html($cart->paymentData->method);
                        }
                        ?>
                    </span> <span class="price"><?php if ($cart->paymentData->priceNet > 0) { ?>+ <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span><?php echo esc_html(PropellerHelper::formatPrice($cart->paymentData->priceNet)); ?><?php } ?></span>
                </div>
            </div>
            <?php if (PROPELLER_PARTIAL_DELIVERY) { ?>
                <div class="col-12 col-md-6">
                    <div class="addr-title"><?php echo esc_html(__('Partial delivery', 'propeller-ecommerce-v2')); ?></div>
                    <div class="user-addr-details">
                        <span><?php if ($cart->postageData->partialDeliveryAllowed == 'N')
                                    echo esc_html(__("I'd like to receive all products at once.", 'propeller-ecommerce-v2'));
                                else echo esc_html(__("I would like to receive the available products as soon as possible, the other products will be delivered later on.", 'propeller-ecommerce-v2')); ?></span>
                        <br>
                    </div>
                </div>
            <?php } ?>
            <?php if (PROPELLER_SELECTABLE_CARRIERS) { ?>
                <div class="col-12 col-md-6">
                    <div class="addr-title"><?php echo esc_html(__('Carriers', 'propeller-ecommerce-v2')); ?></div>
                    <div class="user-addr-details">
                        <span><?php echo esc_html($cart->postageData->carrier); ?></span><br>
                    </div>
                </div>
            <?php } ?>
            <?php if (PROPELLER_USE_DATEPICKER) { ?>
                <div class="col-12 col-md-6">
                    <div class="addr-title"><?php echo esc_html(__('Delivery date', 'propeller-ecommerce-v2')); ?></div>
                    <div class="user-addr-details">
                        <span><?php echo esc_html(gmdate("d-m-Y", strtotime($cart->postageData->requestDate))); ?></span><br>
                    </div>
                </div>
            <?php } ?>
        </div>

    </div>
    <div class="col-2 col-md-1 order-2 order-md-3 d-flex justify-content-end">
        <div class="edit-checkout">
            <a href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::CHECKOUT_PAGE),  '3')); ?>">
                <svg class="icon icon-edit" aria-hidden="true">
                    <use xlink:href="#shape-checkout-edit"></use>
                </svg>
            </a>
        </div>
    </div>
</div>