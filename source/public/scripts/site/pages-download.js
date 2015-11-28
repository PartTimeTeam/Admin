$("#checkCode").click(function(){
		var code = $("#code").val();
		var urlshare = $("#urlShare").val();
		if( code != "" && code.length > 0 && urlshare != "" && urlshare.length > 0 ){
			$.ajax({
	    	    type: "POST",
	            url: '/download/check-code-input',
	            data: { Code: code, UrlShare: urlshare},
	            success: function (data) {
	            	if(isDefined(data.Code) && data.Code == 1){
	            		 $("form").submit();//
	            	} else {
	            		alert('Wrong unlock code. Please try again.');
	            	}
	                
	            },
	            cache: false
	        });
		} else {
			alert('Please enter unlock code!');
		}
});
function isDefined(obj) {
	return typeof obj !== 'undefined' && obj !== null && obj !== undefined;
}
