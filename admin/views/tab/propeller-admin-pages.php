<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php

use Propeller\Includes\Enum\PageType;
use Propeller\Propeller;

$index = 0;

$page_sluggable_hint = "Should this page be handled by Propeller's rewrite rules? Will be handled by Wordpress default rewrite rules if left unchecked";
$is_my_account_page_hint = "If checked, this page will be the default \"My Account Details\" page";
$account_page_is_parent_hint = "If checked, this page will be a child of the \"My Account Details\" page and the URL will contain the \"My Account Details\" slug as prefix";

$shortcodes = Propeller::$fe_shortcodes;


$ref = 'Propeller\Custom\Includes\Enum\PageType';

$page_types = class_exists($ref, true)
    ? $ref
    : PageType::class;

?>
<div class="container-fluid propel-admin-panel">
    <div class="row propeller-admin-title mb-4">
        <div class="col-12 col-md-6">
            <h1 class="mb-2 font-weight-bold">
                <?php echo wp_kses_post(__('Pages Configuration', 'propeller-ecommerce-v2')); ?>
            </h1>
            <small class="d-block text-secondary">
                <?php echo wp_kses_post(__('Manage and configure all webshop pages and their settings', 'propeller-ecommerce-v2')); ?>
            </small>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-10 col-lg-9">
            <form method="POST" class="propel-admin-form" action="#" id="propel_pages_form">
                <input type="hidden" name="action" value="save_propel_pages">
                <input type="hidden" name="delete_pages" id="delete_pages" value="">

                <div class="propel-pages-container">
                    <div class="accordion-container mb-3">

                        <?php foreach ($pages_result as $index => $page) { ?>
                            <div class="ac propel-page-acc-row">
                                <h2 class="ac-header">
                                    <button type="button" class="ac-trigger"><?php echo esc_attr($page->page_name); ?></button>
                                    <button type="button" class="delete-btn btn-close-style" data-id="<?php echo intval($page->id); ?>" data-name="<?php echo esc_attr($page->page_name); ?>" title="<?php echo wp_kses_post(__('Delete page', 'propeller-ecommerce-v2')); ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </button>
                                </h2>
                                <div class="ac-panel">
                                    <div class="propel-page-row ac-text" data-index="<?php echo intval($index); ?>">
                                        <input type="hidden" name="page[<?php echo intval($index); ?>][id]" value="<?php echo intval($page->id); ?>">

                                        <div class="row g-3">
                                            <div class="col-md-11">
                                                <div class="row g-3">
                                                    <div class="form-group col-md-4">
                                                        <label for="pagename_<?php echo intval($index); ?>" class="font-weight-medium"><?php echo wp_kses_post(__('Page name', 'propeller-ecommerce-v2')); ?></label>
                                                        <input type="text" id="pagename_<?php echo intval($index); ?>" class="border form-control" placeholder="<?php echo wp_kses_post(__('Page name', 'propeller-ecommerce-v2')); ?>" name="page[<?php echo intval($index); ?>][page_name]" value="<?php echo esc_attr($page->page_name); ?>" required>
                                                    </div>

                                                    <div class="form-group col-md-4">
                                                        <label class="font-weight-medium" for="pagetype_<?php echo intval($index); ?>"><?php echo wp_kses_post(__('Type', 'propeller-ecommerce-v2')); ?></label>
                                                        <select class="border form-control" id="pagetype_<?php echo intval($index); ?>" name="page[<?php echo intval($index); ?>][page_type]">
                                                            <?php foreach ($page_types::getConstants() as $const => $name) { ?>
                                                                <option value="<?php echo esc_attr($name); ?>" <?php echo (bool) ($page->page_type == $name) ? 'selected' : ''; ?>><?php echo esc_html($name); ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-4">
                                                        <label class="font-weight-medium" for="pageshortcode_<?php echo intval($index); ?>"><?php echo wp_kses_post(__('Shortcode', 'propeller-ecommerce-v2')); ?></label>
                                                        <select class="border form-control" id="pageshortcode_<?php echo intval($index); ?>" name="page[<?php echo intval($index); ?>][page_shortcode]">
                                                            <?php foreach (Propeller::$fe_shortcodes as $shortcode => $method) { ?>
                                                                <option value="<?php echo esc_attr($shortcode); ?>" <?php echo (bool) ($page->page_shortcode == $shortcode) ? 'selected' : ''; ?>><?php echo esc_html($shortcode); ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row g-3">
                                                    <div class="form-group col-4">

                                                        <input type="checkbox" id="page_sluggable_<?php echo intval($index); ?>" class="form-check-input" title="<?php echo esc_attr($page_sluggable_hint); ?>" name="page[<?php echo intval($index); ?>][page_sluggable]" value="1" <?php echo isset($page->page_sluggable) && intval($page->page_sluggable) == 1 ? 'checked' : ''; ?>>
                                                        <label class="form-check-label" for="page_sluggable_<?php echo intval($index); ?>"><?php echo wp_kses_post(__('Apply Read/Write rules', 'propeller-ecommerce-v2')); ?></label>

                                                    </div>

                                                    <div class="form-group col-4">

                                                        <input type="checkbox" id="is_my_account_page_<?php echo intval($index); ?>" class="form-check-input" title="<?php echo esc_attr($is_my_account_page_hint); ?>" name="page[<?php echo intval($index); ?>][is_my_account_page]" value="1" <?php echo isset($page->is_my_account_page) && intval($page->is_my_account_page) == 1 ? 'checked' : ''; ?>>
                                                        <label class="form-check-label" for="is_my_account_page_<?php echo intval($index); ?>"><?php echo wp_kses_post(__('Is My account page', 'propeller-ecommerce-v2')); ?></label>

                                                    </div>

                                                    <div class="form-group col-4">

                                                        <input type="checkbox" id="account_page_is_parent_<?php echo intval($index); ?>" class="form-check-input" title="<?php echo esc_attr($account_page_is_parent_hint); ?>" name="page[<?php echo intval($index); ?>][account_page_is_parent]" value="1" <?php echo isset($page->account_page_is_parent) && intval($page->account_page_is_parent) == 1 ? 'checked' : ''; ?>>
                                                        <label class="form-check-label" for="account_page_is_parent_<?php echo intval($index); ?>"><?php echo wp_kses_post(__('Child of My Account', 'propeller-ecommerce-v2')); ?></label>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label class="font-weight-medium for=" pageslug_<?php echo intval($index); ?>"><?php echo wp_kses_post(__('Slug', 'propeller-ecommerce-v2')); ?></label>
                                                <input type="text" id="pageslug_<?php echo intval($index); ?>" class="border form-control" placeholder="<?php echo wp_kses_post(__('Slug', 'propeller-ecommerce-v2')); ?>" name="page[<?php echo intval($index); ?>][page_slug]" value="<?php echo esc_attr($page->page_slug); ?>" required>
                                            </div>

                                            <?php /*
                            <div class="col-md-4">
                                <div class="form-group col">
                                    <label><?php echo wp_kses_post(__('Slug(s)', 'propeller-ecommerce-v2')); ?></label>

                                    <?php 
                                        $last_slug_id = 0; 
                                        $last_page_id = $page->id; 
                                    ?>

                                    <div class="page-slug-containers page-slugs-container-<?php echo intval($index); ?>" data-index="<?php echo intval($index); ?>" data-page_id="<?php echo esc_attr($slug->page_id); ?>">
                                        <?php foreach ($page->slugs as $slug) { ?>
                                            <div class="row page-slug-row" data-id="<?php echo esc_attr($slug->id); ?>" data-page_id="<?php echo esc_attr($slug->page_id); ?>">
                                                <input type="hidden" name="page[<?php echo intval($index); ?>][slugs][slug_id][<?php echo esc_attr($slug->id); ?>]" value="<?php echo esc_attr($slug->id); ?>">
                                                <input type="hidden" name="page[<?php echo intval($index); ?>][slugs][slug_exists][<?php echo esc_attr($slug->id); ?>]" value="1">

                                                <div class="col-3">
                                                    <select class="form-control page-slugs-languages" name="page[<?php echo intval($index); ?>][slugs][slug_lang][<?php echo esc_attr($slug->id); ?>]">
                                                    <?php foreach ($slug_langs as $lng) { ?>
                                                        <option value="<?php echo esc_attr($lng); ?>" <?php echo $lng == $slug->language ? 'selected' : ''; ?>><?php echo esc_html($lng); ?></option>
                                                    <?php } ?>
                                                    </select> 
                                                </div>
                                                <div class="col-8">
                                                    <input type="text" class="border form-control" placeholder="<?php echo wp_kses_post(__('Page slug', 'propeller-ecommerce-v2')); ?>" name="page[<?php echo intval($index); ?>][slugs][slug][<?php echo esc_attr($slug->id); ?>]" value="<?php echo esc_attr($slug->slug); ?>">
                                                </div>
                                                <div class="col-1">
                                                    <button type="button" class="btn btn-primary propel-add-lng-btn" data-id="<?php echo esc_attr($slug->id); ?>" data-page_id="<?php echo esc_attr($slug->page_id); ?>">+</button>
                                                </div>
                                            </div>
                                            <?php $last_slug_id = $slug->id; ?>
                                        <?php } ?>

                                        <?php if (count($page->slugs) < count($slug_langs)) { ?>
                                            <?php $last_slug_id++; ?>

                                            <div class="row page-slug-row" data-id="<?php echo esc_attr($last_slug_id); ?>" data-page_id="<?php echo esc_attr($last_page_id); ?>">
                                                <input type="hidden" name="page[<?php echo intval($index); ?>][slugs][slug_id][<?php echo esc_attr($last_slug_id); ?>]" value="">
                                                <input type="hidden" name="page[<?php echo intval($index); ?>][slugs][slug_exists][<?php echo esc_attr($last_slug_id); ?>]" value="0">

                                                <div class="col-3">
                                                    <select class="form-control page-slugs-languages" name="page[<?php echo intval($index); ?>][slugs][slug_lang][<?php echo esc_attr($last_slug_id); ?>]">
                                                    <?php foreach ($slug_langs as $lng) { ?>
                                                        <option value="<?php echo esc_attr($lng); ?>"><?php echo esc_html($lng); ?></option>
                                                    <?php } ?>
                                                    </select> 
                                                </div>
                                                <div class="col-8">
                                                    <input type="text" class="border form-control" placeholder="<?php echo wp_kses_post(__('Page slug', 'propeller-ecommerce-v2')); ?>" name="page[<?php echo intval($index); ?>][slugs][slug][<?php echo esc_attr($last_slug_id); ?>]" value="">
                                                </div>
                                                <div class="col-1">
                                                    <button type="button" class="btn btn-primary propel-add-lng-btn" data-id="<?php echo esc_attr($last_slug_id); ?>" data-page_id="<?php echo esc_attr($last_page_id); ?>">+</button>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    
                                </div>
                            </div> 
                            */ ?>

                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php $index++; ?>
                        <?php } ?>

                        <?php if (count($pages_result) == 0) { ?>
                            <div class="ac propel-page-acc-row">
                                <h2 class="ac-header">
                                    <button type="button" class="ac-trigger"><?php echo wp_kses_post(__('New page', 'propeller-ecommerce-v2')); ?></button>
                                </h2>
                                <div class="ac-panel">
                                    <div class="propel-page-row ac-text" data-index="<?php echo intval($index); ?>">
                                        <input type="hidden" name="page[<?php echo intval($index); ?>][id]" value="0">

                                        <div class="row g-3">
                                            <div class="col-md-7">
                                                <div class="row g-3">
                                                    <div class="form-group col-md-4">
                                                        <label class="font-weight-medium" for="pagename_<?php echo intval($index); ?>"><?php echo wp_kses_post(__('Page name', 'propeller-ecommerce-v2')); ?></label>
                                                        <input type="text" id="pagename_<?php echo intval($index); ?>" class="border form-control" placeholder="<?php echo wp_kses_post(__('Page name', 'propeller-ecommerce-v2')); ?>" name="page[<?php echo intval($index); ?>][page_name]" value="">
                                                    </div>

                                                    <div class="form-group col-md-4">
                                                        <label class="font-weight-medium" for="pagetype_<?php echo intval($index); ?>"><?php echo wp_kses_post(__('Type', 'propeller-ecommerce-v2')); ?></label>
                                                        <select class="border form-control" id="pagetype_<?php echo intval($index); ?>" name="page[<?php echo intval($index); ?>][page_type]">
                                                            <?php foreach ($page_types::getConstants() as $const => $name) { ?>
                                                                <option value="<?php echo esc_attr($name); ?>"><?php echo esc_html($name); ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-4">
                                                        <label class="font-weight-medium" for="pageshortcode_<?php echo intval($index); ?>"><?php echo wp_kses_post(__('Shortcode', 'propeller-ecommerce-v2')); ?></label>
                                                        <select class="border form-control" id="pageshortcode_<?php echo intval($index); ?>" name="page[<?php echo intval($index); ?>][page_shortcode]">
                                                            <?php foreach (Propeller::$fe_shortcodes as $shortcode => $method) { ?>
                                                                <option value="<?php echo esc_attr($shortcode); ?>"><?php echo esc_html($shortcode); ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row g-3">
                                                    <div class="form-group col-4">

                                                        <input type="checkbox" id="page_sluggable_<?php echo intval($index); ?>" class="form-check-input" title="<?php echo esc_attr($page_sluggable_hint); ?>" name="page[<?php echo intval($index); ?>][page_sluggable]" value="1">
                                                        <label class="form-check-label" for="page_sluggable_<?php echo intval($index); ?>"><?php echo wp_kses_post(__('Apply Read/Write rules', 'propeller-ecommerce-v2')); ?></label>

                                                    </div>

                                                    <div class="form-group col-4">

                                                        <input type="checkbox" id="is_my_account_page_<?php echo intval($index); ?>" class="form-check-input" title="<?php echo esc_attr($is_my_account_page_hint); ?>" name="page[<?php echo intval($index); ?>][is_my_account_page]" value="1">
                                                        <label class="form-check-label" for="is_my_account_page_<?php echo intval($index); ?>"><?php echo wp_kses_post(__('Is My account page', 'propeller-ecommerce-v2')); ?></label>

                                                    </div>

                                                    <div class="form-group col-4">

                                                        <input type="checkbox" id="account_page_is_parent_<?php echo intval($index); ?>" class="form-check-input" title="<?php echo esc_attr($account_page_is_parent_hint); ?>" name="page[<?php echo intval($index); ?>][account_page_is_parent]" value="1">
                                                        <label class="form-check-label" for="account_page_is_parent_<?php echo intval($index); ?>"><?php echo wp_kses_post(__('Child of My Account', 'propeller-ecommerce-v2')); ?></label>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-4">
                                                <label class="font-weight-medium" for="pageslug_<?php echo intval($index); ?>"><?php echo wp_kses_post(__('Slug', 'propeller-ecommerce-v2')); ?></label>
                                                <input type="text" id="pageslug_<?php echo intval($index); ?>" class="border form-control" placeholder="<?php echo wp_kses_post(__('Slug', 'propeller-ecommerce-v2')); ?>" name="page[<?php echo intval($index); ?>][page_slug]" value="" required>
                                            </div>

                                            <?php /*
                            <div class="col-md-4">
                                <div class="form-group col">
                                    <label><?php echo wp_kses_post(__('Slug(s)', 'propeller-ecommerce-v2')); ?></label>

                                    <?php 
                                        $new_slug_id = 0; 
                                        $new_page_id = 0; 
                                    ?>

                                    <div class="page-slug-containers page-slugs-container-<?php echo intval($index); ?>" data-page_id="<?php echo esc_attr($slug->page_id); ?>">
                                        <div class="row page-slug-row" data-id="<?php echo esc_attr($new_slug_id); ?>" data-page_id="<?php echo esc_attr($new_page_id); ?>">
                                            <input type="hidden" name="page[<?php echo intval($index); ?>][slugs][slug_id][<?php echo esc_attr($new_slug_id); ?>]" value="">
                                            <input type="hidden" name="page[<?php echo intval($index); ?>][slugs][slug_exists][<?php echo esc_attr($new_slug_id); ?>]" value="0">

                                            <div class="col-3">
                                                <select class="form-control page-slugs-languages" name="page[<?php echo intval($index); ?>][slugs][slug_lang][<?php echo esc_attr($new_slug_id); ?>]">
                                                <?php foreach ($slug_langs as $lng) { ?>
                                                    <option value="<?php echo esc_attr($lng); ?>"><?php echo esc_html($lng); ?></option>
                                                <?php } ?>
                                                </select> 
                                            </div>
                                            <div class="col-8">
                                                <input type="text" class="border form-control" placeholder="<?php echo wp_kses_post(__('Page slug', 'propeller-ecommerce-v2')); ?>" name="page[<?php echo intval($index); ?>][slugs][slug][<?php echo esc_attr($new_slug_id); ?>]" value="">
                                            </div>
                                            <div class="col-1">
                                                <button type="button" class="btn btn-primary propel-add-lng-btn" data-id="<?php echo esc_attr($new_slug_id); ?>" data-page_id="<?php echo esc_attr($new_page_id); ?>">+</button>
                                            </div>
                                        </div>                                        
                                    </div>                                    
                                </div>
                            </div>
                            */ ?>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="fixed-floating-wrapper pages-floating-wrapper">
                    <div class="row g-0 gap-2 w-100 justify-content-md-center">
                        <div class="col col-md-4 text-start">
                            <button type="button" id="add_page_btn" class="btn btn-success btn-white w-100 justify-content-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus h-4 w-4">
                                    <path d="M5 12h14"></path>
                                    <path d="M12 5v14"></path>
                                </svg><?php echo wp_kses_post(__('New page', 'propeller-ecommerce-v2')); ?></button>
                        </div>
                        <div class="col col-md-4">
                            <button type="submit" id="submit-key" class="btn btn-success w-100 justify-content-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-save h-4 w-4">
                                    <path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"></path>
                                    <path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7"></path>
                                    <path d="M7 3v4a1 1 0 0 0 1 1h7"></path>
                                </svg><?php echo wp_kses_post(__('Save pages', 'propeller-ecommerce-v2')); ?></button>
                        </div>
                        <div class="col-auto">
                            <button type="button" id="scroll_top" class="integration-form-btn btn btn-success btn-white btn-top">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up me-0 h-4 w-4">
                                    <path d="m5 12 7-7 7 7"></path>
                                    <path d="M12 19V5"></path>
                                </svg></button>
                        </div>
                    </div>
                </div>
            </form>

            <div id="page_row_template">
                <div class="ac propel-page-acc-row">
                    <h2 class="ac-header">
                        <button type="button" class="ac-trigger"><?php echo wp_kses_post(__('New page', 'propeller-ecommerce-v2')); ?></button>
                        <button type="button" class="delete-btn btn-close-style" data-id="0" data-name="New page" title="<?php echo wp_kses_post(__('Delete page', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </h2>
                    <div class="ac-panel">
                        <div class="propel-page-row ac-text" data-index="{index}">
                            <input type="hidden" name="page[{index}][id]" value="0">

                            <div class="row g-3">
                                <div class="col-md-11">
                                    <div class="row g-3">
                                        <div class="form-group col-md-4">
                                            <label for="pagename_{index}"><?php echo wp_kses_post(__('Page name', 'propeller-ecommerce-v2')); ?></label>
                                            <input type="text" id="pagename_{index}" class="border form-control" placeholder="<?php echo wp_kses_post(__('Page name', 'propeller-ecommerce-v2')); ?>" name="page[{index}][page_name]" value="">
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label for="pagetype_{index}"><?php echo wp_kses_post(__('Type', 'propeller-ecommerce-v2')); ?></label>
                                            <select class="border form-control" id="pagetype_{index}" name="page[{index}][page_type]">
                                                <?php foreach ($page_types::getConstants() as $const => $name) { ?>
                                                    <option value="<?php echo esc_attr($name); ?>"><?php echo esc_html($name); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label for="pageshortcode_{index}"><?php echo wp_kses_post(__('Shortcode', 'propeller-ecommerce-v2')); ?></label>
                                            <select class="border form-control" id="pageshortcode_{index}" name="page[{index}][page_shortcode]">
                                                <?php foreach (Propeller::$fe_shortcodes as $shortcode => $method) { ?>
                                                    <option value="<?php echo esc_attr($shortcode); ?>"><?php echo esc_html($shortcode); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="form-group col-4">

                                            <input type="checkbox" id="page_sluggable_{index}" class="form-check-input" title="<?php echo esc_attr($page_sluggable_hint); ?>" name="page[{index}][page_sluggable]" value="1">
                                            <label class="form-check-label" for="page_sluggable_{index}"><?php echo wp_kses_post(__('Apply Read/Write rules', 'propeller-ecommerce-v2')); ?></label>

                                        </div>

                                        <div class="form-group col-4">

                                            <input type="checkbox" id="is_my_account_page_{index}" class="form-check-input" title="<?php echo esc_attr($is_my_account_page_hint); ?>" name="page[{index}][is_my_account_page]" value="1">
                                            <label class="form-check-label" for="is_my_account_page_{index}"><?php echo wp_kses_post(__('Is My account page', 'propeller-ecommerce-v2')); ?></label>

                                        </div>

                                        <div class="form-group col-4">

                                            <input type="checkbox" id="account_page_is_parent_{index}" class="form-check-input" title="<?php echo esc_attr($account_page_is_parent_hint); ?>" name="page[{index}][account_page_is_parent]" value="1">
                                            <label class="form-check-label" for="account_page_is_parent_{index}"><?php echo wp_kses_post(__('Child of My Account', 'propeller-ecommerce-v2')); ?></label>

                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="pageslug_{index}"><?php echo wp_kses_post(__('Slug', 'propeller-ecommerce-v2')); ?></label>
                                    <input type="text" id="pageslug_{index}" class="border form-control" placeholder="<?php echo wp_kses_post(__('Slug', 'propeller-ecommerce-v2')); ?>" name="page[{index}][page_slug]" value="" required>
                                </div>


                                <?php /*
                    <div class="col-md-4">
                        <div class="form-group col">
                            <label><?php echo wp_kses_post(__('Slug(s)', 'propeller-ecommerce-v2')); ?></label>

                            <?php 
                                $new_slug_id = 0; 
                                $new_page_id = 0; 
                            ?>

                            <div class="page-slug-containers page-slugs-container-{index}" data-page_id="<?php echo esc_attr($slug->page_id); ?>">
                                <div class="row page-slug-row" data-id="<?php echo esc_attr($new_slug_id); ?>" data-page_id="<?php echo esc_attr($new_page_id); ?>">
                                    <input type="hidden" name="page[{index}][slugs][slug_id][<?php echo esc_attr($new_slug_id); ?>]" value="">
                                    <input type="hidden" name="page[{index}][slugs][slug_exists][<?php echo esc_attr($new_slug_id); ?>]" value="0">

                                    <div class="col-3">
                                        <select class="form-control page-slugs-languages" name="page[{index}][slugs][slug_lang][<?php echo esc_attr($new_slug_id); ?>]">
                                        <?php foreach ($slug_langs as $lng) { ?>
                                            <option value="<?php echo esc_attr($lng); ?>"><?php echo esc_html($lng); ?></option>
                                        <?php } ?>
                                        </select> 
                                    </div>
                                    <div class="col-8">
                                        <input type="text" class="border form-control" placeholder="<?php echo wp_kses_post(__('Page slug', 'propeller-ecommerce-v2')); ?>" name="page[{index}][slugs][slug][<?php echo esc_attr($new_slug_id); ?>]" value="">
                                    </div>
                                    <div class="col-1">
                                        <button type="button" class="btn btn-primary propel-add-lng-btn" data-id="<?php echo esc_attr($new_slug_id); ?>" data-page_id="<?php echo esc_attr($new_page_id); ?>">+</button>
                                    </div>
                                </div>                                        
                            </div>                                    
                        </div>
                    </div>
                    */ ?>

                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div id="slug_row_template">
                <div class="row page-slug-row" data-id="{slug-id}" data-page_id="{page-id}">
                    <input type="hidden" name="page[{index}][slugs][slug_id][{slug-id}]" value="">
                    <input type="hidden" name="page[{index}][slugs][slug_exists][{slug-id}]" value="0">

                    <div class="col-3">
                        <select class="form-control page-slugs-languages" name="page[{index}][slugs][slug_lang][{slug-id}]">
                            <?php foreach ($slug_langs as $lng) { ?>
                                <option value="<?php echo esc_attr($lng); ?>"><?php echo esc_html($lng); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-8">
                        <input type="text" class="border form-control" placeholder="<?php echo wp_kses_post(__('Page slug', 'propeller-ecommerce-v2')); ?>" name="page[{index}][slugs][slug][{slug-id}]" value="">
                    </div>
                    <div class="col-1">
                        <button type="button" class="btn btn-primary propel-add-lng-btn" data-id="{slug-id}" data-page_id="{page-id}">+</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Page Confirmation Modal -->
<div class="modal fade propel-modal modal-fullscreen-sm-down" id="deletePageModal" tabindex="-1" role="dialog" aria-labelledby="deletePageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="deletePageModalLabel"><?php echo wp_kses_post(__('Delete page?', 'propeller-ecommerce-v2')); ?></h5>
            </div>
            <div class="modal-body">
                <p id="deletePageMessage"><?php echo wp_kses_post(__('Are you sure you want to delete this page? This action cannot be undone.', 'propeller-ecommerce-v2')); ?></p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-success btn-white" data-bs-dismiss="modal"><?php echo wp_kses_post(__('Cancel', 'propeller-ecommerce-v2')); ?></button>
                <button type="button" class="btn btn-success btn-danger" id="confirmDeletePage"><?php echo wp_kses_post(__('Delete', 'propeller-ecommerce-v2')); ?></button>
            </div>
        </div>
    </div>
</div>