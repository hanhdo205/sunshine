$(document).ready(function() {
	var selected_year = $('.selected_year').val();
	if(!selected_year) {
		selected_year = (new Date).getFullYear();
		$('.selected_year').val(selected_year);
	}
	
	// draw value on load
	doDrawValue(selected_year);
	
	$('.goal_setting').datepicker( {
        showButtonPanel: true,
        dateFormat: 'yy',
		stepMonths: 12,
        onClose: function(dateText, inst) { 
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).datepicker('setDate', new Date(year, 0, 1));
			
        },
		onChangeMonthYear: function (year, month, inst ) {
			$('.selected_year').val(year);
			$('.goal-setting-input,.goal-setting-button').prop('disabled',true);
			$('.goal-setting-input').val('');
			doDrawValue(year);
		}
	});
	
	//setDate to selected_year
	var d = new Date();
	var currMonth = d.getMonth();
	var currYear = selected_year;
	var startDate = new Date(currYear, currMonth, 1);
	$('.goal_setting').datepicker("setDate",startDate);

	// enable/disable input field
	$('#editable').on( 'click', function () {
		$('.goal-setting-input,.goal-setting-button').prop('disabled', function(i, v) { return !v; });
	});
});

// function to do ajax
function doDrawValue(year) {
	$.ajax({
			url: ajax_url,
			type: 'post',
			data: {
				action: 'goal_setting_value',
				year: year
			},
			success: function(response){
				$.each( response, function( key, value ) {
					  $.each( value, function( i, val ) {
						  $('.' + i + '_' + key).val(val);
					  });
				});
			}
	});
}