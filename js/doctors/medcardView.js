$(document).ready(function() {
    // Инициализируем пагинацию для списков
    InitPaginationList('omsSearchWithCardResult','oms_number','desc',updatePatientWithCardsList);

    // Поиск по ОМС
    $('#patient-search-submit').click(function(e) {
        updatePatientWithCardsList();
        return false;
    });

    var searchStatus = []; // Здесь - результаты поиска. Есть или нет найденные записи

    function getFilters() {
        var Result =
        {
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

    function seeNotFoundPopup() {
        for(var i = 0; i < searchStatus.length; i++) {
            if(searchStatus[i] == 1) {
                $('#mediateSubmit-cont').removeClass('no-display'); // Положительный результат, кнопку раскомментировать
                searchStatus = [];
                return;
            }
        }
        // Если все "не найдено", показывать модалку
        searchStatus = [];
        $('#notFoundPopup').modal({
        });
    }

    function updatePatientWithCardsList() {
        var filters = getFilters();
        var PaginationData=getPaginationParameters('omsSearchWithCardResult');
        if (PaginationData!='') {
            PaginationData = '&'+PaginationData;
        }
        $.ajax({
            'url' : '/index.php/reception/patient/search/?withonly=0&filters=' + $.toJSON(filters)+PaginationData,
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == true) {
                    // Изначально таблицы скрыты
                    $('#withCardCont').addClass('no-display');

                    if(data.rows.length == 0) {
                        searchStatus.push(0);
                        if(searchStatus.length == 3) {
                            seeNotFoundPopup();
                        }
                    } else {
                        if(data.rows.length > 0) {
                            searchStatus.push(1);
                            displayAllWithCard(data);
                            printPagination('omsSearchWithCardResult',data.total);
                            if(searchStatus.length == 3) {
                                searchStatus = []; // Обнуляем количество статусов: поиск окончен
                                $('#mediateSubmit-cont').removeClass('no-display');
                            }
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
    }

    // Отобразить таблицу тех, кто с картами
    function displayAllWithCard(data) {
        showGreetings = data.greetingsHistory;
        //console.log(showGreetings);
        data = data.rows;
        // Заполняем пришедшими данными таблицу тех, кто без карт
        var table = $('#omsSearchWithCardResult tbody');
        table.find('tr').remove();
        for(var i = 0; i < data.length; i++) {
            linkCellValue = '';
            // Проверяем - разрешено ли видеть историю приёмов пользователю
            if (showGreetings)
            {
                linkCellValue = '<a href="#' + data[i].card_number + '" class="viewHistory" title="Посмотреть информацию по пациенту" target="_blank">' + data[i].last_name + ' ' + data[i].first_name + ' ' + data[i].middle_name + '</a>';
            }
            else
            {
                linkCellValue = data[i].last_name + ' ' + data[i].first_name + ' ' + data[i].middle_name;
            }
            table.append(
                '<tr>' +
                    '<td>' + linkCellValue + '</td>' +
                    '<td>' + data[i].birthday+ '</td>' +
                    '<td>' + data[i].oms_number + '</td>' +
                    '<td>' + data[i].reg_date + '</td>' +
                    '<td>' + data[i].card_number + '</td>' +
                '</tr>'
            );
        }
        table.parents('div.no-display').removeClass('no-display');
    }

    // Отобразить ошибки формы добавления пациента
    $("#patient-withcard-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == 'true') { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            if($(this).attr('id') == '#patient-withcard-form' || $(this).attr('id') == '#patient-withoutcard-form') {
                $(this)[0].reset();
            }
            $('#successAddPopup .writePatient').prop('href', 'http://' + location.host + '/index.php/reception/patient/writepatientsteptwo/?cardid=' + ajaxData.cardNumber);
            $('#successAddPopup').modal({

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

    $("#firstName, #lastName, #middleName").on('keyup', function(e) {
        var str = $(this).val();
        if(str != "") {
            $(this).val(str.charAt(0).toUpperCase() + str.substr(1));
        }
    });

    // Просмотр истории медкарты
    $('#omsSearchWithCardResult').on('click', '.viewHistory', function() {
        $('#panelOfhistoryMedcard').addClass('no-display');
        var medcardId = $(this).attr('href').substr(1);
        $('#viewHistoryPopup .modal-title').text('История карты пациента ' + $(this).text() + ' (карта № ' + medcardId + ' )');
        $('#omsSearchWithCardResult tr').removeClass('active');
        $(this).parents('tr').addClass('active');
        $.ajax({
            'url' : '/index.php/doctors/shedule/gethistorypoints',
            'data' : {
                'medcardid' : medcardId
            },
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == true) {
                    var data = data.data;
                    if(data.length == 0) {
                        alert('Для выбранного пациента нет точек сохранения истории!');
                        return false;
                    }
                    var pointsPanel = $('#panelOfhistoryPoints .panel-body');
                    $(pointsPanel).find('div').remove();
                    for(var i = 0; i < data.length; i++) {

                        doctorName = data[i].last_name;
                        if (data[i].first_name!= '')
                        {
                            doctorName += ( ' '+data[i].first_name.substring(0,1) +'.' );
                        }

                        if (data[i].middle_name!= '')
                        {
                            doctorName += ( ' '+data[i].middle_name.substring(0,1) +'.' );
                        }
                        console.log(data[i]);
                        var div = $('<div>');
                        var a = $('<a>').prop({
                            'href' : '#' + data[i].medcard_id+'_'+data[i].id_record
                        }).prop('title',data[i].template_name ).text(data[i].date_change+ ' - ' + doctorName).appendTo(div);
                        $(div).appendTo(pointsPanel);
                    }
                    $('#viewHistoryPopup').modal({});
                } else {

                }
                return;
            }
        });
        return false;
    });

    $('#panelOfhistoryPoints .panel-body').on('click', 'a', function(e) {
        $('#panelOfhistoryPoints .panel-body div.active').removeClass('active');
        $('#panelOfhistoryMedcard').removeClass('no-display');
        $(this).parent().addClass('active');
        var panel = $('#panelOfhistoryMedcard .modal-body');

        var historyPointCoordinate = $(this).attr('href').substr(1);
        var coordinateStrings = historyPointCoordinate.split('_');

        var medcardId = coordinateStrings[0];
        var pointId = coordinateStrings[1];


        $.ajax({
            'url' : '/index.php/doctors/patient/gethistorymedcard',
            'data' : {
                'medcardid' : medcardId,
                'historyPointId': pointId,
                'date' : $(this).text()
            },
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == 'true') {
                    // Заполняем медкарту-историю значениями
                    var data = data.data;
                    $('#panelOfhistoryMedcard').html(data);
                    /*var form = $('#panelOfhistoryMedcard #patient-edit-form');
                    // Сброс формы
                    $(form)[0].reset();
                    $(form).find('input').val('');
                    for(var i = 0; i < data.length; i++) {
                        var element = $(form).find('#f_history_' + data[i].element_id);
                        if(data[i].type == 3) { // Выпадающий список с множественным выбором
                            data[i].value = $.parseJSON(data[i].value);
                        }
                        element.val(data[i].value);
                    }*/
                } else {

                }
                return;
            }
        });
    });
});