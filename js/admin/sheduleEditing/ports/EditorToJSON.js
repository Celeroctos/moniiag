$(document).ready(function () {

    $.fn['sheduleEditor.port.editorToJSON'] = {
        getResult:function()
        {
            return convertInternal();
        }
    }

    function convertInternal()
    {
        // Выбираем все в строке таблицы редактирования
        rulesRows = $('#edititngSheduleArea .oneRowRuleTimetable');
        rulesObject = [];
        // Перебираем правила
        for (i=0;i<rulesRows.length;i++)
        {
            rulesObject = getRowObject($(rulesRows)[i]);
        }

        return $.toJSON(rulesObject);
    }

    function getRowObject(tableRowRule)
    {
        rowObjectResult = {};

        // Забираем id кабинета
        idRoom = $(tableRowRule).find('.cabinetSelectEdit').val();
        if (idRoom>0)
        {
            rowObjectResult.cabinet = idRoom;
        }
        // Читаем строку с днями
        rowObjectResult.days = getDays( $(tableRowRule).find('.daysOfWeekEdit') );

        // Читаем чётность/нечётность
        oddStatusBlock = $(tableRowRule).find('.oddCheckbox');
        if (   $(oddStatusBlock).find('[name=oddDays]').prop('checked')==true   )
        {
            rowObjectResult.oddance = 1;
        }
        else
        if (  $(oddStatusBlock).find('[name=notoddDays]').prop('checked')==true )
        {
            rowObjectResult.oddance = 0;
        }

        // Читаем статус исключений (поле кроме)
        if (  ($(tableRowRule).find('.exceptionSelect').val()!=-1)  )
        {
            rowObjectResult.except = $(tableRowRule).find('.exceptionSelect').val();
        }

        // Читаем даты из календаря
        datesFromDayTD = $(tableRowRule).find('.daysOneDateContainer');
        if (datesFromDayTD.length>0)
        {
            rowObjectResult.daysDates = [];
            for (i=0;i<datesFromDayTD.length;i++)
            {
                rowObjectResult.daysDates.push( $(datesFromDayTD[i]).find('.daysOneDateValue').text().split('.').reverse().join('-') );
            }
        }

        // Читаем времена начала приёма и работы
        getTime(rowObjectResult, tableRowRule,'.workingHourBeginTime','workingBegin');
        getTime(rowObjectResult, tableRowRule,'.workingHourEndTime','workingEnd');
        getTime(rowObjectResult, tableRowRule,'.greetingHourBeginTime','greetingBegin');
        getTime(rowObjectResult, tableRowRule,'.greetingHourEndTime','greetingEnd');

        // Читаем лимиты
        rowObjectResult.limits = getLimits(tableRowRule);

        // Перебираем обстоятельства
        factsBlocks = $(tableRowRule).find('.factsItemContainer');
        if (factsBlocks.length>0)
        {
            rowObjectResult.facts = [];
            for (i=0;i<factsBlocks.length;i++)
            {
                oneFactArrayEl = {};
                oneFactArrayEl.type = $($(factsBlocks)[i]).find('.typeFactVal').val();
                oneFactArrayEl.isRange = $($(factsBlocks)[i]).find('.isRange').val();
                oneFactArrayEl.begin = ($($(factsBlocks)[i]).find('.dateFactBegin').val()).split('.').reverse().join('-') ;
                oneFactArrayEl.end = ($($(factsBlocks)[i]).find('.dateFactEnd').val()).split('.').reverse().join('-') ;

                rowObjectResult.facts.push(oneFactArrayEl);
            }
        }

        return rowObjectResult;
    }

    function getLimits(rowContainer)
    {
        limitResults = {};

        for (i=1;i<4;i++)
        {
            limitResults[i.toString()] = {};
            limitBlock = $(rowContainer).find('.limitBlock'+ i.toString())
            // Проверяем - если количество - забиваем
            quantity = $(limitBlock).find('.limitQuantity').val();
            timeBegin = $(limitBlock).find('.limitTime'+i.toString()+' input[type=hidden]').val();
            timeEnd = $(limitBlock).find('.limitTime'+i.toString()+'End input[type=hidden]').val();

            if (quantity!='' && quantity!=null)
            {
                limitResults[i.toString()].quantity=quantity;
            }

            if (timeBegin !='' && timeBegin !=null)
            {
                limitResults[i.toString()].begin=timeBegin ;
            }

            if (timeEnd !='' && timeEnd !=null)
            {
                limitResults[i.toString()].end=timeEnd ;
            }

        }

        return limitResults;
    }

    function getTime( ruleObject,ruleTR,selector,ruleObjectField )
    {
        elementValue = $(ruleTR).find(selector+' input[type=hidden]').val();
        if ( elementValue!='' &&  elementValue!=null )
        {
            ruleObject[ruleObjectField] = elementValue;
        }
    }

    function getDays(daysTable)
    {
        daysResult = {};
        weekDays = $(daysTable).find('tr:first td');
        weekDaysNumbers = $(daysTable).find('tr:last td');

        // Перебираем строку с днями
        for (i=0;i<weekDays.length;i++)
        {
            if ( $($(weekDays)[i]).find( '.weekDay'+(i+1).toString()).prop('checked')==true  )
            {
                daysResult[(i+1).toString()] = [];
            }
        }

        // Перебираем ячейки номеров дней
        for (i=0;i<weekDaysNumbers.length;i++)
        {
            numberCHeckBoxes = $(weekDaysNumbers[i]).find('[type=checkbox]');
            // Дальше перебираем чекбоксы
            for (j=0;j<numberCHeckBoxes.length;j++)
            {
                if (  $(numberCHeckBoxes[j]).prop('checked')==true   )
                {
                    // Смотрим - если не определём день недели, то вставляем день недели
                    if (daysResult[(i+1).toString()] == undefined)
                    {
                        daysResult[(i+1).toString()] = [];
                    }
                    // А теперь берём номер у чекбокса и записываем его в массив по дню недели
                    daysResult[(i+1).toString()].push((j+1).toString()) ;

                }
            }

        }

        return daysResult;
    }

});