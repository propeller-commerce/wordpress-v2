<?php
if (! defined('ABSPATH')) exit;

use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Enum\OrderStatus;
use Propeller\Includes\Enum\PageType;

$request_purchase = UserController::is_propeller_logged_in() && UserController::user()->is_purchaser() && $cart->purchaseAuthorizationRequired && UserController::user()->get_authorization_limit() < $cart->total->totalNet;
?>
<div class="container-fluid px-0">
    <div class="row align-items-start justify-content-between">
        <div class="col-12 col-md-4">
            <a href="<?php echo esc_url(home_url()); ?>" class="btn-continue">
                <?php echo esc_html(__('Continue shopping', 'propeller-ecommerce-v2')); ?>
            </a>
        </div>
        <div class="col-12 col-md-8 d-flex justify-content-end text-sm-end">
            <div class="row">
                <?php if ($cart->total->totalNet >= 0) { ?>
                    <?php
                    if (UserController::is_propeller_logged_in() && SessionController::has('PROPELLER_OCI_URL')) {

                        if (SessionController::has('is_cxml') && SessionController::get('is_cxml') && SessionController::has('sid'))
                            echo esc_html($this->convertCartToCXML(
                                $cart,
                                SessionController::get('PROPELLER_OCI_URL'),
                                __('Send via punchout', 'propeller-ecommerce-v2'),
                                'WAMBO_UNIT_OF_MEASURE_CODE',
                                [
                                    'domain' => 'UNSPSC',
                                    'code' => 'CXML_CLASSIFICATION_CODE',
                                    'value' => 'CXML_CLASSIFICATION_VALUE'
                                ]
                            ));
                        else
                            echo esc_html($this::convertCartToOCI(
                                $cart,
                                SessionController::get('PROPELLER_OCI_URL'),
                                __('Send via punchout', 'propeller-ecommerce-v2')
                            ));
                    } else { ?>
                        <?php if (UserController::is_propeller_logged_in()) { ?>
                            <div class="col-sm-auto d-flex justify-content-end justify-content-sm-start">
                                <a href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::CHECKOUT_SUMMARY_PAGE))); ?>" class="btn-checkout btn-outline btn-checkout-ajax" data-status="<?php echo esc_html(OrderStatus::ORDER_STATUS_REQUEST); ?>">
                                    <?php echo esc_html(__('Request a quote', 'propeller-ecommerce-v2')); ?>
                                </a>
                            </div>
                        <?php } ?>
                        <div class="col d-flex justify-content-end justify-content-sm-start">
                            <?php if (!UserController::is_propeller_logged_in() && PROPELLER_ANONYMOUS_ORDERS && !PROPELLER_WP_CLOSED_PORTAL && !PROPELLER_WP_SEMICLOSED_PORTAL) { ?>
                                <?php
                                SessionController::set('login_referrer', $this->buildUrl('', '/' . PageController::get_slug(PageType::CHECKOUT_PAGE)));
                                SessionController::set('register_referrer', $this->buildUrl('', '/' . PageController::get_slug(PageType::CHECKOUT_PAGE)));
                                ?>
                                <a href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::LOGIN_PAGE))); ?>" class="btn-checkout">
                                <?php } else { ?>
                                    <?php
                                    $next_step = $this->buildUrl('', PageController::get_slug(PageType::CHECKOUT_PAGE));

                                    $purchase_authorization_class = '';

                                    if ($request_purchase) {
                                        $purchase_authorization_class = 'btn-purchase-request-ajax';
                                        $next_step = '#';
                                    }

                                    ?>
                                    <a href="<?php echo esc_url($next_step); ?>" class="btn-checkout btn-checkout-ajax <?php echo esc_attr($purchase_authorization_class); ?>" data-status="<?php echo esc_attr(OrderStatus::ORDER_STATUS_NEW); ?>" data-cart="<?php echo esc_attr($cart->cartId); ?>">
                                    <?php } ?>

                                    <?php
                                    if ($request_purchase)
                                        echo esc_html(__('Send for authorization', 'propeller-ecommerce-v2'));
                                    else
                                        echo esc_html(__('Continue to checkout', 'propeller-ecommerce-v2'));
                                    ?>
                                    </a>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>

        </div>
    </div>
</div>