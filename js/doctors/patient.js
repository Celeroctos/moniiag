$(document).ready(function() {
    // Редактирование медкарты
    $("#patient-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового кабинета, перезагружаем jqGrid
            $('#successEditPopup').modal({

            });
            // Вставляем новую запись в список истории
            if(ajaxData.hasOwnProperty('historyDate')) {
                var newDiv = $('<div>');
                $(newDiv).append($('<a>').prop('href', '#' + globalVariables.medcardNumber).attr('class', 'medcard-history-showlink').text(ajaxData.historyDate));
                $('#accordionH .accordion-inner div:first').before(newDiv);
            }
        } else {

        }
    });

    $("#date-cont").on('changeDate', function() {
        $('#change-date-form').submit();
    });

    // Отметка дат, в которых есть пациенты
    $("#date-cont").on('show', function(e) {
        $("#date-cont").trigger("refresh")
    });
    $("#date-cont").on('changeMonth', function(e) {
        $("#date-cont").trigger("refresh", [e.date]);
    });

    $("#date-cont").on('refresh', function(e, date) {
        if(typeof date == 'undefined') {
            var currentDate = $('#filterDate').val();
            var currentDateParts = currentDate.split('-');
        } else {
            var dateObj = new Date(date);
            var currentDateParts = [dateObj.getFullYear(), dateObj.getMonth() + 1, dateObj.getDay() + 1];
        }
        var daysWithPatients = globalVariables.patientsInCalendar;
        for(var i in daysWithPatients) {
            var parts = daysWithPatients[i].patient_day.split('-'); // Год-месяц-день
            if(currentDateParts[0] == parts[0] && currentDateParts[1] == parts[1]) {
                $(".day" + parseInt(parts[2])).filter(':not(.new)').filter(':not(.old)').addClass('day-with');
            }
        }
    });

    $(document).on('click', '.medcard-history-showlink', function(e) {
        var medcardId = $(this).attr('href').substr(1);
        var date = $(this).text();
        $('#historyPopup .medcardNumber').text('№ ' + medcardId);
        $('#historyPopup .historyDate').text(date);
        $.ajax({
            'url' : '/index.php/doctors/patient/gethistorymedcard',
            'data' : {
                medcardid : medcardId,
                date : date
            },
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == 'true') {
                    // Заполняем медкарту-историю значениями
                    var data = data.data;
                    var form = $('#historyPopup #patient-edit-form');
                    // Сброс формы
                    $(form)[0].reset();
                    $(form).find('input').val('');
                    for(var i = 0; i < data.length; i++) {
                        var element = $(form).find('#f_history_' + data[i].element_id);
                        if(data[i].type == 3) { // Выпадающий список с множественным выбором
                            data[i].value = $.parseJSON(data[i].value);
                        }
                        element.val(data[i].value);
                    }
                    $('#historyPopup').modal({

                    });
                }
            }
        });
    });
});
