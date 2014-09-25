$(document).ready(function() {
    $("#separators").jqGrid({
        url: globalVariables.baseUrl + '/guides/medcards/getseparators',
        datatype: "json",
        colNames:['Код', 'Постфикс'],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 50
            },
            {
                name: 'value',
                index: 'value',
                width: 250
            }
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#separatorsPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption:"Постфиксы",
        height: 300,
          ondblClickRow: editSeparator
    });

	$("#separators").jqGrid('navGrid','#separatorsPager',{
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


    $("#addSeparator").click(function() {
        $('#addSeparatorPopup').modal({
        });
    });

    $("#editSeparator").click(editSeparator);

    function editSeparator() {
        var currentRow = $('#separators').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/guides/medcards/getoneseparator?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editSeparatorPopup form')
                        // Соответствия формы и модели
                        var fields = [
                            {
                                modelField: 'id',
                                formField: 'id'
                            },
                            {
                                modelField: 'value',
                                formField: 'value'
                            }
                        ];
                        for(var i = 0; i < fields.length; i++) {
                            form.find('#' + fields[i].formField).val(data.data[fields[i].modelField]);
                        }
                        $("#editSeparatorPopup").modal({

                        });
                    }
                }
            })
        }
    }

    $("#deleteSeparator").click(function() {
        var currentRow = $('#separators').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/guides/medcards/deleteseparator?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $("#separators").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddSeparatorPopup .modal-body .row p').remove();
                        $('#errorAddSeparatorPopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddseparatorsPopup').modal({

                        });
                    }
                }
            })
        }
    });

    $("#separator-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addSeparatorPopup').modal('hide');
            // Перезагружаем таблицу
            $("#separators").trigger("reloadGrid");
            $("#separator-add-form")[0].reset(); // Сбрасываем форму
        } else {

            // Удаляем предыдущие ошибки
            $('#errorAddSeparatorPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddSeparatorPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddSeparatorPopup').modal({

            });
        }
    });



    $("#separator-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#editSeparatorPopup').modal('hide');
            // Перезагружаем таблицу
            $("#separators").trigger("reloadGrid");
            $("#separator-edit-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddSeparatorPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddSeparatorPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddSeparatorPopup').modal({

            });
        }
    });

});