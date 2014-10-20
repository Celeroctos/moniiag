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
});