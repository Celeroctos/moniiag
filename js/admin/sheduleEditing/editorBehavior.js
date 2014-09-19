/*
Здесь описано всё, что касается непосредственно редактора расписания - его создания и т.д.

 */
$(document).ready(function () {

    $(document).on('change','.sourceSelect',function()
        {
            parentTD = $(this).parents('td');
            selectValue = $(this).val();
            $(parentTD).find('.limitBlock').addClass('no-display');
            $(parentTD).find('.limitBlock'+selectValue).removeClass('no-display');

        }
    );

    $(document).on('click', '.removeTimeTableRule', function()
        {
            // Берём родительский tr
            $(this).parents('tr').remove();
            // Надо уменьшить rowspan у ячейки с обстоятельствами
            factsTD = $('#edititngSheduleArea tr.oneRowRuleTimetable:eq(0) td.factsTD');

            $(factsTD).attr('rowspan',   $('#edititngSheduleArea tr.oneRowRuleTimetable').length);

        }
    );

    $(document).on('change', '.oddCheckbox input[type=checkbox]',function(){
        if (  $(this).prop("checked")  )
        {
            parentBlock = $(this).parents('div.oddCheckbox')
            if ( $(this).attr('name')==  'oddDays' )
            {
                // Снимаю выделение с элемента not-odd
                $(parentBlock).find('[name=notoddDays]').removeAttr('checked');

            }

            if ( $(this).attr('name')==  'notoddDays' )
            {
                // Снимаю выделение с элемента odd
                $(parentBlock).find('[name=oddDays]').removeAttr('checked');
            }


        }
    });

    $(document).on('change', '.oneRowRuleTimetable .factsSelect',function(){
        parentContainer = $(this).parents('td');
        // Смотрим - если значение -1, то всё нафиг прячем
        if ( $(this).val()==-1 )
        {
            $(parentContainer).find('.rangeCheckbox').addClass('no-display');
            $(parentContainer).find('.factsRangeDateCalendarContainer').addClass('no-display');
            $(parentContainer).find('.factsOneDateCalendarContainer').addClass('no-display');
        }
        else
        {
            // Показываем всё
            $(parentContainer).find('.rangeCheckbox').removeClass('no-display');
            // Сбрасываем значения чекбокса
            $(parentContainer).find('[name=rangeFact], [name=notRangeFact]').prop('checked', false);
            selectVal = $(this).val();
            // Смотрим в переменной - установлено ли isRange в единицу
            localIsRange = globalVariables.factsForSelect[selectVal];
            if (localIsRange == 1)
            {
                $(parentContainer).find('[name=rangeFact]').prop('checked', true);

            }
            else
            {
                $(parentContainer).find('[name=notRangeFact]').prop('checked', true);
            }

            if ($(parentContainer).find('[name=rangeFact]').prop('checked')==true)
            {
                switchFactsRangeMode(true);
            }
            else
            {
                switchFactsRangeMode(false);
            }

        }



    });

    $(document).on('change', '.rangeCheckbox input[type=checkbox]',function(){
        if (  $(this).prop("checked")  )
        {
            parentBlock = $(this).parents('div.rangeCheckbox')
            if ( $(this).attr('name')==  'rangeFact' )
            {
                // Снимаю выделение с элемента not-odd
                $(parentBlock).find('[name=notRangeFact]').removeAttr('checked');
                switchFactsRangeMode(true);
            }

            if ( $(this).attr('name')==  'notRangeFact' )
            {
                // Снимаю выделение с элемента odd
                $(parentBlock).find('[name=rangeFact]').removeAttr('checked');
                switchFactsRangeMode(false);
            }
        }
        else
        {
            // Ставим обратно свойство checked
            $(this).prop("checked", true);
        }
    });

    function switchFactsRangeMode(turnOn)
    {
        oneDate = $('#edititngSheduleArea .factsOneDateCalendarContainer');
        rangeDate = $('#edititngSheduleArea .factsRangeDateCalendarContainer');
        if (turnOn)
        {
            $(oneDate).addClass('no-display');
            $(rangeDate).removeClass('no-display');
        }
        else
        {
            $(oneDate).removeClass('no-display');
            $(rangeDate).addClass('no-display');
        }
    }

    $(document).on('change','[name=addDateTimetable]',function(){
        // Найдём блок, в который нужно вставить дату
        /*if ( ($(this).val()!='') && ($(this).val()!=undefined) && ($(this).val()!=null) )
        {
            targetBlock = $(this).parents('.daysTD').find('.daysEditingDatesBlock');
            // а Основе this.val - конструируем блок с датой
            newDateBlock = $('#timetableTemplates .daysOneDateContainer').clone();
            // Записываем туда дату
            $(newDateBlock).find('.daysOneDateValue').text(   ($(this).val()).split('-').reverse().join('.') );
            // Записываем newDateBlock в контейнер
            $(targetBlock).append( $(newDateBlock) );
        }*/
        if ( ($(this).val()!='') && ($(this).val()!=undefined) && ($(this).val()!=null) )
        {
            targetBlock = $(this).parents('.daysTD').find('.daysEditingDatesBlock');
            $.fn['timetableEditor'].addDayDate(targetBlock, $(this).val());
        }
    });

    $(document).on('click', '.daysOneDateContainer .daysOneDateValueRemove', function(){
        // Взять родителя у this и удалить
        parentsContainer = $(this).parents('.daysOneDateContainer');
        $(parentsContainer).remove();

    });

    $(document).on('click', '.factsItemContainer .factRemoveButton', function(){
        // Взять родителя у this и удалить
        parentsContainer = $(this).parents('.factsItemContainer');
        $(parentsContainer).remove();

    });

    activeRangePicker = null;

    $(document).on('click', '.factsRangeButton',function(){
       // Ищем родительский контейнер
        activeRangePicker = $(this).parents('.addFactRangeTimetable-cont');

    });

    $(document).on('change', '[name=addFactDateTimetable]',function(){
        parentFactTD = $(this).parents('.factsTD');
        parentFactTD.trigger('needFactSave');
    });

    $(document).on(
      'needFactSave',
      '#edititngSheduleArea .oneRowRuleTimetable:first .factsTD',
        function()
        {
            // Читаем факт и складываем его в таблицу
            console.log('Читаю факт из редактора');

            caption = '';

            containerTemplate = $('#timetableTemplates .factsItemContainer').clone();

            editorContainer = $('#edititngSheduleArea .oneRowRuleTimetable:first .factsTD');
            type =  $(editorContainer).find('.factsSelect').val();
            isRange = '0';
            if (   $(editorContainer).find('[name=rangeFact]').prop('checked')==true  )
            {
                isRange = '1';
            }
            if (   isRange=='1' || isRange==1 )
            {
                dateBegin =
                    $(editorContainer).find('[name=factRangeBegin]').val();

                dateEnd =
                    $(editorContainer).find('[name=factRangeEnd]').val();
            }
            else
            {
                dateBegin =
                    $(editorContainer).find('[name=addFactDateTimetable]').val();
                dateEnd = '';
            }

            $.fn['timetableEditor'].addFact(
                type,
                isRange,
                dateBegin,
                dateEnd
            );
        }
    );


    /* $(document).on(
     'needFactSave',
     '#edititngSheduleArea .oneRowRuleTimetable:first .factsTD',
     function()
     {
     // Читаем факт и складываем его в таблицу
     console.log('Читаю факт из редактора');

     caption = '';

     containerTemplate = $('#timetableTemplates .factsItemContainer').clone();

     editorContainer = $('#edititngSheduleArea .oneRowRuleTimetable:first .factsTD');
     $(containerTemplate).find('.typeFactVal').val(  $(editorContainer).find('.factsSelect').val() );
     $(containerTemplate).find('.isRange').val(  '0' );
     if (   $(editorContainer).find('[name=rangeFact]').prop('checked')==true  )
     {
     $(containerTemplate).find('.isRange').val(  '1' );
     }
     if (   $(containerTemplate).find('.isRange').val(   )=='1' )
     {
     $(containerTemplate).find('.dateFactBegin').val(
     $(editorContainer).find('[name=factRangeBegin]').val()
     );

     caption += $(editorContainer).find('[name=factRangeBegin]').val();

     $(containerTemplate).find('.dateFactEnd').val(
     $(editorContainer).find('[name=factRangeEnd]').val()
     );

     caption += ' - ';
     caption += $(editorContainer).find('[name=factRangeEnd]').val();


     }
     else
     {
     $(containerTemplate).find('.dateFactBegin').val(
     $(editorContainer).find('[name=addFactDateTimetable]').val().split('-').reverse().join('.')
     );

     caption += $(containerTemplate).find('.dateFactBegin').val();
     }
     $(containerTemplate).find('.factTextCaptionDate').text(caption);
     selectedOption = $(editorContainer).find('.factsSelect option:selected');
     //caption += $(selectedOption).text();
     $(containerTemplate).find('.factTextCaptionReason').text($(selectedOption).text());
     $(containerTemplate).find('.factTextCaption').text(caption);
     $(containerTemplate).removeClass('no-display');

     // Записываем в блок
     $(editorContainer).find('.factsDatesBlock').append( $(containerTemplate) );
     }
     );*/

});