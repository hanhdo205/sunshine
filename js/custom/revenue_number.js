$(document).ready(function() {
	$('.revenue_number').datepicker( {
		showButtonPanel: true,
		dateFormat: 'MM yy',
		minDate: new Date,
		onClose: function(dateText, inst) { 
			$(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
		},
		onChangeMonthYear: function (year,month,inst ) {
			var this_id = $(this).attr('data-id');
			setTimeout(function(){
				doDrawNumber(year,month,this_id);
			}, 50);
			
		}
	});
	
	var currentYear = (new Date).getFullYear();
	var currentMonth = (new Date).getMonth() + 1;
	setTimeout(function(){
		doDrawNumber(currentYear,currentMonth,'reminder');
	}, 50);
	
	function doDrawNumber(year,month,this_id) {
		table = $('#datatable').DataTable( {
				"destroy": true,
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
						action: this_id,
						year: year,
						month: month
					},
					
				}
			});

	};

});