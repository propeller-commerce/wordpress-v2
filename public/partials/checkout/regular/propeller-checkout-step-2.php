<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<svg style="display: none;">
    <symbol viewBox="0 0 18 21" id="shape-checkout-edit">
        <title><?php echo esc_html(__('Edit', 'propeller-ecommerce-v2')); ?></title>
        <g fill="none" fill-rule="evenodd">
            <path d="M17.34 1.978 16.023.659A2.242 2.242 0 0 0 14.432 0c-.577 0-1.152.22-1.592.659L.452 13.047l-.447 4.016a.844.844 0 0 0 .932.932l4.012-.444L17.341 5.16a2.25 2.25 0 0 0 0-3.182zM4.434 16.477l-3.27.362.363-3.275 9.278-9.277 2.91 2.91-9.281 9.28zM16.546 4.364l-2.037 2.037-2.91-2.91 2.037-2.037c.212-.212.495-.329.795-.329.3 0 .583.117.796.33l1.319 1.318a1.127 1.127 0 0 1 0 1.591z" fill="#000" fill-rule="nonzero" />
            <path stroke="#28a745" stroke-linecap="round" d="M.5 20.5h17" />
        </g>
    </symbol>
    <symbol viewBox="0 0 28 32" id="shape-calendar">
        <title><?php echo esc_html(__('Calendar', 'propeller-ecommerce-v2')); ?></title>
        <path d="M25 4h-3V.75a.752.752 0 0 0-.75-.75h-.5a.752.752 0 0 0-.75.75V4H8V.75A.752.752 0 0 0 7.25 0h-.5A.752.752 0 0 0 6 .75V4H3a3 3 0 0 0-3 3v22a3 3 0 0 0 3 3h22a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3zM3 6h22c.55 0 1 .45 1 1v3H2V7c0-.55.45-1 1-1zm22 24H3c-.55 0-1-.45-1-1V12h24v17c0 .55-.45 1-1 1zM9.25 20c.412 0 .75-.338.75-.75v-2.5a.752.752 0 0 0-.75-.75h-2.5a.752.752 0 0 0-.75.75v2.5c0 .412.338.75.75.75h2.5zm6 0c.412 0 .75-.338.75-.75v-2.5a.752.752 0 0 0-.75-.75h-2.5a.752.752 0 0 0-.75.75v2.5c0 .412.337.75.75.75h2.5zm6 0c.412 0 .75-.338.75-.75v-2.5a.752.752 0 0 0-.75-.75h-2.5a.752.752 0 0 0-.75.75v2.5c0 .412.337.75.75.75h2.5zm-6 6c.412 0 .75-.338.75-.75v-2.5a.752.752 0 0 0-.75-.75h-2.5a.752.752 0 0 0-.75.75v2.5c0 .412.337.75.75.75h2.5zm-6 0c.412 0 .75-.338.75-.75v-2.5a.752.752 0 0 0-.75-.75h-2.5a.752.752 0 0 0-.75.75v2.5c0 .412.338.75.75.75h2.5zm12 0c.412 0 .75-.338.75-.75v-2.5a.752.752 0 0 0-.75-.75h-2.5a.752.752 0 0 0-.75.75v2.5c0 .412.337.75.75.75h2.5z" />
    </symbol>
</svg>
<div class="propeller-checkout-wrapper">

    <?php

    use Propeller\Includes\Controller\SessionController;

    echo wp_kses_post(apply_filters('propel_checkout_regular_page_title', $this->cart, $this)); ?>

    <div class="container-fluid px-0">
        <div class="row">
            <div class="col-12 col-lg-8">
                <div class="checkout-wrapper-steps">

                    <?php echo wp_kses_post(apply_filters('propel_checkout_step_1_info', $this->cart, $this)); ?>

                </div>
                <div class="checkout-wrapper-steps">

                    <?php echo wp_kses_post(apply_filters('propel_checkout_regular_step_2_titles', $this->cart, $this)); ?>

                    <div class="row">
                        <div class="col-12 col-md-10">

                            <?php echo wp_kses_post(apply_filters('propel_checkout_regular_step_2_form', $this->cart, $this, $slug)); ?>

                        </div>
                    </div>
                </div>

                <?php if (SessionController::get(PROPELLER_ORDER_STATUS_TYPE) != 'REQUEST')
                    echo wp_kses_post(apply_filters('propel_checkout_regular_step_2_other_steps', $this->cart, $this));
                ?>

            </div>

            <?php echo wp_kses_post(apply_filters('propel_shopping_cart_totals', $this->cart, $this)); ?>

        </div>
    </div>
</div>