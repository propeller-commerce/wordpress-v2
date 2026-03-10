<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Enum\PageType;

global $wp;
?>
<svg style="display:none;">
    <symbol viewBox="0 0 7 12" id="shape-arrow-right">
        <title>Arrow right</title>
        <path d="M.275 11.776a.927.927 0 0 0 1.243-.03L6.76 6.562A.784.784 0 0 0 7 6a.787.787 0 0 0-.24-.562L1.518.256A.927.927 0 0 0 .275.224a.777.777 0 0 0-.034 1.155L4.944 6 .241 10.62a.778.778 0 0 0 .034 1.157z" />
    </symbol>
</svg>
<div class="propeller-account-details propeller-mobile-account">
    <div class="row propeller-account-header">
        <div class="col-12">
            <div class="salutation">
                <?php echo esc_html(__('Welcome', 'propeller-ecommerce-v2')); ?> <span class="fullname"><?php echo esc_html(SessionController::get(PROPELLER_USER_DATA)->firstName); ?> <?php echo esc_html(SessionController::get(PROPELLER_USER_DATA)->lastName); ?></span>
            </div>
        </div>
    </div>
    <div class="propeller-account-menu">
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse " id="propeller-account-menu">
                <ul class="navbar-nav d-block w-100">
                    <li>
                        <a href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::MY_ACCOUNT_PAGE))); ?>" class="<?php if ($wp->request == PageController::get_slug(PageType::MY_ACCOUNT_PAGE)) echo 'active'; ?>">
                            <span><?php echo esc_html(__('My account details', 'propeller-ecommerce-v2')); ?></span>
                            <svg class="icon icon-svg" aria-hidden="true">
                                <use xlink:href="#shape-arrow-right"></use>
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::ADDRESSES_PAGE))); ?>" class="<?php if ($wp->request == PageController::get_slug(PageType::ADDRESSES_PAGE)) echo 'active'; ?>">
                            <span><?php echo esc_html(__('My addresses', 'propeller-ecommerce-v2')); ?></span>
                            <svg class="icon icon-svg" aria-hidden="true">
                                <use xlink:href="#shape-arrow-right"></use>
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::ORDERS_PAGE))); ?>" class="<?php if ($wp->request == PageController::get_slug(PageType::ORDERS_PAGE)) echo 'active'; ?>">
                            <span><?php echo esc_html(__('My orders', 'propeller-ecommerce-v2')); ?></span>
                            <svg class="icon icon-svg" aria-hidden="true">
                                <use xlink:href="#shape-arrow-right"></use>
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::QUOTATIONS_PAGE))); ?>" class="<?php if ($wp->request == PageController::get_slug(PageType::QUOTATIONS_PAGE)) echo 'active'; ?>">
                            <span><?php echo esc_html(__('My quotes', 'propeller-ecommerce-v2')); ?></span>
                            <svg class="icon icon-svg" aria-hidden="true">
                                <use xlink:href="#shape-arrow-right"></use>
                            </svg>
                        </a>

                    </li>
                    <li>
                        <a href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::INVOICES_PAGE))); ?>" class="<?php if ($wp->request == PageController::get_slug(PageType::INVOICES_PAGE)) echo 'active'; ?>">
                            <span><?php echo esc_html(__('My invoices', 'propeller-ecommerce-v2')); ?></span>
                            <svg class="icon icon-svg" aria-hidden="true">
                                <use xlink:href="#shape-arrow-right"></use>
                            </svg>
                        </a>

                    </li>
                    <li>
                        <a href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::FAVORITES_PAGE))); ?>" class="<?php if ($wp->request == PageController::get_slug(PageType::FAVORITES_PAGE)) echo 'active'; ?>">
                            <span><?php echo esc_html(__('Favorites', 'propeller-ecommerce-v2')); ?></span>
                            <svg class="icon icon-svg" aria-hidden="true">
                                <use xlink:href="#shape-arrow-right"></use>
                            </svg>
                        </a>

                    </li>
                    <?php
                    $current_user = UserController::user();
                    if ($current_user && $current_user->is_authorization_manager()) { ?>
                        <li>
                            <a href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::MY_ACCOUNT_MOBILE_PAGE), PageController::get_slug(PageType::PURCHASE_AUTHORIZATIONS_PAGE))); ?>" class="<?php if ($wp->request == PageController::get_slug(PageType::FAVORITES_PAGE)) echo 'active'; ?>">
                                <span><?php echo esc_html(__('Authorization settings', 'propeller-ecommerce-v2')); ?></span>
                                <svg class="icon icon-svg" aria-hidden="true">
                                    <use xlink:href="#shape-arrow-right"></use>
                                </svg>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::MY_ACCOUNT_MOBILE_PAGE), PageController::get_slug(PageType::PURCHASE_AUTHORIZATION_REQUESTS_PAGE))); ?>" class="<?php if ($wp->request == PageController::get_slug(PageType::FAVORITES_PAGE)) echo 'active'; ?>">
                                <span><?php echo esc_html(__('Authorization requests', 'propeller-ecommerce-v2')); ?></span>
                                <svg class="icon icon-svg" aria-hidden="true">
                                    <use xlink:href="#shape-arrow-right"></use>
                                </svg>
                            </a>
                        </li>
                    <?php } ?>
                    <?php /*
                    <li>
                        <a href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::FAVORITES_PAGE))); ?>" class="<?php if ($wp->request == PageController::get_slug(PageType::FAVORITES_PAGE)) echo 'active';?>">
                            <span><?php echo esc_html( __('My favorites', 'propeller-ecommerce-v2') ); ?></span>
                            <svg class="icon icon-svg" aria-hidden="true">
                                <use xlink:href="#shape-arrow-right"></use>
                            </svg>
                        </a>
                        
                    </li>
                    <li>
                        <a href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::ORDERLIST_PAGE))); ?>" class="<?php if ($wp->request == PageController::get_slug(PageType::ORDERLIST_PAGE)) echo 'active';?>">
                            <span><?php echo esc_html( __('My orderlist', 'propeller-ecommerce-v2') ); ?></span>
                            <svg class="icon icon-svg" aria-hidden="true">
                                <use xlink:href="#shape-arrow-right"></use>
                            </svg>
                        </a>
                        
                    </li>
                    <li>
                        <a href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::INVOICES_PAGE))); ?>" class="<?php if ($wp->request == PageController::get_slug(PageType::INVOICES_PAGE)) echo 'active';?>">
                            <span><?php echo esc_html( __('My invoices', 'propeller-ecommerce-v2') ); ?></span>
                            <svg class="icon icon-svg" aria-hidden="true">
                                <use xlink:href="#shape-arrow-right"></use>
                            </svg>
                        </a>
                        
                    </li> */ ?>
                    <li>
                        <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>">
                            <span><?php echo esc_html(__('Log out', 'propeller-ecommerce-v2')); ?></span>
                            <svg class="icon icon-svg" aria-hidden="true">
                                <use xlink:href="#shape-arrow-right"></use>
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>