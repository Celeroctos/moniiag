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

    $(document).on('change', '.sheduleBeginDateTime, .sheduleEndDateTime', function()
        {
            // Если у обоих контролов из редактора  есть значение - выводим его, иначе
            //     прячем блок "с по"
            if ( ($('#edititngSheduleArea .sheduleBeginDateTime').val()!='') &&
                ($('#edititngSheduleArea .sheduleEndDateTime').val()!='') )
            {

                $('#edititngSheduleArea .timeTableEditFrom').text(
                    $('#edititngSheduleArea .sheduleBeginDateTime').val().split('-').reverse().join('.')

                );

                $('#edititngSheduleArea .timeTableEditTo').text(
                    $('#edititngSheduleArea .sheduleEndDateTime').val().split('-').reverse().join('.')

                );
                $('.timeTableEditDateTimesAction').removeClass('no-display');
            }
            else
            {
                $('.timeTableEditDateTimesAction').addClass('no-display');
            }
        }
    );

    $(document).on('refreshDoctorsListEditor', function(){
        // 1. Забираем врачей
        // 2. Распределяем их по отделениям
        // 3. В соответствии с отделением - выводим в таблицу
        console.log('я случился!');
        doctorsByWardsSelected = [];
        doctorsByWardsSelectedNames = [];
        selectedDoctors = $('#doctorsSelect').find('option:selected');
        // Перебираем выбранных докторов
        for (i=0;i<selectedDoctors.length;i++)
        {

            if ($(selectedDoctors[i]).attr('value')==-1)
            {
                continue;
            }
            // Для каждого доктора определяем код его отделения
            selectedDoctorWardCode = globalVariables.doctorsForWards[$(selectedDoctors[i]).attr('value')];

            //
            if (selectedDoctorWardCode==null)
            {
                selectedDoctorWardCode = -2;
            }

            // Дальше смотрим - если отделения, которое мы получили нет, то добавляем в массив doctorsByWardsSelected его
            if (doctorsByWardsSelected[selectedDoctorWardCode]==undefined)
            {

                doctorsByWardsSelected[selectedDoctorWardCode] = [];
                if (selectedDoctorWardCode==-2)
                {
                    doctorsByWardsSelectedNames[selectedDoctorWardCode] = 'Без отделения';
                }
                else
                {
                    doctorsByWardsSelectedNames[selectedDoctorWardCode] =
                        $('#wardSelect').find('option[value='+ selectedDoctorWardCode +']').text();
                }

            }

            // В массив по номеру отделения добавляем ФИО доктора
            doctorsByWardsSelected[selectedDoctorWardCode].push(
                $(selectedDoctors[i]).text()
            );

        }
        console.log(doctorsByWardsSelected);
        console.log(doctorsByWardsSelectedNames);

        // Теперь перебираем полученные отделения и распихиваем их по таблице
        // Очищаем таблицу с врачами и отделениями
        $('#edititngSheduleArea').find('.timeTablesEditDoctorsWards tr.oneRowDoctorsWardEditing').remove();
       // for (i=0;i<doctorsByWardsSelected.length;i++)
        for(var key in doctorsByWardsSelected)
        {
            newWardTR = $('#timetableTemplates .oneRowDoctorsWardEditing').clone();
            // Выводим имя отделения
            $(newWardTR).find('.wardsColEditing').text(doctorsByWardsSelectedNames[key]);
            // Перебираем врачей внутри отделения
            for (j=0;j<doctorsByWardsSelected[key].length;j++)
            {
                // Вставляем имя доктора
                $(newWardTR).find('.doctorsColEditing').html(
                    $(newWardTR).find('.doctorsColEditing').html()+ doctorsByWardsSelected[key][j]
                );
                if (j!=doctorsByWardsSelected[key].length-1)
                {
                    $(newWardTR).find('.doctorsColEditing').html(
                        $(newWardTR).find('.doctorsColEditing').html()+ '<br>'
                    );
                }

            }

            // Вставляем в строку
            $('#edititngSheduleArea .timeTablesEditDoctorsWards tbody').append(newWardTR);

        }

    });

    globalVariables.timetableToDelete = null;
    globalVariables.timetableToDeleteHTML = null;
    $(document).on('click','.deleteSheduleButton',function(){
       // Удаляем график, но сначала надо спросить разрешения.
       //  Сначала прочитаем ид расписания
        globalVariables.timetableToDelete  = $(this).parents('.timetableReadOnly').find('input.timeTableId').val();
        globalVariables.timetableToDeleteHTML = $(this).parents('.timetableReadOnly');
        console.log('График для удаления ИД: '+globalVariables.timetableToDelete);
        $('#deleteTemplatePopup').modal({});
    });





});