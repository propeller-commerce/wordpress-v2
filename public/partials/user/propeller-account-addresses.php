<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;
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
                },
                {
                    "@type": "ListItem",
                    "position": 3,
                    "item": {
                        "@type": "Thing",
                        "@id": "<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::ADDRESSES_PAGE))); ?>",
                        "name": "<?php echo esc_attr(__("My addresses", "propeller-ecommerce-v2")); ?>"
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
                    $this->buildUrl('', PageController::get_slug(PageType::ADDRESSES_PAGE)),
                    __('My addresses', 'propeller-ecommerce-v2')
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
            <div class="propeller-account-table">
                <div class="row address-title">

                    <?php echo esc_html(apply_filters('propel_my_account_addresses_title', __('My addresses', 'propeller-ecommerce-v2'))); ?>

                </div>
                <div class="default-addresses">
                    <div class="row">
                        <div class="col-12">
                            <h5><?php echo esc_html(__('Default addresses', 'propeller-ecommerce-v2')); ?></h5>
                        </div>
                    </div>
                    <div class="row">

                        <?php echo esc_html(apply_filters('propel_address_box', $this->get_default_address(AddressType::INVOICE), $this, __('Default billing address', 'propeller-ecommerce-v2'), true, false)); ?>

                        <?php echo esc_html(apply_filters('propel_address_box', $this->get_default_address(AddressType::DELIVERY), $this, __('Default delivery address', 'propeller-ecommerce-v2'), true, false)); ?>

                    </div>
                </div>

                <?php
                $other_invoice_addresses = $this->get_all_addresses(AddressType::INVOICE);

                if (count($other_invoice_addresses)) { ?>
                    <div class="invoice-addresses">
                        <div class="row">
                            <div class="col-12">
                                <h5><?php echo esc_html(__('Billing adresses', 'propeller-ecommerce-v2')); ?></h5>
                            </div>
                        </div>
                        <div class="row">
                            <?php
                            foreach ($other_invoice_addresses as $address)
                                apply_filters('propel_address_box', $address, $this, '', false, true, true, true);
                            ?>
                        </div>
                    </div>
                <?php }

                if (PROPELLER_EDIT_ADDRESSES)
                    apply_filters('propel_address_add', AddressType::INVOICE, __('Add billing address', 'propeller-ecommerce-v2'), $this);
                ?>

                <?php
                $other_delivery_addresses = $this->get_all_addresses(AddressType::DELIVERY);

                if (count($other_delivery_addresses)) { ?>
                    <div class="invoice-addresses">
                        <div class="row">
                            <div class="col-12">
                                <h5><?php echo esc_html(__('Delivery addresses', 'propeller-ecommerce-v2')); ?></h5>
                            </div>
                        </div>
                        <div class="row">
                            <?php
                            foreach ($other_delivery_addresses as $address)
                                apply_filters('propel_address_box', $address, $this, '', false, true, true, true);
                            ?>
                        </div>
                    </div>
                <?php }

                if (PROPELLER_EDIT_ADDRESSES)
                    apply_filters('propel_address_add', AddressType::DELIVERY, __('Add delivery address', 'propeller-ecommerce-v2'), $this);
                ?>
            </div>
        </div>
    </div>
</div>