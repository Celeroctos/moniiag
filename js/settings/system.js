$(document).ready(function() {
    $("#system-settings-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == 'true') { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#successSystemSettingsEditPopup').modal({

            });
        } else {
            // Удаляем предыдущие ошибки
            $('#errorSystemSettingsEditPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorSystemSettingsEditPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorSystemSettingsEditPopup').modal({
            });
        }
    });
});
