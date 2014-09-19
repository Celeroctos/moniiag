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

            //test ='{"rules":[{"cabinet":"18","days":{"1":[],"2":["1"],"3":[],"4":["2"],"5":[],"6":["3"],"7":[]},"oddance":0,"except":"4","daysDates":["2014-09-01","2014-09-02"],"workingBegin":"11:11","workingEnd":"22:22","greetingBegin":"3:33","greetingEnd":"4:44","limits":{"1":{"quantity":"5","begin":"5:55","end":"5:55"},"2":{},"3":{}}}],"facts":[{"type":"1","isRange":"1","begin":"2014-09-01","end":"2014-09-08"},{"type":"4","isRange":"0","begin":"2014-09-24","end":""}]}';
            $.fn['timetableEditor'].startAdding();
            //$.fn['timetableEditor'].startEditing(test);
        }
    );

    $(document).on('click',
        '.addingNewSheduleRoom, .addingNewSheduleDays, .addingNewSheduleHourWork, .addingNewSheduleHourGreeting, .addingNewSheduleLimit',
        function()
        {
            $.fn['timetableEditor'].addRowInEditor();
        }
    );


    $(document).on('click','.saveSheduleButton',function (){
        console.log(
            $.fn['sheduleEditor.port.editorToJSON'].getResult()

        );
    });


});