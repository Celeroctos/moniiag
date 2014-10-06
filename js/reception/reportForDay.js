$(document).ready(function() {
    $("#workForDay").jqGrid({
      //  url: globalVariables.baseUrl + '/guides/employees/get',
        datatype: "json",
        colNames:[
            'ЭМК (Новый)',
            'ФИО',
            'ЭМК (Старый)',
            'Полис',
            'Регистратор',
            'Врач'
        ],
        colModel:[
            {
                name:'card_number',
                index:'card_number',
                width: 50
            },
            {
                name: 'fio',
                index: 'fio',
                width: 80
            },
            {
                name: 'old_card_number',
                index: 'old_card_number',
                width: 50,
                search: false
            },
            {
                name: 'oms',
                index: 'oms',
                width: 60,
                search: false
            },
            {
                name: 'fio_registrator',
                index:'fio_registrator',
                width: 80
               // hidden: true,
               /* searchoptions: {
                    searchhidden: true
                }*/
            },
            {
                name: 'fio_doctor',
                index: 'fio_doctor',
                width: 80
               // hidden: true,
                /*searchoptions: {
                    searchhidden: true
                }*/
            },
        ],
        rowNum: 0,
        rowList:[15,30,50],
        pager: '#workForDayPager'
        ,sortname: 'policy_id'
        ,
        viewrecords: true,
        sortorder: "desc",
        caption:"Отчёт за день",
        height: 550,
        width: 1000
        //,
        //editurl:"someurl.php",
        //ondblClickRow: editEmployee
    });

    $("#workForDay").jqGrid('navGrid','#workForDayPager',{
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
    $('#reportDate').val((new Date).getFullYear() + '-' + ((new Date).getMonth() + 1) + '-' + (new Date).getDate());
    $('#reportDate').trigger('change');


    $('#reportForDayViewSubmit').on('click',function(e){
        // Надо бы обновить грид...
       // $('#workForDay').jqGrid('clearGridData');
        $("#workForDay").jqGrid('setGridParam',{
            url : globalVariables.baseUrl + '/reception/reports/getreportforday?date=' + $('#reportDate').val(),
            page: 1
        });


        $("#workForDay").trigger("reloadGrid");
       // return false;

    });

    $('#reportForDayViewPrint').on(
        'click',
        function(e){
            // Если у грида есть параметр url - то надо
            //   1. взять
            if ( $('#workForDay').getGridParam('url')!= '' &&
                $('#workForDay').getGridParam('url')!= null &&
                $('#workForDay').getGridParam('url')!= undefined
                )
            {

                var patientGrid = $('#workForDay')
                var printWin = window.open(
                    ''
                    , '', 'width=800,height=600,menubar=no,location=no,resizable=no,scrollbars=yes,status=no');
                printWin.focus();
                $(     $($(printWin).document)  ).ready(function() {
                    var date = $('#reportDate').val();
                    var parts = date.split('-');
                    var dateDiv = $('<div>').html($('<strong class="bold">').css({
                        // 'color' : '#FA5858',
                        'font-size' : '20px'
                    }).text('Отчёт о работе за  ' + parts[2] + '.' + parts[1] + '.' + parts[0] + ' г.'));
                    $('body', printWin.document).append(dateDiv);
                });

                // Дальше делаем таблицу ^_^
                var tableToOut =
                    $('<table>');
                $(tableToOut).append('<thead style="font-size:14px;font-weight:bold;">');
                $(tableToOut).append('<tbody style="font-size:12px;">');
                $(tableToOut).find('thead').append('<tr>');
                $(tableToOut).find('thead tr').append('<td>ЭМК (Новый)</td>');
                $(tableToOut).find('thead tr').append('<td>ФИО</td>');
                $(tableToOut).find('thead tr').append('<td>ЭМК (Старый)</td>');
                $(tableToOut).find('thead tr').append('<td>Полис</td>');
                $(tableToOut).find('thead tr').append('<td>Регистратор</td>');
                $(tableToOut).find('thead tr').append('<td>Врач</td>');


                // Перебираем грид и выплёвываем в таблицу значения всiх полей
                var rows = jQuery("#workForDay").getDataIDs();
                for(a=0;a<rows.length;a++)
                {
                    row=jQuery("#workForDay").getRowData(rows[a]);
                    //row.colname1;
                    //row.colname2;

                    var trToInsert = $('<tr>');
                    $(trToInsert).append( $('<td>').html(row.card_number));
                    $(trToInsert).append( $('<td>').html(row.fio));
                    $(trToInsert).append( $('<td>').html(row.old_card_number));
                    $(trToInsert).append( $('<td>').html(row.oms));
                    $(trToInsert).append( $('<td>').html(row.fio_registrator));
                    $(trToInsert).append( $('<td>').html(row.fio_doctor));
                    $(tableToOut).find('tbody').append( $(trToInsert) );
                }

                $(tableToOut).find('td').css({
                    'border' : '1px solid #D4D0C8',
                    'border-collapse' : 'collapse',
                    'padding' : '3px 5px'
                });
                $('body', printWin.document).append(tableToOut);
            }
            return false;
        });

    /*
    $("#motion-history").jqGrid('setGridParam',{
        'datatype' : 'json',
        'url' : globalVariables.baseUrl + '/reception/patient/gethistorymotion/?omsid=' + omsId
    }).trigger("reloadGrid");
    */
    //console.log("'"+$('#workForDay').getGridParam('url')+"'");

});

