$(document).ready(function() {
    $("#templates").jqGrid({
        url: globalVariables.baseUrl + '/index.php/admin/templates/get',
        datatype: "json",
        colNames:['Код', 'Название', 'Страница', 'Категории', '', ''],
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
                name: 'page',
                index:'page',
                width: 150
            },
            {
                name: 'categories',
                index:'categories',
                width: 150,
                searchoptions: {
                    searchhidden: true
                }
            },
            {
                name: 'page_id',
                index: 'pageId',
                hidden: true
            },
            {
                name: 'categorie_ids',
                index: 'categorie_ids',
                hidden: true
            }
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#templatesPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption:"Шаблоны",
        height: 300,
        ondblClickRow: editTemplate
    });

    $("#templates").jqGrid('navGrid','#templatesPager',{
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


    $("#addTemplate").click(function() {
        $('#addTemplatePopup').modal({

        });
    });

    $("#template-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addTemplatePopup').modal('hide');
            // Перезагружаем таблицу
            $("#templates").trigger("reloadGrid");
            $("#template-add-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddTemplatePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddTemplatePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddTemplatePopup').modal({

            });
        }
    });

    $("#template-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#editTemplatePopup').modal('hide');
            // Перезагружаем таблицу
            $("#templates").trigger("reloadGrid");
            $("#template-edit-form")[0].reset();

        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddTemplatePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddTemplatePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddTemplatePopup').modal({

            });
        }
    });


    function editTemplate() {
        var currentRow = $('#templates').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/admin/templates/getone?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editTemplatePopup form')
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
                                modelField: 'categorie_ids',
                                formField: 'categorieIds'
                            },
                            {
                                modelField: 'page_id',
                                formField: 'pageId'
                            }
                        ];
                        for(var i = 0; i < fields.length; i++) {
                            var node = form.find('#' + fields[i].formField);
                            // Выпадающий список с несколькими значениями
                            if(node.attr('multiple') == 'multiple') {
                                data.data[fields[i].modelField] = $.parseJSON(data.data[fields[i].modelField]);
                            }
                            node.val(data.data[fields[i].modelField]);
                        }

                        $("#editTemplatePopup").modal({

                        });
                    }
                }
            })
        }
    }

    $("#editTemplate").click(editTemplate);

    $("#deleteTemplate").click(function() {
        var currentRow = $('#templates').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/admin/templates/delete?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $("#templates").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddTemplatePopup .modal-body .row p').remove();
                        $('#errorAddTemplatePopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddTemplatePopup').modal({

                        });
                    }
                }
            })
        }
    });
});
