$(document).ready(function() {
    // Инициализируем пагинацию для списков
    InitPaginationList('omsSearchWithCardResult','oms_number','desc',updatePatientWithCardsList);
    InitPaginationList('omsSearchWithoutCardResult','oms_number','desc',updatePatientWithoutCardsList);
    InitPaginationList('omsSearchMediateResult','id','desc',updatePatientMediateList);
    
    // Поиск по ОМС
    $('#patient-search-submit').click(function(e) {
        $('#mediateSubmit-cont').addClass('no-display');
        $('#mediate-attach-submit').addClass('disabled');
        mediateClicked = false;
        patientClicked = false; // Сбрасываем параметры сопоставления пациента
        updatePatientWithCardsList();
        updatePatientWithoutCardsList();
        updatePatientMediateList();
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
                            displayAllWithCard(data.rows);
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
    
    function updatePatientWithoutCardsList() {
        var filters = getFilters();
        var PaginationData=getPaginationParameters('omsSearchWithoutCardResult');
        if (PaginationData!='') {
            PaginationData = '&'+PaginationData;
        }
        $.ajax({
            'url' : '/index.php/reception/patient/search/?withoutonly=0&filters=' + $.toJSON(filters)+PaginationData,
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == true) {
                    // Изначально таблицы скрыты
                    $('#withoutCardCont').addClass('no-display');

                    if(data.rows.length == 0) {
                        searchStatus.push(0);
                        if(searchStatus.length == 3) {
                            seeNotFoundPopup();
                        }
                    } else {
                        if(data.rows.length > 0) {
                            searchStatus.push(1);
                            displayAllWithoutCard(data.rows);
                            printPagination('omsSearchWithoutCardResult',data.total);
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

    function updatePatientMediateList() {
        var filters = getFilters();
        var PaginationData = getPaginationParameters('omsSearchMediateResult');
        if (PaginationData!='') {
            PaginationData = '&'+PaginationData;
        }
        $.ajax({
            'url' : '/index.php/reception/patient/search/?mediateonly=0&filters=' + $.toJSON(filters) + PaginationData,
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == true) {
                    // Изначально таблицы скрыты
                    $('#mediateCont').addClass('no-display');

                    if(data.rows.length == 0) {
                        searchStatus.push(0);
                        if(searchStatus.length == 3) {
                            seeNotFoundPopup();
                        }
                    } else {
                        if(data.rows.length > 0) {
                            searchStatus.push(1);
                            displayAllMediate(data.rows);
                            printPagination('omsSearchMediateResult',data.total);
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
    
    
    // Отобазить таблицу тех, кто без карт
    function displayAllWithoutCard(data) {
        // Заполняем пришедшими данными таблицу тех, кто без карт
        var table = $('#omsSearchWithoutCardResult tbody');
        table.find('tr').remove();
        for(var i = 0; i < data.length; i++) {
            table.append(
                '<tr>' +
                    '<td>' +
                        '<input type="radio" name="existsPatient" value="o' + data[i].id + '"/>' +
                    '</td>' +
                    '<td><a href="#" title="Посмотреть информацию по пациенту">' + data[i].last_name + ' ' + data[i].first_name + ' ' + data[i].middle_name + '</a></td>' +
                    '<td>' + data[i].oms_number + '</td>' +
                    '<td>' +
                        '<a title="Регистрировать ЭМК" href="http://' + location.host + '/index.php/reception/patient/viewadd/?patientid=' + data[i].id + '">' +
                            '<span class="glyphicon glyphicon-plus"></span>' +
                        '</a>' +
                    '</td>' +
                    '<td>' +
                        '<a title="Редактировать ОМС" href="http://' + location.host + '/index.php/reception/patient/editomsview/?omsid=' + data[i].id + '">' +
                            '<span class="glyphicon glyphicon-pencil"></span>' +
                        '</a>' +
                    '</td>' +
                '</tr>'
            );
        }
        table.parents('div.no-display').removeClass('no-display');
    }

    // Отобазить таблицу тех, кто опосредован
    function displayAllMediate(data) {
        // Заполняем пришедшими данными таблицу тех, кто без карт
        var table = $('#omsSearchMediateResult tbody');
        table.find('tr').remove();
        for(var i = 0; i < data.length; i++) {
            table.append(
                '<tr>' +
                    '<td>' +
                        '<input name="mediatePatient" value="' + data[i].id + '" type="radio" />' +
                    '</td>' +
                    '<td>' + data[i].last_name + ' ' + data[i].first_name + ' ' + data[i].middle_name + '</td>' +
                    '<td>' + data[i].phone + '</td>' +
                    '<td>' +
                        '<a title="Регистрировать ОМС и ЭМК" href="http://' + location.host + '/index.php/reception/patient/addomsview/" target="_blank">' +
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
                    '<td>' +
                        '<input type="radio" name="existsPatient" value="m' + data[i].card_number + '"/>' +
                    '</td>' +
                    '<td><a href="#" title="Посмотреть информацию по пациенту" target="_blank">' + data[i].last_name + ' ' + data[i].first_name + ' ' + data[i].middle_name + '</a></td>' +
                    '<td>' + data[i].birthday+ '</td>' +
                    '<td>' + data[i].oms_number + '</td>' +
                    '<td>' + data[i].reg_date + '</td>' +
                    '<td>' + data[i].card_number + '</td>' +
                    '<td>' +
                        '<a href="http://' + location.host + '/index.php/reception/patient/viewadd/?patientid=' + data[i].id + '" title="Перерегистрировать ЭМК" target="_blank">' +
                            '<span class="glyphicon glyphicon-plus"></span>' +
                        '</a>' +
                    '</td>' +
                    '<td>' +
                        '<a href="http://' + location.host + '/index.php/reception/patient/editcardview/?cardid=' + data[i].card_number + '" title="Редактировать ЭМК" target="_blank">' +
                            '<span class="glyphicon glyphicon-pencil"></span>' +
                        '</a>' +
                    '</td>' +
                    '<td>' +
                        '<a href="http://' + location.host + '/index.php/reception/patient/editomsview/?omsid=' + data[i].id + '" title="Редактировать ОМС">' +
                            '<span class="glyphicon glyphicon-pencil"></span>' +
                        '</a>' +
                    '</td>' +
                    '<td>' +
                        '<a href="http://' + location.host + '/index.php/reception/patient/writepatientsteptwo/?cardid=' + data[i].card_number + '" title="Записать пациента на приём" target="_blank">' +
                            '<span class="glyphicon glyphicon-dashboard"></span>' +
                        '</a>' +
                    '</td>' +
                    '<td>' +
                        '<a class="printlink" title="Печать титульной страницы медкарты" href="#' + data[i].card_number + '" target="_blank">' +
                            '<span class="glyphicon glyphicon-print"></span>' +
                        '</a>' +
                    '</td>' +
                                      '</td>' +
                    '<td>' +
                        '<a title="История движения медкарты" href="http://' + location.host + '/index.php/reception/patient/viewhistorymotion/?omsid=' + data[i].id + '" target="_blank">' +
                            '<span class="glyphicon glyphicon-list"></span>' +
                        '</a>' +
                    '</td>' +
                '</tr>'
            );
        }
        table.parents('div.no-display').removeClass('no-display');
    }

    // Отобразить ошибки формы добавления пациента
    $("#patient-withoutcard-form, #patient-withcard-form, #patient-medcard-edit-form, #patient-oms-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
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

    // Адрес регистрации совпадает с адресом проживания (флажок)
    $('#addressReg').on('keyup', function() {
        var addressReg = $(this).val();
        $('#address').val(addressReg);
    });

    // Льготы: раскрытие списка дополнительного редактирования документа льготы, если выбран какой-то тип льготы
    $('#privilege').on('change', function(e) {
        var privFields = $('#privDocname, #privDocnumber, #privDocserie, #privDocGivedate').parents('.form-group');

        if($(this).val() != -1) {
            $(privFields).show();
        } else {
            $(privFields).find('input').val('');
            $(privFields).hide();
        }
    });

    // Дата окончания действия полиса: только для временных полисов
    $('#omsType').on('change', function(e) {
        if($(this).val() == 1) {
            $('.policy-enddate').show();
        } else {
            $('.policy-enddate').hide();
        }
    });

    // Печать карты, окно
    $(document).on('click', '.printlink', function(e) {
        var id = $(this).attr('href').substr(1);
        var printWin = window.open('/index.php/doctors/print/printmainpage/?medcardid=' + id,'','width=800,height=600,menubar=no,location=no,resizable=no,scrollbars=yes,status=no');
        printWin.focus();
        return false;
    });

    // Подтверждение опосредованных пациентов
    var mediateClicked = false;
    var patientClicked = false;
    $('#omsSearchMediateResult').on('click', 'input[type="radio"]', function(e) {
        mediateClicked = $(this).val();
        if(patientClicked !== false) {
            $('#mediate-attach-submit').removeClass('disabled');
        } else {
            $('#mediate-attach-submit').addClass('disabled');
        }
    });
    $('#omsSearchWithoutCardResult, #omsSearchWithCardResult').on('click', 'input[name="existsPatient"]', function(e) {
        patientClicked = $(this).val();
        if(mediateClicked !== false  || typeof globalVariables.currentMediateId != 'undefined') {
            $('#mediate-attach-submit').removeClass('disabled');
        } else {
            $('#mediate-attach-submit').addClass('disabled');
        }
    });

    $('#mediate-attach-submit').on('click', function() {
        if($(this).hasClass('disabled')) {
            return false;
        }
        if(typeof globalVariables.currentMediateId != 'undefined') {
            mediateClicked = globalVariables.currentMediateId;
        }
        // Может быть три ситуации: пациент опосредованный без данных, пациент опосредован, но имеет в поликлинике ЭМК, либо имеет только ОМС
        // Смотрим на идентификаторы
        // Сопоставляется ОМС
        if(patientClicked.substr(0, 1) == 'o') {
            var patientId = patientClicked.substr(1);
            // Редирект на экшн, который прокинет данные к опосредованному пациенту и медкарте
            window.open('/index.php/reception/patient/viewadd?mediateid=' + mediateClicked + '&patientid=' + patientId,
                        '_blank');
        }
        // Сопоставляется медкарта
        if(patientClicked.substr(0, 1) == 'm') {
            var patientId = patientClicked.substr(1);
            // Экшн, который запишет уже существующего пациента на данную медкарту к врачу
            $.ajax({
                'url' : '/index.php/reception/patient/mediatetomedcard?medcardid=' + patientId + '&mediateid=' + mediateClicked,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $('#mediateOkPopup').modal({});
                        if($('#sheduleViewSubmit').length > 0) {
                            $('#sheduleViewSubmit').trigger('click');
                        }
                        if($('#patient-search-submit').length > 0) {
                            $('#patient-search-submit').trigger('click');
                        }
                    } else {

                    }
                    return;
                }
            });
        }

    });

    $('#mediateOkPopup').on('hidden.bs.modal', function() {
        if($('#acceptGreetingPopup').length > 0) {
            $('#acceptGreetingPopup').modal('hide');
        }
    });

    $('#addressReg, #address').on('keydown', function(e) {
        if(e.keyCode == 13) {
            return false;
        }
    });
});