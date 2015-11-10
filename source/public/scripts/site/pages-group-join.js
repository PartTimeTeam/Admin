head.ready(function () {
	Namespace(siteConfig.namespace);
	Singleton(siteConfig.namespace + '.GroupJoin', {
	    group_join: {},
	    __init__: function() {
	        var me = this;
	        var aoColumns = [
	        				{ "data": "id"},
	    	                 { "data": "group_name"},
	    	                 { "data": "name" },
	    	                 { "data": "status" },
	    	                 { "data": "create_date" },
	    	                 { "data": "Action_Table"}
	    	];
	        var columnDefs = [
	  					 {
	  							"render": function ( data, type, row ) {
	  								var action = '';
	  								action += '<button type="button" onclick="window.location =\'/group-join/detail/id/'+row['id']+'\'" class="btn btn-primary" rel="tooltip" data-placement="top" title="Detail"><i class="fa fa-pencil-square-o"></i></button> &nbsp;';
	  								return action;
	  							},
	  							"targets": 5,
	  							"orderable": false,
	  							"data": "Action_Table"
	  					   }
	  	    ];
	        XRace.ui.Widget.setupDataTable( "#groupJoinTable", "/group-join/list", aoColumns, columnDefs, {order:[[ 0, "desc" ]]});
	    },
	});
});




