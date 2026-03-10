<?php
if (! defined('ABSPATH')) exit;

use Propeller\Includes\Controller\FavoriteController;
use Propeller\Includes\Controller\PageController;
use Propeller\Includes\Controller\SessionController;
use Propeller\Includes\Enum\PageType;
use Propeller\Includes\Enum\ProductClass;

$is_product_favorite = !is_null($found) && is_array($found) && count($found);

?>
<svg style="display:none">
    <symbol viewBox="0 0 14 14" id="shape-header-close">
        <title><?php echo esc_html(__('Close', 'propeller-ecommerce-v2')); ?></title>
        <path d="M13.656 12.212c.41.41.41 1.072 0 1.481a1.052 1.052 0 0 1-1.485 0L7 8.5l-5.207 5.193a1.052 1.052 0 0 1-1.485 0 1.045 1.045 0 0 1 0-1.481L5.517 7.02.307 1.788a1.045 1.045 0 0 1 0-1.481 1.052 1.052 0 0 1 1.485 0L7.001 5.54 12.208.348a1.052 1.052 0 0 1 1.485 0c.41.408.41 1.072 0 1.48L8.484 7.02l5.172 5.192z" />
    </symbol>
    <symbol viewBox="0 0 16 16" id="shape-error">
        <title><?php echo esc_html(__('Error', 'propeller-ecommerce-v2')); ?></title>
        <path d="M15.75 8A7.751 7.751 0 0 0 .25 8 7.75 7.75 0 0 0 8 15.75 7.75 7.75 0 0 0 15.75 8zM8 9.563a1.437 1.437 0 1 1 0 2.874 1.437 1.437 0 0 1 0-2.874zM6.635 4.395A.375.375 0 0 1 7.01 4h1.98c.215 0 .386.18.375.395l-.232 4.25A.375.375 0 0 1 8.759 9H7.24a.375.375 0 0 1-.374-.355l-.232-4.25z" fill="#E02B27" />
    </symbol>
    <symbol viewBox="0 0 16 12" id="shape-valid">
        <title><?php echo esc_html(__('Valid', 'propeller-ecommerce-v2')); ?></title>
        <path d="m6.566 11.764 9.2-9.253a.808.808 0 0 0 0-1.137L14.634.236a.797.797 0 0 0-1.131 0L6 7.782 2.497 4.259a.797.797 0 0 0-1.131 0L.234 5.397a.808.808 0 0 0 0 1.137l5.2 5.23a.797.797 0 0 0 1.132 0z" fill="#54A023" />
    </symbol>
</svg>
<div id="add_favorite_modal_<?php echo esc_attr(strtolower($product->class)); ?>_<?php echo esc_attr($product->class == ProductClass::Product ? $product->productId : $product->clusterId); ?>" class="propeller-address-modal modal modal-fullscreen-sm-down fade" tabindex="-1" role="dialog" data-bs-backdrop="true" aria-labelledby="propel_modal_add_favorite">
    <div class="modal-dialog modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header propel-modal-header">
                <div id="propel_modal_add_favorite" class="modal-title">
                    <span>
                        <?php echo esc_html(__('Favorite product?', 'propeller-ecommerce-v2')); ?>
                    </span>
                </div>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="<?php echo esc_html(__('Close', 'propeller-ecommerce-v2')); ?>">
                    <span aria-hidden="true">
                        <svg class="icon icon-close">
                            <use class="header-shape-close" xlink:href="#shape-header-close"></use>
                        </svg>
                    </span>
                </button>
            </div>
            <div class="fav-modal modal-body propel-modal-body">
                <?php if ($is_product_favorite) {

                    $foundList = [];
                    foreach ($product->favoriteLists->items as $fav_list) {
                        $fav_list_id = $fav_list->id;
                        foreach (SessionController::get(PROPELLER_USER_FAV_LISTS)->items as $favUserList) {
                            if ($fav_list->id == $favUserList->id)
                                array_push($foundList, $favUserList);
                        }
                    }
                ?>
                    <form name="add-favorite-form" id="add_favorite_form" class="add-favorite form-horizontal validate form-handler modal-edit-form mb-5" method="post">
                        <?php if ($product->class == ProductClass::Product) { ?>
                            <input type="hidden" name="product_id[]" value="<?php echo esc_attr($product->productId); ?>">
                        <?php } else { ?>
                            <input type="hidden" name="cluster_id[]" value="<?php echo esc_attr($product->clusterId); ?>">
                        <?php } ?>

                        <input type="hidden" name="class" value="<?php echo esc_attr($product->class); ?>">
                        <input type="hidden" name="action" value="delete_favorite">

                        <fieldset>
                            <div class="row form-group">
                                <div class="col-form-fields col-12">
                                    <?php $index = 0; ?>
                                    <?php foreach ($foundList as $list) { ?>
                                        <div class="d-flex align-items-center form-check">
                                            <input class="form-check-input" type="checkbox" name="list_id[]" id="list_id_<?php echo esc_attr($list->id); ?>" value="<?php echo esc_attr($list->id); ?>" <?php echo esc_html($index == 0 ? 'checked' : ''); ?> />
                                            <label class="form-check-label" for="list_id_<?php echo esc_attr($list->id); ?>"><?php echo esc_html($list->name); ?></label>
                                        </div>
                                    <?php $index++;
                                    } ?>
                                </div>
                            </div>
                        </fieldset>
                        <div class="row form-group form-group-submit propel-modal-foote">
                            <div class="col-form-fields col-12">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <button type="submit" class="btn-modal btn-proceed w-100 justify-content-center btn-modal-address btn-modal-submit"><?php echo esc_html($is_product_favorite ? __('Remove from favorites', 'propeller-ecommerce-v2') : __('Add to favorites', 'propeller-ecommerce-v2')); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                <?php } ?>

                <?php if ($favorite_lists->itemsFound > 0) { ?>
                    <hr class="mt-5 mb-5" />

                    <form name="add-favorite-form" id="add_favorite_form" class="add-favorite form-horizontal validate form-handler modal-edit-form" method="post">
                        <?php if ($product->class == ProductClass::Product) { ?>
                            <input type="hidden" name="product_id[]" value="<?php echo esc_attr($product->productId); ?>">
                        <?php } else { ?>
                            <input type="hidden" name="cluster_id[]" value="<?php echo esc_attr($product->clusterId); ?>">
                        <?php } ?>

                        <input type="hidden" name="class" value="<?php echo esc_attr($product->class); ?>">

                        <input type="hidden" name="action" value="add_favorite">

                        <fieldset>
                            <div class="row form-group">
                                <div class="col-form-fields col-12">
                                    <div class="row g-3">
                                        <div class="col-12 form-group col-user-country">
                                            <label class="form-label" for="favorite_list"><?php echo esc_html(__('Choose a favorites list', 'propeller-ecommerce-v2')); ?>*</label>

                                            <select name="list_id" class="form-control required">
                                                <?php foreach ($favorite_lists->items as $list) { ?>
                                                    <option value="<?php echo esc_attr($list->id); ?>"><?php echo esc_html($list->name); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <div class="row form-group form-group-submit propel-modal-foote">
                            <div class="col-form-fields col-12">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <button type="submit" class="btn-modal btn-proceed w-100 justify-content-center btn-modal-address btn-modal-submit"><?php echo esc_html(__('Add to favorites', 'propeller-ecommerce-v2')); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                <?php } else { ?>
                    <div class="row propel-modal-footer">
                        <div class="col-12 fav-no-element mb-5">
                            <?php
                            echo esc_html(__("You currently don't have any favorites lists. Please go to your favorites page and create one.", 'propeller-ecommerce-v2'));
                            ?>
                        </div>
                        <div class="col-12">
                            <a class="btn-modal btn-proceed w-100 justify-content-center btn-modal-address btn-modal-submit" href="<?php echo esc_url($this->buildUrl('/' . PageController::get_slug(PageType::FAVORITES_PAGE), '')); ?>"><?php echo esc_html(__('Go to favorites page', 'propeller-ecommerce-v2')); ?></a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>