$(document).ready(function() {
    $('.organizer').on('showShedule', function(e, data, status, response) {
        var year = data.year; // вычисляем текущий год
        var month = data.month - 1; // вычисляем текущий месяц (расхождение с utc в единицу)
        var day = data.day; // вычисляем текущее число

        var doctorList = $(this).find('.doctorList');
        var daysListCont = $(this).find('.daysListCont');
        var headerCont = $(this).find('.headerCont2');

        // Чистим, что осталось с предыдущих времён
        $(headerCont).find('td').remove();
        $(doctorList).find('tr').remove();
        $(daysListCont).find('li').remove();

        // Заполняем для начала заголовок. Для этого берём начальную дату из ответа с сервера
        var rusDays = ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'];
        var dates = [];
        for(var i = 0; i < 7; i++) {
            var headerTd = $('<td>');
            if(i == 0) {
                $(headerTd).addClass('current');
            }

            var d = new Date(year, month, parseInt(day) + i);
            dates.push(d);
            $(headerTd).html((i == 0 ? 'Cегодня<br/>' : rusDays[d.getDay()] + '<br/>') + ' ' + (parseInt(day) + i) + ' ' + globalVariables.months[d.getMonth()]);
            for(var j = 0; j < data.restDays.length; j++) {
                if(data.restDays[j] == d.getDay()) {
                    $(headerTd).addClass('weekday');
                    break;
                }
            }

            $(headerTd).appendTo(headerCont);
        }

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
            // Формируем строку с расписанием
            var ulCont = $('<ul>').addClass('daysList');
            for(var j in data[i].shedule) {
                var dayData = data[i].shedule[j];
                var li = $('<li>');

                if(!dayData.worked) {
                    if(dayData.restDay != false) {
                        $(li).addClass('weekday');
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
                        $(li).html(beginTime + ' - ' + endTime);

                        if(dayData.numPatients == 0) {
                            $(li).addClass('empty');
                        }
                        if(dayData.numPatients > 0 && dayData.numPatients < dayData.quote) {
                            $(li).addClass('notfull');
                        }
                        if(dayData.numPatients >= dayData.quote) {
                            $(li).addClass('full');
                        }

                        (function(i, li, day, month, year, j) {
                            $(li).on('click', function(e) {
                                var doctorId = data[i].id;
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

                                var date = dates[j % 7];
                                $.ajax({
                                    'url' : '/index.php/doctors/shedule/getpatientslistbydate/?doctorid=' + doctorId + '&year=' + date.getFullYear() + '&month=' + (date.getMonth() + 1) + '&day=' + date.getDate(),
                                    'cache' : false,
                                    'dataType' : 'json',
                                    'type' : 'GET',
                                    'success' : function(data, textStatus, jqXHR) {
                                        if(data.success == 'true') {
                                            $(li).popover({
                                                animation: true,
                                                html: true,
                                                placement: 'left',
                                                title: 'Расписание врача ' + fio + ' на ' + date.getDate() + '.' + (date.getMonth() + 1) + '.' + date.getFullYear(),
                                                delay: {
                                                    show: 300,
                                                    hide: 300
                                                },
                                                content: function() {
                                                    var ulInPopover = $('<ul>').addClass('patientList');
                                                    for(var j = 0; j < data.data.length; j++) {
                                                        var li = $('<li>').html(
                                                            data.data[j].timeBegin + ' - ' + data.data[j].timeEnd
                                                        );
                                                        if(data.data[j].cardNumber != null || data.data[j].id == null || $.trim(data.data[j].fio == '')) {
                                                            $(li).addClass('withPatient');
                                                        }
                                                        $(li).on('click', function() {

                                                        }).css({
                                                            'cursor' : 'pointer'
                                                        });
                                                        $(li).appendTo(ulInPopover);
                                                    }
                                                    return ulInPopover;
                                                },
                                                container: $(li)
                                            });

                                            $(li).popover('show');
                                            $(li).find('.popover').css({
                                                'cursor' : 'default',
                                                'width' : '500px',
                                                'max-width' : '500px'
                                            });

                                            $(li).on('click', '.popover', function(e) {
                                                return false;
                                            });
                                        } else {

                                        }
                                        return;
                                    }
                                });
                            });
                        })(i, li, day, month, year, j);

                    } else {
                        $(li).addClass('not-aviable');
                    }
                }

                $(li).appendTo(ulCont);

                $(li).find('popover').css({
                    width: '600px'
                });
            }
            $(daysListCont).append(ulCont);
        }
    });
});