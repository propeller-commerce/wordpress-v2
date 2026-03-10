<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<?php

use Propeller\Includes\Enum\PdpNewWindow;
use Propeller\Includes\Enum\SsoProviders;

$ref = 'Propeller\Custom\Includes\Enum\SsoProviders';

$sso_providers = class_exists($ref, true)
    ? $ref
    : SsoProviders::class;
?>
<div class="container-fluid propel-admin-panel">
    <div class="row propeller-admin-title mb-4">
        <div class="col-12 col-md-6">
            <h1 class="mb-2 font-weight-bold">
                <?php echo wp_kses_post(__('Behavior Settings', 'propeller-ecommerce-v2')); ?>
            </h1>
            <small class="d-block text-secondary">
                <?php echo wp_kses_post(__('Configure how users interact with your webshop and define behavioral rules', 'propeller-ecommerce-v2')); ?>
            </small>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md-10 col-lg-9">
            <form method="POST" class="propel-admin-form propeller-general-form border rounded-lg" action="#" id="propel_behavior_form">
                <input type="hidden" id="setting_id" name="setting_id" value="<?php echo isset($behavior_result->id) ? esc_attr($behavior_result->id) : 0; ?>">
                <input type="hidden" name="action" value="save_propel_behavior">
                <h2 class="propel-subtitle pt-0 font-weight-semibold">
                    <?php echo wp_kses_post(__('Basic Configuration', 'propeller-ecommerce-v2')); ?>
                </h2>
                <div class="row g-3">
                    <div class="form-group col-md-6">
                        <label class="font-weight-medium" for="icp_country">
                            <?php echo wp_kses_post(__('Shop home country', 'propeller-ecommerce-v2')); ?>

                        </label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('The home country of your webshop that drives ICP settings, default tax zones, and regional configurations', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                        <select name="icp_country" id="icp_country" class="form-control">
                            <option value=""><?php echo wp_kses_post(__('Select country', 'propeller-ecommerce-v2')); ?></option>
                            <?php $countries = propel_get_countries(); ?>
                            <?php foreach ($countries as $abbr => $name) { ?>
                                <option value="<?php echo esc_attr($abbr); ?>" <?php echo ($behavior_result->icp_country == $abbr) ? 'selected="selected"' : ''; ?>><?php echo esc_html($name) . ' (' . esc_html($abbr) . ')'; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <h2 class="propel-subtitle font-weight-semibold">
                    <?php echo wp_kses_post(__('Access & Security', 'propeller-ecommerce-v2')); ?>
                </h2>
                <div class="row g-3">
                    <div class="form-group col-12 pb-0">
                        <input type="checkbox" class="border form-control" id="closed_portal" name="closed_portal" value="true" <?php echo isset($behavior_result->closed_portal) && $behavior_result->closed_portal == 1 ? 'checked' : ''; ?>>
                        <label class="font-weight-medium" for="closed_portal">
                            <?php echo wp_kses_post(__('Closed portal mode', 'propeller-ecommerce-v2')); ?>

                        </label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('If checked, anonymous users will land on a login page', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                    </div>


                    <div id="exclusions_container" class="form-group py-0 col-12" style="display: <?php echo intval($behavior_result->closed_portal) == 1 ? 'block' : 'none'; ?>">
                        <div class="row g-3">
                            <div class="form-group col-12">
                                <label class="font-weight-medium">
                                    <?php echo wp_kses_post(__('Select pages for exclusion', 'propeller-ecommerce-v2')); ?>

                                </label>
                                <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Selected pages will be available even if the user is not logged in a closed webshop', 'propeller-ecommerce-v2')); ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                        <path d="M12 17h.01"></path>
                                    </svg>
                                </span>
                                <input type="hidden" id="excluded_pages" name="excluded_pages" value="<?php echo esc_attr($behavior_result->excluded_pages); ?>">
                                <select multiple name="exclusions" id="exclusions" size="10" class="border form-control">
                                    <?php
                                    $exclusions = explode(',', $behavior_result->excluded_pages);

                                    if ($pages = get_pages([])) {
                                        foreach ($pages as $page) {
                                    ?>
                                            <option value="<?php echo esc_attr($page->ID); ?>" <?php echo (in_array($page->ID, $exclusions) ? 'selected' : ''); ?>><?php echo esc_html($page->post_title); ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="form-group col-12 pb-0">
                        <input type="checkbox" class="border form-control" id="semiclosed_portal" name="semiclosed_portal" value="true" <?php echo isset($behavior_result->semiclosed_portal) && $behavior_result->semiclosed_portal == 1 ? 'checked' : ''; ?>>
                        <label class="font-weight-medium" for="semiclosed_portal">
                            <?php echo wp_kses_post(__('Semi-closed portal mode', 'propeller-ecommerce-v2')); ?>

                        </label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('If checked, anonymous users will not be able to add products to cart, see prices or stock, etc.', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="form-group col-12 pb-0">
                        <input type="checkbox" class="border form-control" id="anonymous_orders" name="anonymous_orders" value="true" <?php echo isset($behavior_result->anonymous_orders) && intval($behavior_result->anonymous_orders) == 1 ? 'checked' : ''; ?>>
                        <label class="font-weight-medium" for="anonymous_orders">
                            <?php echo wp_kses_post(__('Allow guest checkout', 'propeller-ecommerce-v2')); ?>

                        </label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Allow guest users to complete the checkout process and create orders', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="form-group col-12 pb-0">
                        <input type="checkbox" class="border form-control" id="use_sso" name="use_sso" value="true" <?php echo isset($behavior_result->use_sso) && $behavior_result->use_sso == 1 ? 'checked' : ''; ?>>
                        <label class="font-weight-medium" for="use_sso">
                            <?php echo wp_kses_post(__('Enable SSO', 'propeller-ecommerce-v2')); ?>

                        </label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('If checked, anonymous users will land on a sign in page for Single Sign On', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                    </div>
                </div>

                <div id="sso_container" class="row g-3" style="display: <?php echo intval($behavior_result->use_sso) == 1 ? 'flex' : 'none'; ?>">
                    <div class="form-group col-md-12">
                        <label class="font-weight-medium" for="sso_provider"><?php echo wp_kses_post(__('SSO provider', 'propeller-ecommerce-v2')); ?>: Firebase</label>
                        <label class="d-block text-secondary"><?php echo wp_kses_post(__('Firebase configuration data', 'propeller-ecommerce-v2')); ?></label>
                    </div>
                    <div id="sso_config" class="form-group col-md-8 justify-content-center align-self-center">
                        <?php
                        include_once PROPELLER_PLUGIN_DIR . '/admin/views/tab/sso/propeller-firebase.php';
                        ?>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="form-group col-12 pb-0">
                        <input type="checkbox" class="border form-control" id="use_recaptcha" name="use_recaptcha" value="true" <?php echo isset($behavior_result->use_recaptcha) && intval($behavior_result->use_recaptcha) == 1 ? 'checked' : ''; ?>>
                        <label class="font-weight-medium" for="use_recaptcha">
                            <?php echo wp_kses_post(__('Enable reCAPTCHA', 'propeller-ecommerce-v2')); ?>

                        </label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Use Google reCaptcha v3 in login and registration forms', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                    </div>
                    <div id="recaptcha_settings" class="form-group col" style="display: <?php echo intval($behavior_result->use_recaptcha) == 1 ? 'block' : 'none'; ?>">
                        <div class="row g-3">
                            <div class="form-group col-md-4">
                                <label class="font-weight-medium" for="recaptcha_site_key"><?php echo wp_kses_post(__('Site key:', 'propeller-ecommerce-v2')); ?></label>
                                <input type="text" class="border form-control" id="recaptcha_site_key" placeholder="reCaptcha Site key" name="recaptcha_site_key" value="<?php echo isset($behavior_result->recaptcha_site_key) ? esc_attr($behavior_result->recaptcha_site_key) : ''; ?>">
                            </div>

                            <div class="form-group col-md-4">
                                <label class="font-weight-medium" for="recaptcha_secret_key"><?php echo wp_kses_post(__('Secret key:', 'propeller-ecommerce-v2')); ?></label>
                                <input type="text" class="border form-control" id="recaptcha_secret_key" placeholder="reCaptcha secret key" name="recaptcha_secret_key" value="<?php echo isset($behavior_result->recaptcha_secret_key) ? esc_attr($behavior_result->recaptcha_secret_key) : ''; ?>">
                            </div>

                            <div class="form-group col-md-4">
                                <label class="font-weight-medium" for="recaptcha_min_score"><?php echo wp_kses_post(__('Minimal valid score:', 'propeller-ecommerce-v2')); ?></label>
                                <input type="text" class="border form-control" id="recaptcha_min_score" placeholder="reCaptcha minimal valid score" name="recaptcha_min_score" value="<?php echo isset($behavior_result->recaptcha_min_score) ? esc_attr($behavior_result->recaptcha_min_score) : ''; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="form-group col-md-12">
                        <input type="checkbox" class="border form-control" id="use_ga4" name="use_ga4" value="true" <?php echo isset($behavior_result->use_ga4) && $behavior_result->use_ga4 == 1 ? 'checked' : ''; ?>>
                        <label class="font-weight-medium" for="use_ga4"><?php echo wp_kses_post(__('Enable Google Analytics 4 & Tag Manager', 'propeller-ecommerce-v2')); ?></label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('If checked, Google Analytics 4 & Google Tag Manager will be measuring experiences in your webshop', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                    </div>
                </div>

                <div id="ga4_container" class="row g-3" style="display: <?php echo intval($behavior_result->use_ga4) == 1 ? 'flex' : 'none'; ?>">
                    <div class="form-group col-md-2">
                        <input type="checkbox" class="border form-control" id="ga4_tracking" name="ga4_tracking" value="true" <?php echo isset($behavior_result->ga4_tracking) && $behavior_result->ga4_tracking == 1 ? 'checked' : ''; ?>>
                        <label class="font-weight-medium" for="ga4_tracking"><?php echo wp_kses_post(__('Enable tracking', 'propeller-ecommerce-v2')); ?></label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Enable or Disable Google Analytics 4 & Google Tag Manager tracking', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                    </div>
                    <div class="form-group col-md-5">
                        <label for="ga4_key" class="font-weight-medium"><?php echo wp_kses_post(__('Google Analytics 4 Measurement ID', 'propeller-ecommerce-v2')); ?></label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Add your Google Analytics 4 Measurement ID', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                        <input type="text" class="border form-control" id="ga4_key" name="ga4_key" value="<?php echo esc_attr($behavior_result->ga4_key); ?>" placeholder="Your Google Analytics 4 Measurement ID">

                    </div>
                    <div class="form-group col-md-5">
                        <label for="gtm_key" class="font-weight-medium"><?php echo wp_kses_post(__('Google Tag Manager Container ID', 'propeller-ecommerce-v2')); ?></label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Add your Google Tag Manager Container ID', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                        <input type="text" class="border form-control" id="gtm_key" name="gtm_key" value="<?php echo esc_attr($behavior_result->gtm_key); ?>" placeholder="Your Google Tag Manager Container ID">
                    </div>
                </div>

                <div class="row g-3">
                    <div class="form-group col-md-12">
                        <input type="checkbox" class="border form-control" id="use_cxml" name="use_cxml" value="true" <?php echo isset($behavior_result->use_cxml) && $behavior_result->use_cxml == 1 ? 'checked' : ''; ?>>
                        <label class="font-weight-medium" for="use_cxml"><?php echo wp_kses_post(__('Enable cXML Punchout', 'propeller-ecommerce-v2')); ?></label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('If checked, cXML Punchout ordering process will be enabled', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                    </div>
                </div>

                <div id="cxml_container" class="row g-3" style="display: <?php echo intval($behavior_result->use_cxml) == 1 ? 'flex' : 'none'; ?>">
                    <div class="form-group col-md-5">
                        <label for="cxml_contact_id" class="font-weight-medium"><?php echo wp_kses_post(__('cXML Contact IDs', 'propeller-ecommerce-v2')); ?></label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Contact IDs that will be used for the CXML punchout process', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                        <input type="text" class="border form-control" id="cxml_contact_id" name="cxml_contact_id" value="<?php echo esc_attr($behavior_result->cxml_contact_id); ?>" placeholder="CXML Contact IDs" data-use-bootstrap-tag>
                    </div>
                </div>

                <h2 class="propel-subtitle font-weight-semibold">
                    <?php echo wp_kses_post(__('Checkout & Cart', 'propeller-ecommerce-v2')); ?>
                </h2>
                <div class="row g-3">
                    <div class="form-group pb-0 col-md-6">
                        <label class="font-weight-medium" for="onacc_payments">
                            <?php echo wp_kses_post(__('On account payment types:', 'propeller-ecommerce-v2')); ?>

                        </label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('On-account payment types for which 3rd party payment providers will be skipped', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                        <input type="text" class="border form-control" id="onacc_payments" placeholder="<?php echo wp_kses_post(__('Comma separated payment types', 'propeller-ecommerce-v2')); ?>" name="onacc_payments" value="<?php echo isset($behavior_result->onacc_payments) ? esc_attr($behavior_result->onacc_payments) : ''; ?>">
                    </div>
                </div>
                <div class="row g-3">
                    <div class="form-group pb-0 col-12">
                        <input type="checkbox" class="border form-control" id="partial_delivery" name="partial_delivery" value="true" <?php echo isset($behavior_result->partial_delivery) && intval($behavior_result->partial_delivery) == 1 ? 'checked' : ''; ?>>
                        <label class="font-weight-medium" for="partial_delivery">
                            <?php echo wp_kses_post(__('Enable partial delivery', 'propeller-ecommerce-v2')); ?>

                        </label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Will add the option to use partial delivery in the checkout process', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="form-group pb-0 col-12">
                        <input type="checkbox" class="border form-control" id="selectable_carriers" name="selectable_carriers" value="true" <?php echo isset($behavior_result->selectable_carriers) && intval($behavior_result->selectable_carriers) == 1 ? 'checked' : ''; ?>>
                        <label class="font-weight-medium" for="selectable_carriers">
                            <?php echo wp_kses_post(__('Enable carrier selection', 'propeller-ecommerce-v2')); ?>
                        </label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Add the option to select a carrier in the checkout process', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="form-group pb-0 col-12">
                        <input type="checkbox" class="border form-control" id="use_datepicker" name="use_datepicker" value="true" <?php echo isset($behavior_result->use_datepicker) && intval($behavior_result->use_datepicker) == 1 ? 'checked' : ''; ?>>
                        <label class="font-weight-medium" for="use_datepicker">
                            <?php echo wp_kses_post(__('Enable delivery date picker', 'propeller-ecommerce-v2')); ?>

                        </label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Show datepicker in the checkout process for chosing a desired delivery date', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="form-group pb-0 col-12">
                        <input type="checkbox" class="border form-control" id="show_actioncode" name="show_actioncode" value="true" <?php echo isset($behavior_result->show_actioncode) && intval($behavior_result->show_actioncode) == 1 ? 'checked' : ''; ?>>
                        <label class="font-weight-medium" for="show_actioncode">
                            <?php echo wp_kses_post(__('Show action code field', 'propeller-ecommerce-v2')); ?>

                        </label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Show action code box in cart', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="form-group pb-0 col-12">
                        <input type="checkbox" class="border form-control" id="show_order_type" name="show_order_type" value="true" <?php echo isset($behavior_result->show_order_type) && intval($behavior_result->show_order_type) == 1 ? 'checked' : ''; ?>>
                        <label class="font-weight-medium" for="show_order_type">
                            <?php echo wp_kses_post(__('Show order type field', 'propeller-ecommerce-v2')); ?>

                        </label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Show order type box in cart', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                    </div>
                </div>


                <h2 class="propel-subtitle font-weight-semibold">
                    <?php echo wp_kses_post(__('Product Catalog & Display', 'propeller-ecommerce-v2')); ?>
                </h2>
                <?php
                $sort_columns = [
                    "SKU" => __('Product code', 'propeller-ecommerce-v2'),
                    "SUPPLIER_CODE" => __('Supplier code', 'propeller-ecommerce-v2'),
                    "CREATED_AT" => __('Created', 'propeller-ecommerce-v2'),
                    "LAST_MODIFIED_AT" => __('Modified', 'propeller-ecommerce-v2'),
                    "NAME" => __('Name', 'propeller-ecommerce-v2'),
                    "SHORT_NAME" => __('Short name', 'propeller-ecommerce-v2'),
                    "PRICE" => __('Price', 'propeller-ecommerce-v2'),
                    "RELEVANCE" => __('Relevance', 'propeller-ecommerce-v2'),
                    "CATEGORY_ORDER" => __('Default sorting', 'propeller-ecommerce-v2'),
                    "PRIORITY" => __('Priority', 'propeller-ecommerce-v2'),
                ];

                $sort_order = [
                    "ASC" => __('Asc', 'propeller-ecommerce-v2'),
                    "DESC" => __('Desc', 'propeller-ecommerce-v2'),
                ];

                $offsets = [
                    12,
                    24,
                    48
                ];
                ?>

                <div class="row g-3">
                    <div class="form-group col-12 pb-0">
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Configure default sorting behavior and pagination for category, search, and brand listing pages', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="font-weight-medium" for="default_sort_column"><?php echo wp_kses_post(__('Default sort field', 'propeller-ecommerce-v2')); ?></label>
                        <select name="default_sort_column" id="default_sort_column" class="border form-control">
                            <?php foreach ($sort_columns as $field => $description) { ?>
                                <option value="<?php echo esc_attr($field); ?>" <?php echo $behavior_result->default_sort_column == $field ? 'selected' : ''; ?>><?php echo esc_html($description); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="font-weight-medium" for="secondary_sort_column"><?php echo wp_kses_post(__('Secondary sort field', 'propeller-ecommerce-v2')); ?></label>
                        <select name="secondary_sort_column" id="secondary_sort_column" class="border form-control">
                            <?php foreach ($sort_columns as $field => $description) { ?>
                                <option value="<?php echo esc_attr($field); ?>" <?php echo $behavior_result->secondary_sort_column == $field ? 'selected' : ''; ?>><?php echo esc_html($description); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="font-weight-medium" for="default_sort_direction"><?php echo wp_kses_post(__('Direction', 'propeller-ecommerce-v2')); ?></label>
                        <select name="default_sort_direction" id="default_sort_direction" class="border form-control">
                            <?php foreach ($sort_order as $field => $description) { ?>
                                <option value="<?php echo esc_attr($field); ?>" <?php echo $behavior_result->default_sort_direction == $field ? 'selected' : ''; ?>><?php echo esc_html($description); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="font-weight-medium" for="default_offset"><?php echo wp_kses_post(__('Per page', 'propeller-ecommerce-v2')); ?></label>
                        <select name="default_offset" id="default_offset" class="border form-control">
                            <?php foreach ($offsets as $offset) { ?>
                                <option value="<?php echo esc_attr($offset); ?>" <?php echo $behavior_result->default_offset == $offset ? 'selected' : ''; ?>><?php echo esc_html($offset); ?></option>
                            <?php } ?>
                        </select>
                    </div>

                </div>
                <div class="row g-3">
                    <div class="form-group col-12 pb-0">
                        <input type="checkbox" class="border form-control" id="load_specifications" name="load_specifications" value="true" <?php echo isset($behavior_result->load_specifications) && intval($behavior_result->load_specifications) == 1 ? 'checked' : ''; ?>>
                        <label class="font-weight-medium" for="load_specifications">
                            <?php echo wp_kses_post(__('Auto-load product specifications', 'propeller-ecommerce-v2')); ?>

                        </label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Automatically load product specifications in the background on product detail pages', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="form-group col-12">
                        <input type="checkbox" class="border form-control" id="stock_check" name="stock_check" value="true" <?php echo isset($behavior_result->stock_check) && intval($behavior_result->stock_check) == 1 ? 'checked' : ''; ?>>
                        <label class="font-weight-medium" for="stock_check">
                            <?php echo wp_kses_post(__('Enable stock validation', 'propeller-ecommerce-v2')); ?>

                        </label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Display a warning popup when ordered quantity exceeds available stock', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="form-group col-12 pb-0">
                        <label class="font-weight-medium">
                            <?php echo wp_kses_post(__('Product page tab behavior', 'propeller-ecommerce-v2')); ?>

                        </label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Control whether customers can toggle product detail pages to open in new tabs while browsing catalog pages', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="form-group col-12 pb-0">
                        <div class="row">
                            <div class="col-12">
                                <input type="radio" class="border form-control" id="pdp_new_window_<?php echo esc_attr(PdpNewWindow::DEFAULT_ON); ?>" name="pdp_new_window" value="<?php echo esc_attr(PdpNewWindow::DEFAULT_ON); ?>" <?php echo intval($behavior_result->pdp_new_window) == PdpNewWindow::DEFAULT_ON ? 'checked' : ''; ?>>
                                <label class="font-weight-normal" for="pdp_new_window_<?php echo esc_attr(PdpNewWindow::DEFAULT_ON); ?>"><?php echo wp_kses_post(__('On (PDP pages will open in new tab)', 'propeller-ecommerce-v2')); ?></label>
                            </div>
                            <div class="col-12">
                                <input type="radio" class="border form-control" id="pdp_new_window_<?php echo esc_attr(PdpNewWindow::DEFAULT_OFF); ?>" name="pdp_new_window" value="<?php echo esc_attr(PdpNewWindow::DEFAULT_OFF); ?>" <?php echo intval($behavior_result->pdp_new_window) == PdpNewWindow::DEFAULT_OFF ? 'checked' : ''; ?>>
                                <label class="font-weight-normal" for="pdp_new_window_<?php echo esc_attr(PdpNewWindow::DEFAULT_OFF); ?>"><?php echo wp_kses_post(__('Off (PDP pages will open regular)', 'propeller-ecommerce-v2')); ?></label>
                            </div>
                            <div class="col-12">
                                <input type="radio" class="border form-control" id="pdp_new_window_<?php echo esc_attr(PdpNewWindow::HIDDEN); ?>" name="pdp_new_window" value="<?php echo esc_attr(PdpNewWindow::HIDDEN); ?>" <?php echo intval($behavior_result->pdp_new_window) == PdpNewWindow::HIDDEN ? 'checked' : ''; ?>>
                                <label class="font-weight-normal" for="pdp_new_window_<?php echo esc_attr(PdpNewWindow::HIDDEN); ?>"><?php echo wp_kses_post(__('Hidden (PDP pages option will remain off and hidden)', 'propeller-ecommerce-v2')); ?></label>
                            </div>
                        </div>
                    </div>
                </div>


                <h2 class="propel-subtitle font-weight-semibold">
                    <?php echo wp_kses_post(__('Pricing & Tax', 'propeller-ecommerce-v2')); ?>
                </h2>
                <div class="row g-3">
                    <div class="form-group col-12 pb-0">
                        <input type="checkbox" class="border form-control" id="default_incl_vat" name="default_incl_vat" value="true" <?php echo isset($behavior_result->default_incl_vat) && $behavior_result->default_incl_vat == 1 ? 'checked' : ''; ?>>
                        <label class="font-weight-medium" for="default_incl_vat">
                            <?php echo wp_kses_post(__('Include VAT by default', 'propeller-ecommerce-v2')); ?>

                        </label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Display prices including VAT throughout the webshop with the VAT switcher enabled by default', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                    </div>
                </div>


                <h2 class="propel-subtitle font-weight-semibold">
                    <?php echo wp_kses_post(__('User Accounts', 'propeller-ecommerce-v2')); ?>
                </h2>
                <div class="row g-3">
                    <div class="form-group col-12 pb-0">
                        <input type="checkbox" class="border form-control" id="register_auto_login" name="register_auto_login" value="true" <?php echo isset($behavior_result->register_auto_login) && intval($behavior_result->register_auto_login) == 1 ? 'checked' : ''; ?>>
                        <label class="font-weight-medium" for="register_auto_login">
                            <?php echo wp_kses_post(__('Automatic login after registration', 'propeller-ecommerce-v2')); ?>

                        </label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Automatically sign in users immediately after they complete registration', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="form-group col-12 pb-0">
                        <input type="checkbox" class="border form-control" id="edit_addresses" name="edit_addresses" value="true" <?php echo isset($behavior_result->edit_addresses) && intval($behavior_result->edit_addresses) == 1 ? 'checked' : ''; ?>>
                        <label class="font-weight-medium" for="edit_addresses">
                            <?php echo wp_kses_post(__('Allow address management', 'propeller-ecommerce-v2')); ?>

                        </label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Enable users to add new addresses and edit existing ones in their account', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="form-group col-md-6 pb-0">
                        <input type="checkbox" class="border form-control" id="pac_add_contacts" name="pac_add_contacts" value="true" <?php echo isset($behavior_result->pac_add_contacts) && intval($behavior_result->pac_add_contacts) == 1 ? 'checked' : ''; ?>>
                        <label class="font-weight-medium" for="pac_add_contacts">
                            <?php echo wp_kses_post(__('Control whether customers can register new accounts and remove existing ones', 'propeller-ecommerce-v2')); ?>

                        </label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Enable customers with authorization manager role to create and delete accounts for their company', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                    </div>
                </div>


                <h2 class="propel-subtitle font-weight-semibold">
                    <?php echo wp_kses_post(__('Display Options', 'propeller-ecommerce-v2')); ?>
                </h2>

                <div class="row g-3">
                    <div class="form-group col-md-6 pb-0">
                        <label class="font-weight-medium" for="track_user_attr">
                            <?php echo wp_kses_post(__('User tracking attributes', 'propeller-ecommerce-v2')); ?>

                        </label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Define the attribute used to personalize content and track behavior for individual users', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                        <input type="text" class="border form-control" id="track_user_attr" placeholder="<?php echo wp_kses_post(__('Comma separated attribute names', 'propeller-ecommerce-v2')); ?>" name="track_user_attr" value="<?php echo isset($behavior_result->track_user_attr) ? esc_attr($behavior_result->track_user_attr) : ''; ?>">
                    </div>
                </div>
                <div class="row g-3">
                    <div class="form-group col-md-6 pb-0">
                        <label class="font-weight-medium" for="track_company_attr">
                            <?php echo wp_kses_post(__('Company tracking attributes', 'propeller-ecommerce-v2')); ?>

                        </label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Define attributes used to personalize content for company accounts (comma-separated)', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                        <input type="text" class="border form-control" id="track_company_attr" placeholder="<?php echo wp_kses_post(__('Comma separated attribute names', 'propeller-ecommerce-v2')); ?>" name="track_company_attr" value="<?php echo isset($behavior_result->track_company_attr) ? esc_attr($behavior_result->track_company_attr) : ''; ?>">
                    </div>
                </div>
                <div class="row g-3">
                    <div class="form-group col-md-6 pb-0">
                        <label class="font-weight-medium" for="track_product_attr">
                            <?php echo wp_kses_post(__('Product tracking attributes', 'propeller-ecommerce-v2')); ?>

                        </label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Specify attributes to display additional product information and enhance tracking (comma-separated)', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                        <input type="text" class="border form-control" id="track_product_attr" placeholder="<?php echo wp_kses_post(__('Comma separated attribute names', 'propeller-ecommerce-v2')); ?>" name="track_product_attr" value="<?php echo isset($behavior_result->track_product_attr) ? esc_attr($behavior_result->track_product_attr) : ''; ?>">
                    </div>
                </div>
                <div class="row g-3">
                    <div class="form-group col-md-6 pb-0">
                        <label class="font-weight-medium" for="track_category_attr">
                            <?php echo wp_kses_post(__('Category tracking attributes', 'propeller-ecommerce-v2')); ?>

                        </label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Specify attributes to display additional category information and enhance tracking (comma-separated)', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                        <input type="text" class="border form-control" id="track_category_attr" placeholder="<?php echo wp_kses_post(__('Comma separated attribute names', 'propeller-ecommerce-v2')); ?>" name="track_category_attr" value="<?php echo isset($behavior_result->track_category_attr) ? esc_attr($behavior_result->track_category_attr) : ''; ?>">
                    </div>
                </div>


                <h2 class="propel-subtitle font-weight-semibold">
                    <?php echo wp_kses_post(__('Performance & Technical', 'propeller-ecommerce-v2')); ?>
                </h2>
                <div class="row g-3">
                    <div class="form-group col-12 pb-0">
                        <input type="checkbox" class="border form-control" id="wordpress_session" name="wordpress_session" value="true" <?php echo isset($behavior_result->wordpress_session) && $behavior_result->wordpress_session == 1 ? 'checked' : ''; ?>>
                        <label class="font-weight-medium" for="wordpress_session">
                            <?php echo wp_kses_post(__('Enable WordPress sessions', 'propeller-ecommerce-v2')); ?>

                        </label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Integrate and synchronize Propeller sessions with WordPress sessions', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="row g-3">
                    <?php $assets_type = isset($behavior_result->assets_type) ? intval($behavior_result->assets_type) : 1; ?>
                    <div class="form-group col-md-6">
                        <label class="font-weight-medium" for="assets_type">
                            <?php echo wp_kses_post(__('Assets enqueuing type', 'propeller-ecommerce-v2')); ?>

                        </label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Standard mode loads assets only where needed (use plugins like Autoptimize for optimization). Global/Minified mode combines and minifies all assets but loads them globally, which may impact performance', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                        <select class="border form-control" id="assets_type" name="assets_type">
                            <option <?php selected(1, $assets_type); ?> value="1"><?php echo wp_kses_post(__('Standard - Enqueue assets without modification/combination, let performance plugins handle this.', 'propeller-ecommerce-v2')); ?></option>
                            <option <?php selected(2, $assets_type); ?> value="2"><?php echo wp_kses_post(__('Global Combined/Minified - Include all assets globally, this method has performance implications.', 'propeller-ecommerce-v2')); ?></option>
                        </select>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="form-group col-md-6 pb-0">
                        <input type="checkbox" class="border form-control" id="lazyload_images" name="lazyload_images" value="true" <?php echo isset($behavior_result->lazyload_images) && intval($behavior_result->lazyload_images) == 1 ? 'checked' : ''; ?>>
                        <label class="font-weight-medium" for="lazyload_images">
                            <?php echo wp_kses_post(__('Enable lazy loading for images', 'propeller-ecommerce-v2')); ?>

                        </label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Lazy load images from the API on category, brand, search, and product detail pages to improve performance', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="form-group col-md-6 pb-0">
                        <input type="checkbox" class="border form-control" id="lang_for_attrs" name="lang_for_attrs" value="true" <?php echo isset($behavior_result->lang_for_attrs) && intval($behavior_result->lang_for_attrs) == 1 ? 'checked' : ''; ?>>
                        <label class="font-weight-medium" for="lang_for_attrs">
                            <?php echo wp_kses_post(__('Use session language for attributes', 'propeller-ecommerce-v2')); ?>

                        </label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Fetch attributes only in the currently selected language instead of retrieving all available language versions', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="form-group col-md-6 pb-0">
                        <input type="checkbox" class="border form-control" id="ids_in_urls" name="ids_in_urls" value="true" <?php echo isset($behavior_result->ids_in_urls) && intval($behavior_result->ids_in_urls) == 1 ? 'checked' : ''; ?>>
                        <label class="font-weight-medium" for="ids_in_urls">
                            <?php echo wp_kses_post(__('Include ID in product/category URLs', 'propeller-ecommerce-v2')); ?>

                        </label>
                        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Append the object ID to product and category URLs for improved tracking and consistency', 'propeller-ecommerce-v2')); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                        </span>
                    </div>
                </div>

                <div class="fixed-floating-wrapper">
                    <div class="row g-0 gap-2 w-100">
                        <div class="col ">
                            <button type="submit" id="submit-key" class="integration-form-btn btn btn-success d-flex align-items-center justify-content-center w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-save h-4 w-4">
                                    <path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"></path>
                                    <path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7"></path>
                                    <path d="M7 3v4a1 1 0 0 0 1 1h7"></path>
                                </svg>
                                <?php echo wp_kses_post(__('Save settings', 'propeller-ecommerce-v2')); ?>
                            </button>
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
        </div>
    </div>
</div>