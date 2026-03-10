<?php
    if ( ! defined( 'ABSPATH' ) ) exit; 

    $default_rows_amount = 5;
?>
<svg style="display:none">
    <symbol viewBox="0 0 14 14" id="shape-remove">
        <title><?php echo esc_html(__('Remove', 'propeller-ecommerce-v2')); ?></title>
        <path d="M13.656 12.212c.41.41.41 1.072 0 1.481a1.052 1.052 0 0 1-1.485 0L7 8.5l-5.207 5.193a1.052 1.052 0 0 1-1.485 0 1.045 1.045 0 0 1 0-1.481L5.517 7.02.307 1.788a1.046 1.046 0 0 1 0-1.481 1.052 1.052 0 0 1 1.485 0L7.001 5.54 12.207.348a1.052 1.052 0 0 1 1.486 0c.41.408.41 1.072 0 1.48L8.484 7.02l5.172 5.192z" />
    </symbol>
</svg>
<div class="container-fluid px-0 propeller-price-request <?php echo esc_html(apply_filters('propel_quick_order_classes', '')); ?>">
    <div class="row">
        <div class="col-12 col-lg-12">
            <div class="h3">
                <?php echo esc_html(__('Add products', 'propeller-ecommerce-v2')) ?>
            </div>
            <form class="price-request-form">
                <input type="hidden" name="action" value="propel_do_price_request">

                <div class="price-request-table" id="price-request-table">
                    <div class="price-request-table-header row ">
                        <div class="col-2 product-code"><?php echo esc_html(__('Article no. / SKU', 'propeller-ecommerce-v2')); ?></div>
                        <div class="col-4 product-name ps-0"><?php echo esc_html(__('Product name', 'propeller-ecommerce-v2')); ?></div>
                        <div class="col-1 product-quantity ps-0"><?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?></div>
                        <div class="col-2 product-total ps-0">&nbsp;</div>
                    </div>
                    <?php if (count($products)) { ?>
                        <?php
                        $index = 0;
                        foreach ($products as $product) { ?>
                            <div class="price-request-row row" id="row-<?php echo esc_html($index); ?>">
                                <div class="col-5 col-sm-2 col-lg-2 product-code">
                                    <input type="text" name="product-code-row[<?php echo esc_attr($index); ?>]" value="<?php echo esc_attr($product->code); ?>" class="form-control product-code" id="product-code-row-<?php echo esc_attr($index); ?>" data-row="<?php echo esc_attr($index); ?>">
                                </div>
                                <div class="col-7 col-sm-5 col-lg-4 product-name ps-0">
                                    <input type="text" readonly name="product-name-row[<?php echo esc_attr($index); ?>]" value="<?php echo esc_attr($product->name); ?>" readonly class="form-control product-name readonly" id="product-name-row-<?php echo esc_attr($index); ?>" data-row="<?php echo esc_attr($index); ?>">
                                </div>
                                <div class="col-8 col-sm-3 col-lg-1 product-quantity px-sm-0">
                                    <div class="input-group product-request-quantity">
                                        <label class="visually-hidden" for="product-quantity-row-<?php echo esc_attr($index); ?>"><?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?></label>
                                        <span class="incr-decr">
                                            <button type="button" class="btn-request-quantity"
                                                data-type="minus">-</button>
                                        </span>
                                        <input
                                            type="number"
                                            ondrop="return false;"
                                            onpaste="return false;"
                                            onkeypress="return event.charCode>=48 && event.charCode<=57"
                                            name="product-quantity-row[<?php echo esc_attr($index); ?>]"
                                            value="<?php echo esc_attr($product->quantity); ?>" class="form-control product-request-quantity"
                                            id="product-quantity-row-<?php echo esc_html($index); ?>" data-row="<?php echo esc_attr($index); ?>" data-id="<?php echo esc_attr($product->id); ?>"
                                            data-unit="<?php echo esc_attr($product->unit); ?>" data-minquantity="<?php echo esc_attr($product->minquantity); ?>">
                                        <span class="incr-decr">
                                            <button type="button" class="btn-request-quantity" data-type="plus">+</button>
                                        </span>
                                    </div>
                                </div>
                                <div class="remove-pr-row col-1 ps-4 d-flex align-items-center" data-row="<?php echo esc_attr($index); ?>">
                                    <button type="button" class="remove-pr-row" data-code="<?php echo esc_attr($product->code); ?>">
                                        <svg class="icon icon-remove">
                                            <use class="shape-remove" xlink:href="#shape-remove"></use>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        <?php
                            $index++;
                        } ?>
                        <div class="price-request-row row" id="row-<?php echo esc_html((int) $index); ?>">
                            <div class="col-5 col-sm-2 col-lg-2 product-code">
                                <input type="text" name="product-code-row[<?php echo esc_html((int) $index); ?>]" value="" class="form-control product-code" id="product-code-row-<?php echo esc_html((int) $index); ?>" data-row="<?php echo esc_attr($index); ?>">
                                <input type="hidden" name="product-id-row[<?php echo esc_html((int) $index); ?>]" value="" class="product-id" id="product-id-row-<?php echo esc_html((int) $index); ?>">
                            </div>
                            <div class="col-7 col-sm-5 col-lg-4 product-name ps-0">
                                <input type="text" readonly name="product-name-row[<?php echo esc_html((int) $index); ?>]" value="" readonly class="form-control product-name readonly" id="product-name-row-<?php echo esc_html((int) $index); ?>" data-row="<?php echo esc_attr($index); ?>">
                            </div>
                            <div class="col-8 col-sm-3 col-lg-1 product-quantity px-sm-0">
                                <div class="input-group product-request-quantity">
                                    <label class="visually-hidden" for="product-quantity-row-<?php echo esc_html((int) $index); ?>"><?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?></label>
                                    <span class="incr-decr">
                                        <button type="button" class="btn-request-quantity"
                                            data-type="minus">-</button>
                                    </span>
                                    <input
                                        type="number"
                                        ondrop="return false;"
                                        onpaste="return false;"
                                        onkeypress="return event.charCode>=48 && event.charCode<=57"
                                        name="product-quantity-row[<?php echo esc_html((int) $index); ?>]" value="" class="form-control product-request-quantity" id="product-quantity-row-<?php echo esc_html((int) $index); ?>" data-row="<?php echo esc_attr($index); ?>" data-id=""
                                        data-unit="1" data-minquantity="1">
                                    <span class="incr-decr">
                                        <button type="button" class="btn-request-quantity" data-type="plus">+</button>
                                    </span>
                                </div>
                            </div>
                            <div class="remove-row col-1 ps-4 d-flex align-items-center" data-row="<?php echo esc_attr($index); ?>">
                                <button type="button" class="remove-pr-row">
                                    <svg class="icon icon-remove">
                                        <use class="shape-remove" xlink:href="#shape-remove"></use>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    <?php } else { ?>
                        <?php for ($i = 0; $i < $default_rows_amount; $i++) { ?>
                            <div class="price-request-row row" id="row-<?php echo esc_html((int) $i); ?>">
                                <div class="col-5 col-sm-2 col-lg-2 product-code">
                                    <input type="text" name="product-code-row[<?php echo esc_html((int) $i); ?>]" value="" class="form-control product-code" id="product-code-row-<?php echo esc_html((int) $i); ?>" data-row="<?php echo esc_attr($i); ?>">
                                    <input type="hidden" name="product-id-row[<?php echo esc_html((int) $i); ?>]" value="" class="product-id" id="product-id-row-<?php echo esc_html((int) $i); ?>">
                                </div>
                                <div class="col-7 col-sm-5 col-lg-4 product-name ps-0">
                                    <input type="text" name="product-name-row[<?php echo esc_html((int) $i); ?>]" value="" readonly class="form-control product-name" id="product-name-row-<?php echo esc_html((int) $i); ?>" data-row="<?php echo esc_attr($i); ?>">
                                </div>
                                <div class="col-8 col-sm-3 col-lg-1 product-quantity px-sm-0">
                                    <div class="input-group product-request-quantity">
                                        <label class="visually-hidden" for="product-quantity-row-<?php echo esc_html((int) $i); ?>"><?php echo esc_html(__('Quantity', 'propeller-ecommerce-v2')); ?></label>
                                        <span class="incr-decr">
                                            <button type="button" class="btn-request-quantity"
                                                data-type="minus">-</button>
                                        </span>
                                        <input
                                            type="number"
                                            ondrop="return false;"
                                            onpaste="return false;"
                                            onkeypress="return event.charCode>=48 && event.charCode<=57"
                                            name="product-quantity-row[<?php echo esc_html((int) $i); ?>]" value="" class="form-control product-request-quantity" id="product-quantity-row-<?php echo esc_html((int) $i); ?>" data-row="<?php echo esc_attr($i); ?>" data-id=""
                                            data-unit="1" data-minquantity="1">
                                        <span class="incr-decr">
                                            <button type="button" class="btn-request-quantity" data-type="plus">+</button>
                                        </span>
                                    </div>
                                </div>
                                <div class="remove-row col-1 ps-4 d-flex align-items-center" data-row="<?php echo esc_attr($i); ?>">
                                    <button type="button" class="remove-pr-row">
                                        <svg class="icon icon-remove">
                                            <use class="shape-remove" xlink:href="#shape-remove"></use>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>

                </div>
                <div class="add-price-request-row row mt-2 mb-5">
                    <div class="col-12">
                        <button type="button" class="add-order-row" id="add-pr-row"><?php echo esc_html(__('Add more rows', 'propeller-ecommerce-v2')); ?></button>
                    </div>
                </div>
                <div class="row align-items-start">
                    <div class="col-12">
                        <div class="title h3"><?php echo esc_html(__('Comments', 'propeller-ecommerce-v2')); ?></div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-form-fields col-12 col-md-7">
                        <div class="row g-3">
                            <div class="col-12 form-group col-notes">
                                <textarea name="request_comment" class="form-control" id="request_comment"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col me-md-5 d-flex justify-content-start">
                        <button type="submit" class="btn-price-request-submit">
                            <?php echo esc_html(__('Send request', 'propeller-ecommerce-v2')); ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>