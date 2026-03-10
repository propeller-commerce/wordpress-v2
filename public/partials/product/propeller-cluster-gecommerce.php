<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Enum\PageType;
use Propeller\PropellerHelper;
 
if ($cluster->has_slug()) { ?>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org/",
        "@type": "Cluster",
        "name": "<?php echo esc_attr($cluster->name[0]->value); ?>",
        "url": "<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::CLUSTER_PAGE), $cluster->get_slug(), $cluster->urlId)); ?>",
        <?php if ($cluster_product->has_images()) { ?>
            "image": "<?php echo esc_url($cluster_product->images[0]->images[0]->url); ?>",
        <?php } ?>
        "description": "<?php echo esc_html(wp_strip_all_tags($cluster->description[0]->value)); ?>",
        <?php if (isset($cluster_product->manufacturerCode)) { ?>
            "mpn": "<?php echo esc_attr($cluster_product->manufacturerCode); ?>",
        <?php } ?>
        "sku": "<?php echo esc_attr($cluster->sku); ?>",
        "productId": "<?php echo esc_attr($cluster->clusterId); ?>",
        <?php if (isset($cluster->category) && $cluster->category) { ?>
            "category": "<?php echo esc_attr($cluster->category->name[0]->value); ?>",
        <?php } ?>
        <?php if (isset($cluster_product->manufacturer)) { ?>
            "brand": {
                "@type": "Brand",
                "name": "<?php echo esc_attr($cluster_product->manufacturer); ?>"
            }"
        <?php } ?>
        <?php if (!(!UserController::is_propeller_logged_in() && PROPELLER_WP_SEMICLOSED_PORTAL)) { ?>
            , "offers": {
                "@type": "Offer",
                "url": "<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::CLUSTER_PAGE), $cluster->get_slug(), $cluster->urlId)); ?>",
                "priceCurrency": "EUR",
                "price": "<?php echo esc_attr(PropellerHelper::formatPriceGTM($cluster_product->price->net)); ?>",
                "itemCondition": "http://schema.org/NewCondition",
                "availability": "http://schema.org/'<?php echo esc_attr((!(empty($cluster_product->inventory) && $cluster_product->inventory->totalQuantity > 0)? 'InStock':'OutOfStock')); ?>"
            }
        <?php } ?>
    }
    </script>
<?php } ?>
    
