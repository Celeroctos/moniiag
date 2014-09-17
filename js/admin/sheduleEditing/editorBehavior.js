/*
Здесь описано всё, что касается непосредственно редактора расписания - его создания и т.д.

 */
$(document).ready(function () {
    $.fn['timetableEditor'] =
    {
        startAdding: function()
        {
            openEditor();
            initEditor();
            editorBlock = $('#edititngSheduleArea');
            initHandlers(editorBlock);
        },
        startEditing: function(timeTableToEdit)
        {
            openEditor();
            initEditor(timeTableToEdit);
            editorBlock = $('#edititngSheduleArea');
            initHandlers(editorBlock);
        },
        addRowInEditor: function()
        {
            addOneRowRule();
        }
    }

    // Открываем редактор (показываем блок с ним)
    function openEditor()
    {
        $('.addingNewSheduleContainer').addClass('no-display');
        editingTemplate = $('#timetableTemplates #timetableEditing');
        // Теперь надо вставить в содержимое editingTemplate в блок
        editorBlock = $('#edititngSheduleArea');
        $(editorBlock).removeClass('no-display');

        $(editorBlock).html(   $(editingTemplate[0]).html()   );
    }

    function initHandlers( editorBlock)
    {
        // В первой tr-ке редактора удаляем крестик удаления
        $(editorBlock).find('.oneRowRuleTimetable:eq(0) td.deleteTD').empty();

        $(editorBlock).find('.sourceSelect').trigger('change');

        // Инитим календарь для ячейки "Дни работы"
        tableRow = $(editorBlock).find('.oneRowRuleTimetable:eq(0)');
        initRowHandlers(tableRow);

        InitOneDateControl(  $(editorBlock).find('.sheduleBeginDateTime-cont'))   ;
        InitOneDateControl(  $(editorBlock).find('.sheduleEndDateTime-cont'))   ;

    }

    function initEditor(timeTableToEdit)
    {
        // Если определён - надо перебрать все правила из графика и запустить их редактирование
        //   иначе - надо открыть только первую строчку в
        if (timeTableToEdit==undefined)
        {

        }
        else
        {

        }
    }

    function initEditorEmpty()
    {

    }

    function initEditorWithData(timeTableToEdit)
    {

    }

    function addOneRowRule()
    {
        // Принцип:
        //      1. берём из шаблона строчку
        //      2. вставляем её перед строкой с кнопочками
        //      3. В колонке "обстоятельства" меняем rowspan на +1
        templateRow = $('#timetableTemplates .oneRowRuleTimetable').clone();
        buttonsRow = $('#edititngSheduleArea tr.addRuleButtons');

        // Прикошачиваем события к строке
        initRowHandlers(templateRow);

        // В добавляемой строке уничтожаю колонку "factsTD"
        $(templateRow).find('.factsTD ').remove();

        templateRow.insertBefore(buttonsRow);

        factsTD = $('#edititngSheduleArea tr.oneRowRuleTimetable:eq(0) td.factsTD');
        $(factsTD).attr('rowspan',   $('#edititngSheduleArea tr.oneRowRuleTimetable').length);
        $('#edititngSheduleArea').find('.sourceSelect').trigger('change');


    }

    // Инитит календарь внутри ячейки "дни работы"
    function initRowHandlers(tableRow)
    {

        InitOneDateControl(   $(tableRow).find('.daysTD .date-timetable')  );
        InitOneTimeControl(   $(tableRow).find('.hoursOfWorkTD .workingHourBeginTime')  );
        InitOneTimeControl(   $(tableRow).find('.hoursOfWorkTD .workingHourEndTime')  );
        InitOneTimeControl(   $(tableRow).find('.hoursOfGreetingTD .greetingHourBeginTime')  );
        InitOneTimeControl(   $(tableRow).find('.hoursOfGreetingTD .greetingHourEndTime')  );

        // Инитим лимит на приёмы
        for (i=0;i<3;i++)
        {
            limitBlock = $(tableRow).find('.limitTD .limitBlock'+ (i+1).toString());
            //
            timeBeginComtrol = $(limitBlock).find( '.limitTime'+ (i+1).toString());
            timeEndComtrol = $(limitBlock).find( '.limitTime'+ (i+1).toString()+'End');
            // Инитим контролы
            InitOneTimeControl(   timeBeginComtrol );
            InitOneTimeControl(  timeEndComtrol );
        }
        // Инитим календари начала и конца действия графика

    }

    //=========>
    // Обработка событий
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

    $(document).on('change', 'input[type=checkbox]',function(){
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
            $(parentContainer).find('.rangeCheckbox').removeclass('no-display');
            // Сбрасываем значения чекбокса
            $(parentBlock).find('[name=rangeFact], [name=notRangeFact]').removeAttr('checked');
            selectVal = $(this).val();
            // Смотрим в переменной - установлено ли isRange в единицу
            localIsRange = globalVariables.factsForSelect[selectVal];
            if (localIsRange == 1)
            {
                $(parentContainer).find('[name=notoddDays]').removeAttr('checked');
            }

        }

        // Смотрим - если селект

    });

    //=========>
});