$(document).ready(function () {


    $.fn['timetableEditor'] =
    {
        startAdding: function()
        {
            openEditor();
           // initEditor();
            editorBlock = $('#edititngSheduleArea');
            initHandlers(editorBlock);
        },
        startEditing: function(timeTableToEdit)
        {
            openEditor();
            editorBlock = $('#edititngSheduleArea');
            initHandlers(editorBlock);
            initEditor(timeTableToEdit);
        },
        addRowInEditor: function()
        {
            addOneRowRule();
        },
        getActiveRangePicker: function()
        {
            return activeRangePicker;
        },
        addDayDate:function(container,dateToAdd)
        {
            newDateBlock = $('#timetableTemplates .daysOneDateContainer').clone();
            // Записываем туда дату
            $(newDateBlock).find('.daysOneDateValue').text(   (dateToAdd).split('-').reverse().join('.') );
            // Записываем newDateBlock в контейнер
            $(container).append( $(newDateBlock) );
        },

        addFact: function(factType,rangeFlag,beginDate,endDate)
        {
            caption = '';
            containerTemplate = $('#timetableTemplates .factsItemContainer').clone();
            editorContainer = $('#edititngSheduleArea .oneRowRuleTimetable:first .factsTD');

            $(containerTemplate).find('.typeFactVal').val(  factType );
            $(containerTemplate).find('.isRange').val(  rangeFlag );
            $(containerTemplate).find('.dateFactBegin').val(beginDate);
            $(containerTemplate).find('.dateFactEnd').val(endDate);

            caption += beginDate.split('-').reverse().join('.');

            if (rangeFlag=='1')
            {
                caption += ' - ';
                caption += endDate.split('-').reverse().join('.');
            }

            optionByVal = $('#timetableTemplates .factsSelect option[value='+factType+']');
            $(containerTemplate).find('.factTextCaptionReason').text($(optionByVal).text());

            $(containerTemplate).find('.factTextCaptionDate').text(caption);
            $(containerTemplate).removeClass('no-display');

            // Записываем в блок
            $(editorContainer).find('.factsDatesBlock').append( $(containerTemplate) );

        }

    }

    activeRangePicker = null;

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
        $.fn['sheduleEditor.port.JSONToEditor'].printToScreen(timeTableToEdit);
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

      //  $($(tableRow)).find('input[name="addFactRangeTimetable"]').daterangepicker();
        $($(tableRow)).find('.factsRangeButton').daterangepicker(
            {},
            function(start, end) {
                console.log(this);
                activeRanger = $.fn['timetableEditor'].getActiveRangePicker();
                if (activeRanger!=null)
                {
                    $(activeRanger).find('[name=factRangeBegin]').val( start.format('YYYY-MM-DD')  );
                    $(activeRanger).find('[name=factRangeEnd]').val( end.format('YYYY-MM-DD')  );
                    $('#edititngSheduleArea .oneRowRuleTimetable:first .factsTD').trigger('needFactSave');
                }
                //$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }
        );

        InitOneDateControl(   $(tableRow).find('.factsTD .addFactDateTimetable-cont')  );

        $(tableRow).find('.oneRowRuleTimetable:first .rangeCheckbox input').trigger('change');
    }
});