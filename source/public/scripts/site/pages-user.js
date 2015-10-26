head.ready(function () {
	Namespace(siteConfig.namespace);
	Singleton(siteConfig.namespace + '.User', {
	    user: {},
	    __init__: function() {
	        var me = this;
	        var aoColumns = [
	    	                 {"data": "user_id"},
	    	                 { "data": "user_name" },
	    	                 { "data": "email" },
	    	                 { "data": "status" },
	    	                 { "data": "full_name" },
	    	                 { "data": "Action_Table"}
	    	];
	        var columnDefs = [
	  						{
	  						    "render": function (data, type, row) {
	  						    	return row["user_id"];
	  						    },
	  						    orderable: false,
	  						    targets: 0
	  						},
	  						{
	  						    "render": function (data, type, row) {
	  						        return row["user_name"];
	  						    },
	  						    orderable: false,
	  						    "targets": 1,
	  						    "data": "user_name"
	  						},  
	  						{
	  							"render": function ( data, type, row ) {
	  								return row["email"];
	  							},
	  							"targets": 2,
	  							orderable: false,
	  							"data": "email"
	  						},
	  						{
	  							"render": function ( data, type, row ) {
	  								return row["status"];
	  							},
	  							"targets": 3,
	  							orderable: false,
	  							"data": "status"
	  						},
	  						{
	  							"render": function ( data, type, row ) {
	  								return row["full_name"];
	  							},
	  							"targets": 4,
	  							orderable: false,
	  							"data": "full_name"
	  					   },
	  					 {
	  							"render": function ( data, type, row ) {
	  								var action = '';
	  								action += '<button type="button" onclick="window.location =\'/user/detail/id/'+row['user_id']+'\'" class="btn btn-primary" rel="tooltip" data-placement="top" title="Delete"><i class="fa fa-pencil-square-o"></i></button> &nbsp;';
	  								action += '<button type="button" onclick="PaymentAdmin.site.pages.User.deleteUser('+row['user_id']+')" class="btn btn-danger" rel="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></button>';
	  								return action;
	  							},
	  							"targets": 5,
	  							"orderable": false,
	  							"data": "Action_Table"
	  					   }
	  	    ];
	        XRace.ui.Widget.setupDataTable( "#userTable", "/user/list", aoColumns, columnDefs, {order:[[ 1, "desc" ]]});
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




