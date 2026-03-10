<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Enum\PageType;

$is_valid = true;

if ($order->invalid) {
    $is_valid = false;
    ?>
    <div class="col-12">
        <div class="alert alert-danger" role="alert">
            <?php echo esc_html(__('This quote is no longer valid. Reason: ', 'propeller-ecommerce-v2') . $order->invalidationReason); ?>
        </div>
    </div>
    <?php
} else if ($order->validUntil && strtotime($order->validUntil) < time()) {
    $is_valid = false;
    ?>
    <div class="col-12">
        <div class="alert alert-danger" role="alert">
            <?php echo esc_html(__('This quote has expired.', 'propeller-ecommerce-v2')); ?>
        </div>
    </div>
    <?php
} else if (!$order->public) {
    $is_valid = false;
    ?>
    <div class="col-12">
        <div class="alert alert-danger" role="alert">
            <?php echo esc_html(__('This quote is not available.', 'propeller-ecommerce-v2')); ?>
        </div>
    </div>
    <?php
} else if (UserController::user()->is_purchaser() && UserController::user()->get_authorization_limit() < $order->total->net) {
    $is_valid = false;
    ?>
    <div class="col-12">
        <div class="alert alert-danger" role="alert">
            <?php echo esc_html(__('Your authorization limit is too low to place this quote as order. Please contact us.', 'propeller-ecommerce-v2')); ?>
        </div>
    </div>
    <?php
}

if ($is_valid) { ?>

<div class="col-12 col-md-6">
    <form name="checkout-notes" class="form-handler checkout-form validate checkout-notes" method="post">
        <input type="hidden" name="action" value="change_order_status" />
        <input type="hidden" name="send_email" value="true" />
        <input type="hidden" name="add_pdf" value="true" />
        <input type="hidden" name="status" value="NEW" />
        <input type="hidden" name="order_id" value="<?php echo esc_attr($order->id); ?>" />
        <fieldset>
            <legend class="checkout-header"><?php echo esc_html(__('Place your order', 'propeller-ecommerce-v2')); ?></legend>
            <div class="row form-group">
                <div class="col-form-fields col-12">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-check-label">
                                <input class="form-check-input" type="checkbox" name="termsConditions" id="termsConditions" value="Y" required aria-required="true">
                                <span><?php echo esc_html(__('I agree with the ', 'propeller-ecommerce-v2')); ?> <a href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::TERMS_CONDITIONS_PAGE))); ?>" target="_blank"><?php echo esc_html(__('Terms and Conditions', 'propeller-ecommerce-v2')); ?></a></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
        <div class="row form-group form-group-submit align-items-end justify-content-end mt-4 mb-4">
            <div class="col-form-fields col-12">
                <div class="row g-3">
                    <div class="col-12">
                        <button type="submit" class="btn-checkout"><?php echo esc_html(__('Place quote as order', 'propeller-ecommerce-v2')); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php } ?>
