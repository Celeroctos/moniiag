$(document).ready(function() {
	var buttonSelectors = [
		'#patient-search-submit',
		'.add-patient-submit input',
        '#medcardContentSave',
        '#printContentButton'
	];
	
	buttonSelectors.forEach(function(buttonSelector) {
		console.log(buttonSelector);
		button = $(buttonSelector);
		var savedText = null;
		$(button).on('begin', function() {
			endCalled = false;
			$(button).prop('disabled', true);
			$(button).trigger('process');
			savedText = $(button).val();
		});
		
		var pointCounter = 0;
		var endCalled = false;
		$(button).on('process', function() {
			setTimeout(process, 500);
			function process() {
				if(!endCalled) {
					var pointStr = '';
					for(var i = 0; i < pointCounter; i++) {  
						pointStr += '.';
					}
					$(button).val('Подождите' + pointStr);
					if(pointCounter == 3) {
						pointCounter = 0;
					} else {
						++pointCounter;
					}
					
					setTimeout(process, 500);
				}
			}
		});
		$(button).on('end', function() {
			endCalled = true;
			$(button).prop('disabled', false);
			$(button).val(savedText);
		});
	});
});