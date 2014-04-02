$(document).ready(function() {
    $("#streets").jqGrid({
        url: globalVariables.baseUrl + '/index.php/guides/cladr/streetget',
        datatype: "json",
        colNames:['ID',
                  'Регион',
                  'Район',
                  'Населённый пункт',
                  'Код КЛАДР',
                  'Название'
                  ],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 60
            },
            {
                name: 'region',
                index: 'region',
                width: 150
            },
            {
                name: 'district',
                index: 'district',
                width: 150
            },
            {
                name: 'settlement',
                index: 'settlement',
                width: 150
            },
            {
                name: 'code_cladr',
                index: 'code_cladr',
                width: 150
            },
            {
                name: 'name',
                index: 'name',
                width: 200
            }
        ],
        rowNum: 30,
        rowList:[10,20,30],
        pager: '#streetsPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption: "Районы",
        height: 600,
        ondblClickRow: editStreet
    });

    $("#streets").jqGrid('navGrid','#streetsPager',{
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

    $("#addStreet").click(function() {
        $('#addStreetPopup').modal({
        });
    });


    $("#street-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового кабинета, перезагружаем jqGrid
            $('#addStreetPopup').modal('hide');
            // Перезагружаем таблицу
            $("#streets").trigger("reloadGrid");
            $("#street-add-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddStreetPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddStreetPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddStreetPopup').modal({
            });
        }
    });

    // Редактирование строки
    $("#street-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        if(Boolean(globalVariables.guideEdit) == false) {
            return false;
        }
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового кабинета, перезагружаем jqGrid
            $('#editStreetPopup').modal('hide');
            // Перезагружаем таблицу
            $("#streets").trigger("reloadGrid");
            $("#street-edit-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddStreetPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddStreetPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddStreetPopup').modal({
            });
        }
    });

    function editStreet() {
        var currentRow = $('#streets').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/guides/cladr/streetgetone?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editStreetPopup form')
                        // Соответствия формы и модели
                        var fields = [
                            {
                                modelField: 'id',
                                formField: 'id'
                            },
                            {
                                modelField: 'code_cladr',
                                formField: 'codeCladr'
                            },
                            {
                                modelField: 'name',
                                formField: 'name'
                            }
                        ];
                        for(var i = 0; i < fields.length; i++) {
                            form.find('#' + fields[i].formField).val(data.data[fields[i].modelField]);
                        }
                        $("#editStreetPopup").modal({
                        });
                    }
                }
            })
        }
    }


    $("#editStreet").click(editStreet);

    $("#deleteStreet").click(function() {
        var currentRow = $('#streets').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/guides/cladr/streetdelete?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $("#streets").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddStreetPopup .modal-body .row p').remove();
                        $('#errorAddStreetPopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddStreetPopup').modal({

                        });
                    }
                }
            });
        }
    });
});
