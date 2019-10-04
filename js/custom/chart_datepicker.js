$(document).ready(function() {
	
	$('#operator_chart_daterange').on('apply.daterangepicker', function(ev, picker) {
		var start_date = $('#operator_chart_daterange').data('daterangepicker').startDate.format('YYYY/MM/DD 00:00');
		var end_date = $('#operator_chart_daterange').data('daterangepicker').endDate.format('YYYY/MM/DD 23:59');
		var this_id = $(this).attr('data-id');
		$('#quantity_' + this_id).remove();
		$('#' + this_id + ' div').remove();
		setTimeout(function(){
			doDrawChart(start_date,end_date,'operator_chart',1);
		}, 500);
	});
	$('#customer_chart_daterange').on('apply.daterangepicker', function(ev, picker) {
		var start_date = $('#customer_chart_daterange').data('daterangepicker').startDate.format('YYYY/MM/DD 00:00');
		var end_date = $('#customer_chart_daterange').data('daterangepicker').endDate.format('YYYY/MM/DD 23:59');
		var this_id = $(this).attr('data-id');
		$('#quantity_' + this_id).remove();
		$('#' + this_id + ' div').remove();
		var this_type = $('.chart_type_button').find('button.active').attr('data-type');
		setTimeout(function(){
			doDrawChart(start_date,end_date,'customer_chart',1,this_type);
		}, 500);
	});
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
	function doDrawChart(start,end, this_id, pagenumber='',chart_type='') {
		var translate_unit = translate.qty;
		if(chart_type == 'amount_chart') {
			translate_unit = translate.jpy;
		}	
		$.ajax({
				url: ajax_url,
				type: 'post',
				data: {
					action: this_id,
					start: start,
					end: end,
					page: pagenumber,
					chart_type: chart_type
				},
				success: function(response){
					$('#' + this_id).append('<canvas id="quantity_' + this_id + '" class="chartjs"></canvas>');
					//console.log(response['datasets_label']);
					var response_data = parseInt(response['total_row']);
					console.log('.' + this_id + '_page');
					if(response_data > 10) {
						$('.' + this_id + '_page').attr( "style", "display: flex !important;" );
						if(response_data < 21) {
							$('.page_3').remove();
						}
					} else {
						$('.' + this_id + '_page').attr( "style", "display: none !important;" );
					}
					var data = {
						labels: response['datasets_label'],
						datasets: [response['data']]
					};
					var options = {
						legend: {
									display: false
								},
						maintainAspectRatio: true,
						spanGaps: false,
						elements: {
							line: {
								tension: 0.000001
							}
						},
						tooltips: {
							mode: 'index',
							intersect: false,
							callbacks: {
								  label: function(tooltipItem, data) {
									  return tooltipItem.xLabel.toFixed().replace(/\B(?=(\d{3})+(?!\d))/g, '$&,');
								  }
							  }
						},
						responsive: true,
						scales: {
							yAxes: [{
								stacked: true,
							}],
							xAxes: [{
								stacked: true,
								ticks: {
									beginAtZero:true,
									userCallback: function(value, index, values) {
									if (Math.floor(value) === value) {
										 return addCommas(value) + ' ' + translate_unit;
									 }
									//return addCommas(value) + ' ' + translate_unit;
									}
								}
							}]
						},
						plugins: {
							filler: {
								propagate: false
							},
							'samples-filler-analyser': {
								target: 'chart-analyser'
							}
						}
					};
					var line_chart = document.getElementById('quantity_' + this_id).getContext('2d');
					var chart = new Chart(line_chart, {
						type: 'horizontalBar',
						data: data,
						options: options
					});
				}
			});
	};
	var o_ranges = $('#operator_chart_daterange').val();
	var c_ranges = $('#customer_chart_daterange').val();
	
	var o_range_arr = o_ranges.split(' - ');
	var o_start_date = o_range_arr[0] + '00:00';
	var o_end_date = o_range_arr[1] + '23:59';
	
	var c_range_arr = c_ranges.split(' - ');
	var c_start_date = c_range_arr[0] + '00:00';
	var c_end_date = c_range_arr[1] + '23:59';
	setTimeout(function(){
		doDrawChart(o_start_date,o_end_date,'operator_chart',1);
	}, 500);
	setTimeout(function(){
		doDrawChart(c_start_date,c_end_date,'customer_chart',1);
	}, 600);
	
	// $('.horizontal_chart').datepicker( {
		// showButtonPanel: true,
		// dateFormat: 'MM yy',
		// maxDate: new Date,
		// onClose: function(dateText, inst) { 
			// $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
		// },
		// onChangeMonthYear: function (year, month, inst ) {
			// var this_id = $(this).attr('data-id');
			// $('#quantity_' + this_id).remove();
			// $('#' + this_id + ' div').remove();
			// $('.selected_month').val(month);
			// $('.selected_year').val(year);
			// setTimeout(function(){
				// doDrawChart(year,month,this_id,1);
			// }, 500);
			
		// }
	// });
	
	$('.chart_pagination a').on( 'click', function () {
		var c_ranges = $('#customer_chart_daterange').val();
		var c_range_arr = c_ranges.split(' - ');
		var c_start_date = c_range_arr[0] + '00:00';
		var c_end_date = c_range_arr[1] + '23:59';
		var this_page = parseInt($(this).attr('data-page'));
		var this_id = $(this).attr('data-id');
		$('#quantity_' + this_id).remove();
		$('#' + this_id + ' div').remove();
		$('.page-item').removeClass('active');
		$('.page-item a[data-page=' + this_page + ']').parent().addClass("active");
		setTimeout(function(){
			doDrawChart(c_start_date,c_end_date,'customer_chart',this_page);
		}, 500);
	});
	
	// when the user clicks revenue chart button
	$(document).on('click', '.chart_type_button button', function() {
		$('.chart_type_button button').removeClass('active');
		$(this).addClass('active');
		var c_ranges = $('#customer_chart_daterange').val();
		var c_range_arr = c_ranges.split(' - ');
		var c_start_date = c_range_arr[0] + '00:00';
		var c_end_date = c_range_arr[1] + '23:59';
		var this_id = $(this).attr('data-id');
		var this_type = $(this).attr('data-type');
		$('#quantity_' + this_id).remove();
		$('#' + this_id + ' div').remove();
		$('.page-item').removeClass('active');
		$('.page-item a[data-page=1]').parent().addClass("active");
		setTimeout(function(){
			doDrawChart(c_start_date,c_end_date,'customer_chart',1,this_type);
		}, 600);
	});
});