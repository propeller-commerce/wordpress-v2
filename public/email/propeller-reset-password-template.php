<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<h1 style="font-size: 18px;"><?php echo esc_html(__('Your password reset link', 'propeller-ecommerce-v2')); ?></h1>
<p><?php echo esc_html(__("We received a request to reset your {{var:site.name}} password.",'propeller-ecommerce-v2')); ?><br>
<?php echo esc_html(__('Please use the following <a href="{{var:resetLink}}">link</a> to reset your password','propeller-ecommerce-v2')); ?><br><br>
<?php echo esc_html(__("If you haven't request a new password from us, no action is needed.",'propeller-ecommerce-v2')); ?></p>