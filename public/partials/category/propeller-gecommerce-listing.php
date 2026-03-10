<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Enum\PageType;
use Propeller\Includes\Enum\ProductClass;
use Propeller\Includes\Object\Product;
use Propeller\PropellerHelper;

?>
<script type="application/ld+json">
    {
        "@context": "http://schema.org",
        "@type": "ItemList",
        "numberOfItems": "<?php echo esc_html(count($products)); ?>",
        "itemListElement": [
            <?php
                foreach ($products as $key => $product) { 
                    if ($product->class == ProductClass::Cluster) {
                        $product = new Product($product->defaultProduct);
                    }
                    
                    if (!$product)
                        continue;
            ?>                
            {
                "@type": "ListItem",
                "position": "<?php echo esc_html($key + 1); ?>",
                "item": {
                    "@type": "Product",
                    "name": "<?php echo esc_html(str_replace('"', '\"', $product->name[0]->value)); ?>",
                    "url": "<?php echo esc_url(stripcslashes($obj->buildUrl(PageController::get_slug($product->class == ProductClass::Cluster ? PageType::CLUSTER_PAGE : PageType::PRODUCT_PAGE), $product->slug[0]->value, $product->urlId))); ?>",
                    <?php if($product->has_images()) { ?> "image": "<?php echo esc_url(stripcslashes($product->images[0]->images[0]->url)); ?>" <?php } ?>
                    "description":"<?php echo esc_html(isset($product->description[0]->value) ? str_replace('"', '\"', wp_strip_all_tags(trim(preg_replace('/\s+/', ' ', $product->description[0]->value)))) : ''); ?>",
                    "mpn":"<?php echo esc_html($product->manufacturerCode); ?>",
                    "sku":"<?php echo esc_html($product->sku); ?>",
                    "productId":"<?php echo esc_html($product->productId); ?>",
                    "category":"<?php echo esc_html(isset($product->category->name[0]->value) ? str_replace('"', '\"', $product->category->name[0]->value) : ''); ?>",
                    "brand": {
                        "@type": "Brand",
                        "name":"<?php echo esc_html($product->manufacturer) ?>"
                    }<?php if (!(!UserController::is_propeller_logged_in() && PROPELLER_WP_SEMICLOSED_PORTAL)) { ?>,
                    "offers": {
                        "@type": "Offer",
                        "url": "<?php echo esc_url(stripcslashes($obj->buildUrl(PageController::get_slug($product->class == ProductClass::Cluster ? PageType::CLUSTER_PAGE : PageType::PRODUCT_PAGE), $product->slug[0]->value, $product->urlId))); ?>",
                        "priceCurrency": "EUR",
                        "price": "<?php echo esc_html(PropellerHelper::formatPriceGTM($product->price->net)); ?>",
                        "itemCondition": "http://schema.org/NewCondition",
                        "availability": "http://schema.org/<?php echo esc_html($product->inventory && $product->inventory->totalQuantity > 0 ? "InStock" : "OutOfStock"); ?>"
                        
                    }<?php } ?>
                }
            }<?php if (count($products) > 1 AND $key + 1 < count($products)) echo esc_html(','); ?>
            <?php } ?>
        ]
    }
</script>