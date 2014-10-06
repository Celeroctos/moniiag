$(document).ready(function() {
    return;
    var cont = $('.alerts-cont');
    $(cont).find('.panel-arrow').on('click', function(e) {
        if($(this).find('span').hasClass('glyphicon-expand')) {
           //show)
            $(cont).animate({
                'left' : '0'
            }, 500, function() {
                $(cont).find('.panel-arrow span').removeClass('glyphicon-expand').addClass('glyphicon-collapse-down');
            });
        } else {
            $(cont).animate({
                'left' : '-250px'
            }, 500, function() {
                $(cont).find('.panel-arrow span').removeClass('glyphicon-collapse-down').addClass('glyphicon-expand');
            });


        }
    });

    /*
    function showIndicators(cont)
    {
        $(cont).animate({
            'left' : '0'
        }, 500, function() {
            $(cont).find('.panel-arrow span').removeClass('glyphicon-expand').addClass('glyphicon-collapse-down');
        });
    }

    function closeIndicators(cont)
    {
        $(cont).animate({
            'left' : '-250px'
        }, 500, function() {
            $(cont).find('.panel-arrow span').removeClass('glyphicon-collapse-down').addClass('glyphicon-expand');
        });
    }
    */

    wasLoadedMessages = false;

    function refreshIndicators()
    {
        // Если панель удалённых показания свёрнута - выходим из функции
        /*if ($('.alerts-cont .panel-arrow span').hasClass('glyphicon-expand'))
        {

        }
        else
        {

            console.log('Тест');
        }*/
        console.log('Тест');
        return false;
        $.ajax({
            'url' : '/doctors/patient/getindicators',
            'data' : {
            },
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == true || data.success == 'true') {
                    var indicators = data.data;
                    var needRefreshList = false;

                    // Переберём данные
                    for (i=0;i<indicators.length;i++)
                    {
                        // Смотрим - если в списке нет хотя бы одного блока с айдишником
                        if ($('#'+'indicatorId'+indicators[i].idRecord).length<=0)
                        {
                            needRefreshList = true;
                            break;
                        }
                    }

                    if (!needRefreshList)
                        return false;
                    // Очищаем div
                    var listOfIndicators = $('.alerts-cont .main-window').empty();
                    $(listOfIndicators).empty()

                    for (i=0;i<indicators.length;i++)
                    {
                        // Дописываем в "main-window" полученные элементы
                        var newBlock = $('<div class = \'indicatorBlock\' id=\'indicatorId'+ indicators[i].idRecord +'\'>');
                        $(newBlock).append(
                            '<span class=\'indicatorPatient\'>'+ indicators[i].patientName +'<\/span><br>'
                        );

                        $(newBlock).append(
                            '<span class=\'indicatorName\'>'+ indicators[i].indicatorName  + ' | ' + indicators[i].dateStamp +'<\/span><br>'
                        );
                        $(newBlock).append(
                            '<span class=\'indicatorValue\'>'+ indicators[i].value +'<\/span>'
                        );

                        // Если значение больше порогового (пусть пороговое значение будет равно 5)
                        if (indicators[i].value>5)
                        {
                            $(newBlock).css('background-color','#FF0000');
                        }

                        $(listOfIndicators).append(newBlock);

                    }
                    // Прокручиваем вниз до конца див со списком показания
                    $(listOfIndicators)[0].scrollTop = $(listOfIndicators)[0].scrollHeight;
                    if (wasLoadedMessages)
                    {
                        $('#incomingIndicator')[0].play();
                    }
                    wasLoadedMessages = true;
                } else {

                }
            }
        });

        // Устанавливаем тайм-аут
        setTimeout(refreshIndicators,1000);
    }
    setTimeout(refreshIndicators,1000);

});