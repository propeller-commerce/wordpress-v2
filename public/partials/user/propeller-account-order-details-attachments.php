<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if ($order->has_attachments()) { ?>


    <svg style="display:none;">
        <symbol viewBox="0 0 48 48" id="shape-attachments">
            <title>Attachments</title>
            <path fill="none" stroke-linecap="square" stroke-miterlimit="10" d="M36 8v25c0 7.2-5.8 13-13 13h0c-7.2 0-13-5.8-13-13V11c0-5 4-9 9-9h0c5 0 9 4 9 9v20c0 2.8-2.2 5-5 5h0c-2.8 0-5-2.2-5-5V14" stroke-width="2" />
        </symbol>

    </svg>
    <div class="order-attachments">
        <div class="row align-items-baseline">
            <div class="col-12 label-title order-attachments-title d-flex aling-items-center">
                <?php echo esc_html(__('Invoices', 'propeller-ecommerce-v2')); ?>
                <svg class="icon icon-order-attachment">
                    <use class="shape-attachments" xlink:href="#shape-attachments"></use>
                </svg>
            </div>

        </div>
        <div class="row order-attachments-container">
            <div class="col-12">
                <?php foreach ($order->get_attachments() as $att) { ?>
                    <a href="#" data-action="download_secure_attachment" data-attachment="<?php echo esc_attr($att->id); ?>" class="d-block secure-attachment-btn" target="_blank">
                        <?php echo esc_html($att->alt[0]->value); ?>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>

<?php } ?>
