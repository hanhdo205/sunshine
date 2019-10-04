$(document).ready(function() {
	var flag = true;

	// Responsible person action
	$(document).on( 'click', '.responsible_action', function () {
		if(flag) {
			flag = false;
			var first_name = '', last_name = '', id = '';
			var action = $(this).attr('data-action');
			var title = translate.add_new_title;
			if(action=='edit_responsible_person') {
				first_name = $(this).attr('data-first');
				last_name = $(this).attr('data-last');
				id = $(this).attr('data-id');
				title = translate.update_title;
			}
			var this_row = $(this).parents('tr');
			
			$.confirm({
				boxWidth: '50%',
				useBootstrap: false,
				title: title,
				content: '' +
				'<form action="" class="mt-3">' +
				'<div class="custom_confirm_form">' +
				'<div class="row">' +
				'<div class="col-sm-6"><div class="row"><label class="col col-form-label" for="first_name_input">'+translate.first_name+'</label>' +
				'<div class="col-md-10"><input type="text" placeholder="'+translate.first_name+'" id="first_name_input" class="first_name form-control" value="'+first_name+'"/></div></div></div>' +
				'<div class="col-sm-6 "><div class="row"><label class="col col-form-label" for="last_name_input">'+translate.last_name+'</label>' +
				'<div class="col-md-10"><input type="text" placeholder="'+translate.last_name+'" id="last_name_input" class="last_name form-control" value="'+last_name+'"/></div></div></div>' +
				'</div>' +
				'</div>' +
				'</form>',
				buttons: {
					formSubmit: {
						text: translate.submit_btn,
						btnClass: 'btn-blue',
						action: function () {
							first_name = this.$content.find('.first_name').val();
							last_name = this.$content.find('.last_name').val();
							if(!first_name || !last_name){
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
							$.ajax({
									url: ajax_url,
									type: 'post',
									data: {
										action: action,
										id: id,
										first_name: first_name,
										last_name: last_name
									},
									success: function(response){
										flag = true;
										if(action=='edit_responsible_person') {
											var $toast = toastr["success"](translate.member_updated,translate.toast_title);
											var cell = table.cell( this_row, ':eq(0)' );
											cell.data(first_name + '' + last_name).draw();
										} else {
											var $toast = toastr["success"](translate.member_created,translate.toast_title);
											table.row.add( [
												first_name + '' + last_name,
												'<a data-id="'+response['id']+'" data-action="edit_responsible_person" data-first="' + first_name + '" data-last="' + last_name + '"  class="responsible_action btn btn-effect-ripple btn-xs btn-secondary mr-2" data-toggle="tooltip" title="" style="overflow: hidden; position: relative;" data-original-title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a><a data-id="'+response['id']+'" class="delete_user_from_list text-white btn btn-effect-ripple btn-xs btn-danger" data-toggle="tooltip" title="" style="overflow: hidden; position: relative;" data-original-title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>'
											] ).draw( false );
										}
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
				}
			});
		}
	});
	
});