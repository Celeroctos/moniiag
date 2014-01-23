$(document).ready(function() {    
    $('.calendar').on('showShedule', function(e, data) {
        var tbody = $(".calendar tbody");
        // Удаляем всё ненужное
        $(tbody).find("tr").remove();
        var isFilled = $(tbody).find("tr").size() > 0;

        var d = new Date(); // определяем текущую дату
        var year = data.data.year; // вычисляем текущий год
        var month = data.data.month - 1; // вычисляем текущий месяц (расхождение с utc в единицу)
        var day = data.data.day; // вычисляем текущее число
        var prevMonth = data.data.month - 2; // предыдущий месяц

        // Сохраняем для междускриптья
        globalVariables.day = day;
        globalVariables.month = month;
        globalVariables.year = year;

        var firstDay = new Date(year, month, 1); // устанавливаем дату первого числа текущего месяца
        var firstWday = firstDay.getDay(); // из нее вычисляем день недели первого числа текущего месяца

        var firstPrevDay = new Date(year, prevMonth, 1);
        var numPrevDays = 32 - new Date(year, prevMonth, 32).getDate();

        var doctorId = data.data.doctorId;

        // Сначала наполняем первую неделю
        var tr = $("<tr>");
        if(firstWday == 0) {
            firstWday = 7;
        }

        var beginPrevDay = numPrevDays - firstWday + 2; // День из предыдущего месяца, который надо нарисовать
        var i = 1;
        for(; i < firstWday; i++) {
            if(!isFilled) {
                $(tr).append($('<td>').addClass('text-muted').text(beginPrevDay));
            }
            beginPrevDay++;
        }
        if((i - 1) % 7 == 0 && i != 0) {
            $(tr).appendTo($(tbody));
            tr = $('<tr>');
        }

        // Строим основной месяц
        var calendar = data.data.calendar;
        for(; i < firstWday + calendar.length; i++) {
            if(!isFilled) {
                var td = $('<td>').text((i - firstWday) + 1);
                $(tr).append($(td));
            }
            // Красим ячейки
            var dayData = calendar[i - firstWday];
            // Выходные
            if(!dayData.worked) {
                if(dayData.restDay != false) {
                    $(td).addClass('orange-block');
                }
            } else {
                if(dayData.allowForWrite) {
                    // Рабочие дни
                    if(dayData.numPatients == 0) {
                        $(td).addClass('lightgreen-block')
                    }
                    if(dayData.numPatients > 0 && dayData.numPatients < dayData.quote) {
                        $(td).addClass('yellow-block')
                    }
                    if(dayData.numPatients == dayData.quote) {
                        $(td).addClass('red-block')
                    }
                } else {
                    $(td).addClass('not-aviable-block');
                }
            }

            if(i % 7 == 0) {
                $(tr).appendTo($(tbody));
                tr = $('<tr>');
            }
        }

        // Строим добавку следующего месяца
        var futureDays = 1;
        for(; i % 7 != 1; i++) {
            $(tr).append($('<td>').addClass('text-muted').text(futureDays));
            futureDays++;
        }

        $(tr).appendTo($(tbody));

        
        // Добавляем подсказку для нерабочих дней "Этот день недоступен для записи"
        var HintBody = $('<div>');
        HintBody.addClass('busy-shedule-hint');
        HintBody.addClass('no-display');
        HintBody.text("На этот день запись на приём к данному врачу недоступна");
        HintBody.appendTo($('body'));
        
        // При событии mousemove для ячейки недоступрно дня - показываем подсказку, что день недоступен
        $('.calendar tbody').find('td.not-aviable-block').on('mousemove',function(pos)
       
             {
                // Меняем координату сообщения
                $(".busy-shedule-hint").css('left',(pos.pageX+5)+'px')
                    .css('top',(pos.pageY+5)+'px');
                    
                // Делаем сообщение видимым
                $(".busy-shedule-hint").removeClass('no-display');
                           
             });
        
        
            // На выходе из ячейки недоступного дня - прячем подсказку
             $('.calendar tbody').find('td.not-aviable-block').on('mouseout',function(pos)
             {
                $(".busy-shedule-hint").addClass('no-display');
             });
        
        // Получение списка пациентов по дате
        $('.calendar tbody').find('td.yellow-block, td.lightgreen-block, td.lightred-block').on('click', function(e) {
            var day = $(this).text();
            globalVariables.clickedTd = $(this);
            $.ajax({
                'url' : '/index.php/doctors/shedule/getpatientslistbydate/?doctorid=' + doctorId + '&year=' + year + '&month=' + (month + 1) + '&day=' + day,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $('#sheduleByBusy').trigger('showBusy', [data, textStatus, jqXHR, doctorId, year, month, day]);
                    } else {

                    }
                    return;
                }
            });
        });
    });

    $('#showPrevMonth').click(function(e) {
        if(globalVariables.month - 1 < 0) {
            globalVariables.clickedLink.trigger('click', [11, parseInt(globalVariables.year) - 1]);
        } else {
            globalVariables.clickedLink.trigger('click', [globalVariables.month - 1, globalVariables.year]);
        }
    });

    $('#showNextMonth').click(function(e) {
        if(globalVariables.month + 1 > 11) {
            globalVariables.clickedLink.trigger('click', [0, parseInt(globalVariables.year) + 1]);
        } else {
            globalVariables.clickedLink.trigger('click', [globalVariables.month + 1, globalVariables.year]);
        }
    });
});