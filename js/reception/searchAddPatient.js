$(document).ready(function() {
    // Инициализируем пагинацию для списков
    InitPaginationList('omsSearchWithCardResult','oms_number','desc',updatePatientWithCardsList);
    InitPaginationList('omsSearchWithoutCardResult','oms_number','desc',updatePatientWithoutCardsList);
    InitPaginationList('omsSearchMediateResult','id','desc',updatePatientMediateList);
    
    // Поиск по ОМС
    $('#patient-search-submit').click(function(e) {
		$(this).trigger('begin');
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
                    'field' : 'address_reg_str',
                    'op' : 'cn',
                    'data' : $('#addressReg').val()
                },
                {
                    'field' : 'address_str',
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
                    'data' : $('#birthday2').val()
                }
            ]
        };
        return Result;
    }

    function seeNotFoundPopup() {
        for(var i = 0; i < searchStatus.length; i++) {
            if(searchStatus[i] == 1) {
                $('#mediateSubmit-cont').removeClass('no-display'); // Положительный результат, кнопку раскомментировать
                $('#patient-search-submit').trigger('end');
				searchStatus = [];
                return;
            }
        }
        // Если все "не найдено", показывать модалку
        searchStatus = [];
		$('#patient-search-submit').trigger('end');
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
								$('#patient-search-submit').trigger('end');
                            }
                        }
                    }
                } else {
                    $('#errorSearchPopup .modal-body .row p').remove();
                    $('#errorSearchPopup .modal-body .row').append('<p class="errorText">' + data.data + '</p>')
                    $('#errorSearchPopup').modal({
                    });
					$('#patient-search-submit').trigger('end');
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
								$('#patient-search-submit').trigger('end');
                            }
                        }
                    }
                } else {
                    $('#errorSearchPopup .modal-body .row p').remove();
                    $('#errorSearchPopup .modal-body .row').append('<p class="errorText">' + data.data + '</p>')
                    $('#errorSearchPopup').modal({
                    });
					$('#patient-search-submit').trigger('end');
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
								$('#patient-search-submit').trigger('end');
                            }
                        }
                    }
                } else {
                    $('#errorSearchPopup .modal-body .row p').remove();
                    $('#errorSearchPopup .modal-body .row').append('<p class="errorText">' + data.data + '</p>')
                    $('#errorSearchPopup').modal({
                    });
                }
                return;
            }
        });
    }
	
	$('#errorSearchPopup').on('hidden.bs.modal', function(e) {
		$('#patient-search-submit').trigger('end');
	});
    
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
                    '<td>' + data[i].birthday+ '</td>' +
                    '<td>' + data[i].oms_number + '</td>' +
                    '<td>' +
                        '<a title="Регистрировать ЭМК" href="http://' + location.host + '/index.php/reception/patient/viewadd/?patientid=' + data[i].id + '">' +
                            '<span class="glyphicon glyphicon-plus"></span>' +
                        '</a>' +
                    '</td>' +
                    '<td>' +
                        '<a href="#' + data[i].id + '" class="editOms" title="Редактировать ОМС">' +
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
                    '<td><a href="#' + data[i].id + '" class="viewHistory" title="Посмотреть историю движения медкарты" target="_blank">' + data[i].last_name + ' ' + data[i].first_name + ' ' + data[i].middle_name + '</a></td>' +
                    '<td>' + data[i].birthday+ '</td>' +
                    '<td>' + data[i].oms_number + '</td>' +
                   /*  '<td>' + data[i].reg_date + '</td>' + */
                    '<td class="cardNumber">' + data[i].card_number + '</td>' +
                    '<td>' +
                        '<a href="http://' + location.host + '/index.php/reception/patient/viewadd/?patientid=' + data[i].id + '" title="Перерегистрировать ЭМК" target="_blank">' +
                            '<span class="glyphicon glyphicon-plus"></span>' +
                        '</a>' +
                    '</td>' +
                    '<td>' +
                        '<a href="#' + data[i].card_number + '" class="editMedcard" title="Редактировать ЭМК" target="_blank">' +
                            '<span class="glyphicon glyphicon-pencil"></span>' +
                        '</a>' +
                    '</td>' +
                    '<td>' +
                        '<a href="#' + data[i].id + '" class="editOms" title="Редактировать ОМС">' +
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
                        '<a href="#' + data[i].id + '" class="viewHistory" title="Посмотреть историю движения медкарты" target="_blank">' +
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
        if(ajaxData.success == 'true') { // Запрос прошёл удачно, закрываем окно, перезагружаем jqGrid
            if($('#successAddPopup').length > 0) {
                if($(this).attr('id') == '#patient-withcard-form' || $(this).attr('id') == '#patient-withoutcard-form') {
                    $(this)[0].reset();
                }
                $('#successAddPopup .writePatient').prop('href', 'http://' + location.host + '/index.php/reception/patient/writepatientsteptwo/?cardid=' + ajaxData.cardNumber);
                cardNumber = ajaxData.cardNumber;
                console.log(cardNumber);
                $('#successAddPopup').modal({

                });
            } else { // Поиск пациента
                $('#editMedcardPopup').modal('hide');
                $('#editOmsPopup').modal('hide');
                $('#patient-search-submit').trigger('click');
            }

        } else {
            // Удаляем предыдущие ошибки
            var popup = $('#errorAddPopup').length > 0 ? $('#errorAddPopup') : $('#errorSearchPopup');
            $(popup).find('.modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $(popup).find(' .modal-body .row').append("<p class=\"errorText\">" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $(popup).css({
                'z-index' : '1051'
            }).modal({});
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

    // Просмотр истории медкарты
    $(document).on('click', '.viewHistory', function() {
        $('#panelOfhistoryMedcard').parent().addClass('no-display');
        var omsId = $(this).attr('href').substr(1);
        $('#viewHistoryMotionPopup .modal-title').text('История движения карты пациента ' + $(this).parents('tr').find('.viewHistory:eq(0)').text() + ' (карта № ' + $(this).parents('tr').find('.cardNumber:eq(0)').text() + ')');
        $('#omsSearchWithCardResult tr').removeClass('active');
        $(this).parents('tr').addClass('active');
        $("#motion-history").jqGrid('setGridParam',{
            'datatype' : 'json',
            'url' : globalVariables.baseUrl + '/index.php/reception/patient/gethistorymotion/?omsid=' + omsId
        }).trigger("reloadGrid");
        $('#viewHistoryMotionPopup').modal({});
        return false;
    });

    // Редактирование медкарты в попапе
    $(document).on('click', '.editMedcard', function(e) {
        console.log($(this).prop('href'));
        $.ajax({
            'url' : '/index.php/reception/patient/getmedcarddata',
            'data' : {
                'cardid' : $(this).prop('href').substr($(this).prop('href').lastIndexOf('#') + 1)
            },
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == true) {
                    var data = data.data.formModel;
                    var form = $('#patient-medcard-edit-form');
                    for(var i in data) {
                        $(form).find('#' + i).val(data[i]);
                    }

                    $(form).find('#documentGivedate').trigger('change');
                    $(form).find('#privilege').trigger('change');

                    $('#editMedcardPopup').modal({});
                } else {
                    $('#errorSearchPopup .modal-body .row p').remove();
                    $('#errorSearchPopup .modal-body .row').append('<p class="errorText">' + data.data + '</p>')
                    $('#errorSearchPopup').modal({

                    });
                }
                return;
            }
        });
        return false;
    });

    // Редактирование полиса в попапе
    $(document).on('click', '.editOms', function(e) {
        $.ajax({
            'url' : '/index.php/reception/patient/getomsdata',
            'data' : {
                'omsid' : $(this).prop('href').substr($(this).prop('href').lastIndexOf('#') + 1)
            },
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == true) {
                    var data = data.data.formModel;
                    var form = $('#patient-oms-edit-form');
                    for(var i in data) {
                        $(form).find('#' + i).val(data[i]);
                    }

                    $(form).find('#policyGivedate').trigger('change');
                    $(form).find('#birthday').trigger('change');

                    $('#editOmsPopup').modal({});
                } else {
                    $('#errorSearchPopup .modal-body .row p').remove();
                    $('#errorSearchPopup .modal-body .row').append('<p class="errorText">' + data.data + '</p>')
                    $('#errorSearchPopup').modal({

                    });
                }
                return;
            }
        });
        return false;
    });

    var clickedRow = null;
    $('.editAddress').on('click', function(e) {
        clickedRow = $(this).parents('.form-group');
        var hiddenData = $(clickedRow).find('input[type="hidden"]').val();
        if($.trim(hiddenData) != '') {
            $.ajax({
                'url' : '/index.php/guides/cladr/getcladrdata',
                'data' : {
                    'data' : hiddenData
                },
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success) {
                        var data = data.data;
                        if(data.region != null) {
                            $.fn['regionChooser'].addChoosed($('<li>').prop('id', 'r' + data.region[0].id).text(data.region[0].name), data.region[0]);
                        }
                        if(data.district != null) {
                            $.fn['districtChooser'].addChoosed($('<li>').prop('id', 'r' + data.district[0].id).text(data.district[0].name), data.district[0]);
                        }
                        if(data.settlement != null) {
                            $.fn['settlementChooser'].addChoosed($('<li>').prop('id', 'r' + data.settlement[0].id).text(data.settlement[0].name), data.settlement[0]);
                        }
                        if(data.street != null) {
                            $.fn['streetChooser'].addChoosed($('<li>').prop('id', 'r' + data.street[0].id).text(data.street[0].name), data.street[0]);
                        }

                        if(data.house != null) {
                            $('#editAddressPopup #house').val(data.house);
                        }
                        if(data.building != null) {
                            $('#editAddressPopup #building').val(data.building);
                        }
                        if(data.flat != null) {
                            $('#editAddressPopup #flat').val(data.flat);
                        }
                        if(data.postindex != null) {
                            $('#editAddressPopup #postindex').val(data.postindex);
                        }
                    }
                    $('#editAddressPopup').modal('show');
                }
            });
        } else {
            $('#editAddressPopup').modal('show');
        }

        return false;
    });

    $('#editAddressPopup .editSubmit').on('click', function(e) {
        if($.fn['regionChooser'].getChoosed().length > 0) {
            var region = $.fn['regionChooser'].getChoosed()[0].name;
            var regionId = $.fn['regionChooser'].getChoosed()[0].id;
        } else {
            var region = 'Регион неизвестен';
            var regionId = null;
        }

        if($.fn['districtChooser'].getChoosed().length > 0) {
            var district = $.fn['districtChooser'].getChoosed()[0].name;
            var districtId = $.fn['districtChooser'].getChoosed()[0].id;
        } else {
            var district = 'район неизвестен'
            var districtId = null;
        }

        if($.fn['settlementChooser'].getChoosed().length > 0) {
            var settlement = $.fn['settlementChooser'].getChoosed()[0].name;
            var settlementId = $.fn['settlementChooser'].getChoosed()[0].id;
        } else {
            var settlement = 'населённый пункт неизвестен';
            var settlementId = null;
        }

        if($.fn['streetChooser'].getChoosed().length > 0) {
            var street = $.fn['streetChooser'].getChoosed()[0].name;
            var streetId = $.fn['streetChooser'].getChoosed()[0].id;
        } else {
            var street = 'улица неизвестна';
            var streetId = null;
        }

        var house = $('#house').val();
        if($.trim(house) == '') {
            house = 'номера дома нет';
        }

        var building = $('#building').val();
        if($.trim(building) == '') {
            building = 'без корпуса / строения';
        }

        var flat = $('#flat').val();
        if($.trim(flat) == '') {
            flat = 'квартиры нет';
        }

        var postindex = $('#postindex').val();
        if($.trim(postindex) == '') {
            postindex = 'без почтового индекса';
        }

        var dataToJson = {
            'regionId' : regionId,
            'districtId' : districtId,
            'settlementId' : settlementId,
            'streetId' : streetId,
            'house' : $.trim($('#house').val()),
            'building' : $.trim($('#building').val()),
            'flat' : $.trim($('#flat').val()),
            'postindex' : $.trim($('#postindex').val())
        };
        var textStr = region + ', ' + district + ', ' + settlement + ', ' + street + ', ' + house + ', ' + building + ', ' + flat + ', ' + postindex;

        $(clickedRow).find('input[type="text"]').val(textStr);
        $(clickedRow).find('input[type="hidden"]').val($.toJSON(dataToJson));

        // Повтор адреса регистрации в адресе проживания
        if($(clickedRow).find('input[type="text"]').prop('id') == 'addressReg') {
            $(clickedRow).parent().find('#address').val(textStr);
            $(clickedRow).parent().find('#addressHidden').val($.toJSON(dataToJson));
        }

        $('#editAddressPopup').modal('hide');
    });

    $('#editAddressPopup').on('hidden.bs.modal', function(e) {
        $.fn['regionChooser'].clearAll();
        $.fn['regionChooser'].enable();
        $.fn['districtChooser'].clearAll();
        $.fn['districtChooser'].enable();
        $.fn['settlementChooser'].clearAll();
        $.fn['settlementChooser'].enable();
        $.fn['streetChooser'].clearAll();
        $.fn['streetChooser'].enable();
        $('#house').val('');
        $('#building').val('');
        $('#flat').val('');
        $('#postindex').val('');
        e.stopPropagation();
    });

    $('.blockEdit').on('focus', function(e) {
        $(this).blur();
        return false;
    });

    var cardNumber = false;
    $('#writePatientBtn').on('click', function() {
        if(cardNumber !== false) {
            location.href = '/index.php/reception/patient/writepatientsteptwo/?cardid=' + cardNumber
        }
    });
});