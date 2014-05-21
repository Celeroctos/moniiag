$(document).ready(function() {
   /* var plot2 = $.jqplot ('chart2', [[3,7,9,1,4,6,8,2,5]], {
        // Give the plot a title.
        title: 'Plot With Options',
        // You can specify options for all axes on the plot at once with
        // the axesDefaults object.  Here, we're using a canvas renderer
        // to draw the axis label which allows rotated text.
        axesDefaults: {
            labelRenderer: $.jqplot.CanvasAxisLabelRenderer
        },
        // An axes object holds options for all axes.
        // Allowable axes are xaxis, x2axis, yaxis, y2axis, y3axis, ...
        // Up to 9 y axes are supported.
        axes: {
            // options for each axis are specified in seperate option objects.

        }
    });
    */







    $("#patientsOnMonitor").jqGrid({
        url: globalVariables.baseUrl + '/index.php/doctors/patient/getmonitoring',
        datatype: "json",
        colNames:['ФИО', 'Тип мониторинга', 'Код'],
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
        ],
       // rowNum: 10,
       // rowList:[10,20,30],
        pager: '#patientsOnMonitorPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption:"Пациенты",
        height: 300,
        onSelectRow: printPlot
    });

    function printPlot()
    {
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
            //------>



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


            //resultsToBuild.push(data.results[i].val);
        }
        var graphicsSeries = new Array();
        graphicsSeries.push(resultsToBuild);

/*
        var line1=[['2008-06-30 8:00AM',4], ['2008-7-30 8:00AM',6.5], ['2008-8-30 8:00AM',5.7], ['2008-9-30 8:00AM',9], ['2008-10-30 8:00AM',8.2]];
        var plot2 = $.jqplot('chart2', [line1], {
            title:'Customized Date Axis',
            gridPadding:{right:35},
            axes:{
                xaxis:{
                    renderer:$.jqplot.DateAxisRenderer,
                    tickOptions:{formatString:'%b %#d, %y'},
                    min:'May 30, 2008',
                    tickInterval:'1 month'
                }
            },
            series:[{lineWidth:4, markerOptions:{style:'square'}}]
        });
        return;
*/
        //console.log(minVal);

        //console.log(maxVal);

        // Построим график
        $.jqplot ('chart2', graphicsSeries, {
            // Give the plot a title.
            title: fioPatient+': '+data.monitoring,
            // You can specify options for all axes on the plot at once with
            // the axesDefaults object.  Here, we're using a canvas renderer
            // to draw the axis label which allows rotated text.
            axesDefaults: {
                labelRenderer: $.jqplot.CanvasAxisLabelRenderer


            },
            // An axes object holds options for all axes.
            // Allowable axes are xaxis, x2axis, yaxis, y2axis, y3axis, ...
            // Up to 9 y axes are supported.
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

                /*// options for each axis are specified in seperate option objects.
                xaxis:
                {
                    $.jqplot.DateAxisRenderer
                }*/
            }
        });



        //
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