$(document).ready(function() {
    var plot2 = $.jqplot ('chart2', [[3,7,9,1,4,6,8,2,5]], {
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
            xaxis: {
                label: "X Axis",
                // Turn off "padding".  This will allow data point to lie on the
                // edges of the grid.  Default padding is 1.2 and will keep all
                // points inside the bounds of the grid.
                pad: 0
            },
            yaxis: {
                label: "Y Axis"
            }
        }
    });








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
                name: 'id',
                index:'id',
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
                    if (data.success == true) {

                    }
                }
            })
            //------>



        }

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