<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="row mb-3">
    <div class="col-12">
        <label for="firebase_api_key" class="font-weight-medium">API Key</label>
        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Firebase Web API Key from Project Settings > General > Web API Key', 'propeller-ecommerce-v2')); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                <path d="M12 17h.01"></path>
            </svg>
        </span>
    </div>
    <div class="col-sm-10 d-flex gap-2">
        <input type="password" class="form-control" id="firebase_api_key" name="Firebase[apiKey]" value="<?php echo isset($sso_data->apiKey) && !empty($sso_data->apiKey) ? esc_attr(str_repeat('•', 32)) : ''; ?>" data-original-value="<?php echo isset($sso_data->apiKey) ? esc_attr($sso_data->apiKey) : ''; ?>" placeholder="Your Firebase API Key">
        <button type="button" class="btn btn-border-keys toggle-password" data-target="#firebase_api_key" title="Show/Hide Firebase API key">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
        </button>
    </div>
</div>

<div class="row mb-3">
    <div class="col-12">
        <label for="firebase_api_key" class="font-weight-medium">Auth Domain</label>
        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Firebase Auth Domain from Project Settings > General', 'propeller-ecommerce-v2')); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                <path d="M12 17h.01"></path>
            </svg>
        </span>
    </div>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="firebase_auth_domain" name="Firebase[authDomain]" value="<?php echo esc_attr($sso_data ? $sso_data->authDomain : ''); ?>" placeholder="your-project.firebaseapp.com">
    </div>
</div>

<div class="row mb-3">
    <div class="col-12">
        <label for="firebase_api_key" class="font-weight-medium">Project ID</label>
        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Firebase Project ID from Project Settings > General', 'propeller-ecommerce-v2')); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                <path d="M12 17h.01"></path>
            </svg>
        </span>
    </div>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="firebase_project_id" name="Firebase[projectId]" value="<?php echo esc_attr($sso_data ? $sso_data->projectId : ''); ?>" placeholder="your-project-id">
    </div>
</div>

<div class="row mb-3">
    <div class="col-12">
        <label for="firebase_api_key" class="font-weight-medium">SSO Provider ID</label>
        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                <path d="M12 17h.01"></path>
            </svg>
        </span>
    </div>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="firebase_provider_id" name="Firebase[providerId]" value="<?php echo esc_attr($sso_data ? $sso_data->providerId : ''); ?>" placeholder="your-provider-id">
    </div>
</div>

<?php /*
<div class="row mb-3">
    <label for="firebase_app_id" class="col-sm-2 col-form-label">App ID <small class="text-muted">(Optional)</small></label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="firebase_app_id" name="Firebase[appId]" value="<?php echo esc_attr($sso_data ? $sso_data->appId : ''); ?>" placeholder="1:123456789:web:abc123def456">
        <small class="text-muted">Firebase App ID from Project Settings > General</small>
    </div>
</div>

<div class="row mb-3">
    <label for="firebase_storage_bucket" class="col-sm-2 col-form-label">Storage Bucket <small class="text-muted">(Optional)</small></label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="firebase_storage_bucket" name="Firebase[storageBucket]" value="<?php echo esc_attr($sso_data ? $sso_data->storageBucket : ''); ?>" placeholder="your-project.appspot.com">
        <small class="text-muted">Firebase Storage Bucket from Project Settings > General</small>
    </div>
</div>

<div class="row mb-3">
    <label for="firebase_messaging_sender_id" class="col-sm-2 col-form-label">Messaging Sender ID <small class="text-muted">(Optional)</small></label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="firebase_messaging_sender_id" name="Firebase[messagingSenderId]" value="<?php echo esc_attr($sso_data ? $sso_data->messagingSenderId : ''); ?>" placeholder="123456789">
        <small class="text-muted">Firebase Messaging Sender ID from Project Settings > General</small>
    </div>
</div>

<div class="row mb-3">
    <label for="firebase_measurement_id" class="col-sm-2 col-form-label">Measurement ID <small class="text-muted">(Optional)</small></label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="firebase_measurement_id" name="Firebase[measurementId]" value="<?php echo esc_attr($sso_data ? $sso_data->measurementId : ''); ?>" placeholder="G-XXXXXXXXXX">
        <small class="text-muted">Firebase Measurement ID from Project Settings > General</small>
    </div>
</div>
*/ ?>
<hr class="my-4">

<h5 class="font-weight-medium mb-3"><?php echo wp_kses_post(__('Multi-tenant Configuration', 'propeller-ecommerce-v2')); ?></h5>

<div class="row mb-3">
    <div class="col-12">
        <label for="firebase_api_key" class="font-weight-medium">Tenant ID <small class="text-muted">(Optional)</small></label>
        <span class="help-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo wp_kses_post(__('Firebase Tenant ID if using multi-tenancy', 'propeller-ecommerce-v2')); ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help h-4 w-4 text-muted-foreground cursor-help" data-state="closed">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                <path d="M12 17h.01"></path>
            </svg>
        </span>
    </div>

    <div class="col-sm-10">
        <input type="text" class="form-control" id="firebase_tenant_id" name="Firebase[tenantId]" value="<?php echo esc_attr($sso_data ? $sso_data->tenantId : ''); ?>" placeholder="tenant-id">
    </div>
</div>

<?php /*
<hr class="my-4">

<h5 class="text-secondary mb-3"><?php echo wp_kses_post(__('Provider-specific Configuration', 'propeller-ecommerce-v2')); ?></h5>

<div class="row mb-3">
    <label for="firebase_azure_tenant_id" class="col-sm-2 col-form-label">Azure Tenant ID <small class="text-muted">(Optional)</small></label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="firebase_azure_tenant_id" name="Firebase[azureTenantId]" value="<?php echo esc_attr($sso_data ? $sso_data->azureTenantId : ''); ?>" placeholder="your-azure-tenant-id">
        <small class="text-muted">Azure AD Tenant ID for Microsoft authentication</small>
    </div>
</div>

<div class="row mb-3">
    <label for="firebase_okta_clientId" class="col-sm-2 col-form-label">OKTA ClientId <small class="text-muted">(Optional)</small></label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="firebase_okta_clientId" name="Firebase[oktaClientId]" value="<?php echo esc_attr($sso_data ? $sso_data->oktaClientId : ''); ?>" placeholder="your-okta-client-id">
        <small class="text-muted">OKTA ClientId for OKTA authentication</small>
    </div>
</div>

<div class="row mb-3">
    <label for="firebase_okta_domain" class="col-sm-2 col-form-label">OKTA Domain <small class="text-muted">(Optional)</small></label>
    <div class="col-sm-10">
        <input type="text" class="form-control" id="firebase_okta_domain" name="Firebase[oktaDomain]" value="<?php echo esc_attr($sso_data ? $sso_data->oktaDomain : ''); ?>" placeholder="your-domain.okta.com">
        <small class="text-muted">OKTA domain for OKTA authentication (without https://)</small>
    </div>
</div>

*/ ?>

<div class="alert alert-info mt-4">
    <h5> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-alert h-5 w-5 text-muted-foreground">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" x2="12" y1="8" y2="12"></line>
            <line x1="12" x2="12.01" y1="16" y2="16"></line>
        </svg> Setup Instructions</h5>
    <ol class="mb-0 mt-3">
        <li>Create a Firebase project at <a href="https://console.firebase.google.com/" target="_blank">Firebase Console</a></li>
        <li>Enable Authentication and configure your desired sign-in providers</li>
        <li>Add your domain to the authorized domains list</li>
        <li>Copy the configuration values from Project Settings > General</li>
        <li>For multi-tenant setups, configure Identity Platform</li>
    </ol>
</div>