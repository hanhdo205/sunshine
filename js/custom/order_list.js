// DataTable Customer List
$(document).ready(function() {
	if($('#datatable').hasClass('datatable')) {
		var start_date = $('#daterange').data('daterangepicker').startDate.format('YYYY/MM/DD 00:00');
		var end_date = $('#daterange').data('daterangepicker').endDate.format('YYYY/MM/DD 23:59');
		var f_search_type = 'order';
		var f_start = start_date;
		var f_end = end_date;
		var f_keyword = '';
		var f_status = 'all';
		var today_date = $('.today_date').val();
		var selected_date = $('#daterange').val();
		
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
		
		var order_status = getUrlParameter('status');
		if(order_status !== undefined) {
			f_status = order_status;
		}
		
		//fetch_data(f_search_type,f_start,f_end,f_keyword,f_status);
		
		// if(order_status=='pending') {
			   // $('.filter_text').text(translate.status_all);
		   // } else if(order_status=='priority') {
			   // $('.filter_text').text(translate.status_all_priority);
		   // }
		function fetch_data(date_search_type, start_date='', end_date='', search='', getstatus = '') {
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
						action: 'order_list',
						date_search_type: date_search_type,
						start_date: start_date,
						end_date: end_date,
						search: search,
						order_status: getstatus,
					},
					
				}
			});
		}
		
		function csv_data(s_search_type, s_start='', s_end='', s_keyword='', s_status) {
			$.fileDownload(ajax_url,{
				httpMethod: 'post',
				data: {
					action: 'csv_order_list',
					date_search_type: s_search_type,
					start_date: s_start,
					end_date: s_end,
					search: s_keyword,
					order_status: s_status
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
			var start_date = $('#daterange').data('daterangepicker').startDate.format('YYYY/MM/DD 00:00');
			var end_date = $('#daterange').data('daterangepicker').endDate.format('YYYY/MM/DD 23:59');
			var search_type = $("input[name='search_type']:checked").val();
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
			   fetch_data(f_search_type, f_start, f_end, f_keyword, f_status);
			   window.history.pushState({}, document.title, '/orders.aspx' );
			   $('.filter_text').text('(' + translate.filter_by_keyword + search + ')');
			   if(search=='') {
				   $('.filter_text').text('');
			   }
		  });
		  
		$('.csv_order_download').on( 'click', function () {
			csv_data(f_search_type,f_start,f_end,f_keyword,f_status);
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
		// Extend dataTables search
		/*$('#daterange').on('apply.daterangepicker', function(ev, picker) {
			//console.log(datearr);
			$.fn.dataTable.ext.search.push(
			  function(settings, data, dataIndex) {
				var min = picker.startDate.format('YYYY/MM/DD 00:00');
				var max = picker.endDate.format('YYYY/MM/DD 23:59');
				//console.log(min);
				var createdAt = data[0] || 0; // Our date column in the table
				
				if (
				  (min == "" || max == "") ||
				  (moment(createdAt).isSameOrAfter(min) && moment(createdAt).isSameOrBefore(max))
				) {
				  return true;
				}
				return false;
			  }
			);
			$(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
			table.draw();
		});*/
	}
});
