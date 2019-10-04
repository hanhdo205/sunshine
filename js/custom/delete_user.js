$(document).ready(function() {
	var flag = true;
	
	// delete user from list
	$(document).on( 'click', '.delete_user_from_list', function () {
		if(flag) {
			flag = false;
			var id = parseInt($(this).attr('data-id'));
			var action = $(this).attr('data-action');
			var this_row = $(this).parents('tr');
			
			$.alert({
				title: false,
				content: translate.sure,
				buttons: {
					confirm: {
						text: translate.yes_btn,
						action: function () {
							$.ajax({
								url: ajax_url,
								type: 'post',
								data: {
									action: action,
									id: id
								},
								success: function(response){
									flag = true;
									
									var $toast = toastr["success"](translate.member_deleted,translate.toast_title);
									
									 table.row( this_row ).remove().draw();
								}
							});
						}
					},
					cancel: {
						text: translate.cancel_btn,
						action: function() {
								flag = true;
							}
						}
					}
				
			});
		}
	});
});