<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;
use Propeller\PropellerHelper;

?>

<li class="row g-0 align-items-start">
    <div class="row order-product-item">
        <div class="col-lg-11 col-11">
            <h4 class="name">
                <a href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::FAVORITES_PAGE), $list->id)); ?>">
                    <?php echo esc_html($list->name); ?>
                </a>
            </h4>
            <?php
            $dateString = $list->updatedAt;
            $date = new DateTime($dateString, new DateTimeZone('UTC'));
            $monthNumber = (int) $date->format('n');
            $months = PropellerHelper::months();
            $monthName = $months[$monthNumber];
            ?>
            <p class="modified-fav-list"><?php echo esc_html(__('Last modified:', 'propeller-ecommerce-v2')); ?> <span><?php echo esc_html($monthName) . ' ';
                                                                                                                        echo esc_html(gmdate('d, Y', strtotime($list->updatedAt))); ?></span></p>
            <span class="countproducts"><?php echo esc_html(($list->products->itemsFound + $list->clusters->itemsFound)); ?> <?php echo esc_html(__('products', 'propeller-ecommerce-v2')); ?></span>
        </div>
        <div class="col-lg-1 col-1 text-end d-flex icon-right-shevron align-middle align-items-center justify-content-end">
            <a href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::FAVORITES_PAGE), $list->id)); ?>">
                <svg class="icon icon-svg" aria-hidden="true">
                    <use xlink:href="#shape-arrow-right"></use>
                </svg>
            </a>
        </div>
    </div>
</li>