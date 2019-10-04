// DataTable Payment List
$(document).ready(function() {
	$("#status-checkbox").click( function(){
	   if( $(this).is(':checked') ) {
			$('.status-has-value').prop('checked', false);
		}
	});
	$(".status-has-value").click( function(){
	   if( $(this).is(':checked') ) {
			$('#status-checkbox').prop('checked', false);
		}
	});
	$('#select_person').select2({
		  theme: 'bootstrap'
	});
	
	function getHora() {
	   date = new Date();   
	   return " "+date.getHours()+':'+date.getMinutes()+':'+date.getSeconds();
	};
	
	function addCommas(nStr)
	{
		nStr += '';
		x = nStr.split('.');
		x1 = x[0];
		x2 = x.length > 1 ? '.' + x[1] : '';
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + ',' + '$2');
		}
		return x1 + x2;
	}
	
	// Member list payment update action
	$(document).on( 'click', '.update_payment', function () {
		if(flag) {
			flag = false;

			var title = translate.form_payment_update_title;
			var id = parseInt($(this).attr('data-id'));
			var sales = parseInt($(this).attr('data-sales'));
			var due = $(this).attr('data-unpaid');
			var this_row = $(this).parents('tr');
			
			$.confirm({
				boxWidth: '50%',
				useBootstrap: false,
				title: title,
				content: '' +
				'<form action="" class="custom_confirm_form mt-3">' +
				'<div class="form-group row">' +
				'<label class="col-md-3 col-form-label">'+translate.date_label+'</label>' +
				'<div class="col-md-9"><input class="form-control" id="dateinput" type="text" name="update"></div>' +
				'</div>' +
				'<div class="form-group row">' +
				'<label class="col-md-3 col-form-label">'+translate.due_label+'</label>' +
				'<div class="col-md-9"><div class="input-group"><input class="form-control due" id="due" name="due" size="16" type="text" value="'+addCommas(due)+'" readonly><div class="input-group-append"><span class="input-group-text">'+translate.jpy+'</span></div></div></div>' +
				'</div>' +
				'<div class="form-group row">' +
				'<label class="col-md-3 col-form-label">'+translate.liquidate_label+'</label>' +
				'<div class="col-md-9"><div class="input-group"><input class="form-control liquidate" id="liquidate" name="liquidate" size="16" type="text"><div class="input-group-append"><span class="input-group-text">'+translate.jpy+'</span></div></div></div>' +
				'</div>' +
				'<div class="form-group row">' +
				'<label class="col-md-3 col-form-label">'+translate.note_label+'</label>' +
				'<div class="col-md-9"><textarea id="comment" name="comment" class="form-control" rows="4"></textarea></div>' +
				'</div>' +
				'</form>',
				buttons: {
					formSubmit: {
						text: translate.submit_btn,
						btnClass: 'btn-blue',
						action: function () {
							var liquidate = this.$content.find('#liquidate').val();
							var dateinput = this.$content.find('#dateinput').val();
							var comment = this.$content.find('#comment').val();
							if(!liquidate){
								$.alert({
									title: false,
									closeIcon: false,
									content: translate.invalid,
									buttons: {
										confirm: {
											text: translate.close_btn,
										}
									}
									});	
								return false;
							}
							$.ajax({
									url: ajax_url,
									type: 'post',
									data: {
										action: 'add_payment',
										id: id,
										due: due,
										liquidate: liquidate,
										dateinput: dateinput,
										comment: comment
									},
									success: function(response){
										flag = true;
										var $toast = toastr["success"](translate.payment_updated,translate.toast_title);
										var cell = table.cell( this_row, ':eq(4)' );
										cell.data(due-liquidate).draw();
										var cell_paid = table.cell( this_row, ':eq(6)' );
										cell_paid.data(translate.response['status']).draw();
										
									}
								});
						}
					},
					cancel: {
						text: translate.cancel_btn,
						action: function() {
								flag = true;
							}
						},
				},
				onContentReady: function () {
					// bind to events
					var jc = this;
					this.$content.find('form').on('submit', function (e) {
						// if the user submits the form by pressing enter in the field.
						e.preventDefault();
						jc.$$formSubmit.trigger('click'); // reference the button and click it
					});
					
					$( "#dateinput" ).datepicker(
					{	
						changeMonth: true,
						changeYear: true,
						dateFormat: 'yy/mm/dd' + getHora(),
					}).datepicker("setDate", new Date());
				}
			});
		}
	});
	
	var flag = true;
	var start_date = $('#daterange').data('daterangepicker').startDate.format('YYYY/MM/DD 00:00');
	var end_date = $('#daterange').data('daterangepicker').endDate.format('YYYY/MM/DD 23:59');
	var f_search_type = 'daterange';
	var f_start = start_date;
	var f_end = end_date;
	var f_keyword = '';
	var f_person = '';
	var f_status = 'all';
	
	fetch_data(f_search_type,f_start,f_end,f_keyword,f_person,f_status);
		
	function fetch_data(date_search_type, start_date='', end_date='', search='', person='', getstatus = '') {
		var amountTotal;
		table = $('#datatable').DataTable( {
			"sPaginationType": "simple_numbers",
			"bFilter": false,
			"language":
				{
					 "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/"+datatables_language+".json"
				},
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			} ],
			"processing": true,
			"serverSide": true,
			"order" : [],
			"ajax":{
				url: ajax_url,
				type: 'post',
				data: {
					action: 'revenue_expenditure',
					date_search_type: date_search_type,
					start_date: start_date,
					end_date: end_date,
					search: search,
					person: person,
					payment_status: getstatus
				}
			}
		});
	}
	
	function csv_data(s_search_type, s_start='', s_end='', s_keyword='', s_person='', s_status) {
		$.fileDownload(ajax_url,{
			httpMethod: 'post',
			data: {
				action: 'csv_revenue_expenditure',
				date_search_type: s_search_type,
				start_date: s_start,
				end_date: s_end,
				search: s_keyword,
				person: s_person,
				payment_status: s_status
			},
			successCallback: function (url) {
				//insert success code

			},
			failCallback: function (html, url) {
				//insert fail code
			}
		});
	}

		/*if(selected_date != today_date) {
			setTimeout(function(){ $('#mySearchButton').trigger('click');}, 200);
		}*/
		// setTimeout(function(){ $('#mySearchButton').trigger('click');}, 200);

	$('#mySearchButton').on( 'keyup click', function () {
		var search = $('#mySearchText').val();
		var select_person = $('#select_person').val();
		start_date = $('#daterange').data('daterangepicker').startDate.format('YYYY/MM/DD 00:00');
		end_date = $('#daterange').data('daterangepicker').endDate.format('YYYY/MM/DD 23:59');
		var search_type = 'daterange';
		
		var selected = new Array();
		$("#statusgroup input[type=checkbox]:checked").each(function () {
			selected.push(this.value);
		});

		if (selected.length > 0) {
			f_status = selected.join(",");
		} else {
			f_status = 'all';
			$('#status-checkbox').prop('checked', true);
		}		
			
		 $('#datatable').DataTable().destroy();
		   f_search_type = search_type;
		   f_start = start_date;
		   f_end = end_date;
		   f_keyword = search;
		   f_person = select_person;
		   fetch_data(f_search_type, f_start, f_end, f_keyword, f_person, f_status);
		   window.history.pushState({}, document.title, '/revenue-expenditure-management.aspx' );
		  
	  });
		  
	$('.csv_payment_download').on( 'click', function () {
		csv_data(f_search_type,f_start,f_end,f_keyword,f_person,f_status);
	});
	
});
