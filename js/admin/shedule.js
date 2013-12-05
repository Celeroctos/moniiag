$(document).ready(function() {
    $("#shifts").jqGrid({
        url: globalVariables.baseUrl + '/index.php/admin/modules/getshifts',
        datatype: "json",
        colNames:['Код', 'Начало приёма', 'Конец приёма'],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 150
            },
            {
                name: 'time_begin',
                index: 'time_begin',
                width: 200
            },
            {
                name: 'time_end',
                index:'time_end',
                width: 150
            },
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#shiftsPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption: "Смены",
        height: 100,
        ondblClickRow: installShift
    });

    $("#shifts").jqGrid('navGrid','#shiftsPager',{
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

    $('#doctor-search-submit').click(function(e) {
        var filters = {
            'groupOp' : 'AND',
            'rules' : [
                {
                    'field' : 'ward_code',
                    'op' : 'eq',
                    'data' :  $('#ward').val()
                },
                {
                    'field' : 'post_id',
                    'op' : 'eq',
                    'data' : $('#post').val()
                },
                {
                    'field' : 'middle_name',
                    'op' : 'cn',
                    'data' : $('#middleName').val()
                },
                {
                    'field' : 'last_name',
                    'op' : 'cn',
                    'data' : $('#lastName').val()
                },
                {
                    'field' : 'first_name',
                    'op' : 'cn',
                    'data' : $('#firstName').val()
                }
            ]
        };

        // Делаем поиск
        $.ajax({
            'url' : '/index.php/reception/doctors/search/?filters=' + $.toJSON(filters),
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                $('#sheduleEditCont').hide();
                if(data.success == true) {
                    // Изначально таблицы скрыты
                    $('#withoutCardCont').addClass('no-display');

                    if(data.data.length == 0) {
                        $('#notFoundPopup').modal({
                        });
                    } else {
                        displayAllDoctors(data.data);
                    }
                } else {
                    $('#errorSearchPopup .modal-body .row p').remove();
                    $('#errorSearchPopup .modal-body .row').append('<p>' + data.data + '</p>')
                    $('#errorSearchPopup').modal({

                    });
                }
                return;
            }
        });
        return false;
    });

    function displayAllDoctors(data) {
        var table = $('#searchDoctorsResult tbody');
        table.find('tr').remove();
        for(var i = 0; i < data.length; i++) {
            var cabinetsStr = '';
            for(var j = 0; j < data[i].cabinets.length; j++) {
                if(j > 0) {
                    cabinetsStr += ', ';
                }
                cabinetsStr += '<a href="#">' + data[i].cabinets[j].description + '</a>';
            }
            table.append(
                '<tr>' +
                    '<td>' +
                        '<a title="Установить для данного сотрудника расписание" href="#employee' + data[i].id + '">' +
                    data[i].last_name + ' ' + data[i].first_name + ' ' + data[i].middle_name +
                        '</a>' +
                    '</td>' +
                    '<td>' +
                        data[i].post +
                    '</td>' +
                    '<td>' +
                        data[i].ward +
                    '</td>' +
                '</tr>'
            );
        }
        table.parents('div.no-display').removeClass('no-display');
    }


    // Отобразить форму редактирования расписания при клике на ссылку
    $(document).on('click', 'a[href^=#employee]', function(e) {
        var attr = $(this).attr('href');
        var id = parseInt(attr.substr(9));
        $('input[id^=doctorId]').val(id);
        $('#sheduleEditCont').slideDown(500);
    });

    // Радиокнопка смены типа расписания
    $('input[name=sheduleType]').on('click', function(e) {
        if($(this).val() == 0) {
            $('#sheduleShifts').slideDown(500);
        } else {
            $('#sheduleShifts').hide();
        }
    });

    // Календари времени на контролы расписания
    $('div[id^=timeBegin], div[id^=timeEnd]').datetimepicker({
        language:  'ru',
        format: 'h:i',
        weekStart: 1,
        todayBtn:  1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 1,
        minView: 0,
        maxView: 1,
        forceParse: 0
    })

    // Календарь на день-исключение
   $('#day0').datetimepicker({
       language: 'ru',
       format: 'yyyy-mm-dd',
       weekStart: 1,
       todayBtn:  1,
       autoclose: 1,
       todayHighlight: 1,
       startView: 2,
       minView: 2,
       forceParse: 0
   });

    function installShift(rowId, status, e) {
        var currentRow = $('#shifts').jqGrid('getRowData', rowId);
        if(currentRow != null) {
            $('#shedule-by-day-form input[id^=timeBegin]').val(currentRow.time_begin);
            $('#shedule-by-day-form input[id^=timeEnd]').val(currentRow.time_end);
        }
    }
});