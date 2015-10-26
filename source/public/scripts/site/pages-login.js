head.ready(function () {
	Namespace(siteConfig.namespace);

	Singleton(siteConfig.namespace + '.Login', {
	    product: {},
	    __init__: function() {
	        var me = this;
	        $("#changeCaptcha").click(function(){
	        	me.changeCaptCha();
	        });
	       
	    },
	    changeCaptCha: function(){
	    	var options = {
                    success: function(data) {
                    	if (data.Code > 0) {
				    	}
                    },
                   
            };
    		var url = '/' + 'sign-up/capt-cha';
    		var data = {
    		};
    		Lunex.Ajax.post(url, data, options);
	    }
	});
});



