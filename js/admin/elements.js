$(document).ready(function() {
    $("#elements").jqGrid({
        url: globalVariables.baseUrl + '/index.php/admin/elements/get',
        datatype: "json",
        colNames:['Код', 'Тип', 'Справочник', 'Категория', 'Метка', '', '', '',''],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 150
            },
            {
                name: 'type',
                index:'type',
                width: 150
            },
            {
                name: 'guide',
                index:'guide',
                width: 150
            },
            {
                name: 'categorie',
                index:'categorie',
                width: 150
            },
            {
                name: 'label',
                index: 'label',
                width: 150
            },
            {
                name: 'categorie_id',
                index: 'categorie_id',
                hidden: true
            },
            {
                name: 'guide_id',
                index: 'guide_id',
                hidden: true
            },
            {
                name: 'type_id',
                index: 'type_id',
                hidden: true
            },
            {
                name: 'allow_add',
                index: 'allow_add',
                hidden: true
            }
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#elementsPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption:"Врачебные справочники",
        height: 300,
        ondblClickRow: editElement
    });

    $("#elements").jqGrid('navGrid','#elementsPager',{
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


    $("#addElement").click(function() {
        $('#addElementPopup').modal({

        });
    });

    $("#element-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addElementPopup').modal('hide');
            // Перезагружаем таблицу
            $("#elements").trigger("reloadGrid");
            $("#element-add-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddElementPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddElementPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddElementPopup').modal({

            });
        }
    });

    $("#element-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#editElementPopup').modal('hide');
            // Перезагружаем таблицу
            $("#elements").trigger("reloadGrid");
            $("#element-edit-form")[0].reset();

        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddElementPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddElementPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddElementPopup').modal({

            });
        }
    });


    function editElement() {
        var currentRow = $('#elements').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/admin/elements/getone?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editElementPopup form')
                        // Соответствия формы и модели
                        var fields = [
                            {
                                modelField: 'id',
                                formField: 'id'
                            },
                            {
                                modelField: 'type',
                                formField: 'type'
                            },
                            {
                                modelField: 'categorie_id',
                                formField: 'categorieId'
                            },
                            {
                                modelField: 'label',
                                formField: 'label'
                            },
                            {
                                modelField: 'guide_id',
                                formField: 'guideId'
                            },
                            {
                                modelField: 'allow_add',
                                formField: 'allowAdd'
                            }
                        ];
                        for(var i = 0; i < fields.length; i++) {
                            form.find('#' + fields[i].formField).val(data.data[fields[i].modelField]);
                        }
                        $.proxy(form.find("select#type").trigger('change'), form.find("select#type")); // $.proxy - вызов контекста

                        $("#editElementPopup").modal({

                        });
                    }
                }
            })
        }
    }

    $("#editElement").click(editElement);

    $("#deleteElement").click(function() {
        var currentRow = $('#elements').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/admin/elements/delete?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $("#elements").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddElementPopup .modal-body .row p').remove();
                        $('#errorAddElementPopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddElementPopup').modal({

                        });
                    }
                }
            })
        }
    });

    // Открытие списка справочников
    $("select#type").on('change', function(e) {
        // Если это список с выбором
        var form = $(this).parents('form');
        if($(this).val() == 2 || $(this).val() == 3) {
            form.find("select#guideId").prop('disabled', false);
            form.find("select#allowAdd").prop('disabled', false);
        } else {
            // Поставить на дефолт
            form.find("select#guideId")
                .val(-1)
                .prop('disabled', true);

            form.find("select#allowAdd")
                .val(0)
                .prop('disabled', true);
        }
    });
});
