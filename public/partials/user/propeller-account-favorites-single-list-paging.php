<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$total_pages = $total_items > 0 ? ceil($total_items / $offset) : 1;

$prev = $current_page - 1;
$prev_disabled = false;

if ($prev < 1) {
    $prev = 1;
    $prev_disabled = 'disabled';
}
    
$next = $current_page + 1;
$next_disabled = false;

if ($current_page == $total_pages)
    $next_disabled = 'disabled';
    
if ($total_pages > 1) { ?>
    <nav class="propeller-account-pagination" data-action="get_favorite_list_page" data-list-id="<?php echo esc_attr($list_id); ?>" data-min="1" data-max="<?php echo esc_attr($total_pages); ?>" data-current="<?php echo esc_attr($current_page); ?>">
        <div class="row">
            <div class="col-12 d-flex align-items-center justify-content-center">
                <a class="previous page-item <?php echo esc_attr($prev_disabled); ?>" data-page="<?php echo esc_attr($prev); ?>" <?php echo esc_attr($prev_disabled); ?>>
                    <span class="icon">
                        <svg class="icon icon-svg" aria-hidden="true">
                            <use xlink:href="#shape-arrow-left"></use>
                        </svg>
                    </span>
                </a>
                
                <span class="page-totals"><?php echo esc_html(__('page', 'propeller-ecommerce-v2')); ?> <?php echo esc_html($current_page); ?> <?php echo esc_html(__('from', 'propeller-ecommerce-v2')); ?> <?php echo esc_html($total_pages); ?></span>
                
                <a class="next page-item <?php echo esc_attr($next_disabled); ?>" data-page="<?php echo esc_attr($next); ?>" <?php echo esc_attr($next_disabled); ?>>
                    <span class="icon">
                        <svg class="icon icon-svg" aria-hidden="true">
                            <use xlink:href="#shape-arrow-right"></use>
                        </svg>
                    </span>
                </a>    
            </div>                    
        </div>
    </nav>
<?php } ?>


