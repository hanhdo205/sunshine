// DataTable Payment List
$(document).ready(function() {
	var flag = true;
	var start_date = $('#daterange').data('daterangepicker').startDate.format('YYYY/MM/DD 00:00');
	var end_date = $('#daterange').data('daterangepicker').endDate.format('YYYY/MM/DD 23:59');
	var f_search_type = 'shipping';
	var f_start = start_date;
	var f_end = end_date;
	var f_keyword = '';
	var f_status = 'all';
	var today_date = $('.today_date').val();
	var selected_date = $('#daterange').val();
		
	function fetch_data(date_search_type, start_date='', end_date='', search='', getstatus = '') {
		var amountTotal;
		var dataTable = $('#datatable').DataTable( {
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
					action: 'payment_list',
					date_search_type: date_search_type,
					start_date: start_date,
					end_date: end_date,
					search: search,
					payment_status: getstatus
				},
				dataSrc: function ( data ) {
					   amountTotal = data.amountTotal;
					   return data.data;
					 } 
				},
			drawCallback: function( settings ) {
				var api = this.api();
				$( api.column( 3 ).footer() ).html(
				  amountTotal
					);
			}
		});
	}
	
	function csv_data(s_search_type, s_start='', s_end='', s_keyword='', s_status) {
		$.fileDownload(ajax_url,{
			httpMethod: 'post',
			data: {
				action: 'csv_payment_list',
				date_search_type: s_search_type,
				start_date: s_start,
				end_date: s_end,
				search: s_keyword,
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
		setTimeout(function(){ $('#mySearchButton').trigger('click');}, 200);

	$('#mySearchButton').on( 'keyup click', function () {
		var search = $('#mySearchText').val();
		start_date = $('#daterange').data('daterangepicker').startDate.format('YYYY/MM/DD 00:00');
		end_date = $('#daterange').data('daterangepicker').endDate.format('YYYY/MM/DD 23:59');
		var search_type = 'shipping';
		
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
		
		//f_status = $("input[name='payment_status']:checked").val();
		
			
		 $('#datatable').DataTable().destroy();
		   f_search_type = search_type;
		   f_start = start_date;
		   f_end = end_date;
		   f_keyword = search;
		   fetch_data(f_search_type, f_start, f_end, f_keyword, f_status);
		   window.history.pushState({}, document.title, '/payment.aspx' );
		   $('.filter_text').text('(' + translate.filter_by_keyword + search + ')');
		   if(search=='') {
			   $('.filter_text').text('');
		   }
	  });
		  
	$('.csv_order_download').on( 'click', function () {
		csv_data(f_search_type,f_start,f_end,f_keyword,f_status);
	});

	$(document).on('click', '.cancel_action', function() {
		$(".payment_dropdown").removeClass('show');
	});
	
	$(document).on('input', 'input[name="partial_input"]', function() {
		$(this).removeClass('has-error');
		$(this).parent().find('.number_invalid').remove();
	});
	
	$(document).on('click', '.save_payment', function() {
		if(flag) {
			//flag = false;
			var isvalid = true;
			var order_id = parseInt($(this).attr('data-id'));
			var paid_status = $('.form_' + order_id).find("input[name='paid_total']:checked").val();
			if(paid_status === undefined) {
				isvalid = false;
			}
			var paid_total = '';
			toastr.options = {
				  "closeButton": true,
				  "debug": false,
				  "newestOnTop": false,
				  "progressBar": false,
				  "positionClass": "toast-top-right",
				  "preventDuplicates": false,
				  "onclick": null,
				  "showDuration": "300",
				  "hideDuration": "1000",
				  "timeOut": "5000",
				  "extendedTimeOut": "1000",
				  "showEasing": "swing",
				  "hideEasing": "linear",
				  "showMethod": "fadeIn",
				  "hideMethod": "fadeOut"
			};
			if(paid_status==1) {
				$(this).closest('.dropright').find('button').remove();
				$(this).closest('.dropright').prepend(translate.paid);
				$(".payment_dropdown").removeClass('show');
			} else if(paid_status==3) {
				paid_total = $('.form_' + order_id).find("input[name='partial_input']").val();
				var total_amount = $('.form_' + order_id).find("input[name='total_amount']").val();
				if(paid_total=='') {
					isvalid = false;
					$('.form_' + order_id).find("input[name='partial_input']").addClass('has-error');
					$('.form_' + order_id + '.partial').find('.number_invalid').remove();
					$('.form_' + order_id + '.partial').append('<small class="number_invalid text-danger">'+translate.number_empty+'</small>');
				} else if(parseInt(paid_total) > parseInt(total_amount)) {
					isvalid = false;
					$('.form_' + order_id).find("input[name='partial_input']").addClass('has-error');
					$('.form_' + order_id + '.partial').find('.number_invalid').remove();
					$('.form_' + order_id + '.partial').append('<small class="number_invalid text-danger">'+translate.number_invalid+'</small>');
				} else {
					$(this).closest('.dropright').find('button').remove();
					$(this).closest('.dropright').prepend(translate.partial);
					$(".payment_dropdown").removeClass('show');
					$('.form_' + order_id).find("input[name='partial_input']").removeClass('has-error');
				}
			}
			if(isvalid) {
				$.ajax({
					url: ajax_url,
					type: 'post',
					data: {
						action:'update_payment_status',
						order_id: order_id,
						paid_status: paid_status,
						paid_total: paid_total
					},
					success: function(response){
						flag = true;
						var $toast = toastr["success"](translate.payment_updated,translate.toast_title);
					}
				});
			}
		}
	});
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
	
});
