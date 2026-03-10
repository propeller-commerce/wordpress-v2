<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div id="propel_toast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="4000">
  <div class="toast-header">
    <div class="propel-toast-body"><?php echo esc_html(__('Propeller', 'propeller-ecommerce-v2')); ?></div>
    <button type="button" class="close" data-bs-dismiss="toast" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
</div>