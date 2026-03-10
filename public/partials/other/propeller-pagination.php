<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if ($paging_data->pages > 1) { ?>
    <div class="col-12">
        <div class="row propeller-listing-pagination" data-min="1" data-max="<?php echo esc_attr($paging_data->pages); ?>" data-current="<?php echo esc_attr($paging_data->page); ?>">
            <div class="col-12 d-flex align-items-center justify-content-center ">
                <?php if ($paging_data->pages > 7) { ?>
                    <a class="previous first-page page-item <?php echo esc_attr($prev_disabled); ?>" data-prop_name="<?php echo esc_html($prop_name); ?>" data-prop_value="<?php echo esc_attr($prop_value); ?>" data-page="1" data-action="<?php echo esc_html($do_action); ?>" data-obid="<?php echo esc_attr($obid); ?>" <?php echo esc_html($prev_disabled); ?>>
                        <span class="d-none d-md-flex"><?php echo esc_html(__('First', 'propeller-ecommerce-v2')); ?></span>
                    </a>
                    <a class="previous page-item <?php echo esc_attr($prev_disabled); ?>" data-prop_name="<?php echo esc_attr($prop_name); ?>" data-prop_value="<?php echo esc_attr($prop_value); ?>" data-page="<?php echo esc_attr($prev); ?>" data-action="<?php echo esc_attr($do_action); ?>" data-obid="<?php echo esc_attr($obid); ?>" <?php echo esc_html($prev_disabled); ?>>
                        <span class="d-none d-md-flex"><?php echo esc_html(__('Previous', 'propeller-ecommerce-v2')); ?></span>
                    </a>
                <?php } ?>
                <?php for ($key = 1; $key <= $paging_data->pages; $key++) {
                    if ($key == 2) { ?>
                        <span class="page-item dots" id="dots-prev">&hellip;</span>
                    <?php }
                    if ($key == $paging_data->pages) { ?>
                        <span class="page-item dots" id="dots-next">&hellip;</span>
                    <?php } ?>
                    <a class="page-item <?php if ($key == $paging_data->page) echo 'active'; ?>" data-prop_name="<?php echo esc_attr($prop_name); ?>" data-prop_value="<?php echo esc_attr($prop_value); ?>" data-page="<?php echo esc_attr($key); ?>" data-action="<?php echo esc_attr($do_action); ?>" data-obid="<?php echo esc_attr($obid); ?>" <?php if ($key == 1) echo esc_attr($prev_disabled); ?> <?php if ($key == $paging_data->pages) echo esc_attr($next_disabled); ?>>
                        <?php echo esc_html($key); ?>
                    </a>
                <?php } ?>
                <?php if ($paging_data->pages > 7) { ?>
                    <a class="next page-item me-3 <?php echo esc_attr($next_disabled); ?>" data-prop_name="<?php echo esc_attr($prop_name); ?>" data-prop_value="<?php echo esc_attr($prop_value); ?>" data-page="<?php echo esc_attr($next); ?>" data-action="<?php echo esc_attr($do_action); ?>" data-obid="<?php echo esc_attr($obid); ?>" <?php echo esc_html($next_disabled); ?>>
                        <span class="d-none d-md-flex"><?php echo esc_html(__('Next', 'propeller-ecommerce-v2')); ?></span>
                    </a>
                    <a class="next last-page page-item <?php echo esc_attr($next_disabled); ?>" data-prop_name="<?php echo esc_html($prop_name); ?>" data-prop_value="<?php echo esc_attr($prop_value); ?>" data-page="<?php echo esc_html($paging_data->pages); ?>" data-action="<?php echo esc_html($do_action); ?>" data-obid="<?php echo esc_attr($obid); ?>" <?php echo esc_html($next_disabled); ?>>
                        <span class="d-none d-md-flex"><?php echo esc_html(__('Last', 'propeller-ecommerce-v2')); ?></span>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>
