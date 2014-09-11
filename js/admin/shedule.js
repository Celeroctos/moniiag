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
});