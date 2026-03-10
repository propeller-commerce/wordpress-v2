<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="row px-2 d-flex row g-3 form-check paymethods radios-container">
    <?php

    use Propeller\Includes\Controller\SessionController;

    foreach ($pay_methods as $payMethod) {

        $is_guest = (
            SessionController::has(PROPELLER_CART)
            && isset(SessionController::get(PROPELLER_CART)->customerId)
            && SessionController::get(PROPELLER_CART)->customerId == PROPELLER_ANONYMOUS_USER
        );

        $is_rekening = isset($payMethod->code) && $payMethod->code === 'REKENING';

        if ($is_guest && $is_rekening) {
            continue;
        }

        echo wp_kses_post(apply_filters('propel_checkout_paymethod', $payMethod, $cart, $obj));
    }

    ?>
</div>