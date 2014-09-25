$(document).ready(function () {

    $.fn['sheduleEditor.port.JSONToPreview'] = {
        getHTML:function(timetableToConvert)
        {
           return getHTMLInternal(timetableToConvert);
        }
    }

    function getHTMLInternal(timetableObject)
    {
        timeTableTemplate = $('#timetableTemplates .timetableReadOnly').clone();
        // Вываливаем врачей
        printDoctors( $(timeTableTemplate).find('.timeTablesRODoctorsWards'), timetableObject.wardsWithDoctors  );

        // Выводим период начла и конца действия
        $(timeTableTemplate).find('.timeTableROFrom').text( timetableObject.date_begin );
        $(timeTableTemplate).find('.timeTableROTo').text( timetableObject.date_end );

        // Переводим об'ект расписания в JSON и закидываем в специальные поля ID расписания и его JSON
        $(timeTableTemplate).find('.timeTableId').val(timetableObject.id);
        $(timeTableTemplate).find('.timeTableJSON').val(  $.toJSON(timetableObject) );

        rulesFactsData = $.parseJSON(timetableObject.json_data);

        // Берём длину массива "правила" и добавляем число строчек, равное длине массива правил
        for (rulesCounter=0;rulesCounter<rulesFactsData.rules.length-1;rulesCounter++)
        {
            // Берём шаблон строки из блока шаблонов
            rowTemplate = $('#timetableTemplates .timetableReadOnly .timeTablesROTable tbody tr').clone();

            // Убираем ячейку .factsTD в добавляемой строке
            $(rowTemplate).find('td.factsTD').remove();


            $(timeTableTemplate).find('.timeTablesROTable tbody').append(rowTemplate);
        }

        // В первой ячейке ставим значение rowspan, равное числу правил в расписании
        $(timeTableTemplate).find('.timeTablesROTable tbody tr:first td.factsTD').attr(
            'rowspan', rulesFactsData.rules.length
        );

        // Перебираем правила и подаём их в функцию, которая берёт строку для правила и само правило и раздербанивает его
        for (rulesCounter=0;rulesCounter<rulesFactsData.rules.length;rulesCounter++)
        {
            printOneRule(
                $(timeTableTemplate).find('.timeTablesROTable tbody tr:eq('+ rulesCounter.toString() +')'),
                rulesFactsData.rules[rulesCounter]
            );
        }

        // Раздербанаиваем факты
        printFacts(
            $(timeTableTemplate).find('.timeTablesROTable tbody tr:first td.factsTD'),
            rulesFactsData.facts
        );

        return timeTableTemplate;
    }

    function printOneRule(trContainer, oneRuleToPrint)
    {
        // Кабинет
        $(trContainer).find('.roomTD').text( globalVariables.cabinetsArray[ oneRuleToPrint.cabinet ] );

        // Печатаем дни недели
        printDaysOfWorks(  $(trContainer).find('.daysTD'), oneRuleToPrint);

        // Печатаем время работы
        timeWorking = '';
        if (oneRuleToPrint.workingBegin!=undefined && oneRuleToPrint.workingEnd!=undefined)
        {
            timeWorking+=oneRuleToPrint.workingBegin+' '+oneRuleToPrint.workingEnd;
        }

        $(trContainer).find('.hoursOfWorkTD').text(timeWorking);
        // ---->
        timeWorking = '';
        if (oneRuleToPrint.greetingBegin!=undefined && oneRuleToPrint.greetingEnd!=undefined)
        {
            timeWorking+=oneRuleToPrint.greetingBegin+' '+oneRuleToPrint.greetingEnd;
        }

        $(trContainer).find('.hoursOfGreetingTD').text(timeWorking);

        // Выводим лимиты
        printLimit($(trContainer).find('.limitTD'),oneRuleToPrint.limits['1'], 1);
        printLimit($(trContainer).find('.limitTD'),oneRuleToPrint.limits['2'],2);
        printLimit($(trContainer).find('.limitTD'),oneRuleToPrint.limits['3'],3);

    }

    function printLimit(limitCell,limitData, limitCode)
    {
        // Если в пределе определено хотя бы одно поле
        if ( limitData.quantity!=undefined ||  limitData.begin!=undefined || limitData.end!=undefined)
        {
            limitContainer = $('#timetableTemplates .limitROBlock').clone();
            limitValue = '';

            if (limitData.quantity!=undefined)
            {
                limitValue += "Количество: ";
                limitValue += limitData.quantity;
            }
            /*if (limitData.begin!=undefined)
            {
                if (limitValue!='')
                {
                    limitValue+='<br>';
                }
                limitValue += ('C '+ limitData.begin )
            }
            if (limitData.end!=undefined)
            {
                if (limitData.begin==undefined && limitValue!='')
                {
                    limitValue+= '<br>';
                }
                limitValue += ('По '+ limitData.begin )
            }
            */
            if ((limitData.begin!=undefined) && (limitData.end!=undefined))
            {
                if (limitValue != '')
                {
                    limitValue += '<br>';
                }
                limitValue += (limitData.begin+' - '+limitData.end);
            }
            $(limitContainer).find('.limitROBody').html(limitValue);
            // Добавляем название лимита
            switch(limitCode.toString())
            {
                case '1':
                    $(limitContainer).find('.limitObjectName').html('Call-Центр');
                    break;
                case '2':
                    $(limitContainer).find('.limitObjectName').html('Регистратура');
                    break;
                case '3':
                    $(limitContainer).find('.limitObjectName').html('Интернет');
                    break;

            }
            $(limitCell).append(  $(limitContainer) );
        }
    }

    function getShortenedWeekDayName(dayCode)
    {
        result = '';
        switch ( dayCode.toString()  )
        {
            case '1': result = 'ПН'; break;
            case '2': result = 'ВТ'; break;
            case '3': result = 'СР'; break;
            case '4': result = 'ЧТ'; break;
            case '5': result = 'ПТ'; break;
            case '6': result = 'СБ'; break;
            case '7': result = 'ВС'; break;
        }
        return result;
    }

    function getFullWeekDayName(dayCode)
    {
        result = '';
        switch ( dayCode.toString()  )
        {
            case '1': result = 'понедельник'; break;
            case '2': result = 'вторник'; break;
            case '3': result = 'среда'; break;
            case '4': result = 'четверг'; break;
            case '5': result = 'пятница'; break;
            case '6': result = 'суббота'; break;
            case '7': result = 'воскресенье'; break;
        }
        return result;
    }

    function getNumberWeekWithLetters(number, gender)
    {
        result = '';
        if (gender==1)
        {
            switch(parseInt(number))
            {
                case 1: result = 'первый'; break;
                case 2: result = 'второй'; break;
                case 3: result = 'третий'; break;
                case 4: result = 'четвёртый'; break;
                case 5: result = 'пятый'; break;
            }
        }
        if (gender==2)
        {
            switch(parseInt(number))
            {
                case 1: result = 'первая'; break;
                case 2: result = 'вторая'; break;
                case 3: result = 'третья'; break;
                case 4: result = 'четвёртая'; break;
                case 5: result = 'пятая'; break;
            }
        }
        if (gender==3)
        {
            switch(parseInt(number))
            {
                case 1: result = 'первое'; break;
                case 2: result = 'второе'; break;
                case 3: result = 'третье'; break;
                case 4: result = 'четвёртое'; break;
                case 5: result = 'пятое'; break;
            }
        }

        return result;
    }

    function getWeekDayNumberString( weekDayNumber, weekNumbers )
    {
        result = '';
        gender = -1; // Пол дня недели, чтобы числительные правильно просклонять
        // Сначала склоняем слово "каждый"
        switch ( weekDayNumber.toString()  )
        {
            case '1':
            case '2':
            case '4':
                result += 'Каждый'; gender=1; break;
            case '3':
            case '5':
            case '6':
                result += 'Каждая'; gender=2;  break;
            case '7':
                result += 'Каждое'; gender=3;  break;
        }

        // Затем перебираем номера дней, склоняем их в зависимости от дня недели
        for (daysNumberCounter = 0;daysNumberCounter<weekNumbers.length;daysNumberCounter++)
        {

            if (daysNumberCounter!=0 && daysNumberCounter!= weekNumbers.length)
            {
                result += ',';
            }
            result += ' ';
            result += getNumberWeekWithLetters(weekNumbers[daysNumberCounter],gender);

        }

        // Прикручиваем название самого дня недели
        result +=( ' '+getFullWeekDayName(weekDayNumber));


        return result;
    }

    function getGenitiveWeekDay(weekDayNumber) // (genetive - родительный падеж)
    {
        result = '';
        switch (parseInt(weekDayNumber))
        {
            case 1: result = 'понедельника'; break;
            case 2: result = 'вторника'; break;
            case 3: result = 'среды'; break;
            case 4: result = 'четверга'; break;
            case 5: result = 'пятницы'; break;
            case 6: result = 'субботы'; break;
            case 7: result = 'воскресенья'; break;
        }
        return result;
    }

    function returnExceptString(exceptObject)
    {
        result = '';
        datesFlag = false;
        // Перебираем значения из exceptObject
        for (exceptCounter = 0;exceptCounter < exceptObject.length;exceptCounter++)
        {
            if (exceptObject[exceptCounter]==-2)
            {
                datesFlag = true;
            }
            else
            {
                // Если не пустая строка - нужно добавить запятулю перед
                if (result!='')
                {
                    result+= ','
                }
                result+=' ';
                result += getGenitiveWeekDay(exceptObject[exceptCounter]);
            }
        }

        if (datesFlag)
        {
            if (result!='')
            {
                result+= ',';
            }
            result+=' ';
            result += ' следующих дат: ';
        }

        return result;
    }

    function printDaysOfWorks(dayWorksTD, ruleToPrint)
    {
        if (ruleToPrint.days!=undefined)
        {
            // Берём массив days и выводим сначала те дни, когда врач работает всегда (не по номеру недели)
            weekDaysString = '';
            for(var key1 in ruleToPrint.days)
            {
                if (ruleToPrint.days[key1].length==0)
                {
                    if (weekDaysString!='')
                    {
                        weekDaysString +=' ';
                    }
                    weekDaysString += getShortenedWeekDayName(key1);

                }
            }

            // Берём массив days и выводим те дни, когда врач работает по номерам недели
            for(var key2 in ruleToPrint.days)
            {
                if (ruleToPrint.days[key2].length!=0)
                {
                    /*
                    // Перебираем номера недель в массиве
                    for (daysNumberCounter = 0;daysNumberCounter<ruleToPrint.days[key].length;daysNumberCounter++)
                    {

                    }*/
                    if (weekDaysString!='')
                    {
                        weekDaysString+='<br>';
                    }
                    weekDaysString+= getWeekDayNumberString( key2, ruleToPrint.days[key2] );

                }
            }

        }
            // Если есть чётность/нечётность
            if (ruleToPrint.oddance!=undefined)
            {
                if (weekDaysString!=''){weekDaysString+= '<br>';}
                if (ruleToPrint.oddance==1)
                {
                    if (weekDaysString!=''){weekDaysString+='<br>';}
                    weekDaysString+='По чётным';

                }

                if (ruleToPrint.oddance==0)
                {

                    if (weekDaysString!=''){weekDaysString+='<br>';}
                    weekDaysString+='По нечётным';
                }

                if (ruleToPrint.except!=undefined)
                {
                    weekDaysString+=' к';
                }

            }
            else
            {
                if (ruleToPrint.except!=undefined)
                {
                    if (weekDaysString!=''){weekDaysString+='<br>'}
                    weekDaysString+='К';
                }
            }
            // Выводим поле "Кроме", если оно есть
            if (ruleToPrint.except!=undefined)
            {
                weekDaysString += 'роме'; // (первую букву в слове "кроме" мы уже вывели выше)
                weekDaysString += returnExceptString(ruleToPrint.except);
            }

            // Выводим даты
            if (ruleToPrint.daysDates!=undefined)
            {
                if (weekDaysString!=''){weekDaysString+='<br>'}
                // Перебираем даты
                for (datesCounter=0;datesCounter<ruleToPrint.daysDates.length;datesCounter++)
                {
                    if (datesCounter!=0)
                    {
                        weekDaysString+=', ';
                    }
                    weekDaysString += (  ruleToPrint.daysDates[datesCounter].split('-').reverse().join('.')   );
                }
            }

        // Запишем в ячейку дня недели
        $(dayWorksTD).html(weekDaysString);

    }

    function printFacts(factsTDContainer, factsArray)
    {
        // Перебираем факты
        for (factCounter=0;factCounter<factsArray.length;factCounter++)
        {
            // Берём контейнер для факта
            factContainter = $('#timetableTemplates .oneFactROBlock').clone();

            // Выводим причину
            reasonText = $('#timetableTemplates select.factsSelect option[value='+ factsArray[factCounter].type +']').text();
            $(factContainter).find('.oneFactTypeRO').text(reasonText);
            // Выводим начало
            $(factContainter).find('.oneFactTimeRO').html(factsArray[factCounter].begin.split('-').reverse().join('.'));
            // Если диапазон - выводим и кончало
            if (factsArray[factCounter].isRange==1)
            {
                $(factContainter).find('.oneFactTimeRO').html(
                    $(factContainter).find('.oneFactTimeRO').html()
                        +'<br>'
                        + factsArray[factCounter].end.split('-').reverse().join('.'));
            }

            // Добавляем в ячейку созданный контейнер
            $(factsTDContainer).append(  $(factContainter) );
        }
    }

    function printDoctors(doctorsTable,doctorsArray)
    {

        // В целевом контейнере убиваем все строки, которые были раньше (т.е. удаляем пустую строку из шаблона)
        $(doctorsTable).find('.oneRowDoctorsWardRO').remove();

        // Перебираем отделения
        //for (wardsCounter = 0; wardsCounter< wardsCounter<doctorsArray.length;wardsCounter++)
        for (var wardsCounter in doctorsArray)
        {
            // Взять строку из таблицы, первую колонку заполнить названием отделения
             newRowTemplate = $('#timetableTemplates .oneRowDoctorsWardRO').clone();
            // Добавляем отделение
            $(newRowTemplate).find('td.wardsColRO').text(doctorsArray[wardsCounter].name);
            // Перебираем врачей
            //for (doctorsCounter=0;doctorsCounter<doctorsArray[wardsCounter].doctors.length;doctorsCounter++)
            wasDoctorPrint = false;
            for (var doctorsCounter in doctorsArray[wardsCounter].doctors)
            {
                if (wasDoctorPrint)
                {
                    $(newRowTemplate).find('td.doctorsColRO').html(
                        $(newRowTemplate).find('td.doctorsColRO').html()+'<br>'
                    );
                }
                wasDoctorPrint = true;
                // Добавляем доктора
                $(newRowTemplate).find('td.doctorsColRO').html(
                    $(newRowTemplate).find('td.doctorsColRO').html()+
                        doctorsArray[wardsCounter].doctors[doctorsCounter]
                );
            }

            $(doctorsTable).find('tbody').append(   $(newRowTemplate)  );
        }

    }

});