$(document).ready(function() {
    $("#edit-profile-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == 'true') { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#successProfilePopup').modal({

            });
        } else {
            // Удаляем предыдущие ошибки
            $('#errorProflePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorProflePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorProflePopup').modal({

            });
        }
    });

    $('#successProfilePopup').on('hidden.bs.modal', function () {
        window.location.reload();
    });
});
