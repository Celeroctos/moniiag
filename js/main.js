//
$(document).ready(function () {
    globalVariables.wrongPassword = false;
    globalVariables.wrongLogin = false;

    this.initColorFields = function (colorPickerFields) {
        $(function () {
            // Маркировка анкет
            for (var i = 0; i < colorPickerFields.length; i++) {
                if ($(colorPickerFields[i]).length == 0) {
                    continue;
                }
                $(colorPickerFields[i]).colorpicker({
                    format: 'hex'
                });
            }
        });
    };

   /* $('#patient-search-form #omsNumber, #policy').keyfilter(/^[\s\d\-/]*$/);
    // --- Begin 17.06.2014 ---
    $('#patient-search-form #omsNumber, #policy').on('keydown', function(e) {
        if($(this).val().length >= 16 && e.keyCode != 8 && e.keyCode != 46) {
            if($(this).val().length == 16 && (e.keyCode == 13 || e.keyCode == 9 || e.keyCode == 8)) {
                return true;
            }
            return false;
        }
    });*/


  /*  $('#docnumber').on('keydown', function(e) {
        if($(this).val().length >= 6 && e.keyCode != 8 && e.keyCode != 46) {
            if($(this).val().length == 6 && (e.keyCode == 13 || e.keyCode == 9 || e.keyCode == 8)) {
                return true;
            }
            return false;
        }
    });*/
    /*
    $('#serie').on('keydown', function(e) {
        if($(this).val().length >= 4 && e.keyCode != 8 && e.keyCode != 46) {
            if($(this).val().length == 4 && (e.keyCode == 13 || e.keyCode == 9 || e.keyCode == 8)) {
                return true;
            }
            return false;
        }
    });*/
    // --- End 17.06.2014 ---

    $('#firstName, #lastName, #middleName').keyfilter(/^[А-Яа-яЁёa-zA-Z\-]*$/);
  //  $('#serie, #docnumber').keyfilter(/^[А-Яа-яЁёa-zA-Z\-\d\s]*$/);

    $('#snils').on('keyup', function (e) {
        var value = $(this).val();
        // СНИЛС по проверке
        if ((value.length == 3 || value.length == 7 || value.length == 11) && e.keyCode != 8) { // Введён год или месяц..
            $(this).val(value + '-');
        }
        if ((value.length == 4 || value.length == 8 || value.length == 12) && e.keyCode == 8) { // Убрать автоматически прочерк
            $(this).val(value.substr(0, value.length - 1));
        }
    });

    // Снилс
    $('#snils').on('keydown', function (e) {
        // Бэкспейс разрешить, цифры разрешить
        var isAllow = true;
        // Проверяем табуляцию и  Enter
        // Если символ Enter или Tab - сразу возвращаем true
        if ((e.keyCode == 13) || (e.keyCode == 9))
            return true;

        var value = $(this).val();
        if (value.length == 14 && e.keyCode != 8) {
            $.fn.switchFocusToNext();
            isAllow = false;
        } else {
            if (!(e.keyCode > 47 && e.keyCode < 58) && !(e.keyCode > 95 && e.keyCode < 106) && e.keyCode != 8) {
                isAllow = false;
            }
        }
        if ((value.length == 3 || value.length == 7 || value.length == 11) && e.keyCode != 8) {
            $(this).val(value + '-');
        }
        return isAllow;
    });


    // При загрузке - если поле "контактные данные" пусто - надо поставить код России в начале в этом поле
    if ($('#contact, #phone').length > 0)
    {
        // Если поле "телефон" пусто - выводим в него '+7'
        if ($('#contact').val()=='')
        {
            $('#contact').val('+7');
        }

    }


    $('#contact, #phone, #phoneFilter').on('keydown', function (e) {
        // Нажатая клавиша
        var pressedKey = e.keyCode;
        // Если символ Enter или Tab - сразу возвращаем true
        if (pressedKey == 13 || pressedKey == 9 || pressedKey == 16)
            return true;

        //var isAllow = true;
        // Значение контрола
        var value = $(this).val();

        // Если телефон - российский, то разрешаем длину в 14 символов
        if (value.substr(0,2)=='+7')
        {
            //разрешаем длину в 14 символов
            if (value.length == 14 && !(pressedKey == 8 ||pressedKey == 37 || pressedKey == 39|| pressedKey == 46)) {
                // Переводим фокус на следующий элемент
                $.fn.switchFocusToNext();
                return false;
            }
        }
        // А если телефон не российский, то длина (теоретически) может быть любая

        if (pressedKey == 8 || pressedKey == 46 || pressedKey == 16)
            return true;

        // Если номер не российский и длина значения больше 2, то разрешаем ставить пробелы
        if (value.substr(0,2)!='+7' && value.length>=2)
        {
            if (pressedKey == 32)
            {
                return true;
            }
        }

        // Если нажатая клавиша - "+",
        //   то его нужно разрешить только в первой позиции
        console.log(pressedKey);
        if (pressedKey == 187) {
            return !$(this).val() != '';
        }

        // Если клавиша - цифра
        if (!(pressedKey  > 47 && pressedKey  < 58) && !(pressedKey > 95 && pressedKey  < 106))
            return false;

        // Делим на подгруппы номер только в том случае, если он российский.
        //  У иностранных номеров может быть коды городов разной длины
        if (value.substr(0,2) == '+7') {
            if (value.length == 2 || value.length == 6) {
                $(this).val(value + '-');
            }
        }
        return true;
    });

    /*$('#phoneFilter').on('keyup', function (e) {
        // Нажатая клавиша
        var pressedKey = e.keyCode;
        // Если символ Enter или Tab - сразу возвращаем true
        if ([13, 9, 16, 8, 46, 32].indexOf(pressedKey) != -1) {
            return true;
        }

        var value = $.trim($(this).val());
        // Если нажатая клавиша - "+",
        // то его нужно разрешить только в первой позиции
        if (pressedKey == 187) {
            return !$('#phoneFilter').val() != '';
        }

        // Если клавиша - цифра или '-'
        if (!(pressedKey  > 47 && pressedKey  < 58) && !(pressedKey > 95 && pressedKey  < 106) && pressedKey != 189) {
            return false;
        }

        // Делим на подгруппы номер только в том случае, если он российский.
        //  У иностранных номеров может быть коды городов разной длины
        if((value.length == 3 && /^\d{3}$/.test(value)) || (value.length == 1 && value == '8') || (value.length == 2 && value == '+7')) {
            $(this).val(value + '-');
        }

        return true;
    }); */


    $('#cardNumber').on('keyup', function (e) {
        if ($(this).val().indexOf('\\')>=0)
        {
            $(this).val(  $(this).val().replace('\\', '/')  );
        }
    });

    // Паспорт (номер)
  //  $('#docnumber').keyfilter(/^[\d]+$/);
    // Номер карты
   // $('#cardNumber').keyfilter(/[\d\\]+/);
    $('#cardNumber').keyfilter(/^[\d]*([\\\/][\d]*){0,1}$/);

    this.initColorFields([
        '.custom-color' // Маркировка анкет
    ]);

    $('#loginSuccessPopup').on('hidden.bs.modal', function () {
        window.location.reload();
    });

    // Форма логина-разлогина
    $("#login-form").on('success', function (eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        globalVariables.wrongPassword = false;
        globalVariables.wrongLogin = false;
        if (ajaxData.success == 'true') { // Логин прошёл удачно
			// Тут много сотрудников..
            if(typeof ajaxData.data == 'object') {
				var select = $('#choose-employee-form #employeeId');
				$(select).find('option').remove();
				var employees = ajaxData.data;
				for(var i = 0; i < employees.length; i++) {
					$(select).append($('<option>').prop({
						'value' : employees[i].id
					}).text(employees[i].last_name + ' ' + employees[i].first_name + (employees[i].middle_name != null ? ' ' + employees[i].middle_name : '') + ', табельный номер ' + employees[i].tabel_number));
				}
				$('#loginEmployeeChoose').modal({
					'keyboard' : false
				});
			} else {
				location.href = ajaxData.data;
			}
        } else if (ajaxData.success == 'notFoundLogin' ||ajaxData.success == 'wrongPassword' ) {
            if (ajaxData.success == 'notFoundLogin')
            {
                globalVariables.wrongLogin = true;
            }

            if (ajaxData.success == 'wrongPassword')
            {
                globalVariables.wrongPassword = true;
            }

            $('#loginNotFoundPopup').modal({
        });
    } else {
        $('#loginErrorPopup').modal({
    });
}
});

	$("#choose-employee-form").on('success', function (eventObj, ajaxData, status, jqXHR) {
		var ajaxData = $.parseJSON(ajaxData);
		if(ajaxData.success == 'true') { 
			location.href = ajaxData.data;
		} else {
			// TODO
		}
	});

    $('#loginNotFoundPopup').on('hidden.bs.modal',function(){
        // Если неправильный логин - выделяем логин
        if(globalVariables.wrongLogin)
        {
            $('#login').focus();
        }

        // Если не правильный пароль - выделяем пароль
        if(globalVariables.wrongPassword)
        {
            $('#password').focus();
        }
        // В остальных случаях - ничего не делаем, отдыхаем

    });

// Форма разлогина
$("#logout-form").on('success', function (eventObj, ajaxData, status, jqXHR) {
    window.location.reload();
});

// Показ подсказки по фокусу на поле
$('input').on('focus', function (e) {

    // Из-за этого рубится событие клик
    //$('.help-block').hide();


    var helpBlock = $(this).parents('.form-group').find('.help-block');
    if (typeof helpBlock != 'undefined') {
        if ($(helpBlock).length > 0) {
            $(helpBlock).show();
        }

    }
});

// Ставим классы для различения контролов времени и даты
$('div.date:not(.date-timetable)').addClass('date-control');
$('div.time-control').removeClass('date-control');

/* Панель быстрого доступа */
$('#quickPanel').css({
//'display' : 'none'
});
$('#quickPanelArrow').on('click', function () {
    $('#recycleBin-cont').css('display', 'none');
    $('#quickPanel').slideToggle(500, function () {
        var quickPanelArrow = $('#quickPanelArrow');
        if ($(quickPanelArrow).find('span').hasClass('glyphicon-collapse-down')) {
            $(quickPanelArrow).find('span').removeClass('glyphicon-collapse-down').addClass('glyphicon-collapse-up');
            $('#recycleBin-cont').css('display', 'block');
        } else {
            $(quickPanelArrow).find('span').removeClass('glyphicon-collapse-up').addClass('glyphicon-collapse-down');
        }
    });
});

var dragLink = null;
var parent = null;
var removeMode = false; // Fix против высплывания событий
$('#quickPanel img:not(.recycleBin)').each(panelDragInit);

$('#recycleBin-cont img').droppable().on('drop', function (event, ui) {
    $.ajax({
        'url': '/quickpanel/removeelement',
        'cache': false,
        'dataType': 'json',
        'data': {
            'href': $(ui.draggable).parent().prop('href'),
            'icon': $(ui.draggable).attr('src')
        },
        'type': 'GET',
        'success': function (data, textStatus, jqXHR) {
            if (data.success == true) {
                $(ui.draggable).parent().remove();
                removeMode = true;
            } else {

            }
        }
    });
});

// Обрабатывает события скрытия аккордеона и выводит слово "Скрыть" и "Раскрыть"
$(this).on('hidden.bs.collapse', '.accordion', function (e) {
    $(e.currentTarget).find('.accordeonToggleAlt').text(' (Раскрыть)');
    return false;
});

$(this).on('shown.bs.collapse', '.accordion', function (e) {
    $(e.currentTarget).find('.accordeonToggleAlt').text(' (Свернуть)');
    return false;
});


function panelDragInit(index, element) {
    var dragMode = false;
    var animateToBig = function () {
        $(element).animate({
            width: 60,
            height: 60
        }, 200);
    };
    var animateToSmall = function () {
        $(element).animate({
            width: 40,
            height: 40
        }, 200);
    };
    $(element).on('mouseover', animateToBig);
    $(element).on('mouseout', animateToSmall);
    $(element).draggable();
    $(element).on('dragstart', function (event, ui) {
        dragMode = true;
        parent = $(element).parent();
        dragLink = $(parent).attr('href');
        $(parent).css('position', 'absolute');
        $(element).trigger('mouseover'),
            $(element).off('mouseover').off('mouseout');
    });
    $(element).on('dragstop', function (event, ui) {
        dragMode = false;
        $('#quickPanel img:not(.recycleBin)').each(panelDragInit);
    });
    $(element).on('drag', function (event, ui) {
        $(element).css({
            'z-index': 9999
        });
    });
}

$('#quickPanel').droppable({
    drop: function (event, ui) {
        var a = $(ui.draggable).parent()
        var link = $('<a>').attr('href', a.prop('href'));
        dragLink = null;
        $(ui.draggable).on('mouseover', function () {
            $(ui.draggable).animate({
                width: 60,
                height: 60
            }, 200);
        });
        $(ui.draggable).on('mouseout', function () {
            $(ui.draggable).animate({
                width: 40,
                height: 40
            }, 200);
        });
        $(ui.draggable).css('position', '');
        $(link).append(ui.draggable);
        $('#quickPanel').append(link);
        $(ui.draggable).trigger('mouseout');

        if (!removeMode) {
            $.ajax({
                'url': '/quickpanel/addelement',
                'cache': false,
                'dataType': 'json',
                'data': {
                    'href': $(a).prop('href'),
                    'icon': $(ui.draggable).attr('src')
                },
                'type': 'GET',
                'success': function (data, textStatus, jqXHR) {
                    if (data.success == true) {

                    } else {

                    }
                }
            });
        } else {
            removeMode = false;
        }
    }
});

// $('#mainSideMenu li img').each(dragInit);

function dragInit(index, element) {
    $(element).draggable();
    var parent = $(element).parent(); // Это Li
    var elementClone = $(element).clone();
    var href = $(element).parent().attr('href');
    $(element).on('drag', onDrag);
    $(element).on('dragstop', function (event, ui) {
        if ($(parent).find(element).length == 0) { // Если это 0, то нужно сделать вставку элемента назад на то место, где теперь пустота
            var elementDoubleClone = $(elementClone).clone();
            var text = $(parent).text();
            $(parent).html(elementDoubleClone).append(text);
            // Меняем href
            $(element).parent().prop('href', href);
            $('#mainSideMenu li img').each(dragInit);
            $('#quickPanel img:not(.recycleBin)').each(panelDragInit);
        }
    });

    $(element).on('dragstart', function (event, ui) {
        $(element).css({
            'position': 'absolute',
            'left': 0,
            'top': 0,
            'right': 0,
            'bottom': 0
        });
    })

    function onDrag(event, ui) {
        $(element).css({
            'z-index': 9999
        });
    }
}

/* Tooltips */
/*$('input[type="text"]').tooltip({
'trigger' : 'focus',
'delay': {
'show': 200,
'hide': 200
}
});*/

$('#fontPlus').on('click', function (e) {
    var fontSize = parseInt($('.sampleLetterSize').css('font-size'));
    $('#fontPlus, #fontMinus').attr('disabled', true);
    $.ajax({
        'url': '/style/changefontsize',
        'cache': false,
        'dataType': 'json',
        'data': {
            'size': fontSize + 1
        },
        'type': 'GET',
        'success': function (data, textStatus, jqXHR) {
            if (data.success) {
                location.reload();
            }
        }
    });
});
$('#fontMinus').on('click', function (e) {
    var fontSize = parseInt($('.sampleLetterSize').css('font-size'));
    $('#fontPlus, #fontMinus').attr('disabled', true);
    $.ajax({
        'url': '/style/changefontsize',
        'cache': false,
        'dataType': 'json',
        'data': {
            'size': fontSize - 1
        },
        'type': 'GET',
        'success': function (data, textStatus, jqXHR) {
            if (data.success) {
                location.reload();
            }
        }
    });
});

$('select[multiple="multiple"]').each(function(index, combo) {
	/* var beforeClicked = $(combo).val() != null ? $(combo).val() : [];
	$(combo).find('option').each(function(index2, option) {
		$(option).on('click', function(e) {
			var optionValue = $(option).prop('value');
			for(var i = 0; i < beforeClicked.length; i++) {
				if(optionValue == beforeClicked[i]) {
					beforeClicked.splice(i, 1);
					$(combo).val(beforeClicked);
					return false;
				}
			}
			beforeClicked.push(optionValue);
			$(combo).val(beforeClicked);
			return false;
		});
		$(option).on('mousedown', function(e) {
			var toVal = beforeClicked.concat([$(this).val()]);
			$(combo).val(toVal);
		});
	}); */
});

$('select[multiple="multiple"]').each(function(index, select) {
	/*$(select).on('scroll', function(e) {
		console.log(select);
		e.stopPropagation();
		return false;
	});*/ 
});
    $('.buttonUpContainer').click(function () {
        // Смотрим - есть ли у this класс "backWardButton"
        if ($(this).hasClass('backWardButton'))
        {
            if (globalVariables.lastScrollTop>0)
            {
                globalVariables.notChangeNavButton = true;
                // Ставим на setTimeOut чтобы после перевода состояния сделать кнопку "Наверх"
                setTimeout(
                    function (){
                        // Показываем контейнер навигации
                        $('.buttonUpContainer').show();
                        // Делаем кнопку "Наверх"
                        $('.buttonUpText').text("Наверх");
                        $('.buttonUpSign').removeClass("glyphicon-chevron-down");
                        $('.buttonUpSign').addClass("glyphicon-chevron-up");
                        globalVariables.notChangeNavButton = false;
                        $('.buttonUpContainer').removeClass('backWardButton');
                    },
                    800
                );
                // Возвращаем скролл
                $('body,html').animate({
                    scrollTop: globalVariables.lastScrollTop
                }, 599);
            }

        }
        else
        {
            globalVariables.lastScrollTop = $(window).scrollTop();

            globalVariables.notChangeNavButton = true;
            setTimeout(
                function (){
                    // Показываем контейнер навигации
                    $('.buttonUpContainer').show();

                    // Делаем кнопку "Назад"
                    $('.buttonUpText').text("Назад");
                    $('.buttonUpSign').removeClass("glyphicon-chevron-up");
                    $('.buttonUpSign').addClass("glyphicon-chevron-down");
                    $('.buttonUpContainer').addClass('backWardButton');
                    globalVariables.notChangeNavButton = false;
                },
                800
            );
            $('body,html').animate({
                scrollTop: 0
            }, 599);

        }
        return false;
    });

 globalVariables.notChangeNavButton = false;
 // Кнопка закрытия приёма в панели 
 if($('#medcardContentSave').length > 0) {
	$('.buttonUpContainer').append($('#medcardContentSave').clone().prop({
		'id' : 'sideMedcardContentSave',
		'value' : 'Сохранить',
		'title' : 'Сохранить текущий приём'
		}).css(
		{
			'marginTop': '100px',
			'width' : '85px',
			'marginLeft' : '8px'
		})
	);
 }

    // Кнопка печати слева
    if($('#printContentButton').length > 0) {
        $('.buttonUpContainer').append($('#printContentButton').clone().prop({
            'id' : 'sidePrintContentButton',
            'value' : 'Печать',
            'title' : 'Распечатать приём'
        }).css(
            {
                'marginTop': '3px',
                'width' : '85px',
                'marginLeft' : '8px'
            })
        );
    }


    $(window).scroll(
        function() 
        {
            if (!globalVariables.notChangeNavButton)
            {
                globalVariables.lastScrollTop = -1;
               // Делаем кнопку "Наверх"
                $('.buttonUpText').text("Наверх");
                $('.buttonUpSign').removeClass("glyphicon-chevron-down");
                $('.buttonUpSign').addClass("glyphicon-chevron-up");
                 $('.buttonUpContainer').removeClass('backWardButton');
            }

          if ($(this).scrollTop() > 100) {
              $('.buttonUpContainer').show();
            } else {
              $(".buttonUpContainer").hide();
            }
        }
    );

    /* Двигающиеся модалки */
    $('.modal:not([id="addFakePopup"])').draggable();

    // По нажатию на кнопку "удалить" - спрашиваем подтверждение на удаление
    $('button[id^=delete]').filter(
		':not(#deleteMedworker, #deleteTemplate, #deleteWard)'
	).on('click',function(e)
    {
        response = confirm ('Вы действительно хотите выполнить удаление?');
        if (!response)
            e.stopImmediatePropagation();
    });

    // Дальше идёт треш по сообщениям о больным, которым плохо
    // ------------------------------->
        var cont = $('.alerts-cont');
        $(cont).find('.panel-arrow').on('click', function(e) {
            if($(this).find('span').hasClass('glyphicon-expand')) {
                //show)
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

        /*
         function showIndicators(cont)
         {
         $(cont).animate({
         'left' : '0'
         }, 500, function() {
         $(cont).find('.panel-arrow span').removeClass('glyphicon-expand').addClass('glyphicon-collapse-down');
         });
         }

         function closeIndicators(cont)
         {
         $(cont).animate({
         'left' : '-250px'
         }, 500, function() {
         $(cont).find('.panel-arrow span').removeClass('glyphicon-collapse-down').addClass('glyphicon-expand');
         });
         }
         */

        wasLoadedMessages = false;
        function refreshIndicators()
        {
            console.log('Тест');

            $.ajax({
                'url' : '/doctors/patient/getindicators',
                'data' : {
                },
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true || data.success == 'true') {
                        if (data.data!='0')
                        {
                            $('.alarm-button').addClass('is-patients-to-check');
                            $('.alarm-button img').removeClass('no-display');

                            // Через полсекунды кнопка пропадает
                            setTimeout(
                                function ()
                                {
                                    $('.alarm-button img').addClass('no-display');
                                },
                                1000
                            );
                        }
                        else
                        {
                            // Кнопочка гасится
                            $('.alarm-button img').removeClass('is-patients-to-check');
                            $('.alarm-button img').addClass('no-display');
                        }
                    } else {

                    }
					// Устанавливаем тайм-аут
				//	setTimeout(refreshIndicators,2000);
                }
            });
        }
        //setTimeout(refreshIndicators,2000);

        // По клику на кнопку перенаправляемся на страницу со списком мониторингов
        $(document).on('click', '.is-patients-to-check', function()
        {
            // Перенаправляем на страницу
            location.href = '/doctors/patient/viewmonitoring?alarm=1'
        });


    // <-------------------------------

    $('#doctor-search-reset').click(
        function ()
        {
            $(this).parents('form')[0].reset();
            return false;
        }
    );

    $(document).on('keydown', function(e) {
        if(e.keyCode == 27) {
            $('.modal').modal('hide');
        }
    });

    $('.modal').on('show.bs.modal', function(e) {
        $(this).css('overflow-y', 'scroll');
        $('html').css('overflow-y', 'hidden');
    });

    $('.modal').on('hide.bs.modal', function(e) {
        $(this).css('overflow-y', 'hidden');
        $('html').css('overflow-y', 'scroll');
        $('.navbar-fixed-top').css('margin-right', 0);
    });

    $.fn.reduceCladrCode = function (codeToReduce){
        result = '';

        result = codeToReduce.substr(0,7);
        if (codeToReduce.length>7)
            result += '...';

        return result;
    };

    // Поменять элемент в фокусе. Вызывается если нельзя больше печатать в элемент, если уже заполнен
    $.fn.switchFocusToNext = function()
    {
        // Выбираем все focus-able элементы
        var focusables = $(':tabbable, .controlTableContentCell').filter(':not(.prev, .next)');
		console.log(focusables);
		console.log($(document.activeElement));
		for (var i = 0; i < focusables.length; i++) {
            // Проверяем - является ли и-тый элемент из фокусабельных элементом,
            //    на котором сейчас стоит фокус
            if ($(focusables[i])[0] == $(document.activeElement)[0]) {

                elementToFocus = null;
                // Тут может быть две ситуации - либо элемент последний в массиве либо нет
                if (i == focusables.length - 1){
                    // Фокусируемся на первый элемент
                    elementToFocus = $(focusables[0]);
                } else {
                    // Фокусируемся на следующий по номеру элемент
                    elementToFocus = $(focusables[i + 1]);
                }

                // Если элемент имеет класс controlTableContentCell, то на него нужно запустить событие клик
                if ( $(elementToFocus).hasClass('controlTableContentCell')  )
                {
                    $(elementToFocus).trigger('click');
                }
				
                $(elementToFocus).focus();
                break;
            }
        }
    }

    // Переходы по Enter-у
    // ------------------>
    enterButtonsSelector =
    [
    ];
    $(document).on('keydown' ,function(e){
        if ((e.keyCode==13 || e.keyCode==9) && (!e.ctrlKey))
        {
            //console.log('Нажата клавиша Enter');
            //console.log(e.currentTarget);
            //console.log($(':focus'));


            // Смотрим что в фокусе - если
            //focusedElement = $($(':focus')[0]);
            focusedElement = $(document.activeElement);
            // Дальше может быть следующее развитие ситуации.
            //   Если в фокусе такой элемент, который не должен засабмитить форму, то нужно перекинуть
            //     фокус на следующий focusable элемент.
            //    Если элемент сабмитит форму, то надо затриггерить на нём клик
            //   Нужно определить является ли элемент таким, который сабмитит форму
            // По умолчанию - не сабмитит
            submittable = false;
            // Если кнопка в форме одна - и она в фокусе то она вызывает сабмит
            containingForm = $(focusedElement).parents('form');

            if (e.keyCode==13)
            {

                //buttons = $(containingForm).find('input[type=submit], input[type=button], button:not(.accordion-clone-btn)');

                // Если в фокусе форма логина пароля
                if ($(containingForm).length>0)
                {
                    if ( $($(containingForm)[0]).attr('id')=='login-form' )
                    {
                        $($(containingForm)[0]).find('input[type=submit]').click();
                    }
                }

                buttons = $(containingForm).find('input[type=submit], input[type=button], button:not(.accordion-clone-btn)');
                if ($(buttons).length = 1 && buttons[0]==focusedElement[0])
                {
                    $(buttons[0]).trigger('click');
                    e.preventDefault();
                    return;
                }
                // Если у сфокусированного элемента есть класс "btn-success" и она одна на форме - её сабмитим
                classedButtons = $(containingForm).find('.btn-success');
                if ($(classedButtons).length = 1 && classedButtons[0]==focusedElement[0])
                {
                    $(classedButtons[0]).trigger('click');
                    e.preventDefault();
                    return;
                }
                // Если у кнопки Value = "Найти" - сабмитим её.
                //   Это конечно криминал (проверять кнопку по ей тексту в интерфейсе),
                //    но пока другого выхода не вижу
                if ( $(focusedElement).is('input[type=submit], input[type=button], button:not(.accordion-clone-btn)') )
                {
                    if ($(focusedElement).val()=='Найти')
                    {
                        $(buttons[0]).trigger('click');
                        e.preventDefault();
                        return;
                    }
                }

                // Ну и совсем на худой конец - берём массив и проверяем, входит ли фокусированный элемент в него
                submitButtons = enterButtonsSelector.join(', ');
                submitButtonsSelected = $(submitButtons);
                // Если is выдаст true на фокусированные элементы и на выбранные по селектору
                if ($(submitButtonsSelected).is(  $(focusedElement)   ))
                {
                    $(buttons[0]).trigger('click');
                    e.preventDefault();
                    return;
                }

            }


            // Ну если мы ничего не затриггерили и попали в эту точку - то можно переводить фокус
            // Берём форму, в которой находится элемент.
            //   Если формы нет - перещёлкиваем на новый элемент

            if ($(containingForm).length==0)
            {
                $.fn.switchFocusToNext();
            }
            else
            {
                // Иначе берём таббабельные элементы из формы и
                tabblesElements = $(containingForm).find(':tabbable, .controlTableContentCell');
                for (i=0;i<tabblesElements.length;i++)
                {
                    // Проверяем - является ли и-тый элемент из фокусабельных элементом,
                    //    на котором сейчас стоит фокус
                    if ($(tabblesElements[i])[0] == $(document.activeElement)[0])
                    {
                        // Тут может быть две ситуации - либо элемент последний в массиве
                        //   либо нет
                        elementToFocus = null;
                        if (i==tabblesElements.length-1)
                        {
                            // Фокусируемся на первый элемент
                            //$(tabblesElements[0]).focus();
                            elementToFocus = $(tabblesElements[0]);
                        }
                        else
                        {
                            // Фокусируемся на следующий по номеру элемент
                            //$(tabblesElements[i+1]).focus();
                            elementToFocus = $(tabblesElements[i+1]);
                        }
                        if (  $(elementToFocus).hasClass('controlTableContentCell')  )
                        {
                            $(elementToFocus).trigger('click');
                        }
                        $(elementToFocus).focus();
                        break;
                    }
                }
            }


            //$.fn.switchFocusToNext();
            // Дальше выключаем обработку этого события
            e.preventDefault();
            return;
        }
        else
        {
            if (e.keyCode==13 || e.keyCode==9)
            {
                focusedElement = $(document.activeElement);

                // Смотрим - если в фокусе элемент textarea
                if ($(focusedElement).is('textarea'))
                {
                    currentPosition = $(focusedElement)[0].selectionStart;
                    // Вставляем в позицию перевод строки
                    $($(focusedElement)[0]).splice(
                        $(focusedElement)[0].selectionStart,
                        $(focusedElement)[0].selectionEnd - $(focusedElement)[0].selectionStart
                    );
                    // Добавляем символ перевода строки в позицию SelectionStart
                    left = $($(focusedElement)[0]).val().substr(0,$(focusedElement)[0].selectionStart);
                    right = $($(focusedElement)[0]).val().substr($(focusedElement)[0].selectionStart);
                    $($(focusedElement)[0]).val(
                        left + '\r\n' + right
                    );
                    // Устанавливаем текущую позицию в currentPosition + 2
                    $(focusedElement)[0].selectionStart =currentPosition +1;
                    $(focusedElement)[0].selectionEnd = $(focusedElement)[0].selectionStart ;



                }

            }

            // Смотрим - если в фокусе элемент textarea
        }
    });
    //   Конец блока переходов по Enter-у
    // <-----------------
	
		
	systemFuncs = {
		sessionTimer : null,
		sessionTime : null,
		checkOnlineDataTime: 60000,
		setSessionTimer : function(value) {
			this.sessionTime = value;
			this.setSessionInterval(value, this);
			var _this = this;
			$(document).on('keydown', function() {
				_this.setSessionInterval(value, _this);
			});
			$(document).on('click', 'a, input, select, button', function() {
				_this.setSessionInterval(value, _this);
			});
		},
		setSessionInterval: function(value, _this) {
			/*clearTimeout(_this.sessionTimer);
			console.log('Timer reseted.');
			_this.sessionTimer = setTimeout(function() {
				$.ajax({
					'url' : '/users/logout',
					'cache' : false,
					'dataType' : 'json',
					'type' : 'GET',
					'success' : function(data, textStatus, jqXHR) {
						if(data.success == 'true') {
							location.href = '/';
						}
					},
				});
			}, value * 1000);*/
		},
		checkOnlineData: function() {
			$.ajax({
				'url' : '/system/getonlinedata',
				'cache' : false,
				'dataType' : 'json',
				'type' : 'GET',
				'success' : function(data, textStatus, jqXHR) {
					if(data.success) {
						var data = data.data;
						if(data.hasOwnProperty('isActiveSession') && data.isActiveSession == 1) {
							systemFuncs.setSessionInterval(systemFuncs.sessionTime, systemFuncs);
						}
					}
				},
			});
			
			setTimeout(systemFuncs.checkOnlineData, systemFuncs.checkOnlineDataTime);
		}
	};
	
	/* Забор всех настроек для клиентской стороны */
	$.ajax({
		'url' : '/system/getsettings',
		'cache' : false,
		'dataType' : 'json',
		'type' : 'GET',
		'success' : function(data, textStatus, jqXHR) {
			if(data.success) {
				var data = data.data;
				for(var i = 0; i < data.length; i++) {
					systemFuncs[data[i].func].call(systemFuncs, data[i].value);
				}
				
				setTimeout(function() {
					systemFuncs.checkOnlineData(); 
				}, systemFuncs.checkOnlineDataTime);
			}
		},
	});
	
	
});
// pre
