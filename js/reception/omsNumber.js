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
        }


        /*
        // Берём скрытое поле "номер ОМС"

        hiddenOmsNumber = $('#policy').val();
        omsNumberParts = new Array();
        // Разделим номер ОМС на части.
        //   --------
        //    Этот кусок надо будет полностью переделать
        // Если тип полюса 1 или 2
        //   отрезаем первые 4 символа
        if ($('#omsType').val()=='1' || $('#omsType').val()=='2' || $('#omsType').val()=='4')
        {
            omsNumberParts[0] =  hiddenOmsNumber.substr(0,4);
            omsNumberParts[1] =  hiddenOmsNumber.substr(5);
        }
        else
        {
            if ($('#omsType').val()=='3')
            {
                omsNumberParts[0] =  hiddenOmsNumber.substr(0,3);
                omsNumberParts[1] =  hiddenOmsNumber.substr(4);
            }
            else
            {
                omsNumberParts[0] = hiddenOmsNumber;
            }
        }
        //  >--------
        // Перенесём части номера в input-ы
        // Берём видимы контейнер номера
        inputsToPut = $('.omsNumberContainer:not(.no-display) input');
        // Перебираем input-ы и заносим массив в эти инпуты
        for (i=0;i<omsNumberParts.length;i++)
        {
            $(inputsToPut[i]).val(  omsNumberParts [i] );
        }
        // Занесли.
        */



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

        return isPlaceInInput(6,pressedKey,value);
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

            // Триггерим событие нажатия клавиши на серию
          /*  var newEvent = $.Event('keydown');
            newEvent.which = pressedKey; // null character
            $('.temporaryOmsNumber input.omsNumberPart').trigger(newEvent);
*/

            //---------->
            /*
            $('.temporaryOmsNumber input.omsNumberPart').one('keydown', function(e){
                var e = $.Event('keyup');
                e.which = pressedKey; // null character
                $(this).trigger(e);
            });

            var e = $.Event('keydown');
            e.which = pressedKey; // null character
            $('.temporaryOmsNumber input.omsNumberPart').trigger(e);
            */
            /*var newEvent = $.Event('keypress');
            newEvent.which = pressedKey; // null character
            $('.temporaryOmsNumber input.omsNumberPart').trigger(newEvent);
*/

            //---------->

            return false;

        }
    });


    $('.constantlyOmsNumber input').on('keydown', function (e) {
        var value = $(this).val();
        var pressedKey = e.keyCode;
        return isPlaceInInput(16,pressedKey,value);
    });

    // Возвращает false,если в контроле закончилось место (в том случае, если есть ограничение на количество символов)
    function isPlaceInInput(maximal,pressedKey,valString)
    {
        if (valString.length >= maximal && !(pressedKey == 8 || pressedKey == 46)) {
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

        // Проверяем выражение по регулярке

        // Вынести в отдельный метод
        /*if (newValue.match(/^[А-л]*$/)==null) {
            needRevese = true;
        }*/



        if (needRevese)
        {
            // Возвращаем назад значение
            if (globalVariables.lastOmsState!=undefined)
            {
                $(this).val( globalVariables.lastOmsState.lastValue   );
                // $(this)[0].selectionStart = globalVariables.lastOmsState.lastStart;
                //  $(this)[0].selectionEnd = globalVariables.lastOmsState.lastEnd;
                console.log($(this));
            }
        }
    }

});




// Ценный кусок кода!!! Здесь хранится метод обработки допустимых символов в поле ввода номера ОМС
/*if (false)
$(document).ready(function() {

    // На keydown поля поставим обработчик, в котором сохраним старое значение этого поля и позицию
    $('#lastName').on('keydown',function(e){
        globalVariables.lastOmsState =
        {
            lastValue: $(this).val(),
            lastStart: $(this)[0].selectionStart,
            lastEnd: $(this)[0].selectionEnd
        };
        globalVariables.omsStateTreated = false;
        $("#lastName").one('controlvaluechanged',onControlValueChanged);
    });

    // По событиям изменения значения проверяем это значение и если оно не соответвует - откатываем назад контрол
    $("#lastName").on("propertychange change keyup paste input", function(e){
        if (globalVariables.omsStateTreated!=undefined)
        {
            if (globalVariables.omsStateTreated == false)
            {
                globalVariables.omsStateTreated = true;
                $(this).trigger ('controlvaluechanged', [e]);
            }
        }
    });

    function onControlValueChanged(e)
    {
        $(this).off('controlvaluechanged');
        // Вот тут надо проверить значение контрола
        //   Если оно не соответствует - надо вернуть обратно то значение, которое было до изменения

        newValue = $(this).val();
        needRevese = false;

        // Проверяем выражение по регулярке
        if (newValue.match(/^[А-л]*$/)==null) {
            needRevese = true;
        }

        if (needRevese)
        {
            // Возвращаем назад значение
            if (globalVariables.lastOmsState!=undefined)
            {
                $(this).val( globalVariables.lastOmsState.lastValue   );
               // $(this)[0].selectionStart = globalVariables.lastOmsState.lastStart;
              //  $(this)[0].selectionEnd = globalVariables.lastOmsState.lastEnd;
                console.log($(this));
            }
        }
    }
});*/