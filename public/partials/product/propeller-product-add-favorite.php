<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="favorites">
    <div class="favorite-add-form">
        <button type="button" class="btn-favorite" rel="nofollow" 
                data-form-id="add_favorite_form" 
                data-bs-target="#add_favorite_modal_product_<?php echo esc_attr($product->productId); ?>"
                data-product="<?php echo esc_attr($product->productId); ?>"
                data-bs-toggle="modal">
            <svg class="icon icon-product-favorite icon-heart <?php echo esc_html( $found ? 'is-favorite' : '' ); ?>">
                <use class="header-shape-heart" xlink:href="#shape-favorites"></use>
            </svg>
        </button>
    </div>
</div>

<?php apply_filters('propel_account_add_favorite_modal', $product, $obj); ?>
