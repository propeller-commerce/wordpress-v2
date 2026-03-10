<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\PropellerHelper;
?>
<form name="checkout-paymethod" class="form-handler checkout-form validate" method="post">
    <input type="hidden" name="action" value="cart_step_3" />
    <input type="hidden" name="step" value="<?php echo esc_attr($slug); ?>" />
    <input type="hidden" name="next_step" value="summary" />
    <input type="hidden" name="icp" value="N" />

    <fieldset>
        <div class="row form-group">
            <div class="col-form-fields col-12">
                <?php
                $paymethods = $obj->get_paymethods();

                echo wp_kses_post(apply_filters('propel_checkout_paymethods', $paymethods, $cart, $obj));
                ?>

            </div>
        </div>
    </fieldset>
    <?php if (PROPELLER_PARTIAL_DELIVERY) { ?>
        <fieldset>
            <div class="row form-group mt-4">
                <div class="col-12">
                    <div class="checkout-title"><?php echo esc_html( __('Partial delivery', 'propeller-ecommerce-v2') ); ?></div>
                </div>
            </div>
            <div class="row form-group partial-deliveries">
                <div class="col-form-fields col-12 mx-5">
                    <label class="form-check-label partialDelivery">
                        <input type="radio" name="partialDeliveryAllowed" id="partialDeliveryAllowed_all" value="N" checked />

                        <span class="partial-name"><?php echo esc_html( __("I'd like to receive all products at once.", 'propeller-ecommerce-v2') ); ?></span>
                    </label>
                </div>
                <div class="col-form-fields col-12 mx-5">
                    <label class="form-check-label partialDelivery">
                        <input type="radio" name="partialDeliveryAllowed" id="partialDeliveryAllowed_semi" value="Y" />

                        <span class="partial-name"><?php echo esc_html( __("I would like to receive the available products as soon as possible, the other products will be delivered later on.", 'propeller-ecommerce-v2') ); ?></span>
                    </label>
                </div>
            </div>
        </fieldset>
    <?php } else { ?>
        <?php /* <input type="hidden" name="partialDeliveryAllowed" value="N" /> */ ?>
    <?php } ?>
    <?php if (PROPELLER_SELECTABLE_CARRIERS) { ?>
        <fieldset>
            <div class="row form-group mt-4">
                <div class="col-12">
                    <div class="checkout-title"><?php echo esc_html( __('Carriers', 'propeller-ecommerce-v2') ); ?></div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-form-fields col-12">
                    <div class="row px-2 row g-3 form-check carriers radios-container">
                        <?php
                        $selected_carrier = $this->get_carrier();
                        $carriers = $obj->get_carriers();

                        foreach ($carriers as $carrier) { ?>
                            <div class="col-12 col-md-8">
                                <label class="form-check-label carrier-label <?php echo esc_attr($selected_carrier == $carrier->name ? 'selected' : ''); ?>">
                                    <span class="row d-flex align-items-center">
                                        <input type="radio" name="carrier" value="<?php echo esc_attr($carrier->name); ?>" title="<?php echo esc_attr( __('Select carrier', 'propeller-ecommerce-v2') ); ?>" data-rule-required="true" required="required" aria-required="true" class="required" <?php echo esc_attr($selected_carrier == $carrier->name ? 'checked="checked"' : ''); ?>>
                                        <span class="carrier-name col-4 col-md-6">
                                            <?php echo esc_html( $carrier->name ); ?>
                                            <?php if (!empty($carrier->logo)) { ?>
                                                <img src="<?php echo esc_url( $carrier->logo ); ?>" class="carrier-logo">
                                            <?php } ?>
                                        </span>
                                        <?php /*<span class="carrier-cost col-3"><span class="currency"><?php echo esc_html( PropellerHelper::currency() ); ?></span> <?php echo esc_html( PropellerHelper::formatPrice($carrier->shippingCost) ); ?></span> */?>

                                    </span>
                                </label>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </fieldset>
    <?php } else { ?>
        <?php /* <input type="hidden" name="carrier" value="" /> */ ?>
    <?php } ?>
    <?php if (PROPELLER_USE_DATEPICKER) { ?>
        <fieldset>
            <div class="row form-group mt-4">
                <div class="col-12">
                    <div class="checkout-title"><?php echo esc_html( __('Delivery date', 'propeller-ecommerce-v2') ); ?></div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-form-fields col-12 col-md-8">
                    <div class="row px-2 d-flex row g-3 form-check deliveries radios-container">
                        <?php
                        $days   = [];
                        $period = new DatePeriod(
                            new DateTime('tomorrow'),
                            new DateInterval('P1D'),
                            2
                        );

                        foreach ($period as $day) {
                            $days[] = [
                                PropellerHelper::days()[$day->format('w')],
                                $day->format('j'),
                                PropellerHelper::months()[$day->format('n')],
                                $day->format('Y-m-d\T00:00:00\Z')
                            ];
                        }
                        ?>
                        <?php
                        $selected_delivery_date = $this->get_postage_data()->requestDate;

                        foreach ($days as $delivery_day) { ?>

                            <div class="col-6 col-sm-3 mb-4">
                                <label class="form-check-label delivery <?php echo esc_attr($selected_delivery_date == $delivery_day[3] ? 'selected' : ''); ?>">
                                    <span class="row d-flex align-items-center text-center">
                                        <input type="radio" name="delivery_select" value="<?php echo esc_attr($delivery_day[3]); ?>" title="<?php echo esc_attr( __('Select delivery date', 'propeller-ecommerce-v2') ); ?>" data-rule-required="true" required="required" aria-required="true" class="required" <?php echo esc_attr($selected_delivery_date == $delivery_day[2] ? 'checked="checked"' : ''); ?>>
                                        <div class="delivery-day col-12"><?php echo esc_html($delivery_day[0]); ?></div>
                                        <div class="delivery-date col-12"><?php echo esc_html($delivery_day[1]); ?> <?php echo esc_html($delivery_day[2]); ?></div>
                                    </span>
                                </label>
                            </div>
                        <?php } ?>

                        <div class="col-6 col-sm-3 mb-4">
                            <label class="form-check-label delivery">
                                <span class="row d-flex align-items-center text-center justify-content-center">
                                    <input type="radio" name="delivery_select" value="0" title="Select delivery date" data-rule-required="true" required="required" aria-required="true" class="required custom-date">
                                    <svg class="icon icon-calendar" aria-hidden="true">
                                        <use xlink:href="#shape-calendar"></use>
                                    </svg>
                                    <div class="d-none delivery-day col-12"></div>
                                    <div class="d-none delivery-date col-12"></div>
                                </span>
                            </label>
                        </div>
                        <div class="calendar-modal modal modal-fullscreen-sm-down fade" id="datePickerModal" tabindex="-1" role="dialog" aria-labelledby="datePickerModalContent">
                            <div class="modal-dialog modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h6 class="modal-title" id="datePickerModalContent"><?php echo esc_html(__('Choose a delivery date', 'propeller-ecommerce-v2')); ?></h6>
                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="calendar-wrapper" id="calendar-wrapper"></div>
                                        <div id="editor"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
    <?php } else { ?>
        <?php /* <input type="hidden" name="delivery_select" value=" " /> */ ?>
    <?php } ?>
    <?php apply_filters('propel_checkout_regular_step_3_submit', $cart, $obj); ?>

</form>