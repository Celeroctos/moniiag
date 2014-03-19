$(document).ready(function() {
    var medcardStatuses = [
        'В регистратуре',
        'Ожидание приёма',
        'На приёме',
        'Консилиум'
    ];


    
   
   /*
   $('#sheduleTable input[type="checkbox"]').on('click',
               function()
               {
                    var checked = $('#sheduleTable input[type="checkbox"]');
                    if(checked.length == 0) {
                        $('#todoctor-submit').prop('disabled', true);
                    }
                    else
                    {
                        $('#todoctor-submit').prop('disabled', false);
                    }
               }
               );
   */
    $('#greetingDate').val((new Date).getFullYear() + '-' + ((new Date).getMonth() + 1) + '-' + (new Date).getDate());
    $('#greetingDate').trigger('change');
    
    $('#doctorCombo').on('change', function(e) {
        if($(this).val() == 0) {
            $('#doctorChooser').addClass('no-display');
        } else {
            $('#doctorChooser').removeClass('no-display');
        }
    });

    $('#patientCombo').on('change', function(e) {
        if($(this).val() == 0) {
            $('#patientChooser').addClass('no-display');
            $('#status').prop('disabled', false);
        } else {
            $('#patientChooser').removeClass('no-display');
            $('#status').prop('disabled', true);
        }
    });

    // Формирование документов на массовую печать
    $('#sheduleViewSubmit').on('click', function(e) {
        // Дата приёма
        var greetingDate = $('#greetingDate').val();
        var forDoctors = $('#doctorCombo').val();
        var forPatients = $('#patientCombo').val();
        var doctorsIds = [];
        var patientIds = [];

        if(forDoctors == 1) { // Для конкретных врачей
            var choosedDoctors = $.fn['doctorChooser'].getChoosed();
            for(var i = 0; i < choosedDoctors.length; i++) {
                doctorsIds.push(choosedDoctors[i].id);
            }
        }

        if(forPatients == 1) {
            var choosedPatients = $.fn['patientChooser'].getChoosed();
            for(var i = 0; i < choosedPatients.length; i++) {
                patientIds.push(choosedPatients[i].id);
            }
        }

        if(forDoctors == 1 && forPatients == 1 && choosedDoctors.length == 0 && choosedPatients.length == 0) {
            $('#errorPopup .modal-body .row p').remove();
            $('#errorPopup .modal-body .row').append($('<p>').text('Один из критериев расписания - врач или пациент - должен быть выбран!'));
            $('#errorPopup').modal({});
            return false;
        }

        var checked = $('#status').prop('checked');

        $.ajax({
            'url' : '/index.php/reception/shedule/getshedule',
            'data' : {
                'doctors' : $.toJSON(doctorsIds),
                'patients' : $.toJSON(patientIds),
                'date' : greetingDate,
                'status' : checked ? 1 : 0,
                'forDoctors' : forDoctors,
                'forPatients' : forPatients
            },
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == true) {
                    var data = data.data;
                    var shedule = data.shedule;
                    var cabinets = data.cabinets;
                    $('#todoctor-submit').prop('disabled', true);
                    var table = $('#sheduleTable');
                    $(table).find('tbody tr').remove();
                    var currentDoctorId = null;
                    var numRows = 1; // Для rowspan
                    var firstTd = null;
                    var added = false; // Добавлена или нет первая ячейка
                    for(var i = 0; i < shedule.length; i++) {
                        var tr = $('<tr>');
                        var content = '';
                        if(shedule[i].doctor_id != currentDoctorId || i + 1 >= shedule.length) {
                            currentDoctorId = shedule[i].doctor_id;
                            if(i + 1 != shedule.length || shedule.length == 1) {
                                var text = "<span class=\"bold\">" + shedule[i].d_last_name + ' ' + shedule[i].d_first_name + ' ' + shedule[i].d_middle_name + "</span>";
                                if(cabinets[shedule[i].doctor_id].cabNumber != null) {
                                    var cabinet = '<span class="bold text-danger">кабинет ' + cabinets[shedule[i].doctor_id].cabNumber + ' (' + cabinets[shedule[i].doctor_id].description + ')</span>';
                                } else {
                                   var cabinet = '<span class="bold text-danger">кабинет неизвестен</span>';
                                }
                                firstTd = $('<td>').html(text + ', ' + cabinet);
                                numRows = 1;
                                added = false;
                            }
                        }

                        if(shedule[i].medcard_id != null  && shedule[i].motion == 0 && shedule[i].is_accepted!=1) {
                            content +=
                                '<td>' +
                                    '<input type="checkbox" id="c' + shedule[i].medcard_id + '" />' +
                                '</td>';
                        } else {
                            content += '<td>' +
                                       '</td>';
                        }

                        // Движение медкарты
                        if(typeof shedule[i].motion != 'undefined') {
                            var motion = medcardStatuses[shedule[i].motion];
                        } else {
                            var motion = '';
                        }

                        content +=
                            '<td>' +
                                ((shedule[i].medcard_id != null) ?
                                    '<a href="#">' + shedule[i].p_last_name + ' ' + shedule[i].p_first_name + ' ' + shedule[i].p_middle_name + '</a>'
                                    :
                                    shedule[i].p_last_name + ' ' + shedule[i].p_first_name + ' ' + shedule[i].p_middle_name
                                ) +
                            '</td>' +
                            '<td>' +
                                (typeof shedule[i].phone != 'undefined' ? shedule[i].phone : '') +
                            '</td>' +
                            '<td>' +
                                shedule[i].patient_time.substr(0, shedule[i].patient_time.lastIndexOf(':')) +
                            '</td>' +
                            '<td>' +
                                ((shedule[i].medcard_id != null) ?  '<a href="#">' + shedule[i].medcard_id + '</a>' : '<button class="btn btn-primary" id="b' + shedule[i].mediate_id + '">Подтвердить приём</button>') +
                            '</td>' +
                            '<td>' +
                                motion +
                            '</td>';

                        if(shedule[i].time_begin == null && shedule[i].time_end == null) {
                            content += '<td>Приём не начат</td>';
                        } else if(shedule[i].time_begin != null && shedule[i].time_end == null) {
                            content += '<td>Приём начат</td>';
                        } else if(shedule[i].time_begin != null && shedule[i].time_end != null) {
                            content += '<td>Приём окончен</td>';
                        }

                        if(typeof shedule[i].oms_id != 'undefined' && shedule[i].oms_id != null) {
                            content += '<td>' +
                                '<a href="#' + shedule[i].oms_id + '" class="viewHistory" target="_blank">' +
                                    '<span class="glyphicon glyphicon-tasks" title="Посмотреть историю"></span>' +
                                '</a>' +
                            '</td>';
                        } else {
                            content += '<td></td>';
                        }

                        if(!added || shedule.length == 1) {
                            $(tr).append(firstTd, content);
                            added = true;
                        } else {
                            $(tr).append(content);
                            numRows++;
                        }

                        if(i + 1 == shedule.length || shedule[i + 1].doctor_id != currentDoctorId) {
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

    // Отметить все медкарты
    $('#checkAll').on('click', function(e) {
        if($(this).prop('checked')) {
            $(this).prop('title', 'Снять все отмеченные');
            $('#sheduleTable tbody input[type="checkbox"]').prop('checked', true);
        } else {
            $(this).prop('title', 'Отметить все');
            $('#sheduleTable tbody input[type="checkbox"]').prop('checked', false);
        }
        checkToDoctorEnabled(e);
    });

    // Разнос отмеченных карт по кабинетам
    $('#todoctor-submit').on('click', function(e) {
        var checked = $('#sheduleTable tbody input[type="checkbox"]');
        if(checked.length == 0 || $(this).prop('disabled')) {
            return false;
        } 

        var ids = [];
        var numChecked = 0;
        for(var i = 0; i < checked.length; i++) {
            if($(checked[i]).prop('checked')) {
                ids.push($(checked[i]).prop('id').substr(1));
                numChecked++;
            }
        }

        if(numChecked == 0) {
            return false;
        }

        $('#todoctor-submit').prop('disabled', true);

        $.ajax({
            'url' : '/index.php/reception/patient/changemedcardstatus',
            'data' : {
                'ids' : $.toJSON(ids),
                'status' : 1 // Передвинем на "Ожидает приёма"
            },
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == true) {
                    $('#todoctor-submit').prop('disabled', false);
                    $('#sheduleViewSubmit').trigger('click');
                } else {

                }
            }
        });
    });


    $('#print-submit').on('click', function() {
        var printWin = window.open('','','width=800,height=600,menubar=no,location=no,resizable=no,scrollbars=yes,status=no');
        var sheduleTable = $('#sheduleTable');
        printWin.focus();
        var document = $(printWin).document;
        $(document).ready(function() {
            var tableClone = $(sheduleTable).clone();
            $(tableClone).find('td').css({
                'border' : '1px solid #D4D0C8',
                'border-collapse' : 'collapse',
                'padding' : '3px 5px'
            });

            $(tableClone).find('td').css({
               'border-collapse' : 'collapse'
            }); // TODO: collapse не работает?

            $(tableClone).find('tr').each(function(index, element) {
                if($(element).find('td').length == 9) { // Это с колонкой врача
                    $(element).find('td:eq(1)').remove();
                    $(element).find('td:eq(8)').remove();
                } else {
                    $(element).find('td:eq(0)').remove();
                    $(element).find('td:eq(7)').remove();
                }
            });

            // Дату в шапку
            var date = $('#greetingDate').val();
            var parts = date.split('-');
            var dateDiv = $('<div>').html($('<strong class="bold">').css({
                'color' : '#FA5858',
                'font-size' : '16px'
            }).text('Расписание на ' + parts[2] + '.' + parts[1] + '.' + parts[0] + ' г.'));

            $(tableClone).find('button').remove();
            var printBtn = $('<button>').text('Распечатать расписание');
            $(printBtn).on('click', function() {
                window.print();
            });
            $('body', printWin.document).append(dateDiv, tableClone, printBtn);
        });
    })
    
        // Проверяет - нужно ли делатиь активной кнопку разноса медкарт
    function checkToDoctorEnabled(e)
    {
                    var checked = $('#sheduleTable tbody input[type="checkbox"]');
                    var checkedCount = 0;
                    for (i=0;i<checked.length;i++)
                    {
                        if ($(checked[i]).prop('checked'))
                        {
                            checkedCount++;
                            break;
                        }
                    }
                    
                    if(checkedCount > 0) {
                        $('#todoctor-submit').prop('disabled', false);
                    }
                    else
                    {
                        $('#todoctor-submit').prop('disabled', true);
                    }
    }
    
    // Ставим на клик по чекбоксам в таблице расписания обрабочтчик,
    //    задача которого проверить выделен ли хотя бы
    //   один чекбокс в таблице, то делаем кнопку "Разнести отмеченные по кабинетам" активной,
    //   в противном случае - ставим неактивной
    $(document).on('change', '#sheduleTable tbody input[type="checkbox"]',
               function(e)
               {
                    checkToDoctorEnabled(e);
               }
     );
    

});