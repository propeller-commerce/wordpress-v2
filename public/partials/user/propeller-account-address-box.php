<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="col-12 col-md-6 order-2 mb-4">
    <div class="address-box">
        <div class="row">
            <div class="col-12">
                <?php if ($show_title) { ?>
                    <div class="addr-title"><?php echo esc_html($title); ?></div>
                <?php } ?>
                <div class="user-addr-details">
                    <?php echo esc_html($address->company); ?><br>
                    <?php echo esc_html($obj->get_salutation($address)); ?>
                    <?php echo esc_html($address->firstName); ?> <?php echo esc_html($address->middleName); ?> <?php echo esc_html($address->lastName); ?><br>
                    <?php echo esc_html($address->street); ?> <?php echo esc_html($address->number); ?> <?php echo esc_html($address->numberExtension); ?><br>
                    <?php echo esc_html($address->postalCode); ?> <?php echo esc_html($address->city); ?><br>
                    <?php
                    $code = $address->country;

                    $countries = propel_get_countries();
                    echo esc_html(!isset($countries[$code]) ? $code : $countries[$code]);
                    ?>
                </div>
            </div>
        </div>
        <?php if (PROPELLER_EDIT_ADDRESSES) { ?>
            <div class="row address-links">
                <div class="col-12">
                    <?php
                    if ($show_modify)
                        apply_filters('propel_address_modify', $address);

                    if ($show_delete)
                        apply_filters('propel_address_delete', $address);

                    if ($show_set_default && $address->isDefault != 'Y')
                        apply_filters('propel_address_set_default', $address);
                    ?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>