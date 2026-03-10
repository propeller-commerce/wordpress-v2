<?php
if ( ! defined( 'ABSPATH' ) ) exit;
// More robust check for description content - same logic as in desc-media template
$hasDescription = false;
if (
    isset($product->description) && is_array($product->description) &&
    count($product->description) > 0 &&
    isset($product->description[0]->value) &&
    !empty(trim(wp_strip_all_tags($product->description[0]->value)))
) {
    $hasDescription = true;
}

if ($hasDescription) { ?>
    <div id="pane-description" class="product-pane">
        <div class="row">
            <div class="col-12">
                <?php echo wp_kses($product->description[0]->value, wp_kses_allowed_html('post')); ?>
            </div>
        </div>
    </div>
<?php } ?>
