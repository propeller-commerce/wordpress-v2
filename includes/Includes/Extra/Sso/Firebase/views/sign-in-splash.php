<?php
if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div id="firebase-auth-container" class="firebase-sso-container">
    <div class="firebase-auth-splash">
        <h2><?php echo esc_html(__('Sign in with SSO', 'propeller-ecommerce-v2')); ?></h2>

        <div id="firebase-status-message" class="alert" style="display: none;"></div>

        <div class="sso-providers">

            <!-- Auth0 Provider -->
            <button type="button" class="btn-green btn-proceed sso-provider-btn" id="auth0-login-btn">
                <i class="fab fa-auth0"></i>
                <?php echo esc_html(__('Sign in with Auth0', 'propeller-ecommerce-v2')); ?>
            </button>

            <?php /*
            <!-- Google Provider -->
            <button type="button" class="btn-green btn-proceed sso-provider-btn" id="google-login-btn">
                <i class="fab fa-google"></i>
                <?php echo __('Sign in with Google', 'propeller-ecommerce-v2'); ?>
            </button>

            <!-- Microsoft/Azure AD Provider -->
            <button type="button" class="btn-green btn-proceed sso-provider-btn" id="microsoft-login-btn">
                <i class="fab fa-microsoft"></i>
                <?php echo __('Sign in with Microsoft', 'propeller-ecommerce-v2'); ?>
            </button>

            <!-- OKTA Provider -->
            <button type="button" class="btn-green btn-proceed sso-provider-btn" id="okta-login-btn">
                <i class="fas fa-building"></i>
                <?php echo __('Sign in with OKTA', 'propeller-ecommerce-v2'); ?>
            </button>

            <!-- MockSAML Provider -->
            <button type="button" class="btn-green btn-proceed sso-provider-btn" id="mocksaml-login-btn">
                <i class="fas fa-key"></i>
                <?php echo __('Sign in with MockSAML', 'propeller-ecommerce-v2'); ?>
            </button>
            */ ?>
        </div>

        <div id="firebase-loading" class="text-center" style="display: none;">
            <div class="spinner-border" role="status">
                <span class="sr-only"><?php echo esc_html(__('Loading...', 'propeller-ecommerce-v2')); ?></span>
            </div>
            <p id="loading-message"><?php echo esc_html(__('Authenticating...', 'propeller-ecommerce-v2')); ?></p>
        </div>
    </div>
</div>

<style>
    .firebase-sso-container {
        max-width: 400px;
        margin: 0 auto;
        padding: 20px;
    }

    .firebase-auth-splash {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .firebase-auth-splash h2 {
        text-align: center;
        margin-bottom: 30px;
        color: #333;
    }

    .sso-providers {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .sso-provider-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 12px 20px;
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.3s ease;
        text-decoration: none;
        border: 2px solid;
        background-color: var(--base-theme-color) !important;
        color: #fff;
        font-size: 1.4rem;
    }

    .sso-provider-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        background-color: var(--second-theme-color) !important;
    }

    .sso-provider-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .sso-provider-btn i {
        font-size: 18px;
    }

    .alert {
        padding: 12px;
        border-radius: 4px;
        margin-bottom: 20px;
    }

    .alert-success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }

    .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }

    .alert-info {
        background-color: #cce7ff;
        border-color: #b8daff;
        color: #004085;
    }

    #firebase-loading {
        margin-top: 20px;
    }

    #firebase-loading p {
        margin-top: 15px;
        color: #666;
    }
</style>