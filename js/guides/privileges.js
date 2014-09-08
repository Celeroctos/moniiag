$(document).ready(function() {
    $("#privileges").jqGrid({
        url: globalVariables.baseUrl + '/guides/privileges/get',
        datatype: "json",
        colNames:['ID',
                  'Код льготы',
                  'Описание'
                  ],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 60
            },
            {
                name: 'code',
                index: 'code',
                width: 180
            },
            {
                name: 'name',
                index: 'name',
                width: 180
            }
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#privilegesPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption: "Льготы",
        height: 300,
        ondblClickRow: editPrivilege
    });

    $("#privileges").jqGrid('navGrid','#privilegesPager',{
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

    $("#addPrivilege").click(function() {
        $('#addPrivilegePopup').modal({

        });
    });


    $("#privilege-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового кабинета, перезагружаем jqGrid
            $('#addPrivilegePopup').modal('hide');
            // Перезагружаем таблицу
            $("#privileges").trigger("reloadGrid");
            $("#privilege-add-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddPrivilegePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddPrivilegePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddPrivilegePopup').modal({

            });
        }
    });

    // Редактирование строки
    $("#privilege-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        if(Boolean(globalVariables.guideEdit) == false) {
            return false;
        }
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового кабинета, перезагружаем jqGrid
            $('#editPrivilegePopup').modal('hide');
            // Перезагружаем таблицу
            $("#privileges").trigger("reloadGrid");
            $("#privilege-edit-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddPrivilegePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddPrivilegePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddPrivilegePopup').modal({

            });
        }
    });

    function editPrivilege() {
        var currentRow = $('#privileges').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/guides/privileges/getone?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editPrivilegePopup form')
                        // Соответствия формы и модели
                        var fields = [
                            {
                                modelField: 'id',
                                formField: 'id'
                            },
                            {
                                modelField: 'code',
                                formField: 'code'
                            },
                            {
                                modelField: 'name',
                                formField: 'name'
                            }
                        ];
                        for(var i = 0; i < fields.length; i++) {
                            form.find('#' + fields[i].formField).val(data.data[fields[i].modelField]);
                        }
                        $("#editPrivilegePopup").modal({
                        });
                    }
                }
            })
        }
    }


    $("#editPrivilege").click(editPrivilege);

    $("#deletePrivilege").click(function() {
        var currentRow = $('#privileges').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/guides/privileges/delete?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $("#privileges").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddPrivilegePopup .modal-body .row p').remove();
                        $('#errorAddPrivilegePopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddPrivilegePopup').modal({

                        });
                    }
                }
            })
        }
    });
});
