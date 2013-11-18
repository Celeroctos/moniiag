$(document).ready(function() {
    $("#categories").jqGrid({
        url: globalVariables.baseUrl + '/index.php/admin/categories/get',
        datatype: "json",
        colNames:['Код', 'Название'],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 150
            },
            {
                name: 'name',
                index:'name',
                width: 250
            },
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#categoriesPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption:"Роли",
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
                'url' : '/index.php/admin/categories/getone?id=' + currentRow,
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
                'url' : '/index.php/admin/categories/delete?id=' + currentRow,
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
