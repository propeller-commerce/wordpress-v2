<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php

$translations = [];

if (isset($_REQUEST['file']) && !empty($_REQUEST['file']) && isset($_REQUEST['open_translation']) && $_REQUEST['open_translation'] == 'true') {
    $open_file = sanitize_text_field($_REQUEST['file']);
    $translations = $translator->load_translation($open_file);
}
?>
<div class="container-fluid propel-admin-panel">
    <div class="row propeller-admin-title mb-4">
        <div class="col-12 col-md-6">
            <h1 class="mb-2 font-weight-bold">
                <?php echo wp_kses_post(__('Translations', 'propeller-ecommerce-v2')); ?>
            </h1>
            <small class="d-block text-secondary">
                <?php echo wp_kses_post(__('Manage multilingual content and localization', 'propeller-ecommerce-v2')); ?>
            </small>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-10 col-lg-9">
            <div class="container-fluid px-0">
                <div class="row">
                    <div class="col-12 col-md-6 mb-4">
                        <form method="GET" class="propel-admin-form propel-translation-form h-100" action="<?php echo esc_url(admin_url('admin.php')); ?>" id="open_translations_form">
                            <input type="hidden" name="page" value="propeller-translations">
                            <div class="d-flex align-items-center mb-2">
                                <div class="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-folder-open h-5 w-5 text-primary">
                                        <path d="m6 14 1.5-2.9A2 2 0 0 1 9.24 10H20a2 2 0 0 1 1.94 2.5l-1.54 6a2 2 0 0 1-1.95 1.5H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3.9a2 2 0 0 1 1.69.9l.81 1.2a2 2 0 0 0 1.67.9H18a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </div>
                                <div class="description">
                                    <p><?php echo wp_kses_post(__('Open translations', 'propeller-ecommerce-v2')); ?></p>
                                    <small><?php echo wp_kses_post(__('Load existing translation file', 'propeller-ecommerce-v2')); ?></small>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="form-group col-md-12">
                                    <select name="file" class="border form-control" id="translation_file">
                                        <option value=""><?php echo wp_kses_post(__('Select translations', 'propeller-ecommerce-v2')); ?></option>
                                        <?php foreach ($translator->get_translations() as $trn_file) { ?>
                                            <?php
                                            $selected = '';

                                            if (
                                                isset($_REQUEST['open_translation']) && $_REQUEST['open_translation'] == 'true' &&
                                                isset($_REQUEST['file']) && $_REQUEST['file'] == basename($trn_file)
                                            ) {
                                                $selected = 'selected="selected"';
                                            }
                                            ?>
                                            <option value="<?php echo esc_attr(basename(esc_attr($trn_file))); ?>" <?php echo esc_attr((string) $selected); ?>><?php echo wp_kses_post(basename($trn_file)); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group col-md-12">
                                    <button type="submit" name="open_translation" value="true" class="w-100 btn btn-success justify-content-center"><?php echo wp_kses_post(__('Open', 'propeller-ecommerce-v2')); ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-12 col-md-6 mb-4">
                        <form method="POST" class="propel-admin-form propel-translation-form h-100" action="<?php echo esc_url(admin_url('admin.php')); ?>" id="create_translations_form">
                            <input type="hidden" name="action" value="create_translations_file">
                            <input type="hidden" name="page" value="propeller-translations">
                            <div class="d-flex align-items-center mb-2">
                                <div class="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-plus h-5 w-5 text-primary">
                                        <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path>
                                        <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
                                        <path d="M9 15h6"></path>
                                        <path d="M12 18v-6"></path>
                                    </svg>
                                </div>
                                <div class="description">
                                    <p><?php echo wp_kses_post(__('Create translations', 'propeller-ecommerce-v2')); ?></p>
                                    <small><?php echo wp_kses_post(__('Generate new translation file', 'propeller-ecommerce-v2')); ?></small>
                                </div>
                            </div>
                            <select name="file" class="border form-control">
                                <option value=""><?php echo wp_kses_post(__('Open translations template', 'propeller-ecommerce-v2')); ?></option>

                                <?php foreach ($translator->get_templates() as $temp_file) { ?>
                                    <option value="<?php echo esc_attr(wp_basename($temp_file)); ?>"><?php echo esc_html(wp_basename($temp_file)); ?></option>
                                <?php } ?>
                            </select>

                            <select name="locale" id="select_lang" class="border form-control mt-3">
                                <option value=""><?php echo wp_kses_post(__('Select language', 'propeller-ecommerce-v2')); ?></option>
                                <?php foreach ($translator->get_available_languages() as $loc => $locale) { ?>
                                    <option value="<?php echo esc_attr($locale['wp_locale']); ?>"><?php echo esc_html($locale['name']); ?></option>
                                <?php } ?>
                            </select>

                            <select name="merge" class="border form-control mt-3">
                                <option value=""><?php echo wp_kses_post(__('Merge with previous translations', 'propeller-ecommerce-v2')); ?></option>
                                <?php foreach ($translator->get_translations() as $trn_file) { ?>
                                    <option value="<?php echo esc_attr(wp_basename($trn_file)); ?>"><?php echo esc_html(wp_basename($trn_file)); ?></option>
                                <?php } ?>
                            </select>
                            <div class="row g-3">
                                <div class="form-group col-md-12">
                                    <button type="submit" name="open_template" class="mt-3 btn btn-success w-100 justify-content-center"><?php echo wp_kses_post(__('Create translations file', 'propeller-ecommerce-v2')); ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-12">
                        <div class="accordion-container restore-container">
                            <button class="btn" type="button" data-bs-toggle="collapse" data-bs-target="#restoreBackups" aria-expanded="false" aria-controls="restoreBackups">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-rotate-ccw h-4 w-4 text-muted-foreground">
                                    <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                                    <path d="M3 3v5h5"></path>
                                </svg>
                                <?php echo wp_kses_post(__('Restore translations', 'propeller-ecommerce-v2')); ?>
                            </button>
                            <div class="collapse" id="restoreBackups">
                                <form method="POST" class="propel-admin-form h-100 mt-4" action="<?php echo esc_url(admin_url('admin.php')); ?>" id="restore_translations_form">
                                    <input type="hidden" name="page" value="propeller-translations">

                                    <div class="row g-3">
                                        <div class="form-group col-md-12">
                                            <select name="backup_date" class="border form-control" id="backup_date">
                                                <option value=""><?php echo wp_kses_post(__('Select backup', 'propeller-ecommerce-v2')); ?></option>
                                                <?php foreach ($translator->get_backups() as $bkp) { ?>
                                                    <option value="<?php echo esc_attr(wp_basename($bkp)); ?>"><?php echo wp_kses_post(str_replace('+', ' ', str_replace('_', ':', wp_basename($bkp)))); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <button type="submit" name="restore_translation" value="true" class="w-100 btn btn-success btn-white justify-content-center"><?php echo wp_kses_post(__('Restore', 'propeller-ecommerce-v2')); ?></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <form method="POST" class="propel-admin-form propel-translation-form mt-4" action="#" id="propel_translations_form">
                <input type="hidden" name="action" value="save_translations" />
                <input type="hidden" name="po_file" value="<?php echo isset($_REQUEST['file']) && !empty($_REQUEST['file']) ? esc_attr($_REQUEST['file']) : ''; ?>" />

                <div class="row justify-content-between mb-2">
                    <div class="col-12 col-md">
                        <h4 class="mb-0"><?php echo wp_kses_post(__('Translation Editor', 'propeller-ecommerce-v2')); ?></h4>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <?php if (!count($translations)) { ?>
                            <div class="alert alert-secondary" role="alert">
                                <div class="d-flex align-items-start">
                                    <div class="alert-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-alert h-5 w-5 text-muted-foreground">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" x2="12" y1="8" y2="12"></line>
                                            <line x1="12" x2="12.01" y1="16" y2="16"></line>
                                        </svg>
                                    </div>
                                    <h5>
                                        <?php echo wp_kses_post(__('Please open a translations file to start editing', 'propeller-ecommerce-v2')); ?>
                                    </h5>
                                </div>
                            </div>
                        <?php } ?>
                        <hr />
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <table class="table table-borderless table-hover table-sm">
                            <thead>
                                <tr>

                                    <th class="propel-col-50"><?php echo wp_kses_post(__('ORIGINAL', 'propeller-ecommerce-v2')); ?><br /><small><?php echo wp_kses_post(__('Source text will appear here', 'propeller-ecommerce-v2')); ?></small></th>
                                    <th class="propel-col-50"><?php echo wp_kses_post(__('TRANSLATED', 'propeller-ecommerce-v2')); ?><br /><small><?php echo wp_kses_post(__('Translations will appear here', 'propeller-ecommerce-v2')); ?></small></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($translations)) { ?>
                                    <?php $index = 1; ?>
                                    <?php foreach ($translations as $translation) { ?>
                                        <tr>

                                            <td>
                                                <input type="text" class="border form-control" readonly name="original[<?php echo esc_attr(intval($index)); ?>]" value="<?php echo esc_attr(htmlspecialchars($translation->getOriginal())); ?>" />
                                            </td>
                                            <td>
                                                <input type="text" class="border form-control" name="translation[<?php echo esc_attr(intval($index)); ?>]" value="<?php echo esc_attr(htmlspecialchars($translation->getTranslation())); ?>" />
                                            </td>
                                        </tr>
                                        <?php $index++; ?>
                                <?php }
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="fixed-floating-wrapper translations-floating-wrapper">
                    <div class="row g-0 gap-2 w-100 justify-content-md-center">
                        <div class="col col-md-3">
                            <button type="button" id="scan_translations" class="integration-form-btn btn btn-success w-100 justify-content-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-scan me-2 h-4 w-4">
                                    <path d="M3 7V5a2 2 0 0 1 2-2h2"></path>
                                    <path d="M17 3h2a2 2 0 0 1 2 2v2"></path>
                                    <path d="M21 17v2a2 2 0 0 1-2 2h-2"></path>
                                    <path d="M7 21H5a2 2 0 0 1-2-2v-2"></path>
                                </svg>
                                <?php echo wp_kses_post(__('Scan for new translations', 'propeller-ecommerce-v2')); ?></button>
                        </div>
                        <div class="col col-md-3">
                            <button type="submit" id="save_translations" class="integration-form-btn btn btn-success btn-white w-100 justify-content-center ">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-save me-2 h-4 w-4">
                                    <path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"></path>
                                    <path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7"></path>
                                    <path d="M7 3v4a1 1 0 0 0 1 1h7"></path>
                                </svg>
                                <?php echo wp_kses_post(__('Save translations', 'propeller-ecommerce-v2')); ?></button>
                        </div>
                        <div class="col col-md-3">
                            <button type="button" id="generate_translations_btn"
                                data-po-file="<?php echo isset($_REQUEST['file']) && !empty($_REQUEST['file']) ? esc_attr($_REQUEST['file']) : '' ?>"
                                class="btn btn-success w-100 justify-content-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-upload me-2 h-5 w-5">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" x2="12" y1="3" y2="15"></line>
                                </svg><?php echo wp_kses_post(__('Publish translations', 'propeller-ecommerce-v2')); ?>
                            </button>
                        </div>

                        <div class="col-auto d-flex justify-content-center">
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