head.ready(function () {
	Namespace(siteConfig.namespace);
	Singleton(siteConfig.namespace + '.Question', {
	    user: {},
	    __init__: function() {
	        var me = this;
	        var aoColumns = [
	    	                 {"data": "id"},
	    	                 { "data": "content" },
	    	                 { "data": "type" },
	    	                 { "data": "answer" },
	    	                 { "data": "status" },
	    	                 { "data": "created-by" },
	    	                 { "data": "Action_Table"}
	    	];
	        var columnDefs = [
	  						
	  					 {
	  							"render": function ( data, type, row ) {
	  								return '<button type="button" onclick="PaymentAdmin.site.pages.Question.deleteQuestion('+row['id']+')" class="btn btn-danger" rel="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></button>';
	  							},
	  							"targets": 8,
	  							"orderable": false,
	  							"data": "Action_Table"
	  					   }
	  	    ];
	        XRace.ui.Widget.setupDataTable( "#questionTable", "/question/list", aoColumns, columnDefs, {order:[[ 0, "desc" ]]});
	    },
	    /*
         * delete user
         */
        deleteUser: function( id ) {
            var me = this;
            XRace.helper.Modal.confirm( "Are you sure want to delete this user", function(result){
                if (result) {
                    var options = {
                            success: function (data) {
                                if (data.Code > 0) {
                                	XRace.helper.Modal.alert("Delete user successfully", function() {
                                            var t = $("#userTable").DataTable();
                                            t.draw();
                                    });
                                } else {
                                	XRace.helper.Modal.alert("Delete user fail");
                                }
                            },
                            error: function(data){
                            	XRace.helper.Modal.alert("Delete user fail");
                            }   
                    };
                    var url = '/user/delete';
                    var data = {
                            id : id
                    };
                    XRace.Ajax.post(url, data, options);
                }
			});
		}
	});
});



