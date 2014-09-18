$(document).ready(function() {
    $("#prefixes").jqGrid({
        url: globalVariables.baseUrl + '/guides/medcards/getprefixes',
        datatype: "json",
        colNames:['Код', 'Наименование'],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 150
            },
            {
                name: 'value',
                index: 'value',
                width: 200
            }
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#prefixesPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption:"Префиксы",
        height: 300,
          ondblClickRow: editPrefix
    });

	$("#prefixes").jqGrid('navGrid','#prefixesPager',{
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


    $("#addPrefix").click(function() {
        $('#addPrefixPopup').modal({
        });
    });

    $("#editPrefix").click(editPrefix);

    function editPrefix() {
        var currentRow = $('#prefixes').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/guides/medcards/getoneprefix?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editPrefixPopup form')
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
                        $("#editPrefixPopup").modal({

                        });
                    }
                }
            })
        }
    }

    $("#deletePrefix").click(function() {
        var currentRow = $('#prefixes').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/guides/medcards/deleteprefix?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $("#prefixes").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddPrefixPopup .modal-body .row p').remove();
                        $('#errorAddPrefixPopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddprefixesPopup').modal({

                        });
                    }
                }
            })
        }
    });

    $("#prefix-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addPrefixPopup').modal('hide');
            // Перезагружаем таблицу
            $("#prefixes").trigger("reloadGrid");
            $("#prefix-add-form")[0].reset(); // Сбрасываем форму
        } else {

            // Удаляем предыдущие ошибки
            $('#errorAddPrefixPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddPrefixPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddPrefixPopup').modal({

            });
        }
    });



    $("#prefix-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#editPrefixPopup').modal('hide');
            // Перезагружаем таблицу
            $("#prefixes").trigger("reloadGrid");
            $("#prefix-edit-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddPrefixPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddPrefixPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddPrefixPopup').modal({

            });
        }
    });


});