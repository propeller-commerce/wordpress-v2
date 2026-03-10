<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="user-details">
    <?php if (!empty($invoice_address->street)) { ?>
        <?php echo esc_html($invoice_address->company); ?><br>
        <?php echo esc_html($invoice_address->firstName); ?> <?php echo esc_html($invoice_address->middleName); ?> <?php echo esc_html($invoice_address->lastName); ?><br>
        <?php echo esc_html($invoice_address->street); ?> <?php echo esc_html($invoice_address->number); ?> <?php echo esc_html($invoice_address->numberExtension); ?><br>
        <?php echo esc_html($invoice_address->postalCode); ?> <?php echo esc_html($invoice_address->city); ?><br>

        <?php echo esc_html(!$countries[$invoice_address->country] ? $invoice_address->country : $countries[$invoice_address->country]); ?><br>

        <?php echo esc_html($invoice_address->email); ?>
    <?php } ?>
</div>

<?php if ($can_edit_address) { ?>
    <a class="btn-address-edit <?php echo esc_html( (empty($invoice_address->street) ? "btn-address-add" : '') ); ?> address-edit open-edit-modal-form"
        data-form-id="edit_address<?php echo esc_html(isset($invoice_address->id) ? $invoice_address->id : '_guest'); ?>"
        data-title="<?php echo esc_html(__('Edit invoice address', 'propeller-ecommerce-v2')); ?>"
        data-bs-target="#edit_address_modal_<?php echo esc_html(isset($invoice_address->id) ? $invoice_address->id : '_guest'); ?>"
        data-bs-toggle="modal"
        role="button">
        <?php
        if (!empty($invoice_address->street))
            echo esc_html(__('Edit', 'propeller-ecommerce-v2'));
        else
            echo esc_html(__('Add new invoice address', 'propeller-ecommerce-v2'));
        ?>
    </a>
<?php } ?>
