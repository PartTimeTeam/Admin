head.ready(function () {
	Namespace(siteConfig.namespace);
	Singleton(siteConfig.namespace + '.Game', {
	    user: {},
	    __init__: function() {
	        var me = this;
	        
	        var aoColumns = [
	    	                 {"data": "id"},
	    	                 {"data": "name"},
	    	                 { "data": "time_start" },
	    	                 { "data": "end_time" },
	    	                 { "data": "list_stage" },
	    	                 { "data": "description" },
	    	                 { "data": "Action_Table"}
	    	];
	        var columnDefs = [
	  					 {
	  							"render": function ( data, type, row ) {
	  								var action = '';
	  								action += '<button type="button" onclick="window.location =\'/game/detail/id/'+row['id']+'\'" class="btn btn-primary" rel="tooltip" data-placement="top" title="Delete"><i class="fa fa-pencil-square-o"></i></button> &nbsp;';
	  								action += '<button type="button" onclick="PaymentAdmin.site.pages.Game.deleteGame('+row['id']+')" class="btn btn-danger" rel="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></button>';
	  								return action;
	  							},
	  							"targets": 6,
	  							"orderable": false,
	  							"data": "Action_Table"
	  					   }
	  	    ];
	        XRace.ui.Widget.setupDataTable( "#gameTable", "/game/list", aoColumns, columnDefs, {order:[[ 0, "desc" ]]});
	    },
	    /*
         * delete user
         */
        deleteGame: function( id ) {
            var me = this;
            XRace.helper.Modal.confirm( "Are you sure want to delete this game", function(result){
                if (result) {
                    var options = {
                            success: function (data) {
                                if (data.Code > 0) {
                                	XRace.helper.Modal.alert("Delete game successfully", function() {
                                            var t = $("#gameTable").DataTable();
                                            t.draw();
                                    });
                                } else {
                                	XRace.helper.Modal.alert("Delete game fail");
                                }
                            },
                            error: function(data){
                            	XRace.helper.Modal.alert("Delete game fail");
                            }   
                    };
                    var url = '/game/delete';
                    var data = {
                            id : id
                    };
                    XRace.Ajax.post(url, data, options);
                }
			});
		}
	});
});



