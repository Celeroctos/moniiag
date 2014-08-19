$(document).ready(function() {
	var buttonSelectors = [
		'#patient-search-submit',
		'#doctor-search-submit',
        '#logs-search-submit',
		//'.add-patient-submit input',
        '#medcardContentSave',
        '#printContentButton',
        '#greetings-search-submit',
		'#greeting-getstat-submit'
	];
	
	buttonSelectors.forEach(function(buttonSelector) {
		var button = $(buttonSelector);
		var savedText = null;
				
		$(button).on('begin', function() {
			endCalled = false;
			$(button).prop('disabled', true);
			savedText = $(button).val();
			$(button).trigger('process');
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