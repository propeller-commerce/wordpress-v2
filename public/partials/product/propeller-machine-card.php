<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="card propeller-product-card">
    <svg style="display: none;">
        <symbol viewBox="0 0 23 20" id="shape-shopping-cart">
            <title><?php echo esc_html(__('Shopping cart', 'propeller-ecommerce-v2')); ?></title>
            <path d="M18.532 20c.72 0 1.325-.24 1.818-.723a2.39 2.39 0 0 0 .739-1.777c0-.703-.253-1.302-.76-1.797a.899.899 0 0 0-.339-.508 1.002 1.002 0 0 0-.619-.195H7.55l-.48-2.5h13.26a.887.887 0 0 0 .58-.215.995.995 0 0 0 .34-.527l1.717-8.125a.805.805 0 0 0-.18-.781.933.933 0 0 0-.739-.352H5.152L4.832.781a.99.99 0 0 0-.338-.566.947.947 0 0 0-.62-.215H.48a.468.468 0 0 0-.34.137.45.45 0 0 0-.14.332V.78c0 .13.047.241.14.332a.468.468 0 0 0 .34.137h3.155L6.43 15.82c-.452.47-.679 1.042-.679 1.72 0 .676.247 1.256.74 1.737.492.482 1.098.723 1.817.723.719 0 1.324-.24 1.817-.723.493-.481.739-1.074.739-1.777 0-.443-.12-.86-.36-1.25h5.832c-.24.39-.36.807-.36 1.25 0 .703.246 1.296.74 1.777.492.482 1.097.723 1.816.723zm1.518-8.75H6.83l-1.438-7.5h16.256l-1.598 7.5zm-11.742 7.5c-.347 0-.646-.124-.899-.371s-.38-.54-.38-.879c0-.339.127-.632.38-.879s.552-.371.899-.371c.346 0 .645.124.898.371s.38.54.38.879c0 .339-.127.632-.38.879s-.552.371-.898.371zm10.224 0c-.346 0-.645-.124-.898-.371s-.38-.54-.38-.879c0-.339.127-.632.38-.879s.552-.371.898-.371c.347 0 .646.124.899.371s.38.54.38.879c0 .339-.127.632-.38.879s-.552.371-.899.371z" fill-rule="nonzero" />
        </symbol>
    </svg>
    <figure class="card-img-top">
        <div class="product-labels">&nbsp;</div>
        <div class="product-card-image">

            <a href="<?php echo esc_url($obj->buildMachineUrl($_SERVER['REQUEST_URI'], $machine->slug[0]->value)); ?>">
                <?php
                if ($machine->has_images()) { ?>
                    <img class="img-fluid"
                        src="<?php echo esc_url($machine->images[0]->images[0]->url); ?>"
                        alt="<?php echo esc_attr((count($machine->images[0]->alt) ? $machine->images[0]->alt[0]->value : "")); ?>"
                        width="<?php echo esc_html(PROPELLER_PRODUCT_IMG_CATALOG_WIDTH); ?>" height="<?php echo esc_html(PROPELLER_PRODUCT_IMG_CATALOG_HEIGHT); ?>">
                <?php } else { ?>
                    <img class="img-fluid"
                        src="<?php echo esc_url($obj->assets_url . '/img/no-image-card.webp'); ?>"
                        alt="<?php echo esc_attr(__('No image found', 'propeller-ecommerce-v2')); ?>"
                        width="300" height="300">
                <?php } ?>
            </a>
        </div>
    </figure>
    <div class="card-body product-card-description">
        <div class="product-name">
            <a href="<?php echo esc_url($obj->buildMachineUrl($_SERVER['REQUEST_URI'], $machine->slug[0]->value)); ?>">
                <?php echo esc_html($machine->name[0]->value); ?>
            </a>
            <?php
            if ($machine->has_documents()) {
                foreach ($machine->documents as $doc) {
            ?>
                    <a class="machine-download-link mt-2" href="<?php echo esc_url($doc->documents[0]->originalUrl); ?>" download target="_blank">
                        <?php
                        if (isset($doc->alt))
                            echo esc_html($doc->alt);
                        else if (isset($doc->description) && count($doc->description))
                            echo esc_html($machine->description[0]->value);
                        else
                            echo esc_html(__("Download document", "propeller-ecommerce-v2"));
                        ?>
                    </a>
            <?php
                }
            }
            ?>
        </div>
    </div>
    <div class="card-footer product-card-footer">
        <?php if (!(!\Propeller\Includes\Controller\UserController::is_propeller_logged_in() && PROPELLER_WP_SEMICLOSED_PORTAL)) { ?>
            <?php if (isset($machine->parts) && is_array($machine->parts) && count($machine->parts)) { ?>
                <div class="product-price">
                    <div class="product-current-price has-discount d-md-inline-flex"><?php echo esc_html(__('Total parts', 'propeller-ecommerce-v2')); ?>: <?php echo esc_html(count($machine->parts)); ?></div>
                </div>
        <?php }
        } ?>
        <!-- Include the order button template -->
        <div class="add-to-basket-wrapper">
            <div class="add-to-basket">
                <a class="btn btn-addtobasket d-flex align-items-center justify-content-center" href="<?php echo esc_url($obj->buildMachineUrl($_SERVER['REQUEST_URI'], $machine->slug[0]->value)); ?>">
                    <svg class="d-flex d-md-none icon icon-cart" aria-hidden="true">
                        <use xlink:href="#shape-shopping-cart"></use>
                    </svg>
                    <span class="d-none d-md-flex text"><?php echo esc_html(__('View', 'propeller-ecommerce-v2')); ?></span>
                </a>
            </div>
        </div>
    </div>
</div>