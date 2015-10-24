Singleton('XRace', {
	
	encode: function(obj) {
		try {
			return JSON.stringify(obj);
		}
		catch (e) {
			return undefined;
		}
	},
	
	decode: function(str) {		
		try {
			return JSON.parse(str);
		}
		catch (e) {
			return undefined;
		}
	},
	
	parseBoolean: function(str) {
		switch (String(str).toLowerCase()) {
			case 'true' :
			case '1' :
			case 'yes' :
			case 'y' :
				return true;
			case 'false' :
			case '0' :
			case 'no' :
			case 'n' :
				return false;
			default :
				// you could throw an error, but 'undefined' seems a
				// more logical reply
				return undefined;
		}
	},
	
	defer: function (fn, delay) {
        window.setTimeout(fn, delay);
    },
	
	isDefined: function(obj) {
		return typeof obj !== 'undefined' && obj !== null && obj !== undefined;
	},
	
	isString: function(obj) {
		return typeof obj == 'string';
	},
	
	isNumber: function(obj) {
		return !isNaN(obj) && isFinite(parseFloat(obj));
	},
	
	isBoolean: function(obj) {
		return typeof value === 'boolean';
	},
	
	isDate: function(obj) {
		return Object.prototype.toString.call(obj) === '[object Date]';
	},
	
	isObject: function(obj) {
		return Object.prototype.toString.call(obj) === '[object Object]';
	},
	
	isArray: ('isArray' in Array) ? Array.isArray: function(obj) {
		return Object.prototype.toString.call(obj) === '[object Array]';
	},
	
	isFunction: function(obj) {
		return typeof(obj) === 'function';
	}
});

(function($) {
    // Attrs
    $.fn.attrs = function(attrs) {
        var t = $(this);
        if (attrs) {
            // Set attributes
            t.each(function(i, e) {
                var j = $(e);
                for (var attr in attrs) {
                    j.attr(attr, attrs[attr]);
                };
            });
            return t;
        } else {
            // Get attributes
            var a = {},
                r = t.get(0);
            if (r) {
                r = r.attributes;
                for (var i in r) {
                    var p = r[i];
                    if (typeof p.nodeValue !== 'undefined') a[p.nodeName] = p.nodeValue;
                }
            }
            return a;
        }
    };
    
    var special = $.event.special,
        uid1 = 'D' + (+new Date()),
        uid2 = 'D' + (+new Date() + 1);

    special.scrollstart = {
        setup: function() {

            var timer,
                handler =  function(evt) {

                    var _self = this,
                        _args = arguments;

                    if (timer) {
                        clearTimeout(timer);
                    } else {
                        evt.type = 'scrollstart';
                        $.event.handle.apply(_self, _args);
                    }

                    timer = setTimeout( function(){
                        timer = null;
                    }, special.scrollstop.latency);

                };

            $(this).bind('scroll', handler).data(uid1, handler);

        },
        teardown: function(){
            $(this).unbind( 'scroll', $(this).data(uid1) );
        }
    };

    special.scrollstop = {
        latency: 300,
        setup: function() {

            var timer,
                handler = function(evt) {

                    var _self = this,
                        _args = arguments;

                    if (timer) {
                        clearTimeout(timer);
                    }

                    timer = setTimeout( function(){

                        timer = null;
                        evt.type = 'scrollstop';
                        $.event.handle.apply(_self, _args);

                    }, special.scrollstop.latency);

                };

            $(this).bind('scroll', handler).data(uid2, handler);

        },
        teardown: function() {
            $(this).unbind( 'scroll', $(this).data(uid2) );
        }
    };
    
    $.fn.doubletap = function(fn) {
        return fn ? this.bind('doubletap', fn) : this.trigger('doubletap');
    };

    special.doubletap = {
        setup: function(data, namespaces){
            $(this).bind('touchend', special.doubletap.handler);
        },

        teardown: function(namespaces){
            $(this).unbind('touchend', special.doubletap.handler);
        },

        handler: function(event){
            var action;

            clearTimeout(action);

            var now       = new Date().getTime(),
            	//the first time this will make delta a negative number
            	lastTouch = $(this).data('lastTouch') || now + 1,
            	delta     = now - lastTouch,
            	delay     = delay == null ? 500 : delay;

            if(delta < delay && delta > 0) {
                // After we detct a doubletap, start over
                $(this).data('lastTouch', null);

                // set event type to 'doubletap'
                event.type = 'doubletap';

                // let jQuery handle the triggering of "doubletap" event handlers
                $.event.handle.apply(this, arguments);
            }
            else {
                $(this).data('lastTouch', now);

                action = setTimeout(function(evt){
                    // set event type to 'doubletap'
                    event.type = 'tap';

                    // let jQuery handle the triggering of "doubletap" event handlers
                    $.event.handle.apply(this, arguments);

                    clearTimeout(action); // clear the timeout
                }, delay, [event]);
            }
        }
    };
})(jQuery);