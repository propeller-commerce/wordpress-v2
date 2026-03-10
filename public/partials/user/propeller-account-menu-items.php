<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Controller\UserController;
use Propeller\Includes\Enum\PageType;
?>
<ul>
    <li>
        <a href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::MY_ACCOUNT_MOBILE_PAGE), PageController::get_slug(PageType::MY_ACCOUNT_PAGE))); ?>"><?php echo esc_html(__('My account details', 'propeller-ecommerce-v2')); ?></a>
    </li>
    <li>
        <a href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::MY_ACCOUNT_MOBILE_PAGE), PageController::get_slug(PageType::ADDRESSES_PAGE))); ?>"><?php echo esc_html(__('My addresses', 'propeller-ecommerce-v2')); ?></a>
    </li>
    <li>
        <a href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::MY_ACCOUNT_MOBILE_PAGE), PageController::get_slug(PageType::ORDERS_PAGE))); ?>"><?php echo esc_html(__('My orders', 'propeller-ecommerce-v2')); ?></a>
    </li>
    <li>
        <a href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::MY_ACCOUNT_MOBILE_PAGE), PageController::get_slug(PageType::QUOTATIONS_PAGE))); ?>"><?php echo esc_html(__('My quotes', 'propeller-ecommerce-v2')); ?></a>
    </li>
    <li>
        <a href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::MY_ACCOUNT_MOBILE_PAGE), PageController::get_slug(PageType::INVOICES_PAGE))); ?>"><?php echo esc_html(__('My invoices', 'propeller-ecommerce-v2')); ?></a>
    </li>
    <li>
        <a href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::MY_ACCOUNT_MOBILE_PAGE), PageController::get_slug(PageType::FAVORITES_PAGE))); ?>"><?php echo esc_html(__('My favorites', 'propeller-ecommerce-v2')); ?></a>
    </li>
    <li>
        <a href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::MY_ACCOUNT_MOBILE_PAGE), PageController::get_slug(PageType::PRODUCT_REQUEST_PAGE))); ?>"><?php echo esc_html(__('Price requests', 'propeller-ecommerce-v2')); ?></a>
    </li>
    <?php if (UserController::user()->is_authorization_manager()) { ?>
        <li>
            <a href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::MY_ACCOUNT_MOBILE_PAGE), PageController::get_slug(PageType::PURCHASE_AUTHORIZATIONS_PAGE))); ?>"><?php echo esc_html(__('Authorization settings', 'propeller-ecommerce-v2')); ?></a>
        </li>
    <?php } ?>
    <?php if (UserController::user()->is_authorization_manager()) { ?>
        <li>
            <a href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::MY_ACCOUNT_MOBILE_PAGE), PageController::get_slug(PageType::PURCHASE_AUTHORIZATION_REQUESTS_PAGE))); ?>"><?php echo esc_html(__('Authorization requests', 'propeller-ecommerce-v2')); ?></a>
        </li>
    <?php } ?>
    <?php /*
    <li>
        <a href="<?php echo esc_url($this->buildUrl(PageController::get_slug(PageType::MY_ACCOUNT_MOBILE_PAGE), PageController::get_slug(PageType::ORDERLIST_PAGE))); ?>"><?php echo esc_html( __('My orderlist', 'propeller-ecommerce-v2') ); ?></a>
    </li>
     */ ?>
</ul>