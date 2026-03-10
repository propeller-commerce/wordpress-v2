<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;
use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Enum\AddressType;

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
                <div class="account-details">
                    <div class="row">

                        <?php apply_filters('propel_my_account_user_details_title', __('Your details', 'propeller-ecommerce-v2')); ?>

                    </div>
                    <div class="row">

                        <?php apply_filters('propel_my_account_user_details', $this->get_user(), $this); ?>
                        <?php if ($this->get_user()->__typename !== 'Customer')
                            apply_filters('propel_my_account_company_details', $this->get_user(), $this); ?>


                    </div>
                </div>
                <div class="default-addresses">
                    <div class="row">

                        <?php apply_filters('propel_my_account_addresses_title', __('My addresses', 'propeller-ecommerce-v2')); ?>

                    </div>
                    <div class="row">

                        <?php apply_filters('propel_address_box', $this->get_default_address(AddressType::INVOICE), $this, __('Default billing address', 'propeller-ecommerce-v2'), true, false); ?>

                        <?php apply_filters('propel_address_box', $this->get_default_address(AddressType::DELIVERY), $this, __('Default delivery address', 'propeller-ecommerce-v2'), true, false); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

</div>