head.ready(function() {
	Namespace('XRace.ui');
	
	Singleton('XRace.ui.Widget', {
		isAllowSearch: true,
		myTimeout: null,
		myTimeout2: null,
		
		__init__: function() {
			var me = this;
			me.setupAll();
		},
		setupAll: function(container) {
			var me = this;
			
			me.setupTooltips(container);
			me.setupDataTables(container);
			me.setupDatePickers();
			me.setupModals();
			me.setupSubmitButtons();
//			me.setupMaskedInputs(container);
		},
		setupSubmitButtons: function(){
			$(document).on( "click", 'button[data-type=submit]', {}, function() {   
				$(this).parents("form").submit();
			});
		},
		
		setupTooltips: function(container) {
			$('[rel="tooltip"]', container).tooltip();
		},
		
		setupModals: function() {
			//init for modal
	    	$('.modal').on('show.bs.modal', function(){
	    		XRace.helper.Modal.centerModal(this);
	    	});
	    	$(window).on("resize", function () {
	    		$('.modal:visible').each(function(){
	    			XRace.helper.Modal.centerModal(this);
	    		});
	    	});	
	    	$('.modal').on('hidden.bs.modal', function(){
	    		if ( $(this).attr("id") != "dialog-loading" ) {
	    			//$(this).empty();
	    		}
	    	});
		},
		
		
		
		setupMaskedInputs: function(container) {
			$(':text[data-type=number]', container).each(function() {
				new XRace.ui.NumberInput(this);
			});
			
			$(':text[data-type=phone]', container).each(function() {
				new XRace.ui.PhoneInput(this);
			});
			
			$(':text[data-type=price]', container).each(function() {
				var maxValue = "999999999.99";
				var maxLength = $(this).attr('maxlength');
				if( XRace.isDefined( maxLength ) && maxLength !='') {
					maxValue = (0.99999999999 * Math.pow (10 , maxLength - 3)).toFixed(2);
				}
				$.extend($.fn.autoNumeric.defaults, {aSep: ',', vMax: maxValue});
				$(this).autoNumeric('init');
			});
		},
		setupDataTables: function(container) {
			var me = this;
	        	
	        $('.data-table-auto', container).each(function() {
	        	me.setupDataTableAuto($(this));
	        });
	    },
	    
	    saveState: function( oSettings, sValue, stateSave ) {
	    	if (stateSave) {
				//Save custom filters
				$(".search-form-datatable input").each(function() {
					if ( $(this).attr('id') && $(this).val() != "" && !($(this).hasClass("no-save"))) {
						sValue[ $(this).attr('id') ] = $(this).val();
					}
					if ($(this).hasClass("no-save")) {
						sValue[ $(this).attr('id') ] = "";
					}
				});
				$(".search-form-datatable select").each(function() {
					if ( $(this).attr('id') && $(this).val() != "" ) {
						sValue[ $(this).attr('id') ] = $(this).val();
					}
				});
	    	}
			return sValue;
	    },
	    
	    loadState: function( oSettings, oData, stateSave ) {
	    	if (stateSave) {
	    		var isLoad = true;
	    		$(".search-form-datatable select").each(function() {
					var oControl = $(this);
					$.each(oData, function(index, value) {
						if ( index == oControl.attr('id') ) {
							var options = $("#" + oControl.attr('id') + " option[value='" + value + "']");
							if (options.length == 0) {
								isLoad = false;
							}
						}
					});
				});
	    		$(".search-form-datatable input").each(function() {
					if ($(this).hasClass("no-save")) {
						isLoad = false;
						return false;
					}
				});
	    		if ( isLoad == true ) {
					//Load custom filters
					$(".search-form-datatable input").each(function() {
						var oControl = $(this);
					     
						$.each(oData, function(index, value) {
							if ( index == oControl.attr('id') ) {
								oControl.val( value );
							}
						});
					});
					$(".search-form-datatable select").each(function() {
						var oControl = $(this);
						$.each(oData, function(index, value) {
							if ( index == oControl.attr('id') ) {
								oControl.val( value );
								oControl.select2("val", value);
							}
						});
					});
	    		} else {
	    			return false;
	    		}
			}
			return true;
	    },
	    
	    setupDataTableAuto: function(dataTableObjOrId) {
	    	var me = this;
	        var dataTable = XRace.ui.Helper.getUiSelector(dataTableObjOrId);
	        // Do nothing to an already setup table
	        if (dataTable.hasClass('no-data') || dataTable.hasClass('setup')) {
	            return; 
	        }
	        var stateSave = true;
	        if (dataTable.hasClass('disable-state-save')) {
	        	stateSave = false;
	        }
	        var autoPagination = true;
	        if (dataTable.hasClass('no-pagination')) {
	        	autoPagination = false;
	        }
	        var ordering = true;
	        if (dataTable.hasClass('no-ordering')) {
	        	ordering = false;
	        }
	        var columns = $("th", dataTable);
	        var aoColumns = [];
	        for ( var i = 0; i < columns.length; i++ ) {
	        	if ( $(columns[i]).hasClass("disable-order") ) {
	        		aoColumns.push({"orderable": false});
	        	} else {
	        		aoColumns.push({"orderable": true});
	        	}
	        }
	        // Mark as setup
	        dataTable.addClass('setup');
	        dataTable.on('xhr.dt', function ( e, settings, json ) {
				if (json && json.Code == PaymentAdmin.site.pages.Constant.CODE_SESSION_EXPIRED) {
					//go to login page if session expire
					window.location.href = "/";
					return false;
				}
	        });
	        dataTable.dataTable({
	            "scrollCollapse": true,
	            "ordering": ordering,
	            "stateSave": stateSave,
	            "bDestroy": true,
	            "stateDuration": 0,
	            "aoColumns": aoColumns,
	            "bPaginate": autoPagination,
	            "language": dataTableLang,
	            "orderMulti": true,
	    		"fnDrawCallback":function(){
	    	        if( $(".dataTables_paginate").find(".paginate_button").length <=5 ) {
	    	            $('.dataTables_wrapper div.dataTables_paginate').hide();
	    	        } else {
	    	            $('.dataTables_wrapper div.dataTables_paginate').show();
	    	        }
	    	        XRace.helper.XRace.centerModal(dataTable.parents(".modal"));
	    	        
	    	        // For checkbox in delete all data
	    	        if(XRace.isDefined( $(".data-table #checkIdAll") )){
	    	        	$("#checkIdAll").attr('checked', false);
	    	        	$('.checkId').change();
	    	        }
	        	},
	            "fnStateSaveParams": function ( oSettings, sValue ) {
	            	return me.saveState(oSettings, sValue, stateSave);
	    		},
	    		"fnStateLoadParams"	: function ( oSettings, oData ) {
	    			return me.loadState(oSettings, oData, stateSave);
	    		}
	    	});
	    },
	    
	    //setup datatables by ajax
	    setupDataTable: function( selector, requestUrl, aoColumns, columnDefs, opts ) {
	    	var me = this;
	    	var stateSave = true;
	    	// Do nothing to an already setup table
	        if ($(selector).hasClass('no-data') || $(selector).hasClass('setup')) {
	            return; 
	        }
	    	if ($(selector).hasClass('disable-state-save')) {
	        	stateSave = false;
	        }
	    	order = [[0, 'desc']];
	    	if ( XRace.isDefined( opts ) && XRace.isDefined( opts.order ) ) {
	    		order = opts.order;
	    	}
	    	$(selector).addClass('setup');
	    	$(selector).on('xhr.dt', function ( e, settings, json ) {
				if (json && json.Code == PaymentAdmin.site.pages.Constant.CODE_SESSION_EXPIRED ) {
					//go to login page if session expire
					window.location.href = "/";
					return false;
				}
	        });
	    	$(selector).dataTable({
	    		"processing": true,
	    		"serverSide": true,
	    		"stateSave": stateSave,
	    		"bDestroy": true,
	    		"stateDuration": 0,
	    		"paging": true,
	    		"sPaginationType": "full_numbers",
	    		"language": dataTableLang,
	    		"ajax": {
	    			"url": requestUrl,
	    			"dataType": "json",
	    			"silent":true,
	    			"silent_sp":true
	    		},
	    		"aoColumns" : aoColumns,
	    		"columnDefs": columnDefs,
	    		"order": order,
	    		"orderMulti": true,
	    		"fnDrawCallback":function(){
	    	        if( $(".dataTables_paginate").find(".paginate_button").length <=5 ) {
	    	            $('.dataTables_wrapper div.dataTables_paginate').hide();
	    	        } else {
	    	            $('.dataTables_wrapper div.dataTables_paginate').show();
	    	        }
	    	        XRace.helper.Modal.centerModal($(selector).parents(".modal"));
	    	        
	    	        // For checkbox in delete all data
	    	        if(XRace.isDefined( $(".data-table #checkIdAll") )){
	    	        	$("#checkIdAll").attr('checked', false);
	    	        	$('.checkId').change();
	    	        }
	        	},
	    		"fnStateSaveParams": function ( oSettings, sValue ) {
	    			return me.saveState(oSettings, sValue, stateSave);
	    		},
	    		"fnStateLoadParams"	: function ( oSettings, oData ) {
	    			return me.loadState(oSettings, oData, stateSave);
	    		}
	    	});
	    },
	    
	    setupDatePickers: function(  ) {
	    	$('.datepicker').each(function(){
	    		var format = 'yyyy/mm/dd';
	    		if ( XRace.isDefined( $(this).attr("format") ) && $(this).attr("format") != "" ) {
	    			format = $(this).attr("format");
	    		}
	    		$(this).datetimepicker({
	        		format: format,
	        		autoclose: 1,
	        		todayHighlight: 1,
	        		minView: 2,
	        		language: Language,
	        		fontAwesome: true
	            });
	    	});
	    	
	    	$('.datetime-picker').each(function(){
	    		var format = 'yyyy/mm/dd hh:ii:ss';
	    		if ( XRace.isDefined( $(this).attr("format") ) && $(this).attr("format") != "" ) {
	    			format = $(this).attr("format");
	    		}
	    		$(this).datetimepicker({
	    			format: format,
	        		autoclose: 1,
	        		todayHighlight: 1,
	        		minView: 0,
	        		language: Language,
	        		fontAwesome: true
	            });
	    	});
	    },
	    
	    setupSelectColumn : function (selectId, dataTableId){
	    	if ( $('#'+selectId).length > 0 ) {
		    	// setup bootstrap multi select 
		    	$('#'+selectId).multiselect({
		    		includeSelectAllOption: true,
		    		selectAllText: translate("check-all"), 
		    		allSelectedText: translate("all-options-selected"), 
		    		numberDisplayed: 0,
		    		buttonTitle: function(options, select) {
		    			return '';
		    		},
		    		
		    		buttonText: function(options, select) {
		    			var text = '';
		    			var len = options.length; 
		    			if(  len > 0 ) {
		    				if( options[0].value == 'multiselect-all' ) {
		    					len-- ;
		    				}
		    			}
		    			if( len == select[0].length - 1) {
		    				text = translate("all-options-selected");
		    			} else if( len == 0){
		    				text = translate("non-select-item");
		    			} else {
		    				text = len + ' ' + translate("item-selected");
		    			}
		    			
		    			return text;
		    		},
		    		
		    		onChange: function(element, checked) {
		    			var table = $('#'+dataTableId).DataTable();
		    			if(element.val() == 'multiselect-all' ){
		    				if(checked === true){
	//	    					table.columns().visible(true);
		    					$('#'+selectId+' option').each (function(){
		    						var column = table.column( $(this).val() );
		    						column.visible( true );
		    					});
		    				}else{
		    					//get all data in select box then set visible s false
		    					$('#'+selectId+' option').each (function(){
		    						var column = table.column( $(this).val() );
		    						column.visible( false );
		    					});
		    				}
		    			}else{
		    				// Get the column API object
		    				var column = table.column( element.attr('value') );
		    				// Toggle the visibility
		    				column.visible( ! column.visible() );
		    			}
		    		}
		    	});
		    	// get data and prepare data provider for multiselect
		    	var columnsData = $('#'+dataTableId).DataTable().context[0].aoColumns;
		    	var length = columnsData.length;
		    	var options = [];
		    	var checkAllDataDefault  = {label: "check-all", title: "check-all", value: 'multiselect-all'};
		    	options.push(checkAllDataDefault);
		    	
		    	for (var i = 0; i < length; i++ ){
		    		// Proceed check hidden or show column data
		    		if( XRace.isDefined( columnsData[i].nTh ) && $(columnsData[i].nTh).hasClass('no-hidden') == false && columnsData[i].visible !== false){
		    			var isVisible = false;
		    			if( XRace.isDefined( columnsData[i].bVisible )  && columnsData[i].bVisible === true ){
		    				isVisible = true;
		    			}
		    			var data = {label: columnsData[i].sTitle, title: columnsData[i].sTitle, value: columnsData[i].idx, selected: isVisible};
		    			options.push(data);
		    		}
		    	}
		    	$('#'+selectId).multiselect('dataprovider', options);
	    	}
	    }
	    
	    
	});
	
	Class('XRace.ui.MaskedInput', {
		
		_input: null,
		
		_textMask: null,
		
		_maskChar: '#',
		
		_regex: null,
		
		__init__: function(input, inputType, textMask, regex) {
			var me = this, input = $(input);
			
			// Cannot directly change input type using jQuery
			// It has known issue with IE, use pure JS instead
			// http://api.jquery.com/prop/
			// http://stackoverflow.com/questions/1544317/change-type-of-input-field-with-jquery
			input.prop('type', inputType);
			
			me._input 	 = input;
			me._textMask = textMask;
			me._regex 	 = regex;
			
			input.keypress(function(evt) {
				me._keypress(evt, me);
			});
			input.bind('paste', function(evt) {
				setTimeout(function() {
					me._paste(evt, me);
				}, 1);
			});
		},
		
		_keypress: function(evt, me) {
			var input    = me._input,
				mask 	 = me._textMask,
				maskChar = me._maskChar,
				regex 	 = me._regex,
				key 	 = XRace.JSEvent.getKeyByEvent(evt);
			
			// Allow Ctrl+C, Ctrl+X, Ctrl+V
			if (evt.ctrlKey && ([99, 118, 120]).indexOf(key) !== -1) {
				return false;
			}
				
			if (!XRace.isDefined(regex)) {
				return false;
			}
				
			if (XRace.JSEvent.isPrintableEventKey(key)) {
				var ch = String.fromCharCode(key),
					str = input.val() + ch,
					pos = str.length;
					
				// Check Regex only
				if (!XRace.isDefined(mask)) {
					if (!regex.test(ch)) {
						XRace.JSEvent.stopEvent(evt);
					}
				}
				// Check Regex and apply Mask
				else {
					mask = mask.trim();
					if (regex.test(ch) && pos <= mask.length) {
						if (mask.charAt(pos - 1) !== maskChar) {
							str = input.val() + mask.charAt(pos - 1) + ch;
						}
						input.val(str);
					}
					XRace.JSEvent.stopEvent(evt);
				}
			}
		},
		
		_paste: $.noop
	});
	
	Class('XRace.ui.NumberInput', XRace.ui.MaskedInput, {
	
		__init__: function(input) {
			var me = this;
			
			me.callSuper(XRace.ui.MaskedInput, '__init__', [input, 'tel', null, /^[0-9]+$/]);
			
			me._input.attr('autocorrect', 'off')
				 	 .attr('autocomplete', 'off');
		},
		
		_paste: function(evt, me) {
			if (isNaN(me._input.val())) {
				me._input.val('');
			}
		}
	});
	
	Class('XRace.ui.PhoneInput', XRace.ui.MaskedInput, {
	
		__init__: function(input) {
			var me = this;
			
			me.callSuper(XRace.ui.MaskedInput, '__init__', [input, 'tel', null, /^[0-9]+$/]);
			
			me._input.attr('autocorrect', 'off')
				 	 .attr('autocomplete', 'off');
				 	 
			if (XRace.String.isEmpty(me._input.attr('maxlength'))) {
				me._input.attr('maxlength', 32);
			}
		},	
		
		_paste: function(evt, me) {
			if (isNaN(me._input.val())) {
				me._input.val('');
			}
		}
	});
	
	Class('XRace.ui.PriceInput', XRace.ui.MaskedInput, {
	
		__init__: function(input) {
			var me = this;
			
			me.callSuper(XRace.ui.MaskedInput, '__init__', [input, 'tel', null, /^[0-9.]+$/]);
			
			me._input.attr('autocorrect', 'off')
				 	 .attr('autocomplete', 'off');
		},
		
		_paste: function(evt, me) {
			if (isNaN(me._input.val())) {
				me._input.val('');
			}
		}
	});
});