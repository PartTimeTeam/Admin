Namespace('XRace.helper');

Singleton('XRace.helper.Money', {
	format: function(data) {
		if (XRace.isDefined(data)) {
			return this.toLocaleString(parseFloat(data).toFixed(2));
		} else {
			return "0.00";
		}
	},
	toLocaleString: function(num) {
		var n = num.toString(), p = n.indexOf('.');
	    return n.replace(/\d(?=(?:\d{3})+(?:\.|$))/g, function($0, i){
	        return p<0 || i<p ? ($0+',') : $0;
	    });
	}
});