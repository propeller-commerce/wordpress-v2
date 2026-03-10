<?php
if (! defined('ABSPATH')) exit;

use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Enum\PageType;
use Propeller\PropellerHelper;

?>
<svg style="display:none;">
    <symbol viewBox="0 0 7 12" id="shape-arrow-right">
        <title>Arrow right</title>
        <path d="M.275 11.776a.927.927 0 0 0 1.243-.03L6.76 6.562A.784.784 0 0 0 7 6a.787.787 0 0 0-.24-.562L1.518.256A.927.927 0 0 0 .275.224a.777.777 0 0 0-.034 1.155L4.944 6 .241 10.62a.778.778 0 0 0 .034 1.157z" />
    </symbol>
</svg>
<div class="mb-4 propeller-account-wrapper">
    <div class="address-box quotations-box">
        <div class="quotations-box-header">
            <h3><?php echo esc_html(__('My favorites', 'propeller-ecommerce-v2')); ?></h3>
            <a href="<?php echo esc_url($obj->buildUrl('', PageController::get_slug(PageType::FAVORITES_PAGE))); ?>" class="view-all-link"><?php echo esc_html(__('View all', 'propeller-ecommerce-v2')); ?></a>
        </div>
        <div class="quotations-box-content ">
            <?php if (!empty($this->data->items)) : ?>
                <?php foreach ($this->data->items as $list) : ?>
                    <a href="<?php echo esc_url($obj->buildUrl(PageController::get_slug(PageType::FAVORITES_PAGE), $list->id)); ?>" class="quotation-item favorites-item">
                        <div class="favorites-box-content">
                            <div class="quotation-info">
                                <span class="quotation-number"><?php echo esc_html($list->name); ?></span>
                            </div>
                            <div class="quotation-price">
                                <?php
                                $dateString = $list->updatedAt;
                                $date = new DateTime($dateString, new DateTimeZone('UTC'));
                                $monthNumber = (int) $date->format('n');
                                $months = PropellerHelper::months();
                                $monthName = $months[$monthNumber];
                                ?>
                                <div class="modified-fav-list"><?php echo esc_html(__('Last modified:', 'propeller-ecommerce-v2')); ?> <span><?php echo esc_html($monthName) . ' ';
                                                                                                                                                echo esc_html(gmdate('d, Y', strtotime($list->updatedAt))); ?></span></div>
                                <div class="countproducts"><?php echo esc_html(($list->products->itemsFound + $list->clusters->itemsFound)); ?> <?php echo esc_html(__('products', 'propeller-ecommerce-v2')); ?></div>

                            </div>
                        </div>
                        <svg class="icon icon-svg" aria-hidden="true">
                            <use xlink:href="#shape-arrow-right"></use>
                        </svg>
                    </a>

                <?php endforeach; ?>
            <?php else : ?>
                <p class="no-quotations"><?php echo esc_html(__('You have not created any favorite lists.', 'propeller-ecommerce-v2')); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>