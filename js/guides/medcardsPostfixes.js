$(document).ready(function() {
    $("#postfixes").jqGrid({
        url: globalVariables.baseUrl + '/guides/medcards/getpostfixes',
        datatype: "json",
        colNames:['Код', 'Постфикс'],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 50
            },
            {
                name: 'name',
                index: 'name',
                width: 250
            }
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#postfixesPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption:"Постфиксы",
        height: 300,
          ondblClickRow: editPostfix
    });

	$("#postfixes").jqGrid('navGrid','#postfixesPager',{
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


    $("#addPostfix").click(function() {
        $('#addPostfixPopup').modal({
        });
    });

    $("#editPostfix").click(editPostfix);

    function editPostfix() {
        var currentRow = $('#postfixes').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/guides/medcards/getonepostfix?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editPostfixPopup form')
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
                        $("#editPostfixPopup").modal({

                        });
                    }
                }
            })
        }
    }

    $("#deletePostfix").click(function() {
        var currentRow = $('#postfixes').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/guides/medcards/deletepostfix?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $("#postfixes").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddPostfixPopup .modal-body .row p').remove();
                        $('#errorAddPostfixPopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddpostfixesPopup').modal({

                        });
                    }
                }
            })
        }
    });

    $("#postfix-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addPostfixPopup').modal('hide');
            // Перезагружаем таблицу
            $("#postfixes").trigger("reloadGrid");
            $("#postfix-add-form")[0].reset(); // Сбрасываем форму
        } else {

            // Удаляем предыдущие ошибки
            $('#errorAddPostfixPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddPostfixPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddPostfixPopup').modal({

            });
        }
    });



    $("#postfix-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#editPostfixPopup').modal('hide');
            // Перезагружаем таблицу
            $("#postfixes").trigger("reloadGrid");
            $("#postfix-edit-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddPostfixPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddPostfixPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddPostfixPopup').modal({

            });
        }
    });


});