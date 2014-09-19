$(document).ready(function () {

    $.fn['sheduleEditor.port.JSONToEditor'] = {
        printToScreen:function(timetableJSON)
        {
            printInternal(timetableJSON)
            return 1;
        }
    }

    function printOneRule(tableRow, ruleToPrint)
    {
        // Выводим кабинет
        $(tableRow).find('.cabinetSelectEdit').val( ruleToPrint.cabinet  );
        // Выводится чётность-нечётность

        if (ruleToPrint.oddance==0)
        {
            $(tableRow).find('[name=notoddDays]').prop('checked',true);
        }

        if (ruleToPrint.oddance==1)
        {
            $(tableRow).find('[name=oddDays]').prop('checked',true);
        }

        // Раздербаниваем дни
        printDays($(tableRow).find('.daysOfWeekEdit'), ruleToPrint.days );

        // Выводим поле "Кроме"
        if ( ruleToPrint.except !=undefined  )
        {
            $(tableRow).find('.exceptionSelect').val(ruleToPrint.except);
        }
        printWorkingGreetingTime(tableRow,ruleToPrint,'workingBegin','.workingHourBeginTime input[type=hidden]');
        printWorkingGreetingTime(tableRow,ruleToPrint,'workingEnd','.workingHourEndTime input[type=hidden]');
        printWorkingGreetingTime(tableRow,ruleToPrint,'greetingBegin','.greetingHourBeginTime input[type=hidden]');
        printWorkingGreetingTime(tableRow,ruleToPrint,'greetingEnd','.greetingHourEndTime input[type=hidden]');
        printBlockLimits(tableRow,ruleToPrint);

        if (ruleToPrint.daysDates!=undefined)
        {
            printDatesDay( $(tableRow).find('.daysEditingDatesBlock'), ruleToPrint.daysDates )
        }

    }

    function printDatesDay(datesContainer, datesToPrint)
    {
        for (i=0;i<datesToPrint.length;i++)
        {
            $.fn['timetableEditor'].addDayDate(datesContainer, datesToPrint[i]);
        }


    }

    function printBlockLimits(tableRow,ruleToPrint)
    {
        for(i=1;i<4;i++)
        {
            currentLimit = ruleToPrint.limits[i];

            if (currentLimit.quantity!=undefined)
            {
                $(tableRow).find('.limitQuantity'+ i.toString()).val(  currentLimit.quantity );
            }

            if (currentLimit.begin!=undefined)
            {
                $(tableRow).find('.limitTime'+ i.toString()+' input[type=hidden]').val(  currentLimit.begin );
                $(tableRow).find('.limitTime'+ i.toString()+' input[type=hidden]').trigger('change');
            }

            if (currentLimit.end!=undefined)
            {
                $(tableRow).find('.limitTime'+ i.toString()+'End input[type=hidden]').val(  currentLimit.end);
                $(tableRow).find('.limitTime'+ i.toString()+'End input[type=hidden]').trigger('change');
            }

        }

    }

    function printWorkingGreetingTime(tableRow,ruleObject,fieldRuleObject,tableRowSelector)
    {
        if (ruleObject[fieldRuleObject]!=undefined)
        {
            $(tableRow).find(tableRowSelector).val(
                ruleObject[fieldRuleObject]
            );
            $(tableRow).find(tableRowSelector).trigger('change');
        }
    }

    function printDays(daysTable, daysRule)
    {
        // Пробегаемся по дням
        for (i=1;i<8;i++)
        {
            if (daysRule[i.toString()]!=undefined)
            {
                if (daysRule[i.toString()].length==0)
                {
                    // Активируем чекбокс с днём
                    $(daysTable).find('tr:eq(0) .weekDay'+ i.toString()).prop('checked',true);
                }
                else
                {
                    // Перебираем номера и активруем их
                    for (j=0;j<daysRule[i.toString()].length;j++)
                    {
                        $(daysTable).find('tr:eq(1) [name=weekDayNumber'+ i.toString() +'_'+  daysRule[i.toString()][j]+']' ).prop('checked',true);
                    }
                }
            }
        }
    }

    function printInternal(timetableJSON)
    {
        timetableObject = $.parseJSON(timetableJSON);

        rulesNumber = timetableObject.rules-1;

        // Добавляем в редактор rulesNumber дополнительных строк
        for (i=0;i<rulesNumber;i++)
        {
            $.fn['timetableEditor'].addRowInEditor();
        }
        // Перебираем правила и для каждого правила вызываем функцию, которая выбросит
        // на текущую строку таблицы данные из объекта правила
        for (rulesCounter = 0;rulesCounter <timetableObject.rules.length;rulesCounter ++)
        {
            printOneRule(  $('#edititngSheduleArea .oneRowRuleTimetable:eq('+ rulesCounter.toString()+')') ,
                timetableObject.rules[rulesCounter]
            );

        }
        // Выведем факты
        for (i=0;i<timetableObject.facts.length;i++)
        {
            $.fn['timetableEditor'].addFact(
                timetableObject.facts[i].type,
                timetableObject.facts[i].isRange,
                timetableObject.facts[i].begin,
                timetableObject.facts[i].end
            );
        }

    }

});