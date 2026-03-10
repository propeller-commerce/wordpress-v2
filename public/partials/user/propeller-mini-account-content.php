<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;

?>
<?php /* <div class="dropdown-menu dropdown-menu-right dropdown-account" aria-labelledby="header-button-account" id="header-dropdown-account"> */ ?>
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
<?php /* </div> */ ?>
