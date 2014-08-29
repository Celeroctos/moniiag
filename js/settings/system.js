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

    $('#allGreetingsDelete').click(
        function(){
            $('#deleteCreetingConfirm').modal('hide');

            $('#clearGreetingDataSubmit').attr('value', 'Подождите, идёт процесс очистки данных...');
            $('#clearGreetingDataSubmit').attr('disabled', true);
            $.ajax({
                'url' : '/admin/categories/cleargreetingsdata',
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        $("#successPopup .modal-body").find('p').remove();
                        $("#successPopup .modal-body").append($('<p>').html(data.data));
                        $('#clearGreetingDataSubmit').attr('value', 'Очистить таблицы для приёмов пациентов');
                        $('#clearGreetingDataSubmit').attr('disabled', false);
                        $("#successPopup").modal({});
                    }
                }
            });
        }
    );

    $('#clearGreetingDataSubmit').on('click', function(e) {
        $('#deleteCreetingConfirm').modal({});
/*
        $(this).attr('value', 'Подождите, идёт процесс очистки данных...');
        $(this).attr('disabled', true);
        $.ajax({
            'url' : '/admin/categories/cleargreetingsdata',
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == true) {
                    $("#successPopup .modal-body").find('p').remove();
                    $("#successPopup .modal-body").append($('<p>').html(data.data));
                    $('#clearGreetingDataSubmit').attr('value', 'Очистить таблицы для приёмов пациентов');
                    $('#clearGreetingDataSubmit').attr('disabled', false);
                    $("#successPopup").modal({});
                }
            }
        });*/
    });
});
