<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="row">
    <div class="col-12">
        <?php if(!empty($product->shortDescription)) { ?><div class="product-short-description">
                <?php echo wp_kses($product->shortDescription[0]->value, wp_kses_allowed_html('post')); ?>
            </div>
        <?php }?>
    </div>
</div>