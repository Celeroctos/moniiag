$(document).ready(function() {
    $("#contacts").jqGrid({
        url: globalVariables.baseUrl + '/index.php/guides/contacts/get',
        datatype: "json",
        colNames:['Код', 'Сотрудник', 'Тип контакта', 'Значение контакта'],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 150
            },
            {
                name: 'fio',
                index: 'fio',
                width: 200
            },
            {
                name: 'type',
                index:'type',
                width: 150
            },
            {
                name: 'contact_value',
                index:'contact_value',
                width: 150
            }
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#contactsPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption:"Контакты",
        height: 300,
        ondblClickRow: editContact
    });

    $("#contacts").jqGrid('navGrid','#contactsPager',{
        edit: false,
        add: false,
        del: false
    });

    $("#addContact").click(function() {
        $('#addContactPopup').modal({

        });
    });

    $("#contact-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addContactPopup').modal('hide');
            // Перезагружаем таблицу
            $("#contacts").trigger("reloadGrid");
            $("#contact-add-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddContactPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddContactPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddContactPopup').modal({

            });
        }
    });

    $("#contact-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#editContactPopup').modal('hide');
            // Перезагружаем таблицу
            $("#contacts").trigger("reloadGrid");
            $("#contact-edit-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddContactPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddContactPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddContactPopup').modal({

            });
        }
    });

    function editContact() {
        var currentRow = $('#contacts').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/guides/contacts/getone?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editContactPopup form')
                        // Соответствия формы и модели
                        var fields = [
                            {
                                modelField: 'id',
                                formField: 'id'
                            },
                            {
                                modelField: 'contact_value',
                                formField: 'contactValue'
                            },
                            {
                                modelField: 'type',
                                formField: 'type'
                            },
                            {
                                modelField: 'doctor_id',
                                formField: 'doctorId'
                            }
                        ];
                        for(var i = 0; i < fields.length; i++) {
                            form.find('#' + fields[i].formField).val(data.data[fields[i].modelField]);
                        }
                        $("#editContactPopup").modal({

                        });
                    }
                }
            })
        }
    }

    $("#editContact").click(editContact);

    $("#deleteContact").click(function() {
        var currentRow = $('#contacts').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/guides/contacts/delete?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $("#contacts").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddContactPopup .modal-body .row p').remove();
                        $('#errorAddContactPopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddContactPopup').modal({

                        });
                    }
                }
            })
        }
    });
});
