$(document).ready(function() {
    $('.subcontrol input').val('');
    $('.subcontrol input').on('change', function(e) {
        var container = $(this).parents('.subcontrol');
        var day = $(container).find('input.day');
        var allowChange = true;
        if($(day).val() == '') {
            allowChange = false;
        }
        var month = $(container).find('input.month');
        if($(month).val() == '') {
            allowChange = false;
        }
        var year = $(container).find('input.year');
        if($(year).val() == '') {
            allowChange = false;
        }

        if(allowChange) {
            $(this).parents('.form-group').find('.date input').trigger('change', [1]);
        }
    });

    $('.subcontrol input.day').on('keyup', function(e) {
        // С табуляции не переводить на следующее поле
        if(e.keyCode == 38 || e.keyCode == 40) {
            return false;
        }

        if($(this).val().length == 2 && e.keyCode != 9) {
            var field = $(this).parent().next().find('input.month');
            $(field).focus();
            $(field).val('');
        } else {
            $(this).val(String.fromCharCode(e.keyCode));
        }
    }).on('keydown', function(e) {
        if($(this).val().length == 2) {
            $(this).select();
            if(e.keyCode == 38 || e.keyCode == 40) {
                return false;
            }
            // Если есть выделение, то тогда можно вводить символы
            if(e.keyCode != 9) {
                $(this).val(String.fromCharCode(e.keyCode));
            }
        }
        if(e.keyCode != 9 && !/^([1-9]|([1][0-9])|([2][0-9])|([3][0-1]))$/.test($(this).val() + '' + String.fromCharCode(e.keyCode))) {
            return false;
        }
    });

    $('.subcontrol input.month').on('keyup', function(e) {
        // С табуляции не переводить на следующее поле
        if(e.keyCode == 38 || e.keyCode == 40) {
            return false;
        }
        if($(this).val().length == 2 && e.keyCode != 9) {
            var field = $(this).parent().next().find('input.year');
            $(field).val('');
            $(field).focus();
        } else {
            $(this).val(String.fromCharCode(e.keyCode));
        }
    }).on('keydown', function(e) {
        if($(this).val().length == 2) {
            $(this).select();
            if(e.keyCode == 38 || e.keyCode == 40) {
                return false;
            }
            // Если есть выделение, то тогда можно вводить символы
            if(e.keyCode != 9) {
                $(this).val(String.fromCharCode(e.keyCode));
            }
        }
        if(e.keyCode != 9 && !/^([1-9]|([1][0-2]))$/.test($(this).val() + '' + String.fromCharCode(e.keyCode))) {
            return false;
        }
    });

    // Фокус на next.next, поскольку следующий контрол есть плюсики и минусики
    $('.subcontrol input.year').on('keyup', function(e) {
        if($(this).val().length == 4 || e.keyCode == 38 || e.keyCode == 40) {
            return false;
        }
    }).on('keydown', function(e) {
        if(e.keyCode == 8) {
            $(this).val('');
            return false;
        }
        if($(this).val().length == 4) {
            console.log("!");
            return false;
        }
        if(e.keyCode != 9 && e.keyCode != 8 && !/^(([12])*([0-9]{0,3})*)$/.test($(this).val() + '' + String.fromCharCode(e.keyCode))) {
            return false;
        }
    });



    // Обрабатывает событие нажатия на кнопку-стрелку для контрола с датой
    // Обрабатывает событие нажатия на кнопку-стрелку для контрола с датой
    function ArrowCalendarClickHandler(Target,Control)
    {
        // Парсим дату
        // Разделяем её на три группы символов
        var DateArray = Control.value.split("-");
        // Если не введён год
        if (DateArray.length==0)  {
            // Добавляем текущй год
            DateArray.push(new Date().getFullYear());
        }
        else
        {
            // Проверяем, является ли первый элемент валидной цифрой для года
            if (!(Number(DateArray[0])>=new Date().getFullYear()-100)&&(Number(DateArray[0])<=new Date().getFullYear()+50))
            {
                // Добавляем текущй год
                DateArray[0] =  new Date().getFullYear();
            }
        }

        // Если не введён месяц
        if (DateArray.length==1)
        {
            // Добавляем текущий месяц
            DateArray.push((new Date().getMonth())+1);

        }
        else
        {
            // Проверяем - валиден ли месяц. Если нет - добавляем текущий
            if (!(Number(DateArray[1])>=1)&&(Number(DateArray[1])<12))
            {
                // Добавляем текущй месяц
                DateArray[1]=new Date().getMonth()+1;
            }
        }

        // Введён ли день
        if (DateArray.length==2)
        {
            // Добавляем текущий день
            DateArray.push(new Date().getDate());
        }
        else
        {
            // Проверяем - валиден ли день, иначе добавляем текущий
            if (!(Number(DateArray[2])>=1)&&(Number(DateArray[2])<31))
            {
                // Добавляем текущй месяц
                DateArray[0] = new Date().getDate()+1;
            }
        }



        // Если всё-таки все группы цифр есть - создаём об'ект даты
        var StructDate = new Date(Number(DateArray[0]), Number(DateArray[1]) - 1, Number(DateArray[2]));

        // В переменной Date распарсенная дата

        // В зависимости от нажатой кнопки - вычисляем дату

        if ($(Target.currentTarget).hasClass('up-year-button'))
        {
            StructDate.setFullYear(StructDate.getFullYear()+1);
        }

        if ($(Target.currentTarget).hasClass('up-month-button'))
        {
            StructDate.setMonth(StructDate.getMonth()+1);
        }

        if ($(Target.currentTarget).hasClass('up-day-button'))
        {
            StructDate.setDate(StructDate.getDate()+1);
        }

        if ($(Target.currentTarget).hasClass('down-year-button'))
        {
            StructDate.setFullYear(StructDate.getFullYear()-1);
        }

        if ($(Target.currentTarget).hasClass('down-month-button'))
        {
            StructDate.setMonth(StructDate.getMonth()-1);
        }

        if ($(Target.currentTarget).hasClass('down-day-button'))
        {
            StructDate.setDate(StructDate.getDate()-1);
        }

        // Преобразовываем изменённую дату обратно и записываем в контрол

        // Преобразуем компоненты в строковое представление с ведущими нулями
        var dd = StructDate.getDate();
        if (dd<10)
        {
            dd= '0'+dd;
        }
        var mm = StructDate.getMonth() + 1;
        if (mm<10)
        {
            mm= '0'+mm;
        }
        var yyyy = StructDate.getFullYear();

        // Записываем измененённое значение даты в контрол
        Control.value = yyyy+'-'+mm+'-'+dd;
        $(Control).trigger('change');
    }

    // Стрелки вверх-вниз для листания
    // Сначала стрелки вверх
    (function () {
        // Выбираем все контролы дат
        var Controls = $('div.date input');
        // Перебираем выбранные контролы
        for (i = 0; i < Controls.length; i++) {
            // Замыкаем ссылку на каждый контрол
            (function (Control) {
                // Подвязываем обработчик события нажатия на верхние кнопки для контрола
                var btnPrev = $(Control).parents('div.form-group').prev().find('button');
                var btnNext = $(Control).parents('div.form-group').next().find('button');
                $(btnPrev).on('click',function (e) {
                    ArrowCalendarClickHandler(e,Control);
                });
                // Подвязываем обработчик события нажатия на нижние кнопки для контрола
                $(btnNext).on('click',function (e) {
                    ArrowCalendarClickHandler(e,Control);
                });
                // Обработчик на субконтролы, если оные есть
                var subcontrol = $(Control).parents('.form-group').find('.subcontrol');
                if(typeof subcontrol != 'undefined') {
                    $(subcontrol).find('input.day').on('keydown', function(e) {
                        // Вниз
                        if(e.keyCode == 40) {
                            $(btnNext[0]).trigger('click');
                        }
                        // Вверх
                        if(e.keyCode == 38) {
                            $(btnPrev[0]).trigger('click');
                        }
                    });
                    $(subcontrol).find('input.month').on('keydown', function(e) {
                        // Вниз
                        if(e.keyCode == 40) {
                            $(btnNext[1]).trigger('click');
                        }
                        // Вверх
                        if(e.keyCode == 38) {
                            $(btnPrev[1]).trigger('click');
                        }
                    });
                    $(subcontrol).find('input.year').on('keydown', function(e) {
                        // Вниз
                        if(e.keyCode == 40) {
                            $(btnNext[2]).trigger('click');
                        }
                        // Вверх
                        if(e.keyCode == 38) {
                            $(btnPrev[2]).trigger('click');
                        }
                    });
                }
            })(Controls[i]);
        }
    }
    )();

    // Поля дат
    (function initDateFields(dateFields) {
        var format = 'yyyy-mm-dd';
        for(var i = 0; i < dateFields.length; i++) {
            if($(dateFields[i]).length == 0) {
                continue;
            }
            $(dateFields[i]).datetimepicker({
                language: 'ru',
                format: format,
                weekStart: 1,
                todayBtn:  1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                minView: 2,
                forceParse: 0
            });
            $(dateFields[i]).find('input').on('keyup', function(e) {
                var value = $(this).val();
                // Дата по регулярке
                if((value.length == 4 || value.length == 7) && e.keyCode != 8) { // Введён год или месяц..
                    $(this).val(value + '-');
                }
                if((value.length == 5 || value.length == 8) && e.keyCode == 8) { // Убрать автоматически прочерк
                    $(this).val(value.substr(0, value.length - 1));
                }
            }).on('change', function(e, type){
                    var subcontrols = $(this).parents('.form-group').find('.subcontrol');
                    if(typeof subcontrols != 'undefined') {
                        var day = $(subcontrols).find('input.day');
                        var month = $(subcontrols).find('input.month');
                        var year = $(subcontrols).find('input.year');
                        // Аргумент type говорит о том, в каком направлении нужно писать: из контролов в субконтролы или наоборот.
                        // Из суб в настоящий
                        if(typeof type == 'undefined') {
                            var currentDate = $(this).val();
                            var parts = currentDate.split('-');
                            $(day).val(parts[2]);
                            $(month).val(parts[1]);
                            $(year).val(parts[0]);
                        } else { // Из настоящего в суб
                            console.log(year.val() + '-' + month.val() + '-' + day.val());
                            $(this).val(year.val() + '-' + month.val() + '-' + day.val());
                        }
                    }
                });
            $(dateFields[i]).find('input').on('keydown', function(e) {
                // Разрешить бекспейс, цифры, табуляция, Enter
                var isAllow = true;
                // Если символ Enter или Tab - сразу возвращаем true
                if ((e.keyCode == 13)||(e.keyCode == 9))
                    return true;
                var value = $(this).val();
                if(value.length == 10 && e.keyCode != 8) {
                    isAllow = false;
                } else {
                    if(!(e.keyCode > 47 && e.keyCode < 58) && !(e.keyCode > 95 && e.keyCode < 106) && e.keyCode != 8) {
                        isAllow = false;
                    }
                }
                if((value.length == 4 || value.length == 7) && e.keyCode != 8) { // Введён год или месяц..
                    $(this).val(value + '-');
                }
                return isAllow;
            });
        }
    })([
        '#birthday-cont',
        '#document-givedate-cont',
        '#search-date-cont',
        '#current-date-cont',
        '#date-cont',
        '#dateBegin-cont',
        '#dateEnd-cont',
        '#dateBeginEdit-cont',
        '#dateEndEdit-cont'
    ]);
});