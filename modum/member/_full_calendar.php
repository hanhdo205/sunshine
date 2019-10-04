<?php
if( $_SESSION["Free"]==1)
{
  $html->redirectURL("/system");
}else
{

?>
<link rel="stylesheet" href="/js/datetimepicker/jquery.datetimepicker.css">
<script src="/js/datetimepicker/jquery.datetimepicker.full.js"></script>
<style>
.xdsoft_datetimepicker.xdsoft_noselect.xdsoft_ {
    z-index: 99999999999999;
}
.fc-time {
	display: none;
}
</style>
<script>


 $(document).ready(function() {

  var date = new Date();
  var d = date.getDate();
  var m = date.getMonth();
  var y = date.getFullYear();


  var calendar = $('#calendar').fullCalendar({
   locale: '<?php echo $_SESSION['language'];?>',
   <?php 
	if($rowgetInfo["roles_id"]!=15){ ?>
		editable: true,
	<?php } else { ?>
		editable: false,
	<?php } ?>
   header: {
    left: 'prev,next today',
    center: 'title',
    right: 'month,agendaWeek,agendaDay'
   },


   events: "/modum/member/events.php",
	eventColor: '#e5d7b4',

   eventRender: function(event, $el) {
	    var event_desc = event.description;
	    var event_pic = event.pic;
		if(event_desc !== "" ) {event_desc = "<br>" + event.description;}
		if(event_pic !== "" ) {
			event_pic = '<img class="align-self-start mr-3" src="/upload/' + event_pic + '" ' + 
             'alt="' + event.title + '" width="100">';
			}
		
		var event_content = "";
	    if (event.all_day === 'true') {
			 event.allDay = true;
			 event_content = '<div class="media">' + event_pic + '<div class="media-body"><strong><?php echo _DATE;?>:</strong> ' + $.fullCalendar.formatDate(event.start, "MM/DD") + event_desc + '</div></div>';
			 
			} else {
			 event.allDay = false;
			 event_content = '<div class="media">' + event_pic + '<div class="media-body"><strong><?php echo _START;?>:</strong> ' + $.fullCalendar.formatDate(event.start, "MM/DD HH:mm") + '<br><strong><?php echo _END;?>:</strong> ' + $.fullCalendar.formatDate(event.end, "MM/DD HH:mm") + event_desc + '</div></div>';
			}
	   
      $el.popover({
        title: event.title,
        content: event_content,
        trigger: 'hover',
        placement: 'top',
		html: true,
        image: true,
        container: 'body'
      });
    },
	<?php 
	if($rowgetInfo["roles_id"]!=15){ ?>
		selectable: true,
		selectHelper: true,
	<?php } else { ?>
		selectable: false,
		selectHelper: false,
	<?php } ?>
   select: function(start, end, allDay) {
   //var title = prompt('Event Title:');
   
   $.confirm({
		boxWidth: '50%',
		useBootstrap: false,
		closeIcon: false,
		title: '<?php echo _ADDEVENT;?>',
		content: '' +
		'<form id="eventform" action="" class="formName">' +
		'<div class="form-group">' +
		'<label><?php echo _EVENTITLE;?></label>' +
		'<input type="text" name="title" placeholder="<?php echo _EVENTITLE;?>" class="title form-control" required />' +
		'</div>' +
		'<input type="hidden" id="allday_text" name="allday_text" value="">' +
		'<div class="row allday">' +
			'<div class="col-md-12">' +
					'<div class="form-check checkbox">' +
						'<input type="checkbox" id="allday" class="form-check-input" name="allday" value="true">' +
						'<label class="form-check-label" for="allday"><?php echo _ALLDAY;?></label>' +
					'</div>' +
			'</div>' +
		'</div>' +
		'<div class="row">' +
			'<div class="datetime col-md-6">' +
				'<div class="form-group">' +
					'<div class="input-group date" id="datetimepicker6">' +
						'<input type="text" id="start_date" class="form-control input-datepicker create_start_time" placeholder="<?php echo _TIMESTART;?>" />' +
					'</div>' +
				'</div>' +
			'</div>' +
			'<div class="datetime col-md-6">' +
				'<div class="form-group">' +
					'<div class="input-group date" id="datetimepicker7">' +
						'<input type="text" id="end_date" class="form-control input-datepicker create_end_time" placeholder="<?php echo _TIMEEND;?>" />' +
					'</div>' +
				'</div>' +
			'</div>' +
			'<div class="col-md-12">' +
				'<div class="form-group">' +
					'<div class="input-group date" id="textdesc">' +
						'<textarea id="description" class="form-control" style="border:1px solid #ced4da;width:100%;" placeholder="<?php echo _DESCRIPTION;?>"></textarea>' +
					'</div>' +
				'</div>' +
			'</div>' +
			'<div class="col-md-12">' +
				'<div class="form-group">' +
					'<div class="input-group" id="eventpic">' +
						'<input id="eventpicture" type="file" name="eventpic" />' +
					'</div>' +
				'</div>' +
			'</div>' +
		'</div>' +
		'</form>',
		buttons: {
			formSubmit: {
				text: '<?php echo _SUBMIT;?>',
				btnClass: 'btn-blue',
				action: function () {
					
					var create_start_time = this.$content.find('.create_start_time').val();
					var create_end_time = this.$content.find('.create_end_time').val();
					var title = this.$content.find('.title').val();
					var desc = this.$content.find('textarea#description').val();
					var all_day = this.$content.find('#allday_text').val();
					var file_data = $("#eventpicture").prop("files")[0];   
					var form_data = new FormData();
					
					if (all_day === 'true') {
						 allDay = true;
						} else {
						 allDay = false;
						 all_day = 'false';
						}
					
					if(!title){
						$.alert('<?php echo _TITLEBLANK;?>');
						return false;
					}if(!create_start_time){
						create_start_time = $.fullCalendar.formatDate(start, "Y-MM-DD HH:mm:ss");
					}if(!create_end_time){
						create_end_time = $.fullCalendar.formatDate(end, "Y-MM-DD HH:mm:ss");
					}if(!create_end_time && !create_start_time){
						all_day = 'true';
						allDay = true;
					}
					
					form_data.append("file", file_data);
					form_data.append("title", title);
					form_data.append("start", create_start_time);
					form_data.append("end", create_end_time);
					form_data.append("desc", desc);
					form_data.append("allday", all_day);
					
					$.ajax({
						   type: "POST",
						   url: '/modum/member/add_events.php',
						   data: form_data,
						   cache: false,
						   contentType: false,
						   processData: false,
						   dataType: 'text',
						   success: function(response) {						
									$.confirm({
											title: false,
											closeIcon: false,
											//autoClose: 'confirm|6000',
											content: '<?php echo _EVENTCREATED;?>',
											buttons: {
												confirm: {
													text: '<?php echo _CLOSED;?>',
													action: function(){
														window.location.reload(true);
													}
												}
											}
										});						
									return false;
								}
					   });
					   calendar.fullCalendar('renderEvent',
					   {
						   title: title,
						   start: create_start_time,
						   end: create_end_time,
						   description: desc,
						   allDay: allDay
					   },
					   true
					   );
				}
			},
			cancel: {
				text: '<?php echo _CANCEL;?>',
				//close
			},
		},
		onContentReady: function () {
			
				
			// bind to events
			var jc = this;
			$('#eventform :checkbox').change(function() {
				// this will contain a reference to the checkbox   
				if (this.checked) {
					// the checkbox is now checked 
					$('.datetime').hide();
					$('.create_start_time').val();
					$('.create_end_time').val();
					$('#allday_text').val('true');
				} else {
					// the checkbox is now no longer checked
					$('.datetime').show();
					$('#allday_text').val('false');
				}
			});
			//date range 1
			if ($('#start_date').length > 0 && $('#end_date').length > 0) {

				$('#start_date').datetimepicker({
					format: 'Y-m-d H:i',
					defaultDate: $.fullCalendar.formatDate(start, "Y-MM-DD"),
					defaultTime: '08:00',
					dayOfWeekStart:1,
					step:30,
					onShow: function (ct) {
						this.setOptions({
							maxDate: $('#end_date').val() ? $('#end_date').val().substring(0, 10) : false
						});
					},
				});
				$('#end_date').datetimepicker({
					format: 'Y-m-d H:i',
					defaultDate: $.fullCalendar.formatDate(start, "Y-MM-DD"),
					defaultTime: '17:00',
					dayOfWeekStart:1,
					step:30,
					onShow: function (ct) {
						this.setOptions({
							minDate: $('#start_date').val() ? $('#start_date').val().substring(0, 10) : false
						});
					},
				});
			}
			this.$content.find('form').on('submit', function (e) {
				// if the user submits the form by pressing enter in the field.
				e.preventDefault();
				jc.$$formSubmit.trigger('click'); // reference the button and click it
			});
		}
	});
   
   


   /*if (title) {
   var start = $.fullCalendar.formatDate(start, "Y-MM-DD HH:mm:ss");
   var end = $.fullCalendar.formatDate(end, "Y-MM-DD HH:mm:ss");
   $.ajax({
	   url: 'add_events.php',
	   data: 'title='+ title+'&start='+ start +'&end='+ end,
	   type: "POST",
	   success: function(json) {
	   alert('Added Successfully');
	   }
   });
   calendar.fullCalendar('renderEvent',
   {
	   title: title,
	   start: start,
	   end: end,
	   allDay: allDay
   },
   true
   );
   }*/
   calendar.fullCalendar('unselect');
   },

	<?php 
	if($rowgetInfo["roles_id"]!=15){ ?>
   editable: true,
   eventDrop: function(event, delta) {
   
   var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
   var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");				   
   $.ajax({
	   url: '/modum/member/update_events.php',
	   data: 'title='+ event.title+'&start='+ start +'&end='+ end +'&desc='+ event.description +'&id='+ event.id ,
	   type: "POST",
	   success: function(json) {
	    $.alert('<?php echo _DATAINSERTED;?>');
	   }
   });
   },
   eventClick: function(event) {
	   var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
	   var start_date = $.fullCalendar.formatDate(event.start, "Y-MM-DD");
	   var start_time = $.fullCalendar.formatDate(event.start, "HH:mm");
	   var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");				   
	   var end_date = $.fullCalendar.formatDate(event.end, "Y-MM-DD");				   
	   var end_time = $.fullCalendar.formatDate(event.end, "HH:mm");
	   var event_pic = '';
	   if(event.pic !=="") {
			event_pic = '<img src="/upload/' + event.pic + '" ' + 'alt="' + event.title + '" width="200" class="align-self-start mr-3">';
	   }
	   var checked = "";
	   var is_all_day = "";
	   if (event.allDay == true) {
		 checked = "checked";
		 is_all_day = "true";
		 start = start_date;
		 end = end_date;
		}
	   $.confirm({
		boxWidth: '50%',
		useBootstrap: false,
		closeIcon: false,
		title: '<?php echo _EDITEVENT;?>',
		content: '' +
		'<form id="eventform" action="" class="formName">' +
		
		'<input type="hidden" id="allday_text" name="allday_text" value="' + is_all_day + '">' +
		'<input type="hidden" id="event_id" name="event_id" value="' + event.id + '">' +
		'<div class="media">' +
			event_pic +
			'<div class="media-body">' +
				'<div class="form-group">' +
					'<div class="row">' +
						'<div class="col-md-12">' +
							'<label><?php echo _EVENTITLE;?></label>' +
							'<input type="text" name="title" placeholder="<?php echo _EVENTITLE;?>" class="title form-control" value="'+ event.title+'" required />' +
						'</div>' +
					'</div>' +
					'<div class="row">' +
						'<div class="col-md-12">' +
							'<div class="form-check checkbox">' +
								'<input type="checkbox" id="allday" class="form-check-input" name="allday" value="true" ' + checked + '>' +
								'<label class="form-check-label" for="allday"><?php echo _ALLDAY;?></label>' +
							'</div>' +
						'</div>' +
						'<div class="datetime col-md-6">' +
							'<div class="form-group">' +
								'<div class="input-group date" id="datetimepicker6">' +
									'<input type="text" id="start_date" class="form-control input-datepicker start_date" placeholder="<?php echo _TIMESTART;?>" value="'+start+'" />' +
								'</div>' +
							'</div>' +
						'</div>' +
						'<div class="datetime col-md-6">' +
							'<div class="form-group">' +
								'<div class="input-group date" id="datetimepicker7">' +
									'<input type="text" id="end_date" class="form-control input-datepicker end_date" placeholder="<?php echo _TIMEEND;?>" value="'+end+'" />' +
								'</div>' +
							'</div>' +
						'</div>' +
					'</div>' +
					
					'<div class="input-group date" id="textdesc">' +
						'<textarea id="description" class="form-control" style="border:1px solid #ced4da;width:100%;" placeholder="<?php echo _DESCRIPTION;?>">'+ event.description+'</textarea>' +
						'<input id="eventpicture" type="file" name="eventpic" />' +
					'</div>' +
				'</div>' +
				'<div class="form-group">' +
					
				'</div>' +
			'</div>' +
		'</div>' +
		'</form>',
		buttons: {
			formSubmit: {
				text: '<?php echo _SUBMIT;?>',
				btnClass: 'btn-blue',
				action: function () {
					
					var title = this.$content.find('.title').val();
					var event_id = this.$content.find('#event_id').val();
					var desc = this.$content.find('textarea#description').val();
					var new_start = this.$content.find('.start_date').val();
					var new_end = this.$content.find('.end_date').val();
					var all_day = this.$content.find('#allday_text').val();
					var file_data = $("#eventpicture").prop("files")[0];  
					var form_data = new FormData();
					if (all_day === 'true') {
					 allDay = true;
					} else {
					 allDay = false;
					}
					if(!title){
						$.alert('<?php echo _TITLEBLANK;?>');
						return false;
					}
					
					form_data.append("file", file_data);
					form_data.append("title", title);
					form_data.append("start", new_start);
					form_data.append("end", new_end);
					form_data.append("desc", desc);
					form_data.append("allday", all_day);
					form_data.append("id", event_id);
					
					$.ajax({
						   type: "POST",
						   url: '/modum/member/update_events.php',
						   data: form_data,
						   cache: false,
						   contentType: false,
						   processData: false,
						   dataType: 'text',
						   success: function(response) {						
								$('#calendar').fullCalendar('removeEvents', event.id);
								$.confirm({
									title: false,
									closeIcon: false,
									//autoClose: 'confirm|6000',
									content: '<?php echo _DATAINSERTED;?>',
									buttons: {
										confirm: {
											text: '<?php echo _CLOSED;?>',
											action: function(){
												window.location.reload(true);
											}
										}
									}
								});	
								//$.alert('<?php echo _DATAINSERTED;?>');
								return false;
						   }
					   });
					   calendar.fullCalendar('renderEvent',
					   {
						   title: title,
						   start: new_start,
						   end: new_end,
						   description: desc,
						   //pic: '/upload/' . event.pic,
						   allDay: allDay
					   },
					   true
					   );
					   
				}
			},
			somethingElse: {
				text: '<?php echo _DELETEEVENT;?>',
				btnClass: 'btn-danger',
				action: function(){
					$.confirm({
						title: false,
						closeIcon: false,
						content: '<?php echo _RUSUREDEL;?>',
						buttons: {
							confirm: {
								text: '<?php echo _YES;?>',
								action: function(){
									$.ajax({
										type: "POST",
										url: "/modum/member/delete_event.php",
										data: "&id=" + event.id,
										 success: function(json) {
											 $('#calendar').fullCalendar('removeEvents', event.id);
											  $.alert('<?php echo _EVENTDELETED;?>');
														return false;}
									});
								}
							},
							cancel: {
								text: '<?php echo _NOT;?>',
							}
						}
					});
				}
			},
			cancel: {
				text: '<?php echo _CANCEL;?>',
				//close
			}
		},
		onContentReady: function () {
			
				
			// bind to events
			var jc = this;
			
			$('#eventform :checkbox').change(function() {
				// this will contain a reference to the checkbox   
				if (this.checked) {
					// the checkbox is now checked 
					
					$('.start_date').val();
					$('.end_date').val();
					//$('.datetime').hide();
					$('#allday_text').val('true');
				} else {
					// the checkbox is now no longer checked
					//$('.datetime').show();
					$('#allday_text').val('false');
				}
			});
			//date range 1
			if ($('#start_date').length > 0 && $('#end_date').length > 0) {

				$('#start_date').datetimepicker({
					format: 'Y-m-d H:i',
					defaultTime: start_time,
					dayOfWeekStart:1,
					defaultDate: start_date,
					step:30,
					onShow: function (ct) {
						this.setOptions({
							maxDate: $('#end_date').val() ? $('#end_date').val().substring(0, 10) : false
						});
					},
				});
				$('#end_date').datetimepicker({
					format: 'Y-m-d H:i',
					defaultDate: end_date,
					defaultTime: end_time,
					dayOfWeekStart:1,
					step:30,
					onShow: function (ct) {
						this.setOptions({
							minDate: $('#start_date').val() ? $('#start_date').val().substring(0, 10) : false
						});
					},
				});
			}
			this.$content.find('form').on('submit', function (e) {
				// if the user submits the form by pressing enter in the field.
				e.preventDefault();
				jc.$$formSubmit.trigger('click'); // reference the button and click it
			});
		}
	});
	/*var decision = confirm("Do you really want to do that?"); 
	if (decision) {
	$.ajax({
		type: "POST",
		url: "/modum/member/delete_event.php",
		data: "&id=" + event.id,
		 success: function(json) {
			 $('#calendar').fullCalendar('removeEvents', event.id);
			  $.alert('<?php echo _DATAINSERTED;?>');
						return false;}
	});
	}*/
  	},
   /*eventResize: function(event) {
	var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
	var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");		
	   $.ajax({
		   url: '/modum/member/update_events.php',
		   data: 'title='+ event.title+'&start='+ start +'&end='+ end +'&desc='+ event.description +'&id='+ event.id ,
		   type: "POST",
		   success: function(json) {
			$.alert('<?php echo _DATAINSERTED;?>');
		   }
	   });
	}*/
   <?php } ?>
  });
  
   
  
 });


</script>

<section id="main">
	<!-- WRAP -->
	<div class="wrap">

     <!-- USERMENU -->
		
		<!-- /USERMENU -->
		<!-- CONTENT -->
	 <section id="content">
            <div id="main-container">
                  <div id="page-content" style="min-height: 318px;">
                        <div class="block">
                            <div class="block-title">
                                <h2><?php echo _CALLENDAR;?></h2>
                            </div>

                            <div id='calendar'></div>

                        </div>
                    </div>
					
            </div>
    <div class="clearfix"></div>
     </section>
</div>
<div class="clearfix"></div>
</section>
<div class="clearfix"></div>
<?php
}
?>
