$(document).ready(function() {
    $("#users").jqGrid({
        url: globalVariables.baseUrl + '/index.php/admin/users/get',
        datatype: "json",
        colNames:['Код', 'Отображается', 'Логин', 'Роль', '', '', 'Сотрудник'],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 150
            },
            {
                name: 'username',
                index:'username',
                width: 150
            },
            {
                name: 'login',
                index: 'login',
                width: 150
            },
            {
                name: 'rolename',
                index:'rolename',
                width: 150
            },
            {
                name: 'role_id',
                index:'role_id',
                hidden: true
            },
            {
                name:  'employee_id',
                index: 'employee_id',
                hidden: true
            },
            {
                name: 'employee',
                index: 'employee',
                width: 250
            }
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#usersPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption:"Пользователи",
        height: 300,
        ondblClickRow: editUser
    });

    $("#users").jqGrid('navGrid','#usersPager',{
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


    $("#addUser").click(function() {
        $.ajax({
            'url' : '/index.php/admin/users/getallforassociate',
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
                if(data.success == true) {
                    var allowForAssociate = data.data;
                    var select = $('select#employeeId');
                    $(select).find('option').remove();

                    for(var i = 0; i < allowForAssociate.length; i++) {
                        $(select).append($('<option>').prop({
                            'value' : allowForAssociate[i].employee_id
                        }).text(allowForAssociate[i].employee_fio));
                    }

                    $('#addUserPopup').modal({

                    });
                } else{

                }
            }
        });
    });

    $("#user-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addUserPopup').modal('hide');
            // Перезагружаем таблицу
            $("#users").trigger("reloadGrid");
            $("#user-add-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddUserPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddUserPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddUserPopup').modal({

            });
        }
    });

    $("#user-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#editUserPopup').modal('hide');
            // Перезагружаем таблицу
            $("#users").trigger("reloadGrid");
            $("#user-edit-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddUserPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddUserPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddUserPopup').modal({

            });
        }
    });

    $("#user-edit-password-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == 'true') { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#editUserPasswordPopup').modal('hide');
            $("#user-edit-password-form")[0].reset(); // Сбрасываем форму
        } else {

            // Удаляем предыдущие ошибки
            $('#errorAddUserPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddUserPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddUserPopup').modal({

            });
        }
    });

    function editUser() {
        var currentRow = $('#users').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/admin/users/getone?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editUserPopup form')
                        // Соответствия формы и модели
                        var fields = [
                            {
                                modelField: 'id',
                                formField: 'id'
                            },
                            {
                                modelField: 'username',
                                formField: 'username'
                            },
                            {
                                modelField: 'login',
                                formField: 'login'
                            },
                            {
                                modelField: 'role_id',
                                formField: 'roleId'
                            },
                            {
                                modelField: 'employee_id',
                                formField: 'employeeId'
                            },
                        ];
                        var user = data.data.user;
                        var allowForAssociate = data.data.associatedEmployees;
                        var select = $('select#employeeId');
                        $(select).find('option').remove();

                        for(var i = 0; i < allowForAssociate.length; i++) {
                            $(select).append($('<option>').prop({
                                'value' : allowForAssociate[i].employee_id
                            }).text(allowForAssociate[i].employee_fio));
                        }

                        $('select#employeeId').append($('<option value="' + user.employee_id + '">' + user.employee_fio + '</option>')).val(user.employee_id);

                        for(var i = 0; i < fields.length; i++) {
                            form.find('#' + fields[i].formField).val(user[fields[i].modelField]);
                        }
                        $("#editUserPopup").modal({

                        });
                    }
                }
            })
        }
    }

    $("#editUser").click(editUser);

    // Смена пароля, окно
    $("#editPasswordUser").click(function() {
        var currentRow = $('#users').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            $.ajax({
                'url' : '/index.php/admin/users/getone?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editUserPasswordPopup form')
                        // Соответствия формы и модели
                        var fields = [
                            {
                                modelField: 'id',
                                formField: 'id'
                            }
                        ];
                        for(var i = 0; i < fields.length; i++) {
                            form.find('#' + fields[i].formField).val(data.data.user[fields[i].modelField]);
                        }
                        $("#editUserPasswordPopup").modal({

                        });
                    }
                }
            })
        }
    });

    $("#deleteUser").click(function() {
        var currentRow = $('#users').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/index.php/admin/users/delete?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $("#users").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddUserPopup .modal-body .row p').remove();
                        $('#errorAddUserPopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddUserPopup').modal({

                        });
                    }
                }
            })
        }
    });
});
