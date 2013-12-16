$(document).ready(function() {
    var global = {
        dateFields: [
            '#birthday-cont',
            '#document-givedate-cont',
            '#search-date-cont',
            '#current-date-cont',
            '#date-cont',
            '#dateBegin-cont',
            '#dateEnd-cont',
            '#dateBeginEdit-cont',
            '#dateEndEdit-cont'
        ],
        colorPickerFields: [
            '.custom-color' // Маркировка анкет
        ]
    }

    this.initFields = function() {
        $(function () {
            // Поля дат
            var format = 'yyyy-mm-dd';
            for(var i = 0; i < global.dateFields.length; i++) {
                if($(global.dateFields[i]).length == 0) {
                    continue;
                }
                $(global.dateFields[i]).datetimepicker({
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
                $(global.dateFields[i]).find('input').on('keyup', function(e) {
                    var value = $(this).val();
                    // Дата по регулярке
                    if((value.length == 4 || value.length == 7) && e.keyCode != 8) { // Введён год или месяц..
                        $(this).val(value + '-');
                    }
                    if((value.length == 5 || value.length == 8) && e.keyCode == 8) { // Убрать автоматически прочерк
                        $(this).val(value.substr(0, value.length - 1));
                    }
                });
                $(global.dateFields[i]).find('input').on('keydown', function(e) {
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
            // Маркировка анкет
            for(var i = 0; i < global.colorPickerFields.length; i++) {
                if($(global.colorPickerFields[i]).length == 0) {
                    continue;
                }
                $(global.colorPickerFields[i]).colorpicker({
                    format: 'hex'
                });
            }

            // Обрабатывает событие нажатия на кнопку-стрелку для контрола с датой
            function ArrowCalendarClickHandler(Target,Control)
            {
                // Парсим дату
                // Разделяем её на три группы символов
                 var DateArray = Control.value.split("-");
                 
                 
                 // Если не введён год
                 if (DateArray.length==0)
                 {
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
                 

            }

			// Стрелки вверх-вниз для листания
			// Сначала стрелки вверх
			(function ()
				{
					// Выбираем все контролы дат
					var Controls = $('div.date input');
					
					// Перебираем выбранные контролы
					for (i=0;i<Controls.length;i++)
					{
						// Замыкаем ссылку на каждый контрол
						(function (Control)
						{
							
							// Подвязываем обработчик события нажатия на верхние кнопки для контрола
							$(Control).parents('div.form-group').prev().find('button').on('click',function (e)
							{
								ArrowCalendarClickHandler(e,Control);
								});
							
							// Подвязываем обработчик события нажатия на нижние кнопки для контрола
							$(Control).parents('div.form-group').next().find('button').on('click',function (e)
							{
								ArrowCalendarClickHandler(e,Control);
								});
							
							
						})(Controls[i]);
						
						
					}
					
				}
			
			)();
			
			/*
			$('.up-calendar-button').parents('.form-group').next().find('.date').find('input').on('click', function(e) {
				//alert(this.value);
					var Control = селектор на текстовое поле
					function = {
						this 	
						
					}()
				});

*/

        });
    };

    $('#omsNumber, #policy').keyfilter(/^[\s\d]*$/);
    $('#firstName, #lastName, #middleName').keyfilter(/^[А-Яа-яЁё\-]*$/);

    $('#snils').on('keyup', function(e) {
        var value = $(this).val();
        // СНИЛС по проверке
        if((value.length == 3 || value.length == 7 || value.length == 11) && e.keyCode != 8) { // Введён год или месяц..
            $(this).val(value + '-');
        }
        if((value.length == 4 || value.length == 8 || value.length == 12) && e.keyCode == 8) { // Убрать автоматически прочерк
            $(this).val(value.substr(0, value.length - 1));
        }
    });

    $('#snils').on('keydown', function(e) {
        // Бэкспейс разрешить, цифры разрешить
        var isAllow = true;
        // Проверяем табуляцию и  Enter
        // Если символ Enter или Tab - сразу возвращаем true
        if ((e.keyCode == 13)||(e.keyCode == 9)) 
            return true;
        
        var value = $(this).val();
        if(value.length == 14 && e.keyCode != 8) {
            isAllow = false;
        } else {
            if(!(e.keyCode > 47 && e.keyCode < 58) && !(e.keyCode > 95 && e.keyCode < 106) && e.keyCode != 8) {
                isAllow = false;
            }
        }
        if((value.length == 3 || value.length == 7 || value.length == 11) && e.keyCode != 8) { // Введён год или месяц..
            $(this).val(value + '-');
        }
        return isAllow;
    });


    this.initFields();

    $('#loginSuccessPopup').on('hidden.bs.modal', function() {
        window.location.reload();
    });

    // Форма логина-разлогина
    $("#login-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == 'true') { // Логин прошёл удачно
            $('#loginSuccessPopup').modal({

            });
        } else if(ajaxData.success == 'notfound') {
            $('#loginNotFoundPopup').modal({

            });
        } else {
            $('#loginErrorPopup').modal({

            });
        }
    });

    // Форма разлогина
    $("#logout-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        window.location.reload();
    });
});