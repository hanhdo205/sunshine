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

	function doDrawChart(start,end, this_id, paginate='') {
		$.ajax({
				url: ajax_url,
				type: 'post',
				data: {
					action: this_id,
					start: start,
					end: end,
					page: paginate
				},
				success: function(response){
					$('#' + this_id).append('<canvas id="quantity_' + this_id + '" class="chartjs"></canvas>');
					//console.log(response['datasets_label']);
					//console.log(response['data']);
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
										 return addCommas(value) + ' ' + translate.qty;
									 }
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
	
	var o_range_arr = o_ranges.split(' - ');
	var o_start_date = o_range_arr[0] + '00:00';
	var o_end_date = o_range_arr[1] + '23:59';
	
	setTimeout(function(){
		doDrawChart(o_start_date,o_end_date,'operator_chart',1);
	}, 500);
	
});