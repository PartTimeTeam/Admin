head.ready(function () {
	Namespace(siteConfig.namespace);
	Singleton(siteConfig.namespace + '.Hint', {
	    user: {},
	    __init__: function() {
	        var me = this;
	        // setup ckeditor
	        
	        // event submit
	        $('#id_stage, #max_time').keypress(function(event) {
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
	    	                 {"data": "id"},
	    	                 {"data": "name"},
	    	                 { "data": "content" },
	    	                 { "data": "question_name" },
	    	                 { "data": "time_open" },
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
	  								action += '<button type="button" onclick="window.location =\'/hint/detail/id/'+row['id']+'\'" class="btn btn-primary" rel="tooltip" data-placement="top" title="Delete"><i class="fa fa-pencil-square-o"></i></button> &nbsp;';
	  								action += '<button type="button" onclick="PaymentAdmin.site.pages.Hint.deleteHint('+row['id']+')" class="btn btn-danger" rel="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></button>';
	  								return action;
	  							},
	  							"targets": 5,
	  							"orderable": false,
	  							"data": "Action_Table"
	  					   }
	  	    ];
	        XRace.ui.Widget.setupDataTable( "#hintTable", "/hint/list", aoColumns, columnDefs, {order:[[ 0, "desc" ]]});
	    },
	    /*
         * delete user
         */
        deleteHint: function( id ) {
            var me = this;
            XRace.helper.Modal.confirm( "Are you sure want to delete this hint", function(result){
                if (result) {
                    var options = {
                            success: function (data) {
                                if (data.Code > 0) {
                                	XRace.helper.Modal.alert("Delete hint successfully", function() {
                                            var t = $("#hintTable").DataTable();
                                            t.draw();
                                    });
                                } else {
                                	XRace.helper.Modal.alert("Delete hint fail");
                                }
                            },
                            error: function(data){
                            	XRace.helper.Modal.alert("Delete hint fail");
                            }   
                    };
                    var url = '/hint/delete';
                    var data = {
                            id : id
                    };
                    XRace.Ajax.post(url, data, options);
                }
			});
		}
	});
});



