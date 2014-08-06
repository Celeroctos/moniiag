$(document).ready(function() {
    var clickedTimeLi = null;
    var clickedDayLi = null;
    var triggeredByLoad = true; // Это для вызова окна записанного пациента автоматом
    var pregnantGreetingsTimeLimit = null; // Настройка по предельному времени приёма для беременных
    var primaryGreetingsTimeLimit = null; // Настройка по предельному времени приёма для первичных приёмов
    var callCenterGreetingsLimit = null; // Количество человек (предельное) по колл-центру
    var firstDayIsToDay = false;

    $('.organizer').on('returnDate', function(e) {
        prevBeginDate(false);
    });

    $('.organizer').on('resetClickedTime', function(e) {
        $(clickedTimeLi).removeClass('withPatient-pressed pressed');
        clickedTimeLi = null;
    });

    $('.organizer').on('resetClickedDay', function(e) {
        clickedDayLi = null;
    });

    $('.organizer').on('resetTriggerByLoad', function(e) {
        triggeredByLoad = false;
    });

    $('.organizer').on('reload', function(e) {
        cleanOrganizier();
        $('.organizer').trigger('resetClickedTime');
        $('.organizer').trigger('resetClickedDay');
        //  $(this).find('.sheduleCont').addClass('no-display');
        $('#doctor-search-submit').trigger('click');
    });

   /* $('.organizer').on('changeTriggerByLoad', function(e) {
        triggeredByLoad = true;
    }); */

    $('.organizer').on('writePatientWithCard', function(e, beginTime, year, month, day, li) {
        var params = {
            month : month + 1,
            year : year,
            day : day,
            doctor_id : globalVariables.doctorId,
            mode: 0, // Обычная запись
            time: beginTime,
            card_number: globalVariables.cardNumber
        };

        if(globalVariables.hasOwnProperty('isWaitingLine') && globalVariables.isWaitingLine == 1) {
            params.order_number = $(li).prop('id').substr(1);
        }

        if(globalVariables.hasOwnProperty('cancelledGreeting')) {
            params.cancelledGreetingId = globalVariables.cancelledGreeting;
        }

        $.ajax({
            'url' : '/index.php/doctors/shedule/writepatient',
            'data' : params,
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == 'true') {
                    $('#successPopup p').text(data.data);
                    $('#successPopup').modal({

                    });

                    // Перезагружаем календарь
                    if(!globalVariables.hasOwnProperty('isWaitingLine') || globalVariables.isWaitingLine != 1) {
                        globalVariables.greetingId = data.greetingId;
                        prevBeginDate();
                    }
                    alert("0" + triggeredByLoad);
                    $('.organizer').trigger('resetTriggerByLoad');
                    alert("2" + triggeredByLoad);
                   // $('.organizer').trigger('changeTriggerByLoad');
                    $('.organizer').trigger('reload');
                    $('.organizer').trigger('resetClickedTime');
                    $('.organizer').trigger('resetClickedDay');
                } else {
                    // Удаляем предыдущие ошибки
                    $('#errorPopup .modal-body .row p').remove();
                    // Вставляем новые
                    $('#errorPopup .modal-body .row').append("<p>" + data.error + "</p>");
                    $('#errorPopup').modal({
                    });
                }
                return;
            }
        });
        return false;
    });

    function isWaitingLineMode()
    {
        result = false;
        if (globalVariables.hasOwnProperty('isWaitingLine') && globalVariables.isWaitingLine == 1)
        {
            result = true;
        }
        return result;
    }


    $('.organizer').on('showPatientData', function(e, patientData, li, year, month, day) {
        var title = 'Пациент ' + patientData.fio + ', записан на ' + day + '.' + month + '.' + year + ', на ' + patientData.patient_time;
        if(patientData.cardNumber != null) {
            title += ', номер карты ' + patientData.cardNumber;
        }

        $(li).popover({
            animation: true,
            html: true,
            placement: 'bottom',
            title: title,
            delay: {
                show: 300,
                hide: 300
            },
            container: $(li),
            content: function() {
                if(patientData.comment == null) {
                    patientData.comment = '';
                }
                var commentBlock = $('<div>').html('<span class="text-danger">Комментарий: </span>' + patientData.comment);
                var unwriteBlock = $('<div>');
                var unwriteLink = $('<a>').text('Отписать пациента');
                $(unwriteLink).on('click', function() {
                    if(window.confirm('Вы действительно хотите отменить приём?')) {
                        var params = {
                            id: $(li).prop('id').substr(1)
                        };
                        // Отписать пациента
                        $.ajax({
                            'url' : '/index.php/doctors/shedule/unwritepatient',
                            'data' : params,
                            'cache' : false,
                            'dataType' : 'json',
                            'type' : 'GET',
                            'success' : function(data, textStatus, jqXHR) {
                                if(data.success == 'true') {
                                    $('#successPopup p').text(data.data);
                                    $('#successPopup').modal({
                                    });
                                    $('.organizer').trigger('reload');
                                } else {

                                }
                                return;
                            }
                        });
                    }
                });
                $(commentBlock).append($(unwriteBlock).append(unwriteLink));
                return commentBlock;
            }
        });

        var span = $('<span class="glyphicon glyphicon-remove" title="Закрыть окно"></span>').css({
            marginLeft: '330px',
            position: 'absolute',
            cursor: 'pointer'
        });

        $(span).on('click', function(e) {
            //$(li).popover('hide');
            $(li).popover('destroy');
            $('.organizer').trigger('resetClickedTime');
            $(li).removeClass('withPatient-pressed');
            e.stopPropagation();
        });

        // Перед этим вызовом popover('show') надо вызвать destroy для всех предыдущих поповеров
        // ------------------>
        //$($('.popover').parents()[0]).popover('destroy');

        //$($(li).parents('ul.patientList').find('.popover').parents()[0]).popover('destroy');
        // ------------------>
        $(li).popover('show');
        $(li).find('.popover span.glyphicon').remove();
        $(li).find('.popover').css({
            minWidth: '350px'
        }).append(span);
    });

    // Чистим, что осталось с предыдущих времён
    function cleanOrganizier()
    {
        var doctorList = $(document).find('.sheduleCont .doctorList');
        var daysListCont = $(document).find('.sheduleCont .daysListCont');
        var headerCont = $(document).find('.sheduleCont .headerCont2');

        $(headerCont).find('td').remove();
        $(doctorList).find('tr').remove();
        $(daysListCont).find('li').remove();
    }


    $('.organizer').on('showShedule', function(e, data, status, response) {
        $('.organizerNav').removeClass('no-display');
        // Проверка на то, что кто-то вообще есть в выборке с расписанием
        var isIssetAnybody = false;
        for(var i = 0; i < data.data.length; i++) {
            if(data.data[i].shedule.length != 0) {
                isIssetAnybody = true;
                break;
            }
        }

        if(!isIssetAnybody) {
            $('#notFoundPopup').modal({});
            return false;
        }

        var year = data.year; // вычисляем текущий год
        var month = data.month - 1; // вычисляем текущий месяц (расхождение с utc в единицу)
        var day = data.day; // вычисляем текущее число


        var doctorList = $(this).find('.doctorList');
        var daysListCont = $(this).find('.daysListCont');
        var headerCont = $(this).find('.headerCont2');

        $(headerCont).find('td').remove();
        $(doctorList).find('tr').remove();
        $(daysListCont).find('li').remove();

        // Заполняем для начала заголовок. Для этого берём начальную дату из ответа с сервера
        var rusDays = ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'];
        var dates = [];
        var today = new Date();
        var wasToday = false;

        firstDayIsToDay = false;
        for(var i = 0; i < 7; i++) {
            var headerTd = $('<td>');
            var d = new Date(year, month, parseInt(day) + i);
            dates.push(d);
            var isToday = (today.getDate() == d.getDate() && today.getFullYear() == d.getFullYear() && today.getMonth() == d.getMonth());
            if(isToday) {
                firstDayIsToDay = true;
                $(headerTd).addClass('current');
                wasToday = true;
            }

            if(i == 6) {
                if(wasToday) {
                    $('.organizerNav .back').addClass('no-display');
                } else {
                    $('.organizerNav .back').removeClass('no-display');
                }
            }

            //$(headerTd).html((isToday ? 'Cегодня<br/>' : rusDays[d.getDay()] + '<br/>') + ' ' + (parseInt(day) + i) + ' ' + globalVariables.months[d.getMonth()]);
            $(headerTd).html((isToday ? 'Cегодня<br/>' : rusDays[d.getDay()] + '<br/>') + ' ' + (d.getDate()) + ' ' + globalVariables.months[d.getMonth()]);

            for(var j = 0; j < data.restDays.length; j++) {
                if(data.restDays[j] == d.getDay()) {
                    $(headerTd).addClass('weekday');
                    break;
                }
            }
/*  && typeof globalVariables.greetingId != 'undefined' && globalVariables.greetingId != null */
            if(d.getDay() == 1) { // Отсчёт в последующие разы начинаем с понедельника
                if(i != 0) { // Если не первый день - првиети к первому дню. Если нет ид расписания
                    globalVariables.beginDate = d.getFullYear() + '-' + (parseInt(d.getMonth()) + 1) + '-' + d.getDate();
                } else {
                    var tempD = new Date(d.getFullYear(), d.getMonth(), d.getDate());
                    tempD.setDate(tempD.getDate() + 7);
                    globalVariables.beginDate = tempD.getFullYear() + '-' + (parseInt(tempD.getMonth()) + 1) + '-' + tempD.getDate();
                }
            }
            $(headerTd).appendTo(headerCont);
        }

        pregnantGreetingsTimeLimit = data.pregnantGreetingsLimit.split(':');
        primaryGreetingsTimeLimit = data.primaryGreetingsLimit.split(':');
        callCenterGreetingsLimit = data.callCenterGreetingsLimit;
        waitingLineTimeWriting = data.waitingLineTimeWriting;
        waitingLineDateWriting = data.waitingLineDateWriting;

        // Заполняем список врачей
        var data = data.data;

        for(var i = 0; i < data.length; i++) {
            // Тех, у кого расписания нет, бессмысленно показывать
            if(data[i].shedule.length == 0) {
                continue;
            }
            // Формируем строку с врачом (ФИО-должность)
            var doctorTr = $('<tr>').append($('<td>').prop('class', 'doctor_cell').html(
                data[i].last_name + ' ' + data[i].first_name + ' ' + data[i].middle_name + '<span>' + data[i].post + '</span>'
                )
            )

            $(doctorList).append(doctorTr);
            var lengthOfRow = $(doctorList).find('tr:last td').height();

            // Формируем строку с расписанием
            var ulCont = $('<ul>').addClass('daysList');
            var counter = 0;
            for(var j in data[i].shedule) {
                var dayData = data[i].shedule[j];
                var li = $('<li>');
                $(li).css({
                    height: $(doctorTr).css('height'),
                    marginBottom: '2px',
                    marginTop: '1px'
                });

                if(!dayData.worked) {
                    if(dayData.restDay != false) {
                        $(li).addClass('not-aviable');
                    } else {
                        if(!dayData.allowForWrite) {
                            $(li).addClass('not-aviable');
                        }
                    }
                } else {
                    if(dayData.allowForWrite) {
                        // Рабочие дни
                        var beginTime = dayData.beginTime.substr(0, dayData.beginTime.lastIndexOf(':'));
                        var endTime = dayData.endTime.substr(0, dayData.endTime.lastIndexOf(':'));
                        if(dayData.secondaryGreetings == null) { // Странный фикс: иногда возвращается null
                            dayData.secondaryGreetings = 0;
                        }
                        if(dayData.primaryGreetings == null) { // Странный фикс: иногда возвращается null
                            dayData.primaryGreetings = 0;
                        }

                        // В том случае, если это не живая очередь, нужно писать время и кол-во первичных-вторичных приёмов. В ином случае, - кол-во пациентов / кол-во пациентов для живой очереди
                        if(globalVariables.hasOwnProperty('isWaitingLine') && globalVariables.isWaitingLine == 1) {
                            $(li).html(beginTime + ' - ' + endTime + '<br />' + dayData.numPatients + '/' + globalVariables.maxInWaitingLine);
                        } else {
                            $(li).html(beginTime + ' - ' + endTime + '<br />' + dayData.primaryGreetings + '/' + (parseInt(dayData.secondaryGreetings) + parseInt(dayData.primaryGreetings)));
                        }

                        if(dayData.numPatients == 0) {
                            $(li).addClass('empty');
                        }
                        if(dayData.numPatients > 0 && dayData.numPatients < dayData.quote) {
                            $(li).addClass('notfull');
                        }
                        if(dayData.numPatients >= dayData.quote) {
                            $(li).addClass('full');
                        }

                        (function(i, li, counter, dayData) {
                            $(li).on('click', function(e) {
                                if($(clickedDayLi).prop('id') == $(li).prop('id')) {
                                    $(li).removeClass('empty-pressed notfull-pressed full-pressed');
                                    e.stopPropagation();
                                    return false;
                                }
                                $('.organizer').on('resetClickedTime');
                                $('.organizer').on('resetClickedDay');
                                clickedDayLi = li;
                                var doctorId = data[i].id;
                                globalVariables.doctorId = doctorId;
                                globalVariables.patientTime = dayData.beginTime;
                                var fio = data[i].last_name + ' ' + data[i].first_name + ' ' + data[i].middle_name;
                                $(daysListCont).find('li').removeClass('empty-pressed notfull-pressed full-pressed');
                                $(daysListCont).find('.popover').remove();
                                if($(li).hasClass('empty')) {
                                    $(li).addClass('empty-pressed');
                                } else if($(li).hasClass('notfull')) {
                                    $(li).addClass('notfull-pressed');
                                } else if($(li).hasClass('full')) {
                                    $(li).addClass('full-pressed');
                                }

                                var date = dates[counter];

                                globalVariables.month = date.getMonth();
                                globalVariables.day = date.getDate();
                                globalVariables.year = date.getFullYear();

                                if(globalVariables.hasOwnProperty('isWaitingLine') && globalVariables.isWaitingLine == 1) {
                                    var params = {
                                        onlywaitingline : 1
                                    };
                                } else {
                                    var params = {};
                                }

                                $.ajax({
                                    'url' : '/index.php/doctors/shedule/getpatientslistbydate/?doctorid=' + doctorId + '&year=' + date.getFullYear() + '&month=' + (date.getMonth() + 1) + '&day=' + date.getDate(),
                                    'cache' : false,
                                    'dataType' : 'json',
                                    'type' : 'GET',
                                    'data' : params,
                                    'success' : function(data, textStatus, jqXHR) {
                                        if(data.success == 'true') {
                                            if($(clickedDayLi).prop('id') == $(li).prop('id')) {
                                                if(globalVariables.hasOwnProperty('isWaitingLine') && globalVariables.isWaitingLine == 1) {
                                                    var title = 'Живая очередь ';
                                                } else {
                                                    var title = 'Расписание ';
                                                }
                                                $(li).popover({
                                                    animation: true,
                                                    html: true,
                                                    placement: 'bottom',
                                                    title: title + 'врача ' + fio + ' на ' + date.getDate() + '.' + (date.getMonth() + 1) + '.' + date.getFullYear(),
                                                    /*delay: {
                                                        show: 300,
                                                        hide: 300
                                                    },*/
                                                    content: function() {
                                                        var ulInPopover = $('<ul>').addClass('patientList');
                                                        for(var j = 0; j < data.data.length; j++) {
                                                            // Живая очередь обрабатывается иначе, чем обычная запись
                                                            if(globalVariables.hasOwnProperty('isWaitingLine') && globalVariables.isWaitingLine == 1) {
                                                                var li = $('<li>').css({
                                                                    'cursor' : 'pointer'
                                                                }).prop({
                                                                    'id' : 'p' + data.data[j].orderNumber
                                                                }).html(data.data[j].orderNumber);

                                                            } else {
                                                                // Проверка настроек и текущего времени: нельзя записать на прошлое время
                                                                if(!isPassedTime(data.data[j].timeBegin, date, true)) {
                                                                    continue;
                                                                }

                                                                var li = $('<li>').css({
                                                                    'cursor' : 'pointer'
                                                                }).prop({
                                                                    'id' : 't' + j
                                                                }).html(
                                                                    data.data[j].timeBegin + ' - ' + data.data[j].timeEnd
                                                                );
                                                            }

                                                            if(data.data[j].cardNumber != null || data.data[j].id != null || $.trim(data.data[j].fio) != '') {
                                                                $(li).addClass('withPatient');
                                                                if(data.data[j].id != null) {
                                                                    $(li).prop('id', 'i' + data.data[j].id);
                                                                }
                                                            } else {
                                                                $(li).prop('title', 'Записать пациента');
                                                            }

                                                            if(!$(li).hasClass('withPatient') && !$(li).hasClass('cantWrite')) {
                                                                (function(timeBegin, li, patientData) {
                                                                    $(li).on('click', function() {
                                                                        // Если есть попап для записи пациента, то его нужно показать
                                                                        if($(clickedTimeLi).prop('id') == $(li).prop('id')) {
                                                                            return false;
                                                                        }

                                                                        if(clickedTimeLi != null) {
                                                                            $(clickedTimeLi).find('.popover').remove();
                                                                            $(clickedTimeLi).removeClass('pressed withPatient-pressed');
                                                                            $(clickedTimeLi).removeClass('pressed');
                                                                        }

                                                                        clickedTimeLi = $(li);
                                                                        $(li).addClass('pressed');

                                                                        globalVariables.patientTime = timeBegin;
                                                                        globalVariables.orderNumber = $(li).prop('id').substr(1);
                                                                        if($('#patientDataPopup').length > 0) {
                                                                            globalVariables.withWindow = 1;
                                                                            if(globalVariables.hasOwnProperty('greetingId') && typeof globalVariables.greetingId != 'undefined') {
                                                                                if(globalVariables.hasOwnProperty('patientData')) {

                                                                                    if(globalVariables.patientData.hasOwnProperty('comment')) {
                                                                                        $('#patientDataPopup #comment').val(globalVariables.patientData.comment);
                                                                                    }
                                                                                    if(globalVariables.patientData.hasOwnProperty('phone')) {
                                                                                        $('#patientDataPopup #phone').val(globalVariables.patientData.phone);
                                                                                    }
                                                                                    if(globalVariables.patientData.hasOwnProperty('lastName')) {
                                                                                        $('#patientDataPopup #lastName').val(globalVariables.patientData.lastName);
                                                                                    }
                                                                                    if(globalVariables.patientData.hasOwnProperty('firstName')) {
                                                                                        $('#patientDataPopup #firstName').val(globalVariables.patientData.firstName);
                                                                                    }
                                                                                    if(globalVariables.patientData.hasOwnProperty('middleName')) {
                                                                                        $('#patientDataPopup #middleName').val(globalVariables.patientData.middleName);
                                                                                    }
                                                                                }
                                                                            }
                                                                            $('#patientDataPopup').modal({});
                                                                        } else { // Должны быть данные для записи пациента
                                                                            var args = [timeBegin, date.getFullYear(), date.getMonth(), date.getDate(), li];
                                                                            $('.organizer').trigger('writePatientWithCard', args);

                                                                        }
                                                                    });
                                                                })(data.data[j].timeBegin, li, data.data[j]);
                                                            } else {
                                                                (function(patientData, li) {
                                                                    $(li).on('click', function(e) {
                                                                        if($(clickedTimeLi).prop('id') == $(li).prop('id')) {
                                                                            e.stopPropagation();
                                                                            return false;
                                                                        }

                                                                        if(clickedTimeLi != null) {
                                                                            $(clickedTimeLi).find('.popover').remove();
                                                                            $(clickedTimeLi).removeClass('withPatient-pressed pressed');
                                                                           // $(clickedTimeLi).removeClass('pressed');
                                                                        }

                                                                        clickedTimeLi = $(li);
                                                                        $(li).addClass('withPatient-pressed');
                                                                        $('.organizer').trigger('showPatientData', [patientData, li, date.getFullYear(), date.getMonth(), date.getDate()]);
                                                                    });
                                                                })(data.data[j], li);
                                                                // Автовызов окна отписи пациента
                                                                if(globalVariables.hasOwnProperty('greetingId') && globalVariables.greetingId == data.data[j].id && triggeredByLoad) {
                                                                    triggeredByLoad = false;
                                                                    $(li).trigger('click');
                                                                }
                                                            }
                                                            $(li).css({
                                                                'cursor' : 'pointer'
                                                            });
                                                            $(li).appendTo(ulInPopover);
                                                        }

                                                        // Ограничение на кол-во приёмов колл-центра
                                                        if(globalVariables.hasOwnProperty('isCallCenter') && globalVariables.isCallCenter == 1 && $(ulInPopover).find('li.withPatient').length >= callCenterGreetingsLimit) {
// Логика неверна: здесь нужно считать количество записанных постфактум
                                                            $(ulInPopover).find('li:not(.withPatient)').addClass('not-aviable').off('click').css({'cursor' : 'default'}).prop('title', 'Превышение квоты записи через Call-Center');
                                                        } else {

                                                        }

                                                        return ulInPopover;
                                                    },
                                                    container: $(li)
                                                });

                                                $(li).popover('show');

                                                var span = $('<span class="glyphicon glyphicon-remove" title="Закрыть окно"></span>').css({
                                                    position: 'absolute',
                                                    cursor: 'pointer',
                                                    left: '480px'
                                                });

                                                $(span).on('click', function(e) {
                                                    //$(li).popover('hide');
                                                    $(li).popover('destroy');
                                                    $(li).removeClass('full-pressed empty-pressed notfull-pressed');
                                                    $('.organizer').trigger('resetClickedDay');
                                                    $('.organizer').trigger('resetClickedTime');
                                                    e.stopPropagation();
                                                    return false;
                                                });

                                                $(li).find('.popover span.glyphicon').remove();
                                                $(li).find('.popover').css({
                                                    'cursor' : 'default',
                                                    'width' : '500px',
                                                    'max-width' : '500px',
                                                    'min-width' : '500px'
                                                }).append(span);

                                                $(li).on('click', '.popover', function(e) {
                                                    return false;
                                                });

                                                $('body, html').animate({
                                                    scrollTop: $(li).find('.popover').css('top')
                                                }, 600);
                                            }
                                        } else {

                                        }
                                        return;
                                    }
                                });
                            });
                        })(i, li, counter, dayData);

                        /*
                        // Де-факто после того, как полностью сформирован список, может получиться так, что в нём ни одного элемента. В первую очередь это выясняется по времени окончания смены
                        if(!isPassedTime(data[i].shedule[j].endTime, dates[counter]) || (counter > 0 && globalVariables.hasOwnProperty('isWaitingLine') && globalVariables.isWaitingLine == 1)) {
                            $(li).removeClass('notfull full empty').addClass('not-aviable').off('click');
                        }
                        */
                        blockThisDay = false;
                        // Прошло ли время приёма у врача
                        if (!isPassedTime(data[i].shedule[j].endTime, dates[counter]))
                        {
                            // Если живая очередь
                            if (isWaitingLineMode())
                            {
                                if (waitingLineTimeWriting==1)
                                {
                                    // Учитываем время (блокируем день)
                                    blockThisDay = true;
                                }
                            }
                            else
                            {
                                blockThisDay = true;
                            }
                        }
                        else
                        {
                            // Время не прошло, но надо проверить - если запись в живую очередь, то надо обработать
                            //     настройку по формированию
                            if (waitingLineDateWriting==1)
                            {
                                // Смотрим - текущее ли число мы обрабатываем. Если нет - надо заблокировать данный день
                                //    Если первый день не сегодняшний или день по счёту больше, чем первый - то блокируем
                                if ((!firstDayIsToDay) || (counter>0))
                                {
                                    blockThisDay = true;
                                }
                            }
                        }
                        if (blockThisDay)
                        {
                            $(li).removeClass('notfull full empty').addClass('not-aviable').off('click');
                        }

                    } else {
                        $(li).addClass('not-aviable');
                    }
                }

                $(li).prop({
                    'id' : 's' + i + '_' + counter
                });
                $(li).appendTo(ulCont);

                $(li).find('popover').css({
                    width: '600px'
                });

                if(globalVariables.hasOwnProperty('greetingId') && globalVariables.hasOwnProperty('greetingDate') && $.trim(globalVariables.greetingDate) != '') {

                    var date = dates[counter];
                    var greetingDataSplitted = globalVariables.greetingDate.split('-');
                    if(date.getFullYear() == parseInt(greetingDataSplitted[0]) && date.getMonth() + 1 == parseInt(greetingDataSplitted[1]) && date.getDate() == parseInt(greetingDataSplitted[2])) {
                        $(li).trigger('click');
                    }
                    triggeredByLoad = false;
                }

                counter++;
            }

            $(daysListCont).append(ulCont);
            // Берём последний UL в daysListCont и проставляем всем элементам Li внутри в высоту, равную высоте ячейки со врачом
            $(daysListCont).find('ul:last li').height(lengthOfRow);
        }
        $('.organizer').find('.sheduleCont').removeClass('no-display');
    });

    globalVariables.resetBeginDate = true;

    $('.organizerNav .back').on('click', function(e) {
        // Нужно поправить дату начала недели
        prevBeginDate(false, 1);
        globalVariables.resetBeginDate = false;
        $('.organizer').trigger('reload');
    });

    function prevBeginDate(onlyOneWeek, dummy) {
        var dateParsed = globalVariables.beginDate.split('-');
        var beginDateDate = new Date(dateParsed[0], parseInt(dateParsed[1]) - 1, dateParsed[2]);
        // Вычитаем из даты 6, чтобы попасть на предыдущую неделю
        if(!onlyOneWeek) {
            beginDateDate.setDate(beginDateDate.getDate() - 7);
        }
        var counter = 0;
        while(true) {
            if(beginDateDate.getDay() == 1 && ((dummy && counter != 0 ) || !dummy)) { // Ищем понедельник предыдущей недели
                break;
            }
            beginDateDate.setDate(beginDateDate.getDate() - 1);
            counter++;
        }

        var today = new Date();
        if(today.getTime() <= beginDateDate.getTime()) {
            globalVariables.beginDate = beginDateDate.getFullYear() + '-' + (beginDateDate.getMonth() + 1) + '-' + beginDateDate.getDate();
        } else {
            globalVariables.beginDate = null;
        }
    }

    $('.organizerNav .forward').on('click', function(e) {
        globalVariables.resetBeginDate = false;
        $('.organizer').trigger('reload');
    });

    $('#patientDataPopup').on('hidden.bs.modal', function(e) {
        $('.organizer').trigger('resetClickedTime');
    });

    function isPassedTime(time, date, isFull) {
        var now = new Date();
        var splitTime = time.split(':');
        if(now.getFullYear() == date.getFullYear() && now.getMonth() == date.getMonth() && now.getDate() == date.getDate()) {
            if(now.getTime() > (new Date(date.getFullYear(), date.getMonth(), date.getDate(), parseInt(splitTime[0]), parseInt(splitTime[1]))).getTime()) {
                return false;
            }
        }

        if(isFull) { // Полная проверка
            // Теперь смотрим на выбранные фильтры..
            if($('#greetingType').val() == 0) { // Первичный приём
                // От беременности
                // Дальше играет роль только время. Поэтому оттолкнёмся от текущей даты даже
                var now1 = new Date(now.getFullYear(), now.getMonth(), now.getDate(), parseInt(primaryGreetingsTimeLimit[0]), parseInt(primaryGreetingsTimeLimit[1]));
                var now2 = new Date(now.getFullYear(), now.getMonth(), now.getDate(), parseInt(splitTime[0]), parseInt(splitTime[0]));
                if(now1.getTime() < now2.getTime()) {
                    return false;
                }
            }
            if($('#greetingType').val() == 2) { // Вторичный приём
                if($('#canPregnant').val() == 1) { // Беременная
                    var now1 = new Date(now.getFullYear(), now.getMonth(), now.getDate(), parseInt(pregnantGreetingsTimeLimit[0]), parseInt(pregnantGreetingsTimeLimit[1]));
                    var now2 = new Date(now.getFullYear(), now.getMonth(), now.getDate(), parseInt(splitTime[0]), parseInt(splitTime[0]));
                    if(now1.getTime() < now2.getTime()) {
                        return false;
                    }
                }
            }
        }

        return true;
    }
});