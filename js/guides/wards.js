$(document).ready(function() {
    $("#wards").jqGrid({
        url: globalVariables.baseUrl + '/guides/wards/get',
        datatype: "json",
        colNames:['Код', 'Наименование', 'Тип учреждения', 'Правило создания номеров карт', ''],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 150
            },
            {
                name: 'name',
                index: 'name',
                width: 200
            },
            {
                name: 'enterprise_name',
                index:'enterprise_name',
                width: 150
            },
			{
				name: 'rule',
				index: 'rule',
				width: 250
			},
			{
				name: 'rule_id',
				index: 'rule_d',
				hidden: true
			}
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#wardsPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption:"Отделения",
        height: 300,
        ondblClickRow: editWard
    });

    $("#wards").jqGrid('navGrid','#wardsPager',{
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

    $("#addWard").click(function() {
        $('#addWardPopup').modal({

        });
    });

    $("#ward-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addWardPopup').modal('hide');
            // Перезагружаем таблицу
            $("#wards").trigger("reloadGrid");
            $("#ward-add-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddWardPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddWardPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddWardPopup').modal({

            });
        }
    });

    $("#ward-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        if(Boolean(globalVariables.guideEdit) == false) {
            return false;
        }
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#editWardPopup').modal('hide');
            // Перезагружаем таблицу
            $("#wards").trigger("reloadGrid");
            $("#ward-edit-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddWardPopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddWardPopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddWardPopup').modal({

            });
        }
    });

    function editWard() {
        var currentRow = $('#wards').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/guides/wards/getone?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editWardPopup form')
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
                                modelField: 'enterprise_id',
                                formField: 'enterprise'
                            },
							{
								modelField: 'rule_id',
								formField: 'ruleId'
							}
                        ];
                        for(var i = 0; i < fields.length; i++) {
                            form.find('#' + fields[i].formField).val(data.data[fields[i].modelField]);
                        }
                        $("#editWardPopup").modal({

                        });
                    }
                }
            })
        }
    }


    $("#editWard").click(editWard);

    $("#deleteWard").click(function() {
        var currentRow = $('#wards').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/guides/wards/delete?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $("#wards").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddWardPopup .modal-body .row p').remove();
                        $('#errorAddWardPopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddWardPopup').modal({

                        });
                    }
                }
            })
        }
    });
});
