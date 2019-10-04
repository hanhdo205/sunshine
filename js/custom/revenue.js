$(document).ready(function() {
	$(function() {
		$('#monthselect').datepicker( {
			changeMonth: true,
			changeYear: true,
			showButtonPanel: true,
			dateFormat: 'yy/m',
			onClose: function(dateText, inst) { 
				$(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
			}
		});
	});
	// DataTable Revenue
	if($('#datatable').hasClass('datatable')) {
		fetch_data('no');
		function fetch_data(month_search,param='') {
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
						action: 'revenue_list',
						search: month_search,
						param: param
					},
					
				}
			} );
		}

		$('#daterangeButton').on( 'keyup click', function () {
			var month = $('#monthselect').val();
			if(month != ""){
			   $('#datatable').DataTable().destroy();
			   fetch_data('yes',month);
			  }else{
			   alert("Please pick a month");
			  }
		  } );

	}
});