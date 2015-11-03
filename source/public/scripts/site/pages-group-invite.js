head.ready(function () {
	Namespace(siteConfig.namespace);
	Singleton(siteConfig.namespace + '.GroupInvite', {
	    group_invite: {},
	    __init__: function() {
	        var me = this;
	        var aoColumns = [
	        				{ "data": "id"},
	    	                 { "data": "group_invite"},
	    	                 { "data": "user_invite" },
	    	                 { "data": "status" },
	    	                 { "data": "create_date" },
	    	                 { "data": "Action_Table"}
	    	];
	        var columnDefs = [
	  					 {
	  							"render": function ( data, type, row ) {
	  								var action = '';
	  								action += '<button type="button" onclick="window.location =\'/group-invite/detail/id/'+row['id']+'\'" class="btn btn-primary" rel="tooltip" data-placement="top" title="Detail"><i class="fa fa-pencil-square-o"></i></button> &nbsp;';
	  								return action;
	  							},
	  							"targets": 5,
	  							"orderable": false,
	  							"data": "Action_Table"
	  					   }
	  	    ];
	        XRace.ui.Widget.setupDataTable( "#groupInviteTable", "/group-invite/list", aoColumns, columnDefs, {order:[[ 0, "desc" ]]});
	    },
	});
});




