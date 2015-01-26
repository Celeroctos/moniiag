$(document).ready(function() {
	InitPaginationList('greetingsSearchResult','id','desc',updatePatientList);
    var mediateStatus = [];

	function getFilters() {
        if($.trim($('#greetingDate').val()) != '') {
            var currentDate = new Date();
            var inputDate =  $('#greetingDate').val();
            var inputDateSplit = inputDate.split('-');
            var dateSplitted = new Date(inputDateSplit[0], inputDateSplit[1], inputDateSplit[2]);
            if(dateSplitted.getTime() < currentDate.getTime()) {
                alert('Искомая дата для изменения / отмены приёмов меньше, чем текущая дата!');
                return false;
            }
        }

        // Смотрим на ФИО пациента
        var patientFio = $('#patientFio').val();
        var parts = patientFio.split(' '); // По пробелу. ФИО = Ф_И_О
        var patientFioFields = [];
        for(var i = 0; i < parts.length; i++) {
            if($.trim(parts[i]) != '') {
                patientFioFields.push($.trim(parts[i]).toLowerCase());
            }
        }

        // Смотрим на ФИО врача
        var doctorFio = $('#doctorFio').val();
        var parts = doctorFio.split(' '); // По пробелу. ФИО = Ф_И_О
        var doctorFioFields = [];
        for(var i = 0; i < parts.length; i++) {
            if($.trim(parts[i]) != '') {
                doctorFioFields.push($.trim(parts[i]).toLowerCase());
            }
        }

        var result = {
            'groupOp' : 'AND',
            'rules' : [
                {
                    'field' : 'p_middle_name',
                    'op' : 'cn',
                    'data' : patientFioFields.length > 2 ?  patientFioFields[2] : '' //$('#middleName').val()
                },
                {
                    'field' : 'p_last_name',
                    'op' : 'cn',
                    'data' :  patientFioFields.length > 0 ?  patientFioFields[0] : '' //$('#lastName').val()
                },
                {
                    'field' : 'p_first_name',
                    'op' : 'cn',
                    'data' : patientFioFields.length > 1 ?  patientFioFields[1] : '' //$('#firstName').val()
                },
                {
                    'field' : 'd_middle_name',
                    'op' : 'cn',
                    'data' : doctorFioFields.length > 2 ?  doctorFioFields[2] : '' //$('#middleName').val()
                },
                {
                    'field' : 'd_last_name',
                    'op' : 'cn',
                    'data' :  doctorFioFields.length > 0 ?  doctorFioFields[0] : '' //$('#lastName').val()
                },
                {
                    'field' : 'd_first_name',
                    'op' : 'cn',
                    'data' : doctorFioFields.length > 1 ?  doctorFioFields[1] : '' //$('#firstName').val()
                },
                {
                    'field' : 'medcard_id',
                    'op' : 'bw',
                    'data' : $('#cardNumber').val()
                },
				{
					'field' : 'patient_day',
					'op' : 'eq',
					'data' : $('#greetingDate').val()
				}
            ]
        };

        if($.trim($('#phoneFilter').val()) != '+7') {
            result.rules.push({
                'field' : 'phone',
                'op' : 'cn',
                'data' : $('#phoneFilter').val()
            });
        }

        return result;
    }
	
	$('#greetings-search-submit').on('click', function(e) {
		updatePatientList();
	});
	
	function updatePatientList() {
		var PaginationData = getPaginationParameters('greetingsSearchResult');
		var filters = getFilters();
        if(filters === false) {
            return;
        }
        $('#greetings-search-submit').trigger('begin');

		$.ajax({
            'url' : '/reception/shedule/search?'+ PaginationData,
            'cache' : false,
            'dataType' : 'json',
			'data' : {
				'mediateonly' : 0,
                'notBeginned': 1,
                'isCallcenter' : globalVariables.isCallCenter ? globalVariables.isCallCenter : 0,
				'filters' : $.toJSON(filters)
			},
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                $('#greetings-search-submit').trigger('end');
                if(data.success) {
					if(data.rows.length > 0) {
                        displayAllGreetings(data.rows);
                        printPagination('greetingsSearchResult',data.total);
                    } else {
                        $('#notFoundPopup').modal({
                        });
                    }
				} else {
                    $('#notFoundPopup').modal({
                    });
				}
			}
		});
	}

    function displayAllGreetings(data) {
        // Заполняем пришедшими данными таблицу тех, кто без карт
        var table = $('#greetingsSearchResult tbody');
        var currentDate = new Date();
        table.find('tr').remove();
        mediateStatus = [];
        for(var i = 0; i < data.length; i++) {
            data[i].patient_day = data[i].patient_day.split('-').reverse().join('.');
            if(data[i].patient_time) {
				var timeSplit = data[i].patient_time.split(':');
				timeSplit.pop();
				data[i].patient_time = timeSplit.join(':');
			} 
            mediateStatus['i' + data[i].id] = {
                id : data[i].id,
                isMediate : data[i].card_number == null ? 1 : 0,
                cardNumber: data[i].card_number
            }; // Опосредованный пациент или нет

            var content = '<tr>' +
                '<td>' +
                    (data[i].card_number == null ? 'Опосредованный' : data[i].card_number ) +
                '</td>';
            if(data[i].card_number != null) {
                content += '<td>' +
                    '<a href="#" title="Посмотреть информацию по пациенту">' + data[i].p_last_name + ' ' + data[i].p_first_name + ' ' + data[i].p_middle_name + '</a>' +
                '</td>';
            } else {
                content += '<td>' + data[i].m_last_name + ' ' + data[i].m_first_name + ' ' + data[i].m_middle_name + '</td>';
            }

            content += '<td>' +
                    '<a href="#" class="" title="Изменить дату приёма">' + data[i].patient_day + '</a>' +
                '</td>' +
                '<td>' +
                    (data[i].patient_time ?  '<a href="#" class="" title="Изменить время приёма">' + data[i].patient_time + '</a>' : 'Живая очередь') +
                '</td>' +
                '<td>' +
                    '<a href="#">' +
                    data[i].d_last_name + ' ' + data[i].d_first_name + ' ' + data[i].d_middle_name + ', ' + data[i].post + '</a>' +
                '</td>' +
                '<td>' + data[i].phone + '</td>';
			
			if(data[i].patient_time) {
				var timeSplitted = data[i].patient_time.split(':');
			} else {
				var timeSplitted = '';
			}
            var daySplitted = data[i].patient_day.split('.');
            var iterateDate = new Date(parseInt(daySplitted[2]), parseInt(daySplitted[1]) - 1, parseInt(daySplitted[0]), parseInt(timeSplitted[0]), parseInt(timeSplitted[1]));

            if(iterateDate.getTime() > (new Date()).getTime()) {
                content += '<td>' +
                    '<a href="#' + data[i].id + '" class="cancelGreeting" title="Отписать пациента">' +
                        '<span class="glyphicon glyphicon-remove"></span>' +
                    '</a>' +
                '</td>' +
                '<td>' +
                    '<a href="#' + data[i].id + '" id="i' + data[i].id + '" class="editGreeting" title="Редактировать запись">' +
                        '<span class="glyphicon glyphicon-pencil"></span>' +
                    '</a>' +
                '</td>';
            } else {
                content += '<td>' +
                    '<a href="#' + data[i].id + '" title="Операция отписания недоступна: время прошло" style="opacity: 0.5">' +
                        '<span class="glyphicon glyphicon-remove"></span>' +
                    '</a>' +
                '</td>' +
                '<td>' +
                    '<a href="#' + data[i].id + '" id="i' + data[i].id + '" title="Операция редактирования недоступна: время прошло" style="opacity: 0.5">' +
                        '<span class="glyphicon glyphicon-pencil"></span>' +
                    '</a>' +
                '</td>';
            }

            content += '</tr>';

            table.append(
                content
            );
        }
        table.parents('div.no-display').removeClass('no-display');
    }


    $('#greetingsSearchResult').on('click', '.cancelGreeting', function(e) {
        if(window.confirm('Вы действительно хотите отменить приём?')) {
            var link = $(this);
            var params = {
                id : $(this).attr('href').substr(1)
            };
            $.ajax({
                'url' : '/doctors/shedule/unwritepatient',
                'data' : params,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $(link).parents('tr').fadeOut(500, function() {
                            $(link).parents('tr').remove();
                        });
                    } else {
                        $('#cannotUnwritePopup p').text(data.data);
                        $('#cannotUnwritePopup').modal({

                        });
                    }
                    return;
                }
            });
        }
    });

    $('#greetingsSearchResult').on('click', '.editGreeting', function(e) {
        var greetingId = $(this).prop('id').substr(1);
        var mediateData = mediateStatus['i' + greetingId];
        // Опосредованных по одному адресу, обычных, с картами, - по другому

        if(!mediateData.isMediate) {
            var url = '/reception/patient/writepatientsteptwo?callcenter=' + globalVariables.isCallCenter + '&cardid=' + mediateData.cardNumber + '&greeting_id=' + mediateData.id
        } else {
            var url = '/reception/patient/writepatientwithoutdata?callcenter=' + globalVariables.isCallCenter + '&greeting_id=' + mediateData.id;
        }

        location.href = 'http://' + location.host + url;
    });

    $('#doctors-search-greetings').on('keydown', function(e) {
        if(e.keyCode == 13) {
            $('#greetings-search-submit').trigger('click');
        }
    });
});