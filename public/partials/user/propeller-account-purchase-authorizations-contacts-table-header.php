<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<svg style="display: none;">
    <symbol viewBox="0 0 14 10" id="shape-checkmark">
        <title><?php echo esc_html(__('Checkmark', 'propeller-ecommerce-v2')); ?></title>
        <path d="M11.918.032 4.725 7.225 2.082 4.582a.328.328 0 0 0-.464 0l-.773.773a.328.328 0 0 0 0 .465l3.648 3.648a.328.328 0 0 0 .464 0l8.198-8.198a.328.328 0 0 0 0-.464l-.773-.774a.328.328 0 0 0-.464 0z" />
    </symbol>
    <symbol viewBox="0 0 24 24" id="shape-plus">
        <title><?php echo esc_html(__('Plus', 'propeller-ecommerce-v2')); ?></title>
        <g fill="none" stroke="#fff" stroke-width="3" stroke-linecap="square" stroke-miterlimit="10">
            <path d="M12 2v20M22 12H2" />
        </g>
    </symbol>
    <symbol viewBox="0 0 24 24" id="shape-minus">
        <title><?php echo esc_html(__('Minus', 'propeller-ecommerce-v2')); ?></title>
        <path fill="none" stroke="#fff" stroke-linecap="square" stroke-miterlimit="10" d="M22 12H2" stroke-width="3" />
    </symbol>
</svg>
<div class="row d-none d-md-flex orders-header g-0">
    <div class="col-md-1"><?php echo esc_html(__('ID', 'propeller-ecommerce-v2')); ?></div>
    <div class="col-md-3"><?php echo esc_html(__('Contact', 'propeller-ecommerce-v2')); ?></div>
    <div class="col-md-3"><?php echo esc_html(__('Role', 'propeller-ecommerce-v2')); ?></div>
    <div class="col-md-3"><?php echo esc_html(__('Authorization limit', 'propeller-ecommerce-v2')); ?></div>
    <div class="col-md-2 text-end"><?php if (PROPELLER_PAC_ADD_CONTACTS) echo esc_html(__('Actions', 'propeller-ecommerce-v2'));
                                    else echo ''; ?></div>
</div>