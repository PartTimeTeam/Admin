$("#checkCode").click(function(){
		var code = $("#code").val();
		var myRegEx  = /^([a-zA-Z0-9 _-]+)$/;
		var isValid = !(myRegEx.test(code));
		if( isValid == true && code !=''){
				$.ajax({
					  type: "POST",
					  url: url,
					  data: data,
					  success: success,
					  dataType: dataType
				});
		} else {
			window.location = '/download/page-not-found';
		}
});