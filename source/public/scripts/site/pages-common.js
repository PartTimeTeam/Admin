/**
 * pages-common.js  
 * Description:
 * @copyright 2014-05-12
 * V001: phucdang - 2014-05-12
 */
head.ready(function () {
Namespace(siteConfig.namespace);

Singleton(siteConfig.namespace + '.Common', {
    autoLogoutHandle: '',
    expiresCookies: 7,
    //init
    __init__: function () {
        var me = this;
        // setup default config when init page 
    },
    reloadCurrentUrl: function() {
    	//get current url
    	getUrl = window.location.href;
    	var num = getUrl.substring(getUrl.length-1,getUrl.length);
    	if (num == '#') {
    		//remove # character
    		getUrl	= getUrl.substring(0,getUrl.length-1);
    	}
    	window.location = getUrl;
    },
  //change language
    loadLanguage: function (langCode) {
    	var me = this;
        //send request
        var options = {
            success: function (data) {
                //reload current page
               me.reloadCurrentUrl();
            }
        };
        var url = "/common/language";
        XRace.Ajax.get(url, {langCode: langCode}, options);
    },
});

});

