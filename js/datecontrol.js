// Перечень контейнеров с контролами дат
    var DateControlContainers =
    [
        '#birthday-cont',
        '#document-givedate-cont',
        '#priv-document-givedate-cont',
        '#policy-givedate-cont',
        '#policy-enddate-cont',
        '#search-date-cont',
        '#current-date-cont',
        '#date-cont',
        '#dateBegin-cont',
        '#dateEnd-cont',
        '#dateBeginEdit-cont',
        '#dateEndEdit-cont',
        '#greetingDate-cont',
	    '#shift-date-begin-cont',
	    '#shift-date-end-cont',
    ];
    
  //   $('.subcontrol input').val('');
    // Обрабатывает событие нажатия на кнопку-стрелку для контрола с датой
    // Обрабатывает событие нажатия на кнопку-стрелку для контрола с датой
   function ArrowCalendarClickHandler(Target, Control)  {
        // Парсим дату
        // Разделяем её на три группы символов
        var DateArray = $(Control).find('input.form-control').val().split("-");
        // Если не введён год
        if (DateArray.length == 0)  {
            // Добавляем текущй год
            DateArray.push(new Date().getFullYear());
        } else {
            // Проверяем, является ли первый элемент валидной цифрой для года
            if (!(parseInt(DateArray[0]) >= new Date().getFullYear() - 100) && (Number(DateArray[0]) <= new Date().getFullYear() + 50)) {
                // Добавляем текущй год
                DateArray[0] =  (new Date()).getFullYear();
            }
        }

        // Если не введён месяц
        if (DateArray.length == 1) {
            // Добавляем текущий месяц
            DateArray.push((new Date().getMonth()) + 1);
        } else {
            // Проверяем - валиден ли месяц. Если нет - добавляем текущий
            if (!(parseInt(DateArray[1]) >= 1) && (parseInt(DateArray[1]) < 12)) {
                // Добавляем текущй месяц
                DateArray[1] = (new Date()).getMonth() + 1;
            }
        }

        // Введён ли день
        if (DateArray.length==2) {
            // Добавляем текущий день
            DateArray.push(new Date().getDate());
        } else  {
            // Проверяем - валиден ли день, иначе добавляем текущий
            if (!(parseInt(DateArray[2]) >= 1) && (parseInt(DateArray[2]) < 31)) {
                // Добавляем текущй день
                DateArray[0] = new Date().getDate() + 1;
            }
        }

        // Если всё-таки все группы цифр есть - создаём объект даты
        var StructDate = new Date(parseInt(DateArray[0]), parseInt(DateArray[1]) - 1, parseInt(DateArray[2]));

        // В переменной Date распарсенная дата
        // В зависимости от нажатой кнопки - вычисляем дату

        if ($(Target.currentTarget).hasClass('up-year-button')) {
            StructDate.setFullYear(StructDate.getFullYear() + 1);
        }

        if ($(Target.currentTarget).hasClass('up-month-button')) {
            StructDate.setMonth(StructDate.getMonth() + 1);
        }

        if ($(Target.currentTarget).hasClass('up-day-button')) {
            StructDate.setDate(StructDate.getDate() + 1);
        }

        if ($(Target.currentTarget).hasClass('down-year-button')) {
            StructDate.setFullYear(StructDate.getFullYear() - 1);
        }

        if ($(Target.currentTarget).hasClass('down-month-button')) {
            StructDate.setMonth(StructDate.getMonth() - 1);
        }

        if ($(Target.currentTarget).hasClass('down-day-button')){
            StructDate.setDate(StructDate.getDate() - 1);
        }

        // Преобразовываем изменённую дату обратно и записываем в контрол
        // Преобразуем компоненты в строковое представление с ведущими нулями
        var dd = StructDate.getDate();
        if (dd < 10) {
            ddWithNull = '0' + dd;
        } else {
            ddWithNull = dd;
        }
        var mm = StructDate.getMonth() + 1;
        if (mm < 10) {
            mmWithNull = '0' + mm;
        } else {
            mmWithNull = mm;
        }
        var yyyy = StructDate.getFullYear();

        // Записываем измененённое значение даты в контрол
        //console.log(Control);
        $(Control).find('input.form-control:first').val(yyyy + '-' + mmWithNull + '-' + ddWithNull);
        $(Control).find('input.form-control:first').trigger('change');
    }

    

    function getSelected(element) {
        return $(element).selection();
    }

   function initDateField(DateField) {
	    var format = 'yyyy-mm-dd';
	    if(DateField.length == 0) {
                return;
        }
        DateField.datetimepicker({
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
       $(DateField).parent().find('label').css({'padding-top' : '25px'});
        var ctrl = DateField.find('input.form-control:first');
        $(ctrl).on('change', function(e, type){
            console.log(e.target);
            var subcontrols = $($(this).parents('div')[0]).find('.subcontrol');
            if(typeof subcontrols != 'undefined') {

                var day = $(subcontrols).find('input.day');
                var month = $(subcontrols).find('input.month');
                var year = $(subcontrols).find('input.year');
                // Аргумент type говорит о том, в каком направлении нужно писать: из контролов в субконтролы или наоборот.
                // Из суб в настоящий
                if(typeof type == 'undefined') {
        //$(subcontrols).find('input').val('');
                    var currentDate = $(this).val();
                    var parts = currentDate.split('-');

        var DayInt = parseInt(parts[2]);
        var MonthInt = parseInt(parts[1]);
        var YearInt =  parseInt(parts[0]);

        // Проверяем - выводим, если ни одна из компонент не равно NaN
        //  Если число равно NaN, то проверка Число == Число выдаст false
        if (DayInt==DayInt && MonthInt==MonthInt && YearInt==YearInt) {

            $(day).val(DayInt);
            $(month).val(MonthInt);
            $(year).val(YearInt);

        }
        //$(day).val(parts[2]);
                    //$(month).val(parts[1]);
                    //$(year).val(parts[0]);
                } else { // Из настоящего в суб
                    $(this).val(year.val() + '-' + month.val() + '-' + day.val());
                }
            }
        });
        if($.trim($(ctrl).val()) != '') {
            $(ctrl).trigger('change');
        }
    }
    
    function initDateControlEventHandlers(Control)
    {
	                // Подвязываем обработчик события нажатия на верхние кнопки для контрола
                var btnPrevNext = $(Control).find('.date-ctrl-up-buttons .btn-group button, .date-ctrl-down-buttons .btn-group button');
                var lastNullEntered = false; // Чтобы считывать 01, 02...
				
		$(btnPrevNext).on('click',function (e) {
                    ArrowCalendarClickHandler(e, Control);
                });
                // Обработчик на субконтролы, если оные есть
                var subcontrol = $(Control).find('.subcontrol');
                $(subcontrol).find('input').on('change', function(e) {
                     var container = $(this).parents('.subfields');
                     var day = $(container).find('input.day');

                     var allowChange = true;
                     if($.trim($(day).val()) == '') {
                        allowChange = false;
                     }

                     var month = $(container).find('input.month');
                     if($.trim($(month).val()) == '') {
                        allowChange = false;
                     }

                     var year = $(container).find('input.year');
                     if($.trim($(year).val()) == '') {
                        allowChange = false;
                     }

                     // Только если все три контрола установлены, дату в нормальном контроле можно менять
                     if(allowChange) {
                         $($(this).parents('div.input-group')[0]).find('input.form-control:first').trigger('change', [1]);
                     }

                     if($.trim($(day).val()) == '' && $.trim($(month).val()) == '' && $.trim($(year).val()) == '') {
                         $(Control).find('input[type="hidden"]').val('');
                     }
                });

                $(subcontrol).find('input.day').on('keyup', function(e) {
                    // Если есть выделение, не переводить контрол автоматом
                    var selected = getSelected(this);
                    if($.trim(selected) != '') {
                        return false;
                    }
					
					if($(this).val().length == 1 && lastNullEntered) { // Значит, реальная длина == 2, нуль был введён...
						lastNullEntered = false;
						$(this).next().focus();
                        $(this).next().select();
					}

                    if(($(this).val().length == 2 || $(this).val().length == 1) && e.keyCode != 9 && (e.keyCode < 37 || e.keyCode > 40)) {
                        if($(this).val().length == 1) {
                            if(parseInt($(this).val()) < 4) {
                                return false;
                            }
                        }
                        $(this).next().focus();
                        $(this).next().select();
                    }

                    if(e.keyCode == 9 && $.trim($(this).val()) != '') {
                        // См. выше не сработает.
                        $(this).select();
                    }
                }).on('keydown', function(e) {
                    $(this).css('background-color', '#ffffff');
                    // Вниз
                    if(e.keyCode == 40) {
                        $(this).parents('.subcontrol').find('.date-ctrl-down-buttons .down-day-button').trigger('click');
                        e.stopPropagation();
                        return false;
                    }
                    // Вверх
                    if(e.keyCode == 38) {
                        $(this).parents('.subcontrol').find('.date-ctrl-up-buttons .up-day-button').trigger('click');
                        e.stopPropagation();
                        return false;
                    }

                    var selected = getSelected(this);
                    // Проверка на корректность введённого дня вообще
                    if((e.keyCode > 48 && e.keyCode < 58) || (e.keyCode > 96 && e.keyCode < 106)) { // Это вообще введены цифры, типа
                        // Очищаем поле, если там есть выделенное
                        if($.trim(selected) != '') {
                            $(this).val('');
                        }
						// Вместо 0-9 - буквы
						if(e.keyCode > 95 && e.keyCode < 106) {
							e.keyCode -= 48;
						}
                        // Это день..
                        // При форме "01, 02" и пр. parseInt даст один символ. А это два. Не давать вводить, если есть ведущий ноль.
                        var day = parseInt('' + $(this).val() + String.fromCharCode(e.keyCode));
                        // День не может быть больше 32 и меньше 1 в общем случае. Нужно получить число дней в месяце, если точная дата задана
                        var month = $(this).parents('.subfields').find('input.month').val();
                        var year = $(this).parents('.subfields').find('input.year').val();

                        if($.trim(month) != '' && $.trim(year) != '') {
                            var numDays = 32 - new Date(year, month - 1, 32).getDate();
                        } else {
                            var numDays = 31;
                        }

                        if(!(day > 0 && day <= numDays)) {
                            $(this).animate({
                                backgroundColor: "rgb(255, 196, 196)"
                            });
                            return false;
                        }
                    } else {
						// Введённый вначале нуль запоминать
						if($(this).val().length == 0 && (e.keyCode == 48 || e.keyCode == 96)) {
							lastNullEntered = true;
							return false;
						}
                        // Стрелки вправо-влево, tab и backspace разрешать, разрешать ведущие нули
                        if(e.keyCode != 9 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 39 && e.keyCode != 16 && e.keyCode != 48 && e.keyCode != 96) {
							$(this).animate({
								backgroundColor: "rgb(255, 196, 196)"
							});
							// Нули просто пропускать
                            return false;
                        }
                    }
                });

                $(subcontrol).find('input.month').on('keyup', function(e) {
                    // Если есть выделение, не переводить контрол автоматом
                    var selected = getSelected(this);
                    if($.trim(selected) != '') {
                        return false;
                    }

					if($(this).val().length == 1 && lastNullEntered) { // Значит, реальная длина == 2, нуль был введён...
						lastNullEntered = false;
						$(this).next().focus();
                        $(this).next().select();
					}
					
                    if(($(this).val().length == 2 || $(this).val().length == 1) && e.keyCode != 9 && (e.keyCode < 37 || e.keyCode > 40)) {
                        if($(this).val().length == 1) {
							if(parseInt($(this).val()) < 2) {
                                return false;
                            }
                        }
                        $(this).next().focus();
                        $(this).next().select();
                    }

                    // Если есть выделение, не переводить контрол автоматом
                    var selected = getSelected(this);
                    if($.trim(selected) != '') {
                        return false;
                    }

                    if(e.keyCode == 9 && $.trim($(this).val()) != '') {
                        $(this).select();
                    }
                }).on('keydown', function(e) {
                    $(this).css('background-color', '#ffffff');
                    // Вниз
                    if(e.keyCode == 40) {
                        $(this).parents('.subcontrol').find('.date-ctrl-down-buttons .down-month-button').trigger('click');
                        e.stopPropagation();
                        return false;
                    }
                    // Вверх
                    if(e.keyCode == 38) {
                        $(this).parents('.subcontrol').find('.date-ctrl-up-buttons .up-month-button').trigger('click');
                        e.stopPropagation();
                        return false;
                    }

                    var selected = getSelected(this);
                    // Проверка на корректность введённого месяца вообще
                    if((e.keyCode > 48 && e.keyCode < 58) || (e.keyCode > 96 && e.keyCode < 106)) { // Это вообще введены цифры, типа
						// Очищаем поле, если там есть выделенное
                        if($.trim(selected) != '') {
                            $(this).val('');
                        }
						// Вместо 0-9 - буквы
						if(e.keyCode > 95 && e.keyCode < 106) {
							e.keyCode -= 48;
						}
                        // Это месяц..
                        var month = parseInt('' + $(this).val() + String.fromCharCode(e.keyCode));
                        // Месяц не может быть меньше 1 и больше 12
                        if(!(month > 0 && month < 13)) {
                            $(this).animate({
                                backgroundColor: "rgb(255, 196, 196)"
                            });
                            return false;
                        }
                        // Теперь смотрим на день. Если день, который поставлен, больше, чем в месяце, то меняем на максимальный день месяца
                        // Для этого смотрим, поставили ли год. Если нет, берём текущий
                        var year = $(this).parents('.subfields').find('input.year').val();
                        if($.trim(year) == '') {
                            year = (new Date()).getFullYear();
                        }
                        var numDays = 32 - new Date(year, month - 1, 32).getDate();
                        var dayField = $(this).parents('.subfields').find('input.day');
                        if(parseInt($(dayField).val()) > numDays) {
                            $(dayField).val(numDays);
                        }
                    } else {
						// Введённый вначале нуль запоминать
						if($(this).val().length == 0 && (e.keyCode == 48 || e.keyCode == 96)) {
							lastNullEntered = true;
							return false;
						}
                        // Стрелки вправо-влево, tab и backspace разрешать
                        if(e.keyCode != 9 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 39 && e.keyCode != 16 && e.keyCode != 48 && e.keyCode != 96) {
                            $(this).animate({
                                backgroundColor: "rgb(255, 196, 196)"
                            });
                            return false;
                        }
                    }
                });

                // Фокус на next.next, поскольку следующий контрол есть плюсики и минусики
                $(subcontrol).find('input.year').on('keyup', function(e) {
                    // Если есть выделение, не переводить контрол автоматом
                    var selected = getSelected(this);
                    if($.trim(selected) != '') {
                        return false;
                    }

                    if(e.keyCode == 9 && $.trim($(this).val()) != '' && (e.keyCode < 37 || e.keyCode > 40)) {
                        $(this).select();
                    }
                }).on('keydown', function(e) {
                    $(this).css('background-color', '#ffffff');
                    // Вниз
                    if(e.keyCode == 40) {
                        $(this).parents('.subcontrol').find('.date-ctrl-down-buttons .down-year-button').trigger('click');
                        e.stopPropagation();
                        return false;
                    }
                    // Вверх
                    if(e.keyCode == 38) {
                        $(this).parents('.subcontrol').find('.date-ctrl-up-buttons .up-year-button').trigger('click');
                        e.stopPropagation();
                        return false;
                    }

                    var selected = getSelected(this);
                    // Если год введён и нет выделения, то не давать больше вводить символы, кроме табуляции и бекспейса
                    if($(this).val().length >= 4 && e.keyCode != 9 && e.keyCode != 8 && $.trim(selected) == '') {
                        return false;
                    }

                    // Проверка на корректность введённого года вообще
                    if((e.keyCode > 47 && e.keyCode < 58) || (e.keyCode > 95 && e.keyCode < 106)) { // Это вообще введены цифры, типа
                        // Очищаем поле, если там есть выделенное
                        if($.trim(selected) != '') {
                            $(this).val('');
                        }
						// Вместо 0-9 - буквы
						if(e.keyCode > 95 && e.keyCode < 106) {
							e.keyCode -= 48;
						}

                        // Это месяц..
                        var year = parseInt('' + $(this).val() + String.fromCharCode(e.keyCode));
                        var length = $(this).val().length + 1; // +1 - добавок от текущей клавиши
                        // Всё зависит от длины ввода
                        var currentYear = (new Date()).getFullYear();
                        var yearParts = currentYear.toString();
                        if(length == 1 && !(year == 1 || year == 2)) {
                            $(this).animate({
                                backgroundColor: "rgb(255, 196, 196)"
                            });
                            return false;
                        }
                        if(length == 2 && !(year >= 19 && year <= parseInt(2 + '' + yearParts[1]))) {
                            $(this).animate({
                                backgroundColor: "rgb(255, 196, 196)"
                            });
                            return false;
                        }
                        if(length == 3 && !(year >= 190 && year <= parseInt(2 + '' + yearParts[1] + '' + yearParts[2]))) {
                            $(this).animate({
                                backgroundColor: "rgb(255, 196, 196)"
                            });
                            return false;
                        }
                        if(length == 4 && !(year >= 1900 && year <= parseInt(2 + '' + yearParts[1] + '' + yearParts[2] + yearParts[3]))) {
                            $(this).animate({
                                backgroundColor: "rgb(255, 196, 196)"
                            });
                            return false;
                        }
                    } else {
                        // Стрелки вправо-влево, tab и backspace разрешать. И шифт
                        if(e.keyCode != 9 && e.keyCode != 8 && e.keyCode != 37 && e.keyCode != 39 && e.keyCode != 16) {
                            $(this).animate({
                                backgroundColor: "rgb(255, 196, 196)"
                            });
                            return false;
                        }
                    }
                });
    }
    
    // Стрелки вверх-вниз для листания
    // Сначала стрелки вверх
   function initDateEventHandlers () {
        var Controls = [];
        for(var OneControlContainer = 0; OneControlContainer < DateControlContainers.length; OneControlContainer++) {
            var ControlSelector = DateControlContainers[OneControlContainer];
		    var ControlsToPush = $(ControlSelector);
		
            for (j=0;j<ControlsToPush.length;j++) {
                Controls.push(ControlsToPush[j]);
            }
        }
        // Выбираем все контролы дат
        //var Controls = $('div.date');
        // Перебираем выбранные контролы
        for (i = 0; i < Controls.length; i++) {
            // Замыкаем ссылку на каждый контрол
            (function(control) {
	            initDateControlEventHandlers(control);
            })(Controls[i]);
        }
    }
    
    
        // Поля дат
   function initDateFields(dateFields) {
        for(var i = 0; i < dateFields.length; i++) {
	        initDateField($(dateFields[i]));
        }
   }
    
    // Инитим все контролы даты
    function InitDateControls() {
        // Проинициализируем обработчики событий
        initDateEventHandlers();
        // Инитим перекачку данных из контрола в подконтролы
        initDateFields(DateControlContainers);
    }

    // Инициализация одного контрола, чтобы можно было
    //    инициализировать контролы динамически после загрузки страницы
    //    по результатам аякс-запроса
    function InitOneDateControl(DateField) {
        initDateControlEventHandlers(DateField);
        initDateField(DateField);
    }
    
$(document).ready(function() {
    InitDateControls();
});