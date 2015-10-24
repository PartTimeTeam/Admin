head.ready(function () {
	Namespace(siteConfig.namespace);

	Singleton(siteConfig.namespace + '.User', {
	    group: {},
	    __init__: function() {
	        var me = this;
	        console.log(siteConfig.namespace);
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
	  								return "";
	  							},
	  							"targets": 5,
	  							orderable: false,
	  							"data": "Action_Table"
	  					   }
	  	    ];
	        XRace.ui.Widget.setupDataTable( "#userTable", "/user/list", aoColumns, columnDefs, {order:[[ 1, "desc" ]]});
	    },
	});
});



