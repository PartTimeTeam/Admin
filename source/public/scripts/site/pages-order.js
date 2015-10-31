head.ready(function () {
	Namespace(siteConfig.namespace);
	Singleton(siteConfig.namespace + '.Order', {
	    product_group: {},
	    __init__: function() {
	        var me = this;
	        var aoColumns = [
	    	                 { "data": "order_id"},
	    	                 { "data": "order_name" },
	    	                 { "data": "amount" },
	    	                 { "data": "currency" },
	    	                 { "data": "payment_amount" },
	    	                 { "data": "payment_fee" },
	    	                 { "data": "payment_type_value" },
	    	                 { "data": "payment_status" },
	    	                 { "data": "created_at" },
	    	                 { "data": "email" },
	    	                 { "data": "Action_Table"}
	    	];
	        var columnDefs = [
                          {
                        	  "render": function ( data, type, row ) {
                        		  return XRace.helper.Money.format(data);
                        	  },
                        	"targets": 2,
							"orderable": false,
							"data": "amount"
                          },
	  					 {
	  							"render": function ( data, type, row ) {
	  								var action = '';
	  								action += '<button type="button" onclick="window.location =\'/product-group/detail/id/'+row['product_group_id']+'\'" class="btn btn-primary" rel="tooltip" data-placement="top" title="Delete"><i class="fa fa-pencil-square-o"></i></button> &nbsp;';
	  								return action;
	  							},
	  							"targets": 10,
	  							"orderable": false,
	  							"data": "Action_Table"
	  					   }
	  	    ];
	        XRace.ui.Widget.setupDataTable( "#orderTable", "/order-detail/list", aoColumns, columnDefs, {order:[[ 1, "desc" ]]});
	    },
	    /*
         * delete
         */
        deleteProductGroup: function( id ) {
            var me = this;
            XRace.helper.Modal.confirm( "Are you sure want to delete this product group", function(result){
                if (result) {
                    var options = {
                            success: function (data) {
                                if (data.Code > 0) {
                                	XRace.helper.Modal.alert("Delete product group successfully", function() {
                                            var t = $("#productGroupTable").DataTable();
                                            t.draw();
                                    });
                                } else {
                                	XRace.helper.Modal.alert("Delete product group fail");
                                }
                            },
                            error: function(data){
                            	XRace.helper.Modal.alert("Delete product group fail");
                            }   
                    };
                    var url = '/product-group/delete';
                    var data = {
                            id : id
                    };
                    XRace.Ajax.post(url, data, options);
                }
			});
		}
	});
});




