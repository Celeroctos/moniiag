$(document).ready(function() {
    $("#categories").jqGrid({
        url: globalVariables.baseUrl + '/admin/categories/get',
        datatype: "json",
        colNames:['Код', 'Название', 'Родитель', 'Динамическая', 'Позиция', 'Полный путь', 'Раскрыта', '','', ''],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 80
            },
            {
                name: 'name',
                index:'name',
                width: 150
            },
            {
                name: 'parent',
                index: 'parent',
                width: 150
            },
            {
                name: 'is_dynamic_name',
                index: 'is_dynamic_name',
                width: 150
            },
            {
                name: 'position',
                index: 'position',
                width: 100
            },
            {
                name: 'path',
                index: 'path',
                width: 200
            },
            {
                name: 'wrapped',
                index: 'wrapped',
                width: 100
            },
            {
                name: 'is_dynamic',
                index: 'is_dynamic',
                hidden: true
            },
            {
                name: 'parent_id',
                index: 'parent_id',
                hidden: true
            },
            {
                name: 'is_wrapped',
                index: 'is_wrapped',
                hidden: true
            }
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#categoriesPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption:"Категории",
        height: 300,
        ondblClickRow: editCategorie
    });

    $("#categories").jqGrid('navGrid','#categoriesPager',{
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


    $("#addCategorie").click(function() {
        $('#addCategoriePopup').modal({

        });
    });

    $("#categorie-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addCategoriePopup').modal('hide');
            // Перезагружаем таблицу
            $("#categories").trigger("reloadGrid");
            $("#categorie-add-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddCategoriePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddCategoriePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddCategoriePopup').modal({

            });
        }
    });

    $("#categorie-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#editCategoriePopup').modal('hide');
            // Перезагружаем таблицу
            $("#categories").trigger("reloadGrid");
            $("#categorie-edit-form")[0].reset();

        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddCategoriePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddCategoriePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddCategoriePopup').modal({

            });
        }
    });


    function editCategorie() {
        var currentRow = $('#categories').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : globalVariables.baseUrl + '/admin/categories/getone?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editCategoriePopup form')
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
                                modelField: 'parent_id',
                                formField: 'parentId'
                            },
                            {
                                modelField: 'is_dynamic',
                                formField: 'isDynamic'
                            },
                            {
                                modelField: 'position',
                                formField: 'position'
                            },
                            {
                                modelField: 'is_wrapped',
                                formField: 'isWrapped'
                            }
                        ];
                        for(var i = 0; i < fields.length; i++) {
                            form.find('#' + fields[i].formField).val(data.data[fields[i].modelField]);
                        }
                        $("#editCategoriePopup").modal({

                        });
                    }
                }
            })
        }
    }

    $("#editCategorie").click(editCategorie);

    $("#deleteCategorie").click(function() {
        var currentRow = $('#categories').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : globalVariables.baseUrl + '/admin/categories/delete?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $("#categories").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddCategoriePopup .modal-body .row p').remove();
                        $('#errorAddCategoriePopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddCategoriePopup').modal({

                        });
                    }
                }
            })
        }
    });
});