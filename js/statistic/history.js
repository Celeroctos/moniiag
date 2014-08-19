$(document).ready(function() {
	$('#reset-submit').on('click', function() {
		$('#patient-search-form').get(0).reset();
		$.fn['doctorChooser'].clearAll();
	});

});