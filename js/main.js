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
        $('.help-block').hide();
        var helpBlock = $(this).parents('.form-group').find('.help-block');
        if(typeof helpBlock != 'undefined') {
            $(helpBlock).show();
        }
    });
});