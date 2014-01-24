$(document).ready(function() {
    this.initColorFields = function(colorPickerFields) {
        $(function () {
            // Маркировка анкет
            for(var i = 0; i < colorPickerFields.length; i++) {
                if($(colorPickerFields[i]).length == 0) {
                    continue;
                }
                $(colorPickerFields[i]).colorpicker({
                    format: 'hex'
                });
            }
        });
    };
//===========================================
//  Перенесено BEGIN
//===========================================
    /*
    // Подвешиваем на все инпуты и селекты в форме следующий обработчик:
    //      По нажатию на таб проверяем - является ли текущий инпут последним в форме
    //         если является, то необходимо перебросить фокус на
    //           первый инпут в этой же форме. Таким образом табуляция будет "закольцована"
    //           по полям формы
    
    (function () {
        // Выбираем инпуты и селекты
        var ControlsInput = $('input');
        var ControlsSelect = $('select');
        // Перебираем все контролы с input-ами
        for (i=0;i<ControlsInput.length;i++) {
            $(ControlsInput[i]).on('keydown',function (e)  {
                PressTabControlHandler(e);
            });
        }

        // Перебираем все контролы с селектами
        for (i=0;i<ControlsSelect.length;i++) {
            $(ControlsSelect[i]).on('keydown',function (e) {
                PressTabControlHandler(e);
            });
        }
    })();

    // Обрабатывает нажатие таб на контроле. Сделано для того, чтобы после нажатия таба
    //    на последнем контроле в форме был переход фокуса
    //   на первый контрол той же формы
    function PressTabControlHandler(Target) {
        if (Target.keyCode==9) {
            // Выбираем div.form-group самого контрола, а также первый и последний
            //    div.form-group формы
            //
            var TargetDiv =  $(Target.currentTarget).parents('div.form-group');
            var LastElementOfForm = $(Target.currentTarget).parents('form').find('div.form-group:last');
            var FirstElementOfForm = $(Target.currentTarget).parents('form').find('div.form-group:first');

            // Проверяем, лежит ли данный контрол в последнем элементе div с классом formgroup
            //   Если да - выбираем первый див с классом form-group в форме и ставим ему фокус
            if ($(TargetDiv)[0]==$(LastElementOfForm)[0]) {
                // Ищем input внутри первого div.form-group
                var NewFocusControl = $(FirstElementOfForm).find('input');
                // Проверяем - если ничего не нашли, то значит в первом див.форм-гроуп не input, а селект
                if (NewFocusControl.length==0) {
                    NewFocusControl = $(FirstElementOfForm).find('select');
                }

                // Контрольная проверка - есть ли контрол (теоретически такой ситуации не должно быть,
                //   но на всякий случай)
                if (NewFocusControl.length!=0) {
                    NewFocusControl.focus();
                    Target.preventDefault();
                }
            }
        }
    }
*/
//===========================================
//  Перенесено END
//===========================================
    
    
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
        if((value.length == 3 || value.length == 7 || value.length == 11) && e.keyCode != 8) {
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
        //console.log('.help-block focus');
        // Из-за этого рубится событие клик
        //$('.help-block').hide();
        
        var helpBlock = $(this).parents('.form-group').find('.help-block');
        if(typeof helpBlock != 'undefined') {
            if ($(helpBlock).length>0) {
                    $(helpBlock).show();
            }
        }
    });
    
    // Ставим классы для различения контролов времени и даты
    $('div.date').addClass('date-control');
    $('div.time-control').removeClass('date-control');
    
    // Ставим обработчик keydown на document, чтобы отловить Enter
    $(document).on('keydown', function(e) {
        // Клавиша Enter
        if (e.keyCode==13) {
            // Если target - потенциально принадлежит форме
            if($(e.target).is('input[type!=submit][type!=button], select, textarea'))
            {
                var ContainerOfElement = null;
                // Определяем контейнер, в которой лежит элемент
                    // Смотрим - если открыт хотя бы один поп-ап
                    //     (установлено ли свойство display у css, равное 'block')- смотрим, есть ли в нём форма
                    var OpenedPopup = $('.modal:visible');
                    // Если открыт поп-ап и в нём нет формы
                    if (OpenedPopup.length>0 && ($($(OpenedPopup)[0]).find('form').length<=0)) {
                        ContainerOfElement = OpenedPopup;
                    }
                    else
                    // Если не открыт поп-ап или в нём есть форма
                    {
                        // Берём у родителя target-а форму
                        ContainerOfElement =
                        $(e.target).parents('form');    
                    }
                    // Если у элемента есть родительская форма
                    if (ContainerOfElement.length>0) {
                        // Находим элемент с классом .button-success.
                        var ButtonToSubmit = $($(ContainerOfElement)[0]).find('.btn-success');
                        // Если кнопки btn-success нет, то ищём кнопку btn-primary
                        if (ButtonToSubmit.length<=0) {
                            ButtonToSubmit = $($(ContainerOfElement)[0]).find('.btn-primary');
                        }
                        // Если кнопки btn-primary нет, то ищём кнопку btn-default
                        if (ButtonToSubmit.length<=0) {
                            ButtonToSubmit = $($(ContainerOfElement)[0]).find('.btn-default');
                        }
                        // Если ничего не нашли - выбираем input type=submit и input type=button
                        if (ButtonToSubmit.length<=0) {
                            ButtonToSubmit = $($(ContainerOfElement)[0]).find('[type=submit], [type=button]');
                        }
                        // Ну а теперь, если всё-таки что-то наконец нашли - выбираем первую из списка
                        //    кнопку - и делаем ей приятно (вызываем click)
                        if (ButtonToSubmit.length>0) {
                            $($(ButtonToSubmit)[0]).trigger('click');
                        }
                    }
            }
        }
    });
   
});