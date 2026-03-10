<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;
use Propeller\Includes\Object\Product;
use Propeller\PropellerHelper;

$countries = propel_get_countries();

?>

<svg style="display:none;">
    <symbol viewBox="0 0 5 8" id="shape-arrow-left">
        <title>Arrow left</title>
        <path d="M4.173 7.85a.546.546 0 0 1-.771-.02L.149 4.375a.545.545 0 0 1 0-.75L3.402.17a.546.546 0 0 1 .792.75L1.276 4l2.918 3.08a.545.545 0 0 1-.021.77z" />
    </symbol>
</svg>
<div class="container-fluid px-0 propeller-account-wrapper">
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
                        "@id": "<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::ORDERS_PAGE))); ?>",
                        "name": "<?php echo esc_attr(__("My orders", "propeller-ecommerce-v2")); ?>
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
                    $this->buildUrl('', PageController::get_slug(PageType::ORDERS_PAGE)),
                    __('My orders', 'propeller-ecommerce-v2')
                ]
            ];

            apply_filters('propel_breadcrumbs', $breadcrumb_paths);
            ?>
        </div>
    </div>
    <div class="row">

        <?php apply_filters('propel_my_account_title', __('My account', 'propeller-ecommerce-v2')); ?>

    </div>
    <div class="row">
        <div class="col-12 col-lg-3">

            <?php apply_filters('propel_my_account_menu', $this); ?>

        </div>
        <div class="col-12 col-lg-9">
            <div class="propeller-account-table">

                <?php apply_filters('propel_order_details_back_button', $this); ?>

                <?php apply_filters('propel_order_details_title', $this->order); ?>

                <?php apply_filters('propel_order_details_data', $this->order, $this); ?>

                <?php echo esc_html(apply_filters('propel_order_details_shipments', $this->order)); ?>

                <div class="row">

                    <?php apply_filters('propel_address_box', $this->order->invoiceAddress[0], $this, __('Billing address', 'propeller-ecommerce-v2'), true); ?>

                    <?php apply_filters('propel_address_box', $this->order->deliveryAddress[0], $this, __('Delivery address', 'propeller-ecommerce-v2'), true); ?>

                </div>

                <div class="row order-products">
                    <div class="col-12">
                        <?php
                        $count = 0;

                        foreach ($this->order->items as $item) {

                            if (
                                $item->class === 'product' &&
                                $item->isBonus !== 'Y' &&
                                empty($item->parentOrderItemId)
                            )
                                $count++;
                        }
                        ?>
                        <h5><?php echo esc_html(__('Order overview', 'propeller-ecommerce-v2')); ?> (<?php echo esc_html($count); ?> <?php echo esc_html(__('items', 'propeller-ecommerce-v2')); ?>)</h5>
                    </div>
                </div>

                <?php apply_filters('propel_order_details_overview_headers', $this->order); ?>

                <?php apply_filters('propel_order_details_overview_items', $this->order->items, $this); ?>

                <?php apply_filters('propel_order_details_overview_bonus_items', $this->order->items, $this); ?>

                <?php apply_filters('propel_order_details_totals', $this->order, $this); ?>

            </div>
        </div>
    </div>
</div>