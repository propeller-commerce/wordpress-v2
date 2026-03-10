<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<form name="newsletter-subscribe-form" class="newsletter-subscribe-form validate form-handler" method="post">
    <input type="hidden" name="action" value="submit_newsletter_form">
    <label class="visually-hidden" for="footer-newsletter"><?php echo esc_html( __('E-mail address', 'propeller-ecommerce-v2') ); ?></label>
    <div class="input-group">
        <input type="email" name="user_mail" id="footer-newsletter" class="form-control required email" value="" placeholder="<?php echo esc_html( __('E-mail address', 'propeller-ecommerce-v2') ); ?>">
        <button type="submit" class="btn-email"><?php echo esc_html( __('Sign up', 'propeller-ecommerce-v2') ); ?></button>
    </div>
</form>