
     // Перечень контейнеров с контролами дат
    var TimeControlContainers =
        [
        "#edit-timeBegin-cont",
        "#edit-timeEnd-cont",
        "#add-timeBegin-cont",
        "#add-timeEnd-cont",
        "#timeBegin0-cont",
        "#timeBegin1-cont",
        "#timeBegin2-cont",
        "#timeBegin3-cont",
        "#timeBegin4-cont",
        "#timeBegin5-cont",
        "#timeBegin6-cont",
        "#timeEnd0-cont",
        "#timeEnd1-cont",
        "#timeEnd2-cont",
        "#timeEnd3-cont",
        "#timeEnd4-cont",
        "#timeEnd5-cont",
        "#timeEnd6-cont",
        
        ];
        
    //$('.subcontrol input').val('');
    // Обрабатывает событие нажатия на кнопку-стрелку для контрола с датой
    // Обрабатывает событие нажатия на кнопку-стрелку для контрола с датой
    function ArrowTimeClickHandler(Target, Control)  {
        // Парсим время
        // Разделяем её на две группы символов
        var TimeArray = $(Control).find('input.form-control').val().split(":");
        // Если не введён час
        if (TimeArray.length == 0)  {
            // Добавляем текущий час
            TimeArray.push(new Date().getHours());
        } else {
            // Проверяем, является ли первый элемент валидной цифрой часа
            if (parseInt(TimeArray[0])<0 ||  parseInt(TimeArray[0])>23 ) {
                // Добавляем текущй год
                TimeArray[0] =  (new Date()).getHours();
            }
        }

        // Если не введён месяц
        if (TimeArray.length == 1) {
            // Добавляем текущие минуты
            TimeArray.push(new Date().getMinutes());
        } else {
            // Проверяем - валидны ли минуты Если нет - добавляем текущий
            if (parseInt(TimeArray[1])<0 ||  parseInt(TimeArray[1])>59) {
                // Добавляем текущй месяц
                TimeArray[1] = new Date().getMinutes();
            }
        }

        // Если всё-таки все группы цифр есть - создаём объект даты
        var StructDate = new Date();
        
        // Устанавливаем час и минуты, чтобы с ними дальше
        StructDate.setHours(TimeArray[0]);
        StructDate.setMinutes(TimeArray[1]);
        
        // В переменной Date распарсенное время
        // В зависимости от нажатой кнопки - вычисляем новое время

        if ($(Target.currentTarget).hasClass('up-hour-button')) {
            StructDate.setHours(StructDate.getHours() + 1);
        }

        if ($(Target.currentTarget).hasClass('up-minute-button')) {
            StructDate.setMinutes(StructDate.getMinutes() + 1);
        }

        if ($(Target.currentTarget).hasClass('down-hour-button')) {
            StructDate.setHours(StructDate.getHours() - 1);
        }

        if ($(Target.currentTarget).hasClass('down-minute-button')) {
            StructDate.setMinutes(StructDate.getMinutes() - 1);
        }

        // Преобразовываем изменённую дату обратно и записываем в контрол
        //console.log(Control);
        $(Control).find('input.form-control:first').val(StructDate.getHours()+ ':' + StructDate.getMinutes());
        $(Control).find('input.form-control:first').trigger('change');
    }

    function InitOneControlTimeHandlers(Control)
    {
         // Подвязываем обработчик события нажатия на верхние кнопки для контрола
                var btnPrevNext = $(Control).find('.time-ctrl-up-buttons .btn-group button, .time-ctrl-down-buttons .btn-group button');
                var lastNullEntered = false; // Чтобы считывать 01, 02...
				$(btnPrevNext).on('click',function (e) {
                    ArrowTimeClickHandler(e, Control);
                });
                // Обработчик на субконтролы, если оные есть
                var subcontrol = $(Control).find('.subcontrol');
                $(subcontrol).find('input').on('change', function(e) {
                     var container = $(this).parents('.subfields');
                     var hour = $(container).find('input.hour');

                     var allowChange = true;
                     if($.trim($(hour).val()) == '') {
                        allowChange = false;
                     }

                     var minute = $(container).find('input.minute');
                     if($.trim($(minute).val()) == '') {
                        allowChange = false;
                     }


                     // Только если все три контрола установлены, дату в нормальном контроле можно менять
                     if(allowChange) {
                         $($(this).parents('div.input-group')[0]).find('input.form-control:first').trigger('change', [1]);
                     }
                });
                $(subcontrol).find('input.hour').on('keyup', function(e) {
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
                            if(parseInt($(this).val()) < 3) {
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
                        $(this).parents('.subcontrol').find('.time-ctrl-down-buttons .down-hour-button').trigger('click');
                        e.stopPropagation();
                        return false;
                    }
                    // Вверх
                    if(e.keyCode == 38) {
                        $(this).parents('.subcontrol').find('.time-ctrl-up-buttons .up-hour-button').trigger('click');
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
                        // Это час
                        // При форме "01, 02" и пр. parseInt даст один символ. А это два. Не давать вводить, если есть ведущий ноль.
                        var hour = parseInt('' + $(this).val() + String.fromCharCode(e.keyCode));
                        // Проверим - введённое число меньше ли 23
                        if(hour>23) {
                            $(this).animate({
                                backgroundColor: "rgb(255, 196, 196)"
                            });
                            return false;
                        }
                    } else {
                            if($(this).val().length == 0 && (e.keyCode == 48 || e.keyCode == 96)) {
                                lastNullEntered = true;
                                return false;
                            }
                        // Стрелки вправо-влево, tab и backspace разрешать, разрешать ведущие нули
                        if(e.keyCode != 9 &&
                           e.keyCode != 8 &&
                           e.keyCode != 37 &&
                           e.keyCode != 39 &&
                           e.keyCode != 13 &&
                           e.keyCode != 16 &&
                           e.keyCode != 48 &&
                           e.keyCode != 96) {
							$(this).animate({
								backgroundColor: "rgb(255, 196, 196)"
							});
							// Нули просто пропускать
                            return false;
                        }
                    }
                });

                
                $(subcontrol).find('input.minute').on('keyup', function(e) {
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
			    if(parseInt($(this).val()) < 6) {
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
                        $(this).parents('.subcontrol').find('.time-ctrl-down-buttons .down-minute-button').trigger('click');
                        e.stopPropagation();
                        return false;
                    }
                    // Вверх
                    if(e.keyCode == 38) {
                        $(this).parents('.subcontrol').find('.time-ctrl-up-buttons .up-minute-button').trigger('click');
                        e.stopPropagation();
                        return false;
                    }

                    var selected = getSelected(this);
                    // Проверка на корректность введённых минут вообще
                    if((e.keyCode > 48 && e.keyCode < 58) || (e.keyCode > 96 && e.keyCode < 106)) { // Это вообще введены цифры, типа
						// Очищаем поле, если там есть выделенное
                        if($.trim(selected) != '') {
                            $(this).val('');
                        }
						// Вместо 0-9 - буквы
						if(e.keyCode > 95 && e.keyCode < 106) {
							e.keyCode -= 48;
						}
                        // Это минуты
                        var minute = parseInt('' + $(this).val() + String.fromCharCode(e.keyCode));
                        // Минуты не могут быть меньше нуля и больше 59
                        if(!(minute > -1 && minute < 60)) {
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
                        if(e.keyCode != 9 &&
                           e.keyCode != 8 &&
                           e.keyCode != 37 &&
                           e.keyCode != 39 &&
                           e.keyCode != 16 &&
                           e.keyCode != 13 &&
                           e.keyCode != 48 &&
                           e.keyCode != 96) {
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
    function initTimeEventHandlers() {
        // Выбираем все контролы дат

            var Controls = [];
            for (OneControlContainer=0;OneControlContainer<TimeControlContainers.length;OneControlContainer++)
            {
                var ControlSelector = TimeControlContainers[OneControlContainer];
                Controls.push($(ControlSelector)[0]);
            }
            
            //$(TimeControlContainers[OneControlContainer]).find('div.time-control');
            // Перебираем выбранные контролы
            for (i = 0; i < Controls.length; i++) {
                InitOneControlTimeHandlers(Controls[i])
    
        }
    
    }

    function getSelected(element) {
        return $(element).selection();
    }
    
    function InitOneTimeControlInternal(timeField) {
            var format = 'h:i';
            if($(timeField).length == 0) {
                return;
            }
            $(timeField).datetimepicker({
                language:  'ru',
                format: format,
                weekStart: 1,
                todayBtn:  1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 1,
                minView: 0,
                maxView: 1,
                forceParse: 0
            });
            
            //continue;
            
            var ctrl = $(timeField).find('input.form-control:first');
            $(ctrl).on('change', function(e, type){
                console.log('TimeControl Changed');
                var subcontrols = $(this).parent().find('.subcontrol');
                if(typeof subcontrols != 'undefined') {
                    var hour = $(subcontrols).find('input.hour');
                    var minute = $(subcontrols).find('input.minute');
                    // Аргумент type говорит о том, в каком направлении нужно писать: из контролов в субконтролы или наоборот.
                    // Из суб в настоящий
                    if(typeof type == 'undefined') {
                       // $(subcontrols).find('input').val('');
                        var currentTime = $(this).val();
                        var parts = currentTime.split(':');
                        
                        var HourInt  = parseInt(parts[0]);
                        var MinuteInt = parseInt(parts[1]);
                        
                        // Проверяем - выводим, если ни одна из компонент не равно NaN
			//  Если число равно NaN, то проверка Число == Число выдаст false
                        if ( HourInt==HourInt&& MinuteInt ==MinuteInt ) {
                            $(hour).val(HourInt);
                            $(minute).val(MinuteInt);                          
                           
                        }
                    } else { // Из настоящего в суб
                        $(this).val(hour.val()+':'+minute.val());
                    }
                }
            });
            if($.trim($(ctrl).val()) != '') {
                $(ctrl).trigger('change');
            }
        
    }
    
    // Поля дат
    function initTimeFields(timeFields) {

        for(var i = 0; i < timeFields.length; i++) {
            
            InitOneTimeControlInternal(timeFields[i]);
        }
    }

    // Инитим все контролы на странице
    function InitTimeControls() {
	initTimeEventHandlers();
	initTimeFields(TimeControlContainers);
    }

// Инитим один контрол, чтобы можно было инитить контрол динимаически по результатам аякс-запроса
function InitOneTimeControl(timeField) {
    InitOneTimeControlInternal(timeField);
    InitOneControlTimeHandlers(timeField);
}

$(document).ready(function() {
    InitTimeControls()
});