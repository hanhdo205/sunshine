$(document).ready(function () {
	$('#avatar-img').click(function () {
		$("#files").trigger('click');
	});
});
var readURL = function(input) {
	$.each(input.files, function(value) {
		var reader = new FileReader();
		reader.onload = function (e) {
			$('#avatar-img').attr('src', e.target.result);
		};
		reader.readAsDataURL(input.files[value]);
	}); 
}

function fixAspect(img) {
  var $img = $(img),
    width = $img.width(),
    height = $img.height(),
    tallAndNarrow = width / height < 1;
  if (tallAndNarrow) {
    $img.addClass('tallAndNarrow');
  }
  $img.addClass('loaded');
}
$(function() {
	$('textarea').each(function() {
		$(this).height($(this).prop('scrollHeight'));
	});
});