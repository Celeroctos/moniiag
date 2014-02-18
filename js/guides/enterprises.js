$(document).ready(function() {
    $("#enterprises").jqGrid({
        url: globalVariables.baseUrl + '/index.php/guides/enterprises/get',
        datatype: "json",
        colNames:['Код', 'Наименование', 'Тип учреждения', 'Реквизиты'],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 150
            },
            {
                name: 'fullname',
                index: 'fullname',
                width: 200
            },
            {
                name: 'enterprise_type',
                index:'enterprise_type',
                width: 150
            },
            {
                name: 'requisits',
                index: 'requisits',
                width: 400,
                align: 'left'
            }
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#enterprisesPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption:"Список учреждений",
        height: 300,
        ondblClickRow: editEnterprise
    });

    $("#enterprises").jqGrid('navGrid','#enterprisesPager',{
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


    $("#addEnterprise").click(function() {
        $('#addEnterprisePopup').modal({

        });
    });

    $("#enterprise-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addEnterprisePopup').modal('hide');
            // Перезагружаем таблицу
            $("#enterprises").trigger("reloadGrid");
            $("#enterprise-add-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddEnterprisePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddEnterprisePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddEnterprisePopup').modal({

            });
        }
    });

    $("#enterprise-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#editEnterprisePopup').modal('hide');
            // Перезагружаем таблицу
            $("#enterprises").trigger("reloadGrid");
            $("#enterprise-edit-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddEnterprisePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddEnterprisePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddEnterprisePopup').modal({

            });
        }
    });

    function editEnterprise() {
        if(Boolean(globalVariables.guideEdit) == false) {
            return false;
        }
        var currentRow = $('#enterprises').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/guides/enterprises/getone?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editEnterprisePopup form')
                        // Соответствия формы и модели
                        var fields = [
                            {
                                modelField: 'id',
                                formField: 'id'
                            },
                            {
                                modelField: 'shortname',
                                formField: 'shortName'
                            },
                            {
                                modelField: 'fullname',
                                formField: 'fullName'
                            },
                            {
                                modelField: 'address_fact',
                                formField: 'addressFact'
                            },
                            {
                                modelField: 'address_jur',
                                formField: 'addressJur'
                            },
                            {
                                modelField: 'phone',
                                formField: 'phone'
                            },
                            {
                                modelField: 'bank',
                                formField: 'bank'
                            },
                            {
                                modelField: 'bank_account',
                                formField: 'bankAccount'
                            },
                            {
                                modelField: 'inn',
                                formField: 'inn'
                            },
                            {
                                modelField: 'kpp',
                                formField: 'kpp'
                            },
                            {
                                modelField: 'type',
                                formField: 'type'
                            }
                        ];
                        for(var i = 0; i < fields.length; i++) {
                            form.find('#' + fields[i].formField).val(data.data[fields[i].modelField]);
                        }
                        $("#editEnterprisePopup").modal({

                        });
                    }
                }
            })
        }
    }

    $("#editEnterprise").click(editEnterprise);

    $("#deleteEnterprise").click(function() {
        var currentRow = $('#enterprises').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/guides/enterprises/delete?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $("#enterprises").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddEnterprisePopup .modal-body .row p').remove();
                        $('#errorAddEnterprisePopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddEnterprisePopup').modal({

                        });
                    }
                }
            })
        }
    });
});
