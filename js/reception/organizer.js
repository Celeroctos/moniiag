$(document).ready(function() {
	var clickedTimeLi = null;
	var clickedDayLi = null;
	var triggeredByLoad = true; // Это для вызова окна записанного пациента автоматом

    $('.organizer').on('reload', function(e) {
        cleanOrganizier();
      //  $(this).find('.sheduleCont').addClass('no-display');
        $('#doctor-search-submit').trigger('click');
    });

    $('.organizer').on('writePatientWithCard', function(e, beginTime, year, month, day) {
        var params = {
            month : month + 1,
            year : year,
            day : day,
            doctor_id : globalVariables.doctorId,
            mode: 0, // Обычная запись
            time: beginTime,
            card_number: globalVariables.cardNumber
        };

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
                    $('.organizer').trigger('reload');
                } else {

                }
                return;
            }
        });
        return false;
    });

    $('.organizer').on('showPatientData', function(e, patientData, li, year, month, day) {
        if($(clickedTimeLi).prop('id') == $(li).prop('id')) {
			e.stopPropagation();
			return false;
		}
		$(li).addClass('pressed');
        var title = 'Пациент ' + patientData.fio + ', записан на ' + day + '.' + month + '.' + year + ', на ' + patientData.patient_time;
        if(patientData.cardNumber != null) {
            title += ', номер карты ' + patientData.cardNumber;
        }
		
		if(clickedTimeLi != null) {
			$(clickedTimeLi).find('.popover').remove();
		}
		clickedTimeLi = $(li);
		
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
                var unwriteLink = $('<a>').text('Отписать пациента');
                $(unwriteLink).on('click', function() {
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
                });
                return unwriteLink;
            }
        });

        var span = $('<span class="glyphicon glyphicon-remove" title="Закрыть окно"></span>').css({
            marginLeft: '480px',
            position: 'absolute',
            cursor: 'pointer'
        });

        $(span).on('click', function(e) {
            $(li).popover('hide');
            e.stopPropagation();
        });

        $(li).popover('show');
        $(li).find('.popover').css({
            position: 'relative',
            width: '350px'
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
        for(var i = 0; i < 7; i++) {
            var headerTd = $('<td>');
            var d = new Date(year, month, parseInt(day) + i);
            dates.push(d);
            var isToday = (today.getDate() == d.getDate() && today.getFullYear() == d.getFullYear() && today.getMonth() == d.getMonth());
            if(isToday) {
                $(headerTd).addClass('current');
            }

            $(headerTd).html((isToday ? 'Cегодня<br/>' : rusDays[d.getDay()] + '<br/>') + ' ' + (parseInt(day) + i) + ' ' + globalVariables.months[d.getMonth()]);
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

                        (function(i, li, counter, dayData) {
                            $(li).on('click', function(e) {
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

                                $.ajax({
                                    'url' : '/index.php/doctors/shedule/getpatientslistbydate/?doctorid=' + doctorId + '&year=' + date.getFullYear() + '&month=' + (date.getMonth() + 1) + '&day=' + date.getDate(),
                                    'cache' : false,
                                    'dataType' : 'json',
                                    'type' : 'GET',
                                    'success' : function(data, textStatus, jqXHR) {
                                        if(data.success == 'true') {
											if($(clickedDayLi).prop('id') == $(li).prop('id')) {
												$(li).popover({
													animation: true,
													html: true,
													placement: 'bottom',
													title: 'Расписание врача ' + fio + ' на ' + date.getDate() + '.' + (date.getMonth() + 1) + '.' + date.getFullYear(),
													delay: {
														show: 300,
														hide: 300
													},
													content: function() {
														var ulInPopover = $('<ul>').addClass('patientList');
														for(var j = 0; j < data.data.length; j++) {
															var li = $('<li>').css({
																'cursor' : 'pointer'
															}).html(
																data.data[j].timeBegin + ' - ' + data.data[j].timeEnd
															);

															if(data.data[j].cardNumber != null || data.data[j].id != null || $.trim(data.data[j].fio) != '') {
                                                                $(li).addClass('withPatient');
																if(data.data[j].id != null) {
																	$(li).prop('id', 'i' + data.data[j].id);
																}
															} else {
																$(li).prop('title', 'Записать пациента')
															}
															$(li).on('mouseover', function(e) {
															   $(this).addClass('pressed');
															});
															$(li).on('mouseout', function(e) {
																$(this).removeClass('pressed');
															});

															if(!$(li).hasClass('withPatient')) {
																(function(timeBegin) {
																	$(li).on('click', function() {
																		// Если есть попап для записи пациента, то его нужно показать
																		$(this).addClass('pressed');
                                                                        globalVariables.patientTime = timeBegin;
																		if($('#patientDataPopup').length > 0) {
																			$('#patientDataPopup').modal({});
																		} else { // Должны быть данные для записи пациента
																			$('.organizer').trigger('writePatientWithCard', [timeBegin, date.getFullYear(), date.getMonth(), date.getDate()]);

																		}
																	});
																})(data.data[j].timeBegin)
															} else {
																(function(patientData, li) {
																	$(li).on('click', function(e) {
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
											}
                                        } else {

                                        }
                                        return;
                                    }
                                });
                            });
                        })(i, li, counter, dayData);
                    } else {
                        $(li).addClass('not-aviable');
                    }
                }

                $(li).appendTo(ulCont);

                $(li).find('popover').css({
                    width: '600px'
                });

                if(globalVariables.hasOwnProperty('greetingId') && counter == 0 && triggeredByLoad) {
                    $(li).trigger('click');
                }

				counter++;
            }

            $(daysListCont).append(ulCont);
            // Берём последний UL в daysListCont и проставляем всем элементам Li внутри в высоту, равную высоте ячейки со врачом
            $(daysListCont).find('ul:last li').height(lengthOfRow);
        }
        $('.organizer').find('.sheduleCont').removeClass('no-display');
    });
});