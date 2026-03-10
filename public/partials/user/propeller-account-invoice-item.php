<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\PropellerHelper;

$index = 0;

?>
<?php foreach ($invoice->attachments as $attachment) { ?>
    <div class="order-product-item">
        <div class="row g-0 align-items-start">
            <div class="col-md-3">
                <span class="label d-inline-flex d-md-none"><?php echo esc_html(__('Order number', 'propeller-ecommerce-v2')); ?>: </span>
                <span class="code"><?php echo esc_html($invoice->code . ($index > 0 ? '-' . $index : '')); ?></span>
            </div>
            <div class="col-md-3">
                <span class="label d-inline-flex d-md-none"><?php echo esc_html(__('Date', 'propeller-ecommerce-v2')); ?>: </span>
                <span class="date"><?php echo esc_html(gmdate("d-m-Y", strtotime($attachment->createdAt))); ?></span>
            </div>
            <div class="col-md-4">
                <span class="label d-inline-flex d-md-none"><?php echo esc_html(__('Total', 'propeller-ecommerce-v2')); ?>: </span>
                <span class="total"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?></span> <?php echo esc_html($invoice->total); ?></span>
            </div>
            <div class="col-md-2">
                <a href="#" data-action="download_secure_attachment" data-attachment="<?php echo esc_attr($attachment->id); ?>" class="d-flex align-items-center download-pdf-link secure-attachment-btn" target="_blank">
                    <svg class="icon icon-download icon-arrow-download" aria-hidden="true">
                        <use class="shape-download" xlink:href="#shape-download"></use>
                    </svg>
                    <span>
                        <?php echo esc_html(__('Download PDF', 'propeller-ecommerce-v2')); ?>
                    </span>
                </a>
            </div>
        </div>
    </div>
    <?php $index++; ?>
<?php } ?>
