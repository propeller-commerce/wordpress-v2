<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;

?>

<div class="product-meta d-flex d-md-none">
    <span class="product-category"><a href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::CATEGORY_PAGE), $product->category->slug[0]->value, $product->category->urlId)); ?>"><?php echo esc_html($product->category->name[0]->value); ?></a></span>
    <span class="product-code"><?php echo esc_html( __('SKU', 'propeller-ecommerce-v2') ); ?>: <?php echo esc_html($product->sku); ?></span>
    <input type="hidden" id="productId" value="<?php echo esc_attr($product->productId); ?>">
</div>