$(document).ready(function() {
    // Поиск пациента
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
                },
                {
                    'field' : 'card_number',
                    'op' : 'cn',
                    'data' : $('#cardNumber').val()
                }
            ]
        };

        // Делаем поиск
        $.ajax({
            'url' : '/index.php/reception/patient/search/?filters=' + $.toJSON(filters),
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == true) {
                    // Изначально таблицы скрыты
                    $('#withoutCardCont').addClass('no-display');

                    if(data.data.with.length == 0) {
                        $('#notFoundPopup').modal({
                        });
                    } else {
                        displayAllPatients(data.data.with);
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


    $('#doctor-search-submit').click(function(e) {
        var filters = {
            'groupOp' : 'AND',
            'rules' : [
                {
                    'field' : 'ward_code',
                    'op' : 'eq',
                    'data' :  $('#ward').val()
                },
                {
                    'field' : 'post_id',
                    'op' : 'eq',
                    'data' : $('#post').val()
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
                },
                {
                    'field' : 'first_name',
                    'op' : 'cn',
                    'data' : $('#firstName').val()
                }
            ]
        };

        // Делаем поиск
        $.ajax({
            'url' : '/index.php/reception/doctors/search/?filters=' + $.toJSON(filters),
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == true) {
                    // Изначально таблицы скрыты
                    $('#withoutCardCont').addClass('no-display');

                    if(data.data.length == 0) {
                        $('#notFoundPopup').modal({
                        });
                    } else {
                        displayAllDoctors(data.data);
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


    // Отобразить таблицу тех, кто с картами
    function displayAllPatients(data) {
        var table = $('#searchWithCardResult tbody');
        table.find('tr').remove();
        for(var i = 0; i < data.length; i++) {
            table.append(
                '<tr>' +
                    '<td>' +
                        '<a title="Посмотреть информацию по пациенту" href="http://moniiag.toonftp.ru/index.php/reception/patient/editcardview/?cardid=' + data[i].card_number + '">' +
                            data[i].last_name + ' ' + data[i].first_name + ' ' + data[i].middle_name +
                        '</a>' +
                    '</td>' +
                    '<td>' +
                        '<a title="Посмотреть информацию по карте" href="http://moniiag.toonftp.ru/index.php/reception/patient/editcardview/?cardid=' + data[i].card_number + '">' +
                            data[i].card_number +
                        '</a>' +
                    '</td>' +
                    '<td>' +
                        '<a title="Посмотреть информацию по ОМС" href="http://moniiag.toonftp.ru/index.php/reception/patient/editomsview/?omsid=' + data[i].id + '">' +
                            data[i].oms_number +
                        '</a>' +
                    '</td>' +
                    '<td>' +
                        '<a title="Записать пациента" href="http://moniiag.toonftp.ru/index.php/reception/patient/writepatientsteptwo/?cardid=' + data[i].card_number + '">' +
                            '<span class="glyphicon glyphicon-dashboard"></span>' +
                        '</a>' +
                    '</td>' +
                '</tr>'
            );
        }
        table.parents('div.no-display').removeClass('no-display');
    }

    // Отобразить таблицу тех, кто с картами
    function displayAllDoctors(data) {
        var table = $('#searchDoctorsResult tbody');
        table.find('tr').remove();
        for(var i = 0; i < data.length; i++) {
            var cabinetsStr = '';
            for(var j = 0; j < data[i].cabinets.length; j++) {
                if(j > 0) {
                    cabinetsStr += ', ';
                }
                cabinetsStr += '<a href="#">' + data[i].cabinets[j].description + '</a>';
            }
            table.append(
                '<tr>' +
                    '<td>' +
                        '<a title="Посмотреть информацию по пациенту" href="http://moniiag.toonftp.ru/index.php/reception/patient/editcardview/?cardid=' + data[i].card_number + '">' +
                            data[i].last_name + ' ' + data[i].first_name + ' ' + data[i].middle_name +
                        '</a>' +
                    '</td>' +
                    '<td>' +
                        '<a title="Посмотреть информацию по карте" href="http://moniiag.toonftp.ru/index.php/reception/patient/editcardview/?cardid=' + data[i].card_number + '">' +
                            ((data[i].post == null) ? '' : data[i].post) +
                        '</a>' +
                    '</td>' +
                    '<td>' +
                        '<a title="Посмотреть информацию по ОМС" href="http://moniiag.toonftp.ru/index.php/reception/patient/editomsview/?omsid=' + data[i].id + '">' +
                            data[i].ward  +
                        '</a>' +
                    '</td>' +
                    '<td>' +
                        cabinetsStr +
                    '</td>' +
                    '<td>' +
                    '</td>' +
                    '<td>' +
                        '<a title="Записать пациента" href="http://moniiag.toonftp.ru/index.php/reception/patient/writepatientsteptwo/?cardid=' + data[i].card_number + '">' +
                            '<span class="glyphicon glyphicon-dashboard"></span>' +
                        '</a>' +
                    '</td>' +
                '</tr>'
            );
        }
        table.parents('div.no-display').removeClass('no-display');
    }
});