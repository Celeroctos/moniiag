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
        // В добавляемой строке уничтожаю колонку "factsTD"
        $(templateRow).find('.factsTD').remove();

        templateRow.insertBefore(buttonsRow);

        factsTD = $('#edititngSheduleArea tr.oneRowRuleTimetable:eq(0) td.factsTD');
        $(factsTD).attr('rowspan',   $('#edititngSheduleArea tr.oneRowRuleTimetable').length);
        $('#edititngSheduleArea').find('.sourceSelect').trigger('change');

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

    //=========>
});