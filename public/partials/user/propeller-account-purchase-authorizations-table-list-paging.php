<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$prev = $data->page - 1;
$prev_disabled = false;

if ($prev < 1) {
    $prev = 1;
    $prev_disabled = 'disabled';
}

$next = $data->page + 1;
$next_disabled = false;

if ($data->page == $data->pages)
    $next_disabled = 'disabled';

if ($data->pages > 1) { ?>
    <div class="col-12">
        <div class="row propeller-account-pagination propeller-purchase-authorizations-pagination" data-action="purchase_authorizations" data-min="1" data-max="<?php echo esc_html((int) $data->pages); ?>" data-current="<?php echo esc_html((int) $data->page); ?>">
            <div class="col-12 d-flex align-items-center justify-content-center ">
                <a class="previous page-item <?php echo esc_attr($prev_disabled); ?>" data-page="<?php echo esc_attr($prev); ?>" <?php echo esc_attr($prev_disabled); ?>>
                    <span class="icon">
                        <svg class="icon icon-svg" aria-hidden="true">
                            <use xlink:href="#shape-arrow-left"></use>
                        </svg>
                    </span>
                </a>

                <span class="page-totals"><?php echo esc_html(__('page', 'propeller-ecommerce-v2')); ?> <?php echo esc_html((int) $data->page); ?> <?php echo esc_html(__('from', 'propeller-ecommerce-v2')); ?> <?php echo esc_html((int) $data->pages); ?></span>

                <a class="next page-item <?php echo esc_attr($next_disabled); ?>" data-page="<?php echo esc_attr($next); ?>" <?php echo esc_attr($next_disabled); ?>>
                    <span class="icon">
                        <svg class="icon icon-svg" aria-hidden="true">
                            <use xlink:href="#shape-arrow-right"></use>
                        </svg>
                    </span>
                </a>
            </div>
        </div>
    </div>

<?php } ?>
