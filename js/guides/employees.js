$(document).ready(function() {
    $("#employees").jqGrid({
        url: globalVariables.baseUrl + '/index.php/guides/employees/get',
        datatype: "json",
        colNames:['Код',
                  'ФИО',
                  '',
                  'Медработник',
                  'Табельный номер',
                  'Контакты',
                  '',
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
                width: 400
            },
            {
                name: 'more_info',
                index: 'more_info',
                width: 20,
                search: false
            },
            {
                name: 'post',
                index:'post',
                width: 120,
                hidden: true,
                searchoptions: {
                    searchhidden: true
                }
            },
            {
                name: 'tabel_number',
                index: 'tabel_number',
                width: 130,
                hidden: true,
                searchoptions: {
                    searchhidden: true
                }
            },
            {
                name: 'contact',
                index: 'contact',
                width: 155,
                hidden: true
            },
            {
                name: 'contact_see',
                index: 'contact_see',
                width: 20,
                search: false
            },
            {
                name: 'degree',
                index: 'degree',
                width: 90,
                hidden: true,
                searchoptions: {
                    searchhidden: true
                }
            },
            {
                name: 'titul',
                index: 'titul',
                width: 70,
                hidden: true,
                searchoptions: {
                    searchhidden: true
                }
            },
            {
                name: 'date_begin',
                index: 'date_begin',
                width: 160,
                hidden: true,
                searchoptions: {
                    searchhidden: true
                }
            },
            {
                name: 'date_end',
                index: 'date_end',
                width: 180,
                hidden: true
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

    $("#addEmployee").click(function() {
        $('#addEmployeePopup').modal({

        });
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
        var currentRow = $('#employees').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/guides/employees/delete?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $("#employees").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddEmployeePopup .modal-body .row p').remove();
                        $('#errorAddEmployeePopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddEmployeePopup').modal({

                        });
                    }
                }
            })
        }
    });

    // Фикс для того, чтобы узнать информацию о столбце: нативно по одиночному клику такая информация не выводится
    $("#employees").click(function(e) {
        var el = e.target;
        if (el.nodeName !== "TD") {
            el = $(el, this.rows).closest("td");
        }
        var iCol = $(el).index();
        var nCol = $(el).siblings().length;
        var row = $(el,this.rows).closest("tr.jqgrow");
        var rowId = row[0].id;
        if(iCol == 2) {
            editEmployee();
        }
    });

    // Установка смены
    function installShift(rowId, status, e) {

    }

});
