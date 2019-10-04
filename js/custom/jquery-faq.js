$(document).ready(function() {
		var browseButton = function (context) {
		  var ui = $.summernote.ui;

		  // create button
		  var button = ui.button({
			contents: '<i class="fa fa-folder-open-o"/>',
			tooltip: 'Browse',
			click: function () {
			  // invoke insertText method with 'hello' on editor module.
			  //open up bootstrap modal      
				  $('#modalMedia').modal({
					show: true  
				  });
			}
		  });

		  return button.render();   // return button as jquery object
		};
		var options = {
		  url : baseUrl + '/vendors/elfinder/php/connector.minimal.php',  // connector URL (REQUIRED)
		  commandsOptions : {
			getfile : {
			  onlyURL  : true
			}
		  },            
		  getFileCallback: function(url) {
			//insert url into summernote as callback
			$('.summernote').summernote('insertImage', url);
			$('#modalMedia').modal('hide');        
		  }
		};
		$('#modalMedia').on('show.bs.modal', function () {
		  $('#elfinder').elfinder(options).elfinder('instance');
		}); 
        $('.summernote').summernote({
			height: 400,
			lang: summernote_lang,
			/*toolbar: [
					['style', ['style']],
					['style', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
					['fontname', ['fontname']],
					['fontsize', ['fontsize']],
					['color', ['color']],
					['para', ['ul', 'ol', 'paragraph']],
					['height', ['height']],
					['table', ['table']],
					['insert', ['link', 'picture', 'video', 'hr', 'browse']],
					['genixcms', ['elfinder']],
					['view', ['fullscreen', 'codeview']],
					['help', ['help']]
				],
			buttons: {
				browse: browseButton
			  },*/
			callbacks: {
				onImageUpload: function(image) {
					uploadImage(image[0]);
				},
				onMediaDelete : function(target) {
					// alert(target[0].src) 
					deleteFile(target[0].src);
				}
			}
		});

		function uploadImage(image) {
			var data = new FormData();
			data.append("file", image);
			data.append("action", 'summernote_upload_file');
			$.ajax({
				url: ajax_url,
				cache: false,
				contentType: false,
				processData: false,
				data: data,
				type: "post",
				success: function(response) {
					if(response['status']==1) {
						var image = $('<img>').attr({src: response['data'], class: 'post_img'});
						$('.summernote').summernote("insertNode", image[0]);
					} else {
						alert(response['data']);
					}
				},
				error: function(data) {
					console.log(data);
				}
			});
		}
		
		function deleteFile(src) {
			$.ajax({
				data: {
					action: 'summernote_delete_file',
					src : src,
				},
				type: "POST",
				url: ajax_url,
				cache: false,
				success: function(resp) {
					//console.log(resp);
				}
			});
		}
    });
	
	function getHora() {
	   date = new Date();   
	   return " "+date.getHours()+':'+date.getMinutes()+':'+date.getSeconds();
	};

	$( function() {
		$( "#datecreate" ).datepicker(
	   {	
			changeMonth: true,
			changeYear: true,
			dateFormat: 'dd-mm-yy' + getHora(),
		});
	  });