<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;
use Propeller\PropellerHelper;

?>
<svg style="display:none;">
    <symbol viewBox="0 0 5 8" id="shape-arrow-left">
        <title>Arrow left</title>
        <path d="M4.173 7.85a.546.546 0 0 1-.771-.02L.149 4.375a.545.545 0 0 1 0-.75L3.402.17a.546.546 0 0 1 .792.75L1.276 4l2.918 3.08a.545.545 0 0 1-.021.77z" />
    </symbol>
    <symbol viewBox="0 0 5 8" id="shape-arrow-right">
        <title>Arrow right</title>
        <path d="M.827.15a.546.546 0 0 1 .771.02L4.851 3.625a.545.545 0 0 1 0 .75L1.598 7.83a.546.546 0 0 1-.792-.75L3.724 4 .806.92A.545.545 0 0 1 .827.15z" />
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
            <div class="propeller-account-table singlefavlistheading mb-5">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <h4 class="favorite-list-name"><?php echo esc_html($this->data->name); ?></h4>
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <p class="countproducts"><span class="favorite-products-count"><?php echo esc_html($this->data->products->itemsFound + $this->data->clusters->itemsFound); ?></span> <?php echo esc_html(__('products', 'propeller-ecommerce-v2')); ?></p>
                            </div>
                            <div class="col-12 col-md-6">
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
                    <div class="col-12 col-md-6">
                        <div class="row justify-end">
                            <div class="col-6 col-md-4 col-xl-3">
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
                            <div class="col-6 col-md-4 col-xl-3">
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

            <div class="row d-flex mt-5">
                <div class="col-3 d-flex align-items-center">
                    <input type="checkbox" class="fav-items-check-all" id="favs_all" />
                    <label for="favs_all"><?php echo esc_html(__('Select all', 'propeller-ecommerce-v2')); ?></label>
                </div>
                <div class="col-6">

                </div>
                <div class="col-3">

                </div>
            </div>

            <div class="propeller-account-list">
                <div class="propeller-account-table propeller-favorites-table mt-2" id="favorites_<?php echo esc_attr($this->data->id); ?>">
                    <?php
                    if (sizeof($this->products)) {
                        foreach ($this->products as $product) {
                            apply_filters('propel_account_favorites_' . strtolower($product->class) . '_card', $product, $this);
                        }
                    } else {
                    ?>
                        <h5>
                            <?php echo esc_html(__("No favorite items in this list", 'propeller-ecommerce-v2')); ?>
                        </h5>
                    <?php } ?>
                </div>

                <?php
                // Pagination
                $pagination_data = (object)[
                    'total_items' => $this->data->products->itemsFound + $this->data->clusters->itemsFound,
                    'offset' => 12,
                    'current_page' => 1,
                    'list_id' => $this->data->id
                ];

                apply_filters('propel_account_favorites_single_list_paging', $pagination_data, $this);
                ?>
            </div>

            <div class="propeller-add-to-favorites row mt-4">
                <div class="col-6">
                    <button class="btn btn-primary add-to-favorite-btn" data-bs-toggle="modal" data-bs-target="#addToList">
                        <?php echo esc_html(__('Add product directly to this wishlist', 'propeller-ecommerce-v2')); ?>
                    </button>
                </div>
            </div>

            <div class="favorites-bottom-panel-container container-fluid px-0" style="display: none;">
                <div class="favorites-bottom-panel">
                    <div class="row d-flex">
                        <div class="col-12 col-lg-2 my-auto">
                            <input type="checkbox" class="fav-items-check-all" id="favs_all_bottom" />
                            <label for="favs_all_bottom"><?php echo esc_html(__('Select all', 'propeller-ecommerce-v2')); ?></label>
                        </div>
                        <div class="col-12 col-lg-10">
                            <form method="post" class="favorites-bulk-form" action="#">
                                <div class="row g-3">
                                    <div class="col-12 col-md align-middle justify-items-center my-auto">
                                        <span class="favorites-selected"></span>
                                        <?php echo esc_html(__('of', 'propeller-ecommerce-v2')); ?>
                                        <span class="favorites-total"></span>
                                        <?php echo esc_html(__('items selected', 'propeller-ecommerce-v2')); ?>
                                    </div>
                                    <div class="col">
                                        <button type="button" data-bs-toggle="modal" data-bs-target="#remove_favorites" class="btn-favorites-bulk-action btn-remove-selected-favorites">
                                            <?php echo esc_html(__('Remove from this list', 'propeller-ecommerce-v2')); ?>
                                        </button>
                                    </div>
                                    <div class="col text-end">
                                        <button type="button" data-list_id="<?php echo esc_attr($this->data->id); ?>" data-action="cart_add_items_bulk" class="btn-favorites-bulk-action btn-favorites-addtobasket add-to-cart-selected-favorites">
                                            <svg class="icon icon-cart" aria-hidden="true">
                                                <use href="#shape-shopping-cart"></use>
                                            </svg>
                                            <?php echo esc_html(__('Add to cart', 'propeller-ecommerce-v2')); ?>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php apply_filters('propel_account_favorites_delete_favorite_list_modal', $this); ?>
<?php apply_filters('propel_account_favorites_rename_favorite_list_modal', $this); ?>
<?php apply_filters('propel_account_favorites_add_to_favorite_list_modal', $this); ?>
<?php apply_filters('propel_account_favorites_remove_favorite_items_modal', $this->data, $this); ?>
