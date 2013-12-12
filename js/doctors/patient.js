$(document).ready(function() {
    // Редактирование медкарты
    $("#patient-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового кабинета, перезагружаем jqGrid
            $('#successEditPopup').modal({

            });
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
});
