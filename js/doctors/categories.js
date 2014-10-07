$(document).ready(function() {

    $.fn['categories'] = {
        initSelectOnClick: function(controlToInit)
        {
            initSelectControlClick(controlToInit);
        }
    }

    $('button[id^=ba]').filter('[id*=history]').prop('disabled', true);
    $('button[id^=ba]').filter(':not([id*=history])').on('click', function(e) {
        var elementId = $(this).attr('id').substr($(this).attr('id').lastIndexOf('_') + 1);
        $('#controlId').val(elementId);
        globalVariables.elementId = elementId;
       // globalVariables.domElement = $(this).parents('.form-group').find('select');
        // Выбираем
        globalVariables.domElement = $('select[id$=_' + elementId + '], input[id$=_' + elementId + ']');
        $('#addValuePopup').modal({
        });
    });

    $('input[type="number"]').on('focus', function(e) {
        $(this).css({
            backgroundColor: "rgb(255, 255, 255)"
        });
    });



    $('input[type="number"]').on('keydown', function(e) {
        if((!(e.keyCode >= 48 && e.keyCode < 58) && !(e.keyCode >= 96 && e.keyCode < 106)) && e.keyCode != 8 && e.keyCode != 190 && e.keyCode != 188 && e.keyCode != 9 && e.keyCode != 46) {
            return false;
        }
        else
        {
            // Проверим - если мы вводим точку или запятую и если в значении уже есть точка или запятая - надо запретить ввод
            if ( (e.keyCode == 190 || e.keyCode == 188)&&
                ($(this).val().indexOf('.')>=0
                    ||
                    $(this).val().indexOf(',')>=0)
                )
            {
                return false;
            }
        }
    });

    $('input[type="number"]').on('blur', function(e) {
        $(this).css({
            backgroundColor: "rgb(255, 255, 255)"
        });
        var max = parseFloat($(this).attr('max'));
        var min = parseFloat($(this).attr('min'));
        var value = parseFloat('' + $(this).val() + String.fromCharCode(e.keyCode));
        if(typeof max != 'undefined') {
            if(value > max) {
                $(this).animate({
                    backgroundColor: "rgb(255, 196, 196)"
                });
                $(this).focus();
                return false;
            }
        }
        if(typeof min != 'undefined') {
            if(value < min) {
                $(this).animate({
                    backgroundColor: "rgb(255, 196, 196)"
                });
                $(this).focus();
                return false;
            }
        }
    });

    $("#add-value-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == 'true') { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addValuePopup').modal('hide');
            $("#add-value-form")[0].reset(); // Сбрасываем форму
            //$(globalVariables.domElement).find('option:first').before('<option value="' + ajaxData.id + '">' + ajaxData.display + '</option>');
            //$(globalVariables.domElement).val(ajaxData.id);
            // Проверим - чем является элемент, который сохранён в globalVariables.domElement. Если - select - обрабатываем
            //    одним образом - иначе, другим
            if (  $(globalVariables.domElement).is('select') )
            {
                $(globalVariables.domElement).find('option:first').before('<option value="' + ajaxData.id + '">' + ajaxData.display + '</option>');
                $(globalVariables.domElement).val(ajaxData.id);
            }
            else
            {
                if (  $(globalVariables.domElement).is('input') )
                {
                    // Ищем таблицу
                    $.fn[ $(globalVariables.domElement).attr('id') ].addSelected(ajaxData.id,ajaxData.display);
                }
            }

        } else {
           showErrors(ajaxData);
        }
    });

    $("#add-greeting-value-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == 'true') { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addGreetingComboValuePopup').modal('hide');
            $("#add-greeting-value-form")[0].reset(); // Сбрасываем форму
            if (  $(globalVariables.domElement).is('select') )
            {
                $(globalVariables.domElement).find('option:first').before('<option value="' + ajaxData.id + '">' + ajaxData.display + '</option>');
                $(globalVariables.domElement).val(ajaxData.id);
            }
            else
            {
                if (  $(globalVariables.domElement).is('input') )
                {
                    $.fn[ $(globalVariables.domElement).attr('id') ].addSelected(ajaxData.id,ajaxData.display);
                }
            }
        } else {
            showErrors(ajaxData);
        }
    });

    function showErrors(ajaxData) {
        // Удаляем предыдущие ошибки
        $('#errorPopup .modal-body .row p').remove();
        // Вставляем новые
        // Только одна ошибка...
        if(ajaxData.hasOwnProperty('error')) {
            $('#errorPopup .modal-body .row').append("<p>" + ajaxData.error + "</p>")
        } else {
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }
        }

        $('#errorPopup').modal({
        });
    }

    // Старый код, потом выкинуть
    /*
    $('.accordion-inner select').each(function(index, element) {
        var currentValue = $(element).val();
        //$(element).on('change', function(e) {
        $(document).on('change',element, function(e) {
            if($(this).val() == '-3') {
                globalVariables.domElement = element;
                var elementId =  $(element).attr('id').substr($(element).attr('id').lastIndexOf('_') + 1);
                $('#addGreetingComboValuePopup #controlId').val(elementId);
                $('#addGreetingComboValuePopup').modal({});
                $(element).val(currentValue);
                return false;
            } else {
                currentValue = $(this).val();
            }
        });
    });
    */



    $('.accordion-inner select').each(function(index,element){
        initSelectControlClick(element);
    });
    function initSelectControlClick(element)
    {
        var currentValue = $(element).val();
        $(element).on('change', function(e) {
       // $(document).on('change',element, function(e) {
            if($(this).val() == '-3') {
                globalVariables.domElement = element;
                var elementId = undefined;
                if ($(this).attr('id')!=undefined)
                {
                    elementId =  $(this).attr('id').substr($(this).attr('id').lastIndexOf('_') + 1);
                }
                else
                {
                    // Иначе берём в родителе input
                    hiddenInput = $($(this).parents()[0]).find('input[type=hidden]');
                    elementIdRaw = $(hiddenInput).attr('id');
                    elementId = elementIdRaw.substr(elementIdRaw.lastIndexOf('_')+1);
                }
                $('#addGreetingComboValuePopup #controlId').val(elementId);
                $('#addGreetingComboValuePopup').modal({});
                $(element).val(currentValue);
                return false;
            } else {
                currentValue = $(this).val();
            }
        });
    }

/*
    lastSelectValue = undefined;
    $(document).on('click','.accordion-inner select',function()
    {
        lastSelectValue = $(element).val();
    });

    $(document).on('change','.accordion-inner select',function()
    {
            if($(this).val() == '-3') {
                globalVariables.domElement = this;
                var elementId = undefined;
                if ($(this).attr('id')!=undefined)
                {
                    elementId =  $(this).attr('id').substr($(this).attr('id').lastIndexOf('_') + 1);
                }
                else
                {
                    // Иначе берём в родителе input
                    hiddenInput = $($(this).parents()[0]).find('input[type=hidden]');
                    elementIdRaw = $(hiddenInput).attr('id');
                    elementId = elementIdRaw.substr(elementIdRaw.lastIndexOf('_')+1);
                }
                $('#addGreetingComboValuePopup #controlId').val(elementId);
                $('#addGreetingComboValuePopup').modal({});
                $(this).val(currentValue);
                return false;
            } else {
                currentValue = $(this).val();
            }
        }
    );*/



    $('.templatesListNav a').click(function (e) {
        e.preventDefault();
        var tabId = $(this).prop('id').substr(1);
        heightBefore = $(document).height();
        scrollHeightTopDifference = $(document).height() - $(document).scrollTop();
        console.log('Высота сначала '+$(document).height());
        $('form#template-edit-form').find('[id^=tab]').addClass('no-display');
        $('form#template-edit-form').find('#tab' + tabId).removeClass('no-display').show(500);
        console.log('Высота потом '+$(document).height());
        heightAfter = $(document).height();
        heightDifference = heightAfter - heightBefore;
        console.log(heightDifference);
        console.log('ScrollTOp до '+$(document).scrollTop());


        if ( ($(this).parents('.templatesListNav').hasClass('templatesListNavBottom'))  )
        {

            destinationAnchor = $('a[name=topMainTemplates]');
            if (destinationAnchor!=undefined)
            {
                destination = $(destinationAnchor)[0].offsetTop;
                $('body,html').animate({
                    scrollTop: destination
                }, 599);
            }

            // Старый код. Скорее всего не понадобится
            /*  if (heightDifference>0)
            {
                $(document).scrollTop($(document).scrollTop()+heightDifference);
            }
            else
            {
                $(document).scrollTop(   $(document).height() -  scrollHeightTopDifference );
            }*/
        }

        console.log('ScrollTOp после '+$(document).scrollTop());
        // Теперь нужно сдвинуть scrollTop в плюс на разницу heightDifference


        //$(this).tab('show')

        /*allTabs = $('templatesListNav a[id^=t]');
        // Снимаем всем класс active
        $(allTabs).parents('li').removeClass('active');

        return;*/
        tabs = $('[id=t'+ tabId +']');
        for (i=0;i<tabs.length;i++)
        {
            $(tabs[i]).tab('show');
        }
    });

    $('.recomTemplatesListNav a').click(function (e) {
        e.preventDefault();
        var tabId = $(this).prop('id').substr(2);
        //heightBefore = $(document).height();
        //scrollHeightTopDifference = $(document).height() - $(document).scrollTop();
        //console.log('Высота сначала '+$(document).height());
        $('form#template-edit-form').find('[id^=rtab]').addClass('no-display');
        $('form#template-edit-form').find('#rtab' + tabId).removeClass('no-display').show(500);
        //console.log('Высота потом '+$(document).height());
        //heightAfter = $(document).height();
        //heightDifference = heightAfter - heightBefore;
        //console.log(heightDifference);
        //console.log('ScrollTOp до '+$(document).scrollTop());


        if ( ($(this).parents('.recomTemplatesListNav').hasClass('recomTemplatesListNavBottom'))  )
        {

            destinationAnchor = $('a[name=topRecomTemplates]');
            if (destinationAnchor!=undefined)
            {
                destination = $(destinationAnchor)[0].offsetTop;
                $('body,html').animate({
                    scrollTop: destination
                }, 599);
            }

            // Старый код. Скорее всего не понадобится
            /*  if (heightDifference>0)
             {
             $(document).scrollTop($(document).scrollTop()+heightDifference);
             }
             else
             {
             $(document).scrollTop(   $(document).height() -  scrollHeightTopDifference );
             }*/
        }

        //console.log('ScrollTOp после '+$(document).scrollTop());
        // Теперь нужно сдвинуть scrollTop в плюс на разницу heightDifference


        //$(this).tab('show')

        /*allTabs = $('templatesListNav a[id^=t]');
         // Снимаем всем класс active
         $(allTabs).parents('li').removeClass('active');

         return;*/
        tabs = $('[id=rt'+ tabId +']');
        for (i=0;i<tabs.length;i++)
        {
            $(tabs[i]).tab('show');
        }
    });
	
	var popoverCont = null;
	// Просмотр динамики параметров
	$(document).on('click', '.showDynamicIcon', function(e) {
		popoverCont = $(this);
		$(this).popover({
            animation: true,
            html: true,
            placement: 'right',
            title: 'Динамика изменения параметра',
            delay: {
                show: 300,
                hide: 300
            },
            container: $(this),
            content: function() {
				var table = $('<table>').css({
					'width' : '100%'
				}).addClass('table');
				var diff1TdHeader = $('<td>').text('Разница с текущим');
				var diff2TdHeader = $('<td>').text('Разница с предыдущим');
				$(table).append($('<thead>').append($('<tr>').addClass('bold').append($('<td>').text('Дата'), $('<td>').text('Значение'), diff1TdHeader, diff2TdHeader)), $('<tbody>'));
				var elementId = $(this).next().prop('id');
				var currentValue = $(this).next().val();
				var ajaxGif =  $('<img>').prop({
                    'src' : '/images/ajax-loader.gif',
                    'width' : 32,
                    'height' : 32,
                    'alt' : 'Загрузка...'
                });
				var container = $('<div>');
				$.ajax({
					'url' : '/doctors/shedule/getparamhistory',
					'cache' : false,
					'dataType' : 'json',
					'data' : {
						'element' : elementId,
						'medcard' : globalVariables.medcardNumber,
						'greetingId' : $('#greetingId').val()
					},
					'type' : 'GET',
					'success' : function(data, textStatus, jqXHR) {
						if(data.success == true) {
							$(table).find('tbody tr:not(:first)').remove();
							var data = data.data;
							if(data.length > 0 && data[0].type == 5) {
								$(diff1TdHeader).removeClass('no-display');
								$(diff2TdHeader).removeClass('no-display');
							} else {
								$(diff1TdHeader).addClass('no-display');
								$(diff2TdHeader).addClass('no-display');
							}
							var prevValue = null;
							for(var i = 0; i < data.length; i++) {
								var tr = $('<tr>').append(
									$('<td>').text(data[i].change_date),
									$('<td>').text(data[i].value)
								);
								if(data[i].type == 5) { // Тип numberField
									var diff1 = parseFloat(currentValue) - parseFloat(data[i].value);
									if(i > 0 && prevValue != null) {
										var diff2 = prevValue - parseFloat(data[i].value);
									} else {
										var diff2 = 0;
									}
									prevValue = parseFloat(data[i].value);
									
									var diffTd1 = $('<td>').addClass('bold').text(diff1);
									var diffTd2 = $('<td>').addClass('bold').text(diff2);
									
									if(diff1 > 0) {
										$(diffTd1).addClass('text-danger');
									} else if(diff1 < 0) {
										$(diffTd1).addClass('text-success');
									}
									
									if(diff2 > 0) {
										$(diffTd2).addClass('text-danger');
									} else if(diff2 < 0) {
										$(diffTd2).addClass('text-success');
									}
									$(tr).append(diffTd1, diffTd2);
								} 
								$(table).find('tbody').append(tr);
							}
							$(ajaxGif).remove();
							$(container).append(table);
						} 
					}
				});
				return $(container).addClass('changesList').append(ajaxGif);
			}
		});
		
	    $(this).popover('show');
		
		var span = $('<span class="glyphicon glyphicon-remove" title="Закрыть окно"></span>').css({
			position: 'absolute',
			cursor: 'pointer',
			left: '480px'
		});

		$(span).on('click', function(e) {
			$(popoverCont).popover('destroy');
			$(span).remove();
			e.stopPropagation();
			return false;
		});
		
		$(this).find('.popover-title').css({
			'color' : '#000000',
			'fontWeight' : 'bold'
		}).text('Динамика изменения параметра');
		
		$(this).find('.popover-content, .popover-title').css({
			'width' : '100%'
		}).on('click', function(e) {
			e.stopPropagation();
			return false;
		});
		
		$(this).find('.popover').css({
			'cursor' : 'default',
			'width' : '500px',
			'max-width' : '500px',
			'min-width' : '500px'
		}).append(span);

	    e.stopPropagation();
	    return false;
	});
});