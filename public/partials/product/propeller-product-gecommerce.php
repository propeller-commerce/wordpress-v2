<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Enum\PageType;
use Propeller\PropellerHelper;

?>

<script type="application/ld+json">
{
    "@context": "https://schema.org/",
    "@type": "Product",
    "name": "<?php echo esc_attr( str_replace('"', '\"', $product->name[0]->value) ); ?>",
    "url": "<?php echo esc_url( stripcslashes($obj->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $product->slug[0]->value, $product->urlId)) ); ?>",
    <?php if ($product->has_images()) { ?>
        "image": "<?php echo esc_url($product->images[0]->images[0]->url); ?>",
    <?php } ?> 
    "description": "<?php echo esc_attr( str_replace('"', '\"', wp_strip_all_tags(trim(preg_replace('/\s+/', ' ', $product->description[0]->value)))) ); ?>",
    "mpn": "<?php echo esc_attr( $product->manufacturerCode ); ?>",
    "sku": "<?php echo esc_attr( $product->sku ); ?>",
    "productId":"<?php echo esc_attr( $product->productId ); ?>",
    "category": "<?php echo esc_attr( isset($product->category->name[0]->value) ? wp_strip_all_tags($product->category->name[0]->value) : '' ); ?>",
    "brand": {
        "@type": "Brand",
        "name": "<?php echo esc_attr( $product->manufacturer ); ?>"
    }<?php if (!(!UserController::is_propeller_logged_in() && PROPELLER_WP_SEMICLOSED_PORTAL)) {?>,
    "offers": {
        "@type": "Offer",
        "url": "<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $product->slug[0]->value, $product->urlId)); ?>",
        "priceCurrency": "EUR",
        "price": "<?php echo esc_attr( PropellerHelper::formatPriceGTM($product->price->net) ); ?>",
        "itemCondition": "http://schema.org/NewCondition",
        "availability": "http://schema.org/<?php echo esc_attr( !empty($product->inventory) && $product->inventory->totalQuantity > 0 ? 'InStock' : 'OutOfStock' ); ?>"
    }
    <?php } ?>
}
</script>