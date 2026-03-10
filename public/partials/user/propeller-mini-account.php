<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;

?>
<svg style="display:none">
    <symbol viewBox="0 0 28 32" id="shape-header-account">
        <title><?php echo esc_html(__('Account', 'propeller-ecommerce-v2')); ?></title>
        <path d="M14 16a8 8 0 0 0 8-8 8 8 0 0 0-8-8 8 8 0 0 0-8 8 8 8 0 0 0 8 8zm0-2a6.01 6.01 0 0 1-6-6c0-3.306 2.694-6 6-6s6 2.694 6 6-2.694 6-6 6zm11 18a3 3 0 0 0 3-3v-2.6c0-4.637-3.763-8.4-8.4-8.4-1.794 0-2.656 1-5.6 1-2.944 0-3.8-1-5.6-1A8.402 8.402 0 0 0 0 26.4V29a3 3 0 0 0 3 3h22zm0-2H3c-.55 0-1-.45-1-1v-2.6C2 22.869 4.869 20 8.4 20c1.225 0 2.444 1 5.6 1 3.15 0 4.375-1 5.6-1 3.531 0 6.4 2.869 6.4 6.4V29c0 .55-.45 1-1 1z" />
    </symbol>
    <symbol viewBox="0 0 20 18" id="shape-logout">
        <title><?php echo esc_html(__('Logout', 'propeller-ecommerce-v2')); ?></title>
        <path d="M7.503 17.036a.95.95 0 0 1-.938.964H3.75C1.68 18 0 16.273 0 14.143V3.857C0 1.727 1.68 0 3.751 0h2.814a.95.95 0 0 1 .938.964.95.95 0 0 1-.938.965H3.75c-1.031 0-1.875.867-1.875 1.928v10.286c0 1.06.844 1.928 1.875 1.928h2.814c.52 0 .938.43.938.965zm12.25-8.695c.34.37.34.948-.071 1.32l-4.967 5.464a.921.921 0 0 1-.683.304c-.195 0-.425-.089-.644-.263a.982.982 0 0 1-.04-1.362l3.514-3.84H7.155c-.483 0-.903-.434-.903-.964s.418-.964.903-.964h9.742L13.42 4.198a.982.982 0 0 1 .04-1.363.918.918 0 0 1 1.326.042l4.966 5.464z" />
    </symbol>
</svg>

<div class="propeller-mini-header-buttons propeller-mini-account dropdown dropdown-menu-end" id="propel_mini_account">
    <a class="btn-header-account d-flex d-md-none" href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::MY_ACCOUNT_MOBILE_PAGE))); ?>">
        <span class="account-icon">
            <svg class="icon icon-account">
                <use class="header-shape-account" xlink:href="#shape-header-account"></use>
            </svg>
        </span>
    </a>
    <a class="btn-header-account d-none d-md-flex " href="#" id="header-button-account" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="account-icon">
            <svg class="icon icon-account">
                <use class="header-shape-account" xlink:href="#shape-header-account"></use>
            </svg>
        </span>
        <span class="account-label d-none d-md-flex">
            <span class="account-title">
                <?php if (UserController::is_propeller_logged_in())
                    echo esc_html(__('Welcome', 'propeller-ecommerce-v2'));
                else
                    echo esc_html(__('Account', 'propeller-ecommerce-v2'));
                ?>
            </span>
            <?php if (UserController::is_propeller_logged_in()) { ?>
                <span class="account-user"><?php echo esc_html(SessionController::get(PROPELLER_USER_DATA)->firstName); ?></span>
            <?php } else { ?>
                <span class="account-user"><?php echo esc_html(__('Log in', 'propeller-ecommerce-v2')); ?></span>
            <?php } ?>
        </span>

    </a>

    <div class="dropdown-menu dropdown-account" aria-labelledby="header-button-account" id="header-dropdown-account">
        <div class="dropdown-wrapper">
            <?php if (UserController::is_propeller_logged_in()) { ?>
                <div class="title d-flex align-items-center justify-content-between">
                    <span><?php echo esc_html(__('My account', 'propeller-ecommerce-v2')); ?></span>
                    <button type="button" class="close">&times;</button>
                </div>
                <div class="account-links">
                    <?php require $this->load_template('partials', '/user/propeller-account-menu-items.php'); ?>
                </div>

            <?php } else { ?>
                <div class="title d-flex align-items-center justify-content-between">
                    <span><?php echo esc_html(__('Log in', 'propeller-ecommerce-v2')); ?></span>
                    <button type="button" class="close">&times;</button>
                </div>
            <?php
                require $this->load_template('partials', '/user/propeller-header-login-form.php');
            } ?>
        </div>

        <div class="dropdown-footer">
            <?php if (UserController::is_propeller_logged_in()) { ?>

                <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="btn-logout">
                    <span><?php echo esc_html(__('Log out', 'propeller-ecommerce-v2')); ?></span>
                    <span class="account-logout">
                        <svg class="icon icon-logout">
                            <use class="header-shape-logout" xlink:href="#shape-logout"></use>
                        </svg>
                    </span>
                </a>
            <?php } else { ?>
                <a href="<?php echo esc_url($this->buildUrl('', PageController::get_slug(PageType::REGISTER_PAGE))); ?>" class="btn-logout">
                    <span><?php echo esc_html(__('Register', 'propeller-ecommerce-v2')); ?></span>
                    <span class="account-logout">
                        <svg class="icon icon-logout">
                            <use class="header-shape-logout" xlink:href="#shape-logout"></use>
                        </svg>
                    </span>
                </a>
            <?php } ?>
        </div>
    </div>
</div>