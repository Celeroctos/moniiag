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
        ondblClickRow: editShift
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

    function editShift() {

    }

    // Отобразить ошибки формы добавления пациента
    $("#shedule-settings-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == 'true') { // Запрос прошёл удачно
            $('#successSheduleSettingsEditPopup').modal({

            });
        } else {
            // Удаляем предыдущие ошибки
            $('#errorSheduleSettingsEditPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorSheduleSettingsEditPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorSheduleSettingsEditPopup').modal({

            });
        }
    });

    $("#addShift").click(function() {
        $('#addShiftPopup').modal({

        });
    });

    $("#shift-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == 'true') { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addShiftPopup').modal('hide');
            // Перезагружаем таблицу
            $("#shifts").trigger("reloadGrid");
            $("#shift-add-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddShiftPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddShiftPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddShiftPopup').modal({

            });
        }
    });

    $("#shift-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == 'true') { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#editShiftPopup').modal('hide');
            // Перезагружаем таблицу
            $("#shifts").trigger("reloadGrid");
            $("#shift-edit-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddShiftPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddShiftPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddShiftPopup').modal({

            });
        }
    });

    function editShift() {
        var currentRow = $('#shifts').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/admin/modules/getoneshift?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        // Заполняем форму значениями
                        var form = $('#editShiftPopup form')
                        // Соответствия формы и модели
                        var fields = [
                            {
                                modelField: 'id',
                                formField: 'id'
                            },
                            {
                                modelField: 'time_begin',
                                formField: 'timeBegin'
                            },
                            {
                                modelField: 'time_end',
                                formField: 'timeEnd'
                            }
                        ];
                        for(var i = 0; i < fields.length; i++) {
                            form.find('#' + fields[i].formField).val(data.data[fields[i].modelField]);
                        }
                        $("#editShiftPopup").modal({

                        });
                    }
                }
            })
        }
    }

    $("#editShift").click(editShift);

    $("#deleteShift").click(function() {
        var currentRow = $('#shifts').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/admin/modules/deleteshift?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $("#shifts").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddShiftPopup .modal-body .row p').remove();
                        $('#errorAddShiftPopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddShiftPopup').modal({

                        });
                    }
                }
            })
        }
    });

    // Инициализация timePicker-ов
    $('#timeBegin-cont, #timeEnd-cont').datetimepicker({
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
});