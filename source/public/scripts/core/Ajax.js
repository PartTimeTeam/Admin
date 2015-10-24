Namespace('XRace');

Singleton('XRace.Ajax', {

    __init__: function () {
        var me = this;
        head.ready(function () {
            me.setup();
        });
    },

    _baseUrl: '',

    _timeout: 45000,

    _beforeSuccessFunc: $.noop,

    _afterSuccessFunc: $.noop,

    _beforeErrorFunc: $.noop,

    _afterErrorFunc: $.noop,

    _queue: 0,
    
    _still: false,
    
    _silent: false,

    getBaseUrl: function () {
        return this._baseUrl;
    },

    setBaseUrl: function (baseUrl) {
        this._baseUrl = baseUrl;
    },

    setTimeout: function (timeout) {
        this._timeout = timeout;
    },

    setBeforeSuccessFunc: function (beforeSuccessFunc) {
        this._beforeSuccessFunc = beforeSuccessFunc;
    },

    setAfterSuccessFunc: function (afterSuccessFunc) {
        this._afterSuccessFunc = afterSuccessFunc;
    },

    setBeforeErrorFunc: function (beforeErrorFunc) {
        this._beforeErrorFunc = beforeErrorFunc;
    },

    setAfterErrorFunc: function (afterErrorFunc) {
        this._afterErrorFunc = afterErrorFunc;
    },

    setSilent: function (silent) {
        this._silent = silent;
    },
    
    setup: function () {
        var me = this;

        // Setup AJAX default
        $.ajaxSetup({
            dataType: 'json',
            timeout: me._timeout,
            complete: function(data){
    			//hide loading dialog after request complete
    			if (data && data.responseJSON && data.responseJSON.Code == "-999") {
    				//go to login page if session expire
    				window.location.href = "/";
    				return false;
    			}
    		}
        });

        // Setup AJAX Before FIRST Request
        $(document).ajaxSend(function (evt, xhr, opt) {
            if (opt.loadingContext ) {
                me._showLoadingMask(opt.loadingContext);
                return;
            }
            me._silent = opt.silent;
            if (me._queue === 0 && !opt.silent ) {
               me._showLoadingMask();
            }
            if( opt.silent_sp == undefined || opt.silent_sp == false ) {
            	me._queue++;
            }
            me._still = opt.keepLoading || false;
        });

        // Setup AJAX After LAST Request
        $(document).ajaxComplete(function (evt, xhr, opt) {
            if (opt.loadingContext) {
                me._hideLoadingMask(opt.loadingContext);
                return;
            }

            if (me._queue === 1 && !me._still) 
            {
                me._hideLoadingMask();
            }
            
            if( opt.silent_sp == undefined || opt.silent_sp == false ) {
            	me._queue--;
            }            
        });
    },

    get: function (url, data, options) {
        return this.request(url, 'GET', data, options);
    },

    post: function (url, data, options) {
        return this.request(url, 'POST', data, options);
    },

    request: function (url, type, data, options) {
        var me = this;

        // Append request URL to base URL
        url = XRace.String.format('{0}/{1}', me._baseUrl, url);
        // Clean up URL, remove dupe splashes
        url = url.replace(/\/+/g, '/').replace(/\:\//, '://');

        // Request options
        var reqOptions = {
            url: url,
            global: true,
            async: true,
            type: type,
            data: data || {}
        };
        // Merge with request options
        options = $.extend({}, reqOptions, options);
        
        // Add custom success/error functions
        // only if options.global = true 
        if (options.global) {
            // Alter user-defined success/error functions
            options._success = options.success;
            options._error = options.error;

            // Inject before/after success functions
            options.success = function () {
                var preventSuccess = false;
                if (me._beforeSuccessFunc) {
                    var _beforeSuccessReturn = me._beforeSuccessFunc.apply(this, arguments);
                    preventSuccess = false;
                }
                if (preventSuccess) return;
                if (options._success) {
                    options._success.apply(this, arguments);
                }
                if (me._afterSuccessFunc) {
                    me._afterSuccessFunc.apply(this, arguments);
                }
            };
            // Inject before/after error functions
            options._error = function () {
                var preventError = false;
                if (me._beforeErrorFunc) {
                    var _beforeErrorReturn = me._beforeErrorFunc.apply(this, arguments);
                    preventError = XRace.isDefined(_beforeErrorReturn) ? !_beforeErrorReturn : false;
                }
                if (preventError) return;
                if (options._error) {
                    options._error.apply(this, arguments);
                }
                if (me._afterErrorFunc) {
                    me._afterErrorFunc.apply(this, arguments);
                }
            };
        }

        // Call the low-level function
        return $.ajax(options);
    },

    _showLoadingMask: function (contextId) {
    	{
    	}
    },

    _hideLoadingMask: function (contextId) {
    	{
    	}
    },
});