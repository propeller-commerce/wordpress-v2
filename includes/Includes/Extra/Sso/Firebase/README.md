# Firebase SSO Authentication for Propeller E-commerce Plugin

This implementation provides Firebase-based Single Sign-On (SSO) authentication for the Propeller E-commerce v2 WordPress plugin.

## Features

- **Multiple Provider Support**: Auth0, Google, Microsoft/Azure AD, OKTA, MockSAML
- **Firebase Integration**: Full Firebase Authentication integration
- **Multi-tenant Support**: Supports Firebase Identity Platform multi-tenancy
- **Security**: Proper nonce verification and secure token handling
- **User Claims**: Automatic claims reset after successful authentication
- **Modern UI**: Clean, responsive authentication interface

## Architecture

### Core Files

1. **PropellerSso.php** - Main SSO coordinator class
2. **PropellerFirebase.php** - Firebase authentication handler
3. **views/sign-in-splash.php** - Authentication UI template
4. **firebase-auth.js** - Frontend JavaScript for Firebase integration

### Admin Configuration

- **propeller-firebase.php** - Admin settings page for Firebase configuration

## Setup Instructions

### 1. Firebase Project Setup

1. Create a Firebase project at [Firebase Console](https://console.firebase.google.com/)
2. Enable Authentication in the Firebase Console
3. Configure your desired sign-in providers:
   - **Google**: Enable Google provider in Authentication > Sign-in method
   - **Microsoft**: Configure Microsoft provider with Azure AD settings
   - **OKTA**: Set up OKTA as a SAML provider
   - **Auth0**: Configure Auth0 as an OIDC provider
   - **MockSAML**: Set up SAML provider for testing

### 2. Domain Authorization

Add your WordPress domain to Firebase authorized domains:
1. Go to Authentication > Settings > Authorized domains
2. Add your domain (e.g., `yoursite.com`)

### 3. Configuration Values

From Firebase Console > Project Settings > General, copy these values:

- **API Key**: Web API Key
- **Auth Domain**: Project ID + `.firebaseapp.com`
- **Project ID**: Your Firebase project ID
- **App ID**: Web app ID (format: `1:123456789:web:abc123def456`)

### 4. WordPress Admin Configuration

1. Go to WordPress Admin > Propeller > Settings > SSO
2. Select "Firebase" as SSO Provider
3. Enter your Firebase configuration values
4. Save settings

### 5. Multi-tenant Setup (Optional)

For multi-tenant environments:

1. Enable Identity Platform in Firebase Console
2. Create tenants for different organizations
3. Configure tenant-specific settings in the admin panel

## Provider-Specific Configuration

### Microsoft/Azure AD

1. Register application in Azure AD
2. Configure redirect URIs in Azure AD
3. Add Azure Tenant ID to Firebase configuration

### OKTA

1. Create OKTA application
2. Configure SAML settings
3. Add OKTA domain to Firebase configuration

### Auth0

1. Create Auth0 application
2. Configure as OIDC provider in Firebase
3. Set up appropriate scopes and claims

## Implementation Details

### Authentication Flow

1. User clicks provider button on sign-in page
2. Firebase popup opens for provider authentication
3. User completes authentication with provider
4. Firebase returns ID token and user data
5. Frontend sends token to WordPress via AJAX
6. PropellerFirebase calls AuthController::reset_claims()
7. Claims are reset in Propeller GraphQL API
8. User is redirected to homepage

### Security Features

- **Nonce Verification**: All AJAX requests use WordPress nonces
- **Token Validation**: Firebase ID tokens are validated server-side
- **Secure Storage**: Sensitive configuration stored in database
- **Input Sanitization**: All user inputs are sanitized

### Error Handling

- **Popup Blocking**: Graceful handling of popup blockers
- **Provider Errors**: Specific error messages for different failure scenarios
- **Network Issues**: Retry mechanisms and user feedback
- **Configuration Errors**: Clear error messages for setup issues

## Usage

### Shortcode

The SSO login interface is available via the `[sso-sign-in]` shortcode, which is automatically rendered on the `/sign-in/` page.

### Programmatic Access

```php
// Get Firebase instance
$firebase = \Propeller\Includes\Extra\Sso\Firebase\PropellerFirebase::instance();

// Check if configured
if ($firebase->isConfigured()) {
    // Render sign-in interface
    echo $firebase->renderSignIn();
}
```

## Troubleshooting

### Common Issues

1. **Popup Blocked**: Ensure users allow popups for your domain
2. **Domain Not Authorized**: Add domain to Firebase authorized domains
3. **Provider Not Enabled**: Enable providers in Firebase Console
4. **Tenant Mismatch**: Verify tenant configuration for multi-tenant setups

### Debug Mode

Enable WordPress debug mode to see detailed error logs:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

### JavaScript Console

Check browser console for Firebase-specific errors and configuration issues.

## API Reference

### PropellerFirebase Methods

- `isConfigured()`: Check if Firebase is properly configured
- `renderSignIn()`: Render the authentication interface
- `handleAuthCallback()`: Process authentication callback
- `getClientConfig()`: Get Firebase configuration for frontend

### JavaScript Events

- `firebase:auth:start`: Authentication started
- `firebase:auth:success`: Authentication successful
- `firebase:auth:error`: Authentication failed

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## Dependencies

- Firebase JavaScript SDK 9.0.0+
- jQuery (included with WordPress)
- Bootstrap CSS (for styling)

## Changelog

### Version 1.0.0
- Initial implementation
- Support for Google, Microsoft, OKTA, Auth0, MockSAML
- Multi-tenant support
- Complete admin interface
- Comprehensive error handling
