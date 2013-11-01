$(document).ready(function() {
    $("#employees").jqGrid({
        url: globalVariables.baseUrl + '/index.php/guides/employees/get',
        datatype: "json",
        colNames:['Код',
                  'ФИО',
                  'Медработник',
                  'Табельный номер',
                  'Код списка контактов',
                  'Контакт',
                  'Степень',
                  'Звание',
                  'Дата начала действия',
                  'Дата окончания действия',
                  'Отделение'],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 60
            },
            {
                name: 'fio',
                index: 'fio',
                width: 200
            },
            {
                name: 'post',
                index:'post',
                width: 120
            },
            {
                name: 'tabel_number',
                index: 'tabel_number',
                width: 130
            },
            {
                name: 'contact_code',
                index: 'contact_code',
                hidden: true
            },
            {
                name: 'contact',
                index: 'contact',
                width: 155
            },
            {
                name: 'degree',
                index: 'degree',
                width: 90
            },
            {
                name: 'titul',
                index: 'titul',
                width: 70
            },
            {
                name: 'date_begin',
                index: 'date_begin',
                width: 160
            },
            {
                name: 'date_end',
                index: 'date_end',
                width: 180
            },
            {
                name: 'ward',
                index: 'ward',
                width: 110
            }
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#employeesPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption:"Сотрудники",
        height: 300,
        editurl:"someurl.php",
        ondblClickRow: editEmployee
    });

    $("#employees").jqGrid('navGrid','#employeesPager',{
        edit: false,
        add: false,
        del: false
    });

    $("#addEmployee").click(function() {
        $('#addEmployeePopup').modal({

        });
    });

    // Инициализация дейтпикеров
    $('#dateBegin-cont').datetimepicker({
        format: 'yyyy-MM-dd hh:mm:ss'
    });
    $('#dateEnd-cont').datetimepicker({
        format: 'yyyy-MM-dd hh:mm:ss'
    });
    $('#dateBeginEdit-cont').datetimepicker({
        format: 'yyyy-MM-dd hh:mm:ss'
    });
    $('#dateEndEdit-cont').datetimepicker({
        format: 'yyyy-MM-dd hh:mm:ss'
    });

    $("#employee-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addEmployeePopup').modal('hide');
            // Перезагружаем таблицу
            $("#employees").trigger("reloadGrid");
            $("#employee-add-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddEmployeePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddEmployeePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddEmployeePopup').modal({

            });
        }
    });

    // Редактирование строки
    $("#employee-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#editEmployeePopup').modal('hide');
            // Перезагружаем таблицу
            $("#employees").trigger("reloadGrid");
            $("#employee-edit-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddEmployeePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddEmployeePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddEmployeePopup').modal({

            });
        }
    });

    // Форма фильтрации сотрудника
    $("#employee-filter-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var url = '/index.php/guides/employees/get?enterpriseid=' + $("#enterpriseCode").val() + '&wardid=' + $("#wardCodeFilter").val();
        $("#employees").jqGrid('setGridParam', { url: url });
        $("#employees").trigger("reloadGrid");
    });

    // Форма фильтрации сотрудника: подгрузка отделений учреждения
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

    function editEmployee() {
        var currentRow = $('#employees').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/guides/employees/getone?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editEmployeePopup form')
                        // Соответствия формы и модели
                        var fields = [
                            {
                                modelField: 'id',
                                formField: 'id'
                            },
                            {
                                modelField: 'first_name',
                                formField: 'firstName'
                            },
                            {
                                modelField: 'middle_name',
                                formField: 'middleName'
                            },
                            {
                                modelField: 'last_name',
                                formField: 'lastName'
                            },
                            {
                                modelField: 'post_id',
                                formField: 'postId'
                            },
                            {
                                modelField: 'tabel_number',
                                formField: 'tabelNumber'
                            },
                            {
                                modelField: 'contact_code',
                                formField: 'contactCode'
                            },
                            {
                                modelField: 'degree_id',
                                formField: 'degreeId'
                            },
                            {
                                modelField: 'titul_id',
                                formField: 'titulId'
                            },
                            {
                                modelField: 'date_begin',
                                formField: 'dateBegin'
                            },
                            {
                                modelField: 'date_end',
                                formField: 'dateEnd'
                            },
                            {
                                modelField: 'ward_code',
                                formField: 'wardCode'
                            },
                        ];
                        for(var i = 0; i < fields.length; i++) {
                            form.find('#' + fields[i].formField).val(data.data[fields[i].modelField]);
                        }
                        $("#editEmployeePopup").modal({

                        });
                    }
                }
            })
        }
    }


    $("#editEmployee").click(editEmployee);

    $("#deleteEmployee").click(function() {

    });
});
