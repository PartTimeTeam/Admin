Namespace('XRace.ui');

Singleton('XRace.ui.Helper', {
	
	getUiSelector: function(uiObjOrId) {
		var uiObj = null;
		if (XRace.isString(uiObjOrId)) {
            uiObj = $(uiObjOrId);
        } else {
            uiObj = uiObjOrId;
        }
        return uiObj;
	}
});