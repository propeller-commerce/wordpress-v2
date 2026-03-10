<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php get_header(); ?>
<div class="d-flex align-items-center justify-content-center pt-5 pb-5">
    <div class="text-center">
        <h1 class="h1 display-1 fw-bold text-deafult-color">404</h1>
        <p class="display-4"> <span class="text-deafult-color"><?php echo esc_html(__("We’re sorry, we can’t show you this page at the moment.", 'propeller-ecommerce-v2')); ?></span></p>
        <p class="display-4 fs-16">
            <?php echo esc_html(__("Please return to the", "propeller-ecommerce-v2")); ?> <a href="/" class="btn-404"><?php echo esc_html(__("homepage", "propeller-ecommerce-v2")); ?></a> <?php echo esc_html(__("or try again in a little while.", "propeller-ecommerce-v2")); ?>
        </p>

    </div>
</div>
<?php get_footer(); ?>