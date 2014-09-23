$(document).ready(function() {
    $("#rules").jqGrid({
        url: globalVariables.baseUrl + '/guides/medcards/getrules',
        datatype: "json",
        colNames:['Код', 'Название', 'Постфикс', 'Префикс', 'Правило', 'Унаследован от', 'Предыдущие префиксы и постфиксы', '', '', '', '', ''],
        colModel:[
            {
                name:'id',
                index:'id',
                width: 60
            },
			{
                name: 'name',
                index: 'name',
                width: 150
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
                name: 'rule',
                index: 'rule',
                width: 100
            },
			{
                name: 'parent',
                index: 'parent',
                width: 100
            },
			{
                name: 'participle_mode_desc',
                index: 'participle_mode_desc',
                width: 250
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
            },
			{
                name: 'value',
                index: 'value',
                hidden: true
            },
			{
				name:  'participle_mode',
				index: 'participle_mode',
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
                            },
                            {
                                modelField: 'parent_id',
                                formField: 'parentId'
                            },
							{
                                modelField: 'prefix_id',
                                formField: 'prefixId'
                            },
							{
                                modelField: 'postfix_id',
                                formField: 'postfixId'
                            },
							{
								modelField: 'value',
								formField: 'typeId'
							},
							{
								modelField: 'participle_mode',
								formField: 'participleMode'
							}
                        ];
                        for(var i = 0; i < fields.length; i++) {
							if(fields[i].formField == 'typeId') {
								if(data.data[fields[i].modelField] == 2) {
									form.find('#parentId').parents('.form-group').removeClass('no-display');
									form.find('#participleMode').parents('.form-group').removeClass('no-display');
								} else {
									form.find('#parentId').parents('.form-group').addClass('no-display');
									form.find('#participleMode').parents('.form-group').addClass('no-display');
								}
							}
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
						$('#rule-edit-form, #rule-add-form').find('#parentId').trigger('update');
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
			$('#rule-edit-form, #rule-add-form').find('#parentId').trigger('update');
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

	$('#rule-edit-form, #rule-add-form').find('#typeId').on('change', function(e) {
		if($(this).val() == 2) {
			$(this).parents('.form-group').next().removeClass('no-display').find('select').val(-1);
		} else {
			$(this).parents('.form-group').next().addClass('no-display').find('select').val(-1);
		}
	});
	
	$('#rule-edit-form, #rule-add-form').find('#parentId').on('update', function(e) {
		$(this).prop('disabled', true);
		$.ajax({
			'url' : '/guides/medcards/updateruleslist',
			'cache' : false,
			'dataType' : 'json',
			'type' : 'GET',
			'success' : function(data, textStatus, jqXHR) {
				if(data.success) {
					// TODO
					var select = $('#rule-edit-form, #rule-add-form').find('#parentId');
					$(select).find('option').remove();
					var data = data.data;
					for(var i in data) {
						$(select).append($('<option>').prop({
							'value' : i
						}).text(data[i]));
					}
					$('#rule-edit-form, #rule-add-form').find('#parentId').prop('disabled', false).val(-1);
				}
			}
		});
	});
});