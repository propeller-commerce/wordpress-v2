<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="propeller-account-wrapper">
    <h2><?php

        use Propeller\Includes\Controller\SessionController;

        echo esc_html(SessionController::get(PROPELLER_CONTACT_COMPANY_NAME)); ?></h2>
</div>