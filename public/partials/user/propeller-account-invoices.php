<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;
?>
<svg style="display:none;">
    <symbol viewBox="0 0 5 8" id="shape-arrow-left">
        <title>Arrow left</title>
        <path d="M4.173 7.85a.546.546 0 0 1-.771-.02L.149 4.375a.545.545 0 0 1 0-.75L3.402.17a.546.546 0 0 1 .792.75L1.276 4l2.918 3.08a.545.545 0 0 1-.021.77z" />
    </symbol>
    <symbol viewBox="0 0 12 16" id="shape-download">
        <title>PDF file</title>
        <g>
            <path d="M10.5 16c.417 0 .77-.146 1.062-.438.292-.291.438-.645.438-1.062V4.125c0-.417-.146-.77-.438-1.062L8.937.438A1.443 1.443 0 007.875 0H1.5C1.083 0 .73.146.437.438A1.446 1.446 0 000 1.5v13c0 .417.146.77.437 1.062.292.292.646.438 1.063.438h9zM11 4H8V1a.87.87 0 01.219.156l2.625 2.625A.87.87 0 0111 4zm-.5 11h-9a.49.49 0 01-.36-.14.49.49 0 01-.14-.36v-13a.49.49 0 01.14-.36A.49.49 0 011.5 1H7v3.25c0 .208.073.385.219.531A.723.723 0 007.75 5H11v9.5a.49.49 0 01-.14.36.49.49 0 01-.36.14zm-7.809-1.997L2.812 13c.292-.042.579-.219.86-.531.281-.313.61-.792.984-1.438l.469-.156c.958-.312 1.656-.51 2.094-.594.333.188.687.339 1.062.453.375.115.693.172.953.172s.459-.078.594-.234a.694.694 0 00.172-.547c-.02-.208-.083-.365-.188-.469C9.5 9.344 8.73 9.271 7.5 9.438c-.625-.375-1.094-.97-1.406-1.782l.031-.093a6.76 6.76 0 00.187-.97c.063-.395.063-.708 0-.937-.041-.291-.166-.484-.375-.578a.85.85 0 00-.64-.031c-.219.073-.35.203-.39.39-.084.271-.095.615-.032 1.032.042.354.135.823.281 1.406-.479 1.167-.916 2.083-1.312 2.75C2.74 11.208 2.125 11.76 2 12.281c-.02.188.047.36.203.516.156.156.36.224.61.203l-.122.003zm2.996-6.034c-.083-.23-.125-.531-.125-.906s.021-.563.063-.563v-.031c.125 0 .193.208.203.625.01.416-.036.708-.14.875zm-.937 3.5c.27-.5.562-1.167.875-2 .292.541.656.979 1.094 1.312-.375.063-.896.23-1.563.5l-.406.188zm4.687-.156c-.083.02-.208.01-.375-.032A3.772 3.772 0 018.187 10c.375-.042.688-.042.938 0 .187.042.318.089.39.14.074.053.079.1.016.141-.02.021-.052.032-.094.032zm-6.775 2.13c.098-.297.432-.693.994-1.193l.094-.094a5.837 5.837 0 01-.594.813c-.125.166-.24.291-.344.375-.104.083-.156.114-.156.094l.006.006z" />
        </g>
    </symbol>
</svg>
<div class="container-fluid px-0 propeller-account-wrapper propeller-favorites-wrapper">
    <script type="application/ld+json">
        {
            "@context": "http://schema.org",
            "@type": "BreadcrumbList",
            "itemListElement": [
                { 
                    "@type": "ListItem",
                    "position": 1,
                    "item": {
                        "@type": "Thing",
                        "@id": "<?php echo esc_url(home_url()); ?>" , 
                        "name": "<?php echo esc_attr(__("Home","propeller-ecommerce-v2")); ?>"
                    }
                },
                {
                    "@type": "ListItem",
                    "position": 2,
                    "item": {
                        "@type": "Thing",
                        "@id": "<?php echo esc_url($this->buildUrl('',PageController::get_slug(PageType::MY_ACCOUNT_PAGE))); ?>" , 
                        "name": "<?php echo esc_attr(__("My account", "propeller-ecommerce-v2")); ?>"
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
                    $this->buildUrl('', PageController::get_slug(PageType::INVOICES_PAGE)),
                    __('My invoices', 'propeller-ecommerce-v2')
                ]
            ];

            apply_filters('propel_breadcrumbs', $breadcrumb_paths);
            ?>
        </div>
    </div>
    <duv class="row">
        <?php echo esc_html( apply_filters('propel_my_account_title', __('My account', 'propeller-ecommerce-v2')) ); ?>
    </duv>
    <div class="row">
        <div class="col-12 col-lg-3">
            <?php echo esc_html( apply_filters('propel_my_account_menu', $this) ); ?>
        </div>
        <div class="col-12 col-lg-9">
            <div class="propeller-account-table propeller-invoices-table">
                <h4><?php echo esc_html( __('My invoices', 'propeller-ecommerce-v2') ); ?></h4>

                <?php if (count($invoices)) { ?>
                    <div class="order-headers d-none d-md-flex">
                        <div class="row w-100 align-items-center">
                            <div class="col-md-3 code">
                                <?php echo esc_html( __('Order number', 'propeller-ecommerce-v2') ); ?>
                            </div>
                            <div class="col-md-3 date">
                                <?php echo esc_html( __('Date', 'propeller-ecommerce-v2') ); ?>
                            </div>
                            <div class="col-md-4 total">
                                <?php echo esc_html( __('Total', 'propeller-ecommerce-v2') ); ?>
                            </div>
                            <div class="col-md-2 download">
                                <?php echo esc_html( __('Download', 'propeller-ecommerce-v2') ); ?>
                            </div>
                        </div>
                    </div>
                    <div class="order-product-item">
                        <?php
                        foreach ($invoices as $invoice)
                            apply_filters('propel_my_account_invoice_item', $invoice, $this);
                        ?>
                    </div>
                <?php } else { ?>
                    <div class="row g-0 align-items-start">
                        <div class="col-md">
                            <?php echo esc_html( __('You don\'t have any invoices', 'propeller-ecommerce-v2') ); ?>
                        </div>
                    </div>
                <?php } ?>
            </div>

        </div>

    </div>

</div>