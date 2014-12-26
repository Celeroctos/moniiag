$(document).ready(function() {
	$('#navbarTools .arrow-panel').on('click', function(e) {
		var panelArrow = $('#navbarTools .arrow-panel');
		if ($(panelArrow).find('span').hasClass('glyphicon-collapse-down')) {
			$('#footerTabPanel').animate({ 
				'marginTop' : '0'
			}, 200, function() {
				$(panelArrow).find('span').removeClass('glyphicon-collapse-down').addClass('glyphicon-collapse-up');
				$(panelArrow).animate({ 
					'marginTop' : '25px'
				}, 500, function () {
					$(panelArrow).find('span').removeClass('glyphicon-collapse-down').addClass('glyphicon-collapse-up');
				}); 
			});
		} else {
			$(panelArrow).animate({ 
				'marginTop' : '-25px'
			}, 500, function () {
				$('#footerTabPanel').animate({ 
					'marginTop' : '-25px'
				}, 200, function() {
					// Empty now
				});
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
	
	$('#footerTabPanel li').on('click', function(e) {
		$('#footerTabPanel li.active').removeClass('active');
		$(this).css({
			'zIndex' : 999
		}).addClass('active');
		$('#footerTabPanel li:not(.active)').css({
			'zIndex' : 10
		});
		var classes = $(this).prop('class').split(' ');
		for(var i = 0; i < classes.length; i++) {
			if(/^panel/i.test(classes[i])) {
				$('.footerPanel').filter('.active').fadeOut(500, function() {
					$(this).removeClass('active').css({
						'zIndex': 90
					});
				});
				
				$('#' + classes[i]).fadeIn('slow', function() {
					$('#' + classes[i]).addClass('active');
				}).css({
					'zIndex' : 98
				});
				$('#navbarTools .arrow-panel').css({
					'background': $(this).css('background')
				});
			}
		}
	});
	
	$('#toolsList img').on('mouseover', function(e) {
		$(this).effect('bounce');
	});
	
	/* Калькулятор беременности */
	$('#calcBBToolLink').on('click', function(e) {
		var parentCont = $(this).parent();
		$(parentCont).popover({
            animation: true,
            html: true,
            placement: 'right',
            title: 'Калькулятор беременности',
			template: "<div class=\"popover calcBBPopover\" role=\"tooltip\"><div class=\"arrow\"></div><h3 class=\"popover-title\"></h3><div class=\"popover-content\"></div></div>",
            delay: {
                show: 300,
                hide: 300
            },
            container: $(this).parent(),
			content: function() {
				var methods = [
					{ 
						display : 'По первому дню последней менструации',
						func: function() {
						
						}
					},
					{ 
						display : 'По раннему УЗИ',
						func: function() {
						
						}
					},
					{ 
						display : 'По дате переноса эмбриона (при ЭКО)',
						func: function() {
						
						}
					},
					{ 
						display : 'По дате первых замеченных шевелений',
						func: function() {
						
						}
					},
					{ 
						display : 'По предполагаемой дате зачатия',
						func: function() {
						
						}
					},
					{ 
						display : 'По дате овуляции',
						func: function() {
						
						}
					},
					{ 
						display : 'По дате инсеминации',
						func: function() {
						
						}
					},
					{ 
						display : 'По первой явке в женскую консультацию.',
						func: function() {
						
						}
					}
				];
				var calcMethodCombo = $('<select>').prop({
					'class' : 'form-control'
				});
				for(var i = 0; i < methods.length; i++) {
					$(calcMethodCombo).append($('<option>').prop({
						'value' : i
					}).text(methods[i].display));
				}
				
				return $('<div>').append($('<div>').prop({
					'class' : 'form-group'
				}).append($('<label>').prop({
					'class' : 'col-xs-5 control-label'
				}).text('Выберите метод расчёта'), $('<div>').prop({
					'class' : 'col-xs-7'
				}).append(calcMethodCombo)));
			}
        });
		
		$(parentCont).on('shown.bs.popover', function(e) {		
			var span = $('<span class="glyphicon glyphicon-remove" title="Закрыть окно"></span>').css({
				position: 'absolute',
				cursor: 'pointer',
				left: '480px'
			});
				
			$(span).on('click', function(e) {
				$(parentCont).popover('destroy');
				$('#toolsList .popover').remove(); // Shit. Not destroys automat. 
				return false;
			});

			$('#toolsList .popover span.glyphicon').remove();
			$('#toolsList .popover-title').append(span);
			
			$(parentCont).on('click', '.popover', function(e) {
				return false;
			});
		})
	});
});