<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;

?>
<div class="row align-items-start">
    <div class="col-10 col-md-3">
        <div class="checkout-step"><?php echo esc_html(__('Step 1', 'propeller-ecommerce-v2')); ?></div>
        <div class="checkout-title"><?php echo esc_html(__('Invoice details', 'propeller-ecommerce-v2')); ?></div>
    </div>
    <div class="col-12 col-md-7 col-lg-7 ms-md-auto order-3 order-md-2 user-details">
        <div class="addr-title"><?php echo esc_html(__('Invoice address', 'propeller-ecommerce-v2')); ?></div>

        <div class="user-addr-details">
            <?php echo esc_html($invoice_address->company); ?><br>
            <?php echo esc_html($obj->get_salutation($invoice_address)); ?>
            <?php echo esc_html($invoice_address->firstName); ?> <?php echo esc_html($invoice_address->middleName); ?> <?php echo esc_html($invoice_address->lastName); ?><br>
            <?php echo esc_html($invoice_address->street); ?> <?php echo esc_html($invoice_address->number); ?> <?php echo esc_html($invoice_address->numberExtension); ?><br>
            <?php echo esc_html($invoice_address->postalCode); ?> <?php echo esc_html($invoice_address->city); ?><br>

            <?php echo esc_html(!$countries[$invoice_address->country] ? $invoice_address->country : $countries[$invoice_address->country]); ?>
        </div>
    </div>
    <div class="col-2 col-md-1 order-2 order-md-3 d-flex justify-content-end">
        <div class="edit-checkout">
            <a href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::CHECKOUT_PAGE), '1')); ?>">
                <svg class="icon icon-edit" aria-hidden="true">
                    <use xlink:href="#shape-checkout-edit"></use>
                </svg>
            </a>
        </div>
    </div>
</div>