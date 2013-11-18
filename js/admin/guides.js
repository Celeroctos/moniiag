$(document).ready(function() {
    $("#guides").jqGrid({
        url: globalVariables.baseUrl + '/index.php/admin/guides/get',
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
        pager: '#guidesPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption:"Роли",
        height: 300,
        ondblClickRow: editGuide
    });

    $("#guides").jqGrid('navGrid','#guidesPager',{
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


    $("#addGuide").click(function() {
        $('#addGuidePopup').modal({

        });
    });

    $("#guide-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addGuidePopup').modal('hide');
            // Перезагружаем таблицу
            $("#guides").trigger("reloadGrid");
            $("#guide-add-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddGuidePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddGuidePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddGuidePopup').modal({

            });
        }
    });

    $("#guide-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#editGuidePopup').modal('hide');
            // Перезагружаем таблицу
            $("#guides").trigger("reloadGrid");
            $("#guide-edit-form")[0].reset();

        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddGuidePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddGuidePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddGuidePopup').modal({

            });
        }
    });


    function editGuide() {
        var currentRow = $('#guides').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/admin/guides/getone?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editGuidePopup form')
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
                        $("#editGuidePopup").modal({

                        });
                    }
                }
            })
        }
    }

    $("#editGuide").click(editGuide);

    $("#deleteGuide").click(function() {
        var currentRow = $('#guides').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/admin/guides/delete?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $("#guides").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddGuidePopup .modal-body .row p').remove();
                        $('#errorAddGuidePopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddGuidePopup').modal({

                        });
                    }
                }
            })
        }
    });
});
