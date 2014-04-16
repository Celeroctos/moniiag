$(document).ready(function () {
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

    $('#omsNumber, #policy').keyfilter(/^[\s\d]*$/);
    $('#firstName, #lastName, #middleName').keyfilter(/^[А-Яа-яЁёa-zA-Z\-]*$/);

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

    // Паспорт (номер)
    $('#docnumber').keyfilter(/^[\d]+$/);
    // Номер карты
    $('#cardNumber').keyfilter(/[\d\\]+/);

    this.initColorFields([
        '.custom-color' // Маркировка анкет
    ]);

    $('#loginSuccessPopup').on('hidden.bs.modal', function () {
        window.location.reload();
    });

    // Форма логина-разлогина
    $("#login-form").on('success', function (eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if (ajaxData.success == 'true') { // Логин прошёл удачно
            /*$('#loginSuccessPopup').modal({
            });*/
            location.href = ajaxData.data;
        } else if (ajaxData.success == 'notfound') {
            $('#loginNotFoundPopup').modal({
        });
    } else {
        $('#loginErrorPopup').modal({
    });
}
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
$('div.date').addClass('date-control');
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
        'url': '/index.php/quickpanel/removeelement',
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
                'url': '/index.php/quickpanel/addelement',
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
        'url': '/index.php/style/changefontsize',
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
        'url': '/index.php/style/changefontsize',
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

});