$(document).ready(function() {
	var flag = true;
	
	var revenue_options = {
		legend: {
			display: false
		},
		tooltips: {
			mode: 'index',
			intersect: false,
			callbacks: {
				  label: function(tooltipItem, data) {
					  return tooltipItem.yLabel.toFixed().replace(/\B(?=(\d{3})+(?!\d))/g, '$&,');
				  }
			  }
		},
		responsive: true,
		scales: {
			xAxes: [{
				stacked: true,
			}],
			yAxes: [{
				stacked: true,
				ticks: {
					beginAtZero:true,
					callback: function(value, index, values) {
						return addCommas(value) + ' ' + translate.jpy;
					}
				}
			}]
		},
	}
	
	var dual_options = {
		responsive: true,
		legend: {
			position: 'top',
		},
		tooltips: {
			mode: 'index',
			intersect: false,
			callbacks: {
				  label: function(tooltipItem, data) {
					  return tooltipItem.yLabel.toFixed().replace(/\B(?=(\d{3})+(?!\d))/g, '$&,');
				  }
			  }
		},
		scales: {
			xAxes: [{
				stacked: false,
			}],
			yAxes: [{
				stacked: false,
				ticks: {
					beginAtZero:true,
					callback: function(value, index, values) {
						return addCommas(value) + ' ' + translate.jpy;
					}
				}
			}]
		},
	}
	
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
	
	if($('div').hasClass('js_chart_seven_days_chart')) {
		$('#seven_days_chart').addClass('active');
		setTimeout(function(){ $('#seven_days_chart').trigger('click');}, 400);
		
	}
	
	// when the user clicks revenue chart button
	$(document).on('click', '.revenue_button button', function() {
		$('.revenue_button button').removeClass('active');
		$(this).addClass('active');
		var this_id = $(this).attr('id');
		$('div[id^="js_chart_"]').hide();
		$('#js_chart_' + this_id).show();

		$.ajax({
			url: ajax_url,
			type: 'post',
			data: {
				action: this_id
			},
			success: function(response){
				var rvn = document.getElementById('revenue_' + this_id).getContext('2d');
				if(response['type']==='dual') {
					var data_sets = [{
								label: response['thisyear'],
								backgroundColor: 'rgba(1, 57, 118, 1)',
								borderColor: 'rgba(1, 57, 118, 1)',
								borderWidth: 1,
								data: response['data'] 
							},{
								label: response['lastyear'],
								backgroundColor: 'rgba(248, 148, 28, 1)',
								borderColor: 'rgba(248, 148, 28, 1)',
								borderWidth: 1,
								data: response['last_data'] 
							}];
					var option_set = dual_options;
				} else {
					var data_sets = [{
								backgroundColor: '#013976',
								data: response['data'] 
							}];
					var option_set = revenue_options;
				}
				var revenueChart = new Chart(rvn, {
					type: 'bar',
					data: {
						labels: response['datasets_label'],
						datasets: data_sets
							
					},
					options: option_set
				});
			}
		});
	});
	
	// when the user clicks on refresh _member_create.php
	$(document).on('click', '.refresh', function() {
		$('.rotate').toggleClass('down')  ; 
		$.ajax({
			url: ajax_url,
			type: 'post',
			data: {
				'password': 'random'
			},
			success: function(response){
				$('#User_Password').val(response['data']);
			}
		});
	});
	
	// show hide password _member_create.php
	$("#show_hide_password a").on('click', function(event) {
        event.preventDefault();
        if($('#show_hide_password input').attr("type") == "text"){
            $('#show_hide_password input').attr('type', 'password');
            $('#show_hide_password i').addClass( "fa-eye-slash" );
            $('#show_hide_password i').removeClass( "fa-eye" );
        }else if($('#show_hide_password input').attr("type") == "password"){
            $('#show_hide_password input').attr('type', 'text');
            $('#show_hide_password i').removeClass( "fa-eye-slash" );
            $('#show_hide_password i').addClass( "fa-eye" );
        }
    });
	
	if($('div').hasClass('price_setting')) {
		var vat_default = parseInt($('.vat').val());
		var price_default = parseInt($('.price').val());
		vat_default = (isNaN(vat_default) ? 0 : vat_default);
		var vat_df = (vat_default * price_default)/100;
		var total_df = price_default + vat_df;
		
		$('.total_element').text(total_df);
	}
	
	//price setting _price-setting.php
	$(document).on('input', '.price', function() {
		var vat_input = parseInt($('.vat').val());
		var price_input = parseInt($(this).val());
		vat_input = (isNaN(vat_input) ? 0 : vat_input);
		var vat = (vat_input * price_input)/100;
		var total = price_input + vat;
		
		$('.total_element').text(total);
			
	});
	
	$(document).on('input', '.vat', function() {
		var price_input = parseInt($('.price').val());
		var vat_input = parseInt($(this).val());
		vat_input = (isNaN(vat_input) ? 0 : vat_input);
		var vat = (vat_input * price_input)/100;
		var total = price_input + vat;
		
		$('.total_element').text(total);
			
	});
	
	//urgent_price setting _price-setting.php
	$(document).on('input', '.urgent_price', function() {
		var price_input = parseInt($(this).val());
		var get_price = parseInt($('.hidden_price').val());
		var get_vat = parseInt($('.hidden_vat').val());
		var vat = (get_vat * get_price)/100;
		var total_price = get_price + vat;
		var total = (price_input * total_price)/100;
		
		$('.total_element_urgent').text(total);
			
	});
	// Switch show hide price
	$('.show_price').on( 'click', function () {
		if(flag) {
			flag = false;
			var id = parseInt($(this).attr('data-id'));
			$.ajax({
				url: ajax_url,
				type: 'post',
				data: {
					action:'show_price_on_off',
					id: id
				},
				success: function(response){
					flag = true;
				}
			});
		}
	});
	
	// Customer list CSV
	$('.customer_list_download').on( 'click', function () {
		$.fileDownload(ajax_url,{
			httpMethod: 'post',
			data: {
				action: 'customer_list_download',
			},
			successCallback: function (url) {
				//insert success code

			},
			failCallback: function (html, url) {
				//insert fail code
			}
		});
	});
	
	// Revenue CSV
	$('.revenue_download').on( 'click', function () {
		var yearmonth = $('.month_to_show').val();
		$.fileDownload(ajax_url,{
			httpMethod: 'post',
			data: {
				action: 'revenue_download',
				yearmonth: yearmonth
			},
			successCallback: function (url) {
				//insert success code

			},
			failCallback: function (html, url) {
				//insert fail code
			}
		});
	});
	
	//Password to download shippable file
	
	$('.download_file_able').on( 'click', function () {
		if(flag) {
		flag = false;
			var detail_id = parseInt($(this).attr('data-id'));
			
			$.confirm({
				title: translate.password_form,
				content: '' +
				'<form action="" class="formName">' +
				'<div class="form-group">' +
				'<label>'+translate.enter_password+'</label>' +
				'<input type="password" placeholder="'+translate.password_placeholder+'" class="name form-control" required />' +
				'</div>' +
				'</form>',
				buttons: {
					formSubmit: {
						text: translate.submit_btn,
						btnClass: 'btn-blue',
						action: function () {
							var name = this.$content.find('.name').val();
							if(!name){
								$.alert({
										title: false,
										closeIcon: false,
										//autoClose: 'confirm|6000',
										content: translate.invalid_password,
										buttons: {
											confirm: {
												text: translate.close_btn,
											}
										}
									});
								return false;
							}
							/*begin ajax >>*/
							$.ajax({
								type: 'post',
								data:{
									action: 'shippable_download_password',
									password_entered: name,
									id: detail_id
								},  
								dataType: 'json',
								
								url: ajax_url,
								success: function(response) {						
									if(response["status"]==1)
									{ 	
										$.fileDownload(ajax_url,{
											httpMethod: 'post',
											data: {
												action: 'shippable_download',
												id: detail_id
											},
											successCallback: function (url) {
												//insert success code

											},
											failCallback: function (html, url) {
												//insert fail code
											}
										});							
									}else
									{
										$.alert({
										title: false,
										closeIcon: false,
										content: translate.wrong_password,
										buttons: {
											confirm: {
												text: translate.close_btn,
											}
										}
									});
										return false;						
									}
									flag = true;
								}
							});
							/*End ajax << */							
							flag = true;
						}
						
					},
					cancel: {
							text: translate.cancel_btn,
							action: function(){
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
				}
			}); /*End confirm << */
			
		}
		
	});
	
	/*re-order click*/
	$('.reorder_btn').on( 'click', function () {
		$('#modalOrder').modal({
			show: true  
		  });
	});
	
	$("#logo_img").change(function() {
			var filename = this.files[0].name;
			$('.logo-input').val(filename);
			$('.logo-photo').remove();	
		
	});	
	
});