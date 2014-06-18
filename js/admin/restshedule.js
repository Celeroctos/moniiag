$(document).ready(function() {
    calendarBuffer = {};
    dateChanged = new Array();
    // Ставим обработчик - По изменению значений чекбоксов выбора
    //    выходных - кликаем на кнопку, которая засабмичивает форму
    $('#weekEndSelector :checkbox').on(
                                       'change',
                                       function(e)
                                       {
                                            $('#submitRestDays').click();
                                       }
                                       );

    function synchronizeHolidays()
    {
        // Подкачать аяксом выходные текущего года
        $.ajax({
            'url' : '/index.php/admin/shedule/getholidays?currentYear=' + globalVariables.currentYear,
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if (data.success==true || data.success=='true')
                {
                    // Раскодировать данные
                    var dates = data.data;
                    // Перебираем дату
                    for (i in dates)
                    {
                        // Если calendarBuffer нет даты - вставляем её в буфер
                        if (calendarBuffer[i]==undefined)
                            calendarBuffer[i] = dates[i];
                    }
                    $('.calendarTable').empty();
                    var calendarAll = $('.calendarTable');
                    for (rowMonths=0;rowMonths<3;rowMonths++)
                    {
                        var oneRow = $('<tr>')
                        $(calendarAll).append(oneRow);

                        for (colMonths=0;colMonths<4;colMonths++)
                        {
                            var oneCell = $('<td>');
                            $(oneCell).addClass('calendarTd');
                            $(oneCell).append( $('<h6>') );
                            oneRow.append(oneCell);

                            $('.calendarTable').trigger('showCalendar',[rowMonths,colMonths, globalVariables.currentYear])
                        }
                    }
                }
            }
        });

    }

    $('.calendarTable').on('refresh', function() {
        refreshExceptionalDays();
    });

    var monthsDrawed = 0;
    $('.calendarTable').on('showCalendar', function(e, row, col, year) {
        holidays = globalVariables.weekEndDays;

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
            tdDayOfWeek = $('<td>');
            $(tdDayOfWeek).text(daysOfWeek[i]);

            // Смотрим - является ли день выходным.
            //   Если является - ставим ему класс "rest"
            for(var j = 0; j < holidaysArr.length; j++) {
                if(holidaysArr[j] == 0) {
                    holidaysArr[j] = 7;
                }
                if (i==holidaysArr[j]-1) {
                    $(tdDayOfWeek).addClass('rest');
                }
            }

            $(theader).append(tdDayOfWeek);
        }

        // Смотрим, есть ли уже проставленные дни в календаре
        var restDaysCurrent = [];

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

        // На кнопочки "Следующий" и "Предыдущий год" выведем цифры year+1 и year-1
        $('#previousYearBtnCaption').text(year-1);
        $('#nextYearBtnCaption').text(year+1);

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
        if(month < 9) {
            month = '0' + (month + 1);
        } else {
            month = month + 1;
        }
        // Строим основной месяц
        for(; i < firstWday + numDays; i++) {
            var currentDay = (i - firstWday) + 1;
            var td = $('<td>').addClass('clickableCell').text(currentDay);
            if(currentDay < 9) {
                currentDay = '0' + currentDay;
            }
            var id = year + '-' + month + '-' + currentDay;

            $(td).prop({
                'id' : 'd' + id
            });
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
                $(trs[j]).find('td:eq(' + (holidaysArr[i] - 1) + ')').not('.not-clicked').addClass('rest not-clicked').removeClass('clickableCell');
            }
        }


        $(tableForCalendar).appendTo(tdForTable);

        monthsDrawed++;
        if (monthsDrawed == 12)
        {
            refreshExceptionalDays();
            monthsDrawed = 0;
        }
    });

    $(document).on('click', 'td.clickableCell', function() {
        //onChangeHolidays();
        onCellClick(this);
        //refreshExceptionalDays();

    });

    $("#restcalendar-shedule-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            location.reload();
        }
    });


    function getSelectedDoctors()
    {
        var result = new Array();
        if ( $('#doctorsSelector .twoColumnListTo option').length!=0 )
        {
            // Перебираем и снимаем value
            doctors = $('#doctorsSelector .twoColumnListTo option');
            for (i=0;i<doctors.length;i++)
            {
                result.push($(doctors[i]).val());
            }
        }
        else
        {
            // Перебираем и снимаем value
            doctors = $('#doctorsSelector .twoColumnListFrom option');
            for (i=0;i<doctors.length;i++)
            {
                result.push($(doctors[i]).val());
            }
        }

        return result;
    }

    var needCancelWeekEnds = false;
    var dateToChange = null;

    function onCellClick(cellClicked)
    {
        var selectedDoctors = getSelectedDoctors();
        // Читаем дату из cellClicked
        var dateString = $(cellClicked).prop('id').substr(1);
        // Читаем врачей которые в дате
        dateToChange = dateString;
        var doctorsInDate = calendarBuffer[dateString];
        if (doctorsInDate==undefined)
            doctorsInDate = new Array();

        indexToDelete = new Array();

        allFound = true;
        oneWasFound = false;
        allTypesIsEqual = true;

        currentType = -1;

        // Пробегаемся по doctorsInDate
        for (i = 0;i<selectedDoctors.length;i++)
        {
            wasFound = false;
            // Перебираем докторов в массиве selectedDoctors
            for (j=0;j<doctorsInDate.length;j++)
            {
                // ищем по совпадению id-шников
                if (doctorsInDate[j]['doctor']==selectedDoctors[i])
                {
                    // Запоминаем индекс - его надо будет удалить
                    indexToDelete.push(j);

                    wasFound = true;

                    // Проверим тип
                    // Если тип равен -1 - берём его из текущего doctorsInDate
                    if (currentType==-1)
                    {
                        currentType = doctorsInDate[j]['type'];
                    }
                    else
                    {
                        // Проверяем - совпадает ли тип
                        if (currentType !=doctorsInDate[j]['type'])
                            allTypesIsEqual = false;
                    }



                    break;
                }
            }
            if (!wasFound)
                allFound = false;
            else
            {
                oneWasFound = true;
            }

        }
        // Если тип currentType и значение переключателя не равны
        //   то сбрасываем флаг "все типы равны"
        if (  parseInt($('input[name=dayType]:checked')[0].value)!=parseInt(currentType) )
        {
            allTypesIsEqual = false;
        }

        // Имеем три состояния
        //     allFound = true, allTypesIsEqual = true - квадратик красный
        //     allFound = true, allTypesIsEqual = false - квадратик жёлтый
        //     allFound = false, oneWasFound = true - квадратик жёлтый
        //     allFound = false, oneWasFound = false - квадратик белый

        needCancelWeekEnds = allFound && allTypesIsEqual;

        // Если устанавливаем рабочие дни, то есть переменная needCancelWeekends = true,
        //   то делать аякс-запрос не нужно - сразу вызываем функцию изменения дня
        if (needCancelWeekEnds)
        {
            changeDay();
            return;
        }

        // Иначе делаем ajax-запрос и выясняем, можно ли
        $.ajax({
            'url': '/index.php/admin/shedule/isgreeting?begin=' + dateString + '&end=' + dateString,
            'cache': false,
            'dataType': 'json',
            'type': 'GET',
            'data': { 'doctorsIds':selectedDoctors }, // Отправляем выделенных докторов
            'async': false,
            'success': function (data, textStatus, jqXHR)
            {

                if (data.success == true) {
                    if (data.data <= 0)
                    {
                        changeDay();
                    }
                    else
                    {
                        printWrittenPatients();

                    }
                }
            }
        });

    }
    $('.calendarTable').on('print', function(e) {
        printYearCalendar();
    });

    function printWrittenPatients()
    {
        // Открываем поп-ап с поциэнтами, которых надо отписать
        //alert('Есть поциенты');

        jQuery("#writtenPatients").jqGrid(
            'setGridParam',
            {
                url: globalVariables.baseUrl +
                    '/index.php/admin/shedule/getwrittenpatients?' +
                    'doctorsIds=' + $.toJSON(getSelectedDoctors()) +
                    '&begin=' + dateToChange +
                    '&end=' + dateToChange,
                page: 1
            }
        );
        jQuery("#writtenPatients").trigger('reloadGrid');
        $('#viewWritedPatient').modal({});

    }


    function changeDay()
    {
        var selectedDoctors = getSelectedDoctors();

        // Читаем врачей которые в дате
        var doctorsInDate = calendarBuffer[dateToChange];
        if (doctorsInDate==undefined)
            doctorsInDate = new Array();

        // Теперь удаляем сконца элементы из массива doctorsInDate
        for (i=0;i<indexToDelete.length;i++)
        {
            doctorsInDate[indexToDelete[i]] = undefined;
        }

        // Копируем массив
        newDoctorsInDate = new Array();
        for (i=0;i<doctorsInDate.length;i++)
        {
            if (doctorsInDate[i]!=undefined)
            {
                newDoctorsInDate.push(doctorsInDate[i]);
            }
        }
        doctorsInDate = newDoctorsInDate;
        // Теперь в том случае, если не всё найдено и не все типы равны - добавляем в doctorsInDate врачей с типами их выходного
        if (! needCancelWeekEnds )
        {
            // Добавляем в doctorsInDate
            if (selectedDoctors.length!=0)
            {
                for(i=0;i<selectedDoctors.length;i++)
                {
                    var newOneDoctor = {};
                    newOneDoctor['doctor'] = selectedDoctors[i];
                    newOneDoctor['type'] = $('input[name=dayType]:checked')[0].value;
                    doctorsInDate.push(newOneDoctor);
                }
            }

        }
        // Возвращаем данные в буффер
        calendarBuffer[dateToChange] = doctorsInDate;
        dateChanged.push(dateToChange);

        // Обновляем
        refreshExceptionalDays();
    }

    // Перебор исключительных дней и перекраска ячеек календаря в соответствии с ними
    function refreshExceptionalDays()
    {
        var types = new Array();

        // Берём врачей, для которые выбраны в селекте
        var selectedDoctors = getSelectedDoctors();

        // Перебираем такие данные в буфере, которые входят в текущий год
        for (oneDate in calendarBuffer)
        {
            // Проверим - надо ли обрабатывать данную запись (если её год равен текущему году из globalVariables)
            var dateComponents = oneDate.split('-');
            if ( parseInt(dateComponents[0]) ==  parseInt(globalVariables.currentYear) )
            {
                // Теперь надо посмотреть - у всех ли выбранных врачей в данный день выходной и
                //      причём выходной одного типа

                // Перебираем всех выбранных врачей
                var types = new Array();
                yellow = false;
                wasOneFound = false;
                for (i=0;i<selectedDoctors.length;i++)
                {
                    // Если хотя бы один врач не найден в списке врачей на дату (или не равен type) - то красим в красный цвет
                    wasFound = false;
                    for (j=0;j<calendarBuffer[oneDate].length;j++)
                    {
                        if (   selectedDoctors[i] ==  calendarBuffer[oneDate][j]['doctor'] )
                        {
                            wasFound= true;
                            wasOneFound = true;
                            types.push(calendarBuffer[oneDate][j]['type']);
                            break;
                        }
                    }
                    if (!wasFound)
                    {
                        yellow = true;
                    }
                }

                // Перебираем типы и проверяем - есть ли исключительные дни разных типов
                currentType = -10000;
                for (i=0;i<types.length;i++)
                {
                    if (currentType<0)
                    {
                        currentType = types[i];
                    }
                    else
                    {
                        if (currentType!=types[i])
                        {
                            yellow = true;
                        }
                    }
                }

                console.log(oneDate);
                console.log(wasOneFound);
                console.log(yellow);

                if (!wasOneFound)
                {
                    $('#d' + oneDate).removeClass('redCell');
                    $('#d' + oneDate).removeClass('yellowCell');

                    $('#d' + oneDate).removeClass('greenCell');
                    $('#d' + oneDate).removeClass('pinkCell');
                    $('#d' + oneDate).removeClass('blueCell');
                }
                else
                {
                    if (yellow)
                    {
                        $('#d' + oneDate).addClass('yellowCell');
                        $('#d' + oneDate).removeClass('redCell');

                        $('#d' + oneDate).removeClass('greenCell');
                        $('#d' + oneDate).removeClass('pinkCell');
                        $('#d' + oneDate).removeClass('blueCell');

                    }
                    else
                    {
                        // ToDo красить разные типы в разные цвета
                        switch (parseInt(types[0]))
                        {
                            case 1:
                                $('#d' + oneDate).addClass('redCell');
                                $('#d' + oneDate).removeClass('greenCell');
                                $('#d' + oneDate).removeClass('pinkCell');
                                $('#d' + oneDate).removeClass('blueCell');

                            break;

                            case 2:
                                $('#d' + oneDate).removeClass('redCell');
                                $('#d' + oneDate).addClass('greenCell');
                                $('#d' + oneDate).removeClass('pinkCell');
                                $('#d' + oneDate).removeClass('blueCell');
                                break;

                            case 3:
                                $('#d' + oneDate).removeClass('redCell');
                                $('#d' + oneDate).removeClass('greenCell');
                                $('#d' + oneDate).addClass('pinkCell');
                                $('#d' + oneDate).removeClass('blueCell');
                                break;


                            case 4:
                                $('#d' + oneDate).removeClass('redCell');
                                $('#d' + oneDate).removeClass('greenCell');
                                $('#d' + oneDate).removeClass('pinkCell');
                                $('#d' + oneDate).addClass('blueCell');
                                break;
                        }
                        $('#d' + oneDate).removeClass('yellowCell');
                    }
                }
            }
        }
    }

    // Вывести на экран календарь
    function printYearCalendar()
    {
        if (globalVariables.currentYear == undefined)
        {
            globalVariables.currentYear = (new Date()).getFullYear();
        }

        // Вот тут надо аяксом подтянуть данные дата->врач->тип даты
        synchronizeHolidays();
    }
    $('.editCalendar').on('click',function(){
        var toSend = {};
        for (d in calendarBuffer)
        {
            // Смотрим - есть ли в списке изменённых
            //   Данная дата

            for (changed in dateChanged)
            {
                // Если d==changed, то добавляем в toSend
                if (d==dateChanged[changed])
                    toSend[d] = calendarBuffer[d];
            }

        }

        // Сохраняем данные в базу
        params = {
            'calendarData': $.toJSON(toSend)
             /* Кодируем в JSON, чтобы не нагружать метод $.ajax. Он ругается */
        };
        $.ajax({
            'url' : '/index.php/admin/shedule/saverestdays',
            'data' : params,
            'cache' : false,
            'dataType' : 'json',
            'type' : 'POST',
            'success' : function(data, textStatus, jqXHR) {

                dateChanged = new Array();
                $('#successPopup').modal({
                });
                return;
            }
        });


    });

    $('#showNextYear').click(function() {
        globalVariables.currentYear = parseInt(globalVariables.currentYear)+1;
        $('.calendarTable').trigger('print');
        refreshExceptionalDays();

    });
    $('#showPrevYear').click(function() {
        globalVariables.currentYear = parseInt(globalVariables.currentYear)-1;
        $('.calendarTable').trigger('print');
        refreshExceptionalDays()
    });
    $('.twoColumnList').on('refresh',
        function(){
           // Сортируем по имени элементы
           //   (и в той и в другой колонке)
            var $elements = $(this).find('.twoColumnListFrom option');
            $elements.sort(function (a, b) {
                var an = $(a).text(),
                    bn = $(b).text();

                if (an && bn) {
                    return an.toUpperCase().localeCompare(bn.toUpperCase());
                }

                return 0;
            });
            $elements.detach().appendTo(  $(this).find('.twoColumnListFrom')   );
            // ------------->
            var $elements = $(this).find('.twoColumnListTo option');
            $elements.sort(function (a, b) {
                var an = $(a).text(),
                    bn = $(b).text();
                if (an && bn) {
                    return an.toUpperCase().localeCompare(bn.toUpperCase());
                }
                return 0;
            });
            $elements.detach().appendTo(  $(this).find('.twoColumnListTo')   );

            refreshExceptionalDays();

        });
});