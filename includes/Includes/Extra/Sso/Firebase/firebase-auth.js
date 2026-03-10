/**
 * Firebase Authentication Handler for Propeller Plugin
 */
(function($) {
    'use strict';

    class PropellerFirebaseAuth {
        constructor() {
            this.app = null;
            this.auth = null;
            this.isLoading = false;
            this.init();
        }

        init() {
            $(document).ready(() => {
                this.initializeFirebase();
                this.bindEvents();
            });
        }

        initializeFirebase() {
            try {
                // Initialize Firebase
                const firebaseConfig = {
                    apiKey: propellerFirebaseConfig.apiKey,
                    authDomain: propellerFirebaseConfig.authDomain,
                    projectId: propellerFirebaseConfig.projectId,
                    // appId: propellerFirebaseConfig.appId
                };

                this.app = firebase.initializeApp(firebaseConfig);
                this.auth = firebase.auth();

                // Set tenant ID if provided
                if (propellerFirebaseConfig.tenantId) {
                    this.auth.tenantId = propellerFirebaseConfig.tenantId;
                }

                console.log('Firebase initialized successfully');
            } catch (error) {
                console.error('Firebase initialization error:', error);
                this.showMessage('Firebase initialization failed: ' + error.message, 'danger');
            }
        }

        bindEvents() {
            $('#google-login-btn').on('click', () => this.signInWithGoogle());
            $('#microsoft-login-btn').on('click', () => this.signInWithMicrosoft());
            $('#okta-login-btn').on('click', () => this.signInWithOkta());
            $('#mocksaml-login-btn').on('click', () => this.signInWithMockSAML());
            $('#auth0-login-btn').on('click', () => this.signInWithAuth0());
        }

        async signInWithGoogle() {
            if (this.isLoading) return;
            
            try {
                this.setLoading(true, 'Connecting to Google...');
                
                const provider = new firebase.auth.GoogleAuthProvider();
                provider.addScope('openid');
                provider.addScope('profile');
                provider.addScope('email');
                provider.setCustomParameters({
                    prompt: 'select_account'
                });

                const result = await this.auth.signInWithPopup(provider);
                await this.handleAuthResult(result);
                
            } catch (error) {
                this.handleAuthError(error);
            } finally {
                this.setLoading(false);
            }
        }

        async signInWithMicrosoft() {
            if (this.isLoading) return;
            
            try {
                this.setLoading(true, 'Connecting to Microsoft...');
                
                const provider = new firebase.auth.OAuthProvider('microsoft.com');
                if (propellerFirebaseConfig.azureTenantId) {
                    provider.setCustomParameters({
                        tenant: propellerFirebaseConfig.azureTenantId,
                        prompt: 'select_account'
                    });
                }
                provider.addScope('openid');
                provider.addScope('profile');
                provider.addScope('email');

                const result = await this.auth.signInWithPopup(provider);
                await this.handleAuthResult(result);
                
            } catch (error) {
                this.handleAuthError(error);
            } finally {
                this.setLoading(false);
            }
        }

        async signInWithOkta() {
            if (this.isLoading) return;
            
            try {
                this.setLoading(true, 'Connecting to OKTA...');
                
                const provider = new firebase.auth.OAuthProvider('oidc.okta');
                if (propellerFirebaseConfig.oktaDomain) {
                    provider.setCustomParameters({
                        customDomain: `https://${propellerFirebaseConfig.oktaDomain}`,
                        prompt: 'select_account'
                    });
                }
                provider.addScope('openid');
                provider.addScope('profile');
                provider.addScope('email');

                const result = await this.auth.signInWithPopup(provider);
                await this.handleAuthResult(result);
                
            } catch (error) {
                this.handleAuthError(error);
            } finally {
                this.setLoading(false);
            }
        }

        async signInWithMockSAML() {
            if (this.isLoading) return;
            
            try {
                this.setLoading(true, 'Connecting to MockSAML...');
                
                const provider = new firebase.auth.SAMLAuthProvider('saml.saml-provider');

                const result = await this.auth.signInWithPopup(provider);
                await this.handleAuthResult(result);
                
            } catch (error) {
                this.handleAuthError(error);
            } finally {
                this.setLoading(false);
            }
        }

        async signInWithAuth0() {
            if (this.isLoading) return;
            
            try {
                this.setLoading(true, 'Connecting to Auth0...');
                
                // Auth0 would typically be handled differently as it's not a Firebase provider
                // This is a placeholder - you might need to implement Auth0 separately
                const provider = new firebase.auth.OAuthProvider(propellerFirebaseConfig.providerId);
                provider.addScope('openid');
                provider.addScope('profile');
                provider.addScope('email');

                const result = await this.auth.signInWithPopup(provider);
                await this.handleAuthResult(result);
                
            } catch (error) {
                this.handleAuthError(error);
            } finally {
                this.setLoading(false);
            }
        }

        async handleAuthResult(result) {
            if (!result.user) {
                throw new Error('No user returned from authentication');
            }

            this.setLoading(true, 'Configuring your account...');

            try {
                const idToken = await result.user.getIdToken();
                
                // Send authentication data to server
                const response = await $.ajax({
                    url: propellerFirebaseConfig.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'firebase_auth_callback',
                        nonce: propellerFirebaseConfig.nonce,
                        idToken: idToken,
                        email: result.user.email,
                        uid: result.user.uid,
                        displayName: result.user.displayName || '',
                    }
                });

                if (response.success) {
                    const refreshedToken = await result.user.getIdToken();
                    
                    // Send refreshed token to server
                    const storeTokenResponse = await $.ajax({
                        url: propellerFirebaseConfig.ajaxUrl,
                        type: 'POST',
                        data: {
                            action: 'firebase_store_token',
                            nonce: propellerFirebaseConfig.nonce,
                            refreshedToken: refreshedToken,
                            email: result.user.email,
                        }
                    });

                    if (storeTokenResponse.success) {
                        this.showMessage('Authentication successful! Redirecting...', 'success');
                    
                        // Redirect after successful authentication
                        setTimeout(() => {
                            window.location.href = storeTokenResponse.data.redirect || '/';
                        }, 1500);
                    } else {
                        throw new Error(storeTokenResponse.data.message || 'Server storing token failed');
                    }
                } else {
                    throw new Error(response.data.message || 'Server authentication failed');
                }

            } catch (error) {
                // If server authentication fails, sign out from Firebase
                await this.auth.signOut();
                throw error;
            }
        }

        handleAuthError(error) {
            console.error('Authentication error:', error);
            
            let message = 'Authentication failed';
            
            switch (error.code) {
                case 'auth/popup-blocked':
                    message = 'Popup was blocked by your browser. Please allow popups and try again.';
                    break;
                case 'auth/popup-closed-by-user':
                    message = 'Authentication popup was closed. Please try again.';
                    break;
                case 'auth/operation-not-allowed':
                    message = 'This sign-in provider is not enabled. Please contact support.';
                    break;
                case 'auth/unauthorized-domain':
                    message = 'This domain is not authorized for authentication.';
                    break;
                case 'auth/tenant-id-mismatch':
                    message = 'User does not belong to the specified tenant.';
                    break;
                default:
                    message = error.message || message;
            }
            
            this.showMessage(message, 'danger');
        }

        setLoading(loading, message = '') {
            this.isLoading = loading;
            
            if (loading) {
                $('#firebase-loading').show();
                if (message) {
                    $('#loading-message').text(message);
                }
                $('.sso-provider-btn').prop('disabled', true);
            } else {
                $('#firebase-loading').hide();
                $('.sso-provider-btn').prop('disabled', false);
            }
        }

        showMessage(message, type = 'info') {
            const alertClass = `alert-${type}`;
            const messageElement = $('#firebase-status-message');
            
            messageElement
                .removeClass('alert-success alert-danger alert-info alert-warning')
                .addClass(`alert ${alertClass}`)
                .text(message)
                .show();

            // Auto-hide success messages
            if (type === 'success') {
                setTimeout(() => {
                    messageElement.fadeOut();
                }, 5000);
            }
        }
    }

    // Initialize Firebase Auth when the page loads
    new PropellerFirebaseAuth();

})(jQuery);
