Namespace('XRace.ui');

Singleton('XRace.ui.Helper', {
	
	getUiSelector: function(uiObjOrId) {
		var uiObj = null;
		if (Lunex.isString(uiObjOrId)) {
            uiObj = $(uiObjOrId);
        } else {
            uiObj = uiObjOrId;
        }
        return uiObj;
	}
});