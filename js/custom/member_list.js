// DataTable Customer List
$(document).ready(function() {
	var flag = true;

	$('#select_person').select2({
		  theme: 'bootstrap'
	});

	function getHora() {
	   date = new Date();   
	   return " "+date.getHours()+':'+date.getMinutes()+':'+date.getSeconds();
	};

	// get URL param
	var getUrlParameter = function getUrlParameter(sParam) {
		var sPageURL = window.location.search.substring(1),
			sURLVariables = sPageURL.split('&'),
			sParameterName,
			i;

		for (i = 0; i < sURLVariables.length; i++) {
			sParameterName = sURLVariables[i].split('=');

			if (sParameterName[0] === sParam) {
				return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
			}
		}
	};

	// page load after add new member
	var action = getUrlParameter('action');
	if(action === 'member-created') {
		var $toast = toastr["success"](translate.member_created,translate.toast_title);
		window.history.replaceState(null, null, window.location.pathname);
	}

	// Member list payment update action
	$(document).on( 'click', '.add_sales', function () {
		if(flag) {
			flag = false;

			var title = translate.form_payment_update_title;
			var id = parseInt($(this).attr('data-id'));
			var sales = parseInt($(this).attr('data-sales'));
			var paid = parseInt($(this).attr('data-paid'));
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
				'<label class="col-md-3 col-form-label">'+translate.amount_label+'</label>' +
				'<div class="col-md-9"><div class="input-group"><input class="form-control amount" id="amount" name="amount" size="16" type="text"><div class="input-group-append"><span class="input-group-text">'+translate.jpy+'</span></div></div></div>' +
				'</div>' +
				'<div class="form-group row">' +
				'<label class="col-md-3 col-form-label">'+translate.vat_label+'</label>' +
				'<div class="col-md-9"><div class="input-group"><input class="form-control vat" id="vat" name="vat" size="16" type="text"><div class="input-group-append"><span class="input-group-text">'+translate.jpy+'</span></div></div></div>' +
				'</div>' +
				'<div class="form-group row">' +
				'<label class="col-md-3 col-form-label">'+translate.received_label+'</label>' +
				'<div class="col-md-9 col-form-label"><div class="form-check form-check-inline mr-1"><input class="form-check-input" id="inline-paid" type="radio" value="1" name="payment_status"><label class="form-check-label" for="inline-paid">'+translate.paid_label+'</label></div><div class="form-check form-check-inline mr-1"><input class="form-check-input" id="inline-unpaid" type="radio" value="2" name="payment_status" checked><label class="form-check-label" for="inline-unpaid">'+translate.unpaid_label+'</label></div><div class="form-check form-check-inline mr-1"><input class="form-check-input" id="inline-partial" type="radio" value="3" name="payment_status"><label class="form-check-label" for="inline-partial">'+translate.partial_label+'</label></div><div class="form-check form-check-inline mr-1 partial-input-field d-none"><div class="input-group"><input class="form-control partial" id="partial" name="partial" size="16" type="text"><div class="input-group-append"><span class="input-group-text">'+translate.jpy+'</span></div></div></div></div></div>' +
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
							var amount = this.$content.find('#amount').val();
							var vat = this.$content.find('#vat').val();
							var dateinput = this.$content.find('#dateinput').val();
							var payment_status = this.$content.find("input[name='payment_status']:checked").val();
							var comment = this.$content.find('#comment').val();
							var partial = this.$content.find('#partial').val();
							if(!amount){
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
										action: 'add_sales',
										id: id,
										amount: amount,
										vat: vat,
										dateinput: dateinput,
										payment_status: payment_status,
										comment: comment,
										partial: partial,
									},
									success: function(response){
										flag = true;
										var $toast = toastr["success"](translate.sales_updated,translate.toast_title);
										var cell = table.cell( this_row, ':eq(7)' );
										cell.data(sales + 1).draw();
										if(payment_status !== 'unpaid') {
											var cell_paid = table.cell( this_row, ':eq(9)' );
											cell_paid.data(paid + 1).draw();
										}
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
					$("#amount").on("input", function(){
						var am = $('#amount').val();
						var total = (am * vat) / 100;
						$('#vat').val(total);
					});
					$('input[type=radio][name=payment_status]').change(function() {
						if (this.value == 3) {
							$('.partial-input-field').removeClass('d-none');
						}
						else {
							$('.partial-input-field').addClass('d-none');
						}
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
	
	// datattable action
	if($('#datatable').hasClass('datatable')) {
		var start_date = $('#daterange').data('daterangepicker').startDate.format('YYYY/MM/DD 00:00');
		var end_date = $('#daterange').data('daterangepicker').endDate.format('YYYY/MM/DD 23:59');
		var f_search_type = $("input[name='search_type']:checked").val();
		var f_start = start_date;
		var f_end = end_date;
		var f_keyword = '';
		var f_person = $("#select_person").val();
		
		fetch_data(f_search_type,f_start,f_end,f_keyword,f_person);
		
		function fetch_data(date_search_type, start_date='', end_date='', search='', getperson = '') {
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
						action: 'member_list',
						date_search_type: date_search_type,
						start_date: start_date,
						end_date: end_date,
						search: search,
						person: getperson,
					},
					
				}
			});
		}
		
		function csv_data(s_search_type, s_start='', s_end='', s_keyword='', s_person) {
			$.fileDownload(ajax_url,{
				httpMethod: 'post',
				data: {
					action: 'csv_member_download',
					date_search_type: s_search_type,
					start_date: s_start,
					end_date: s_end,
					search: s_keyword,
					person: s_person
				},
				successCallback: function (url) {
					//insert success code

				},
				failCallback: function (html, url) {
					//insert fail code
				}
			});
		}
		
		//setTimeout(function(){ $('#mySearchButton').trigger('click');}, 200);
		  
		$('#mySearchButton').on( 'keyup click', function () {
			var search = $('#mySearchText').val();
			var start_date = $('#daterange').data('daterangepicker').startDate.format('YYYY/MM/DD 00:00');
			var end_date = $('#daterange').data('daterangepicker').endDate.format('YYYY/MM/DD 23:59');
			var search_type = $("input[name='search_type']:checked").val();
			var search_person = $("#select_person").val();
			
			 $('#datatable').DataTable().destroy();
			   f_search_type = search_type;
			   f_start = start_date;
			   f_end = end_date;
			   f_keyword = search;
			   f_person = search_person;
			   fetch_data(f_search_type, f_start, f_end, f_keyword, f_person);
			   window.history.pushState({}, document.title, '/member-list.aspx' );
			   
		  });
		  
		$('.csv_member_download').on( 'click', function () {
			csv_data(f_search_type,f_start,f_end,f_keyword,f_person);
		});

	}
	
});
