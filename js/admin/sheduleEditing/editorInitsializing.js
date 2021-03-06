$(document).ready(function () {


    $.fn['timetableEditor'] =
    {
        startAdding: function()
        {
            openEditor();
           // initEditor();

        },
        startAddingAnotherTimetable: function(timeTableToEdit)
        {
            openEditor();
            addNewTimetable(timeTableToEdit)
        },

        startEditing: function(timeTableToEdit)
        {
            openEditor();
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

        },
        closeEditing: function()
        {
            $('#edititngSheduleArea').empty();
            $('#edititngSheduleArea').addClass('no-display');
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
        $(document).trigger('refreshDoctorsListEditor');
        initHandlers(editorBlock);
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
        console.log(timeTableToEdit);

        // Выводим содержимое самого календаря
        $.fn['sheduleEditor.port.JSONToEditor'].printToScreen(timeTableToEdit.json_data);
        // Даты начала и даты конца действия графика
        $('#edititngSheduleArea .sheduleBeginDateTime').val(timeTableToEdit.date_begin);
        $('#edititngSheduleArea .sheduleEndDateTime').val(timeTableToEdit.date_end);

        $('#edititngSheduleArea .sheduleBeginDateTime').trigger('change');
        $('#edititngSheduleArea .sheduleEndDateTime').trigger('change');

        // Записываем Id расписания
        $('#edititngSheduleArea .timeTableId').val(timeTableToEdit.id);

        // Выбираем докторов, которые указаны в списке
        // Сначала выберем "все отделения"
        $('#wardSelect').find('option').attr('selected',false);
        $('#wardSelect').find('option[value=-1]').attr('selected','selected');
        $('#wardSelect').trigger('change');

        doctorsToSelect = [];
        for (var wardKey in timeTableToEdit.wardsWithDoctors)
        {
            for (var doctorKey in timeTableToEdit.wardsWithDoctors[wardKey].doctors)
            {
                doctorsToSelect.push(doctorKey);
            }
        }

        $('#doctorsSelect').val(doctorsToSelect );
        $('#doctorsSelect').trigger('change');

    }

    function addNewTimetable(doctors)
    {

        // Выбираем докторов, которые указаны в списке
        // Сначала выберем "все отделения"
        $('#wardSelect').find('option').attr('selected',false);
        $('#wardSelect').find('option[value=-1]').attr('selected','selected');
        $('#wardSelect').trigger('change');

        doctorsToSelect = [];
        for (var wardKey in doctors)
        {
            for (var doctorKey in doctors[wardKey].doctors)
            {
                doctorsToSelect.push(doctorKey);
            }
        }

        $('#doctorsSelect').val(doctorsToSelect );
        $('#doctorsSelect').trigger('change');
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