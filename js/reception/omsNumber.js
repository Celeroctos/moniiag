$(document).ready(function() {
    // Устанавливаем keyfilter на ОМС номер
    $('.territorialOmsNumber input.omsSeriaPart').keyfilter(/^[0-9A-Za-zА-Яа-я\s\-]*$/);
    $('.territorialOmsNumber input.omsNumberPart').keyfilter(/^[0-9A-Za-zА-Яа-я\s\-]+$/);

    $('.dmsOmsNumber input.omsSeriaPart').keyfilter(/^[0-9A-Za-zА-Яа-я\s\-]*$/);
    $('.dmsOmsNumber input.omsNumberPart').keyfilter(/^[0-9A-Za-zА-Яа-я\s\-]+$/);

    $('.temporaryOmsNumber input.omsSeriaPart').keyfilter(/^[0-9]{1,3}$/);
    $('.temporaryOmsNumber input.omsNumberPart').keyfilter(/^[0-9]{1,6}$/);

    $('.petitionOmsNumber input.omsSeriaPart').keyfilter(/^[0-9A-Za-zА-Яа-я\s\-]*$/);
    $('.petitionOmsNumber input.omsNumberPart').keyfilter(/^[0-9A-Za-zА-Яа-я\s\-]+$/);

    $('.constantlyOmsNumber input').keyfilter(/^[0-9]{1,16}$/);

    // Функция определяет, какой контейнер контролов с номером ОМС нужно показать и показывает его
    function omsContainerShow()
    {
        // Анализируем - какой тип полюсов выбран
        omsTypeValue = $('#omsType').val();
        // Закрываем все контейнеры номеров
        $('.omsNumberContainer').addClass('no-display');
        // Теперь анализируем какое значение у omsType
        switch(omsTypeValue)
        {
            case '1':
                $('.territorialOmsNumber').removeClass('no-display');
                break;


            case '2':
                $('.dmsOmsNumber').removeClass('no-display');
                break;


            case '3':
                $('.temporaryOmsNumber').removeClass('no-display');
                break;


            case '4':
                $('.petitionOmsNumber').removeClass('no-display');
                break;


            case '5':
                $('.constantlyOmsNumber').removeClass('no-display');
                $('#omsSeries').val('');
                break;
        }

    }

    omsContainerShow();

    // На omsType change вешаем обработчик
    $('#omsType').on('change', function(e){
        // Сбрасываем значение номера
        //   из контейнеров номеров разных типов
        $('.omsNumberContainer input').val('');
        // Сбрасываем скрытое поле серии, номера

        // Вызываем смену видимости контейнеров
        omsContainerShow();
        $(document).trigger ('omsnumberpopulate');
    });

    // Обработчик события изменения номера ОМС. Задача этой функции взять компоненты номера из контейнера
    //   соединить их и записать в специальное скрытое поле. Событие должно генерироваться при удачном пропеча
    //    тывании символа в компоненте номера
    $('.omsNumberContainer').on('omsnumberchanged', function(){
        // Проверяем - есть ли в об'екте this инпут, отвечающий за серию
        if ( $(this).find('input.omsSeriaPart').length>0 )
        {
            // Перекачиваем в hidden
            $('#omsSeries').val(  $(this).find('input.omsSeriaPart').val()  );
        }
        // Смотрим - если есть в this-е input c классом "Номер", то читаем значение из него в поле "номер"
        if ( $(this).find('input.omsNumberPart').length>0 )
        {
            $('#policy').val(  $(this).find('input.omsNumberPart').val()  );
        }
        else
        {
            // Прочитываем
            $('#policy').val(  $(this).find('input').val()  );
        }

    });

    // Обработчик события, которое возникает при необходимости заполнить видимый шаблон номера
    $(document).on ('omsnumberpopulate', function(){
        omsContainerShow();
        // Берём видимы контейнер номера
        inputsToPut = $('.omsNumberContainer:not(.no-display)');
        // Смотрим - если в контейнре есть "серия", то переносим в видимое поле "Серия"
        if (  $(inputsToPut).find('.omsSeriaPart').length>0 )
        {
            $(inputsToPut).find('input.omsSeriaPart').val( $('#omsSeries').val() );
            $(inputsToPut).find('input.omsNumberPart').val( $('#policy').val() );
        }
        else
        {
			// Иначе загружаем в поле input
            $(inputsToPut).find('input').val( $('#policy').val() );
            // Если тип полиса - постоянный - убираем пробелы
            if ($('#omsType').val()== 5)
            {
                $('#policy').val(    $('#policy').val().replace(' ','')    );
                $(inputsToPut).find('input').val(    $(inputsToPut).find('input').val().replace(' ','')    );
            }
        }
    });


    // На keydown поля поставим обработчик, в котором сохраним старое значение этого поля и позицию
    $('.omsNumberContainer input').on('keydown',function(e){
        globalVariables.lastOmsState =
        {
            lastValue: $(this).val(),
            lastStart: $(this)[0].selectionStart,
            lastEnd: $(this)[0].selectionEnd
        };
        globalVariables.omsStateTreated = false;
    //    $(this).one('controlvaluechanged',onControlValueChanged);
    });

    $.fn.checkOmsNumber = function()
    {
        serialHidden = $('#omsSeries').val();
        numberHidden = $('#policy').val();
        omsTypeValue = $('#omsType').val();
        result = true;

        switch(omsTypeValue)
        {
            case '1':

            case '2':
                if (numberHidden=='')
                {
                    result = false;
                    printOmsNumberError('Для выбранного типа полюсов номер должен быть заполнен');

                }

                break;


            case '3':
                //$('.temporaryOmsNumber input.omsSeriaPart').keyfilter(/^[0-9]{1,3}$/);
                //$('.temporaryOmsNumber input.omsNumberPart').keyfilter(/^[0-9]{1,6}$/);

                if (serialHidden=='' && numberHidden=='')
                {
                    result = false;
                    printOmsNumberError('Для данного типа полиса обязательно должны быть заполнены Серия и Номер');
                    break;
                }

                if (serialHidden=='')
                {
                    result = false;
                    printOmsNumberError('Для данного типа полиса обязательно должны быть заполнена Серия');
                    break;
                }

                if (numberHidden=='')
                {
                    result = false;
                    printOmsNumberError('Для данного типа полиса обязательно должен быть заполнен Номер');
                    break;
                }

                if ((serialHidden.match(/^[0-9]{3}$/)==null) || (numberHidden.match(/^[0-9]{6}$/)==null))
                {
                    result = false;
                    printOmsNumberError('Не правильно введён номер ОМС. Для данного типа полюсов предполагается наличие трёх цифр в серии и шести - в номере.');
                }

                break;


            case '4':
                break;


            case '5':
                if (numberHidden.match(/^[0-9]{16}$/)==null)
                {
                    result = false;
                    printOmsNumberError('Не правильно введён номер ОМС. Для данного типа полюсов предполагается наличие шестнадцати цифр в номере.');
                }

                break;
        }
        return result;
    }

    function printOmsNumberError(errorText)
    {
        // Сначала трём все старые ошибки
        $('#omsNumberErrorPopup .modal-body .row p').remove();
        $('#omsNumberErrorPopup .modal-body .row').append('<p class="errorText">' + errorText + '</p>')
        $('#omsNumberErrorPopup').modal({
        });

    }

    $('.temporaryOmsNumber input.omsNumberPart').on('keydown', function (e) {
        var value = $(this).val();
        var pressedKey = e.keyCode;
        // Смотрим - если нажатая клавиша бекспейс или стрелка назад и selectionStart = 0 - надо поставить в фокус поле "Серия"
        if ((pressedKey==37 || pressedKey==8)&& this.selectionStart==0)
        {
            $('.temporaryOmsNumber input.omsSeriaPart').focus();
            ($('.temporaryOmsNumber input.omsSeriaPart')[0]).selectionStart =
                ($('.temporaryOmsNumber input.omsSeriaPart').val().length)
            return true;
        }

        //return isPlaceInInput(6,pressedKey,value);

        result = isPlaceInInput(6,pressedKey,value);
        if (!result){$.fn.switchFocusToNext(); }

        return result;
    });

    $('.temporaryOmsNumber input.omsSeriaPart').on('keydown', function (e) {
        var value = $(this).val();
        var pressedKey = e.keyCode;
        if (!isPlaceInInput(3,pressedKey,value))
        {
            // Если нет места в серии временного полиса - надо сделать следующее:
            //   1. Посмотреть - есть ли в соседнем контроле (номер временного полиса)
            //      место (длина меньше либо равна 5). Если меньше - то отрезать первый символ
            //   2. Поставить фокус у контрола номера временного полиса
            //   3. Поставить selectionstart и selectionend у контрола номера временного полиса
            //   4. Затриггерить событие нажатие клавиши на контроле номера временного полиса
            //   5. Подавить событие нажатия клавиши на данном контроле

            if ($('.temporaryOmsNumber input.omsNumberPart').val().length>=6)
            {
                // Отрезаем первый символ
                /*$('.temporaryOmsNumber input.omsNumberPart').val(
                    $('.temporaryOmsNumber input.omsNumberPart').val().substr(1)
                );*/

                // Ничего не делаем, возвращаем false
                return false;
            }
            $('.temporaryOmsNumber input.omsNumberPart').focus();
            // Проверим - является ли вводимый символ цифрой
            if (String.fromCharCode(pressedKey)>='0' && String.fromCharCode(pressedKey)<='9')
            {
                $('.temporaryOmsNumber input.omsNumberPart').val( String.fromCharCode(pressedKey)+
                    $('.temporaryOmsNumber input.omsNumberPart').val()
                );
                $('.temporaryOmsNumber input.omsNumberPart')[0].selectionStart = 1;
                $('.temporaryOmsNumber input.omsNumberPart')[0].selectionEnd = 1;
            }
            else
            {
                $('.temporaryOmsNumber input.omsNumberPart')[0].selectionStart = 0;
                $('.temporaryOmsNumber input.omsNumberPart')[0].selectionEnd = 0;
            }


            return false;

        }
    });




    $('.constantlyOmsNumber input').on('keydown', function (e) {
        var value = $(this).val();
        var pressedKey = e.keyCode;
        //return isPlaceInInput(16,pressedKey,value);
        result = isPlaceInInput(16,pressedKey,value);
        // Если результат = false, то

        if (!result){ $.fn.switchFocusToNext();}
        return result;
    });

    // Возвращает false,если в контроле закончилось место (в том случае, если есть ограничение на количество символов)
    function isPlaceInInput(maximal,pressedKey,valString)
    {
        if (valString.length >= maximal && !(pressedKey == 8 || pressedKey == 46 || pressedKey == 37|| pressedKey == 39)) {
            return false;
        }
        return true;
    }

    $(".omsNumberContainer input").on("propertychange change paste input", function(e){
        // Вызываем событие "Номер полюса изменился"
        $(this).parents('.omsNumberContainer').trigger('omsnumberchanged');
    });

    // Проверить можно ли было вводить символ, который ввёлся последний раз
    function checkOmsNumberOnLive(inputToCheck)
    {
        // Определим, к какому типу относится текущий inputToCheck



    }

    function onControlValueChanged(e)
    {
        $(this).off('controlvaluechanged');
        // Вот тут надо проверить значение контрола
        //   Если оно не соответствует - надо вернуть обратно то значение, которое было до изменения

        newValue = $(this).val();
        needRevese = false;

        if (needRevese)
        {
            // Возвращаем назад значение
            if (globalVariables.lastOmsState!=undefined)
            {
                $(this).val( globalVariables.lastOmsState.lastValue   );
                console.log($(this));
            }
        }
    }

    $('#omsType').trigger('change');
});