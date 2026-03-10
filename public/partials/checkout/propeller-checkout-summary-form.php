<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Enum\PageType;
use Propeller\Includes\Controller\PageController;

?>
<div class="row align-items-start">
    <div class="col-10 col-md-3 col-lg-3">
        <div class="checkout-title"><?php echo esc_html(__('Notes', 'propeller-ecommerce-v2')); ?></div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <form name="checkout-notes" class="form-handler checkout-form validate" method="post">
            <input type="hidden" name="action" value="cart_process" />
            <input type="hidden" name="status" value="<?php echo esc_attr(SessionController::get(PROPELLER_ORDER_STATUS_TYPE)); ?>" />
            <input type="hidden" name="payMethod" value="<?php echo esc_attr($cart->paymentData->method); ?>" />
            <input type="hidden" name="carrier" value="<?php echo esc_attr($cart->postageData->carrier); ?>" />

            <fieldset>
                <div class="row form-group">
                    <div class="col-form-fields col-12 col-md-8">
                        <label class="form-label" for="field_extra"><?php echo esc_html(__('Your reference (optional)', 'propeller-ecommerce-v2')); ?></label>
                        <input name="reference" value="" class="form-control" id="field_extra" placeholder="<?php echo esc_html(__('Your reference (optional)', 'propeller-ecommerce-v2')); ?>">
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-form-fields col-12 col-md-8">
                        <label class="form-label" for="field_notes"><?php echo esc_html(__('Your comment (optional)', 'propeller-ecommerce-v2')); ?></label>
                        <textarea type="text" name="notes" value="" class="form-control" id="field_notes" placeholder="<?php echo esc_html(__('Your comment (optional)', 'propeller-ecommerce-v2')); ?>"></textarea>
                    </div>
                </div>

            </fieldset>

            <?php if (SessionController::get(PROPELLER_ORDER_STATUS_TYPE) != 'REQUEST') { ?>
                <hr class="checkout-divider" />
                <fieldset>
                    <legend class="checkout-header"><?php echo esc_html(__('Place your order', 'propeller-ecommerce-v2')); ?></legend>
                    <div class="row form-group">
                        <div class="col-form-fields col-12">
                            <div class="row g-3">
                                <div class="col-12 col-md-8">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="checkbox" name="termsConditions" id="termsConditions" value="Y" required aria-required="true">
                                        <span><?php echo esc_html(__('I agree with the', 'propeller-ecommerce-v2')); ?> <a href="<?php echo esc_url($obj->buildUrl('', PageController::get_slug(PageType::TERMS_CONDITIONS_PAGE))); ?>" target="_blank"><?php echo esc_html(__('Terms and Conditions', 'propeller-ecommerce-v2')); ?></a></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>
            <?php } ?>

            <?php echo esc_html( wp_kses_post(apply_filters('propel_checkout_summary_submit', $this->cart, $this)) ); ?>

        </form>
    </div>
</div>