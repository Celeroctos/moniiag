$(document).ready(function() {

	var editCategoryPopup = $("#editCategoriePopup");
	var designTemplatePopup = $("#designTemplatePopup");
	var editElementPopup = $("#editElementPopup");
	var addCategoriePopup = $("#addCategoriePopup");

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
        $('#addTemplatePopup').modal();
    });

    $("#template-add-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
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

            $('#errorAddTemplatePopup').modal({

            });
        }
    });

    $("#template-edit-form").on('success', function(eventObj, ajaxData, status, jqXHR) {
        var ajaxData = $.parseJSON(ajaxData);
        if(ajaxData.success == true) { // Запрос прошёл удачно, закрываем окно для добавления нового предприятия, перезагружаем jqGrid
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

            $('#errorAddTemplatePopup').modal({

            });
        }
    });

    function editTemplate() {
        var currentRow = $('#templates').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : globalVariables.baseUrl + '/admin/templates/getone?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == true) {
                        // Заполняем форму значениями
                        var form = $('#editTemplatePopup form')
                        // Соответствия формы и модели
                        var fields = [{
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
                                data.data[fields[i].modelField] = $.parseJSON(data.data[fields[i].modelField]);
                            }
                            node.val(data.data[fields[i].modelField]);
                        }

                        $("#editTemplatePopup").modal({

                        });
                    }
                }
            })
        }
    }

    $("#editTemplate").click(editTemplate);

    $("#deleteTemplate").click(function() {
        var currentRow = $('#templates').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : globalVariables.baseUrl + '/admin/templates/delete?id=' + currentRow,
                'cache' : false,
                'dataType' : 'json',
                'type' : 'GET',
                'success' : function(data, textStatus, jqXHR) {
                    if(data.success == 'true') {
                        $("#templates").trigger("reloadGrid");
                    } else {
                        // Удаляем предыдущие ошибки
                        $('#errorAddTemplatePopup .modal-body .row p').remove();
                        $('#errorAddTemplatePopup .modal-body .row').append("<p>" + data.error + "</p>")

                        $('#errorAddTemplatePopup').modal({

                        });
                    }
                }
            })
        }
    });

    $('#showTemplate').on('click', function(e) {
        showTemplate();
    });

    function showTemplate() {
        var currentRow = $('#templates').jqGrid('getGridParam','selrow');
        if(currentRow != null) {
            $('#showTemplate').prop({
                'disabled' : true
            }).text('Подождите, шаблон вызывается...');
            // Надо вынуть данные для редактирования
            $.ajax({
                'url' : globalVariables.baseUrl + '/admin/templates/show?id=' + currentRow,
                'cache' : false,
                'type' : 'GET',
                'dataType' : 'json',
                'success' : function(data, textStatus, jqXHR) {
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

                        $('#errorAddTemplatePopup').modal({
                        });
                    }
                }
            })
        }
    }

	var applyFieldsToForm = function(item, form, fields) {
		for(var i = 0; i < fields.length; i++) {
			var formField = form.find('#' + fields[i].formField);
			if (fields[i].value) {
				formField.val(fields[i].value);
			} else {
				if (item.length() > 0) {
					formField.val(item.field(fields[i].modelField));
				}
			}
			if (fields[i].hidden) {
				formField.parent(".col-xs-9").parent(".form-group")
					.css("visibility", "hidden")
					.css("position", "absolute");
			}
		}
	};

	var registerTemplateEngine = function(template) {
		// restart template engine to remove all
		// categories and it's elements
		TemplateEngine.registerTemplate(template)
			.onEdit("category", function() {
				var that = this;
				// Заполняем форму значениями
				var form = $('#editCategoriePopup form')
				// Соответствия формы и модели
				applyFieldsToForm(this, form, [{
					modelField: 'id',
					formField: 'id'
				}, {
					modelField: 'name',
					formField: 'name'
				}, {
					modelField: 'parent_id',
					formField: 'parentId',
					hidden: true
				}, {
					modelField: 'is_dynamic',
					formField: 'isDynamic'
				}, {
					modelField: 'position',
					formField: 'position',
					hidden: true
				}, {
					modelField: 'is_wrapped',
					formField: 'isWrapped'
				}]);
				editCategoryPopup.modal().draggable("disable")
					.disableSelection().css("z-index", 1051);
				editCategoryPopup.on("hide.bs.modal", function() {
					that.fetch(globalVariables.baseUrl + "/admin/categories/getone?id=" + that.field("id"));
				});
			})
			.onAppend("category", function() {
				var that = this;
				var parentID = -1;
				var parent = that.parent();
				if (TemplateEngine.isCategory(parent)) {
					if (!parent.length()) {
						return false;
					} else {
						parentID = parent.field("id");
					}
				} else {
					console.log(parent);
				}
				// Заполняем форму значениями
				var form = $('#addCategoriePopup form')
				// Соответствия формы и модели
				applyFieldsToForm(this, form, [{
					modelField: 'name',
					formField: 'name'
				}, {
					modelField: 'parent_id',
					formField: 'parentId',
					hidden: true,
					value: parentID
				}, {
					modelField: 'is_dynamic',
					formField: 'isDynamic'
				}, {
					modelField: 'position',
					formField: 'position',
					hidden: true,
					value: 0x7b
				}, {
					modelField: 'is_wrapped',
					formField: 'isWrapped'
				}]);
				$('#addCategoriePopup').modal()
					.on("hide.bs.modal", function() {
						// check for empty template
						if (!that.length()) {
							return true;
						}
						// send request to add category
						$.ajax({
							'url' : globalVariables.baseUrl + '/admin/templates/addcategory?id=' + currentRow,
							'cache' : false,
							'dataType' : 'json',
							'type' : 'GET',
							'success' : function(data, textStatus, jqXHR) {
								// check data for success and terminate execution
								// if we have any errors
								if(data.success != true) {
									console.log(data); return false;
								}
								console.log(data);
							}
						});
						// update data
						that.fetch(globalVariables.baseUrl + "/admin/categories/getone?id=" + that.field("id"));
					});
			})
			.onEdit("static", function() {
				// TODO Add static context linkage
			})
			.onEdit(null, function() {
				if (!TemplateEngine.isItem(this)) {
					return false;
				}
				// Заполняем форму значениями
				var form = $('#editElementPopup form')
				// Соответствия формы и модели
				var fields = [{
						modelField: 'id',
						formField: 'id'
					}, {
						modelField: 'type',
						formField: 'type'
					}, {
						modelField: 'categorie_id',
						formField: 'categorieId',
						disabled: true
					}, {
						modelField: 'label',
						formField: 'label'
					}, {
						modelField: 'guide_id',
						formField: 'guideId'
					}, {
						modelField: 'allow_add',
						formField: 'allowAdd'
					}, {
						modelField: 'is_required',
						formField: 'isRequired'
					}, {
						modelField: 'label_after',
						formField: 'labelAfter'
					}, {
						modelField: 'size',
						formField: 'size'
					}, {
						modelField: 'is_wrapped',
						formField: 'isWrapped'
					}, {
						modelField: 'position',
						formField: 'position',
						disabled: true
					}, {
						modelField: 'config',
						formField: 'config'
					}, {
						modelField: 'default_value',
						formField: 'defaultValue'
					}, {
						modelField: 'default_value',
						formField: 'defaultValueText'
					}, {
						modelField: 'label_display',
						formField: 'labelDisplay'
					}, {
						modelField: 'show_dynamic',
						formField: 'showDynamic'
					}, {
						modelField: 'hide_label_before',
						formField: 'hideLabelBefore'
					}
				];
				var data = {
					data: this.model()
				};
				$('#editElementPopup #showDynamic').prop('disabled', data.data['type'] == 4);
				for (var i = 0; i < fields.length; i++) {
					// Подгрузка значений справочника для дефолтного значения
					if (fields[i].formField == 'defaultValue' && (data.data['type'] == 2 || data.data['type'] == 3)) {
						// Это значение ставится асинхронно
						$('select#guideId').trigger('change', [data.data[fields[i].modelField]]);
						continue;
					}
					var formField = form.find('#' + fields[i].formField).val(
						data.data[fields[i].modelField]
					);
					if (fields[i].disabled) {
						formField.attr("disabled", "disabled");
					}
					// Таблица
					if (fields[i].formField == 'config') {
						if(typeof data.data['config'] != 'object') {
							var config = $.parseJSON(data.data['config']);
						} else {
							var config = data.data['config'];
						}
						if (data.data['type'] == 4) {
							printHeadersTable(config,
								$('#editElementPopup .table-config-headers tbody'),
								$('#editElementPopup .colsHeaders'),
								$('#editElementPopup .rowsHeaders'),
								$('#editElementPopup #numRows'),
								$('#editElementPopup #numCols')
							);
							printDefaultValuesTable(config.numCols, config.numRows);
							if (config.values != undefined && config.values != null) {
								writeDefValuesFromConfig(config.values);
							}
						}
						if (data.data['type'] == 5) {
							$('#editElementPopup').find('#numberFieldMaxValue, #numberFieldMinValue, #numberStep').parents('.form-group').removeClass('no-display');
							$('#editElementPopup #numberFieldMaxValue').val(config.maxValue);
							$('#editElementPopup #numberFieldMinValue').val(config.minValue);
							$('#editElementPopup #numberStep').val(config.step);
						}
						if (data.data['type'] == 6) {
							$('#editElementPopup').find('#dateFieldMaxValue, #dateFieldMinValue').parents('.form-group').removeClass('no-display');
							if (config != null && config != '') {
								$('#editElementPopup #dateFieldMaxValue').val(config.maxValue);
								$('#editElementPopup #dateFieldMinValue').val(config.minValue);
							} else {
								// Если конфига нет - надо просто поставить пустое значение
								$('#editElementPopup #dateFieldMaxValue').val('');
								$('#editElementPopup #dateFieldMinValue').val('');
							}
							// Затриггерим контрол, чтобы данные подкачались в видимые поля контрола
							$('#editElementPopup #dateFieldMaxValue').trigger('change');
							$('#editElementPopup #dateFieldMinValue').trigger('change');
						}
					}
				}
				// Теперь нужно проверить - если взведён флаг "есть зависимость" - нужно выключить некоторые опции в
				//    в изменении типа
				if (data.data.is_dependencies == 1) {
					$('#element-edit-form select#type option:not([value=2]):not([value=3])').addClass('no-display');
				} else {
					$('#element-edit-form select#type option').removeClass('no-display');
				}
				$.proxy(form.find("select#type").trigger('change'), form.find("select#type")); // $.proxy - вызов контекста
				editElementPopup.modal().draggable("disable")
					.disableSelection();
				var that = this;
				editElementPopup.on("hide.bs.modal", function() {
					that.fetch(globalVariables.baseUrl + "/admin/elements/getone?id=" + that.field("id"));
				});
			});
	};

    function designTemplate() {
        var currentRow;
        if((currentRow = $('#templates').jqGrid('getGridParam','selrow')) == null) {
            return false;
        }
        $.ajax({
            'url' : globalVariables.baseUrl + '/admin/templates/getcategories?id=' + currentRow,
            'cache' : false,
            'dataType' : 'json',
            'type' : 'GET',
            'success' : function(data, textStatus, jqXHR) {
				// check data for success and terminate execution
				// if we have any errors
                if(data.success != true) {
                    console.log(data); return false;
                }
				// register template engine with some template, it
				// will restart engine and append current categories
				registerTemplateEngine(data.template);
				// display template engine designer modal window
                $('#designTemplatePopup').modal({
					keyboard: false
				}).draggable("disable").disableSelection();
            }
        });
    }

    $("#designTemplate").click(designTemplate);
});