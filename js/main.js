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

            // Обрабатывает нажатие таб на контроле. Сделано для того, чтобы после нажатия таба
            //    на последнем контроле в форме был переход фокуса 
            //   на первый контрол той же формы
            function PressTabControlHandler(Target)
            {
                if (Target.keyCode==9)
                {
                        // Выбираем div.form-group самого контрола, а также первый и последний
                        //    div.form-group формы
                        //                                      
                        var TargetDiv =  $(Target.currentTarget).parents('div.form-group');
                        var LastElementOfForm = $(Target.currentTarget).parents('form').find('div.form-group:last');
                        var FirstElementOfForm = $(Target.currentTarget).parents('form').find('div.form-group:first');
                
                       // Проверяем, лежит ли данный контрол в последнем элементе div с классом formgroup
                       //   Если да - выбираем первый див с классом form-group в форме и ставим ему фокус
                       if ($(TargetDiv)[0]==$(LastElementOfForm)[0])
                       {
                           // Ищем input внутри первого div.form-group
                           var NewFocusControl = $(FirstElementOfForm).find('input');
                           // Проверяем - если ничего не нашли, то значит в первом див.форм-гроуп не input, а селект
                           if (NewFocusControl.length==0)
                           {
                               NewFocusControl = $(FirstElementOfForm).find('select'); 
                           }                          
                           
                           // Контрольная проверка - есть ли контрол (теоретически такой ситуации не должно быть,
                           //   но на всякий случай)
                           if (NewFocusControl.length!=0)
                           {
                               NewFocusControl.focus();
                               Target.preventDefault();       
                           }
                           
                           
                       }
                }
            }
            
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
			
            // ========>
            
            // Подвешиваем на все инпуты и селекты в форме следующий обработчик:
            //      По нажатию на таб проверяем - является ли текущий инпут последним в форме
            //         если является, то необходимо перебросить фокус на 
            //           первый инпут в этой же форме. Таким образом табуляция будет "закольцована"
            //           по полям формы
            (function ()
            {
                // Выбираем инпуты и селекты
                 var ControlsInput = $('input');
                 var ControlsSelect = $('select');
                 
                 // Перебираем все контролы с input-ами
                 for (i=0;i<ControlsInput.length;i++)
                 {
                            $(ControlsInput[i]).on('keydown',function (e)
                            {
                                PressTabControlHandler(e);
                            });
                 }
                 
                 // Перебираем все контролы с селектами
                 for (i=0;i<ControlsSelect.length;i++)
                 {
                            $(ControlsSelect[i]).on('keydown',function (e)
                            {
                                PressTabControlHandler(e);
                            });
                 }
                
            })();
            // <=======
            
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

    // Снилс
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

    // Паспорт (номер)
    $('#docnumber').keyfilter(/^[\d]+$/);
    // Номер карты
    $('#cardNumber').keyfilter(/[\d\\]+/);

    /* -------------------- */
    // Доп. контролы даты:
    $('.subcontrol input.day').keyfilter(/^[\d]+$/);
    $('.subcontrol input.month').keyfilter(/^[\d]+$/);
    $('.subcontrol input.year').keyfilter(/^[\d]+$/);

    $('.subcontrol input').val('');
    $('.subcontrol input').on('change', function(e) {
        var container = $(this).parents('.subcontrol');
        var day = $(container).find('input.day');
        if($(day).val() == '') {
            $(day).val((new Date).getDay());
        }
        var month = $(container).find('input.month');
        if($(month).val() == '') {
            $(month).val((new Date).getMonth() + 1);
        }
        var year = $(container).find('input.year');
        if($(year).val() == '') {
            $(year).val((new Date()).getFullYear());
        }
        $(this).parents('.form-group').find('.date input').trigger('change', [1]);
    });

    $('.subcontrol input.day').on('keyup', function(e) {
        // С табуляции не переводить на следующее поле
        if(e.keyCode == 9 || e.keyCode == 38 || e.keyCode == 40) {
            return;
        }
        if($(this).val().length == 2) {
            $(this).parent().next().find('input.month').focus();
        }
    }).on('keydown', function(e) {
        if(e.keyCode == 38 || e.keyCode == 40) {
            return;
        }
        if($(this).val().length == 2 && e.keyCode != 8) {
            return false;
        }
    });
    $('.subcontrol input.month').on('keyup', function(e) {
        if(e.keyCode == 9 || e.keyCode == 38 || e.keyCode == 40) {
            return;
        }
        if($(this).val().length == 2) {
            $(this).parent().next().find('input.year').focus();
        }
    }).on('keydown', function(e) {
            if($(this).val().length == 2 && e.keyCode != 8) {
                return false;
            }
    });
    // Фокус на next.next, поскольку следующий контрол есть плюсики и минусики
    $('.subcontrol input.year').on('keyup', function(e) {
        if($(this).val().length == 4) {
            $(this).parents('.form-group').next().next().find('input').focus();
        }
    }).on('keydown', function(e) {
        if($(this).val().length == 4 && e.keyCode != 8) {
            return false;
        }
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
    // Показ подсказки по фокусу на поле
    $('input').on('focus', function(e) {
        $('.help-block').hide();
        var helpBlock = $(this).parents('.form-group').find('.help-block');
        if(typeof helpBlock != 'undefined') {
            $(helpBlock).show();
        }
    });
});