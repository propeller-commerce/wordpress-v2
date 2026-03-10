<?php
if ( ! defined( 'ABSPATH' ) ) exit;
use Propeller\Includes\Enum\PdpNewWindow;

if ($data->itemsFound > 0) { ?>

    <?php
    if ($data->page == 1)
        $nProductCountPerPrevPage = 1;
    else
        $nProductCountPerPrevPage = ($data->page - 1) * $data->offset + 1;
    if ($data->page * $data->offset < $data->itemsFound)
        $nProductCountPerCurrentPage = $data->page * $data->offset;
    else
        $nProductCountPerCurrentPage = $data->itemsFound;
    ?>

    <div class="row align-items-center catalog-result-options">
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
        <div class="col-auto me-auto catalog-result">
            <div class="catalog-result-count"><?php echo esc_html($nProductCountPerPrevPage); ?> - <?php echo esc_html($nProductCountPerCurrentPage); ?> <?php echo esc_html(__('from', 'propeller-ecommerce-v2')); ?> <span id="catalog_total"><?php echo esc_html($data->itemsFound); ?></span> <?php echo esc_html(__('results', 'propeller-ecommerce-v2')); ?></div>

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
    </div>
<?php } ?>

<?php apply_filters('propel_selected_filters', $obj); ?>

<?php if ($data->itemsFound == 0) { ?>
    <div class="row mb-5">
        <div class="col-12">
            <h1 class="title <?php echo esc_attr(apply_filters('propel_listing_title_classes', '')); ?>"><?php echo esc_html(__('No results', 'propeller-ecommerce-v2')); ?></h1>
            <p>
                <?php echo esc_html(__('Go to our', 'propeller-ecommerce-v2')); ?> <a href="/" class="back-link"><?php echo esc_html(__('home page', 'propeller-ecommerce-v2')); ?></a>.
            </p>
        </div>
    </div>
<?php } ?>
