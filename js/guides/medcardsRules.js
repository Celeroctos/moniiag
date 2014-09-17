$(document).ready(function() {
    $("#rules").jqGrid({
        url: globalVariables.baseUrl + '/guides/medcards/getrules',
        datatype: "json",
        colNames:['Код', 'Постфикс', 'Префикс', 'Правило', 'Унаследован от', '', '', ''],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 60
            },
            {
                name: 'postfix',
                index: 'postfix',
                width: 80
            },
			{
                name: 'prefix',
                index: 'prefix',
                width: 80
            },
			{
                name: 'value',
                index: 'value',
                width: 100
            },
			{
                name: 'parent',
                index: 'parent',
                width: 100
            },
			{
                name: 'postfix_id',
                index: 'postfix_id',
                hidden: true
            },
			{
                name: 'prefix_id',
                index: 'prefix_id',
                hidden: true
            },
			{
                name: 'parent_id',
                index: 'parent_id',
                hidden: true
            }
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#rulesPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption: "Правила формирования номеров",
        height: 300,
          ondblClickRow: editRule
    });
	
	 $("#rules").jqGrid('navGrid','#rulesPager',{
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



    $("#addRule").click(function() {
        $('#addRulePopup').modal({
        });
    });

    $("#editRule").click(editRule);

    function editRule() {
        var currentRow = $('#rules').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/guides/medcards/getonerule?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editRulePopup form')
                        // Соответствия формы и модели
                        var fields = [
                            {
                                modelField: 'id',
                                formField: 'id'
                            },
                            {
                                modelField: 'name',
                                formField: 'name'
                            }
                        ];
                        for(var i = 0; i < fields.length; i++) {
                            form.find('#' + fields[i].formField).val(data.data[fields[i].modelField]);
                        }
                        $("#editRulePopup").modal({

                        });
                    }
                }
            })
        }
    }

    $("#deleteRule").click(function() {
        var currentRow = $('#rules').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : '/guides/medcards/deleterule?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $("#rules").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddRulePopup .modal-body .row p').remove();
                        $('#errorAddRulePopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddrulesPopup').modal({

                        });
                    }
                }
            })
        }
    });

    $("#rule-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addRulePopup').modal('hide');
            // Перезагружаем таблицу
            $("#rules").trigger("reloadGrid");
            $("#rule-add-form")[0].reset(); // Сбрасываем форму
        } else {

            // Удаляем предыдущие ошибки
            $('#errorAddRulePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddRulePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddRulePopup').modal({

            });
        }
    });



    $("#rule-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#editRulePopup').modal('hide');
            // Перезагружаем таблицу
            $("#rules").trigger("reloadGrid");
            $("#rule-edit-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddRulePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddRulePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }

            $('#errorAddRulePopup').modal({

            });
        }
    });


});