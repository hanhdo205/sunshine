$(document).ready(function () {
	var flag = true;
	$(document).on('click', '.accordion-icon, .order-accordion', function() {
		$(this).find('.rotate').toggleClass('down')  ; 
	});
	if ($('#paid_date').val().length > 0) {
		$('.payment_method, .paid_field, .quantity_field, .vat_field, .sub_total, .total_amount').removeAttr("disabled");
	}
	//var d = new Date();
	$('#paid_date').datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: "yy/mm/dd",
		onSelect: function(dateText) {
		  $('.payment_method, .paid_field, .quantity_field, .vat_field, .sub_total, .total_amount').removeAttr("disabled");
		  $(this).change();
		}
		//minDate: d.getHours() >= 15 ? 1 : 0
	});
	$('.desired_date').datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: "yy/mm/dd"
	});
	$("#data-log").fancybox({
        closeClick  : true,
        openEffect  : 'fade',
        closeEffect : 'fade',
        scrolling   : false,
        padding     : 0,
        type		: 'iframe',		
		autoScale: false,
		autoSize: false,
		smallBtn : true,
		toolbar  : false
        });


	// when the user clicks on download button
	$(document).on('click', '.download_file_manufacture', function() {
		if(flag) {
			var order_id = parseInt($(this).attr('data-id'));
			var user_id = parseInt($(this).attr('data-user'));
			var total_task = parseInt($('.total_task').text());
			var file_name = $(this).attr('data-file');
			flag = false;
			$.fileDownload(ajax_url,{
				httpMethod: 'post',
				data: {
					action: 'manufature_download',
					file_name: file_name
				},
				successCallback: function (url) {
					//insert success code
				},
				failCallback: function (html, url) {
					//insert fail code
				}
			});
			$.ajax({
				url: ajax_url,
				type: 'post',
				data: {
					action: 'download_file',
					order_id: order_id,
					user_id: user_id
				},
				success: function(response){
					if(response['status']==1) {
						$('.task-item').find('[data-id="'+order_id+'"]').remove();	
						$('.total_task').text(total_task - 1);
						$('.download_file').removeClass('has-error');
					}
				}
			});
			flag = true;
		}
	});
	
	// when the user clicks on download button
	$(document).on('click', '.download_file_shippable', function() {
		if(flag) {
			var order_id = parseInt($(this).attr('data-id'));
			var file_name = $(this).attr('data-ship');
			flag = false;
			$.fileDownload(ajax_url,{
				httpMethod: 'post',
				data: {
					action: 'check_file_download',
					file_name: file_name
				},
				successCallback: function (url) {
					//insert success code
				},
				failCallback: function (html, url) {
					//insert fail code
				}
			});
			$.ajax({
				url: ajax_url,
				type: 'post',
				data: {
					action: 'check_file',
					order_id: order_id,
				},
				success: function(response){
					//insert success code
				}
			});
			flag = true;
		}
	});
});