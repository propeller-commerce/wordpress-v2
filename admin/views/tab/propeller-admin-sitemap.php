<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
    <div class="container-fluid propel-admin-panel">
        <div class="row propeller-admin-title mb-4">
            <div class="col-12 col-md-6">
                <h1 class="mb-2 font-weight-bold">
                    <?php echo wp_kses_post(__('Sitemap', 'propeller-ecommerce-v2')); ?>
                </h1>
                <small class="d-block text-secondary">
                    <?php echo wp_kses_post(__('Configure your sitemap and SEO settings', 'propeller-ecommerce-v2')); ?>
                </small>
            </div>
            <div class="col-12 col-md-4 col-lg-3 d-flex justify-content-md-end align-items-start mt-3 mt-md-0">
                <button type="button" id="generate_sitemap" class="sitemap-form-btn btn btn-success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles me-2 h-5 w-5">
                        <path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"></path>
                        <path d="M20 3v4"></path>
                        <path d="M22 5h-4"></path>
                        <path d="M4 17v2"></path>
                        <path d="M5 18H3"></path>
                    </svg>
                    <?php echo wp_kses_post(__('Generate sitemap', 'propeller-ecommerce-v2')); ?>
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-md-10 col-lg-9">
                <?php
                if (!count($sitemap_files)) { ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-warning" role="alert">
                                <div class="d-flex align-items-start">
                                    <div class="alert-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-alert h-5 w-5 text-muted-foreground">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" x2="12" y1="8" y2="12"></line>
                                            <line x1="12" x2="12.01" y1="16" y2="16"></line>
                                        </svg>
                                    </div>
                                    <h5>
                                        <?php echo wp_kses_post(__('There are no sitemap files.', 'propeller-ecommerce-v2')); ?>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php } else if ($sitemap_valid) { ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-success" role="alert">
                                <div class="d-flex align-items-start">
                                    <div class="alert-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-alert h-5 w-5 text-muted-foreground">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <path d="M9 12l2 2 4-4" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                    <h5>
                                        <?php echo wp_kses_post(__('Your Sitemap is valid.', 'propeller-ecommerce-v2')); ?>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-warning" role="alert">
                                <div class="d-flex align-items-start">
                                    <div class="alert-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-triangle-alert h-5 w-5 text-orange-600 dark:text-orange-400">
                                            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"></path>
                                            <path d="M12 9v4"></path>
                                            <path d="M12 17h.01"></path>
                                        </svg>
                                    </div>
                                    <h5>
                                        <?php echo wp_kses_post(__('Your Sitemap was generated more than 24 hours ago. Please regenerate if you have made any changes to your content or products recently.', 'propeller-ecommerce-v2')); ?>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

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
                                    <?php echo wp_kses_post(__('Generating your sitemap usually takes place after midnight on a daily basis by running a scheduled task when your webshop is not under heavy usage. Generating sitemap might take a while until your catalog is processed.', 'propeller-ecommerce-v2')); ?>
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-md-8">
                        <div class="propel-translation-form h-100">
                            <div class="d-flex align-items-center mb-2">
                                <div class="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text h-5 w-5 text-primary">
                                        <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path>
                                        <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
                                        <path d="M10 9H8"></path>
                                        <path d="M16 13H8"></path>
                                        <path d="M16 17H8"></path>
                                    </svg>
                                </div>
                                <div class="description">
                                    <p><?php echo wp_kses_post(__('Generated Sitemaps', 'propeller-ecommerce-v2')); ?></p>
                                    <small><?php echo wp_kses_post(__('Available sitemap files', 'propeller-ecommerce-v2')); ?></small>
                                </div>
                            </div>
                            <?php if (count($sitemap_files)) { ?>
                                <?php if ($sitemap->has_index()) { ?>
                                    <?php $index = $sitemap->get_index(); ?>
                                    <div class="sitemap-row d-flex justify-content-between align-items-center">
                                        <a href="<?php echo esc_url($index->url); ?>" target="_blank">
                                            <?php echo esc_html(basename($index->url)); ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-external-link h-3 w-3">
                                                <path d="M15 3h6v6"></path>
                                                <path d="M10 14 21 3"></path>
                                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                            </svg>
                                        </a>
                                        <div class="time-slot">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock h-4 w-4">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <polyline points="12 6 12 12 16 14"></polyline>
                                            </svg>
                                            <?php echo esc_html($index->lastmod); ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php foreach ($sitemap_files as $file) { ?>
                                    <div class="sitemap-row d-flex justify-content-between align-items-center">
                                        <a href="<?php echo esc_url($file->url); ?>" target="_blank">
                                            <?php echo esc_html(basename($file->url)); ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-external-link h-3 w-3">
                                                <path d="M15 3h6v6"></path>
                                                <path d="M10 14 21 3"></path>
                                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                            </svg>
                                        </a>
                                        <div class="time-slot">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock h-4 w-4">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <polyline points="12 6 12 12 16 14"></polyline>
                                            </svg>
                                            <?php echo esc_html($file->lastmod); ?>
                                        </div>
                                    </div>
                                <?php } ?>

                            <?php } ?>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="propel-translation-form h-100">
                            <div class="d-flex align-items-center mb-2">
                                <div class="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles h-5 w-5 text-primary">
                                        <path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"></path>
                                        <path d="M20 3v4"></path>
                                        <path d="M22 5h-4"></path>
                                        <path d="M4 17v2"></path>
                                        <path d="M5 18H3"></path>
                                    </svg>
                                </div>
                                <div class="description">
                                    <p><?php echo wp_kses_post(__('Yoast SEO', 'propeller-ecommerce-v2')); ?></p>
                                    <small><?php echo wp_kses_post(__('Plugin integration', 'propeller-ecommerce-v2')); ?></small>
                                </div>
                            </div>
                            <?php
                            if (count($sitemap_files)) {
                                if ($sitemap->yoast_active()) {
                            ?>
                                    <div class="yst-plugin active"><?php echo esc_html(__('Active', 'propeller-ecommerce-v2')); ?></div>
                                    <p><?php echo esc_html(__('Yoast plugin is active! You can check your sitemap here: ', 'propeller-ecommerce-v2')); ?></p>
                                    <a href="<?php echo esc_url(home_url('/sitemap_index.xml')); ?>" target="_blank">
                                        <?php echo esc_html('sitemap_index.xml'); ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-external-link h-3 w-3">
                                            <path d="M15 3h6v6"></path>
                                            <path d="M10 14 21 3"></path>
                                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                        </svg>
                                    </a>
                                    <hr />
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
                                                <?php echo wp_kses_post(__('When Yoast plugin is active, Propeller\'s sitemap.xml file is not used.', 'propeller-ecommerce-v2')); ?>
                                            </h5>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <div class="yst-plugin inactive"><?php echo esc_html(__('Not active', 'propeller-ecommerce-v2')); ?></div>
                                    <p><?php echo esc_html(__('You are not using Yoast! You can check your sitemap here: ', 'propeller-ecommerce-v2')); ?></p>
                                    <a href="<?php echo esc_url(home_url('/sitemap.xml')); ?>" target="_blank">
                                        <?php echo esc_html('sitemap.xml'); ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-external-link h-3 w-3">
                                            <path d="M15 3h6v6"></path>
                                            <path d="M10 14 21 3"></path>
                                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                        </svg>
                                    </a>

                            <?php }
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>