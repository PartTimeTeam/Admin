head.ready(function () {
	Namespace(siteConfig.namespace);
	Singleton(siteConfig.namespace + '.Member', {
	    member: {},
	    __init__: function() {
	        var me = this;
	        var aoColumns = [
	    	                 { "data": "user_id"},
	    	                 { "data": "user_name" },
	    	                 { "data": "email" },
	    	                 { "data": "gender" },
	    	                 { "data": "birthday" },
	    	                 { "data": "status" },
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
	  								action += '<button type="button" onclick="window.location =\'/member/detail/id/'+row['user_id']+'\'" class="btn btn-primary" rel="tooltip" data-placement="top" title="Detail"><i class="fa fa-pencil-square-o"></i></button> &nbsp;';
	  								return action;
	  							},
	  							"targets": 7,
	  							"orderable": false,
	  							"data": "Action_Table"
	  					   }
	  	    ];
	        XRace.ui.Widget.setupDataTable( "#memberTable", "/member/list", aoColumns, columnDefs, {order:[[ 0, "desc" ]]});
	    },
	});
});




