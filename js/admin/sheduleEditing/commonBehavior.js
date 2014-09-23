/*
Этот файл для реализации общего поведения страницы-редактора расписания.
Здесь описаны запрос и получение данных от сервера, логика открытия страницы редактора.
 */
$(document).ready(function () {

    InitPaginationList('existingTimeTablesList','tt.id','desc',refreshTimeTableList)

    $('#wardSelect').on(
        'change',
        function(e)
        {
            // Если выбраны все отделения - выводим всех докторов
            if ( $(this).find('option[value="-1"]:selected').length>0 )
            {
                $('#doctorsSelect option').removeClass('no-display');
            }
            else
            {
                selectedWards = $(this).find('option:selected');
                selectedWardsIds = [];
                for(i=0;i< $(selectedWards).length;i++ )
                {
                    selectedWardsIds.push(  $($(selectedWards)[i]).val()  );
                }

                isWithoutWard = ($(this).find('option[value="-2"]:selected').length>0);

                // А теперь перебираем докторов и смотрим - если их отделения в выбранных
                allDoctorsFromSelect = $('#doctorsSelect option');
                doctorsToHide = [];
                for (i=0;i<$(allDoctorsFromSelect).length;i++)
                {
                    // Если у врача val=-1, то показываем его всегда
                    if ($($(allDoctorsFromSelect)[i]).val()==-1)
                    {
                        continue;
                    }

                    wardForThisDoctor = globalVariables.doctorsForWards[  $($(allDoctorsFromSelect)[i]).val() ];

                    // Если у врача нет отделения - т.е. оно равно null, то надо проверить - выбрано ли отделение "без отделения"
                    if (wardForThisDoctor==null)
                    {
                        if (isWithoutWard)
                        {
                            // Показываем опшен
                            $($(allDoctorsFromSelect)[i]).removeClass('no-display');
                        }
                        else
                        {
                            // Скрываем опшен
                            $($(allDoctorsFromSelect)[i]).addClass('no-display');
                            $($(allDoctorsFromSelect)[i]).attr('selected', false);

                        }
                    }
                    else
                    {
                        // У доктора задано отделение
                        wasWardFound = false;
                        // Поиск отделения в спискоте выбранных
                        for(j=0;j<selectedWardsIds.length;j++)
                        {
                            if (selectedWardsIds[j]==wardForThisDoctor)
                            {
                                wasWardFound=true;
                            }
                        }


                        if (wasWardFound)
                        {
                            // Показываем доктора
                            $($(allDoctorsFromSelect)[i]).removeClass('no-display');
                        }
                        else
                        {
                            // Скрываем доктора
                            $($(allDoctorsFromSelect)[i]).addClass('no-display');
                            $($(allDoctorsFromSelect)[i]).attr('selected', false);
                        }

                    }
                }

            }
        }
    );

    $('#wardSelect, #doctorsSelect').on(
        'change',
        function()
        {
            //
            if ($(this).is('#doctorsSelect')  )
            {
                if ( $(this).find('option[value=-1]').is(':selected') )
                {
                    // Снять выделение с "Все врачи", выделить всех остальных
                    $('#doctorsSelect').find('option[value=-1]').prop('selected',false);
                    $('#doctorsSelect').find('option:not([value=-1])').prop('selected', true);
                }

            }

            refreshEditorDoctors();
            refreshTimeTableList();
        }
    );

    function refreshEditorDoctors()
    {
        // Если редактор открыт - нужно перепрочитать список врачей и отделений
        if ( $('#edititngSheduleArea').hasClass('no-display')==false  )
        {
            $(document).trigger('refreshDoctorsListEditor');
        }
    }

    function refreshTimeTableList()
    {
        // Собираем id-шники выбранных отделений и врачей
        selectedWards = $('#wardSelect').val();
        selectedDoctors = $('#doctorsSelect').val();
        console.log(selectedWards);
        console.log(selectedDoctors);
        // Если selectedDoctors или selectedWards равен нулю - то надо подать массив из одного элемента -1
        /*if (selectedWards ==null)
        {
            selectedWards = $.toJSON( [-1] );
        }

        if (selectedDoctors ==null)
        {
            selectedDoctors = $.toJSON( [-1] );
        }*/
        // Отправляем запрос
        params = {};
        if (selectedDoctors!=null)
        {
            params.doctors = selectedDoctors;
        }

        if (selectedWards!=null)
        {
            params.wards = selectedWards;
        }

        console.log(params);
        $.ajax({
            'url' : '/index.php/admin/shedule/getshedule?'+getPaginationParameters('existingTimeTablesList'),
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'data': params,
            'success' : function(data, textStatus, jqXHR) {
                onShedulesRecieve(data);
            }
        });




    }

    refreshTimeTableList();

    function onShedulesRecieve(dataObject)
    {
        if (dataObject.success==true || dataObject.success=="true")
        {
            if (dataObject.rows.length>0)
            {
                $('#existingSheduleAreaList').empty();
                // Внутри обработчика запроса перебираем графики и выводим их в виде таблицы
                printExistingTimetables(dataObject.rows);
                // Скрываем кнопочку "Сопоставить"
                $('.addingNewSheduleContainer').addClass('no-display');
                $('#existingSheduleArea').removeClass('no-display');

            }
            else
            {
                $('#existingSheduleArea').addClass('no-display');
                $('#existingSheduleAreaList').empty();
                // Делаем видимой кнопочку "Сопоставить"
                //     (если не открыт блок редактирования)
                if (  $('#edititngSheduleArea').hasClass('no-display')  )
                {
                    // Если в списке докторов кто-то выбран
                    if ( $('#doctorsSelect').find('option:selected').length!=0 )
                    {
                        $('.addingNewSheduleContainer').removeClass('no-display');
                    }
                    else
                    {
                        $('.addingNewSheduleContainer').addClass('no-display');
                    }
                }
            }
        }

    }

    function printExistingTimetables(timeTableRows)
    {
        //
        for (rowTimetableCounter=0;rowTimetableCounter<timeTableRows.length;timeTableRows++)
        {
            existingTimeTableContainer = $('<div>');
            $(existingTimeTableContainer).addClass('existingTimeTableContainer');
            $(existingTimeTableContainer).html(
                $.fn['sheduleEditor.port.JSONToPreview'].getHTML(timeTableRows)
            );

            // Добавляем в блок спискоты существующих графиков тот, который мы вывели
            $('#existingSheduleAreaList').append(existingTimeTableContainer);

        }
    }

    function startSheduleEdit()
    {

    }

    function startSheduleAdd()
    {


    }

    function startSheduleEditor(shedule)
    {
        // Если shedule не определено - то значит надо создать новое расписание
        //   если определено - то значит надо открыть для редактирования

    }

    $('.addingNewShedule').click(
        function()
        {

            //test ='{"rules":[{"cabinet":"18","days":{"1":[],"2":["1"],"3":[],"4":["2"],"5":[],"6":["3"],"7":[]},"oddance":0,"except":"4","daysDates":["2014-09-01","2014-09-02"],"workingBegin":"11:11","workingEnd":"22:22","greetingBegin":"3:33","greetingEnd":"4:44","limits":{"1":{"quantity":"5","begin":"5:55","end":"5:55"},"2":{},"3":{}}}],"facts":[{"type":"1","isRange":"1","begin":"2014-09-01","end":"2014-09-08"},{"type":"4","isRange":"0","begin":"2014-09-24","end":""}]}';
            $.fn['timetableEditor'].startAdding();
            //$.fn['timetableEditor'].startEditing(test);
        }
    );

    $(document).on('click','.cancelSheduleButton',function(){
        // Надо сделать невидимым блок редактирования
        $('#edititngSheduleArea').empty();
        $('#edititngSheduleArea').addClass('no-display');
        refreshTimeTableList();

    });

    $(document).on('click',
        '.addingNewSheduleRoom, .addingNewSheduleDays, .addingNewSheduleHourWork, .addingNewSheduleHourGreeting, .addingNewSheduleLimit',
        function()
        {
            $.fn['timetableEditor'].addRowInEditor();
        }
    );




    /*$(document).on('click','.saveSheduleButton',function (){
        console.log(
            $.fn['sheduleEditor.port.editorToJSON'].getResult()

        );
    });*/

    function onSaveSheduleEnd(returningData)
    {
        if (returningData.success==true || returningData.success=='true')
        {
            // 1. Выводим сообщение, что всё хорошо
            // 2. Закрываем редактор
            // 3. Обновляем список выведенных графиков
            $('#successEditPopup').modal({});
            $.fn['timetableEditor'].closeEditing();
            refreshTimeTableList();
        }
        else
        {
            // Выводим ошибку

        }
    }

    $(document).on('click','.saveSheduleButton',function (){
        // 1. Читаем данные из интерфейса
        // Читаем докторов
        doctorsIds = $('#doctorsSelect').val();
        //console.log(doctorsIds);

        // Читаем JSON
        timetableJSON = $.fn['sheduleEditor.port.editorToJSON'].getResult();

        // Читаем года
        dateBegin = $('#edititngSheduleArea').find('.sheduleBeginDateTime').val();
        dateEnd = $('#edititngSheduleArea').find('.sheduleEndDateTime').val();

        timetableId = $('#edititngSheduleArea').find('.timeTableId').text();

        timeTableDataContainer = {
            'doctors':$.toJSON(doctorsIds),
            'timeTableBody': timetableJSON,
            'timeTableId': timetableId,
            'begin' : dateBegin,
            'end': dateEnd
        }
        // 2. Отправляем их серверу
        $.ajax({
            'url' : '/index.php/admin/shedule/save',
            'cache' : false,
            'dataType' : 'json',
            'data': timeTableDataContainer,
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                // 3. Вызываем call-back
                onSaveSheduleEnd(data);
            }
        });


    });



});