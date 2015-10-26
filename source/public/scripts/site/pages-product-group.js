head.ready(function () {
	Namespace(siteConfig.namespace);
	Singleton(siteConfig.namespace + '.ProductGroup', {
	    product_group: {},
	    __init__: function() {
	        var me = this;
	        $('.product_rating, .status, .duration, .product_min, .product_max, .member_min, .rule_id, .product_group_type_id').keypress(function(event) {
	            if(event.which < 46
	            || event.which > 59) {
	                event.preventDefault();
	            } // prevent if not number/dot

	            if(event.which == 46
	            && $(this).val().indexOf('.') != -1) {
	                event.preventDefault();
	            } // prevent if already dot
	        });
	        var aoColumns = [
	    	                 { "data": "product_group_id"},
	    	                 { "data": "product_name" },
	    	                 { "data": "product_rating" },
	    	                 { "data": "status" },
	    	                 { "data": "city" },
	    	                 { "data": "duration" },
	    	                 { "data": "difficulty" },
	    	                 { "data": "local_knowledge" },
	    	                 { "data": "created_at" },
	    	                 { "data": "member_min" },
	    	                 { "data": "member_max" },
	    	                 { "data": "gold" },
	    	                 { "data": "rule_id" },
	    	                 { "data": "Action_Table"}
	    	];
	        var columnDefs = [
	  					 {
	  							"render": function ( data, type, row ) {
	  								var action = '';
	  								action += '<button type="button" onclick="window.location =\'/product-group/detail/id/'+row['product_group_id']+'\'" class="btn btn-primary" rel="tooltip" data-placement="top" title="Delete"><i class="fa fa-pencil-square-o"></i></button> &nbsp;';
	  								action += '<button type="button" onclick="PaymentAdmin.site.pages.ProductGroup.deleteProductGroup('+row['product_group_id']+')" class="btn btn-danger" rel="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></button>';
	  								return action;
	  							},
	  							"targets": 13,
	  							"orderable": false,
	  							"data": "Action_Table"
	  					   }
	  	    ];
	        XRace.ui.Widget.setupDataTable( "#productGroupTable", "/product-group/list", aoColumns, columnDefs, {order:[[ 1, "desc" ]]});
	    },
	    /*
         * delete
         */
        deleteProductGroup: function( id ) {
        	console.log(id);
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




