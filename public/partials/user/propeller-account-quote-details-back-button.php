<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="back-link">
    <button onclick="history.back();">
        <svg class="icon icon-svg icon-arrow-left" aria-hidden="true">
            <use class="icon-shape-arrow-left" xlink:href="#shape-arrow-left"></use>
        </svg>
        <?php echo esc_html( __('Back to quote overview', 'propeller-ecommerce-v2') ); ?>
    </button>
</div>