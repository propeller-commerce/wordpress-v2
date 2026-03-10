'use strict';

// Global object
window.Propeller || (window.Propeller = {});
// Detect Internet Explorer
window.Propeller.isIE = navigator.userAgent.indexOf("Trident") >= 0;
// Detect Edge
window.Propeller.isEdge = navigator.userAgent.indexOf("Edge") >= 0;
// Detect Mobile
window.Propeller.isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
// Tax codes
window.Propeller.TaxCodes = {
    H: 21,
    L: 9,
    N: 0
};
window.Propeller.product_container = '#propeller-product-list';

(function ($, window, document) {

    // Helper functions and extensions
    $.fn.serializeObject = function () {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function () {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };

    $.fn.serializeURLParams = function() {
        var result = {};
    
        if( !this.is("a") || this.attr("href").indexOf("?") == -1 ) 
            return( result );
    
        var pairs = this.attr("href").split("?")[1].split('&');
        pairs.forEach(function(pair) {
            pair = pair.split('=');
            var name = decodeURI(pair[0])
            var value = decodeURI(pair[1])
            if( name.length )
                if (result[name] !== undefined) {
                    if (!result[name].push) {
                        result[name] = [result[name]];
                    }
                    result[name].push(value || '');
                } else {
                    result[name] = value || '';
                }
        });
        return( result )
    }


    Propeller.Global = {
        urlencoded_regex: /%[0-9A-F]{2}/g,
        maxSuggestions: 6,
        searchSuggestionTemplate: '<div class="beer-card">' +
            '<div class="beer-card__image">' +
            '<img src="/assets/jquerytypeahead/img/beer_v2/{{group}}/{{display|raw|slugify}}.jpg">' +
            '</div>' +
            '<div class="beer-card__name">{{display}}</div>' +
            '</div>',
        init: function () {
            this.get_nonce();
            this.convertLocalDates();
        },
        scrollTo: function (target) {
            $('html, body').stop().animate({
                'scrollTop': $(target).offset().top
            }, 500, 'swing');
        },
        changeAjaxPage: function (data, title, url) {
            if (window.history.pushState)
                window.history.pushState(data, title, url);
            else
                window.location.href = url;
        },

        formatPrice: function (price) {
            return Number(parseFloat(price).toFixed(2)).toLocaleString('nl', {
                minimumFractionDigits: 2
            });
        },
        getPercentage: function (percent, total) {
            return (percent / 100) * total;
        },
        parseQuery: function(queryString) {
            var query = {};

            var pairs = (queryString[0] === '?' ? queryString.substr(1) : queryString).split('&');

            if (pairs != '') {
                for (var i = 0; i < pairs.length; i++) {
                    var pair = pairs[i].split('=');
                    if(Array.isArray(pair) && pair.length > 1) {
                        if (Propeller.Global.preg_match(Propeller.Global.urlencoded_regex, pair[1]) !== false) {
                            if (pair[0].includes('[]')) {
                                if (typeof query[pair[0]] == 'undefined')
                                    query[pair[0]] = [];

                                query[pair[0]].push(decodeURIComponent(pair[1]));
                            }
                            else 
                                query[pair[0]] = decodeURIComponent(pair[1]);
                        } else {
                            if (pair[0].includes('[]')) {
                                if (typeof query[pair[0]] == 'undefined')
                                    query[pair[0]] = [];

                                query[pair[0]].push(pair[1]);
                            }
                            else 
                                query[pair[0]] = pair[1];
                        }                        
                    }
                }
            }

            return query;
        },
        buildQuery: function(qryObj) {
            var aString = [];

            for (const key in qryObj) {
                if (qryObj.hasOwnProperty(key)) {

                    if (typeof qryObj[key] == 'object') {
                        for (var i = 0; i < qryObj[key].length; i++) {

                            if (Propeller.Global.preg_match(Propeller.Global.urlencoded_regex, qryObj[key][i]) === false)
                                aString.push(`${key}=${encodeURIComponent(qryObj[key][i])}`);
                            else
                                aString.push(`${key}=${qryObj[key][i]}`);
                        }                        
                    }
                    else if (typeof qryObj[key] == 'string'){
                        if (Propeller.Global.preg_match(Propeller.Global.urlencoded_regex, qryObj[key]) === false && !qryObj[key].includes('['))
                            aString.push(`${key}=${encodeURIComponent(qryObj[key])}`);
                        else
                            aString.push(`${key}=${qryObj[key]}`);
                    }
                    else if (typeof qryObj[key] == 'number'){
                        if (Propeller.Global.preg_match(Propeller.Global.urlencoded_regex, qryObj[key]))
                            aString.push(`${key}=${encodeURIComponent(qryObj[key])}`);
                        else
                            aString.push(`${key}=${qryObj[key]}`);
                    }
                }
            }

            return aString.join('&');
        },
        preg_match: function(regex, str) {
            if (new RegExp(regex).test(str))
                return regex.exec(str);
            
            return false;
        },
        merge_custom: function(original_js, custom_js) {
            for (const key in custom_js) {
                delete original_js[key];

                original_js[key] = custom_js[key];
            }
            
            return original_js;
        },
        formatLocalDate: function(utcDateString) {
            var d = new Date(utcDateString);
            if (isNaN(d.getTime())) return utcDateString;
            var day = ('0' + d.getDate()).slice(-2);
            var month = ('0' + (d.getMonth() + 1)).slice(-2);
            var year = d.getFullYear();
            return day + '-' + month + '-' + year;
        },
        convertLocalDates: function() {
            $('.local-date[data-utc]').each(function() {
                var utc = $(this).data('utc');
                if (utc) $(this).text(Propeller.Global.formatLocalDate(utc));
            });
        },
        get_nonce: function() {
            $.ajax({
                url: PropellerHelper.ajax_url,
                method: 'POST',
                data: {
                    action: 'propeller_get_nonce'
                },
                success: function(response) {
                    if (response.success) {
                        window.newnonce = response.data.nonce;
                    }
                }
            });
        }
    }

    Propeller.Cookie = {
        get: function (sKey) {
            return decodeURIComponent(document.cookie.replace(new RegExp("(?:(?:^|.*;)\\s*" + encodeURIComponent(sKey).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=\\s*([^;]*).*$)|^.*$"), "$1")) || null;
        },
        set: function (sKey, sValue, vEnd, sPath = '/', sDomain = '', bSecure = true) {
            if (!sKey || /^(?:expires|max\-age|path|domain|secure)$/i.test(sKey)) { return false; }

            var sExpires = "";

            var date = new Date();
            date.setTime(date.getTime() + (vEnd * 24 * 60 * 60 * 1000));
            sExpires = "; expires=" + date.toUTCString();
            
            // if (vEnd) {
            //     switch (vEnd.constructor) {
            //         case Number:
            //             sExpires = vEnd === Infinity ? "; expires=Fri, 31 Dec 9999 23:59:59 GMT" : "; max-age=" + vEnd;
            //             break;
            //         case String:
            //             sExpires = "; expires=" + vEnd;
            //             break;
            //         case Date:
            //             sExpires = "; expires=" + vEnd.toUTCString();
            //             break;
            //     }
            // }

            document.cookie = encodeURIComponent(sKey) + "=" + encodeURIComponent(sValue) + sExpires + (sDomain ? "; domain=" + sDomain : "") + (sPath ? "; path=" + sPath : "") + (bSecure ? "; secure" : "");
            
            return true;
        },
        remove: function (sKey, sPath, sDomain) {
            if (!sKey || !this.hasItem(sKey)) 
                return false; 

            document.cookie = encodeURIComponent(sKey) + "=; expires=Thu, 01 Jan 1970 00:00:00 GMT" + ( sDomain ? "; domain=" + sDomain : "") + ( sPath ? "; path=" + sPath : "");
            
            return true;
        },
        has: function (sKey) {
            return (new RegExp("(?:^|;\\s*)" + encodeURIComponent(sKey).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=")).test(document.cookie);
        }
    }

    $(function() {
        Propeller.Global.convertLocalDates();
    });

}(window.jQuery, window, document))