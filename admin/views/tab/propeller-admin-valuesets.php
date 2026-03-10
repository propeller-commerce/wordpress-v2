<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="container-fluid propel-admin-panel">
    <div class="row propeller-admin-title mb-4">
        <div class="col-12 col-md-6">
            <h1 class="mb-2 font-weight-bold">
                <?php echo wp_kses_post(__('Valuesets', 'propeller-ecommerce-v2')); ?>
            </h1>
            <small class="d-block text-secondary">
                <?php echo wp_kses_post(__('Manage and synchronize your valuesets with translation data', 'propeller-ecommerce-v2')); ?>
            </small>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-10 col-lg-9">
            <form method="POST" class="propel-admin-form propel-translation-form" action="#" id="propel_valuesets_form">
                <input type="hidden" name="action" value="propel_sync_valuesets">
                <div class="row justify-content-between mb-2">
                    <div class="col-12 col-md">
                        <h4 class="mb-0"><?php echo wp_kses_post(__('Synchronization', 'propeller-ecommerce-v2')); ?></h4>
                        <small class="text-secondary"><?php echo wp_kses_post(__('Keep your valuesets in sync with webshop translations', 'propeller-ecommerce-v2')); ?></small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info" role="alert">
                            <div class="d-flex align-items-start">
                                <div class="alert-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-alert h-5 w-5 text-muted-foreground">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" x2="12" y1="8" y2="12"></line>
                                        <line x1="12" x2="12.01" y1="16" y2="16"></line>
                                    </svg>
                                </div>
                                <h5>
                                    <?php echo wp_kses_post(__('This action will synchronize your valuesets into the translations of the webshop.', 'propeller-ecommerce-v2')); ?>
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <button type="submit" id="submit-key" class="integration-form-btn btn btn-success">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-refresh-cw me-2 h-5 w-5">
                                <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"></path>
                                <path d="M21 3v5h-5"></path>
                                <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"></path>
                                <path d="M8 16H3v5"></path>
                            </svg>
                            <?php echo wp_kses_post(__('Sync valuesets into translatoins', 'propeller-ecommerce-v2')); ?>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>