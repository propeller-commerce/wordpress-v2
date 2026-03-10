/**
 * jQuery Cookie Plugin Compatibility Layer
 * Provides $.cookie() compatibility using js-cookie library
 * 
 * This file bridges the gap between the old jQuery Cookie Plugin API
 * and the modern js-cookie library for backward compatibility.
 */

(function($) {
    'use strict';
    
    // Check if js-cookie is loaded
    if (typeof Cookies === 'undefined') {
        console.error('js-cookie library is required for jQuery Cookie compatibility');
        return;
    }
    
    // jQuery Cookie Plugin compatibility
    $.cookie = function(key, value, options) {
        // If no arguments provided, return all cookies
        if (arguments.length === 0) {
            return Cookies.get();
        }
        
        // If only key provided, get cookie value
        if (arguments.length === 1) {
            return Cookies.get(key);
        }
        
        // If value is null or undefined, remove the cookie
        if (value === null || value === undefined) {
            Cookies.remove(key, options);
            return;
        }
        
        // Set cookie with value and options
        var cookieOptions = {};
        
        if (options) {
            // Convert jQuery Cookie Plugin options to js-cookie format
            if (options.expires) {
                if (typeof options.expires === 'number') {
                    // Number of days
                    cookieOptions.expires = options.expires;
                } else if (options.expires instanceof Date) {
                    // Date object
                    cookieOptions.expires = options.expires;
                }
            }
            
            if (options.path) {
                cookieOptions.path = options.path;
            }
            
            if (options.domain) {
                cookieOptions.domain = options.domain;
            }
            
            if (options.secure) {
                cookieOptions.secure = options.secure;
            }
            
            if (options.sameSite) {
                cookieOptions.sameSite = options.sameSite;
            }
        }
        
        Cookies.set(key, value, cookieOptions);
        return value;
    };
    
    // Additional jQuery Cookie Plugin methods for compatibility
    $.removeCookie = function(key, options) {
        Cookies.remove(key, options);
        return !Cookies.get(key);
    };
    
})(jQuery);
