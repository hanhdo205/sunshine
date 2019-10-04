$(document).ready(function() {
	var flag = true;
	toastr.options = {
			  "closeButton": true,
			  "debug": false,
			  "newestOnTop": false,
			  "progressBar": false,
			  "positionClass": "toast-top-right",
			  "preventDuplicates": false,
			  "onclick": null,
			  "showDuration": "300",
			  "hideDuration": "1000",
			  "timeOut": "5000",
			  "extendedTimeOut": "1000",
			  "showEasing": "swing",
			  "hideEasing": "linear",
			  "showMethod": "fadeIn",
			  "hideMethod": "fadeOut"
			};
	// Delete faq from list
	$(document).on( 'click', '.delete_faq_from_list', function () {
		if(flag) {
			flag = false;
			var id = parseInt($(this).attr('data-id'));
			var this_a = $(this);
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
									action:'delete_faq',
									id: id
								},
								success: function(response){
									flag = true;
									
									var $toast = toastr["success"](translate.post_deleted,translate.toast_title); 
									
									 table
									.row( this_a.parents('tr') )
									.remove()
									.draw();
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
	
	// Delete faq from list
	$(document).on( 'click', '.delete_category_faq_from_list', function () {
		if(flag) {
			flag = false;
			var id = parseInt($(this).attr('data-id'));
			var this_a = $(this);
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
									action:'delete_category_faq',
									id: id
								},
								success: function(response){
									flag = true;
									
									var $toast = toastr["success"](translate.post_deleted,translate.toast_title); 
									
									 table
									.row( this_a.parents('tr') )
									.remove()
									.draw();
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
	
	// Delete post from list
	$(document).on( 'click', '.delete_post_from_list', function () {
			
		if(flag) {
			flag = false;
			var id = parseInt($(this).attr('data-id'));
			var this_a = $(this);
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
									action:'delete_post',
									id: id
								},
								success: function(response){
									flag = true;

									 var $toast = toastr["success"](translate.post_deleted,translate.toast_title); 

									 table
									.row( this_a.parents('tr') )
									.remove()
									.draw();
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