<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Enum\PageType;
use Propeller\Includes\Object\Product;
use Propeller\PropellerHelper;

?>
<?php if($cluster_product->isBundleLeader == 'Y') { ?>   
    <div class="row product-bundles <?php echo esc_html( apply_filters('propel_product_bundles_classes', '') ); ?>">
        <div class="col-12">
            <h2 class="product-info-title mt-5 mb-4"><?php echo esc_html( __('Recomended combo deals', 'propeller-ecommerce-v2') ); ?></h2>
        </div>
        <div class="col-12">
            <?php if(!empty($cluster_product->bundles)) { 
                foreach($cluster_product->bundles as $bundle) {
            ?>

                <div class="product-bundle-wrapper">
                    <div class="row g-0">
                        <div class="col-12 col-lg-9">
                            <div class="row g-0">
                                <?php $bundleClass = 'col-12 col-md-4 d-md-flex'; ?>
                                <?php foreach ($bundle->items as $key => $bundleItem) { ?> 
                                    <?php $bundleItem->product = new Product($bundleItem->product); ?>
                                    <div class="<?php echo esc_attr($bundleClass); if($bundleItem->isLeader != 'Y') echo ' bundle-item-col'; ?> px-4 pt-2 pb-4">
                                        <div class="card propeller-product-card">
                                            <figure class="card-img-top">
                                                <div class="product-card-image">					
                                                    <!-- build the product urls with the classId of the product (temporary) -->
                                                    
                                                    <a href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $bundleItem->product->slug[0]->value, $bundleItem->product->urlId)); ?>">
                                                        <?php if($bundleItem->product->has_images()) {?>
                                                            <img class="img-fluid" 
                                                                src="<?php echo esc_url($bundleItem->product->images[0]->images[0]->url); ?>"
                                                                alt="<?php echo esc_attr($bundleItem->product->images[0]->images[0]->alt[0]->value); ?>"
                                                                loading="lazy"
                                                                width="140" 
                                                                height="140">
                                                        <?php }
                                                            else { ?>
                                                            <img class="img-fluid" 
                                                                src="<?php echo esc_url($obj->assets_url . '/img/no-image-card.webp'); ?>"
                                                                alt="<?php echo esc_attr( __('No image found', 'propeller-ecommerce-v2') ); ?>"
                                                                loading="lazy"
                                                                width="300" height="300">
                                                        <?php } ?>
                                                    </a>   
                                                </div>
                                            </figure>                                            
                                            <div class="card-body product-card-description">
                                                <div class="product-name">
                                                    <!-- build the product urls with the classId of the product (temporary) -->
                                                    <a href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::PRODUCT_PAGE), $bundleItem->product->slug[0]->value, $bundleItem->product->urlId)); ?>">
                                                        <?php echo esc_html($bundleItem->product->name[0]->value); ?>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="card-footer product-card-footer">
                                                <?php if (!(!UserController::is_propeller_logged_in() && PROPELLER_WP_SEMICLOSED_PORTAL)) { ?> 
                                                    <!-- Include the price display template -->
                                                    <div class="product-price"> 
                                                        <div class="product-current-price"><span class="price"><span class="symbol"><?php echo esc_html( PropellerHelper::currency() ); ?>&nbsp;</span><?php echo esc_html( PropellerHelper::formatPrice($bundleItem->product->price->gross) ); ?><small><?php echo esc_html( __('excl. VAT', 'propeller-ecommerce-v2') ); ?></small></span></div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <?php if(sizeof($bundle->items) != ($key + 1)) { ?>
                                            <div class="bundle-icon">
                                                <div class="bundle-icon-border"></div>
                                                <div class="bundle-icon-svg">
                                                    <svg class="icon icon-plus">
                                                        <use class="shape-plus" xlink:href="#shape-plus"></use>
                                                    </svg>
                                                </div>
                                                <div class="bundle-icon-border"></div>
                                            </div>
                                        <?php } ?>
                                    </div>                                            
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-12 col-lg-3 d-flex justify-content-end bundle-details-col">
                            <div class="bundle-icon">
                                <div class="bundle-icon-border"></div>
                                <div class="bundle-icon-svg">
                                <svg class="icon icon-equals">
                                    <use class="shape-equals" xlink:href="#shape-equals"></use>
                                </svg>
                                </div>
                                <div class="bundle-icon-border"></div>
                            </div>
                            <div class="bundle-wrapper-details">
                                <div class="bundle-desc">
                                    <div class="bundle-title"><?php echo esc_html($bundle->name); ?></div>
                                    <?php if(!empty($bundle->description)) { ?>
                                        <div class="bundle-description"><?php echo esc_html( $bundle->description ); ?></div>
                                    <?php } ?>
                                </div> 
                                <?php if (!(!UserController::is_propeller_logged_in() && PROPELLER_WP_SEMICLOSED_PORTAL)) { ?>                                
                                    <div class="add-to-basket"> 
                                        <div class="bundle-prices">
                                            <?php if( $bundle->price->originalGross != $bundle->price->gross ) { ?>
                                                <div class="bundle-old-price">
                                                    <span class="price"><?php echo esc_html( PropellerHelper::formatPrice($bundle->price->originalGross) ); ?></span>
                                                </div>
                                            <?php } ?>
                                            <div class="bundle-current-price"> 
                                                <span class="price"><span class="symbol"><?php echo esc_html( PropellerHelper::currency() ); ?>&nbsp;</span><?php echo esc_html( PropellerHelper::formatPrice($bundle->price->gross) ); ?><small><?php echo esc_html( __('excl. VAT', 'propeller-ecommerce-v2') ); ?></small></span> 	
                                            </div>                                    
                                        </div>
                                        <div class="discount-add-to-basket row align-items-center">
                                            <?php if( $bundle->price->originalGross != $bundle->price->gross ) { 
                                                $bundleDiscount = $bundle->price->originalGross - $bundle->price->gross; ?>
                                                <div class="col-12">
                                                    <div class="discount">
                                                        <?php echo esc_html( __("Your savings",'propeller-ecommerce-v2') ); ?>: <span class="price"><?php echo esc_html( PropellerHelper::formatPrice($bundleDiscount) ); ?></span>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <div class="col-12">
                                                <form name="add-to-basket-bundle" method="post" class="add-to-basket-bundle-form">
                                                    <input type="hidden" name="bundle_id" value="<?php echo esc_attr($bundle->comboId); ?>">
                                                    <input type="hidden" name="quantity" value="1">
                                                    <input type="hidden" name="action" value="cart_add_bundle">
                                                
                                                    <button type="submit" class="btn btn-addtobasket d-flex align-items-center justify-content-center">
                                                        <svg class="d-flex d-lg-none icon icon-cart" aria-hidden="true">
                                                            <use xlink:href="#shape-shopping-cart"></use>
                                                        </svg>    
                                                        <span class="d-none d-lg-flex text"><?php echo esc_html( __('In cart', 'propeller-ecommerce-v2') ); ?></span>
                                                    </button> 
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } }?> 
        </div>
    </div>
<?php } ?>
