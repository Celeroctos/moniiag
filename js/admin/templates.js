$(document).ready(function() {

	$("#templates").jqGrid({
        url: globalVariables.baseUrl + '/admin/templates/get',
        datatype: "json",
        colNames:['Код', 'Название', 'Страница', 'Категории', 'Обязательность диагноза', 'Порядок', '', '', ''],
        colModel:[
			{
                name:'id',
                index:'id',
                width: 150
            }, {
                name: 'name',
                index:'name',
                width: 150
            }, {
                name: 'page',
                index:'page',
                width: 150
            }, {
                name: 'categories',
                index:'categories',
                width: 150,
                searchoptions: {
                    searchhidden: true
                }
            }, {
                name: 'primary_diagnosis_desc',
                index:'primary_diagnosis_desc',
                width: 200
            }, {
                name: 'index',
                index: 'index',
                width: 80
            }, {
                name: 'page_id',
                index: 'pageId',
                hidden: true
            }, {
                name: 'categorie_ids',
                index: 'categorie_ids',
                hidden: true
            }, {
                name: 'primary_diagnosis',
                index: 'primary_diagnosis',
                hidden: true
            }
        ],
        rowNum: 10,
        rowList:[10,20,30],
        pager: '#templatesPager',
        sortname: 'id',
        viewrecords: true,
        sortorder: "desc",
        caption:"Шаблоны",
        height: 300,
        ondblClickRow: showTemplate
    });

    $("#templates").jqGrid('navGrid','#templatesPager',{
            edit: false,
            add: false,
            del: false
        }, {}, {}, {}, {
            closeOnEscape:true,
            multipleSearch :true,
            closeAfterSearch: true
        }
    );

    $("#addTemplate").click(function() {
        $('#addTemplatePopup').modal().draggable("disable");
    });

    $("#template-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) {
        	// Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#addTemplatePopup').modal('hide');
            // Перезагружаем таблицу
            $("#templates").trigger("reloadGrid");
            $("#template-add-form")[0].reset(); // Сбрасываем форму
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddTemplatePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddTemplatePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }
            $('#errorAddTemplatePopup').modal();
        }
    });

    $("#template-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) {
        	// Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
            $('#editTemplatePopup').modal('hide');
            // Перезагружаем таблицу
            $("#templates").trigger("reloadGrid");
            $("#template-edit-form")[0].reset();
        } else {
            // Удаляем предыдущие ошибки
            $('#errorAddTemplatePopup .modal-body .row p').remove();
            // Вставляем новые
            for(var i in ajaxData.errors) {
                for(var j = 0; j < ajaxData.errors[i].length; j++) {
                    $('#errorAddTemplatePopup .modal-body .row').append("<p>" + ajaxData.errors[i][j] + "</p>")
                }
            }
            $('#errorAddTemplatePopup').modal();
        }
    });

    function editTemplate() {
        var currentRow = $('#templates').jqGrid('getGridParam','selrow');
		if(currentRow == null) {
			return false;
		}
		var success = function(data) {
			if(data.success != true) {
				return false;
			}
			// Заполняем форму значениями
			var form = $('#editTemplatePopup form')
			// Соответствия формы и модели
			var fields = [
				{
					modelField: 'id',
					formField: 'id'
				}, {
					modelField: 'name',
					formField: 'name'
				}, {
					modelField: 'categorie_ids',
					formField: 'categorieIds'
				}, {
					modelField: 'page_id',
					formField: 'pageId'
				}, {
					modelField: 'primary_diagnosis',
					formField: 'primaryDiagnosisFilled'
				}, {
					modelField: 'index',
					formField: 'index'
				}
			];
			for(var i = 0; i < fields.length; i++) {
				var node = form.find('#' + fields[i].formField);
				// Выпадающий список с несколькими значениями
				if(node.attr('multiple') == 'multiple') {
					data.data[fields[i].modelField] = $.parseJSON(
						data.data[fields[i].modelField]
					);
				}
				node.val(data.data[fields[i].modelField]);
			}
			$("#editTemplatePopup").removeData("modal").modal();
		};
		// Надо вынуть данные для редактирования
		$.ajax({
			'url': globalVariables.baseUrl + '/admin/templates/getone?id=' + currentRow,
			'cache': false,
			'dataType': 'json',
			'type': 'GET',
			'success': success
		})
    }

    $("#editTemplate").click(editTemplate);

    $("#deleteTemplate").click(function() {
        var currentRow = $('#templates').jqGrid('getGridParam','selrow');
		if(currentRow == null) {
			return true;
		}
		var success = function(data) {
			if(data.success == 'true') {
				$("#templates").trigger("reloadGrid");
			} else {
				// Удаляем предыдущие ошибки
				$('#errorAddTemplatePopup .modal-body .row p').remove();
				$('#errorAddTemplatePopup .modal-body .row').append("<p>" + data.error + "</p>")
				// Отображаем модальное окно
				$('#errorAddTemplatePopup').modal();
			}
		};
		// Надо вынуть данные для редактирования
		$.ajax({
			'url': globalVariables.baseUrl + '/admin/templates/delete?id=' + currentRow,
			'cache': false,
			'dataType': 'json',
			'type': 'GET',
			'success': success
		});
    });

    $('#showTemplate').on('click', function(e) {
        showTemplate();
    });

    function showTemplate() {
        var currentRow = $('#templates').jqGrid('getGridParam','selrow');
		if(currentRow == null) {
			return false;
		}
		var success = function(data) {
			if(data.success) {
				$('#showTemplatePopup .modal-body .row').html(data.data);
				$('#showTemplatePopup .btn-sm').prop('disabled', true);
				$('#showTemplatePopup').modal({});
				$("#templates").trigger("reloadGrid");
				$('#showTemplate').attr({
					'disabled' : false
				}).text('Просмотр шаблона');
			} else {
				// Удаляем предыдущие ошибки
				$('#errorAddTemplatePopup .modal-body .row p').remove();
				$('#errorAddTemplatePopup .modal-body .row').append("<p>" + data.error + "</p>")
				$('#errorAddTemplatePopup').modal();
			}
		};
		$('#showTemplate').prop({
			'disabled' : true
		}).text('Подождите, шаблон вызывается...');
		// Надо вынуть данные для редактирования
		$.ajax({
			'url': globalVariables.baseUrl + '/admin/templates/show?id=' + currentRow,
			'cache': false,
			'type': 'GET',
			'dataType': 'json',
			'success': success
		})
    }

    function designTemplate() {
        var currentRow;
        if((currentRow = $('#templates').jqGrid('getGridParam','selrow')) == null) {
            return false;
        }
		var success = function(data) {
			// check data for success and terminate execution
			// if we have any errors
			if(data.success != true) {
				console.log(data);
				return false;
			}
			// register template engine with some template, it
			// will restart engine and append current categories
			// restart template engine to remove all
			// categories and it's elements
			TemplateEngine.registerTemplate(data.template)
				// Categories actions
				.onAppend("category", function() {
					if (this.template() && this.template().key() == "clone") {
						onCloneCategory(this);
					}
				})
			// display template engine designer modal window
			$('#designTemplatePopup').modal({
				backdrop: 'static',
				keyboard: false
			}).draggable("disable").disableSelection();
		};
        $.ajax({
            'url': globalVariables.baseUrl + '/admin/templates/getcategories?id=' + currentRow,
            'cache': false,
            'dataType': 'json',
            'type': 'GET',
            'success': success
        });
    }

    $("#designTemplate").click(designTemplate);
});