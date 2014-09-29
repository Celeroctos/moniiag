$(document).ready(function() {
	// Необязательная конфигурация. Пока в том же файле.
	var config = {
		'kdoReg' : {
			'overlay' : $('#patient-withoutcard-form .row')
		}
	};
	
    $('.newMedcardNumberBlock').each(function() {
		(function(element, config) { 
			if(typeof $(element).prop('id') != 'undefined' && config.hasOwnProperty($(element).prop('id'))) {
				var config = config[$(element).prop('id')];
			} else {
				var config = {};
			}

			$(element).on('update', function() {
				$.ajax({
					'url' : '/reception/patient/generatecardnumber',
					'cache' : false,
					'dataType' : 'json',
					'type' : 'GET',
					'success' : function(data, textStatus, jqXHR) {
						if(data.success) {
						
						}
					}
				});
			});
			
			$(element).on('click', '.bigger', function(e) {
				$(element).trigger('beginedit');
				e.stopPropagation();
			});
			
			$(element).on('beginedit', function() {
				$(this).find('.bigger').replaceWith($('<span>').addClass('prefix').text(globalVariables.medcardData.prefix), $('<input>').prop({
					'type' : 'text',
					'class' : 'number'
				}).val(globalVariables.medcardData.number), $('<span>').addClass('postfix').text(globalVariables.medcardData.postfix));
				$(this).find('input').focus();
			});
			
			$(element).on('endedit', function() {
				var number = $(this).find('.number');
				var prefix =  $(this).find('.prefix');
				var postfix =  $(this).find('.postfix');
				if(isNaN($(number).val())) {
					alert('Некорректный номер карты!');
					return false;
				}
				if(config.hasOwnProperty('overlay')) {
					$(config.overlay).css({
						'position' : 'relative'
					}).prepend($('<div>').prop('class', 'overlay').css({'marginLeft' : '10px'}));
				}
				$(number).prop('disabled', true);
				$.ajax({
					'url' : '/reception/patient/checkissetcardnumber',
					'cache' : false,
					'dataType' : 'json',
					'data' : {
						'cardnumber' : prefix.text() + number.val() + postfix.text()
					},
					'type' : 'POST',
					'success' : function(data, textStatus, jqXHR) {
						if(data.success) {
							$(number).replaceWith($('<span>').prop({
								'class' : 'bigger'
							}).text(prefix.text() + number.val() + postfix.text()));
							$(prefix).remove();
							$(postfix).remove();
							$(number).remove();
							globalVariables.medcardData.number = number.val();
							$(config.overlay).find('.overlay').remove();
						} else {
							alert(data.error);
							$(config.overlay).find('.overlay').remove();
							$(number).prop('disabled', false);
							return false;
						}
					}
				});
			});
			
			$(element).on('blur', 'input', function() {
				$(element).trigger('endedit');
			});
		})(this, config);
	});
});
