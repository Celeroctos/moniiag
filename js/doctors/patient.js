$(document).ready(function() {
    var numCalls = 0; // Одна или две формы вызвались. Делается для того, чтобы не запускать печать два раза
    // Редактирование медкарты
    $("#patient-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового кабинета, перезагружаем jqGrid
            $('#successEditPopup').modal({

            });
            if(isThisPrint) {
                if($(".submitEditPatient").length - 1 == numCalls) {
                    // Сбрасываем режим на дефолт
                    isThisPrint = false;
                    numCalls = 0;
                    $('.activeGreeting .print-greeting-link').trigger('print');
                } else {
                    ++numCalls;
                }
            }
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

    $('.print-greeting-link').on('click', function(e) {
        $('#noticePopup').modal({});
    });

    var isThisPrint = false;
    // После закрытия окна начинать сохранение медкарты и печать листа приёма
    $('#noticePopup').on('hidden.bs.modal', function(e) {
        isThisPrint = true;
        $('.submitEditPatient input').trigger('click');
    });

    $('#successEditPopup').on('show.bs.modal', function(e) {
        // Если это режим печати, то показывать окно успешности редактирования не надо
        if(isThisPrint) {
            return false;
        }
    });

    $('#successEditPopup').on('hidden.bs.modal', function(e) {
        if(!isThisPrint) {
            $('#printPopup').modal({});
        }
    });

    $('#printPopup .btn-success').on('click', function(e) {
        $('.activeGreeting .print-greeting-link').trigger('print');
        isThisPrint = false;
    });

    // Печать листа приёма, само действие
    $('.print-greeting-link').on('print', function(e) {
        var id = $(this).attr('href').substr(1);
        var printWin = window.open('/index.php/doctors/print/printgreeting/?greetingid=' + id,'','width=800,height=600,menubar=no,location=no,resizable=no,scrollbars=yes,status=no');
        printWin.focus();
        return false;
    });

    // Сохранение диагнозов
    $('#submitDiagnosis').on('click', function(e) {
        var choosedPrimary = $.fn['primaryDiagnosisChooser'].getChoosed();
        var choosedSecondary = $.fn['secondaryDiagnosisChooser'].getChoosed();

        var primaryIds = [];
        var secondaryIds = [];
        for(var i = 0; i < choosedPrimary.length; i++) {
            primaryIds.push(choosedPrimary[i].id);
        }
        for(var i = 0; i < choosedSecondary.length; i++) {
            secondaryIds.push(choosedSecondary[i].id);
        }

        $.ajax({
            'url' : '/index.php/doctors/patient/savediagnosis',
            'data' : {
                'primary' : $.toJSON(primaryIds),
                'secondary' : $.toJSON(secondaryIds),
                'note' : $('#diagnosisNote').val(),
                'greeting_id' : $('#greetingId').val()
            },
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == true) {
                    $('#successDiagnosisPopup').modal({});
                }
            }
        });
    });

    globalVariables.onlyLikes = 0;
    // Флаг любимых и общих диагнозов
    $('#onlyLikeDiagnosis').click(function(e) {
        if(!$(this).prop('checked')) {
            globalVariables.onlyLikes = 0;
        } else {
            globalVariables.onlyLikes = 1;
        }
    });


    // Это для того, чтобы занести в диагнозы всё то, что было при загрузке страницы: первичные
    (function(choosers) {
        for(var j = 0; j < choosers.length; j++) {
            var chooser = $('#' + choosers[j]);
            if($(chooser).length > 0) {
                var preChoosed = $(chooser).find('.choosed span.item');
                for(var i = 0; i < preChoosed.length; i++) {
                    var id = $(preChoosed[i]).prop('id').substr(1);
                    $.fn[choosers[j]].addChoosed($('<li>').prop('id', 'r' + id).text($(preChoosed[i]).find('span').html()), {
                        'id' : id,
                        'description' : $(preChoosed[i]).find('span').html()
                    }, 1);
                }
            }
        }
    })(['primaryDiagnosisChooser', 'secondaryDiagnosisChooser']);
});

function getOnlyLikes() {
    return globalVariables.onlyLikes;
}
