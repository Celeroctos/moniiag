/*
Этот файл для реализации общего поведения страницы-редактора расписания.
Здесь описаны запрос и получение данных от сервера, логика открытия страницы редактора.
 */
$(document).ready(function () {


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
            refreshTimeTableList();
        }
    );

    function refreshTimeTableList()
    {
        // Собираем id-шники выбранных отделений и врачей
        selectedWards = $('#wardSelect').val();
        selectedDoctors = $('#doctorsSelect').val();
        console.log(selectedWards);
        console.log(selectedDoctors);
        // Если selectedDoctors или selectedWards равен нулю - то надо подать массив из одного элемента -1
        if (selectedWards ==null)
        {
            selectedWards = $.toJSON( [-1] );
        }

        if (selectedDoctors ==null)
        {
            selectedDoctors = $.toJSON( [-1] );
        }
        // Отправляем запрос
        params = {
            'doctors': selectedDoctors,
            'wards': selectedWards
        };
        $.ajax({
            'url' : '/index.php/admin/shedule/getshedule',
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
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
            if (dataObject.shedules.length>0)
            {
                // Внутри обработчика запроса перебираем графики и выводим их в виде таблицы
                // Скрываем кнопочку "Сопоставить"
                $('.addingNewSheduleContainer').addClass('no-display');
            }
            else
            {
                // Делаем видимой кнопочку "Сопоставить"
                //     (если не открыт блок редактирования)
                if (  $('#edititngSheduleArea').hasClass('no-display')  )
                {
                    $('.addingNewSheduleContainer').removeClass('no-display');
                }
            }
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
            $.fn['timetableEditor'].startAdding();
        }
    );

    $(document).on('click',
        '.addingNewSheduleRoom, .addingNewSheduleDays, .addingNewSheduleHourWork, .addingNewSheduleHourGreeting, .addingNewSheduleLimit',
        function()
        {
            $.fn['timetableEditor'].addRowInEditor();
        }
    );
});