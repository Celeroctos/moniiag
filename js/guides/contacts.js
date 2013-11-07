$(document).ready(function() {
    $("#contacts").jqGrid({
        url: globalVariables.baseUrl + '/index.php/guides/contacts/get',
        datatype: "local",
        colNames:['Код', 'Сотрудник', 'Тип контакта', 'Значение контакта', ''],
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
            },
            {
                name:'employee_id',
                index:'employee_id',
                width: 150,
                hidden: true
            },
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

    $("#addContact").click(function() {
        var form = $('#addContactPopup form');
        form.find('#employeeId').val($("#employeeCodeFilter").val());

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
                                modelField: 'employee_id',
                                formField: 'employeeId'
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

    // Форма фильтрации контактов
    $("#contact-filter-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var url = '/index.php/guides/contacts/get?enterpriseid=' + $("#enterpriseCode").val() + '&wardid=' + $("#wardCodeFilter").val() + '&employeeid=' + $("#employeeCodeFilter").val();
        $("#contacts").jqGrid('setGridParam', {
            url: url,
            datatype: 'json'
        });
        $("#addContact").attr('disabled', false);
        $("#contacts").trigger("reloadGrid");
    });

    // Форма фильтрации контактов: подгрузка отделений учреждения
    $("#enterpriseCode").on('change', function(e) {
        var enterpriseCode = $(this).val();
        if(enterpriseCode != -1) { // В том случае, если это не "Нет учреждения", подгрузим отделения его..
            $.ajax({
                'url' : '/index.php/guides/wards/getbyenterprise?id=' + enterpriseCode,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        $("#wardCodeFilter option[value != -1]").remove(); // Удалить все, кроме отсутствующего
                        $("#wardCodeFilter").val('-1'); // По дефолту - Нет

                        $("#employeeCodeFilter option[value != -1]").remove(); // Удалить все, кроме отсутствующего
                        $("#employeeCodeFilter").val('-1'); // По дефолту - Нет

                        $("#addContact").attr('disabled', true);

                        // Заполняем из пришедших данных
                        for(var i = 0; i < data.data.length; i++) {
                            $("#wardCodeFilter").append('<option value="' + data.data[i].id + '">' + data.data[i].name + '</option>')
                        }
                        $("#wardCodeFilter").parents('.no-display').removeClass('no-display');
                    }
                }
            });
        }
    });

    // Форма фильтрации контактов: подгрузка сотрудников отделения
    $("#wardCodeFilter").on('change', function(e) {
        var wardCode = $(this).val();
        if(wardCode != -1) { // В том случае, если это не "Нет учреждения", подгрузим отделения его..
            $.ajax({
                'url' : '/index.php/guides/employees/getbyward?id=' + wardCode,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        $("#employeeCodeFilter option[value != -1]").remove(); // Удалить все, кроме отсутствующего
                        $("#employeeCodeFilter").val('-1'); // По дефолту - Нет

                        $("#addContact").attr('disabled', true);

                        // Заполняем из пришедших данных
                        for(var i = 0; i < data.data.length; i++) {
                            $("#employeeCodeFilter").append('<option value="' + data.data[i].id + '">' + data.data[i].first_name + ' ' + data.data[i].last_name + ' ' + data.data[i].middle_name + '</option>')
                        }
                        $("#employeeCodeFilter").parents('.no-display').removeClass('no-display');
                    }
                }
            });
        }
    });

    $("#employeeCodeFilter").on('change', function(e) {
        if($(this).val() != -1) {
            $("#addContact").attr('disabled', false);
        } else {
            $("#addContact").attr('disabled', true);
        }
    });

    // При загрузке: если заданы параметры сотрудника, раскрыть кнопку добавления контакта и отфильтровать таблицу
    (function() {
        if($("#employeeCodeFilter").val() != -1 &&  $("#wardCodeFilter").val() != -1 &&  $("#enterpriseCode").val() != -1) {
            $("#addContact").attr('disabled', true);
            $("#contact-filter-form").trigger('success');
        } else {
            // Простая загрузка всей таблицы
            $("#contacts").jqGrid('setGridParam', {
                datatype: 'json'
            });
            $("#contacts").trigger("reloadGrid");
        }
    })();
});
