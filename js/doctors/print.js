$(document).ready(function(e) {
    var table = $('.medcardIndex');
    // Заполняем таблицу значениями
    $('.printBtn').on('click', function(e) {
        window.print();
    });

    // Формирование документов на массовую печать
    $('.print-submit button').on('click', function(e) {
        $('#massPrintDocs').hide();
        // Дата приёма
        var greetingDate = $('#greetingDate').val();
        var choosedDoctors = $.fn['doctorChooser'].getChoosed();
        var choosedPatients = $.fn['patientChooser'].getChoosed();
        if(choosedDoctors.length == 0) {
            $('#errorPopup .modal-body .row p').remove();
            $('#errorPopup .modal-body .row').append($('<p>').text('Вы не выбрали ни одного врача!'));
            $('#errorPopup').modal({});
            return false;
        }
        if(choosedPatients.length == 0) {
            $('#errorPopup .modal-body .row p').remove()
            $('#errorPopup .modal-body .row').append($('<p>').text('Вы не выбрали ни одного пациента!'));
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
            'url' : '/index.php/doctors/print/makeprintlistview',
            'data' : {
                'doctors' : $.toJSON(doctorsIds),
                'patients' : $.toJSON(patientIds),
                'date' : greetingDate
            },
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == 'true') {
                    var data = data.data;
                    var table = $('#massPrintDocs tbody');
                    table.find('tr').remove();
                    for(var i = 0; i < data.length; i++) {
                        table.append(
                            '<tr>' +
                                '<td>' + data[i].patient_day + ' ' + data[i].patient_time + '</td>' +
                                '<td>' + data[i].p_last_name + ' ' + data[i].p_first_name + ' ' + data[i].p_middle_name + '</td>' +
                                '<td>' + data[i].d_last_name + ' ' + data[i].d_first_name + ' ' + data[i].d_middle_name + '</td>' +
                                '<td>' +
                                    '<a href="#' + data[i].id + '" title="Напечатать результат приёма">' +
                                        '<span class="glyphicon glyphicon-print"></span>' +
                                    '</a>' +
                                '</td>' +
                            '</tr>'
                        );
                    }
                    $('#massPrintDocs').show();
                } else {

                }
            }
        });

        return false;
    });
});