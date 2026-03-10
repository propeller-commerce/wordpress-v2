<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;
use Propeller\PropellerHelper;

?>
<div class="mb-4 propeller-account-wrapper">
    <div class="address-box quotations-box">
        <div class="quotations-box-header">
            <h3><?php echo esc_html(__('Open quotations', 'propeller-ecommerce-v2')); ?></h3>
            <a href="<?php echo esc_url($obj->buildUrl('', PageController::get_slug(PageType::QUOTATIONS_PAGE))); ?>" class="view-all-link"><?php echo esc_html(__('View all', 'propeller-ecommerce-v2')); ?></a>
        </div>
        <div class="quotations-box-content">
            <?php if (!empty($quotations)) : ?>
                <?php foreach ($quotations as $quotation) : ?>
                    <a href="<?php echo esc_url($obj->buildUrl('', PageController::get_slug(PageType::QUOTATION_DETAILS_PAGE))); ?>?order_id=<?php echo esc_attr((int) $quotation->id); ?>" class="quotation-item">
                        <div class="quotation-info">
                            <span class="quotation-number">#<?php echo esc_html($quotation->id); ?></span>
                            <span class="quotation-valid"><?php echo esc_html(__('Valid until', 'propeller-ecommerce-v2')); ?> <?php echo !empty($quotation->validUntil) ? esc_html(gmdate("d-m-Y", strtotime($quotation->validUntil))) : '-'; ?></span>
                        </div>
                        <div class="quotation-price">
                            <?php echo esc_html(PropellerHelper::currency()); ?> <?php echo esc_html(PropellerHelper::formatPrice($quotation->total->net)); ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else : ?>
                <p class="no-quotations"><?php echo esc_html(__('You have no open quotes.', 'propeller-ecommerce-v2')); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>