<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;
use Propeller\Includes\Controller\UserController;
use Propeller\PropellerHelper;

?>
<svg style="display:none;">
    <symbol viewBox="0 0 5 8" id="shape-arrow-left">
        <title>Arrow left</title>
        <path d="M4.173 7.85a.546.546 0 0 1-.771-.02L.149 4.375a.545.545 0 0 1 0-.75L3.402.17a.546.546 0 0 1 .792.75L1.276 4l2.918 3.08a.545.545 0 0 1-.021.77z" />
    </symbol>
    <symbol viewBox="0 0 23 20" id="shape-shopping-cart">
        <title>Shopping cart</title>
        <path d="M18.532 20c.72 0 1.325-.24 1.818-.723a2.39 2.39 0 0 0 .739-1.777c0-.703-.253-1.302-.76-1.797a.899.899 0 0 0-.339-.508 1.002 1.002 0 0 0-.619-.195H7.55l-.48-2.5h13.26a.887.887 0 0 0 .58-.215.995.995 0 0 0 .34-.527l1.717-8.125a.805.805 0 0 0-.18-.781.933.933 0 0 0-.739-.352H5.152L4.832.781a.99.99 0 0 0-.338-.566.947.947 0 0 0-.62-.215H.48a.468.468 0 0 0-.34.137.45.45 0 0 0-.14.332V.78c0 .13.047.241.14.332a.468.468 0 0 0 .34.137h3.155L6.43 15.82c-.452.47-.679 1.042-.679 1.72 0 .676.247 1.256.74 1.737.492.482 1.098.723 1.817.723.719 0 1.324-.24 1.817-.723.493-.481.739-1.074.739-1.777 0-.443-.12-.86-.36-1.25h5.832c-.24.39-.36.807-.36 1.25 0 .703.246 1.296.74 1.777.492.482 1.097.723 1.816.723zm1.518-8.75H6.83l-1.438-7.5h16.256l-1.598 7.5zm-11.742 7.5c-.347 0-.646-.124-.899-.371s-.38-.54-.38-.879c0-.339.127-.632.38-.879s.552-.371.899-.371c.346 0 .645.124.898.371s.38.54.38.879c0 .339-.127.632-.38.879s-.552.371-.898.371zm10.224 0c-.346 0-.645-.124-.898-.371s-.38-.54-.38-.879c0-.339.127-.632.38-.879s.552-.371.898-.371c.347 0 .646.124.899.371s.38.54.38.879c0 .339-.127.632-.38.879s-.552.371-.899.371z" fill-rule="nonzero" />
    </symbol>
    <symbol viewBox="0 0 10 10" id="shape-delete">
        <title>Delete</title>
        <path d="M1.282.22 5 3.937 8.718.22A.751.751 0 1 1 9.78 1.282L6.063 5 9.78 8.718A.751.751 0 0 1 8.718 9.78L5 6.063 1.282 9.78A.751.751 0 1 1 .22 8.718L3.937 5 .22 1.282A.751.751 0 0 1 1.282.22z" fill="#005FAD" fill-rule="evenodd" />
    </symbol>
</svg>
<div class="container-fluid px-0 propeller-account-wrapper propeller-favorites-wrapper">
    <script type="application/ld+json">
        {
            "@context": "http://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [{
                    "@type": "ListItem",
                    "position": 1,
                    "item": {
                        "@type": "Thing",
                        "@id": "<?php echo esc_url(home_url()); ?>",
                        "name": "<?php echo esc_attr(__("Home", "propeller-ecommerce-v2")); ?>"
                    }
                },
                {
                    "@type": "ListItem",
                    "position": 2,
                    "item": {
                        "@type": "Thing",
                        "@id": "<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::MY_ACCOUNT_PAGE))); ?>",
                        "name": "<?php echo esc_attr(__("My account", "propeller-ecommerce-v2")); ?>"
                    }
                },
                {
                    "@type": "ListItem",
                    "position": 3,
                    "item": {
                        "@type": "Thing",
                        "@id": "<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::ORDERLIST_PAGE))); ?>",
                        "name": "<?php echo esc_attr(__("Order list", "propeller-ecommerce-v2")); ?>
                    }
                }
            ]
        }
    </script>

    <div class="row">
        <div class="col">
            <?php
            $breadcrumb_paths = [
                [
                    $this->buildUrl('', PageController::get_slug(PageType::MY_ACCOUNT_MOBILE_PAGE)),
                    __('My account', 'propeller-ecommerce-v2')
                ],
                [
                    $this->buildUrl('', PageController::get_slug(PageType::MY_ACCOUNT_PAGE)),
                    __('My account', 'propeller-ecommerce-v2')
                ],
                [
                    $this->buildUrl('', PageController::get_slug(PageType::ORDERLIST_PAGE)),
                    __('Order list', 'propeller-ecommerce-v2')
                ]
            ];

            apply_filters('propel_breadcrumbs', $breadcrumb_paths);
            ?>
        </div>
    </div>
    <div class="row">
        <?php echo esc_html(apply_filters('propel_my_account_title', __('My account', 'propeller-ecommerce-v2'))); ?>
    </div>
    <div class="row">
        <div class="col-12 col-lg-3">
            <?php echo esc_html(apply_filters('propel_my_account_menu', $this)); ?>
        </div>
        <div class="col-12 col-lg-9">
            <div class="propeller-account-table propeller-favorites-table">
                <h4><?php echo esc_html(__('Order list', 'propeller-ecommerce-v2')); ?></h4>
                <?php ?>

                <div class="order-headers d-none d-md-flex">
                    <div class="row w-100 align-items-center">
                        <div class="col-md-5 description">
                            <?php echo esc_html(__('Products', 'propeller-ecommerce-v2')); ?>
                        </div>
                        <div class="col-md-2 status">
                            <?php echo esc_html(__('Status', 'propeller-ecommerce-v2')); ?>
                        </div>
                        <div class="col-md-2 price-per-item">
                            <?php echo esc_html(__('Price', 'propeller-ecommerce-v2')); ?>
                        </div>
                        <div class="col-md-3 quantity text-center">
                            <?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?>
                        </div>
                    </div>
                </div>
                <div class="order-product-item">
                    <div class="row g-0 align-items-start">
                        <div class="col-2 col-md-1 col-lg-1 px-22 product-image order-1">
                            <a href="#">
                                <img class="img-fluid" src="<?php echo esc_url($this->assets_url . '/img/no-image-card.webp'); ?>" alt="product-name">
                            </a>
                        </div>
                        <div class="col-9 col-md-3 col-xl-4 pe-5 product-description order-2">
                            <a class="product-name" href="#">
                                Lenovo ThinkPad L13 Yoga Gen 2 (20VK003UMH)
                            </a>
                            <div class="product-sku">
                                <?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>: 12345
                            </div>

                        </div>
                        <div class="offset-2 offset-md-0 col-9 col-md-2 stock-status in-stock order-md-3 order-last">
                            <span class="stock"><?php echo esc_html(__('Available', 'propeller-ecommerce-v2')); ?>:</span> <span class="stock-total">123</span>
                        </div>
                        <div class="offset-2 offset-md-0 col-4 col-md-2 price-per-item order-4">
                            <div class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                                430,00
                            </div>
                            <small><?php echo esc_html(__('excl. VAT', 'propeller-ecommerce-v2')); ?></small>
                        </div>
                        <div class="col-6 col-md-3 mb-4 order-5">
                            <form class="add-to-basket-form d-flex justify-content-end" name="add-product" method="post">
                                <input type="hidden" name="product_id" value="">
                                <input type="hidden" name="action" value="cart_add_item">
                                <div class="input-group product-quantity">
                                    <label class="visually-hidden" for="quantity-item-1"><?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?></label>
                                    <input
                                        type="number"
                                        id="quantity-item-1"
                                        class="quantity large form-control input-number"
                                        name="quantity"
                                        value="1"
                                        autocomplete="off"
                                        min=""
                                        data-min=""
                                        data-unit="">
                                </div>
                                <button class="btn-addtobasket d-flex align-items-center justify-content-center" type="submit">
                                    <svg class="icon icon-cart" aria-hidden="true">
                                        <use xlink:href="#shape-shopping-cart"></use>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="order-product-item">
                    <div class="row g-0 align-items-start">
                        <div class="col-2 col-md-1 col-lg-1 px-22 product-image order-1">
                            <a href="#">
                                <img class="img-fluid" src="<?php echo esc_url($this->assets_url . '/img/no-image-card.webp'); ?>" alt="product-name">
                            </a>
                        </div>
                        <div class="col-9 col-md-3 col-xl-4 pe-5 product-description order-2">
                            <a class="product-name" href="#">
                                ASUS BR1100FKA-BP0328R - QWERTY Laptop - Grijs
                            </a>
                            <div class="product-sku">
                                <?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>: 54654654
                            </div>

                        </div>
                        <div class="offset-2 offset-md-0 col-9 col-md-2 stock-status in-stock order-md-3 order-last">
                            <span class="stock"><?php echo esc_html(__('Available', 'propeller-ecommerce-v2')); ?>:</span> <span class="stock-total">123</span>
                        </div>
                        <div class="offset-2 offset-md-0 col-4 col-md-2 price-per-item order-4">
                            <div class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                                686,00
                            </div>
                            <small><?php echo esc_html(__('excl. VAT', 'propeller-ecommerce-v2')); ?></small>
                        </div>
                        <div class="col-6 col-md-3 mb-4 order-5">
                            <form class="add-to-basket-form d-flex justify-content-end" name="add-product" method="post">
                                <input type="hidden" name="product_id" value="">
                                <input type="hidden" name="action" value="cart_add_item">
                                <div class="input-group product-quantity">
                                    <label class="visually-hidden" for="quantity-item-1"><?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?></label>
                                    <input
                                        type="number"
                                        id="quantity-item-1"
                                        class="quantity large form-control input-number"
                                        name="quantity"
                                        value="1"
                                        autocomplete="off"
                                        min=""
                                        data-min=""
                                        data-unit="">
                                </div>
                                <button class="btn-addtobasket d-flex align-items-center justify-content-center" type="submit">
                                    <svg class="icon icon-cart" aria-hidden="true">
                                        <use xlink:href="#shape-shopping-cart"></use>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="order-product-item">
                    <div class="row g-0 align-items-start">
                        <div class="col-2 col-md-1 col-lg-1 px-22 product-image order-1">
                            <a href="#">
                                <img class="img-fluid" src="<?php echo esc_url($this->assets_url . '/img/no-image-card.webp'); ?>" alt="product-name">
                            </a>
                        </div>
                        <div class="col-9 col-md-3 col-xl-4 pe-5 product-description order-2">
                            <a class="product-name" href="#">
                                Blijf overal productief met de Acer Chromebook Enterprise Spin
                            </a>
                            <div class="product-sku">
                                <?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>: 4656545
                            </div>

                        </div>
                        <div class="offset-2 offset-md-0 col-9 col-md-2 stock-status in-stock order-md-3 order-last">
                            <span class="stock"><?php echo esc_html(__('Available', 'propeller-ecommerce-v2')); ?>:</span> <span class="stock-total">123</span>
                        </div>
                        <div class="offset-2 offset-md-0 col-4 col-md-2 price-per-item order-4">
                            <div class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                                450,00
                            </div>
                            <small><?php echo esc_html(__('excl. VAT', 'propeller-ecommerce-v2')); ?></small>
                        </div>
                        <div class="col-6 col-md-3 mb-4 order-5">
                            <form class="add-to-basket-form d-flex justify-content-end" name="add-product" method="post">
                                <input type="hidden" name="product_id" value="">
                                <input type="hidden" name="action" value="cart_add_item">
                                <div class="input-group product-quantity">
                                    <label class="visually-hidden" for="quantity-item-1"><?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?></label>
                                    <input
                                        type="number"
                                        id="quantity-item-1"
                                        class="quantity large form-control input-number"
                                        name="quantity"
                                        value="1"
                                        autocomplete="off"
                                        min=""
                                        data-min=""
                                        data-unit="">
                                </div>
                                <button class="btn-addtobasket d-flex align-items-center justify-content-center" type="submit">
                                    <svg class="icon icon-cart" aria-hidden="true">
                                        <use xlink:href="#shape-shopping-cart"></use>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="order-product-item">
                    <div class="row g-0 align-items-start">
                        <div class="col-2 col-md-1 col-lg-1 px-22 product-image order-1">
                            <a href="#">
                                <img class="img-fluid" src="<?php echo esc_url($this->assets_url . '/img/no-image-card.webp'); ?>" alt="product-name">
                            </a>
                        </div>
                        <div class="col-9 col-md-3 col-xl-4 pe-5 product-description order-2">
                            <a class="product-name" href="#">
                                HP ProBook 450 G8 Notebook 39,6 cm (15.6") Full HD
                            </a>
                            <div class="product-sku">
                                <?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>: 6544564
                            </div>

                        </div>
                        <div class="offset-2 offset-md-0 col-9 col-md-2 stock-status in-stock order-md-3 order-last">
                            <span class="stock"><?php echo esc_html(__('Available', 'propeller-ecommerce-v2')); ?>:</span> <span class="stock-total">123</span>
                        </div>
                        <div class="offset-2 offset-md-0 col-4 col-md-2 price-per-item order-4">
                            <div class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                                1.016,00
                            </div>
                            <small><?php echo esc_html(__('excl. VAT', 'propeller-ecommerce-v2')); ?></small>
                        </div>
                        <div class="col-6 col-md-3 mb-4 order-5">
                            <form class="add-to-basket-form d-flex justify-content-end" name="add-product" method="post">
                                <input type="hidden" name="product_id" value="">
                                <input type="hidden" name="action" value="cart_add_item">
                                <div class="input-group product-quantity">
                                    <label class="visually-hidden" for="quantity-item-1"><?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?></label>
                                    <input
                                        type="number"
                                        id="quantity-item-1"
                                        class="quantity large form-control input-number"
                                        name="quantity"
                                        value="1"
                                        autocomplete="off"
                                        min=""
                                        data-min=""
                                        data-unit="">
                                </div>
                                <button class="btn-addtobasket d-flex align-items-center justify-content-center" type="submit">
                                    <svg class="icon icon-cart" aria-hidden="true">
                                        <use xlink:href="#shape-shopping-cart"></use>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="order-product-item">
                    <div class="row g-0 align-items-start">
                        <div class="col-2 col-md-1 col-lg-1 px-22 product-image order-1">
                            <a href="#">
                                <img class="img-fluid" src="<?php echo esc_url($this->assets_url . '/img/no-image-card.webp'); ?>" alt="product-name">
                            </a>
                        </div>
                        <div class="col-9 col-md-3 col-xl-4 pe-5 product-description order-2">
                            <a class="product-name" href="#">
                                Logitech Keyboard K120 for Business toetsenbord USB ĄŽERTY Litouws Zwart
                            </a>
                            <div class="product-sku">
                                <?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>: 1321545
                            </div>

                        </div>
                        <div class="offset-2 offset-md-0 col-9 col-md-2 stock-status in-stock order-md-3 order-last">
                            <span class="stock"><?php echo esc_html(__('Available', 'propeller-ecommerce-v2')); ?>:</span> <span class="stock-total">123</span>
                        </div>
                        <div class="offset-2 offset-md-0 col-4 col-md-2 price-per-item order-4">
                            <div class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                                12,33
                            </div>
                            <small><?php echo esc_html(__('excl. VAT', 'propeller-ecommerce-v2')); ?></small>
                        </div>
                        <div class="col-6 col-md-3 mb-4 order-5">
                            <form class="add-to-basket-form d-flex justify-content-end" name="add-product" method="post">
                                <input type="hidden" name="product_id" value="">
                                <input type="hidden" name="action" value="cart_add_item">
                                <div class="input-group product-quantity">
                                    <label class="visually-hidden" for="quantity-item-1"><?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?></label>
                                    <input
                                        type="number"
                                        id="quantity-item-1"
                                        class="quantity large form-control input-number"
                                        name="quantity"
                                        value="1"
                                        autocomplete="off"
                                        min=""
                                        data-min=""
                                        data-unit="">
                                </div>
                                <button class="btn-addtobasket d-flex align-items-center justify-content-center" type="submit">
                                    <svg class="icon icon-cart" aria-hidden="true">
                                        <use xlink:href="#shape-shopping-cart"></use>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="order-product-item">
                    <div class="row g-0 align-items-start">
                        <div class="col-2 col-md-1 col-lg-1 px-22 product-image order-1">
                            <a href="#">
                                <img class="img-fluid" src="<?php echo esc_url($this->assets_url . '/img/no-image-card.webp'); ?>" alt="product-name">
                            </a>
                        </div>
                        <div class="col-9 col-md-3 col-xl-4 pe-5 product-description order-2">
                            <a class="product-name" href="#">
                                Logitech Rally Plus Ultra HD conferentie camera systeem
                            </a>
                            <div class="product-sku">
                                <?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>: 564654
                            </div>

                        </div>
                        <div class="offset-2 offset-md-0 col-9 col-md-2 stock-status in-stock order-md-3 order-last">
                            <span class="stock"><?php echo esc_html(__('Available', 'propeller-ecommerce-v2')); ?>:</span> <span class="stock-total">123</span>
                        </div>
                        <div class="offset-2 offset-md-0 col-4 col-md-2 price-per-item order-4">
                            <div class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                                2.300,00
                            </div>
                            <small><?php echo esc_html(__('excl. VAT', 'propeller-ecommerce-v2')); ?></small>
                        </div>
                        <div class="col-6 col-md-3 mb-4 order-5">
                            <form class="add-to-basket-form d-flex justify-content-end" name="add-product" method="post">
                                <input type="hidden" name="product_id" value="">
                                <input type="hidden" name="action" value="cart_add_item">
                                <div class="input-group product-quantity">
                                    <label class="visually-hidden" for="quantity-item-1"><?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?></label>
                                    <input
                                        type="number"
                                        id="quantity-item-1"
                                        class="quantity large form-control input-number"
                                        name="quantity"
                                        value="1"
                                        autocomplete="off"
                                        min=""
                                        data-min=""
                                        data-unit="">
                                </div>
                                <button class="btn-addtobasket d-flex align-items-center justify-content-center" type="submit">
                                    <svg class="icon icon-cart" aria-hidden="true">
                                        <use xlink:href="#shape-shopping-cart"></use>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="order-product-item">
                    <div class="row g-0 align-items-start">
                        <div class="col-2 col-md-1 col-lg-1 px-22 product-image order-1">
                            <a href="#">
                                <img class="img-fluid" src="<?php echo esc_url($this->assets_url . '/img/no-image-card.webp'); ?>" alt="product-name">
                            </a>
                        </div>
                        <div class="col-9 col-md-3 col-xl-4 pe-5 product-description order-2">
                            <a class="product-name" href="#">
                                HP ProBook 450 G8 Notebook 39,6 cm (15.6") Full HD
                            </a>
                            <div class="product-sku">
                                <?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>: 6544564
                            </div>

                        </div>
                        <div class="offset-2 offset-md-0 col-9 col-md-2 stock-status in-stock order-md-3 order-last">
                            <span class="stock"><?php echo esc_html(__('Available', 'propeller-ecommerce-v2')); ?>:</span> <span class="stock-total">123</span>
                        </div>
                        <div class="offset-2 offset-md-0 col-4 col-md-2 price-per-item order-4">
                            <div class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                                1.016,00
                            </div>
                            <small><?php echo esc_html(__('excl. VAT', 'propeller-ecommerce-v2')); ?></small>
                        </div>
                        <div class="col-6 col-md-3 mb-4 order-5">
                            <form class="add-to-basket-form d-flex justify-content-end" name="add-product" method="post">
                                <input type="hidden" name="product_id" value="">
                                <input type="hidden" name="action" value="cart_add_item">
                                <div class="input-group product-quantity">
                                    <label class="visually-hidden" for="quantity-item-1"><?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?></label>
                                    <input
                                        type="number"
                                        id="quantity-item-1"
                                        class="quantity large form-control input-number"
                                        name="quantity"
                                        value="1"
                                        autocomplete="off"
                                        min=""
                                        data-min=""
                                        data-unit="">
                                </div>
                                <button class="btn-addtobasket d-flex align-items-center justify-content-center" type="submit">
                                    <svg class="icon icon-cart" aria-hidden="true">
                                        <use xlink:href="#shape-shopping-cart"></use>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="order-product-item">
                    <div class="row g-0 align-items-start">
                        <div class="col-2 col-md-1 col-lg-1 px-22 product-image order-1">
                            <a href="#">
                                <img class="img-fluid" src="<?php echo esc_url($this->assets_url . '/img/no-image-card.webp'); ?>" alt="product-name">
                            </a>
                        </div>
                        <div class="col-9 col-md-3 col-xl-4 pe-5 product-description order-2">
                            <a class="product-name" href="#">
                                Philips 276C8 - QHD USB-C IPS Monitor - 27 Inch
                            </a>
                            <div class="product-sku">
                                <?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>: 644654656
                            </div>

                        </div>
                        <div class="offset-2 offset-md-0 col-9 col-md-2 stock-status in-stock order-md-3 order-last">
                            <span class="stock"><?php echo esc_html(__('Available', 'propeller-ecommerce-v2')); ?>:</span> <span class="stock-total">123</span>
                        </div>
                        <div class="offset-2 offset-md-0 col-4 col-md-2 price-per-item order-4">
                            <div class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                                319,33
                            </div>
                            <small><?php echo esc_html(__('excl. VAT', 'propeller-ecommerce-v2')); ?></small>
                        </div>
                        <div class="col-6 col-md-3 mb-4 order-5">
                            <form class="add-to-basket-form d-flex justify-content-end" name="add-product" method="post">
                                <input type="hidden" name="product_id" value="">
                                <input type="hidden" name="action" value="cart_add_item">
                                <div class="input-group product-quantity">
                                    <label class="visually-hidden" for="quantity-item-1"><?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?></label>
                                    <input
                                        type="number"
                                        id="quantity-item-1"
                                        class="quantity large form-control input-number"
                                        name="quantity"
                                        value="1"
                                        autocomplete="off"
                                        min=""
                                        data-min=""
                                        data-unit="">
                                </div>
                                <button class="btn-addtobasket d-flex align-items-center justify-content-center" type="submit">
                                    <svg class="icon icon-cart" aria-hidden="true">
                                        <use xlink:href="#shape-shopping-cart"></use>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php ?>

            </div>

        </div>

    </div>

</div>