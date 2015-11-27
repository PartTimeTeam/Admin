$("#checkCode").click(function(){
		var code = $("#code").val();
		if( code != "" && code.length > 0 ){
			$.ajax({
	    	    type: "POST",
	            url: '/download/check-code-input',
	            data: { Code: code },
	            success: function (data) {
	            	if(isDefined(data.Code) && data.Code == 1){
	            		 $("form").submit();//
	            	} else {
	            		alert('Code is not available!');
	            	}
	                
	            },
	            cache: false
	        });
		} else {
			alert('Invalid Code');
		}
});
function isDefined(obj) {
	return typeof obj !== 'undefined' && obj !== null && obj !== undefined;
}
