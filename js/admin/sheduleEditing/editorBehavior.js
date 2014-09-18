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

            }

            if ( $(this).attr('name')==  'notRangeFact' )
            {
                // Снимаю выделение с элемента odd
                $(parentBlock).find('[name=rangeFact]').removeAttr('checked');
            }
        }
        else
        {
            // Ставим обратно свойство checked
            $(this).prop("checked", true);
        }
    });

    $(document).on('change','[name=addDateTimetable]',function(){
        // Найдём блок, в который нужно вставить дату
        if ( ($(this).val()!='') && ($(this).val()!=undefined) && ($(this).val()!=null) )
        {
            targetBlock = $(this).parents('.daysTD').find('.daysEditingDatesBlock');
            // а Основе this.val - конструируем блок с датой
            newDateBlock = $('#timetableTemplates .daysOneDateContainer').clone();
            // Записываем туда дату
            $(newDateBlock).find('.daysOneDateValue').text(   ($(this).val()).split('-').reverse().join('.') );
            // Записываем newDateBlock в контейнер
            $(targetBlock).append( $(newDateBlock) );
        }
    });

    $(document).on('click', '.daysOneDateContainer .daysOneDateValueRemove', function(){
        // Взять родителя у this и удалить
        parentsContainer = $(this).parents('.daysOneDateContainer');
        $(parentsContainer).remove();

    });

});