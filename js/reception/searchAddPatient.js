$(document).ready(function() {
    // Поиск по ОМС
    $('#patient-search-submit').click(function(e) {
        var filters = {
            'groupOp' : 'AND',
            'rules' : [
                {
                    'field' : 'oms_number',
                    'op' : 'cn',
                    'data' :  $('#omsNumber').val()
                },
                {
                    'field' : 'first_name',
                    'op' : 'cn',
                    'data' : $('#firstName').val()
                },
                {
                    'field' : 'middle_name',
                    'op' : 'cn',
                    'data' : $('#middleName').val()
                },
                {
                    'field' : 'last_name',
                    'op' : 'cn',
                    'data' : $('#lastName').val()
                }
            ]
        };

        // Делаем поиск по ОМС
        $.ajax({
            'url' : '/index.php/reception/patient/search/?filters=' + $.toJSON(filters),
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == true) {
                    // Изначально таблицы скрыты
                    $('#withoutCardCont').addClass('no-display');
                    $('#withCardCont').addClass('no-display');

                    if(data.data.with.length == 0 && data.data.without.length == 0) {
                        $('#notFoundPopup').modal({
                        });
                    } else {
                        if(data.data.without.length > 0) {
                            displayAllWithoutCard(data.data.without);
                        }
                        if(data.data.with.length > 0) {
                            displayAllWithCard(data.data.with);
                        }
                    }
                } else {
                    $('#errorSearchPopup .modal-body .row p').remove();
                    $('#errorSearchPopup .modal-body .row').append('<p>' + data.data + '</p>')
                    $('#errorSearchPopup').modal({

                    });
                }
                return;
            }
        });
        
        return false;
    });

    // Отобазить таблицу тех, кто без карт
    function displayAllWithoutCard(data) {
        // Заполняем пришедшими данными таблицу тех, кто без карт
        var table = $('#omsSearchWithoutCardResult tbody');
        table.find('tr').remove();
        for(var i = 0; i < data.length; i++) {
            table.append(
                '<tr>' +
                    '<td><a href="#" title="Посмотреть информацию по пациенту">' + data[i].first_name + ' ' + data[i].last_name + ' ' + data[i].middle_name + '</a></td>' +
                    '<td>' + data[i].oms_number + '</td>' +
                    '<td>' +
                        '<a href="http://moniiag.toonftp.ru/index.php/reception/patient/viewadd/?patientid=' + data[i].id + '">' +
                            '<span class="glyphicon glyphicon-plus"></span>' +
                        '</a>' +
                    '</td>' +
                '</tr>'
            );
        }
        table.parents('div.no-display').removeClass('no-display');
    }

    // Отобразить таблицу тех, кто с картами
    function displayAllWithCard(data) {
        // Заполняем пришедшими данными таблицу тех, кто без карт
        var table = $('#omsSearchWithCardResult tbody');
        table.find('tr').remove();
        for(var i = 0; i < data.length; i++) {
            table.append(
                '<tr>' +
                    '<td><a href="#" title="Посмотреть информацию по пациенту">' + data[i].first_name + ' ' + data[i].last_name + ' ' + data[i].middle_name + '</a></td>' +
                    '<td>' + data[i].oms_number + '</td>' +
                    '<td>' + data[i].reg_date + '</td>' +
                    '<td>' + data[i].card_number + '</td>' +
                    '<td>' +
                        '<a href="http://moniiag.toonftp.ru/index.php/reception/patient/viewadd/?patientid=' + data[i].id + '">' +
                            '<span class="glyphicon glyphicon-plus"></span>' +
                        '</a>' +
                    '</td>' +
                '</tr>'
            );
        }
        table.parents('div.no-display').removeClass('no-display');
    }
});