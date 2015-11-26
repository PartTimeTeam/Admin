head.ready(function () {
	Namespace(siteConfig.namespace);

	Singleton(siteConfig.namespace + '.ReferralFile', {
	    product: {},
	    __init__: function() {
	        var me = this;
            // setup datatable
            var aoColumns = [
	    	                 {"data": "id"},
	    	                 { "data": "code" },
	    	                 { "data": "file_name" },
	    	                 { "data": "url_share_file" },
	    	                 { "data": "Action_Table"}
	    	];
	        var columnDefs = [
							{
									"render": function ( data, type, row ) {
										var url ='';
										if ( row['url_share_file'].length > 0 ){
											url = server_host+row['url_share_file'];
										}
										return url;
									},
									"targets": 3,
									"orderable": false,
									"data": "url_share_file"
							 },
	  						{
	  							"render": function ( data, type, row ) {
	  								var action = '';
	  								action += '<button type="button" onclick="window.location =\'/referral-file/detail/id/'+row['id']+'\'" class="btn btn-primary" rel="tooltip" data-placement="top" title="Delete"><i class="fa fa-pencil-square-o"></i></button> &nbsp;';
	  								action += '<button type="button" onclick="PaymentAdmin.site.pages.ReferralFile.deleteReferralFile('+row['id']+')" class="btn btn-danger" rel="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash-o"></i></button>';
	  								return action;
	  							},
	  							"targets": 4,
	  							"orderable": false,
	  							"data": "Action_Table"
	  					   }
	  	    ];
	        XRace.ui.Widget.setupDataTable( "#referralFileTable", "/referral-file/list", aoColumns, columnDefs, {order:[[ 1, "desc" ]]});
	    },
        /*
         * delete user group
         */
        deleteReferralFile: function( id ) {
            var me = this;
            XRace.helper.Modal.confirm( "Are you sure want to delete this referral file", function(result){
                if (result) {
                    var options = {
                            success: function (data) {
                                if (data.Code > 0) {
                                	XRace.helper.Modal.alert("Delete referral file successfully", function() {
                                            var t = $("#referralFileTable").DataTable();
                                            t.draw();
                                    });
                                } else {
                                	XRace.helper.Modal.alert("Delete referral file fail");
                                }
                            },
                            error: function(data){
                            	XRace.helper.Modal.alert("Delete referral file fail");
                            }   
                    };
                    var url = '/referral-file/delete';
                    var data = {
                            id : id
                    };
                    XRace.Ajax.post(url, data, options);
                }
			});
		}
	});
});


