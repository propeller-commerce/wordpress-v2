<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Enum\PdpNewWindow;

if ($obj->parts->itemsFound > 0 || wp_doing_ajax()) { ?>
    <div class="row align-items-center catalog-result-options">
    <?php } ?>
    <?php if ($obj->parts->itemsFound > 0) { ?>
        <div class="col-12 mb-4 d-md-none">
            <button class="d-flex d-md-none btn-filter-menu" type="button" data-bs-toggle="off-canvas-filters"
                data-bs-target="#filter_container" aria-controls="filter_container"
                aria-expanded="false" aria-label="Toon menu">
                <span><?php echo esc_html(__('Filters', 'propeller-ecommerce-v2')); ?></span>
                <svg class="icon icon-svg" aria-hidden="true">
                    <use xlink:href="#shape-filters"></use>
                </svg>
            </button>
        </div>
    <?php } ?>


    <!-- Machines search placeholder -->
    <?php if ($obj->parts->itemsFound > 0 || wp_doing_ajax()) { ?>
        <div class="machine-search-wrapper col-12 mb-4 mt-4">
            <div class="row">
                <div class="col-12 mb-4 mt-4 row">
                    <label for="term-machine" class="visually-hidden"><?php echo esc_html(__('Search spare parts', 'propeller-ecommerce-v2')); ?></label>
                    <input
                        type="search"
                        name="term"
                        id="term-machine"
                        class="form-control machine-search-input col-10"
                        placeholder="<?php echo esc_html(__('Search spare parts', 'propeller-ecommerce-v2')); ?>"
                        value=""
                        autocomplete="off" />
                    <span class="input-group-text col-2 ps-0">
                        <button class="newcustom" id="spare-parts-btn-search">
                            <svg class="icon icon-search" aria-hidden="true">
                                <use class="header-shape-search" xlink:href="#shape-search"></use>
                            </svg>
                        </button>
                    </span>
                </div>
                <div class="col-12 mb-4 mt-4">
                    <?php
                    if (isset($obj->term) && !empty($obj->term)) {
                        echo esc_html(__('You searched for:', 'propeller-ecommerce-v2')) . ' ' . esc_html($obj->term);
                    }
                    ?>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if ($obj->parts->itemsFound > 0) { ?>
        <!-- Active filters -->
        <div class="col-12 mb-4 mt-4">
            <?php
            if (sizeof($selected_filters) || (isset($obj->term) && !empty($obj->term))) { ?>
                <span class="label"> <?php echo esc_html(__('Selected filters', 'propeller-ecommerce-v2')); ?> </span>
            <?php }

            if (isset($obj->term) && !empty($obj->term)) { ?>
                <a class="btn-active-filter btn-active-filter-term"
                    data-filter='<?php echo esc_html($obj->term); ?>'
                    data-value='<?php echo esc_attr(wp_slash($obj->term)); ?>'
                    data-type='<?php echo esc_html($obj->term); ?>'>
                    <span class="active-filter-name"><?php echo esc_html($obj->term); ?></span>
                    <svg class="icon icon-svg" aria-hidden="true">
                        <use xlink:href="#shape-close"></use>
                    </svg>
                </a>
            <?php }

            foreach ($selected_filters as $selected_filter) {
            ?>
                <a class="btn-active-filter"
                    data-filter='<?php echo esc_html($selected_filter->filter->searchId); ?>'
                    data-value='<?php echo esc_attr(wp_slash($selected_filter->value)); ?>'
                    data-type='<?php echo esc_html($selected_filter->filter->type); ?>'>
                    <span class="active-filter-name"><?php echo esc_html($selected_filter->value); ?></span>
                    <svg class="icon icon-svg" aria-hidden="true">
                        <use xlink:href="#shape-close"></use>
                    </svg>
                </a>
            <?php }
            if (sizeof($selected_filters) || (isset($obj->term) && !empty($obj->term))) { ?>
                <a class="btn-remove-active-filters"><?php echo esc_html(__('Clear all filters', 'propeller-ecommerce-v2')); ?></a>
            <?php } ?>
        </div>
        <div class="col-auto me-auto catalog-result">
            <div class="catalog-result-count">
                <?php if (count($obj->machines)) { ?>
                    <span id="catalog_total"><?php echo esc_html(count($obj->machines)); ?></span>
                    <?php echo esc_html(__('machines', 'propeller-ecommerce-v2')); ?>
                <?php } ?>

                <?php if ($obj->parts->itemsFound > 0) { ?>
                    <?php if (count($obj->machines)) echo ', '; ?>

                    <span id="catalog_total"><?php echo esc_html($obj->parts->itemsFound); ?></span>
                    <?php echo esc_html(__('parts', 'propeller-ecommerce-v2')); ?>
                <?php } ?>
            </div>

            <?php if (PROPELLER_PDP_NEW_TAB != PdpNewWindow::HIDDEN) { ?>
                <div class="catalog-behavior mt-2">
                    <input type="checkbox" id="pdp_new_tab" name="pdp_new_tab" value="1" <?php echo esc_html($obj->get_cookie(PROPELLER_PDP_BEHAVIOR) && $obj->get_cookie(PROPELLER_PDP_BEHAVIOR) === 'true' ? 'checked' : ''); ?> />
                    <label for="pdp_new_tab"><?php echo esc_html(__('Open products in new tab', 'propeller-ecommerce-v2')); ?></label>
                </div>
            <?php } ?>
        </div>

        <?php require $obj->load_template('partials', '/other/propeller-offset-sort.php'); ?>

        <div class="col-auto d-none d-md-flex align-items-center liststyle-options">
            <div class="input-group">
                <a class="input-group-text btn-liststyle" href="" data-liststyle="list" aria-label="<?php echo esc_html(__('Show as list', 'propeller-ecommerce-v2')); ?>" rel="nofollow">
                    <span class="icon">
                        <svg class="icon icon-svg" aria-hidden="true">
                            <use xlink:href="#shape-list"></use>
                        </svg>
                    </span>
                </a>
                <a class="input-group-text btn-liststyle active" href="" data-liststyle="blocks" aria-label="<?php echo esc_html(__('Show as blocks', 'propeller-ecommerce-v2')); ?>" rel="nofollow">
                    <span class="icon">
                        <svg class="icon icon-svg" aria-hidden="true">
                            <use xlink:href="#shape-blocks"></use>
                        </svg>
                    </span>
                </a>
            </div>
        </div>
    <?php } ?>

    <?php if ($obj->parts->itemsFound > 0 || wp_doing_ajax()) { ?>
    </div>
<?php } ?>
