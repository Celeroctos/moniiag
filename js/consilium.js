$(document).ready(function() {
	var cont = $('.consilium-cont');
	$(cont).find('.panel-arrow').on('click', function(e) {
		if($(this).find('span').hasClass('glyphicon-expand')) {
			$(cont).animate({
				'left' : '0'
			}, 500, function() {
				$(cont).find('.panel-arrow span').removeClass('glyphicon-expand').addClass('glyphicon-collapse-down');
			});
		} else {
			$(cont).animate({
				'left' : '-250px'
			}, 500, function() {
				$(cont).find('.panel-arrow span').removeClass('glyphicon-collapse-down').addClass('glyphicon-expand');
			});
		}
	});
});