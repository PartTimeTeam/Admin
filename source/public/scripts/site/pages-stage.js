head.ready(function () {
	Namespace(siteConfig.namespace);
	Singleton(siteConfig.namespace + '.Stage', {
	    user: {},
	    __init__: function() {
	        var me = this;
	        var aoColumns = [
	    	                 {"data": "id"},
	    	                 {"data": "name"},
	    	                 { "data": "content" },
	    	                 { "data": "description" },
	    	                 { "data": "Action_Table"}
	    	];
	        var columnDefs = [
							{
									"render": function ( data, type, row ) {
										// strip_tags html
										return row['content'].replace(/(<([^>]+)>)/ig,"");
									},
									"targets": 2,
									"orderable": true,
									"data": "content"
							 },
	  					 {
	  							"render": function ( data, type, row ) {
	  								var action = '';
	  								action += '<button type="button" onclick="window.location =\'/stage/detail/id/'+row['id']+'\'" class="btn btn-primary" rel="tooltip" data-placement="top" title="Delete"><i class="fa fa-pencil-square-o"></i></button> &nbsp;';
	  								action += '<button type="button" onclick="PaymentAdmin.site.pages.Stage.deleteStage('+row['id']+')" class="btn btn-danger" rel="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></button>';
	  								return action;
	  							},
	  							"targets": 4,
	  							"orderable": false,
	  							"data": "Action_Table"
	  					   }
	  	    ];
	        XRace.ui.Widget.setupDataTable( "#stageTable", "/stage/list", aoColumns, columnDefs, {order:[[ 0, "desc" ]]});
	    },
	    /*
         * delete user
         */
        deleteStage: function( id ) {
            var me = this;
            XRace.helper.Modal.confirm( "Are you sure want to delete this stage", function(result){
                if (result) {
                    var options = {
                            success: function (data) {
                                if (data.Code > 0) {
                                	XRace.helper.Modal.alert("Delete stage successfully", function() {
                                            var t = $("#stageTable").DataTable();
                                            t.draw();
                                    });
                                } else {
                                	XRace.helper.Modal.alert("Delete stage fail");
                                }
                            },
                            error: function(data){
                            	XRace.helper.Modal.alert("Delete stage fail");
                            }   
                    };
                    var url = '/stage/delete';
                    var data = {
                            id : id
                    };
                    XRace.Ajax.post(url, data, options);
                }
			});
		}
	});
});



