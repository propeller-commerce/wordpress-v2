<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\PropellerHelper;

// var_dump($payMethod);
?>
<div class="col-6 col-md-3 mb-4">
    <label class="form-check-label paymethod">
        <span class="row d-flex align-items-center text-center">
            <input type="radio" name="payMethod" id="payMethod_<?php echo esc_attr($payMethod->code); ?>" value="<?php echo esc_attr($payMethod->code); ?>" title="<?php echo esc_attr(__('Select paymethod', 'propeller-ecommerce-v2')); ?>" required="required" data-rule-required="true" required="required" aria-required="true" class="required" />
            <div class="paymethod-img col-12">
                <svg class="icon icon-paymethod-logo" aria-hidden="true">
                    <use xlink:href="#shape-<?php echo esc_attr($payMethod->name); ?>"></use>
                </svg>
            </div>
            <div class="paymethod-name col-12">
                <?php
                $paymethods_array = new \WP_Query(array(
                    'post_type' => 'paymethods'
                ));

                $matched = false;

                foreach ($paymethods_array->posts as $post) {
                    if ($payMethod->code === $post->post_title) {
                        echo esc_html($post->post_excerpt);
                        $matched = true;
                        break;
                    }
                }

                if (!$matched) {
                    echo esc_html($payMethod->name);
                }
                ?>

            </div>
            <div class="paymethod-cost col-12"><span class="currency"></span></div>
        </span>
    </label>
</div>