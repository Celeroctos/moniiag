$(document).ready(function() {
    $("#patientsOnMonitor").jqGrid({
        url: globalVariables.baseUrl + '/index.php/doctors/patient/getmonitoring',
        datatype: "json",
        colNames:['ФИО', 'Тип мониторинга', 'Код',''],
        colModel:[
            {
                name:'fio',
                index:'fio',
                width: 200
            },
            {
                name:'name', // Имя мониторинга
                index:'name',
                width: 200
            },
            {
                name: 'monitoring_id',
                index:'monitoring_id',
                width: 50
            },
            {
                name: 'need_look',
                index:'need_look',
                width: 50,
                hidden:true
            }
        ],
       // rowNum: 10,
       // rowList:[10,20,30],
        pager: '#patientsOnMonitorPager',
        sortname: 'monitoring_id',
        viewrecords: true,
        sortorder: "desc",
        caption:"Пациенты",
        height: 300,
        onSelectRow: onSelectRow,
        loadComplete:onLoadComplete
    });


    function onLoadComplete()
    {
        console.log('LoadComplete');
        // Если в адресе есть строчка "alarm=1", то нужно перебрать
        //    строки и проверить на наличие измерений, превышающих пороговое значение
        if (location.href.indexOf('alarm=1')<0)
            return;
        var gridRows  = $('table#patientsOnMonitor tr:not(.jqgfirstrow)');
        for (i=0;i<gridRows.length;i++)
        {
            // Выцепляем ячейку с количеством непрочтённых показаний и проверяем количество показаний
            if ( $($(gridRows[i]).find('td[aria-describedby=patientsOnMonitor_need_look]')[0]).text()!='0' )
            {
                // Перекрашиваем строку
                console.log('Перекрашиваем');
                $(gridRows[i]).addClass('red-block');
            }
        }
    }

    // Переменная которая хранит таймер перерисовки графика
    //    Когда происходит выделение некоей строчки в гриде -
    //    отменяется таймер перерисовки и перерисовка запускается
    //   по-новому
    var plotRefreshTimeOut = null

    function onSelectRow(Args)
    {
        // Снимаем красную краску с ряда
        var gridRows  = $('table#patientsOnMonitor tr:not(.jqgfirstrow)');
        for (i=0;i<gridRows.length;i++)
        {
            // Выцепляем ячейку с ид, сравниваем её с Args и если совпало - убираем у ряда покраску в красный свет
            if ( $($(gridRows[i]).find('td[aria-describedby=patientsOnMonitor_monitoring_id]')[0]).text()==Args )
            {
                $(gridRows[i]).removeClass('red-block');
            }
        }
        if (plotRefreshTimeOut!=null)
            clearTimeout(plotRefreshTimeOut);
        printPlot();
    }

    function printPlot()
    {
        // Запускаем перерисовку раз в секунду
        plotRefreshTimeOut = setTimeout(printPlot,2000);
        // Ставим на
        // По ID-шнику мониторинга подкачиваю массив результатов
        var currentRow = $('#patientsOnMonitor').jqGrid('getGridParam', 'selrow');
        if (currentRow != null) {
            // Обращаемся по аяксу за результатом
            $.ajax({
                'url': '/index.php/doctors/patient/getmonitoringresults?monId=' + currentRow,
                'cache': false,
                'dataType': 'json',
                'type': 'GET',
                'success': function (data, textStatus, jqXHR) {
                    if (data.success == true || data.success == 'true') {
                        printChart(data.data);
                    }
                }
            })
        }

    }

    function printChart(data)
    {

        $('#chart2').empty();
        // Если data.result - Пустой - надо написать "Значений нет"
        if (data.results.length==0)
            $('#chart2').append('Не было проведено никаких измерений');
        // Из JQGrid-а считываем ФИО поциэнта
        var currentRow = $('#patientsOnMonitor').jqGrid('getGridParam', 'selrow');
        var rowData = $('#patientsOnMonitor').getRowData(currentRow);
        var fioPatient = rowData['fio'];
        console.log(fioPatient);
        // Из результатов обследования формируем массив для графика
        var maxVal = data.results[0].val;
        var minVal = data.results[0].val;
        var resultsToBuild = new Array();
        for (i=0;i<data.results.length;i++)
        {
            var onePoint = new Array();
            onePoint.push(data.results[i].time);
            onePoint.push(data.results[i].val);
            resultsToBuild.push(onePoint);
            if (data.results[i].val<parseFloat(minVal))
                minVal = data.results[i].val;

            if (data.results[i].val>parseFloat(maxVal))
                maxVal = data.results[i].val;
        }
        var graphicsSeries = new Array();
        graphicsSeries.push(resultsToBuild);
        // Построим график
        $.jqplot ('chart2', graphicsSeries, {
            title: fioPatient+': '+data.monitoring,
            axesDefaults: {
                labelRenderer: $.jqplot.CanvasAxisLabelRenderer


            },
            axes: {
                xaxis:{
                    renderer: $.jqplot.DateAxisRenderer,
                    pad: 0
                },
                yaxis:
                {
                    min: (parseFloat(minVal)-1),
                    max: (parseFloat(maxVal)+1)
                }
            }
        });
    }

    $("#patientsOnMonitor").jqGrid('navGrid','#patientsOnMonitorPager',{
            edit: false,
            add: false,
            del: false
        },
        {},
        {},
        {},
        {
            closeOnEscape:true,
            multipleSearch :true,
            closeAfterSearch: true
        }
    );
    $('#addMonitor').on('click',
        function()
        {
            $('#addMonitoringPopup').modal({
            });
        }
    );

});