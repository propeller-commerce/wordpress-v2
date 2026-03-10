<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\PropellerHelper;
use Propeller\Includes\Enum\PageType;
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
                        "@id": "<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::FAVORITES_PAGE))); ?>",
                        "name": "<?php echo esc_attr(__("Favorites", "propeller-ecommerce-v2")); ?>
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
                    $this->buildUrl('', PageController::get_slug(PageType::FAVORITES_PAGE)),
                    __('Favorites', 'propeller-ecommerce-v2')
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
            <div class="propeller-account-table singlefavlistheading">
                <div class="row">
                    <div class="col-6">
                        <h4><?php echo esc_html($this->data->name); ?></h4>
                        <div class="row">
                            <div class="col-3">
                                <p class="countproducts"><?php if ((sizeof($this->data->products->items) > 0) || (sizeof($this->data->clusters->items) > 0)) {
                                                                echo esc_html((count($this->data->products->items) + count($this->data->clusters->items)));
                                                            } ?> <?php echo esc_html(__('products', 'propeller-ecommerce-v2')); ?></p>
                            </div>
                            <div class="col-6">
                                <?php
                                $dateString = $this->data->updatedAt;
                                $date = new DateTime($dateString, new DateTimeZone('UTC'));
                                $monthNumber = (int) $date->format('n');
                                $months = PropellerHelper::months();
                                $monthName = $months[$monthNumber];
                                ?>
                                <p class="updatedat"><?php echo esc_html(__('Last modified:', 'propeller-ecommerce-v2')); ?> <?php echo esc_html($monthName) . ' ';
                                                                                                                                echo esc_html(gmdate('d, Y', strtotime($this->data->updatedAt))); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="row justify-end">
                            <div class="col-3">
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#renameList"> <svg fill="#2f99dd" height="20px" width="20px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 306.637 306.637" xml:space="preserve">
                                        <g>
                                            <g>
                                                <path d="M12.809,238.52L0,306.637l68.118-12.809l184.277-184.277l-55.309-55.309L12.809,238.52z M60.79,279.943l-41.992,7.896 l7.896-41.992L197.086,75.455l34.096,34.096L60.79,279.943z" />
                                                <path d="M251.329,0l-41.507,41.507l55.308,55.308l41.507-41.507L251.329,0z M231.035,41.507l20.294-20.294l34.095,34.095 L265.13,75.602L231.035,41.507z" />
                                            </g>
                                            <g></g>
                                            <g></g>
                                            <g></g>
                                            <g></g>
                                            <g></g>
                                            <g></g>
                                            <g></g>
                                            <g></g>
                                            <g></g>
                                            <g></g>
                                            <g></g>
                                            <g></g>
                                            <g></g>
                                            <g></g>
                                            <g></g>
                                        </g>
                                    </svg> <?php echo esc_html(__('Rename', 'propeller-ecommerce-v2')); ?></button>
                            </div>
                            <div class="col-3">
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#deleteList"><svg fill="#2f99dd" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20px" height="20px" viewBox="0 0 41.336 41.336" xml:space="preserve">
                                        <g>
                                            <path d="M36.335,5.668h-8.167V1.5c0-0.828-0.672-1.5-1.5-1.5h-12c-0.828,0-1.5,0.672-1.5,1.5v4.168H5.001c-1.104,0-2,0.896-2,2 s0.896,2,2,2h2.001v29.168c0,1.381,1.119,2.5,2.5,2.5h22.332c1.381,0,2.5-1.119,2.5-2.5V9.668h2.001c1.104,0,2-0.896,2-2 S37.438,5.668,36.335,5.668z M14.168,35.67c0,0.828-0.672,1.5-1.5,1.5s-1.5-0.672-1.5-1.5v-21c0-0.828,0.672-1.5,1.5-1.5 s1.5,0.672,1.5,1.5V35.67z M22.168,35.67c0,0.828-0.672,1.5-1.5,1.5s-1.5-0.672-1.5-1.5v-21c0-0.828,0.672-1.5,1.5-1.5 s1.5,0.672,1.5,1.5V35.67z M25.168,5.668h-9V3h9V5.668z M30.168,35.67c0,0.828-0.672,1.5-1.5,1.5s-1.5-0.672-1.5-1.5v-21 c0-0.828,0.672-1.5,1.5-1.5s1.5,0.672,1.5,1.5V35.67z" />
                                        </g>
                                    </svg><?php echo esc_html(__('Delete', 'propeller-ecommerce-v2')); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="propeller-account-table propeller-favorites-table">
                <?php foreach ($this->data as $products) {
                    foreach ($products->items as $product) { ?>
                        <div class="order-product-item">
                            <div class="row g-0 align-items-start">
                                <div class="col-2 col-md-1 col-lg-1 px-22 product-image order-1">
                                    <a href="/product/<?php echo esc_html($product->slug[0]->value); ?>">
                                        <?php if ($product->media->images->items[0]->images[0]->originalUrl) { ?>
                                            <img class="img-fluid" src="<?php echo esc_url($product->media->images->items[0]->images[0]->originalUrl); ?>" alt="product-name">
                                        <?php } else { ?>
                                            <img class="img-fluid" src="<?php echo esc_url($this->assets_url . '/img/no-image-card.webp'); ?>" alt="product-name">
                                        <?php } ?>
                                    </a>
                                </div>
                                <div class="col-9 col-md-3 col-xl-4 pe-5 product-description order-2">
                                    <a class="product-name" href="/product/<?php echo esc_html($product->slug[0]->value); ?>">
                                        <?php echo esc_html($product->name[0]->value); ?>
                                    </a>
                                    <div class="product-sku">
                                        <?php echo esc_html(__('SKU', 'propeller-ecommerce-v2')); ?>: <?php echo esc_html($product->sku); ?>
                                    </div>
                                </div>
                                <div class="offset-2 offset-md-0 col-9 col-md-2 stock-status in-stock order-md-3 order-last">
                                    <span class="stock"><?php echo esc_html(__('Available', 'propeller-ecommerce-v2')); ?>:</span> <span class="stock-total"><?php echo esc_html($product->price->quantity); ?></span>
                                </div>
                                <div class="offset-2 offset-md-0 col-4 col-md-2 price-per-item order-4">
                                    <div class="price"><span class="symbol"><?php echo esc_html(PropellerHelper::currency()); ?>&nbsp;</span>
                                        <?php echo esc_html(PropellerHelper::formatPrice($product->price->gross)); ?>
                                    </div>
                                    <small><?php echo esc_html(__('excl. VAT', 'propeller-ecommerce-v2')); ?></small>
                                </div>
                                <div class="col-6 col-md-3 col-xl-2 mb-4 order-5">
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
                                <div class="col-1 d-flex align-items-center justify-content-end order-3 order-md-last">
                                    <!-- <form name="delete-product" method="post" class="delete-favorite-item-form"> -->
                                    <input type="hidden" name="item_id" value="">
                                    <input type="hidden" name="action" value="favorite_delete_item">
                                    <div class="input-group">
                                        <button class="btn-delete d-flex align-items-start align-items-md-center justify-content-end removeProductFavList" product-id="<?php echo esc_html($product->productId); ?>">
                                            <svg class="icon icon-delete" aria-hidden="true">
                                                <use xlink:href="#shape-delete"></use>
                                            </svg>
                                        </button>
                                    </div>
                                    <!-- </form>            -->
                                </div>
                            </div>
                        </div>
                <?php
                    }
                } ?>
            </div>
            <div class="addproductwrap">
                <div class="clickadd">
                    <p> <svg class="minus d-none" version="1.2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19 6" width="19" height="6">
                            <path id="Layer" fill-rule="evenodd" class="s0" d="m2.5 2.8q0-0.2 0.1-0.3 0-0.2 0.1-0.3 0.1-0.1 0.3-0.1 0.1-0.1 0.3-0.1h13.4q0.4 0 0.6 0.2 0.2 0.2 0.2 0.6 0 0.3-0.2 0.5-0.2 0.2-0.6 0.2h-13.5q-0.1 0-0.2-0.1-0.2 0-0.3-0.1-0.1-0.1-0.1-0.3-0.1-0.1-0.1-0.2z" />
                        </svg> <svg class="plus" fill="#fff" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 45.402 45.402" xml:space="preserve">
                            <g>
                                <path d="M41.267,18.557H26.832V4.134C26.832,1.851,24.99,0,22.707,0c-2.283,0-4.124,1.851-4.124,4.135v14.432H4.141 c-2.283,0-4.139,1.851-4.138,4.135c-0.001,1.141,0.46,2.187,1.207,2.934c0.748,0.749,1.78,1.222,2.92,1.222h14.453V41.27 c0,1.142,0.453,2.176,1.201,2.922c0.748,0.748,1.777,1.211,2.919,1.211c2.282,0,4.129-1.851,4.129-4.133V26.857h14.435 c2.283,0,4.134-1.867,4.133-4.15C45.399,20.425,43.548,18.557,41.267,18.557z" />
                            </g>
                        </svg><?php echo esc_html(__('Add product directly to this list', 'propeller-ecommerce-v2')); ?></p>
                </div>
                <div id="optionsWrap" class="optionsWrap d-none">
                    <var id="result-container" class="result-container"></var>
                    <input type="hidden" name="action" value="search">
                    <div class="input-group">
                        <label for="term-1<?php echo esc_attr($search_id); ?>" class="visually-hidden"><?php echo esc_html(__('Search by product', 'propeller-ecommerce-v2')); ?></label>
                        <input
                            id="searchfavProducts"
                            type="search"
                            name="term"
                            class="form-control"
                            listId="<?php echo esc_html($this->data->id); ?>"
                            data-list_id="<?php echo esc_attr($this->data->id); ?>"
                            data-update_list="1"
                            placeholder="<?php echo esc_html(__('Search by product', 'propeller-ecommerce-v2')); ?>"
                            value=""
                            autocomplete="off">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal Delete List -->
<div class="modal fade modalm" id="deleteList" tabindex="-1" role="dialog" aria-labelledby="deleteListLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteListLabel"><?php echo esc_html(__('Delete list', 'propeller-ecommerce-v2')); ?></h5>
                <!-- <button type="button" class="closebtn" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button> -->
            </div>
            <div class="modal-body">
                <h4><?php echo esc_html(__('Are you sure you want to delete this favorite list?', 'propeller-ecommerce-v2')); ?></h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo esc_html(__('Close', 'propeller-ecommerce-v2')); ?></button>
                <button type="button" class="btn btn-primary" id="deleteListbtn"><?php echo esc_html(__('Delete', 'propeller-ecommerce-v2')); ?></button>
            </div>
        </div>
    </div>
</div>


<!-- Modal Rename List -->
<div class="modal fade modalm" id="renameList" tabindex="-1" role="dialog" aria-labelledby="renameListLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="renameListLabel"><?php echo esc_html(__('Rename favorites list', 'propeller-ecommerce-v2')); ?></h4>
            </div>
            <div class="modal-body">
                <input type="text" id="listNameval" placeholder="<?php echo esc_html(__('Favorites list name...', 'propeller-ecommerce-v2')); ?>">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo esc_html(__('Close', 'propeller-ecommerce-v2')); ?></button>
                <button type="button" class="btn btn-primary" id="renameListbtn"><?php echo esc_html(__('Rename', 'propeller-ecommerce-v2')); ?></button>
            </div>
        </div>
    </div>
</div>