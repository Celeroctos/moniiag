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
            for(var i = 0; i < global.dateFields.length; i++) {
                if($(global.dateFields[i]).length == 0) {
                    continue;
                }
                $(global.dateFields[i]).datetimepicker({
                    language: 'ru',
                    format: 'yyyy-mm-dd',
                    weekStart: 1,
                    todayBtn:  1,
                    autoclose: 1,
                    todayHighlight: 1,
                    startView: 2,
                    minView: 2,
                    forceParse: 0
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