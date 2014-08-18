$(document).ready(function() {
	$('#sensorsTable tbody tr').on('click', function(e) {
		$('#sensorEditPopup').modal({});
	});
});