<?php
if (! defined('ABSPATH')) exit;

use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;
use Propeller\PropellerHelper;

?>

<div class="mb-4 propeller-account-wrapper">
    <div class="address-box quotations-box">
        <div class="quotations-box-header">
            <h3><?php echo esc_html(__('Authorization requests', 'propeller-ecommerce-v2')); ?></h3>
            <a href="<?php echo esc_url($obj->buildUrl('', PageController::get_slug(PageType::PURCHASE_AUTHORIZATION_REQUESTS_PAGE))); ?>" class="view-all-link"><?php echo esc_html(__('View all', 'propeller-ecommerce-v2')); ?></a>
        </div>
        <div class="quotations-box-content ">
            <?php if (!empty($purchase_authorizations)) : ?>
                <?php foreach ($purchase_authorizations as $purchase_authorization) : ?>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#purchase_authorization_preview_modal" data-cart="<?php echo esc_attr($purchase_authorization->cartId); ?>" class="quotation-item purchase-authorization-item">
                        <div class="quotation-info">
                            <span class="quotation-number"><?php echo esc_html(gmdate("d-m-Y", strtotime($purchase_authorization->lastModifiedAt))); ?></span>
                            <span class="quotation-valid"><?php echo esc_html(sprintf('%s %s %s', $purchase_authorization->contact->firstName, $purchase_authorization->contact->middleName, $purchase_authorization->contact->lastName)); ?></span>
                        </div>
                        <div class="quotation-price">
                            <span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                            <?php echo esc_html(PropellerHelper::formatPrice($purchase_authorization->total->totalNet)); ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else : ?>
                <p class="no-quotations"><?php echo esc_html(__('You do not have any authorization requests yet.', 'propeller-ecommerce-v2')); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>