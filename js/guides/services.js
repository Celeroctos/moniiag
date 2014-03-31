$(document).ready(function() {
    $("#services").jqGrid({
        url: globalVariables.baseUrl + '/index.php/guides/service/get',
        datatype: "json",
        colNames:['ID',
                  'Код услуги в ТАСУ',
                  'Описание'
                  ],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 60
            },
            {
                name: 'tasu_code',
                index: 'tasu_code',
                width: 150
            },
            {
                name: 'name',
                index: 'name',
                width: 400
            }
        ],
        rowNum: 30,
        rowList:[10,20,30],
        pager: '#servicesPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption: "Медицинские услуги",
        height: 600,
        ondblClickRow: editService
    });

    $("#services").jqGrid('navGrid','#servicesPager',{
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

    $("#addService").click(function() {
        $('#addServicePopup').modal({
        });
    });


    $("#service-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового кабинета, перезагружаем jqGrid
            $('#addServicePopup').modal('hide');
            // Перезагружаем таблицу
            $("#services").trigger("reloadGrid");
            $("#service-add-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddServicePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddServicePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddServicePopup').modal({

            });
        }
    });

    // Редактирование строки
    $("#service-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        if(Boolean(globalVariables.guideEdit) == false) {
            return false;
        }
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового кабинета, перезагружаем jqGrid
            $('#editServicePopup').modal('hide');
            // Перезагружаем таблицу
            $("#services").trigger("reloadGrid");
            $("#service-edit-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddServicePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddServicePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddServicePopup').modal({

            });
        }
    });

    function editService() {
        var currentRow = $('#services').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/guides/service/getone?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editServicePopup form')
                        // Соответствия формы и модели
                        var fields = [
                            {
                                modelField: 'id',
                                formField: 'id'
                            },
                            {
                                modelField: 'tasu_code',
                                formField: 'code'
                            },
                            {
                                modelField: 'name',
                                formField: 'name'
                            }
                        ];
                        for(var i = 0; i < fields.length; i++) {
                            form.find('#' + fields[i].formField).val(data.data[fields[i].modelField]);
                        }
                        $("#editServicePopup").modal({
                        });
                    }
                }
            })
        }
    }


    $("#editService").click(editService);

    $("#deleteService").click(function() {
        var currentRow = $('#services').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/guides/service/delete?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $("#services").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddServicePopup .modal-body .row p').remove();
                        $('#errorAddServicePopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddServicePopup').modal({

                        });
                    }
                }
            });
        }
    });

    $("#syncService").click(function() {
        $(this).attr({
            'disabled' : true
        }).text('Идёт синхронизация с ТАСУ...');
        $.ajax({
            'url' : '/index.php/guides/service/syncwithtasu',
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success) {
                    $(this).attr({
                        'disabled' : false
                    }).text('Синхронизировать с ТАСУ (ТАСУ -> МИС)');
                    $("#services").trigger("reloadGrid");
                } else {
                    // Удаляем предыдущие ошибки
                    $('#errorAddServicePopup .modal-body .row p').remove();
                    $('#errorAddServicePopup .modal-body .row').append("<p>" + data.error + "</p>")

                    $('#errorAddServicePopup').modal({

                    });
                }
            }
        })
    });
});
