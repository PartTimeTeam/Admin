Namespace('XRace');

Singleton('XRace.String', {
	
	trim: function (str) {
		return str.replace(/^\s+|\s+$/gm, '');
	},
	
	add: function(str, newStr, index) {
	    index = !index ? str.length : index;
	    return str.slice(0, index) + newStr + str.slice(index);
	},
	
	format: function(str) {
	    var formatted = str;
	    for (var i = 1; i < arguments.length; i++) {
	        var regexp = new RegExp('\\{' + (i - 1) + '\\}', 'gi');
	        formatted = formatted.replace(regexp, arguments[i]);
	    }
	    return formatted;
	},
	
	isEmpty: function(str) {
		return !str || this.trim(str).length === 0;
	},
	
	removeNonNumeric: function(str) {
	    return str.replace(/[^0-9]/g, '');
	},
	
	createUUID: function() {
		return Lunex.Crypto.createUUID();
	},
	
	htmlEncode: function(html) {
		return document.createElement('a').appendChild( 
        	document.createTextNode(html)
        ).parentNode.innerHTML;
	},
	
	htmlDecode: function(html) {
		var a = document.createElement('a'); a.innerHTML = html;
    	return a.textContent;
	}
});