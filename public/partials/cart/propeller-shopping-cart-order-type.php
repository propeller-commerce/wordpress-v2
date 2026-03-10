<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Enum\OrderType;
?>
<div class="col-12 col-md-6 col-lg-4 ms-lg-auto">
    <?php
    if (PROPELLER_SHOW_ORDER_TYPE) {
        if (UserController::is_propeller_logged_in() && (SessionController::get(PROPELLER_ORDER_STATUS_TYPE) != 'REQUEST')) {
    ?>
            <svg style="display:none">
                <symbol viewBox="0 0 14 14" id="shape-header-close">
                    <title><?php echo esc_html(__('Close', 'propeller-ecommerce-v2')); ?></title>
                    <path d="M13.656 12.212c.41.41.41 1.072 0 1.481a1.052 1.052 0 0 1-1.485 0L7 8.5l-5.207 5.193a1.052 1.052 0 0 1-1.485 0 1.045 1.045 0 0 1 0-1.481L5.517 7.02.307 1.788a1.045 1.045 0 0 1 0-1.481 1.052 1.052 0 0 1 1.485 0L7.001 5.54 12.208.348a1.052 1.052 0 0 1 1.485 0c.41.408.41 1.072 0 1.48L8.484 7.02l5.172 5.192z" />
                </symbol>
            </svg>
            <div class="shopping-cart-order-type">
                <div class="row g-0 align-items-baseline">
                    <div class="col-12 d-flex align-items-center justify-content-between">
                        <div class="sc-title"><?php echo esc_html(__('Type of order', 'propeller-ecommerce-v2')); ?></div>
                    </div>

                    <div class="order-type" id="orderType">
                        <form name="order-type" class="order-type-form" method="post">
                            <input type="hidden" name="action" value="cart_change_order_type">
                            <div class="row form-group">
                                <div class="col-form-fields col-12">
                                    <div class="row g-3">
                                        <?php foreach ($order_types->items as $order_type) { ?>
                                            <?php if ($order_type->hide) continue; ?>

                                            <div class="col-12 form-group ">
                                                <label class="btn-radio-checkbox -label justify-content-start ">
                                                    <input type="radio" class="-input" name="order_type" value="<?php echo esc_attr($order_type->value); ?>" <?php echo esc_attr(SessionController::get(PROPELLER_ORDER_TYPE) == $order_type->value ? 'checked' : ''); ?>> <span><?php echo esc_html($order_type->description); ?></span>
                                                </label>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="row">
                            <div class="col-12">
                                <a data-bs-target="#orderTypeModal" data-bs-toggle="modal" href="#orderTypeModal" class="order-type-modal"><?php echo esc_html(__('Find out more about different order types here', 'propeller-ecommerce-v2')); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="propeller-order-modal modal fade modal-fullscreen-sm-down" tabindex="-1" role="dialog" aria-labelledby="modal-title" id="orderTypeModal">
                <div class="modal-dialog modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header propel-modal-header">
                            <div id="propel_modal_title" class="modal-title">
                                <span><?php echo esc_html(__('Different order types', 'propeller-ecommerce-v2')); ?></span>
                            </div>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="<?php echo esc_html(__('Close', 'propeller-ecommerce-v2')); ?>">
                                <span aria-hidden="true">
                                    <svg class="icon icon-close">
                                        <use class="header-shape-close" xlink:href="#shape-header-close"></use>
                                    </svg>
                                </span>
                            </button>
                        </div>
                        <div class="modal-body propel-modal-body" id="propel_modal_body">
                            <div class="order-title">
                                <?php echo esc_html(__('Regular order', 'propeller-ecommerce-v2')); ?>
                            </div>
                            <div class="order-description">
                                <?php echo esc_html(__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas sodales congue ipsum id ultrices. Quisque eu nisl sapien. In auctor pulvinar lorem, ac posuere mauris sagittis at. Integer maximus elementum pulvinar. Donec commodo quam id tellus fermentum.', 'propeller-ecommerce-v2')); ?>
                            </div>
                            <div class="order-title">
                                <?php echo esc_html(__('Dropshipment', 'propeller-ecommerce-v2')); ?>
                            </div>
                            <div class="order-description">
                                <?php echo esc_html(__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas sodales congue ipsum id ultrices. Quisque eu nisl sapien. In auctor pulvinar lorem, ac posuere mauris sagittis at. Integer maximus elementum pulvinar. Donec commodo quam id tellus fermentum.', 'propeller-ecommerce-v2')); ?>
                            </div>
                            <div class="order-title">
                                <?php echo esc_html(__('Pick up', 'propeller-ecommerce-v2')); ?>
                            </div>
                            <div class="order-description">
                                <?php echo esc_html(__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas sodales congue ipsum id ultrices. Quisque eu nisl sapien. In auctor pulvinar lorem, ac posuere mauris sagittis at. Integer maximus elementum pulvinar. Donec commodo quam id tellus fermentum.', 'propeller-ecommerce-v2')); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    <?php }
    } ?>
</div>