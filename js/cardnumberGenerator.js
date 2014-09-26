$(document).ready(function() {
    $('.newMedcardNumberBlock').each(function(index, element) {
		$(element).on('update', function() {
			$.ajax({
				'url' : '/reception/patient/generatecardnumber',
				'cache' : false,
				'dataType' : 'json',
				'data' : {
					'ruleid' : null;
				},
				'type' : 'GET',
				'success' : function(data, textStatus, jqXHR) {
					if(data.success) {
					
					}
				}
			});
		});
	});
});