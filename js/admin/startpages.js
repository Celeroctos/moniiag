$(document).ready(function() {
    $("#startpages").jqGrid({
        url: globalVariables.baseUrl + '/admin/roles/getstartpages',
        datatype: "json",
        colNames:['Код', 'Название', 'Адрес', 'Приоритет'],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 150
            },
            {
                name: 'name',
                index:'name',
                width: 150
            },
            {
                name: 'url',
                index: 'url',
                width: 150
            },
            {
                name: 'priority',
                index: 'priority',
                width: 150
            },
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#startpagesPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption: "Стартовые страницы для ролей",
        height: 300,
        ondblClickRow: editStartpage
    });

    $("#startpages").jqGrid('navGrid','#startpagesPager',{
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


    $("#addStartpage").click(function() {
        $('#addStartpagePopup').modal({

        });
    });

    $("#startpage-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addStartpagePopup').modal('hide');
            // Перезагружаем таблицу
            $("#startpages").trigger("reloadGrid");
            $("#startpage-add-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddStartpagePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddStartpagePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddStartpagePopup').modal({

            });
        }
    });

    $("#startpage-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#editStartpagePopup').modal('hide');
            // Перезагружаем таблицу
            $("#startpages").trigger("reloadGrid");
            $("#startpage-edit-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddStartpagePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddStartpagePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddStartpagePopup').modal({

            });
        }
    });

    function editStartpage() {
        var currentRow = $('#startpages').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/admin/roles/getonestartpage?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editStartpagePopup form')
                        // Соответствия формы и модели
                        var fields = [
                            {
                                modelField: 'id',
                                formField: 'id'
                            },
                            {
                                modelField: 'name',
                                formField: 'name'
                            },
                            {
                                modelField: 'url',
                                formField: 'url'
                            },
                            {
                                modelField: 'priority',
                                formField: 'priority'
                            }
                        ];
                        var startpage = data.data;

                        for(var i = 0; i < fields.length; i++) {
                            form.find('#' + fields[i].formField).val(startpage[fields[i].modelField]);
                        }
                        $("#editStartpagePopup").modal({

                        });
                    }
                }
            })
        }
    }

    $("#editStartpage").click(editStartpage);


    $("#deleteStartpage").click(function() {
        var currentRow = $('#startpages').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/admin/roles/deletestartpage?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $("#startpages").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddStartpagePopup .modal-body .row p').remove();
                        $('#errorAddStartpagePopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddStartpagePopup').modal({

                        });
                    }
                }
            })
        }
    });
});
