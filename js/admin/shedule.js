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
        // Здесь - запрос к базе: мб, уже есть установленное раписание, тогда его надо вывести
        $.ajax({
            'url' : '/index.php/admin/shedule/get?id=' + id,
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == 'true') {
                    // Формирование раписания из уже имеющихся данных
                    var shedule = data.data.data;
                    if(data.data.dateBegin != null) {
                        $('#dateBegin').val(data.data.dateBegin);
                    }
                    if(data.data.dateEnd != null) {
                        $('#dateEnd').val(data.data.dateEnd);
                    }
                    // Здесь удаляем все строки кроме первой
                    $('#shedule-exp-form tbody').find('tr:not(:first)').remove();

                    var numExps = 0;
                    for(var i = 0; i < shedule.length; i++) {
                        // Если конкретного дня нет - значит, это расписание общее (0)
                        if(shedule[i].type == 0) {
                            var form = $('#shedule-by-day-form');
                            $(form).find('#timeBegin' + shedule[i].weekday).val(shedule[i].timeBegin);
                            $(form).find('#timeEnd' + shedule[i].weekday).val(shedule[i].timeEnd);
                            $(form).find('#cabinet' + shedule[i].weekday).val(shedule[i].cabinetId);
                        }
                        if(shedule[i].type == 1) {
                            var form = $('#shedule-exp-form');
                            $(form).find('#id' + numExps).val(shedule[i].id);
                            $(form).find('#doctorId' + numExps).val(shedule[i].employeeId);
                            $(form).find('#day' + numExps).val(shedule[i].day);
                            $(form).find('#timeBegin' + numExps).val(shedule[i].timeBegin);
                            $(form).find('#timeEnd' +  numExps).val(shedule[i].timeEnd);
                            $(form).find('#cabinet' +  numExps).val(shedule[i].cabinetId);
                            $('#doctor-exp-add').trigger('click');
                            ++numExps;
                        }
                    }
                    $('#sheduleEditCont').slideDown(500);
                }
                return;
            }
        });
    });

    // Радиокнопка смены типа расписания
    $('input[name=sheduleType]').on('click', function(e) {
        if($(this).val() == 0) {
            $('#sheduleShifts').slideDown(500);
        } else {
            $('#sheduleShifts').hide();
        }
    });

    // Календарь на день-исключение
   function initExpCalendar() {
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
       });

        $('div[id^=day]').datetimepicker({
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
   };
   initExpCalendar();

    function installShift(rowId, status, e) {
        var currentRow = $('#shifts').jqGrid('getRowData', rowId);
        if(currentRow != null) {
            $('#shedule-by-day-form input[id^=timeBegin]').val(currentRow.time_begin);
            $('#shedule-by-day-form input[id^=timeEnd]').val(currentRow.time_end);
        }
    }

    $("#shedule-exp-form, #shedule-by-day-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == 'true') { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#successPopup').modal({

            });
        } else {
            // Удаляем предыдущие ошибки
            $('#errorPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorPopup').modal({

            });
        }
    });

    $('#doctor-exp-add').click(function(e) {
        var node = $('#shedule-exp-table tr:eq(1)').clone();
        var collLength = $('#shedule-exp-table tr').size();
        // Заменили имена ноды
        $(node).find('input[id^=id]').prop({
            'name' : 'FormSheduleExpAdd[' + (collLength - 1) + '][id]',
            'id' : 'id' + (collLength - 1)
        }).val('');
        $(node).find('input[id^=day]').prop({
            'name' : 'FormSheduleExpAdd[' + (collLength - 1) + '][day]',
            'id' : 'day' + (collLength - 1)
        }).val('');
        $(node).find('input[id^=timeBegin]').prop({
            'name' : 'FormSheduleExpAdd[' + (collLength - 1) + '][timeBegin]',
            'id' : 'timeBegin' + (collLength - 1)
        }).val('');
        $(node).find('input[id^=timeEnd]').prop({
            'name' : 'FormSheduleExpAdd[' + (collLength - 1) + '][timeEnd]',
            'id' : 'timeEnd' + (collLength - 1)
        }).val('');
        $(node).find('input[id^=doctorId]').prop({
            'name' : 'FormSheduleExpAdd[' + (collLength - 1) + '][doctorId]',
            'id' : 'doctorId' + (collLength - 1)
        });
        $(node).find('select').prop({
            'name' : 'FormSheduleExpAdd[' + (collLength - 1) + '][cabinet]',
            'id' : 'cabinet' + (collLength - 1)
        });

        $(node).insertAfter('#shedule-exp-table tr:last');
        initExpCalendar();
    });
});