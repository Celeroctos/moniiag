$(document).ready(function() {
    function displayAll(data) {
        // Заполняем пришедшими данными таблицу тех, кто без карт
        var table = $('#omsSearchPregnantResult tbody');
        table.find('tr').remove();
        for(var i = 0; i < data.length; i++) {
            table.append(
                '<tr>' +
                    '<td>' +
                        '<a href="#" title="Посмотреть информацию по пациенту">' + data[i].last_name + ' ' + data[i].first_name + ' ' + data[i].middle_name + '</a>' +
                    '</td>' +
                    '<td>' +
                        '<a href="http://moniiag.toonftp.ru/reception/patient/editomsview/?omsid=' + data[i].id + '">' +
                            data[i].oms_number +
                        '</a>' +
                    '</td>' +
                    '<td>' +
                        '<a href="http://moniiag.toonftp.ru/reception/patient/editcardview/?cardid=' + data[i].card_number + '">' +
                            data[i].card_number +
                        '</a>' +
                    '</td>' +
                    '<td>' +
                        '<a href="http://moniiag.toonftp.ru/reception/patient/addpregnant/?cardid=' + data[i].card_number + '">' +
                            '<span class="glyphicon glyphicon-edit"></span>' +
                        '</a>' +
                    '</td>' +
                '</tr>'
            );
        }
        table.parents('div.no-display').removeClass('no-display');
    }

    // Отобразить ошибки формы добавления пациента
    $("#pregnant-addedit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == 'true') { // Запрос прошёл удачно
            $('#successAddEditPregnantPopup').modal({

            });
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddPopup').modal({

            });
        }
    });

    $("#pregnant-search-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == 'true') { // Запрос прошёл удачно, строим таблицы
            $('#pregnant-addedit-form').remove();
            $('#withoutCardCont').addClass('no-display');

            if(ajaxData.data.length == 0) {
                $('#notFoundPopup').modal({
                });
            } else {
                displayAll(ajaxData.data);
            }
        } else {
            $('#errorSearchPopup .modal-body .row p').remove();
            $('#errorSearchPopup .modal-body .row').append('<p>' + ajaxData.data + '</p>')
            $('#errorSearchPopup').modal({

            });
        }
    });

    $('#successAddEditPregnantPopup').on('hidden.bs.modal', function () {
        window.location.reload();
    });
});