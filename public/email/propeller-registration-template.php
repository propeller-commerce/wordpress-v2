<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php /* translators: %s: Blog name */ ?>
<h1 style="font-size: 18px;"><?php echo esc_html(sprintf(__('Welcome to %s', 'propeller-ecommerce-v2'), get_bloginfo('name'))); ?></h1>
<p>
    <?php /* translators: %s: User's first name */ ?>
    <?php echo esc_html(sprintf(__("Dear %s,", 'propeller-ecommerce-v2'), $user_data->firstName)); ?><br>
    <?php /* translators: %s: Blog name */ ?>
    <?php echo esc_html(sprintf(__("Thank you for registering with %s.", 'propeller-ecommerce-v2'), get_bloginfo('name'))); ?><br />
    <?php echo esc_html(__("With your account you can place an order, check your account history and manage your details.", 'propeller-ecommerce-v2')); ?><br />
    <?php /* translators: %s: Blog login URL */ ?>
    <?php echo wp_kses_post(sprintf(__('You can log in using your chosen username and password using this <a href="%s" target="_blank">link</a>.', 'propeller-ecommerce-v2'), $login_url)); ?><br />
</p>