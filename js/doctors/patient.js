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

    $("#date-cont").on('changeDate', function(e) {
        $('#filterDate').val(e.date.getFullYear() + '-' + (e.date.getMonth() + 1) + '-' + e.date.getDate());
        $('#change-date-form').submit();
    });

    $("#date-cont").trigger("refresh");

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
            if(parseInt(currentDateParts[0]) == parseInt(parts[0]) && parseInt(currentDateParts[1]) == parseInt(parts[1])) {
                $(".day" + parseInt(parts[2])).filter(':not(.new)').filter(':not(.old)').addClass('day-with');
            }
        }
    });
    $('#date-cont').trigger('refresh');

    $(document).on('click', '.medcard-history-showlink', function(e) {
        $(this).parents('.accordion-inner:eq(0)').find('.active').removeClass('active');
        $(this).parent().addClass('active');
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
                console.log(data);
                if(data.success == 'true') {
                    // Заполняем медкарту-историю значениями
                    var data = data.data;
                   /* var form = $('#historyPopup #patient-edit-form');
                    // Сброс формы
                    $(form)[0].reset();
                    $(form).find('input').val('');
                    for(var i = 0; i < data.length; i++) {
                        var element = $(form).find('#f_history_' + data[i].element_id);
                        if(data[i].type == 3) { // Выпадающий список с множественным выбором
                            data[i].value = $.parseJSON(data[i].value);
                        }
                        element.val(data[i].value);
                    }*/
                    $('#historyPopup .modal-body .row').html(data);
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


    // Просмотр медкарты в попапе
    $(document).on('click', '.editMedcard', function(e) {
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
                    $('#errorSearchPopup .modal-body .row').append('<p>' + data.data + '</p>')
                    $('#errorSearchPopup').modal({

                    });
                }
                return;
            }
        });
        return false;
    });

    // Запрет редактирования карты
    $('#patient-medcard-edit-form .modal-body').find('input, select, button').prop('disabled', true);
    $('.date-control .input-group-addon').remove();

    // Здесь будут храниться ID клонов элементов
    var clones = {

    };

    // Клонирование элементов
    /* Клоны считаются, как clone_xx_yy, где xx - ID аккордеона, yy - порядковый номер клона */
    $(document).on('click', '.accordion-clone-btn', function(e) {
        var prKey = $(this).find('span.pr-key').text();
        var accParent = $(this).parents('.accordion')[0];
        var accClone = $(accParent).clone();

        // Теперь нужно отклонировать элемент. Для этого мы подадим запрос, результатом которого станет категория (кусок дерева)
        $.ajax({
            'url' : '/index.php/doctors/patient/cloneelement',
            'data' : {
                'pr_key' : prKey
            },
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == true) {
                    var toggle = $(accParent).find('.accordion-toggle');
                    var body = $(accParent).find('.accordion-body');

                    if(!clones.hasOwnProperty($(accParent).prop('id'))) {
                        clones[$(accParent).prop('id')] = 1;
                    } else {
                        clones[$(accParent).prop('id')]++;
                    }

                    var accId = $(accParent).prop('id');
                    var accNumberId = accId.substr(accId.lastIndexOf('_') + 1);
                    var idCloneCount = clones[$(accParent).prop('id')];

                    var toggleDataParent = $(toggle).data()['parent'];
                    var toggleDataHref = $(toggle).prop('href');
                    $(accClone).find('.accordion-clone-btn:eq(0)')
                        .removeClass('accordion-clone-btn')
                        .addClass('accordion-unclone-btn')
                        .find('span.glyphicon-plus')
                        .removeClass('glyphicon-plus')
                        .addClass('glyphicon-minus');

                    $(accClone).find('.accordion-heading button:not(:eq(0))').remove();

                    $(accClone).prop('id', $(accParent).prop('id') + '_clone_' + accNumberId + '_' + idCloneCount);
                    $(accClone).find('.accordion-body:eq(0)').prop('id', 'collapse_clone_' + accNumberId + '_' + idCloneCount);
                    $(accClone).find('.accordion-heading:eq(0)').attr('data-parent', $(accParent).prop('id') + '_clone_' + accNumberId + '_' + idCloneCount);
                    $(accClone).find('.accordion-heading:eq(0) a').attr('href', '#collapse_clone_' + accNumberId + '_' + idCloneCount);

                    // Дальше пробегаемся по всем вложенным в дерево элементам. Ситуация повторяется: переименовываем
                    var inserts = $(accClone).find('.accordion');
                    for(var i = 0; i < inserts.length; i++) {
                        if(!clones.hasOwnProperty($(inserts[i]).prop('id'))) {
                            clones[$(inserts[i]).prop('id')] = 1;
                        } else {
                            clones[$(inserts[i]).prop('id')]++;
                        }

                        accId = $(inserts[i]).prop('id');
                        accNumberId = accId.substr(accId.lastIndexOf('_') + 1);
                        idCloneCount = clones[$(inserts[i]).prop('id')];

                        $(inserts[i]).prop('id', $(inserts[i]).prop('id') + '_clone_' + accNumberId + '_' + idCloneCount);
                        $(inserts[i]).find('.accordion-body:eq(0)').prop('id', 'collapse_clone_' + accNumberId + '_' + idCloneCount);
                        $(inserts[i]).find('.accordion-heading:eq(0)').attr('data-parent', $(accParent).prop('id') + '_clone_' + accNumberId + '_' + idCloneCount);
                        $(inserts[i]).find('.accordion-heading:eq(0) a').attr('href', '#collapse_clone_' + accNumberId + '_' + idCloneCount);
                    }

                    // Ставим кнопке пришедший первичный ключ
                    $(accClone).find('span.pr-key').text(data.data.pk_key);

                    $(accClone).insertAfter($(accParent));
                } else {

                    return;
                }
            }
        });
    });

    // UnКлонирование элементов
    $(document).on('click', '.accordion-unclone-btn', function(e) {
        var accParent = $(this).parents('.accordion')[0];
        var prKey = $(this).find('span.pr-key').text();
        $.ajax({
            'url' : '/index.php/doctors/patient/uncloneelement',
            'data' : {
                'pr_key' : prKey
            },
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == true) {
                    $(accParent).remove();
                }
            }
        });
    });
});

function getOnlyLikes() {
    return globalVariables.onlyLikes;
}
