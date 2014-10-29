$(document).ready(function() {
	$('#navbarTools .arrow').on('click', function(e) {
		var panelArrow = $('#navbarTools .arrow');
		if ($(panelArrow).find('span').hasClass('glyphicon-collapse-down')) {
			$(panelArrow).find('span').removeClass('glyphicon-collapse-down').addClass('glyphicon-collapse-up');
			$(panelArrow).animate({ 
				'marginTop' : '25px'
			}, 500, function () {
				$(panelArrow).find('span').removeClass('glyphicon-collapse-down').addClass('glyphicon-collapse-up');
			}); 
		} else {
			$(panelArrow).animate({ 
				'marginTop' : '-25px'
			}, 500, function () {
				$(panelArrow).find('span').removeClass('glyphicon-collapse-up').addClass('glyphicon-collapse-down');
			}); 
		}
	});

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