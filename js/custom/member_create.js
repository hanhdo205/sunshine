// DataTable Payment List
$(document).ready(function() {
	var flag = true;
	
	// CSV import
	$(document).on( 'click', '.csv_member_import', function () {
		if(flag) {
			flag = false;
			
			$.confirm({
				title: false,
				content: '' +
				'<form action="" class="custom_confirm_form">' +
				'<div class="form-group row">' +
				'<input id="csv_import" name="csv_file" type="file" style="visibility:hidden;" accept=".csv">' +
				'<div class="col-md-12"><span class="input-group div-select-stlinput_0"><input type="text" name="csv_import" class="csv_import input full upload form-control" placeholder="'+translate.choose_csv_label+'" value="" autocomplete="off" style="padding: 3px !important;background: #fff;"><span class="input-group-append"><label for="csv_import" class="btn btn-primary">'+translate.select_file_label+'</label></span></span></div>' +
				'</div>' +
				'</form>',
				buttons: {
					formSubmit: {
						text: translate.submit_btn,
						btnClass: 'btn-blue',
						action: function () {
							var fileType = ".csv";
							var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:])+("
									+ fileType + ")$");
							//if (!regex.test($("#csv_import").val().toLowerCase())) {
							if (!regex.test($("#csv_import").val())) {
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
							var file_data = $('#csv_import').prop('files')[0];   
							var form_data = new FormData();                  
							form_data.append('file', file_data);
							form_data.append('action', 'csv_import');
							$.ajax({
									url: ajax_url,
									type: 'post',
									dataType: 'text',  // what to expect back from the PHP script, if anything
									cache: false,
									contentType: false,
									processData: false,
									data: form_data,
									success: function(response){
										flag = true;
										var $toast = toastr["success"](translate.csv_imported,translate.toast_title);
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
					var fileSelectEle = document.getElementById('csv_import');
					fileSelectEle.onchange = function ()
					{
						//upload_image();
						if(fileSelectEle.value.length == 0) {
							$('.csv_import').val('');
						} else {
							$('.csv_import').val(fileSelectEle.files[0].name);
						}
					}
				}
			});
		}
	});
	
});
