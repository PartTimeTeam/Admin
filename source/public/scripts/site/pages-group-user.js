head.ready(function () {
	Namespace(siteConfig.namespace);
	Singleton(siteConfig.namespace + '.GroupUser', {
	    group_user: {},
	    __init__: function() {
	        var me = this;
	        var aoColumns = [
	    	                 { "data": "group_id"},
	    	                 { "data": "group_name" },
	    	                 { "data": "user_create" },
	    	                 { "data": "user_name_leader" },
	    	                 { "data": "group_slogan" },
	    	                 { "data": "email_leader" },
	    	                 { "data": "status" },
	    	                 { "data": "phone" },
	    	                 { "data": "created_at" },
	    	                 { "data": "Action_Table"}
	    	];
	        var columnDefs = [
//                          {
//                        	  "render": function ( data, type, row ) {
//                        		  return XRace.helper.Money.format(data);
//                        	  },
//                        	"targets": 2,
//							"orderable": false,
//							"data": "amount"
//                          },
//                          {
//                          	"targets": 4,
//  							"orderable": false,
//  							"data": "payment_amount"
//                          },
//                          {
//                            	"targets": 5,
//    							"orderable": false,
//    							"data": "payment_fee"
//                          },
//                          {
//                              	"targets": 6,
//      							"orderable": false,
//      							"data": "payment_type_value"
//                          },
	  					 {
	  							"render": function ( data, type, row ) {
	  								var action = '';
	  								action += '<button type="button" onclick="window.location =\'/group-user/detail/id/'+row['group_id']+'\'" class="btn btn-primary" rel="tooltip" data-placement="top" title="Detail"><i class="fa fa-pencil-square-o"></i></button> &nbsp;';
	  								return action;
	  							},
	  							"targets": 9,
	  							"orderable": false,
	  							"data": "Action_Table"
	  					   }
	  	    ];
	        XRace.ui.Widget.setupDataTable( "#groupUserTable", "/group-user/list", aoColumns, columnDefs, {order:[[ 0, "desc" ]]});
	    },
	});
});




