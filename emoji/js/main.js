      
$(document).ready(function() {
	var flag = true;
	$(".emojionearea").emojioneArea({
		emojiPlaceholder: ":grinning:",
		searchPlaceholder: "Search",
		buttonTitle: "Use you TAB key to insert emoji faster",
		searchPosition: "bottom",
		pickerPosition: "bottom",
		saveEmojisAs: "shortname",
		autoHideFilters: false,
          events: {
            /**
             * @param {jQuery} editor EmojioneArea input
             * @param {Event} event jQuery Event object
             */
            focus: function (editor, event) {
              //console.log('event:focus');
            },
            /**
             * @param {jQuery} editor EmojioneArea input
             * @param {Event} event jQuery Event object
             */
            blur: function (editor, event) {
              //console.log('event:blur');
            },
            /**
             * @param {jQuery} editor EmojioneArea input
             * @param {Event} event jQuery Event object
             */
            mousedown: function (editor, event) {
              //console.log('event:mousedown');
            },
            /**
             * @param {jQuery} editor EmojioneArea input
             * @param {Event} event jQuery Event object
             */
            mouseup: function (editor, event) {
              //console.log('event:mouseup');
            },
            /**
             * @param {jQuery} editor EmojioneArea input
             * @param {Event} event jQuery Event object
             */
            click: function (editor, event) {
              //console.log('event:click');
            },
            /**
             * @param {jQuery} editor EmojioneArea input
             * @param {Event} event jQuery Event object
             */
            keyup: function (editor, event) {
              //console.log('event:keyup');
            },
            /**
             * @param {jQuery} editor EmojioneArea input
             * @param {Event} event jQuery Event object
             */
            keydown: function (editor, event) {
              //console.log('event:keydown');
            },
            /**
             * @param {jQuery} editor EmojioneArea input
             * @param {Event} event jQuery Event object
             */
            keypress: function (editor, event) {
              //console.log('event:keypress');
            },
            /**
             * @param {jQuery} editor EmojioneArea input
             * @param {Event} event jQuery Event object
             */
            paste: function (editor, event) {
              //console.log('event:paste');
            },
            /**
             * @param {jQuery} editor EmojioneArea input
             * @param {Event} event jQuery Event object
             */
            change: function (editor, event) {
              //console.log('event:change');
            },
            /**
             * @param {jQuery} filter EmojioneArea filter
             * @param {Event} event jQuery Event object
             */
            filter_click: function (filter, event) {
              //console.log('event:filter.click, filter=' + filter.data("filter"));
            },
            /**
             * @param {jQuery} button EmojioneArea emoji button
             * @param {Event} event jQuery Event object
             */
            emojibtn_click: function (button, event) {
              //console.log('event:emojibtn.click, emoji=' + button.children().data("name"));
            },
            /**
             * @param {jQuery} button EmojioneArea left arrow button
             * @param {Event} event jQuery Event object
             */
            arrowLeft_click: function (button, event) {
              //console.log('event:arrowLeft.click');
            },
            /**
             * @param {jQuery} button EmojioneArea right arrow button
             * @param {Event} event jQuery Event object
             */
            arrowRight_click: function (button, event) {
              //console.log('event:arrowRight.click');
            }
          }
	});
	
	$( ".emojionediv" ).each(function( index ) {
		var input = $(this).html();
		var output = emojione.shortnameToImage(input);
		$(this).html(output);
	});
	
		// when the user clicks on like
		$(document).on('click', '.like', function() {
		//$('.like').on('click', function(){
			var postid = $(this).data('id');
			    $post = $(this);

			$.ajax({
				url: ajax_url,
				type: 'post',
				data: {
					'liked': 1,
					'postid': postid
				},
				success: function(response){
					$post.parent().find('span.likes_count').text(" " + response + " " + likes);
					$post.addClass('hide');
					$post.siblings().removeClass('hide');
				}
			});
		});

		// when the user clicks on unlike
		$(document).on('click', '.unlike', function() {
		//$('.unlike').on('click', function(){
			var postid = $(this).data('id');
		    $post = $(this);

			$.ajax({
				url: ajax_url,
				type: 'post',
				data: {
					'unliked': 1,
					'postid': postid
				},
				success: function(response){
					$post.parent().find('span.likes_count').text(" " + response + " " + likes);
					$post.addClass('hide');
					$post.siblings().removeClass('hide');
				}
			});
		});
				
		// when the user clicks on dismis_casestudy
		$('.dismis_casestudy').on('click', function(){
			var caseid = $(this).data('id');
			var dismisid = $(this).data('user');

			$.ajax({
				url: ajax_url,
				type: 'post',
				data: {
					'caseid': caseid,
					'dismisid': dismisid
				},
				success: function(response){
					//nothing to show
				}
			});
		});
		
		// when the user clicks on dismis_infomation
		$('.dismis_infomation').on('click', function(){
			var infoid = $(this).data('id');
			var dismisid = $(this).data('user');
			
			$.ajax({
				url: ajax_url,
				type: 'post',
				data: {
					'infoid': infoid,
					'dismisid': dismisid
				},
				success: function(response){
					//nothing to show
				}
			});
		});
		
		// when the user clicks on comm_reply
		$(document).on('click', '.comm_reply', function() {
			if(flag) {
				var comm_id = parseInt($(this).attr('data-id'));
				$('#loading_' + comm_id).show();
				$('#commentform_reply').remove();
				$('#commentform_edit').remove();
				flag = false;
				$.ajax({
					url: ajax_url,
					type: 'post',
					data: {
						'reply_comm_id': comm_id
					},
					success: function(response){
						flag = true;
						$('#loading_' + comm_id).hide();
						$(response).insertAfter('#content_' + comm_id);
						$(".emojionearea_reply").emojioneArea({
							emojiPlaceholder: ":smile_cat:",
							searchPlaceholder: "Search",
							buttonTitle: "Use you TAB key to insert emoji faster",
							searchPosition: "bottom",
							pickerPosition: "bottom",
							saveEmojisAs: "shortname",
							autoHideFilters: false,
						});
					}
				});
			}
		});
		
		// when the user clicks on rep_reply
		$(document).on('click', '.rep_reply', function() {
			if(flag) {
				var comm_id = parseInt($(this).attr('data-id'));
				var sub_id = parseInt($(this).attr('data-sub'));
				var tag = $(this).attr('data-user');
				$('#loading_' + sub_id).show();
				$('#commentform_reply').remove();
				$('#commentform_edit').remove();
				flag = false;
				$.ajax({
					url: ajax_url,
					type: 'post',
					data: {
						'reply_sub_id': comm_id,
						'user_tag': tag
					},
					success: function(response){
						flag = true;
						$('#loading_' + sub_id).hide();
						$(response).insertAfter('#content_' + sub_id);
						$(".emojionearea_reply").emojioneArea({
							emojiPlaceholder: ":smile_cat:",
							searchPlaceholder: "Search",
							buttonTitle: "Use you TAB key to insert emoji faster",
							searchPosition: "bottom",
							pickerPosition: "bottom",
							saveEmojisAs: "shortname",
							autoHideFilters: false,
						});
					}
				});
			}
		});
		
		// when the user clicks on comm_edit
		$(document).on('click', '.comm_edit', function() {
			if(flag) {
				var comm_id = parseInt($(this).attr('data-id'));
				$('#loading_' + comm_id).show();
				$('#commentform_edit').remove();
				$('#commentform_reply').remove();
				flag = false;
				$.ajax({
					url: ajax_url,
					type: 'post',
					data: {
						'edit_comm_id': comm_id
					},
					success: function(response){
						flag = true;
						$('#loading_' + comm_id).hide();
						$(response).insertAfter('#content_' + comm_id);
						$('#content_' + comm_id).hide();
						$(".emojionearea_edit").emojioneArea({
							emojiPlaceholder: ":smile_cat:",
							searchPlaceholder: "Search",
							buttonTitle: "Use you TAB key to insert emoji faster",
							searchPosition: "bottom",
							pickerPosition: "bottom",
							saveEmojisAs: "shortname",
							autoHideFilters: false,
						});
					}
				});
			}
		});
		
		$(document).on('click', '#comm_cancel', function() {
			if($(this).closest("form").parent().find('.talk-bubble').is(":hidden")) {
				//console.log('hidden');
				$(this).closest("form").parent().find('.talk-bubble').show();
			}
			$(this).closest("form").remove();
		});
		
			// when the user clicks on more_rep
			$(document).on('click', '.more_rep', function() {
				if(flag) {
					var comm_id = parseInt($(this).attr('data-id'));
					var limit = parseInt($(this).attr('data-limit'));
					var start = parseInt($(this).attr('data-start'));
					var total = parseInt($(this).attr('data-total'));
					flag = false;
					$.ajax({
						url: ajax_url,
						type: 'post',
						data: {
							'rep_id': comm_id,
							'limit': limit,
							'start': start
						},
						success: function(response){
							flag = true;						
							$(response).insertBefore('#more_reply_' + comm_id);
							var newstart = parseInt($('#repbutton_' + comm_id).attr( 'data-start'));
							newstart = newstart + limit;
							var more = total - newstart;
							if(more < 3) {
								$('#repbutton_' + comm_id).html('<i class="fa fa-undo" aria-hidden="true"></i> ' + viewmore + ' ' + more + ' ' + replies);
							}
							$('#repbutton_' + comm_id).attr( 'data-start', newstart);
							//if(total-newstart < limit) $('#repbutton_' + comm_id).attr( 'data-limit', total-newstart);
							if($('#repbutton_' + comm_id).attr( 'data-start') > $('#repbutton_' + comm_id).attr( 'data-total'))
								$('#more_reply_' + comm_id).remove();
						}
					});
				}
			});
			
			// when the user clicks on more_comment
			$(document).on('click', '.more_comment', function() {
				if(flag) {
					var limit = parseInt($(this).attr('data-limit'));
					var start = parseInt($(this).attr('data-start'));
					var total = parseInt($(this).attr('data-total'));
					flag = false;
					$.ajax({
						url: ajax_url,
						type: 'post',
						data: {
							'comm_id':'more_comm',
							'limit': limit,
							'start': start
						},
						success: function(response){
							flag = true;						
							$('.comment-list').append(response);
							var newstart = parseInt($('#com_button').attr( 'data-start'));
							newstart = newstart + limit;
							var more = total - newstart;
							
							$('#com_button').attr( 'data-start', newstart);
							if($('#com_button').attr( 'data-start') > $('#com_button').attr( 'data-total'))
								$('#more_comment').remove();
						}
					});
				}
			});
			
		//scroll to comment #id	
		var header = $('.header-desktop').outerHeight();
		$('.notifi__item a').click(function(e) {
			
			//if(flag && $(this).data('id')) {
			if(flag) {
				var notiid = $(this).data('id');
				var notiuserid = $(this).data('user');
				var noti_item = $(this);
				flag = false;
				$.ajax({
					url: ajax_url,
					type: 'post',
					data: {
						'noticlickuserid': notiuserid,
						'notiid': notiid
					},
					success: function(response){
						flag = true;
						noti_item.parent().parent().addClass('clicked');
						noti_item.removeAttr('data-id');
						noti_item.removeAttr('data-user');
						$('.comment_content_div').html(response);
					}
				});
			}
			
			if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
				var target = $(this.hash);
				target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
				if (target.length) {
					if(window.innerWidth > 992){
						$('html,body').animate({
								scrollTop: target.offset().top - header
						}, 500);
					}
					else{
						$('html,body').animate({
							scrollTop: target.offset().top 
						}, 500);
					}
					return false;
				}
			}
		});
		
		// when the user clicks on mark_all_read
		$('.mark_all_read').on('click', function(){
			if(flag) {
				var userid = $(this).data('id');
				flag = false;
				$.ajax({
					url: ajax_url,
					type: 'post',
					data: {
						'allreaduserid': userid
					},
					success: function(response){
						flag = true;
						location.reload();
					}
				});
			}
		});

		// when the user clicks on dismis_notification
		$('.dismis_notification').on('click', function(){
			if(flag) {
				var notiid = $(this).data('id');
				var notiuserid = $(this).data('user');
				var quantity = parseInt($('.quantity').text());
				var noti_item = $(this);
				flag = false;
				$.ajax({
					url: ajax_url,
					type: 'post',
					data: {
						'notiid': notiid,
						'notiuserid': notiuserid
					},
					success: function(response){
						flag = true;
						noti_item.parent().remove();
						quantity = quantity - 1;
						if(quantity > 0)
							$('.quantity').text(quantity);
						else $('.quantity').remove();
					}
				});
			}
		});	
		
		$(document).ajaxComplete(function(){
			$( ".emojionediv" ).each(function( index ) {
				var input = $(this).html();
				var output = emojione.shortnameToImage(input);
				$(this).html(output);
			});
		});

});


