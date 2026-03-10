<?php

namespace Propeller\Includes\Extra\Sso\Firebase;

if ( ! defined( 'ABSPATH' ) ) exit;

use Propeller\Includes\Controller\AuthController;
use Propeller\Includes\Enum\SsoProviders;
use Propeller\Includes\Controller\SessionController;
use Exception;
use Propeller\Includes\Controller\UserController;

class PropellerFirebase {
    private static $instance;
    private $config_data;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    public function __construct() {
        $this->config_data = $this->getFirebaseConfigData();
        
        // Register AJAX handlers for Firebase authentication
        add_action('wp_ajax_firebase_auth_callback', [$this, 'handleAuthCallback']);
        add_action('wp_ajax_nopriv_firebase_auth_callback', [$this, 'handleAuthCallback']);

        add_action('wp_ajax_firebase_store_token', [$this, 'storeRefreshedToken']);
        add_action('wp_ajax_nopriv_firebase_store_token', [$this, 'storeRefreshedToken']);
    }

    /**
     * Check if Firebase is properly configured
     */
    public function isConfigured(): bool {
        return !empty($this->config_data) && 
               !empty($this->config_data->apiKey) && 
               !empty($this->config_data->authDomain) && 
               !empty($this->config_data->projectId) && 
               !empty($this->config_data->providerId);
    }

    /**
     * Get Firebase configuration data from database
     */
    private function getFirebaseConfigData() {
        global $table_prefix, $wpdb;
        
        $behavior_result = $wpdb->get_row($wpdb->prepare("SELECT * FROM %i", $table_prefix . PROPELLER_BEHAVIOR_TABLE));

        if ($behavior_result && $behavior_result->use_sso && 
            !empty($behavior_result->sso_provider) && 
            $behavior_result->sso_provider == SsoProviders::FIREBASE) {
            return $behavior_result->sso_data ? json_decode($behavior_result->sso_data) : null;
        }
        
        return null;
    }

    /**
     * Enqueue Firebase scripts
     */
    public function enqueueFirebaseScripts() {
        if (!$this->isConfigured()) {
            return;
        }

        // Check if scripts are already enqueued to avoid duplicates
        if (wp_script_is('propeller-firebase-auth', 'enqueued')) {
            return;
        }

        // Firebase core libraries
        wp_enqueue_script(
            'firebase-app',
            plugin_dir_url(__FILE__) . 'firebase-app-compat.js',
            [],
            '9.0.0',
            true
        );

        wp_enqueue_script(
            'firebase-auth',
            plugin_dir_url(__FILE__) . 'firebase-auth-compat.js',
            ['firebase-app'],
            '9.0.0',
            true
        );

        // Custom Firebase auth script
        wp_enqueue_script(
            'propeller-firebase-auth',
            plugin_dir_url(__FILE__) . 'firebase-auth.js',
            ['firebase-app', 'firebase-auth', 'jquery'],
            '1.0.1',
            true
        );

        // Pass Firebase config to JavaScript
        wp_localize_script('propeller-firebase-auth', 'propellerFirebaseConfig', [
            'apiKey' => $this->config_data->apiKey,
            'authDomain' => $this->config_data->authDomain,
            'projectId' => $this->config_data->projectId,
            'providerId' => $this->config_data->providerId ?? '',
            // 'appId' => $this->config_data->appId,
            'tenantId' => $this->config_data->tenantId ?? '',
            // 'azureTenantId' => $this->config_data->azureTenantId ?? '',
            // 'oktaDomain' => $this->config_data->oktaDomain ?? '',
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('firebase_auth_nonce')
        ]);
    }

    /**
     * Render the sign-in interface
     */
    public function renderSignIn(): string {
        if (!$this->isConfigured()) {
            // Debug: Show more specific error message
            $config_status = [];
            if (empty($this->config_data)) {
                $config_status[] = 'No configuration data found';
            } else {
                if (empty($this->config_data->apiKey)) $config_status[] = 'Missing API Key';
                if (empty($this->config_data->authDomain)) $config_status[] = 'Missing Auth Domain';
                if (empty($this->config_data->projectId)) $config_status[] = 'Missing Project ID';
                if (empty($this->config_data->providerId)) $config_status[] = 'Missing Provider ID';
            }
            
            return '<p>Firebase SSO is not properly configured.<br><small>Missing: ' . implode(', ', $config_status) . '</small></p>';
        }

        // Add Firebase scripts to footer for this page
        add_action('wp_footer', [$this, 'printFirebaseScripts'], 20);

        ob_start();
        include __DIR__ . '/views/sign-in-splash.php';
        return ob_get_clean();
    }

    /**
     * Print Firebase scripts directly in footer
     */
    public function printFirebaseScripts() {
        if (!$this->isConfigured()) {
            return;
        }

        // Check if scripts are already printed to avoid duplicates
        static $scripts_printed = false;
        if ($scripts_printed) {
            return;
        }
        $scripts_printed = true;

        $config = [
            'apiKey' => $this->config_data->apiKey,
            'authDomain' => $this->config_data->authDomain,
            'projectId' => $this->config_data->projectId,
            'providerId' => $this->config_data->providerId,
            'tenantId' => $this->config_data->tenantId ?? '',
            // 'azureTenantId' => $this->config_data->azureTenantId ?? '',
            // 'oktaDomain' => $this->config_data->oktaDomain ?? '',
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('firebase_auth_nonce')
        ];

        /* Firebase Authentication Scripts */
        $src = plugin_dir_url(__FILE__) . 'firebase-app-compat.js';
        wp_enqueue_script('firebase-app-compat', $src, array(), null, false);

        $src = plugin_dir_url(__FILE__) . 'firebase-auth-compat.js';
        wp_enqueue_script('firebase-auth-compat', $src, array(), null, false);
        
        apply_filters('propel_firebase_auth_scripts', $config, plugin_dir_url(__FILE__));

        $src = plugin_dir_url(__FILE__) . 'firebase-auth.js?v=1.0.1';

        wp_enqueue_script('propeller-firebase-auth', $src, array(), null, false);
    }

    /**
     * Handle Firebase authentication callback
     */
    public function handleAuthCallback() {
        // Verify nonce for security
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'firebase_auth_nonce')) {
            wp_die('Security check failed');
        }

        $firebase_token = sanitize_text_field($_POST['idToken'] ?? '');
        $email = sanitize_email($_POST['email'] ?? '');
        $uid = sanitize_text_field($_POST['uid'] ?? '');
        $display_name = sanitize_text_field($_POST['displayName'] ?? '');

        if (empty($firebase_token) || empty($email) || empty($uid)) {
            wp_send_json_error(['message' => 'Missing required authentication data']);
            return;
        }

        try {
            SessionController::set(PROPELLER_ID_TOKEN, $firebase_token);
            
            // Call reset_claims method from AuthController
            $auth_controller = new AuthController();
            $result = $auth_controller->reset_claims($email, $uid);

            SessionController::remove(PROPELLER_ID_TOKEN);

            if ($result) {
                // Claims reset successful, user is now authenticated
                wp_send_json_success([
                    'message' => 'Authentication successful',
                    'user' => [
                        'email' => $email,
                        'uid' => $uid,
                        'displayName' => $display_name
                    ],
                    'redirect' => home_url('/')
                ]);
            } else {
                wp_send_json_error(['message' => 'Claims reset failed']);
            }

        } catch (Exception $e) {
            error_log('Firebase authentication error: ' . $e->getMessage());
            wp_send_json_error(['message' => 'Authentication failed: ' . $e->getMessage()]);
        }
    }

    public function storeRefreshedToken() {
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'firebase_auth_nonce')) {
            wp_die('Security check failed');
        }

        $refreshedToken = sanitize_text_field($_POST['refreshedToken'] ?? '');
        
        SessionController::set(PROPELLER_ACCESS_TOKEN, $refreshedToken);

        $userController = new UserController();
        
        wp_send_json_success($userController->firebase_sso_login());
    }

    /**
     * Get Firebase configuration for frontend use
     */
    public function getClientConfig(): array {
        if (!$this->isConfigured()) {
            return [];
        }

        return [
            'apiKey' => $this->config_data->apiKey,
            'authDomain' => $this->config_data->authDomain,
            'projectId' => $this->config_data->projectId,
            // 'appId' => $this->config_data->appId,
            'tenantId' => $this->config_data->tenantId ?? '',
        ];
    }
}