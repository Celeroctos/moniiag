$(document).ready(function() {
	$('#submitEmployeeRole').on('click', function(e) {
		$('.main-container').css({
			'position' : 'relative'
		}).prepend($('<div>').prop('class', 'overlay').css({'marginLeft' : '10px'}));
		$('#currentEmployeeRole').prop('disabled', true);
		$.ajax({
			'url': '/users/loginStep2',
			'cache': false,
			'dataType': 'json',
			'data': {
				'FormChooseEmployee[id]': $('#currentEmployeeRole').val()
			},
			'type': 'POST',
			'success': function (data, textStatus, jqXHR) {
				if (data.success == 'true') {
					location.reload();
				} else {
					alert('Произошла ошибка при переключении сотрудника.');
				}
			}
		});
	});
});