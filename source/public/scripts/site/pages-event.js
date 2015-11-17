head.ready(function () {
	Namespace(siteConfig.namespace);
	Singleton(siteConfig.namespace + '.Event', {
	    event: {},
	    __init__: function() {
	        var me = this;
	        $(document).on('click', '.logo', function() {
	        	$("#dialog-event").empty();
	        var options = {
	            success: function (data) {
	            	console.log(data);
	                if (data.Code > 0) {
	                    $("#dialog-event").html(data.Data);
	                    $("#dialog-event").modal("show");
	                    XRace.helper.Modal.centerModal($("#dialog-event"));
	                }
	            }
	        };
	        var url = "/event/view-logo/";
	        var data = {
	            id: $('.logo').attr('data')
	        };
	        XRace.Ajax.get(url, data, options);
	        });
	        $('.team_member, .team_number, .round').keypress(function(event) {
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
	    	                 { "data": "event_id"},
	    	                 { "data": "event_name" },
	    	                 { "data": "logo_url" },
	    	                 { "data": "description" },
	    	                 { "data": "date" },
	    	                 { "data": "team_number" },
	    	                 { "data": "team_member" },
	    	                 { "data": "price" },
	    	                 { "data": "round" },
	    	                 { "data": "Action_Table"}
	    	];
	        var columnDefs = [
	        				{
	  							"render": function ( data, type, row ) {
	  								var logo = '';
	  								//if( Xrace.isDefined( row['logo_url'] ) ){
	  									logo = '<a class="logo" href="#" data="'+row['event_id']+'">'+row['logo_url']+'</a>';
	  								//}
	  								
	  								return logo;
	  							},
	  							"targets": 2,
	  							"orderable": false,
	  							"data": "Action_Table"
	  					   },
	  					 {
	  							"render": function ( data, type, row ) {
	  								var action = '';
	  								action += '<button type="button" onclick="window.location =\'/event/detail/id/'+row['event_id']+'\'" class="btn btn-primary" rel="tooltip" data-placement="top" title="Delete"><i class="fa fa-pencil-square-o"></i></button> &nbsp;';
	  								action += '<button type="button" onclick="PaymentAdmin.site.pages.Event.deleteEvent('+row['event_id']+')" class="btn btn-danger" rel="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></button>';
	  								return action;
	  							},
	  							"targets": 9,
	  							"orderable": false,
	  							"data": "Action_Table"
	  					   }
	  	    ];
	        XRace.ui.Widget.setupDataTable( "#eventTable", "/event/list", aoColumns, columnDefs, {order:[[ 0, "desc" ]]});
	    },
	    /*
         * delete
         */
        deleteEvent: function( id ) {
            var me = this;
            XRace.helper.Modal.confirm( "Are you sure want to delete this event", function(result){
                if (result) {
                    var options = {
                            success: function (data) {
                                if (data.Code > 0) {
                                	XRace.helper.Modal.alert("Delete event successfully", function() {
                                            var t = $("#eventTable").DataTable();
                                            t.draw();
                                    });
                                } else {
                                	XRace.helper.Modal.alert("Delete event fail");
                                }
                            },
                            error: function(data){
                            	XRace.helper.Modal.alert("Delete event fail");
                            }   
                    };
                    var url = '/event/delete';
                    var data = {
                            id : id
                    };
                    XRace.Ajax.post(url, data, options);
                }
			});
		}
	});
});




