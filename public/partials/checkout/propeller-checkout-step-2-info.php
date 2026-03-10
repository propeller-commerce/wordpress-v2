<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\OrderType;
use Propeller\Includes\Enum\PageType;
use Propeller\PropellerHelper;

?>
<div class="row align-items-start">
    <div class="col-10 col-md-3 col-lg-3">
        <div class="checkout-step"><?php echo esc_html(__('Step 2', 'propeller-ecommerce-v2')); ?></div>
        <div class="checkout-title"><?php echo esc_html(__('Delivery details', 'propeller-ecommerce-v2')); ?></div>
    </div>
    <div class="col-12 col-md-7 col-lg-7 ms-md-auto order-3 order-md-2 user-details">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="addr-title"><?php echo esc_html(__('Delivery address', 'propeller-ecommerce-v2')); ?></div>
                <div class="user-addr-details">
                    <?php echo esc_html($delivery_address->company); ?><br>
                    <?php echo esc_html($obj->get_salutation($delivery_address)); ?>
                    <?php echo esc_html($delivery_address->firstName); ?> <?php echo esc_html($delivery_address->middleName); ?> <?php echo esc_html($delivery_address->lastName); ?><br>
                    <?php echo esc_html($delivery_address->street); ?> <?php echo esc_html($delivery_address->number); ?> <?php echo esc_html($delivery_address->numberExtension); ?><br>
                    <?php echo esc_html($delivery_address->postalCode); ?> <?php echo esc_html($delivery_address->city); ?><br>
                    <?php echo esc_html(!$countries[$delivery_address->country] ? $delivery_address->country : $countries[$delivery_address->country]); ?>

                </div>
            </div>
            <?php if (!PROPELLER_SELECTABLE_CARRIERS) { ?>
                <div class="col-12 col-md-6">
                    <div class="addr-title"><?php echo esc_html(__('Shipping', 'propeller-ecommerce-v2')); ?></div>
                    <div class="user-addr-details">
                        <span class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                            <?php
                            if ($cart->postageData->method != $obj->get_order_types()->items[OrderType::PICKUP]->value)
                                echo esc_html(PropellerHelper::formatPrice(!$user_prices ? $cart->postageData->price : $cart->postageData->priceNet));
                            else {
                                echo esc_html(PropellerHelper::formatPrice(!$user_prices ? $cart->postageData->price : $cart->postageData->priceNet) . '&nbsp;');
                                echo esc_html(__('(Pickup)', 'propeller-ecommerce-v2'));
                            }
                            ?>
                        </span>
                        <br />
                    </div>
                </div>
            <?php } ?>
        </div>

    </div>
    <div class="col-2 col-md-1 order-2 order-md-3 d-flex justify-content-end">
        <div class="edit-checkout">
            <a href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::CHECKOUT_PAGE), '2')); ?>">
                <svg class="icon icon-edit" aria-hidden="true">
                    <use xlink:href="#shape-checkout-edit"></use>
                </svg>
            </a>
        </div>
    </div>
</div>