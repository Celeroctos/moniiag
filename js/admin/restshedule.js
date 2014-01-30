$(document).ready(function() {
    $('.calendarTable').on('showCalendar', function(e, restDays, row, col, year, holidays) {
        var holidaysArr = [];
        for(var i in holidays) {
            holidaysArr.push(i);
        }
        var months = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август','Сентябрь','Октябрь','Ноябрь','Декабрь'];
        var daysOfWeek = ['Пн','Вт','Ср','Чт','Пт','Сб','Вс'];
        var tdNum = 4 * row + (col + 1) - 1;
        var tdForTable = $(".calendarTable tbody td.calendarTd:eq(" + tdNum + ")");
        $(tdForTable).find('h6').text(months[tdNum]);
        var tableForCalendar = $('<table>');
        var theader = $('<thead>');
        for(var i = 0; i < 7; i++) {
            $(theader).append($('<td>').text(daysOfWeek[i]));
        }

        // Смотрим, есть ли уже проставленные дни в календаре
        var restDaysCurrent = [];
        if(typeof restDays[tdNum] != 'undefined') {
            restDaysCurrent = restDays[tdNum];
        }

        var tbody = $('<tbody>');
        $(tableForCalendar).append(theader, tbody);

        var d = new Date(year, tdNum, 1); // определяем текущую дату
        var year = d.getFullYear(); // вычисляем текущий год
        var month = d.getMonth(); // вычисляем текущий месяц (расхождение с utc в единицу)
        var prevMonth = month - 2; // предыдущий месяц

        var firstDay = new Date(year, month, 1); // устанавливаем дату первого числа текущего месяца
        var firstWday = firstDay.getDay(); // из нее вычисляем день недели первого числа текущего месяца

        var firstPrevDay = new Date(year, prevMonth, 1);
        var numPrevDays = 32 - new Date(year, prevMonth, 32).getDate();
        var numDays = 32 - new Date(year, month, 32).getDate();

        // Сначала наполняем первую неделю
        var tr = $("<tr>");
        if(firstWday == 0) {
            firstWday = 7;
        }

        var beginPrevDay = numPrevDays - firstWday + 2; // День из предыдущего месяца, который надо нарисовать
        var i = 1;
        for(; i < firstWday; i++) {
            $(tr).append($('<td>').addClass('not-clicked').text(beginPrevDay));
            beginPrevDay++;
        }
        if((i - 1) % 7 == 0 && i != 0) {
            $(tr).appendTo($(tbody));
            tr = $('<tr>');
        }
        if(month < 10) {
            month = '0' + (month + 1);
        } else {
            month = month + 1;
        }
        // Строим основной месяц
        for(; i < firstWday + numDays; i++) {
            var currentDay = (i - firstWday) + 1;
            var td = $('<td>').text(currentDay);
            if(currentDay < 10) {
                currentDay = '0' + currentDay;
            }
            var id = year + '-' + month + '-' + currentDay;

            $(td).prop({
                'id' : 'd' + id
            });
            for(var j = 0; j < restDaysCurrent.length; j++) {
                var restDayParts = restDaysCurrent[j].date.split(' ');
                if(id == restDayParts[0]) {
                    $(td).addClass('clicked');
                }
            }
            $(tr).append($(td));

            if(i % 7 == 0) {
                $(tr).appendTo($(tbody));
                tr = $('<tr>');
            }
        }

        // Строим добавку следующего месяца
        var futureDays = 1;
        for(; i % 7 != 1; i++) {
            $(tr).append($('<td>').addClass('not-clicked').text(futureDays));
            futureDays++;
        }

        $(tr).appendTo($(tbody));
        // Теперь стабильные выхи
        var trs = $(tbody).find('tr');
        for(var j = 0; j < trs.length; j++) {
            for(var i = 0; i < holidaysArr.length; i++) {
                if(holidaysArr[i] == 0) {
                    holidaysArr[i] = 7;
                }
                $(trs[j]).find('td:eq(' + (holidaysArr[i] - 1) + ')').addClass('rest not-clicked');
            }
        }

        $(tableForCalendar).appendTo(tdForTable);
    });


    $('td.calendarTd').on('click', 'tbody td:not(.not-clicked)', function() {
        if($(this).hasClass('clicked')) {
            $(this).removeClass('clicked');
        } else {
            $(this).addClass('clicked');
        }
    });

    $("#restcalendar-shedule-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            location.reload();
        }
    });

    $('#submitHolidays').on('click', function() {
        var clickedTds = $('.calendarTable td.calendarTd tbody td.clicked');
        var jsonData = [];
        for(var i = 0; i < clickedTds.length; i++) {
            jsonData.push($(clickedTds[i]).prop('id').substr(1));
        }
        $.ajax({
            'url' : '/index.php/admin/shedule/setholidays?dates=' + $.toJSON(jsonData),
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == true) {
                    location.reload();
                } else {

                }
            }
        });
    });
    $('#showNextYear').click(function() {
        var year = parseInt($('.currentYear').text());
        location.href = globalVariables.baseUrl + '/index.php/admin/shedule/viewrest?date=' + (year + 1) + '-01-01';
    });
    $('#showPrevYear').click(function() {
        var year = parseInt($('.currentYear').text());
        location.href = globalVariables.baseUrl + '/index.php/admin/shedule/viewrest?date=' + (year - 1) + '-01-01';
    });
});