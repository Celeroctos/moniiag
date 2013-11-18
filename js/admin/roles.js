$(document).ready(function() {
    $("#roles").jqGrid({
        url: globalVariables.baseUrl + '/index.php/admin/roles/get',
        datatype: "json",
        colNames:['Код', 'Название', 'Родитель', ''],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 150
            },
            {
                name: 'name',
                index:'name',
                width: 150
            },
            {
                name: 'parent',
                index: 'parent',
                width: 150
            },
            {
                name: 'parent_id',
                index:'parent_id',
                hidden: true
            }
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#rolesPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption:"Роли",
        height: 300,
        ondblClickRow: editRole
    });

    $("#roles").jqGrid('navGrid','#rolesPager',{
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


    $("#addRole").click(function() {
        $('#addRolePopup').modal({

        });
    });

    $("#role-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addRolePopup').modal('hide');
            // Перезагружаем таблицу
            $("#roles").trigger("reloadGrid");
            $("#role-add-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddRolePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddRolePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddRolePopup').modal({

            });
        }
    });

    $("#role-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#editRolePopup').modal('hide');
            window.location.reload();
            // Перезагружаем таблицу
            //$("#roles").trigger("reloadGrid");
            //$("#role-edit-form")[0].reset();

        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddRolePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddRolePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddRolePopup').modal({

            });
        }
    });


    function editRole() {
        var currentRow = $('#roles').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/admin/roles/getone?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editRolePopup form')
                        // Соответствия формы и модели
                        var fields = [
                            {
                                modelField: 'id',
                                formField: 'id'
                            },
                            {
                                modelField: 'name',
                                formField: 'name'
                            },
                            {
                                modelField: 'parent_id',
                                formField: 'parentId'
                            }
                        ];
                        for(var i = 0; i < fields.length; i++) {
                            form.find('#' + fields[i].formField).val(data.data[fields[i].modelField]);
                        }
                        // Проставим экшены
                        form.find('input[type="checkbox"]').prop('checked', false); // Анальный баг: невозможно сбросить чекбокс..
                        var actions = data.data.actions;
                        for(var i = 0; i < actions.length; i++) {
                            form.find('input[name="action' + actions[i] + '"]').prop('checked', true);
                        }
                        $("#editRolePopup").modal({

                        });
                    }
                }
            })
        }
    }

    $("#editRole").click(editRole);

    $("#deleteRole").click(function() {
        var currentRow = $('#roles').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/admin/roles/delete?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        window.location.reload();
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddRolePopup .modal-body .row p').remove();
                        $('#errorAddRolePopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddRolePopup').modal({

                        });
                    }
                }
            })
        }
    });
});
