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
		/* ticket 10333:
			Если задан только один из этих параметров, то пользователю выдавать сообщение:
			"Недостаточно параметров для поиска" 
		*/
		var counter = 0;
		var check = [
			$.trim($('#docnumber').val()),
			$.trim($('#serie').val()),
			$.trim($('#birthday2').val())
		].forEach(function(element) {
			if(element != '') {
				counter++;
			}
		});
		if(counter == 1) {
			$('#errorSearchPopup .modal-body .row p').remove();
			$('#errorSearchPopup .modal-body .row').append('<p class="errorText">Недостаточно параметров для поиска!</p>');
			$('#errorSearchPopup').modal({});
			return false;
		}
		
        var Result =
        {
            'groupOp' : 'AND',
            'rules' : [
                {
                    'field' : 'oms_number',
                    'op' : 'eq',
                    'data' :  $('#omsNumber').val()
                },
                {
                    'field' : 'first_name',
                    'op' : 'eq',
                    'data' : $('#firstName').val().toUpperCase()
                },
                {
                    'field' : 'middle_name',
                    'op' : 'eq',
                    'data' : $('#middleName').val().toUpperCase()
                },
                {
                    'field' : 'last_name',
                    'op' : 'eq',
                    'data' : $('#lastName').val().toUpperCase()
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
                    'op' : 'bw',
                    'data' : $('#cardNumber').val()
                },
                {
                    'field' : 'serie',
                    'op' : 'eq',
                    'data' : $('#serie').val()
                },
                {
                    'field' : 'docnumber',
                    'op' : 'eq',
                    'data' : $('#docnumber').val()
                },
                {
                    'field' : 'snils',
                    'op' : 'eq',
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
		if(!filters) {
			return -1;
		}
        var PaginationData=getPaginationParameters('omsSearchWithCardResult');
        if (PaginationData!='') {
            PaginationData = '&'+PaginationData;
        }
        $.ajax({
            'url' : '/reception/patient/search/?withonly=0&filters=' + $.toJSON(filters)+PaginationData,
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

    $('.add-patient-submit input').on('click', function(e){
        // Сначала проверим полис
        isRightOmsNumber = $.fn.checkOmsNumber();
        if (!isRightOmsNumber)
        {
            return false;
        }
    });

    cancelSaving = false;

    $('#patient-oms-edit-form #policy').on('blur',function(){
        isAlreadyOmsNumber();
    });

    $('#patient-oms-edit-form #saveOms').on ('click', function (e){

        isAlreadyOmsNumber();
        if (cancelSaving)
        {
            cancelSaving = false;
            return false;
        }
    });

    function isAlreadyOmsNumber()
    {
        // Сначала проверим полис
        isRightOmsNumber = $.fn.checkOmsNumber();
        if (!isRightOmsNumber)
        {
            return false;
        }

        //return false;
        // Запрашиваем аяксом на существование данного полиса

        $.ajax({
            'url' : '/reception/patient/getisomswithnumber',
            'data' : {
                'omsNumberToCheck' : $('#patient-oms-edit-form #policy').val(),
                'omsSeriesToCheck' :  $('#patient-oms-edit-form #omsSeries').val(),
                'omsIdToCheck' : $('#patient-oms-edit-form #id').val()
            },
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'async': false,
            'error': function(){  return false; },
            'success' : function(data, textStatus, jqXHR) {
                data = data.answer;

                if (data.newOms==undefined) return;
                //if (data.newOms.id >= 0)
                //{

                    // Пишем в глобальные переменные Id полиса чтобы можно было карту перевязать на данный полис
                    //globalVariables.medcardOmsToEdit
                    globalVariables.newOmsId = data.newOms.id;

                    if (data.nonCoincides!=undefined)
                    {
                        $('.concidesOmsDataMessage').addClass('no-display');
                        $('.nonConcidesOmsDataMessage').removeClass('no-display');

                        // Выводим данные по новому полису
                        $('.nonConcidesOmsDataMessage #fioNewOms').text(
                            data.newOms.first_name + ' ' + data.newOms.middle_name + ' ' + data.newOms.last_name
                        );

                        $('.nonConcidesOmsDataMessage #birthdayNewOms').text(
                            data.newOms.birthday.split('-').reverse().join('.')
                        );

                        // Выводим данные по старому полису
                        $('.nonConcidesOmsDataMessage #fioOldOms').text(
                            data.oldOms.first_name + ' ' + data.oldOms.middle_name + ' ' + data.oldOms.last_name
                        );

                        $('.nonConcidesOmsDataMessage #birthdayOldOms').text(
                            data.oldOms.birthday.split('-').reverse().join('.')
                        );


                        // Выводим данные по картам (если каждая из них существует)
                        if (data.oldMedcard!=undefined)
                        {
                            $('.nonConcidesOmsDataMessage .oldCardOmsMessage').text('Номер карты: '+data.oldMedcard);
                        }
                        if (data.newMedcard!=undefined)
                        {
                            $('.nonConcidesOmsDataMessage .newCardOmsMessage').text('Номер карты: '+data.newMedcard);
                        }
                    }
                    else
                    {
                        // nonCoincides не определено
                        // Закрываем блок который мы выводим при несовпадении и открываем,
                        //      который мы выводим при совпадении

                        $('.concidesOmsDataMessage').removeClass('no-display');
                        $('.nonConcidesOmsDataMessage').addClass('no-display');

                        // Нужно заполнить поп-ап значениями из полиса
                        if ( $('#existOmsPopup').length>0 )
                        {
                            $('.concidesOmsDataMessage #fioExistingOms').text(
                                data.newOms.first_name + ' ' + data.newOms.middle_name + ' ' + data.newOms.last_name
                            );

                            $('.concidesOmsDataMessage #birthdayExistingOms').text(
                                data.newOms.birthday.split('-').reverse().join('.')
                            );

                            // Если есть поле старой карты в ответе - выводим в интерфейс
                            if (data.newOms.oldMedcard!=undefined)
                            {
                                $('.oldCardOnNewOmsMessage').html(
                                    'Этот полис пользуется для карты №: <strong>'+data.newOms.oldMedcard + '</strong>'
                                );
                            }
                            // иначе - стираем
                            else
                            {
                                $('.oldCardOnNewOmsMessage').text(
                                    ''
                                );
                            }
                        }
                    }
                        $('#existOmsPopup').modal({});
                        cancelSaving = true;
                   // }


                    console.log($('#patient-oms-edit-form #policy').val());
                //}

                return;
            }
        });
    }

    $('#existOmsPopup .btn-success').on('click', function(){
        // Берём номер карты. Берём id нового ОМС и вызываем action смены полиса у карточки
        medcard = globalVariables.medcardOmsToEdit;
        oms = globalVariables.newOmsId;

        $.ajax({
            'url' : '/reception/patient/rebindomsmedcard',
            'data' : {
                //if (isset($_GET['cardNumber']) && isset($_GET['newOmsId']))
                'cardNumber' : medcard,
                'newOmsId' : oms
            },
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {

                if (data.success=='true')
                {
                    // Обновляем таблицу
                    // Прячем поп-ап для редактирования
                    $('#editOmsPopup').modal('hide');
                    $("#patient-search-submit").trigger("click")
                }
                if (data.success=='false')
                {
                    $('#errorPopup .modal-body .row').html('');
                    // Нужно вывести ошибки
                    for(var i in data.errors) {
                      //  for(var j = 0; j < data.errors[i].length; j++) {
                            $('#errorPopup .modal-body .row').append("<p>" + data.errors[i]+ "</p>")
                       // }
                    }

                    $('#errorPopup').modal({});

                }
            }
        });

    });


    function updatePatientWithoutCardsList() {
        var filters = getFilters();
		if(!filters) {
			return -1;
		}
        var PaginationData=getPaginationParameters('omsSearchWithoutCardResult');
        if (PaginationData!='') {
            PaginationData = '&'+PaginationData;
        }
        $.ajax({
            'url' : '/reception/patient/search/?withoutonly=0&filters=' + $.toJSON(filters)+PaginationData,
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
		if(!filters) {
			return -1;
		}
        var PaginationData = getPaginationParameters('omsSearchMediateResult');
        if (PaginationData!='') {
            PaginationData = '&'+PaginationData;
        }
        $.ajax({
            'url' : '/reception/patient/search/?mediateonly=0&filters=' + $.toJSON(filters) + PaginationData,
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
                        '<a title="Регистрировать ЭМК" href="http://' + location.host + '/reception/patient/viewadd/?patientid=' + data[i].id + '">' +
                            '<span class="glyphicon glyphicon-plus"></span>' +
                        '</a>' +
                    '</td>' +
                    '<td>' +
                        '<a href="#' + data[i].id + '" class="editOms omsWOCard" title="Редактировать ОМС">' +
                            '<span class="glyphicon glyphicon-pencil"></span>' +
                        '</a>' +
                    '</td>' +
					'<td>' +
                        '<a href="#' + data[i].id + '" class="deleteOms" title="Удалить ОМС">' +
                            '<span class="glyphicon glyphicon-remove text-danger"></span>' +
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
                        '<a title="Регистрировать ОМС и ЭМК" href="http://' + location.host + '/reception/patient/addomsview/">' +
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
                        '<a href="http://' + location.host + '/reception/patient/viewadd/?patientid=' + data[i].id + '" title="Перерегистрировать ЭМК">' +
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
                        '<a href="#' + data[i].card_number + '" class="writePatientLink">' +
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
	
	$(document).on('click', '.writePatientLink', function(e) {
		$('.writePatientLink').popover('destroy');
		var cardId = $(this).attr('href').substr(1);
		var url = 'http://' + location.host + '/reception/patient/writepatientsteptwo/?cardid=' + cardId;
		$(this).popover({
            animation: true,
            html: true,
            placement: 'bottom',
            title: 'Выберите тип записи пациента:',
            delay: {
                show: 300,
                hide: 300
            },
            container: $(this).parents('td'),
            content: function() {
				var a1 = $('<a>').prop({
					href: url,
					target: '_blank'
				}).css({
					fontWeight: 'bold'
				}).text('На определённое время');
				
				var a2 = $('<a>').prop({
					href: url + '&waitingline=1',
					target: '_blank'
				}).css({
					fontWeight: 'bold'
				}).text('В живую очередь');
				
				return $('<div>').append(a1, $('<br>'), a2);
			}
		});
	   $(this).popover('show');
	   return false;
	});

    // Отобразить ошибки формы добавления пациента
    $("#patient-withoutcard-form, #patient-withcard-form, #patient-medcard-edit-form, #patient-oms-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == 'true') { // Запрос прошёл удачно, закрываем окно, перезагружаем jqGrid
            if($('#successAddPopup').length > 0) {
                if($(this).attr('id') == '#patient-withcard-form' || $(this).attr('id') == '#patient-withoutcard-form') {
                    $(this)[0].reset();
                }
                $('#successAddPopup .writePatient').prop('href', 'http://' + location.host + '/reception/patient/writepatientsteptwo/?cardid=' + ajaxData.cardNumber);
                cardNumber = ajaxData.cardNumber;
                $('#successAddPopup').find('#successCardNumber').text(cardNumber);
                $('#successAddPopup').find('#newPatientFio').text(ajaxData.fioBirthday);
                $('#successAddPopup').modal({
                });
            } else { // Поиск пациента
                $('#editMedcardPopup').modal('hide');
                $('#editOmsPopup').modal('hide');
                if(ajaxData.foundOmsMsg != null) {
                    $('#foundPopup .modal-body .row p').remove();
                    $('#foundPopup .modal-body .row').append("<p>" + ajaxData.foundOmsMsg + "</p>");
                    $('#foundPopup').modal({});
                }
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
		$(this).find("input[type='submit']").trigger("end");
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
        if($(this).val() == 3 || $('#status').val()==3) { // Если тип не временный, но статус может быть погашен,
            // поэтому проверка на поле статус
            $('.policy-enddate').show();
        } else {
            $('.policy-enddate').hide();
        }
    });

    // Дата окончания действия полиса: только для погашенных полисов
    $('#status').on('change', function(e) {
        if($(this).val() == 3 || $('#omsType').val()==3) { // Если статус не временный, но тип может быть временный -
            //    поэтому вставлено второе условие
            $('.policy-enddate').show();
        } else {
            $('.policy-enddate').hide();
        }
    });

    // Печать карты, окно
    $(document).on('click', '.printlink', function(e) {
        var id = $(this).attr('href').substr(1);
        var printWin = window.open('/doctors/print/printmainpage/?medcardid=' + id,'','width=800,height=600,menubar=no,location=no,resizable=no,scrollbars=yes,status=no');
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
            window.open('/reception/patient/viewadd?mediateid=' + mediateClicked + '&patientid=' + patientId,
                        '_blank');
        }
        // Сопоставляется медкарта
        if(patientClicked.substr(0, 1) == 'm') {
            var patientId = patientClicked.substr(1);
            // Экшн, который запишет уже существующего пациента на данную медкарту к врачу
            $.ajax({
                'url' : '/reception/patient/mediatetomedcard?medcardid=' + patientId + '&mediateid=' + mediateClicked,
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
            //return false;
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
            'url' : globalVariables.baseUrl + '/reception/patient/gethistorymotion/?omsid=' + omsId
        }).trigger("reloadGrid");
        $('#viewHistoryMotionPopup').modal({});
        return false;
    });

    // Редактирование медкарты в попапе
    $(document).on('click', '.editMedcard', function(e) {
        console.log($(this).prop('href'));
        $.ajax({
            'url' : '/reception/patient/getmedcarddata',
            'data' : {
                'cardid' : $(this).prop('href').substr($(this).prop('href').lastIndexOf('#') + 1)
            },
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == true) {
					// Проверка статуса ТАСУ
					if(typeof data.data.tasuStatus != 'undefined' && !data.data.tasuStatus) {
						$('.noTasuConnection').removeClass('no-display');
					} else {
						$('.noTasuConnection').addClass('no-display');
					}
                    var data = data.data.formModel;
                    var form = $('#patient-medcard-edit-form');
                    for(var i in data) {
                        $(form).find('#' + i).val(data[i]);
                    }

                    $(form).find('#documentGivedate').trigger('change');
                    $(form).find('#privilege').trigger('change');
                    var emptyFactAddress = true;

                    // Если есть адрес регистрации
                    if ($(form).find('#addressRegHidden').val()!='' && $(form).find('#addressRegHidden').val()!=undefined)
                    {
                        // Если адрес проживания не заполнен, а адрес регистрации - заполнен,
                        //     то берём адрес проживания из адреса регистрации
                        if ($(form).find('#addressHidden').val()!='' && $(form).find('#addressHidden').val()!=undefined)
                        {
                            var addrHidden = $.parseJSON($(form).find('#addressHidden').val());
                            // Перебираем элементы объекта addrHidden
                            //    и проверяем на пустоту поле. Если хотя бы одно поле не пустое
                            //    - значит адрес считается введённым
                            for (var properties in addrHidden)
                            {
                                if (addrHidden[properties]!='' && addrHidden[properties]!=null)
                                {
                                    emptyFactAddress = false;
                                    break;
                                }
                            }
                        }
                        // Если поле фактического проживания пусто - берём значения полей "адрес регистрации" и перекачиваем
                        //     их значения
                        if (emptyFactAddress)
                        {
                            $(form).find('#address').val(  $(form).find('#addressReg').val()  );
                            $(form).find('#addressHidden').val(  $(form).find('#addressRegHidden').val()  );
                        }
                    }
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
    $(document).on('click', '.deleteOms', function(e) {
		var link = $(this);
		if(!window.confirm('Вы действительно хотите удалить этот ОМС?')) {
			return false;
		}
		
		$.ajax({
            'url' : '/reception/patient/deleteoms',
            'data' : {
                'omsid' : $(link).prop('href').substr($(this).prop('href').lastIndexOf('#') + 1)
            },
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == true) {
					$(link).parents('tr').fadeOut(500, function() {
						$(link).parents('tr').remove();
					});
				}	
			}
		});
		
	});

    // Редактирование полиса в попапе
    $(document).on('click', '.editOms', function(e) {
        // Запишем в глобальную переменную номер карты. Это может пригодится при редактировании ОМС при его перепривязки
        globalVariables.medcardOmsToEdit = $($(this).parents('tr')[0]).find('.cardNumber').text();

        // Если у this есть класс "омс без карты" - то надо заблокировать поле "тип" и "номер"
        //  иначе разблокировать
        if ( $(this).hasClass('omsWOCard') )
        {
            $('#omsType').attr('disabled', true);
            $('.omsNumberContainer input').attr('disabled', true);
        }
        else
        {
            $('#omsType').attr('disabled', false);
            $('.omsNumberContainer input').attr('disabled', false);
        }


        $.ajax({
            'url' : '/reception/patient/getomsdata',
            'data' : {
                'omsid' : $(this).prop('href').substr($(this).prop('href').lastIndexOf('#') + 1)
            },
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == true) {
					// Проверка статуса ТАСУ
					if(typeof data.data.tasuStatus != 'undefined' && !data.data.tasuStatus) {
						$('.noTasuConnection').removeClass('no-display');
					} else {
						$('.noTasuConnection').addClass('no-display');
					}
				
                    var insId = data.data.insuranceId;
                    var insName = data.data.insuranceName;
                    var regId = data.data.regionId;
                    var regName = data.data.regionName;
                    var data = data.data.formModel;

                    var form = $('#patient-oms-edit-form');
                    for(var i in data) {
                        $(form).find('#' + i).val(data[i]);
                    }

                    $(form).find('#policyGivedate').trigger('change');
                    $(form).find('#birthday').trigger('change');

                    // Запишем в чюзер страховой компании её название и ИД
                    /*<span class="item"
                    id="r<?php echo $ins['id']; ?>"><?php echo $dia['name']; ?>
                        <span class="glyphicon glyphicon-remove"></span></span>
                       */
                    // Закроем у чюююууузеров блок "variants" и очистим поля ввода
                    $('#insuranceChooser input').val('');
                    $('#regionPolicyChooser input').val('');

                    $('#insuranceChooser .variants').addClass('no-display');
                    $('#insuranceChooser .variants').css('display', '');
                    $('#regionPolicyChooser .variants').addClass('no-display');
                    $('#regionPolicyChooser .variants').css('display', '');

                    if (insId!='' && insId!=null)
                    {
                        $('#insuranceChooser .choosed').html(
                            "<span class=\"item\"" +
                            "id=\"r"+ insId +"\">" + insName +
                                "<span class=\"glyphicon glyphicon-remove\"></span></span>"
                        );

                        // Заблочим чюзер
                        $('#insuranceChooser input').attr('disabled', '');

                        // Добавим в поле insuranceHidden ид-шник
                        $('#insuranceHidden input').val(insId);
                    }
                    else
                    {
                        $('#insuranceChooser .choosed').empty();
                        $('#insuranceChooser input').removeAttr('disabled', '');
                        $('#insuranceHidden input').val('');
                    }

                    // Запишем в чюзер регион
                    if (regId!='' && regId!=null)
                    {
                        $('#regionPolicyChooser .choosed').html(
                            "<span class=\"item\"" +
                                "id=\"r"+ regId +"\">" + regName +
                                "<span class=\"glyphicon glyphicon-remove\"></span></span>"
                        );

                        // Заблочим чюзер
                        $('#regionPolicyChooser input').attr('disabled', '');


                        $('#policyRegionHidden input').val(regId);
                    }
                    else
                    {
                        //$('#regionPolicyChooser .choosed').empty();
                        $.fn['regionPolicyChooser'].clearAll();

                        $('#regionPolicyChooser input').removeAttr('disabled', '');


                        $('#policyRegionHidden input').val('');
                    }

                    $(document).trigger('omsnumberpopulate');

                    $('select#omsType').trigger('change');
                    $('select#status').trigger('change');

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
                'url' : '/guides/cladr/getcladrdata',
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
                            if (data.district [0]!=undefined && data.district [0]['code_cladr']!='000')
                            {
                                $.fn['districtChooser'].addChoosed($('<li>').prop('id', 'r' + data.district[0].id).text(data.district[0].name), data.district[0]);
                            }
                        }
                        if(data.settlement != null) {
                            if (data.settlement[0]!=undefined && data.settlement[0]['code_cladr']!='000000')
                            {
                                $.fn['settlementChooser'].addChoosed($('<li>').prop('id', 'r' + data.settlement[0].id).text(data.settlement[0].name), data.settlement[0]);
                            }
                        }
                        if(data.street != null) {
                            $.fn['streetChooser'].addChoosed($('<li>').prop('id', 'r' + data.street[0].id).text(data.street[0].name), data.street[0]);
                        }

                        console.log(data);

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

    // --- Begin 17.06.2014 ---
    $('#editAddressPopup').on('hidden.bs.modal', function() {
        if(clickedRow != null) {
            $(clickedRow).find('input').focus();
        }
    });

    $('#editAddressPopup').on('shown.bs.modal', function() {
        $('#editAddressPopup #region').focus();
    });
    // --- End 17.06.2014 ---

    $('#editAddressPopup .editSubmit').on('click', function(e) {
        if($.fn['regionChooser'].getChoosed().length > 0) {
            var region = $.fn['regionChooser'].getChoosed()[0].name + ', ';
            var regionId = $.fn['regionChooser'].getChoosed()[0].id;
        } else {
            var region = '';
            var regionId = null;
        }

        if($.fn['districtChooser'].getChoosed().length > 0) {
            var district = $.fn['districtChooser'].getChoosed()[0].name + ', ';
            var districtId = $.fn['districtChooser'].getChoosed()[0].id;
        } else {
            var district = ''
            var districtId = null;
        }

        if($.fn['settlementChooser'].getChoosed().length > 0) {
            var settlement = $.fn['settlementChooser'].getChoosed()[0].name + ', ';
            var settlementId = $.fn['settlementChooser'].getChoosed()[0].id;
        } else {
            var settlement = '';
            var settlementId = null;
        }

        if($.fn['streetChooser'].getChoosed().length > 0) {
            var street = $.fn['streetChooser'].getChoosed()[0].name + ', ';
            var streetId = $.fn['streetChooser'].getChoosed()[0].id;
        } else {
            var street = '';
            var streetId = null;
        }

        var house = $('#house').val();
        if($.trim(house) == '') {
            house = '';
        } else {
			house += ', '
		}

        var building = $('#building').val();
        if($.trim(building) == '') {
            building = '';
        } else {
			building += ', ';
		}

        var flat = $('#flat').val();
        if($.trim(flat) == '') {
            flat = '';
        } else {
			flat += ', ';
		}

        var postindex = $('#postindex').val();
        if($.trim(postindex) == '') {
            postindex = '';
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
        var textStr = region + district + settlement + street + house + building + flat + postindex;

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

    $('.blockEdit').on('keydown', function(e) {
        console.log(e.keyCode);
        if(e.keyCode != 9 && e.keyCode != 13) {
            return false;
        }
    });

    var cardNumber = false;
    $('#writePatientBtn').on('click', function() {
        if(cardNumber !== false) {
            location.href = '/reception/patient/writepatientsteptwo/?cardid=' + cardNumber
        }
    });

    $('#printPatientBtn').on('click', function() {
        if(cardNumber !== false) {
            var printWin = window.open('/doctors/print/printmainpage/?medcardid=' + cardNumber,'','width=800,height=600,menubar=no,location=no,resizable=no,scrollbars=yes,status=no');
            printWin.focus();
            return false;
        }
    });

    // По закрытию окна с инфой об успешной регистрацией - перенаправляем на поиск
    $('#successAddPopup').on(
        'hidden.bs.modal',
        function()
        {
            location.href = '/reception/patient/viewsearch';
        }
    );


    function serializeParameters()
    {
        result = '';

        if ( $('#omsNumber').length>0 && $('#omsNumber').val().length>0 )
        {
            result += '&newOmsNumber='+encodeURIComponent($('#omsNumber').val());
        }


        if ( $('#lastName').length>0 && $('#lastName').val().length>0)
        {
            result += '&newLastName='+encodeURIComponent($('#lastName').val());
        }

        if ( $('#firstName').length>0 && $('#firstName').val().length>0)
        {
            result += '&newFirstName='+encodeURIComponent($('#firstName').val());
        }

        if ( $('#middleName').length>0 && $('#middleName').val().length>0)
        {
            result += '&newMiddleName='+encodeURIComponent($('#middleName').val());
        }


        if ( $('#birthday2').length>0 && $('#birthday2').val().length>0)
        {
            result += '&newBirthday='+encodeURIComponent($('#birthday2').val());
        }

        if ( $('#serie').length>0 && $('#serie').val().length>0)
        {
            result += '&newSerie='+encodeURIComponent($('#serie').val());
        }

        if ( $('#docnumber').length>0 && $('#docnumber').val().length>0)
        {
            result += '&newDocnumber='+encodeURIComponent($('#docnumber').val());
        }

        if ( $('#snils').length>0 && $('#snils').val().length>0)
        {
            result += '&newSnils='+encodeURIComponent($('#snils').val());
        }

        if ( result.length>0)
            result = ('?'+result.substr(1));

        return result;
    }

    // Переадресация на страницу создания нового пациента
    $('#createNewPatientBtn').on('click', function(e) {
        // В запрос подаём данные из полей, которые мы ввели в форме, чтобы при создании пациента не вводить эти
        //   данные заново
        location.href = '/reception/patient/viewadd' + serializeParameters();
    });

    $('#patient-search-form').on('keydown', function(e) {
        // Обработка Enter
        if(e.keyCode == 13) {
            $('#patient-search-submit').trigger('click');
        }
    });
});