$(document).ready(function() {
	InitPaginationList('greetingsSearchResult','id','desc',updatePatientList);
    var mediateStatus = [];

	function getFilters() {
		var doctorIds = [];
		var omsIds = [];
		var choosedDoctors = $.fn['doctorChooser'].getChoosed();
		var choosedOms = $.fn['patientChooser'].getChoosed();
		for(var i = 0; i < choosedDoctors.length; i++) {
			doctorIds.push(choosedDoctors[i].id);
		}
		for(var i = 0; i < choosedOms.length; i++) {
			omsIds.push(choosedOms[i].id);
		}

        var result = {
            'groupOp' : 'AND',
            'rules' : [
                {
                    'field' : 'doctor_id',
                    'op' : 'in',
                    'data' :  doctorIds
                },
				{
                    'field' : 'oms_id',
                    'op' : 'in',
                    'data' :  omsIds
                },
                {
                    'field' : 'medcard_id',
                    'op' : 'eq',
                    'data' : $('#cardNumber').val()
                },
                {
                    'field' : 'phone',
                    'op' : 'eq',
                    'data' : $('#phoneNumber').val()
                },
				{
					'field' : 'patient_day',
					'op' : 'eq',
					'data' : $('#greetingDate').val()
				}
            ]
        };
        return result;
    }
	
	$('#greetings-search-submit').on('click', function(e) {
		updatePatientList();
	});
	
	function updatePatientList() {
		var PaginationData = getPaginationParameters('greetingsSearchResult');
		var filters = getFilters();
        $('#greetings-search-submit').trigger('begin');

		$.ajax({
            'url' : '/index.php/reception/shedule/search?'+ PaginationData,
            'cache' : false,
            'dataType' : 'json',
			'data' : {
				'mediateonly' : 0,
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
        table.find('tr').remove();
        mediateStatus = [];
        for(var i = 0; i < data.length; i++) {
            data[i].patient_day = data[i].patient_day.split('-').reverse().join('.');
            var timeSplit = data[i].patient_time.split(':');
            timeSplit.pop();
            data[i].patient_time = timeSplit.join(':');
            mediateStatus['i' + data[i].id] = {
                id : data[i].id,
                isMediate : data[i].card_number == null ? 1 : 0,
                cardNumber: data[i].card_number
            }; // Опосредованный пациент или нет

            table.append(
                '<tr>' +
                    '<td>' +
                        (data[i].card_number == null ? 'Опосредованный' : data[i].card_number ) +
                    '</td>' +
                    '<td>' +
                        '<a href="#" title="Посмотреть информацию по пациенту">' + data[i].p_last_name + ' ' + data[i].p_first_name + ' ' + data[i].p_middle_name + '</a>' +
                    '</td>' +
                    '<td>' +
                        '<a href="#" class="" title="Изменить дату приёма">' + data[i].patient_day + '</a>' +
                    '</td>' +
                    '<td>' +
                        '<a href="#" class="" title="Изменить время приёма">' + data[i].patient_time + '</a>' +
                    '</td>' +
                    '<td>' +
                        '<a href="#">' +
                            data[i].d_last_name + ' ' + data[i].d_first_name + ' ' + data[i].d_middle_name + ', ' + data[i].post + '</a>' +
                    '</td>' +
                    '<td>' + data[i].phone + '</td>' +
                    '<td>' +
                        '<a href="#' + data[i].id + '" class="cancelGreeting" title="Отписать пациента">' +
                            '<span class="glyphicon glyphicon-remove"></span>' +
                        '</a>' +
                    '</td>' +
                    '<td>' +
                        '<a href="#' + data[i].id + '" id="i' + data[i].id + '" class="editGreeting" title="Редактировать запись">' +
                            '<span class="glyphicon glyphicon-pencil"></span>' +
                        '</a>' +
                    '</td>' +
                '</tr>'
            );
        }
        table.parents('div.no-display').removeClass('no-display');
    }


    $('#greetingsSearchResult').on('click', '.cancelGreeting', function(e) {
        var link = $(this);
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
                    $(link).parents('tr').fadeOut(500, function() {
                        $(link).parents('tr').remove();
                    });
                } else {

                }
                return;
            }
        });
    });

    $('#greetingsSearchResult').on('click', '.editGreeting', function(e) {
        var greetingId = $(this).prop('id').substr(1);
        var mediateData = mediateStatus['i' + greetingId];
        // Опосредованных по одному адресу, обычных, с картами, - по другому

        if(!mediateData.isMediate) {
            var url = '/index.php/reception/patient/writepatientsteptwo?callcenter=1&cardid=' + mediateData.cardNumber + '&greeting_id=' + mediateData.id
        } else {
            var url = '/index.php/reception/patient/writepatientwithoutdata?callcenter=1&greeting_id=' + mediateData.id;
        }

        location.href = 'http://' + location.host + url;
    });
});