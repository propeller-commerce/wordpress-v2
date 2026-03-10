<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php get_header(); ?>
<div class="d-flex align-items-center justify-content-center pt-5 pb-5">
    <div class="text-center">
        <h1 class="h1 display-1 fw-bold text-deafult-color">401</h1>
        <p class="fs-3 display-1"> <span class="text-deafult-color"><?php echo esc_html(__('Unauthorized', 'propeller-ecommerce-v2')); ?></p>
        <p class="mb-5 fs-16">
            <?php echo esc_html(__("You are not authorized to access this page.", "propeller-ecommerce-v2")); ?>
        </p>
        <a href="/" class="btn button-btn-primary btn-homepage"><?php echo esc_html(__('Back to homepage', 'propeller-ecommerce-v2')); ?></a>
    </div>
</div>
<?php get_footer(); ?>