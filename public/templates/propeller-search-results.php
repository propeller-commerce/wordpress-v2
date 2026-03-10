<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<svg style="display:none;">
    <symbol viewBox="0 0 18 14" id="shape-list">
        <title>List</title>
        <g fill-rule="evenodd">
            <rect width="18" height="2" rx=".5" />
            <rect y="6" width="18" height="2" rx=".5" />
            <rect y="12" width="18" height="2" rx=".5" />
        </g>
    </symbol>
    <symbol viewBox="0 0 18 14" id="shape-blocks">
        <title>Blocks </title>
        <g fill-rule="evenodd">
            <rect width="4" height="6" rx=".5" />
            <rect x="7" width="4" height="6" rx=".5" />
            <rect x="14" width="4" height="6" rx=".5" />
            <rect y="8" width="4" height="6" rx=".5" />
            <rect x="7" y="8" width="4" height="6" rx=".5" />
            <rect x="14" y="8" width="4" height="6" rx=".5" />
        </g>
    </symbol>
    <symbol viewBox="0 0 18 19" id="shape-filters">
        <title>Filters </title>
        <path d="M15.625.75H1.875C.84.75 0 1.59 0 2.625v13.75c0 1.035.84 1.875 1.875 1.875h13.75c1.035 0 1.875-.84 1.875-1.875V2.625C17.5 1.59 16.66.75 15.625.75zm.625 15.625a.627.627 0 0 1-.625.625H1.875a.627.627 0 0 1-.625-.625V2.625c0-.344.281-.625.625-.625h13.75c.344 0 .625.281.625.625v13.75zM12.656 5.75H8.75V4.188a.94.94 0 0 0-.938-.938H5.937A.94.94 0 0 0 5 4.188V5.75H3.594a.47.47 0 0 0-.469.469v.312A.47.47 0 0 0 3.594 7H5v1.563a.94.94 0 0 0 .937.937h1.875a.94.94 0 0 0 .938-.937V7h3.906a.47.47 0 0 0 .469-.469V6.22a.47.47 0 0 0-.469-.469zM7.5 8.25H6.25V4.5H7.5v3.75zM13.281 12H12.5v-1.562a.94.94 0 0 0-.938-.938H9.687a.94.94 0 0 0-.937.938V12H4.219a.47.47 0 0 0-.469.469v.312a.47.47 0 0 0 .469.469H8.75v1.563a.94.94 0 0 0 .937.937h1.875a.94.94 0 0 0 .938-.937V13.25h.781a.47.47 0 0 0 .469-.469v-.312a.47.47 0 0 0-.469-.469zm-2.031 2.5H10v-3.75h1.25v3.75z" />
    </symbol>
    <symbol viewBox="0 0 8 14" id="shape-arrow-left">
        <title>Arrow left </title>
        <path d="m7.69.303.004.005a1.055 1.055 0 0 1 0 1.487L2.522 7l5.172 5.205a1.055 1.055 0 0 1 0 1.487 1.041 1.041 0 0 1-1.478 0L.306 7.744a1.055 1.055 0 0 1 0-1.488h.001L.58 5.98 6.216.308A1.041 1.041 0 0 1 7.69.303z" fill-rule="evenodd" />
    </symbol>
    <symbol viewBox="0 0 8 14" id="shape-arrow-right">
        <title>Arrow right </title>
        <path d="M.31.303.307.308a1.055 1.055 0 0 0 0 1.487L5.478 7 .306 12.205a1.055 1.055 0 0 0 0 1.487 1.041 1.041 0 0 0 1.478 0l5.91-5.948a1.055 1.055 0 0 0 0-1.488h-.001L7.42 5.98 1.784.308A1.041 1.041 0 0 0 .31.303z" fill-rule="evenodd" />
    </symbol>
    <symbol viewBox="0 0 14 14" id="shape-close">
        <title>Close</title>
        <path d="M1.795.308 7 5.512 12.205.308a1.052 1.052 0 1 1 1.487 1.487L8.488 7l5.204 5.205a1.052 1.052 0 1 1-1.487 1.487L7 8.488l-5.205 5.204a1.052 1.052 0 1 1-1.487-1.487L5.512 7 .308 1.795A1.052 1.052 0 1 1 1.795.308z" fill-rule="evenodd" />
    </symbol>
</svg>
<div class="container-fluid px-0 <?php echo esc_html(apply_filters('propel_product_listing_classes', 'propeller-product-listing')); ?>">
    <?php if ($this->data->itemsFound > 0) { ?>
        <div class="row">
            <div class="col-12">
                <h1 class="title <?php echo esc_html(apply_filters('propel_listing_title_classes', '')); ?>"><?php echo esc_html(__('You searched for', 'propeller-ecommerce-v2')); ?> '<?php echo esc_html(urldecode($term)); ?>'</h1>
            </div>
        </div>
    <?php } ?>
    <div class="row d-flex" data-action="<?php echo esc_attr($this->filters->get_action()) ?>" data-prop_value="<?php echo esc_attr($this->filters->get_slug()) ?>" data-prop_name="<?php echo esc_attr($this->filters->get_prop()) ?>" data-liststyle="<?php echo esc_attr($this->filters->get_liststyle()); ?>">
        <div class="col-12 col-md-4 col-xl-3 propeller-catalog-filters <?php echo esc_html(apply_filters('propel_catalog_filters_classes', '')); ?>" id="propeller-catalog-filters">
            <?php if ($this->data->itemsFound > 0) { ?>
                <div class="row g-0 fixed-filter-header d-flex d-md-none">
                    <div class="col">
                        <header class="navbar navbar-dark justify-content-between">
                            <strong class="h2"><?php echo esc_html(__('Filters', 'propeller-ecommerce-v2')); ?></strong>
                            <button type="button" class="close-filters" aria-label="Close">
                                <svg class="icon icon-svg" aria-hidden="true">
                                    <use xlink:href="#shape-close"></use>
                                </svg>
                            </button>
                        </header>
                    </div>
                </div>

                <!-- Catalog menu  -->
                <?php apply_filters('propel_category_menu', $this->data); ?>

                <!-- TEXT,ENUM FILTERS -->
                <?php apply_filters('propel_category_filters', $this->filters); ?>

            <?php } ?>
            <div class="row g-0 fixed-menu-footer d-flex d-md-none">
                <div class="col-8 d-flex align-items-center">
                    <button type="button" class="btn-apply-filters" id="filter-menu-show-selection">
                        <?php echo esc_html(__('Show', 'propeller-ecommerce-v2')); ?> <span class="catalog-filtered-results" id="filtered_results"><?php echo esc_html($this->data->itemsFound); ?></span> <?php echo esc_html(__('results', 'propeller-ecommerce-v2')); ?>
                    </button>
                </div>
                <div class="col-4 d-flex align-items-center justify-content-end">
                    <button type="button" class="btn-clear-filters" id="filter-menu-clear-selection">
                        <?php echo esc_html(__('Clear filters', 'propeller-ecommerce-v2')); ?>
                    </button>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-8 col-xl-9" id="propeller-product-list">

            <?php apply_filters('propel_category_grid', $this, $this->products, $paging_data, $sort, $prop_name, $prop_value, $do_action); ?>

        </div>
    </div>
</div>
<div class="mobile-filter-wrapper"></div>