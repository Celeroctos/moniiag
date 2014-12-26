$(document).ready(function() {
    var controls = $('.controltable');

    $.fn['tableControl']= {
        init: function(newControl)
        {
            initTableControlInternal(newControl);
        }
    };

    function initTableControlInternal(control) {
        var clickFunc = function(e) {
            var content = $(this).html();
            var input = $('<input>').prop({
                'type' : 'text',
                'value' : content,
                'class' : 'form-control'
            });

            $(input).on('blur', function(e) {
                var inputVal = $(this).val();
                var parentTd =  $(this).parent();
                $(parentTd).html(inputVal);
                $(parentTd).on('click', clickFunc);
                // Теперь записываем значения в виде таблицы в скрытое поле
                var tds = $(control).find('td.controlTableContentCell');
                var hiddenJson = [];
                for(var i = 0; i < tds.length; i++) {
                    var coordsStr = $(tds[i]).prop('class').substr($(tds[i]).prop('class').lastIndexOf('-') + 1);
                    var coords = coordsStr.split('_');
                    if(typeof hiddenJson[coords[0]] == 'undefined') {
                        hiddenJson[coords[0]] = [];
                    }
                    hiddenJson[coords[0]][coords[1]] = $.trim($(tds[i]).html());
                }
                $(control).parent().find('input[type="hidden"]').val($.toJSON(hiddenJson));
            });

            $(this).html(input);
            $(this).off('click');
            $(this).find('input').focus();
        };

        var keyDownFunc = function(e) {
            currentControlTable =  $(this).parents('.controltable')[0];
            // Смотрим - если нажали на таб - надо прейти на следующую ячейку
            if (e.keyCode == 9  || e.keyCode == 13)
            {
                // Ищем родителя у текущего элемента


                // Дальше надо найти следующий элемент с контентом в таблице и затриггерить событие click для ячейки
                tdFromTable = $(currentControlTable).find('td.controlTableContentCell');
                // Найдём ячейку, в котором мы находимся
                i = 0;
                while ( $(tdFromTable[i]).find('input').length<=0 && (i<tdFromTable.length) )
                {
                    i++;
                }

                // Мы находимся в i-ой ячейке
                // Если i = length-1, то идём в первую. Иначе - в следующую (i+1)
                if (i == tdFromTable.length -1 )
                {
                    return;
                }
                else
                {
                    $(tdFromTable[i+1]).click();
                }

                e.preventDefault();
                return false;
            }

            // Если код клавиши один из четырёх чисел. Это стрелки в четыре направления
            if ( ([37,38,39,40]).indexOf(e.keyCode)>=0 )
            {
                console.log('Нажата стрелка');
                // Выберем активный инпут
                activeInput = $(currentControlTable).find('td.controlTableContentCell input');
                // Вот тут надо проверить.
                //  Если мы нажали стрелку назад <- и при этом курсор не стоит в начале текстового инпута, то мы ничего
                //    ничего не делаем. Аналогично нужно проверить и для стрелки -> а то, стоит ли курсор в конце
                //    текстового инпута
                cancelArrowPress = false;
                if ( (e.keyCode==37)&&(  activeInput[0].selectionStart!=0  )  )
                {
                    cancelArrowPress = true;
                }

                if ( (e.keyCode==39)&&(
                    activeInput[0].selectionEnd != $(activeInput).val().length
                    )  )
                {
                    cancelArrowPress = true;
                }
                if (!cancelArrowPress)
                {
                    // Теперь нам надо посмотреть по какой оси мы должны переместиться. В зависимости от этого мы по разному будем перемещаться
                    if (e.keyCode == 37 || e.keyCode == 39)
                    {
                        // Перемещаемся внутри строки
                        parentTd = $(activeInput).parents('td')[0];
                        targetTD = null;


                        if (e.keyCode==37)
                        {
                            targetTD = $(parentTd).prev('.controlTableContentCell');
                        }
                        else
                        {
                            targetTD = $(parentTd).next('.controlTableContentCell');
                        }
                        //console.log('targetTD =');
                        //console.log(targetTD);
                        // Если длина целевого объекта не равна нулю - переходим на него
                        if (targetTD.length != 0)
                        {
                            $($(targetTD)[0]).trigger('click');
                        }


                    }

                    if (e.keyCode == 38 || e.keyCode == 40)
                    {
                        // Перемещаемся внутри столбца
                        parentTd = $(activeInput).parents('td')[0];
                        parentTr = $(parentTd).parents('tr')[0];
                        targetRow = null;
                        if (e.keyCode==38)
                        {
                            targetRow = $(parentTr).prev('tr');
                        }
                        else
                        {
                            targetRow = $(parentTr).next('tr');
                        }

                        if ($(targetRow).length!=0 )
                        {
                            // А теперь проверим - внутри строки по тому же индексу ячейка обладает ли классом редактируемой ячейки
                            cellIndex = $(parentTd).index();
                            newActiveCell = $(targetRow).find(':eq('+cellIndex.toString()+')');
                            if (  $(newActiveCell).hasClass('controlTableContentCell') )
                            {
                                $(newActiveCell).trigger('click');
                            }

                        }


                    }


                }


            }

        };

        $(control).find('td.controlTableContentCell').on('click', clickFunc);
        $(control).find('td.controlTableContentCell').on('keydown', keyDownFunc);

        // Если при инициализации в контрол было что-то вписано, нужно вписать в эту таблицу
        var jsonValue = $(control).parent().find('input[type="hidden"]').val();
        console.log(jsonValue);
        if($.trim(jsonValue) != '') {
            var initValues = $.parseJSON(jsonValue);
            for(var i = 0; i < initValues.length; i++) {
                for(var j = 0; j < initValues[i].length; j++) {
                    if($.trim(initValues[i][j]) != '') {
                        $(control).find('.content-' + i + '_' + j).html(initValues[i][j]);
                    }
                }
            }
        }
    }


    for(var i = 0; i < controls.length; i++) {
        // Инициализируем контролы таблиц
        //-->
        initTableControlInternal(controls[i]);

        // Потом убрать, старый код
        /*(function(control) {
            var clickFunc = function(e) {
                var content = $(this).html();
                var input = $('<input>').prop({
                    'type' : 'text',
                    'value' : content,
                    'class' : 'form-control'
                });

                $(input).on('blur', function(e) {
                    var inputVal = $(this).val();
                    var parentTd =  $(this).parent();
                    $(parentTd).html(inputVal);
                    $(parentTd).on('click', clickFunc);
                    // Теперь записываем значения в виде таблицы в скрытое поле
                    var tds = $(control).find('td[class^="content"]');
                    var hiddenJson = [];
                    for(var i = 0; i < tds.length; i++) {
                        var coordsStr = $(tds[i]).prop('class').substr($(tds[i]).prop('class').lastIndexOf('-') + 1);
                        var coords = coordsStr.split('_');
                        if(typeof hiddenJson[coords[0]] == 'undefined') {
                            hiddenJson[coords[0]] = [];
                        }
                        hiddenJson[coords[0]][coords[1]] = $.trim($(tds[i]).html());
                    }
                    $(control).parent().find('input[type="hidden"]').val($.toJSON(hiddenJson));
                });

                $(this).html(input);
                $(this).off('click');
                $(this).find('input').focus();
            };

            var keyDownFunc = function(e) {
                // Смотрим - если нажали на таб - надо прейти на следующую ячейку
                if (e.keyCode == 9)
                {
                    // Ищем родителя у текущего элемента
                    currentControlTable =  $(this).parents('.controltable')[0];

                    // Дальше надо найти следующий элемент с контентом в таблице и затриггерить событие click для ячейки
                    tdFromTable = $(currentControlTable).find('td[class^="content"]');
                    // Найдём ячейку, в котором мы находимся
                    i = 0;
                    while ( $(tdFromTable[i]).find('input').length<=0 && (i<tdFromTable.length) )
                    {
                        i++;
                    }

                    // Мы находимся в i-ой ячейке
                    // Если i = length-1, то идём в первую. Иначе - в следующую (i+1)
                    if (i == tdFromTable.length -1 )
                    {
                        return;
                    }
                    else
                    {
                        $(tdFromTable[i+1]).click();
                    }

                    e.preventDefault();
                    return false;
                }
            };

            $(control).find('td.controlTableContentCell').on('click', clickFunc);
            $(control).find('td.controlTableContentCell').on('keydown', keyDownFunc);

            // Если при инициализации в контрол было что-то вписано, нужно вписать в эту таблицу
            var jsonValue = $(control).parent().find('input[type="hidden"]').val();
            console.log(jsonValue);
            if($.trim(jsonValue) != '') {
                var initValues = $.parseJSON(jsonValue);
                for(var i = 0; i < initValues.length; i++) {
                    for(var j = 0; j < initValues[i].length; j++) {
                        if($.trim(initValues[i][j]) != '') {
                            $(control).find('.content-' + i + '_' + j).html(initValues[i][j]);
                        }
                    }
                }
            }
        })(controls[i]);
        */
        //--<
    }
});
var config = {

};