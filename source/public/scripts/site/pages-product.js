head.ready(function () {
	Namespace(siteConfig.namespace);

	Singleton(siteConfig.namespace + '.Product', {
	    product: {},
	    __init__: function() {
	        var me = this;
	        // event click submit upload
	        $(document).on("click", "#upload", {}, function () {
	        	XRace.helper.Modal.showLoadingDialog();
	        });
	        // add choice
            $(document).on("click", '#btnAddImage', {}, function () {
                me.addImage();
            });
            // remove choice
            $(document).on("click", '.btnDelete', {}, function () {
                var dataParent = $(this).attr('data-parent');
                me.removeImage( dataParent );
            });
            
            // setup datatable
            var aoColumns = [
	    	                 {"data": "id"},
	    	                 { "data": "name" },
	    	                 { "data": "Action_Table"}
	    	];
	        var columnDefs = [
	  						{
	  						    "render": function (data, type, row) {
	  						    	return row["id"];
	  						    },
	  						    "orderable": false,
	  						    "targets": 0
	  						},
	  						{
	  						    "render": function (data, type, row) {
	  						        return row["name"];
	  						    },
	  						    "orderable": false,
	  						    "targets": 1,
	  						    "data": "name"
	  						},  
	  						{
	  							"render": function ( data, type, row ) {
	  								return '<button type="button" onclick="PaymentAdmin.site.pages.Product.deleteProduct('+row['id']+')" class="btn btn-danger" rel="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></button>';
	  							},
	  							"targets": 2,
	  							"orderable": false,
	  							"data": "Action_Table"
	  					   }
	  	    ];
	        XRace.ui.Widget.setupDataTable( "#productTable", "/product/list", aoColumns, columnDefs, {order:[[ 1, "desc" ]]});
	    },
	    // add input image
	    addImage: function(){
	    	var inputImg = $('.Image');
	    	if ( inputImg.length != 4 ){
	    		var divParent = $('.divParent');
	            var dataIndex = $('#count-image').val(); 
	            if ( inputImg.length == 1 ){
	            	 var index = inputImg.attr('data-index');
	                 $('.btnDel_'+index).removeClass('hidden');
	            }
	            var input = '<div class="col-md-12 divImg_'+dataIndex+'"><div class="col-md-3"><input type="file" name="product'+dataIndex+'" class="form-control Image" data-index="'+dataIndex+'"></div><div class="col-md-1 btnDel_'+dataIndex+'"><button type="button" class="btn btn-danger btnDelete" data-parent="'+dataIndex+'"><i class="fa fa-trash-o"></i></button></div></div>';
	            divParent.append(input);
	            $('#count-image').val( parseInt(dataIndex)+1 );
	    	}
	    },
        // remove image
        removeImage: function( dataParent ){
            $('.divImg_'+dataParent).remove();
            var inputImg = $('.Image');
            if ( inputImg.length == 1 ){
           	 	var index = inputImg.attr('data-index');
                $('.btnDel_'+index).addClass('hidden');
           }
        },
        /*
         * delete user group
         */
        deleteProduct: function( id ) {
            var me = this;
            XRace.helper.Modal.confirm( "Are you sure want to delete this product", function(result){
                if (result) {
                    var options = {
                            success: function (data) {
                                if (data.Code > 0) {
                                	XRace.helper.Modal.alert("Delete product successfully", function() {
                                            var t = $("#productTable").DataTable();
                                            t.draw();
                                    });
                                } else {
                                	XRace.helper.Modal.alert("Delete product fail");
                                }
                            },
                            error: function(data){
                            	XRace.helper.Modal.alert("Delete product fail");
                            }   
                    };
                    var url = '/product/delete';
                    var data = {
                            id : id
                    };
                    XRace.Ajax.post(url, data, options);
                }
			});
		}
	});
});


