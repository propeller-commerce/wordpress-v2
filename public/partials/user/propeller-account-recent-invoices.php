<?php
if (! defined('ABSPATH')) exit;

use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;
use Propeller\PropellerHelper;

?>
<div class="mb-4 propeller-account-wrapper">
    <div class="address-box quotations-box">
        <div class="quotations-box-header">
            <h3><?php echo esc_html(__('My invoices', 'propeller-ecommerce-v2')); ?></h3>
            <a href="<?php echo esc_url($obj->buildUrl('', PageController::get_slug(PageType::INVOICES_PAGE))); ?>" class="view-all-link"><?php echo esc_html(__('View all', 'propeller-ecommerce-v2')); ?></a>
        </div>
        <div class="quotations-box-content">
            <?php if (!empty($invoices)) : ?>
                <?php foreach ($invoices as $invoice) : ?>
                    <?php foreach ($invoice->attachments as $index => $attachment) : ?>
                        <a href="#" data-action="download_secure_attachment" data-attachment="<?php echo esc_attr($attachment->id); ?>" class="quotation-item secure-attachment-btn" target="_blank">
                            <div class="quotation-info">
                                <span class="quotation-number">#<?php echo esc_html($invoice->code . ($index > 0 ? '-' . $index : '')); ?></span>
                                <span class="quotation-valid"><?php echo esc_html(gmdate("d-m-Y", strtotime($attachment->createdAt))); ?></span>
                            </div>
                            <div class="quotation-price">
                                <?php echo esc_html(PropellerHelper::currency()); ?> <?php echo esc_html(PropellerHelper::formatPrice($invoice->total)); ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php else : ?>
                <p class="no-quotations"><?php echo esc_html(__('You don\'t have any invoices', 'propeller-ecommerce-v2')); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>