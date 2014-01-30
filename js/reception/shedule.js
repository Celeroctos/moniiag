$(document).ready(function() {
    // Формирование документов на массовую печать
    $('#sheduleViewSubmit').on('click', function(e) {
        // Дата приёма
        var greetingDate = $('#greetingDate').val();
        var choosedDoctors = $.fn['doctorChooser'].getChoosed();
        var choosedPatients = $.fn['patientChooser'].getChoosed();

        if(choosedDoctors.length == 0 && choosedPatients.length == 0) {
            $('#errorPopup .modal-body .row p').remove();
            $('#errorPopup .modal-body .row').append($('<p>').text('Один из критериев расписания - врач или пациент - должен быть выбран!'));
            $('#errorPopup').modal({});
            return false;
        }

        var doctorsIds = [];
        var patientIds = [];
        for(var i = 0; i < choosedDoctors.length; i++) {
            doctorsIds.push(choosedDoctors[i].id);
        }
        for(var i = 0; i < choosedPatients.length; i++) {
            patientIds.push(choosedPatients[i].id);
        }

        $.ajax({
            'url' : '/index.php/reception/shedule/getshedule',
            'data' : {
                'doctors' : $.toJSON(doctorsIds),
                'patients' : $.toJSON(patientIds),
                'date' : greetingDate
            },
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == true) {
                    var data = data.data;
                    var table = $('#sheduleTable');
                    $(table).find('tbody tr').remove();
                    var currentDoctorId = null;
                    var numRows = 1; // Для rowspan
                    var firstTd = null;
                    var added = false; // Добавлена или нет первая ячейка
                    for(var i = 0; i < data.length; i++) {
                        var tr = $('<tr>');
                        var content = '';
                        if(data[i].doctor_id != currentDoctorId || i + 1 == data.length) {
                            currentDoctorId = data[i].doctor_id;
                            if(i + 1 != data.length) {
                                firstTd = $('<td>').text(data[i].d_last_name + ' ' + data[i].d_first_name + ' ' + data[i].d_middle_name);
                                numRows = 1;
                                added = false;
                            }
                        }

                        content +=
                            '<td>' +
                                ((data[i].medcard_id != null) ?
                                    '<a href="#">' + data[i].p_last_name + ' ' + data[i].p_first_name + ' ' + data[i].p_middle_name + '</a>'
                                    :
                                    data[i].p_last_name + ' ' + data[i].p_first_name + ' ' + data[i].p_middle_name
                                ) +
                            '</td>' +
                            '<td>' +
                                data[i].patient_time.substr(0, data[i].patient_time.lastIndexOf(':')) +
                            '</td>' +
                            '<td>' +
                                ((data[i].medcard_id != null) ?  '<a href="#">' + data[i].medcard_id + '</a>' : '<button class="btn btn-primary" id="b' + data[i].mediate_id + '">Подтвердить приём</button>') +
                            '</td>' +
                            '<td>' +
                            '</td>';

                        if(data[i].time_begin == null && data.time_end == null) {
                            content += '<td>Приём не начат</td>';
                        } else if(data[i].time_begin != null && data.time_end == null) {
                            content += '<td>Приём начат</td>';
                        } else if(data[i].time_begin != null && data.time_end != null) {
                            content += '<td>Приём окончен</td>';
                        }

                        if(!added) {
                            $(tr).append(firstTd, content);
                            added = true;
                        } else {
                            $(tr).append(content);
                            numRows++;
                        }

                        if(i + 1 == data.length || data[i + 1].doctor_id != currentDoctorId) {
                            $(firstTd).prop({
                                'rowspan' : numRows
                            });
                        }
                        $(table).find('tbody').append(tr);
                    }
                } else {
                    // Удаляем предыдущие ошибки
                    $('#errorPopup .modal-body .row p').remove();
                    // Вставляем новые
                    for(var i in data.errors) {
                        for(var j = 0; j < data.errors[i].length; j++) {
                            $('#errorPopup .modal-body .row').append("<p>" + data.errors[i][j] + "</p>")
                        }
                    }

                    $('#errorPopup').modal({

                    });
                }
            }
        });
    });

    $('#sheduleTable').on('click', 'td button', function() {
        // Логика следующая: опосредованный пациент может быть на самом деле пустым, с ОМС или с медкартой. Ищем по ОМС, т.к. пациент всегда предъявляет ОМС и даём сопоставить, как в случае поиска
        globalVariables.currentMediateId = $(this).prop('id').substr(1);
        $('#acceptGreetingPopup').modal({});
    });
});