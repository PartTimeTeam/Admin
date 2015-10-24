Namespace('XRace.helper');

Singleton('XRace.helper.Modal', {
	//show loading dialog
    showLoadingDialog: function() {
    	$('#dialog-loading').modal("show");
    	this.centerModal($('#dialog-loading'));
    },
    //hide loading dialog
    hideLoadingDialog: function() {
    	$('#dialog-loading').modal("hide");
    },
    //Move modal to center
    centerModal: function( dialog ) {
    	$(dialog).css('display', 'block');
    	var $dialog = $(dialog).find(".modal-dialog");
    	var offset = ($(window).height() - $dialog.height()) / 2;
    	$dialog.css("margin-top", offset);
    },
    //Wrapper alert dialog of bootbox
    alert: function( message, callback, opts ) {
    	bootbox.dialog({
    		message: message,
    		buttons: {
    			main: {
    				label: translate('ok'),
    				className: "btn-primary",
    				callback: function() {
    					if (callback && typeof(callback) === 'function') {			
    						var args = [].splice.call(arguments, 0);
	    					callback.apply(callback, args);
    					}
    				}
    			}
    		}
    	});
    },
    //Wrapper confirm dialog of bootbox
    confirm: function( message, callback, opts ) {
    	bootbox.dialog({
    		message: message,
    		buttons: {
    			danger: {
    				label: translate('cancel'),
    				className: "",
    				callback: function() {
    				}
    			},
    			main: {
    				label: translate('ok'),
    				className: "btn-primary",
    				callback: function() {
    					if (callback && typeof(callback) === 'function') {			
    						var args = [].splice.call(arguments, 0);
	    					callback.apply(callback, args);
    					}
    				}
    			}
    		}
    	});
    }
    // show image view 
    
});