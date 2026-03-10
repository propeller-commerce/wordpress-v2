<?php

if ( ! defined( 'ABSPATH' ) ) exit;

use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Enum\PageType;

?><div class="row login-wrapper">
    <?php if (UserController::is_propeller_logged_in()) { ?>
        <div class="col-12">
            <h1><?php echo esc_html(__("You are already logged in", 'propeller-ecommerce-v2')); ?></h1>
        </div>
    <?php  } else { ?>
        <div class="col-12">
            <h1><?php echo esc_html(__('Login', 'propeller-ecommerce-v2')); ?></h1>

            <?php echo do_shortcode('[login-form]'); ?>
        </div>
        <div class="col-12">
            <div class="container-fluid px-0">
                <div class="row">
                    <div class="col-12">
                        <a class="btn-checkout btn-register" href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::REGISTER_PAGE))); ?>">
                            <?php echo esc_html(__('Register', 'propeller-ecommerce-v2')); ?>
                        </a>
                    </div>
                    <?php if ($show_guest_checkout) { ?>
                        <div class="col-12">
                            <a href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::CHECKOUT_PAGE))); ?>" class="btn-checkout btn-checkout-ajax btn-guest-checkout" data-status="NEW">
                                <?php echo esc_html(__('Checkout as guest', 'propeller-ecommerce-v2')); ?>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>