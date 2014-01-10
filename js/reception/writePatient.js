$(document).ready(function() {
    // Инициализируем пагинацию для списков
    InitPaginationList('searchWithCardResult','oms_number','desc',updatePatientsList);
    InitPaginationList('searchDoctorsResult','d.middle_name','desc',updateDoctorsList);
    // Поиск пациента
    $('#patient-search-submit').click(function(e) {
        updatePatientsList();
        return false;

    });

    function getPatientsFilter() {
        var Result = {
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
                    'field' : 'address_reg',
                    'op' : 'cn',
                    'data' : $('#addressReg').val()
                },
                {
                    'field' : 'address',
                    'op': 'cn',
                    'data' : $('#address').val()
                },
                {
                    'field' : 'card_number',
                    'op' : 'cn',
                    'data' : $('#cardNumber').val()
                },
                {
                    'field' : 'serie',
                    'op' : 'cn',
                    'data' : $('#serie').val()
                },
                {
                    'field' : 'docnumber',
                    'op' : 'cn',
                    'data' : $('#docnumber').val()
                },
                {
                    'field' : 'snils',
                    'op' : 'cn',
                    'data' : $('#snils').val()
                },
                {
                    'field' : 'birthday',
                    'op' : 'eq',
                    'data' : $('#birthday').val()
                }
            ]
        };
        
        return Result;
    }
    
    function getDoctorsFilter() {
        var Result ={
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
        return Result;
    }
    
    function updatePatientsList() {
        
        var filters = getPatientsFilter();
        var PaginationData=getPaginationParameters('searchWithCardResult');
        if (PaginationData!='') {
            PaginationData = '&'+PaginationData;
        }
        // Делаем поиск
        $.ajax({
            'url' : '/index.php/reception/patient/search/?withonly=0&filters=' + $.toJSON(filters)+PaginationData,
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == true) {
                    // Изначально таблицы скрыты
                    $('#withoutCardCont').addClass('no-display');

                    if(data.rows.length == 0) {
                        $('#notFoundPopup').modal({
                        });
                    } else {
                        displayAllPatients(data.rows);
                        printPagination('searchWithCardResult',data.total);
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
        
    }

    function updateDoctorsList() {
        var filters = getDoctorsFilter();
        var PaginationData=getPaginationParameters('searchDoctorsResult');
        if (PaginationData!='') {
            PaginationData = '&'+PaginationData;
        }
        // Делаем поиск
        $.ajax({
            'url' : '/index.php/reception/doctors/search/?filters=' + $.toJSON(filters)+PaginationData,
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
                        printPagination('searchDoctorsResult',data.total);
                    }
                } else {
                    $('#errorPopup .modal-body .row p').remove();
                    $('#errorPopup .modal-body .row').append('<p>' + data.data + '</p>')
                    $('#errorPopup').modal({

                    });
                }
                return;
            }
        });
    }
    
    $('#doctor-search-submit').click(function(e) {
        updateDoctorsList();

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
                        '<a title="Посмотреть информацию по пациенту" href="http://' + location.host + '/index.php/reception/patient/editcardview/?cardid=' + data[i].card_number + '">' +
                            data[i].last_name + ' ' + data[i].first_name + ' ' + data[i].middle_name +
                        '</a>' +
                    '</td>' +
                    '<td>' +
                        '<a title="Посмотреть информацию по карте" href="http://' + location.host + '/index.php/reception/patient/editcardview/?cardid=' + data[i].card_number + '">' +
                            data[i].card_number +
                        '</a>' +
                    '</td>' +
                    '<td>' +
                        '<a title="Посмотреть информацию по ОМС" href= "http://' + location.host + '/index.php/reception/patient/editomsview/?omsid=' + data[i].id + '">' +
                            data[i].oms_number +
                        '</a>' +
                    '</td>' +
                    '<td>' +
                        '<a title="Записать пациента" href="http://' + location.host + '/index.php/reception/patient/writepatientsteptwo/?cardid=' + data[i].card_number + '">' +
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
                cabinetsStr += '' + data[i].cabinets[j].description + '';
            }
            table.append(
                '<tr>' +
                    '<td>' +
                        '<a title="Записать пациента" class="write-patient-link" href="#d' + data[i].id + '">' +
                            '<span class="glyphicon glyphicon-dashboard"></span>' +
                        '</a>' +
                    '</td>' +
                    '<td>' +
                        '<a title="Посмотреть информацию по врачу" href="#">' +
                            data[i].last_name + ' ' + data[i].first_name + ' ' + data[i].middle_name +
                        '</a>' +
                    '</td>' +
                    '<td>' +
                        ((data[i].post == null) ? '' : data[i].post) +
                    '</td>' +
                    '<td>' +
                        data[i].ward  +
                    '</td>' +
                    '<td>' +
                        cabinetsStr +
                    '</td>' +
                    '<td>' +
                    '</td>' +
                '</tr>'
            );
        }
        table.parents('div.no-display').removeClass('no-display');
    }

    $(document).on('click', '.write-patient-link', function(e, month, year) {
        if(globalVariables.hasOwnProperty('clickedLink')) {
            $(globalVariables.clickedLink).parents('tr').removeClass('success');
        }
        $(this).parents('tr').addClass('success');
        globalVariables.doctorId = $(this).attr('href').substr(2);
        globalVariables.clickedLink = $(this);
        globalVariables.fio = $(this).parents('tr').find('td:first a').text();
        loadCalendar(month, year);
    });

    function loadCalendar(month, year) {
        $('.busyShedule, .busySheduleHeader').hide();
       // var doctorId = $(link).attr('href').substr(2);
        var doctorId = globalVariables.doctorId;
       // globalVariables.fio = $(link).parents('tr').find('td:first a').text();
       // globalVariables.clickedLink = $(link);
        var params = {};
        $('.busyFio').text(globalVariables.fio);
        if(typeof month == 'undefined' || typeof year == 'undefined') {
            $('.busyDate').text(globalVariables.months[(new Date()).getUTCMonth()] + ' ' + (new Date()).getFullYear() + ' г.');
        } else {
            // Ведущий ноль
            if((month + 1) < 10) {
                month = '0' + (month + 1);
            } else {
                month += 1;
            }
            var current = (new Date(year + '-' + month )).getUTCMonth();
            $('.busyDate').text(globalVariables.months[current] + ' ' + year + ' г.');
            params.month = parseInt(month);
            params.year = year;
        }
        // Делаем запрос на информацию и обновляем шаблон календаря
        // Делаем поиск
        params.doctorid = doctorId;
        $.ajax({
            'url' : '/index.php/doctors/shedule/getcalendar',
            'data' : params,
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == 'true') {
                    $("#writeShedule").trigger("showShedule", [data, textStatus, jqXHR])
                    $('.headerBusyCalendar, .busyCalendar').show();
                } else {
                    $('#errorPopup .modal-body .row p').remove();
                    $('#errorPopup .modal-body .row').append('<p>' + data.data + '</p>')
                    $('#errorPopup').modal({

                    });
                }
                return;
            }
        });
    }

    $('#sheduleByBusy').on('showBusy', function(e, data, textStatus, jqXHR, doctorId, year, month, day) {
        $('.busyDay').text(day + '.' + (month + 1) + '.' + year + ' г.');
        var table = $(this).find('tbody');
        var data = data.data;
        table.find('tr').remove();
        globalVariables.doctorId = doctorId;
        globalVariables.day = day;

        for(var i = 0; i < data.length; i++) {
            if(data[i].isAllow) {
                var str =
                '<tr>' +
                    '<td>' + data[i].timeBegin + ' - ' + data[i].timeEnd + '</td>' +
                    '<td></td>' +
                    '<td>' +
                        '<a class="write-link" href="#' + data[i].timeBegin + '" title="Записать пациента">' +
                            '<span class="glyphicon glyphicon-dashboard"></span>' +
                        '</a>' +
                    '</td>' +
                '</tr>';
            } else {
                var str =
                '<tr>' +
                    '<td>' + data[i].timeBegin + ' - ' + data[i].timeEnd + '</td>' +
                    '<td>' +
                        '<a href="http://' + location.host + '/index.php/reception/patient/editcardview/?cardid=' + data[i].cardNumber + '" title="Посмотреть информацию по пациенту">' +
                                data[i].fio  +
                        '</a>' +
                    '</td>' +
                    '<td>' +
                        '<a class="unwrite-link" href="#' + data[i].id + '">' +
                            '<span class="glyphicon glyphicon-remove" title="Снять пациента с записи"></span>' +
                        '</a>' +
                    '</td>' +
                '</tr>';
            }

            table.append(str);
        }

        $('.busyShedule, .busySheduleHeader').show();
    });

    $(document).on('click', '.write-link', function(e) {
        var params = {
            card_number : globalVariables.cardNumber,
            month : globalVariables.month + 1,
            year : globalVariables.year,
            day : globalVariables.day,
            doctor_id : globalVariables.doctorId
        };
        params.time = $(this).attr('href').substr(1);

        $.ajax({
            'url' : '/index.php/doctors/shedule/writepatient',
            'data' : params,
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == 'true') {
                    $('#successPopup p').text(data.data);
                    $('#successPopup').modal({

                    });
                    globalVariables.clickedTd.trigger('click');
                    
                    // Перезагружаем календарь
                    loadCalendar(globalVariables.month, globalVariables.year);
                    
                } else {

                }
                return;
            }
        });
        return false;
    });

    $(document).on('click', '.unwrite-link', function(e) {
        var params = {
           id : $(this).attr('href').substr(1)
        };
        $.ajax({
            'url' : '/index.php/doctors/shedule/unwritepatient',
            'data' : params,
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == 'true') {
                    $('#successPopup p').text(data.data);
                    $('#successPopup').modal({

                    });
                    globalVariables.clickedTd.trigger('click');
                    
                    // Перезагружаем календарь
                    loadCalendar(globalVariables.month, globalVariables.year);
                } else {

                }
                return;
            }
        });
        return false;
    });

});