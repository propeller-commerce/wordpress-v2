<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<svg style="display: none;">
    <symbol viewBox="0 0 20 21" id="shape-zoom">
        <title><?php echo esc_html(__('Zoom', 'propeller-ecommerce-v2')); ?></title>
        <path d="m19.863 18.816-4.742-4.742a.464.464 0 0 0-.332-.136h-.516A8.124 8.124 0 0 0 8.125.5 8.124 8.124 0 0 0 0 8.624a8.124 8.124 0 0 0 13.437 6.148v.516a.48.48 0 0 0 .137.332l4.742 4.742a.47.47 0 0 0 .664 0l.883-.883a.47.47 0 0 0 0-.664zM8.125 14.875a6.248 6.248 0 0 1-6.25-6.25 6.248 6.248 0 0 1 6.25-6.25 6.248 6.248 0 0 1 6.25 6.25 6.248 6.248 0 0 1-6.25 6.25z" />
    </symbol>
</svg>
<div class='row'>
    <div class="product-image col-12 col-md-8 mx-md-auto col-lg-9 order-1 order-lg-2 ps-lg-0">
        <div class="product-labels">
            <?php if ($product->has_attributes()) {
                // Do whatever with attrs
            }
            ?>
        </div>


        <div class="gallery-container" id="gallery-container">
            <div id="slick-gallery" class="slick-gallery">
                <?php

                if ($product->has_images()) {
                    foreach ($product->images as $images) {
                        foreach ($images->images as $image) { ?>
                            <div class="gallery-item-slick">
                                <a href='<?php echo esc_url($image->url); ?>' data-size="800x800">
                                    <img <?php if ($lazy_load_images) { ?>
                                        src='<?php echo esc_url($obj->assets_url . '/img/no-image.webp'); ?>'
                                        data-src='<?php echo esc_url($image->url); ?>'
                                        class="d-block mx-auto img-fluid lazy"
                                        <?php } else { ?>
                                        src='<?php echo esc_url($image->url); ?>'
                                        class="d-block mx-auto img-fluid"
                                        <?php } ?>
                                        alt='<?php echo esc_attr((count($images->alt) ? esc_attr($images->alt[0]->value) : "")); ?>'
                                        width="450" height="450">
                                    <span class="zoom-link">
                                        <svg class="icon icon-zoom" aria-hidden="true">
                                            <use xlink:href="#shape-zoom"></use>
                                        </svg>
                                    </span>
                                </a>
                            </div>
                    <?php }
                    }
                } else { ?>
                    <div class="gallery-item-slick">
                        <a href='<?php echo esc_url($obj->assets_url . '/img/no-image.webp'); ?>' data-size="450x450">
                            <img src='<?php echo esc_url($obj->assets_url . '/img/no-image.webp'); ?>' class="d-block mx-auto img-fluid" alt='<?php echo esc_attr(__('No image found', 'propeller-ecommerce-v2')); ?>' width="450" height="450">
                            <span class="zoom-link">
                                <svg class="icon icon-zoom" aria-hidden="true">
                                    <use xlink:href="#shape-zoom"></use>
                                </svg>
                            </span>
                        </a>
                    </div>
                <?php } ?>
            </div>
        </div>
        <!-- Root element of PhotoSwipe. Must have class pswp. -->
        <div class="pswp" tabindex="-1" role="dialog" aria-hidden="true" id="pswp">
            <div class="pswp__bg"></div>
            <div class="pswp__scroll-wrap">
                <div class="pswp__container">
                    <div class="pswp__item"></div>
                    <div class="pswp__item"></div>
                    <div class="pswp__item"></div>
                </div>
                <div class="pswp__ui pswp__ui--hidden">
                    <div class="pswp__top-bar">
                        <div class="pswp__counter"></div>
                        <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
                        <button class="pswp__button pswp__button--share" title="Share"></button>
                        <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
                        <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
                        <div class="pswp__preloader">
                            <div class="pswp__preloader__icn">
                                <div class="pswp__preloader__cut">
                                    <div class="pswp__preloader__donut"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                        <div class="pswp__share-tooltip"></div>
                    </div>
                    <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">
                    </button>
                    <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">
                    </button>
                    <div class="pswp__caption">
                        <div class="pswp__caption__center"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div id="thumbnail-slick" class="product-thumbnail-slider-slick col-12 col-md-8 mx-md-auto col-lg-3 order-2 order-lg-1 d-none d-md-block">
        <div class="product-thumbnail-slick <?php if ($product->has_images() && count($product->images) <= 3 or empty($product->images)) echo 'single-thumbnail' ?>" id="product-thumb-slick">
            <?php
            $keyThumbImg = 0;

            if ($product->has_images()) {
                foreach ($product->images as $images) {
                    foreach ($images->images as $image) {
            ?>
                        <div class="item">
                            <div class="image">
                                <img <?php if ($lazy_load_images) { ?>
                                    src="<?php echo esc_url($obj->assets_url . '/img/no-image-small.webp'); ?>"
                                    data-src="<?php echo esc_url($image->url); ?>"
                                    class="lazy"
                                    <?php } else { ?>
                                    src="<?php echo esc_url($image->url); ?>"
                                    <?php } ?>
                                    alt="<?php echo esc_attr((count($images->alt) ? $images->alt[0]->value : "")); ?>"
                                    <?php if ($keyThumbImg > 4) { ?> <?php } ?> width="120" height="120" />
                            </div>
                        </div>
                <?php }
                    $keyThumbImg++;
                }
            } else { ?>
                <div class="item">
                    <div class="image">
                        <img src="<?php echo esc_url($obj->assets_url . '/img/no-image-small.webp'); ?>" <?php if ($keyThumbImg > 3) { ?> loading="lazy" <?php } ?> alt="<?php echo esc_attr(__('No image found', 'propeller-ecommerce-v2')); ?>" width="120" height="120" />
                    </div>
                </div>
            <?php } ?>
        </div>

    </div>
</div>