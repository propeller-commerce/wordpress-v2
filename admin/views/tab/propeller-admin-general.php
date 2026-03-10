<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php

use Propeller\Includes\Enum\Currency;

?>
<div class="container-fluid propel-admin-panel">
    <div class="row propeller-admin-title mb-4">
        <div class="col-12 col-md-6 col-lg-6">
            <h1 class="mb-2 font-weight-bold">
                <?php echo wp_kses_post(__('Propeller Settings', 'propeller-ecommerce-v2')); ?>
            </h1>
            <small class="d-block text-secondary">
                <?php echo wp_kses_post(__('Configure API connections and general settings for Propeller Commerce', 'propeller-ecommerce-v2')); ?>
            </small>
        </div>
        <div class="col-12 col-md-4 col-lg-3 d-flex justify-content-md-end align-items-start mt-3 mt-md-0">
            <form method="POST" class="propel-admin-form ps-3" action="#" id="propeller_cache_form">
                <input type="hidden" name="action" value="propel_destroy_caches">
                <button type="submit" id="submit-key" class="integration-form-btn btn btn-success d-flex align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-refresh-cw h-4 w-4">
                        <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"></path>
                        <path d="M21 3v5h-5"></path>
                        <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"></path>
                        <path d="M8 16H3v5"></path>
                    </svg>
                    <?php echo wp_kses_post(__('Clear cache', 'propeller-ecommerce-v2')); ?>
                </button>
            </form>
            <form method="POST" class="propel-admin-form ps-3" action="#" id="propeller_rw_rules_form">
                <input type="hidden" name="action" value="propel_flush_rw_rules">
                <button type="submit" id="submit-key" class="integration-form-btn btn btn-success d-flex align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-repeat h-4 w-4">
                        <path d="m17 2 4 4-4 4"></path>
                        <path d="M3 11v-1a4 4 0 0 1 4-4h14"></path>
                        <path d="m7 22-4-4 4-4"></path>
                        <path d="M21 13v1a4 4 0 0 1-4 4H3"></path>
                    </svg>
                    <?php echo wp_kses_post(__('Flush rewrite rules', 'propeller-ecommerce-v2')); ?>
                </button>
            </form>

        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md-10 col-lg-9">
            <form method="POST" class="propel-admin-form propeller-general-form border rounded-lg" action="#" id="propel_settings_form">
                <input type="hidden" id="setting_id" name="setting_id" value="<?php echo isset($settings_result->id) ? esc_attr($settings_result->id) : 0; ?>">
                <input type="hidden" name="action" value="save_propel_settings">

                <div class="form-group pb-0 pt-0">
                    <label class="font-weight-medium" for="api_url"><?php echo wp_kses_post(__('API URL', 'propeller-ecommerce-v2')); ?></label>
                    <input type="text" class="border form-control" id="api_url" name="api_url" value="<?php echo isset($settings_result->api_url) ? esc_url($settings_result->api_url) : ''; ?>" required>
                </div>
                <div class="form-group pb-0">
                    <label class="font-weight-medium" for="api_key"><?php echo wp_kses_post(__('API key', 'propeller-ecommerce-v2')); ?></label>
                    <div class="d-flex gap-2">
                        <input type="password" class="border form-control" id="api_key" name="api_key" value="<?php echo isset($settings_result->api_key) && !empty($settings_result->api_key) ? esc_attr(str_repeat('•', 32)) : ''; ?>" data-original-value="<?php echo isset($settings_result->api_key) ? esc_attr($settings_result->api_key) : ''; ?>" required>
                        <button type="button" class="btn btn-border-keys toggle-password" data-target="#api_key" title="Show/Hide API key">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                        <button type="button" class="btn btn-border-keys copy-to-clipboard" data-target="#api_key" title="Copy API key">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="14" height="14" x="8" y="8" rx="2" ry="2"></rect>
                                <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label class="font-weight-medium" for="order_api_key"><?php echo wp_kses_post(__('Orders API key', 'propeller-ecommerce-v2')); ?></label>
                    <div class="d-flex gap-2">
                        <input type="password" class="border form-control" id="order_api_key" name="order_api_key" value="<?php echo isset($settings_result->order_api_key) && !empty($settings_result->order_api_key) ? esc_attr(str_repeat('•', 32)) : ''; ?>" data-original-value="<?php echo isset($settings_result->order_api_key) ? esc_attr($settings_result->order_api_key) : ''; ?>" required>
                        <button type="button" class="btn btn-border-keys toggle-password" data-target="#order_api_key" title="Show/Hide Orders API key">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                        <button type="button" class="btn btn-border-keys copy-to-clipboard" data-target="#order_api_key" title="Copy Orders API key">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="14" height="14" x="8" y="8" rx="2" ry="2"></rect>
                                <path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="form-group col-md-4">
                        <label class="font-weight-medium" for="anonymous_user"><?php echo wp_kses_post(__('Anonymous user ID', 'propeller-ecommerce-v2')); ?></label>
                        <input type="text" class="border form-control" id="anonymous_user" name="anonymous_user" value="<?php echo isset($settings_result->anonymous_user) ? esc_attr($settings_result->anonymous_user) : ''; ?>" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="font-weight-medium" for="catalog_root"><?php echo wp_kses_post(__('Catalog root ID', 'propeller-ecommerce-v2')); ?></label>
                        <input type="text" class="border form-control" id="catalog_root" name="catalog_root" value="<?php echo isset($settings_result->catalog_root) ? esc_attr($settings_result->catalog_root) : ''; ?>" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="font-weight-medium" for="site_id"><?php echo wp_kses_post(__('Channel ID', 'propeller-ecommerce-v2')); ?></label>
                        <input type="text" class="border form-control" id="site_id" name="site_id" value="<?php echo isset($settings_result->site_id) ? esc_attr($settings_result->site_id) : ''; ?>" required>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="form-group col-md-4">
                        <label class="font-weight-medium" for="default_locale"><?php echo wp_kses_post(__('Default language', 'propeller-ecommerce-v2')); ?></label>

                        <select name="default_locale" id="default_locale" class="form-control">
                            <option value=""><?php echo wp_kses_post(__('Select language', 'propeller-ecommerce-v2')); ?></option>
                            <?php $locales = include PROPELLER_PLUGIN_DIR . '/includes/Locales.php'; ?>
                            <?php foreach ($locales as $loc => $locale) { ?>
                                <option value="<?php echo esc_attr($locale['wp_locale']); ?>" <?php echo (bool) ($settings_result->default_locale == $locale['wp_locale']) ? 'selected="selected"' : ''; ?>><?php echo esc_html($locale['name']) . ' (' . esc_html($locale['code']) . ')'; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="form-group col-md-6">
                        <label class="font-weight-medium" for="cc_email"><?php echo wp_kses_post(__('CC Email', 'propeller-ecommerce-v2')); ?></label>
                        <input type="text" class="border form-control" id="cc_email" name="cc_email" value="<?php echo isset($settings_result->cc_email) ? esc_attr($settings_result->cc_email) : ''; ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="font-weight-medium" for="bcc_email"><?php echo wp_kses_post(__('BCC Email', 'propeller-ecommerce-v2')); ?></label>
                        <input type="text" class="border form-control" id="bcc_email" name="bcc_email" value="<?php echo isset($settings_result->bcc_email) ? esc_attr($settings_result->bcc_email) : ''; ?>">
                    </div>
                </div>

                <div class="row g-3">
                    <div class="form-group col-md-4">
                        <label class="font-weight-medium" for="currency"><?php echo wp_kses_post(__('Default currency', 'propeller-ecommerce-v2')); ?></label>

                        <select name="currency" id="currency" class="form-control">
                            <?php foreach (Currency::$currencies as $val => $sign) { ?>
                                <option value="<?php echo esc_attr($val); ?>" <?php echo (bool) (trim($settings_result->currency) == $val) ? 'selected="selected"' : ''; ?>><?php echo esc_html($sign); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col d-flex justify-content-end">
                        <button type="submit" id="submit-key" class="integration-form-btn d-flex align-items-center btn btn-success">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-save h-4 w-4">
                                <path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"></path>
                                <path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7"></path>
                                <path d="M7 3v4a1 1 0 0 0 1 1h7"></path>
                            </svg>
                            <?php echo wp_kses_post('Save settings'); ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>