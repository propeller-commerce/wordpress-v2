<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if (PROPELLER_SHOW_ACTIONCODE) { ?>
    <?php $tooltip_content = __('<strong>Action code</strong> <div class="content">Have you received a promotional code? Enter this code and click Add.</div><div class="content">Only 1 promotional code can be used per order.</div><div class="content"><a href="/">View the promotion conditions</a></div>', 'propeller-ecommerce-v2'); ?>
    <svg style="display:none;">
        <symbol viewBox="0 0 22 22" id="shape-info">
            <title>Info</title>
            <path d="M11 1.469c5.097 0 9.281 4.128 9.281 9.281A9.279 9.279 0 0 1 11 20.031a9.279 9.279 0 0 1-9.281-9.281A9.28 9.28 0 0 1 11 1.469zm0-1.375C5.115.094.344 4.867.344 10.75c0 5.887 4.771 10.656 10.656 10.656 5.885 0 10.656-4.77 10.656-10.656C21.656 4.867 16.885.094 11 .094zM9.453 14.875a.516.516 0 0 0-.516.516v.343c0 .285.231.516.516.516h3.094c.285 0 .515-.23.515-.516v-.343a.516.516 0 0 0-.515-.516h-.516V8.859a.516.516 0 0 0-.515-.515H9.453a.516.516 0 0 0-.516.515v.344c0 .285.231.516.516.516h.516v5.156h-.516zM11 4.563a1.375 1.375 0 1 0 0 2.75 1.375 1.375 0 0 0 0-2.75z" />
        </symbol>
        <symbol viewBox="0 0 16 12" id="shape-checkmark">
            <title>Success</title>
            <path d="m6.566 11.764 9.2-9.253a.808.808 0 0 0 0-1.137L14.634.236a.797.797 0 0 0-1.131 0L6 7.782 2.497 4.259a.797.797 0 0 0-1.131 0L.234 5.397a.808.808 0 0 0 0 1.137l5.2 5.23a.797.797 0 0 0 1.132 0z" fill="#54A023" />
        </symbol>
    </svg>
    <div class="col-12 col-md-6 col-lg-4 ms-lg-auto">
        <div class="shopping-cart-voucher">
            <div class="row g-0 align-items-baseline">

                <div class="col-12 d-flex align-items-center justify-content-between">
                    <div class="sc-voucher-title"><?php echo esc_html(__('Action code', 'propeller-ecommerce-v2')); ?></div>
                    <div class="actioncode-tooltip" data-bs-toggle="tooltip" data-html="true" title="<?php echo esc_attr($tooltip_content); ?>">
                        <svg class="d-flex icon icon-info" aria-hidden="true">
                            <use xlink:href="#shape-info"></use>
                        </svg>
                    </div>
                </div>
                <div class="col-12" id="actionCode">
                    <div class="vouchercode add-product" id="vouchercode">
                        <div class="add-to-basket">
                            <?php if (empty($cart->actionCode) && empty($cart->vouchers)) { ?>
                                <form name="add-voucher" class="basket-voucher-form" method="post">
                                    <input type="hidden" name="action" value="cart_add_action_code">
                                    <input type="text" name="actionCode" class="form-control required" value="" placeholder="<?php echo esc_attr(__('Your action code', 'propeller-ecommerce-v2')); ?>">
                                    <button type="submit" class="btn-voucher"><?php echo esc_html(__('Add action code', 'propeller-ecommerce-v2')); ?></button>
                                </form>
                            <?php } else { ?>
                                <div class="action-success-info">
                                    <span><?php if (!empty($cart->vouchers))
                                                echo esc_html(__('Your voucher code is added!', 'propeller-ecommerce-v2'));
                                            else if (!empty($cart->actionCode))
                                                echo esc_html(__('Your action code is added!', 'propeller-ecommerce-v2')); ?></span>
                                    <svg class="d-flex icon icon-checkmark" aria-hidden="true">
                                        <use xlink:href="#shape-checkmark"></use>
                                    </svg>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
<?php } ?>
