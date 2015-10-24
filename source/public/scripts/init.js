 /* CONFIG LOADING */
/* ------------------------------------------------------------------------------ */

var siteConfig = {};
var GlobalNameSpace = null;
var Translations = {};

// Set global namespace
function getNamespace(){
    GlobalNameSpace = eval(siteConfig.namespace);
    return GlobalNameSpace;
}

function loadJSClass( clss, url, callBack ) {
    var clssObj = eval(siteConfig.namespace+'.'+ clss );
    if( !clssObj ) {
      if( callBack && typeof( callBack ) == 'function' ) {  
        $.getScript( url, callBack );
      } else {
         $.getScript( url ); 
      }
    } else if( callBack && typeof( callBack ) == 'function' ) {
        callBack();
    }
}
// Get translation
function translate(text, group) {
	if ( Lunex.isDefined(group) ) {
		if ( group[text] ) {
                    return group[text];
                }else if( GlobalNameSpace.Translation.Translations[text] ){
                    return GlobalNameSpace.Translation.Translations[text];
                } else {
                    return "Error! This text '" + group + "." + text + "' have not translated.";
                }
	} else {
		if ( GlobalNameSpace.Translation.Translations[text] ) {
	        return GlobalNameSpace.Translation.Translations[text];
	    } else {
	        return "Error! This text '" + text + "' have not translated.";
	    }
	}
}
// Check if array is empty
function isArrayEmpty( obj ){
	var count = 0;
	for( var i in obj ) {
		count++;
		if( count > 0 ) {
			return false;
		}
	}
	return true;
}
/* CSS LOADING */
/* ------------------------------------------------------------------------------ */

head.load(
	'/resources/css/jquery.dataTables.min.css',		
    '/libs/jqvld/jquery.validationEngine.css',
    '/resources/css/bootstrap-datetimepicker.min.css',
    '/libs/select2/select2.css',
    '/libs/select2/select2-bootstrap.css',
	'/resources/css/bootstrap-multiselect.css',
	'/resources/css/prettify.css',
	'/resources/css/site.css'
);

/* JS LOADING */
/* ------------------------------------------------------------------------------ */

head.load(
	// Libraries
	'/libs/jquery.dataTables.min.js',
	'/libs/jquery.cookie.js',
	'/libs/bootstrap-formhelpers.min.js',
    '/libs/bootstrap-datetimepicker.min.js',
    '/libs/bootbox.min.js',
    '/libs/select2/select2.min.js',
    '/libs/autoNumeric-1.9.35.js',
    '/libs/jqvld/jquery.validationEngine.js',
	'/libs/bootstrap-multiselect.js',
	'/libs/prettify.js',
	'/scripts/site.js',
	'/scripts/core/Core.js',
	'/scripts/core/String.js',
    '/scripts/core/Ajax.js',
	'/scripts/helper/Modal.js',
	'/scripts/ui/Helper.js',
	'/scripts/ui/Widget.js'
);
if ( Language == "es" ) {
	head.load(
	    '/libs/locales/bootstrap-datetimepicker.es.js',
	    '/libs/select2/select2_locale_es.js'
	);
}
/* SHOW PAGE AFTER ALL READY */
/* ------------------------------------------------------------------------------ */

head.ready(function() {
	$(document.body).fadeIn(400);
});