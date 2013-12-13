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
                $(global.dateFields[i]).find('input').on('keydown', function(e) {
                    var value = $(this).val();
                    // Дата по регулярке
					if((value.length == 4 || value.length == 7)&&(e.keyCode != 8)) {
						// Введён год или месяц..
                        		$(this).val(value + '-');			
                    }
                 // Убрать автоматически прочерк
				    if((value.length == 6 || value.length == 9) && e.keyCode == 8) {
                        $(this).val(value.substr(0, value.length - 1));
                    }
                });
                $(global.dateFields[i]).find('input').on('keydown', function(e) {
                    // Бэкспейс разрешить, цифры разрешить
                    var isAllow = true;
                    if($(this).val().length == 10 && e.keyCode != 8) {
                        isAllow = false;
                    } else {
                        if(!(e.keyCode > 47 && e.keyCode < 58) && !(e.keyCode > 95 && e.keyCode < 106) && e.keyCode != 8) {
                            isAllow = false;
                        }
                    }
                    return isAllow;
                })
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
        });
    };


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