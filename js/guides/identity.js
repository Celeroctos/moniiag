$(document).ready(function() {
    $("#identity").jqGrid({
        url: globalVariables.baseUrl + '/index.php/guides/doctype/get',
        datatype: "json",
        colNames:['Код', 'Наименование'],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 150
            },
            {
                name: 'name',
                index: 'name',
                width: 200
            }
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#identityPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption:"Удостоверения личности",
        height: 300,
          ondblClickRow: editDoctype
    });


    $("#addDoctype").click(function() {
        $('#addDoctypePopup').modal({
        });
    });

    $("#editDoctype").click(editDoctype);

    function editDoctype() {
        var currentRow = $('#identity').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/guides/doctype/getone?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editDoctypePopup form')
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
                        $("#editDoctypePopup").modal({

                        });
                    }
                }
            })
        }
    }

    $("#deleteDoctype").click(function() {
        var currentRow = $('#identity').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/guides/doctype/delete?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $("#identity").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddDoctypePopup .modal-body .row p').remove();
                        $('#errorAddDoctypePopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddIdentityPopup').modal({

                        });
                    }
                }
            })
        }
    });

    $("#doctype-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addDoctypePopup').modal('hide');
            // Перезагружаем таблицу
            $("#identity").trigger("reloadGrid");
            $("#doctype-add-form")[0].reset(); // Сбрасываем форму
        } else {

            // Удаляем предыдущие ошибки
            $('#errorAddDoctypePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddDoctypePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddDoctypePopup').modal({

            });
        }
    });



    $("#doctype-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#editDoctypePopup').modal('hide');
            // Перезагружаем таблицу
            $("#identity").trigger("reloadGrid");
            $("#doctype-edit-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddDoctypePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddDoctypePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddDoctypePopup').modal({

            });
        }
    });


});