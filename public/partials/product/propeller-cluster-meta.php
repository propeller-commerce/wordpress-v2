<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;

?>
<div class="product-meta d-none d-md-flex">
    <?php if ($cluster->category) { ?>
        <span class="product-category"><a href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::CATEGORY_PAGE), $cluster->category->slug[0]->value, $cluster->category->urlId)); ?>"><?php echo esc_html($cluster->category->name[0]->value); ?></a></span>
    <?php } ?>
    <span class="product-code"><?php echo esc_html( __('SKU', 'propeller-ecommerce-v2') ); ?>: <?php if (!empty($cluster->sku)) echo esc_html($cluster->sku);
                                                                                    else echo esc_html($cluster_product->sku); ?></span>
</div>