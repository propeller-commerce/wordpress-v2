<?php 

namespace Propeller\Includes\Extra\Sso;

if ( ! defined( 'ABSPATH' ) ) exit;

use Propeller\Includes\Extra\Sso\Firebase\PropellerFirebase;
use Propeller\Includes\Enum\SsoProviders;

class PropellerSso {
    private static $instance;

    public static function instance(): self {
        return self::$instance ??= new self();
    }

    public function __construct() { 
        // Initialize Firebase SSO provider
        if (class_exists('Propeller\Includes\Extra\Sso\Firebase\PropellerFirebase')) {
            PropellerFirebase::instance();
        }

        // Initialize other SSO providers as needed
        // if (class_exists('Propeller\Includes\Extra\Sso\Auth0\PropellerAuth0'))
        //     \Propeller\Includes\Extra\Sso\Auth0\PropellerAuth0::instance();
    }

    public function bootstrap() {
        self::instance();
    }

    public function renderSignIn() {
        $firebase = PropellerFirebase::instance();
        if ($firebase && $firebase->isConfigured()) {
            return $firebase->renderSignIn();
        }
        
        // Fallback if no providers are configured
        return '<p>No SSO provider configured.</p>';
    }

    /**
     * Get Firebase configuration data from database
     */
    public function getFirebaseData() {
        global $table_prefix, $wpdb;
        
        $behavior_result = $wpdb->get_row($wpdb->prepare("SELECT * FROM %i", $table_prefix . PROPELLER_BEHAVIOR_TABLE));

        $sso_data = null;

        if ($behavior_result && $behavior_result->use_sso && !empty($behavior_result->sso_provider) && $behavior_result->sso_provider == SsoProviders::FIREBASE) {
            $sso_data = $behavior_result->sso_data ? json_decode($behavior_result->sso_data) : null;
        }
        
        return $sso_data;
    }

    /**
     * Handle Firebase authentication callback
     */
    public function handleFirebaseAuth($firebase_token, $user_data) {
        $firebase = PropellerFirebase::instance();
        if ($firebase) {
            return $firebase->handleAuthCallback($firebase_token, $user_data);
        }
        return false;
    }
}