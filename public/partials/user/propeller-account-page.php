<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;
?>
<svg style="display:none;">
    <symbol viewBox="0 0 7 12" id="shape-arrow-right">
        <title>Arrow right</title>
        <path d="M.275 11.776a.927.927 0 0 0 1.243-.03L6.76 6.562A.784.784 0 0 0 7 6a.787.787 0 0 0-.24-.562L1.518.256A.927.927 0 0 0 .275.224a.777.777 0 0 0-.034 1.155L4.944 6 .241 10.62a.778.778 0 0 0 .034 1.157z" />
    </symbol>
    <symbol viewBox="0 0 20 18" id="shape-logout">
        <title>Logout</title>
        <path d="M7.503 17.036a.95.95 0 0 1-.938.964H3.75C1.68 18 0 16.273 0 14.143V3.857C0 1.727 1.68 0 3.751 0h2.814a.95.95 0 0 1 .938.964.95.95 0 0 1-.938.965H3.75c-1.031 0-1.875.867-1.875 1.928v10.286c0 1.06.844 1.928 1.875 1.928h2.814c.52 0 .938.43.938.965zm12.25-8.695c.34.37.34.948-.071 1.32l-4.967 5.464a.921.921 0 0 1-.683.304c-.195 0-.425-.089-.644-.263a.982.982 0 0 1-.04-1.362l3.514-3.84H7.155c-.483 0-.903-.434-.903-.964s.418-.964.903-.964h9.742L13.42 4.198a.982.982 0 0 1 .04-1.363.918.918 0 0 1 1.326.042l4.966 5.464z" />
    </symbol>
</svg>

<div class="propeller-account-details">

    <?php if (UserController::is_propeller_logged_in()) { ?>
        <div class="row">
            <div class="col-12">
                <h1><?php echo esc_html( __('My account', 'propeller-ecommerce-v2') ); ?></h1>
            </div>
        </div>
        <div class="row propeller-account-header">
            <div class="col-12">
                <div class="salutation">
                    <?php echo esc_html( __('Welcome', 'propeller-ecommerce-v2') ); ?> <span class="fullname"><?php echo esc_html( SessionController::get(PROPELLER_USER_DATA)->firstName ); ?> <?php echo esc_html( SessionController::get(PROPELLER_USER_DATA)->lastName ); ?></span>
                </div>
            </div>
        </div>
        <div class="propeller-account-menu">
            <nav class="navbar" id="propeller-account-menu">

                <ul class="navbar-nav d-block w-100">
                    <li>
                        <a href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::MY_ACCOUNT_PAGE))); ?>">
                            <span><?php echo esc_html( __('My account details', 'propeller-ecommerce-v2') ); ?></span>
                            <svg class="icon icon-svg" aria-hidden="true">
                                <use xlink:href="#shape-arrow-right"></use>
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::ADDRESSES_PAGE))); ?>">
                            <span><?php echo esc_html( __('My addresses', 'propeller-ecommerce-v2') ); ?></span>
                            <svg class="icon icon-svg" aria-hidden="true">
                                <use xlink:href="#shape-arrow-right"></use>
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::ORDERS_PAGE))); ?>">
                            <span><?php echo esc_html( __('My orders', 'propeller-ecommerce-v2') ); ?></span>
                            <svg class="icon icon-svg" aria-hidden="true">
                                <use xlink:href="#shape-arrow-right"></use>
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::QUOTATIONS_PAGE))); ?>">
                            <span><?php echo esc_html( __('My quotes', 'propeller-ecommerce-v2') ); ?></span>
                            <svg class="icon icon-svg" aria-hidden="true">
                                <use xlink:href="#shape-arrow-right"></use>
                            </svg>
                        </a>

                    </li>
                    <li>
                        <a href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::INVOICES_PAGE))); ?>">
                            <span><?php echo esc_html( __('My invoices', 'propeller-ecommerce-v2') ); ?></span>
                            <svg class="icon icon-svg" aria-hidden="true">
                                <use xlink:href="#shape-arrow-right"></use>
                            </svg>
                        </a>

                    </li>
                    <li>
                        <a href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::FAVORITES_PAGE))); ?>">
                            <span><?php echo esc_html( __('My favorites', 'propeller-ecommerce-v2') ); ?></span>
                            <svg class="icon icon-svg" aria-hidden="true">
                                <use xlink:href="#shape-arrow-right"></use>
                            </svg>
                        </a>

                    </li>
                    <li class="logout-item">
                        <a href="<?php echo esc_url( wp_logout_url(home_url()) ); ?>" class="btn-logout">
                            <span><?php echo esc_html( __('Log out', 'propeller-ecommerce-v2') ); ?></span>
                            <svg class="icon icon-svg" aria-hidden="true">
                                <use xlink:href="#shape-arrow-right"></use>
                            </svg>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php } else {
        require $this->load_template('partials', '/user/propeller-login-form.php');
    } ?>
</div>
<?php require $this->load_template('partials', '/other/propeller-toast.php'); ?>
